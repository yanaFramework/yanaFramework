<?php
/**
 * Administration Panel
 *
 * Create the administration menu.
 * It is used to manage plugins and other installed components.
 *
 * {@translation
 *
 *    de: Administrationsmenü
 *
 *        Erzeugt das Administrationsmenü.
 *        Es wird benötigt um Plugins und andere installierte Komponenten zu verwalten.
 * }
 *
 * @menu       group: setup, title: {lang id="configmenu"}
 * @author     Thomas Meyer
 * @type       config
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @active     always
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * Configration menu
 *
 * This plugin provides the basic administration menu and
 * interfaces to create custom profile settings.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_config extends StdClass implements IsPlugin
{

    /**
     * is user expert mode
     *
     * @access  private
     * @var     bool
     */
    private $_isExpert = null;

    /**
     * Default event handler
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * create administration panel
     *
     * this function does not expect any arguments
     *
     * @type        config
     * @template    INDEX_TEMPLATE
     * @user        group: admin, level: 1
     * @menu        group: start
     * @title       {lang id="configmenu"}
     *
     * @access      public
     */
    public function index()
    {
        global $YANA;

        // create options for select-boxes
        $YANA->setVar('LANGUAGEFILES', $YANA->getLanguage()->getLanguages());
        $YANA->setVar('SKINFILES', \Yana\Views\Skin::getSkins());
        // create a list of profiles
        assert('!isset($profiles); // Cannot redeclare var $profiles');
        $profiles = array();
        assert('!isset($profile); // Cannot redeclare var $profile');
        foreach ($YANA->getResource('system:/config/profiledir')->dirlist('*.cfg') as $profile)
        {
            $profiles[$profile] = mb_substr($profile, 0, mb_strrpos($profile, "."));
        }
        // store list
        $YANA->setVar('PROFILES', $profiles);
        unset($profile, $profiles);

        $this->index_plugins();

        $YANA->setVar('USER_IS_EXPERT', $this->_getIsExpert());
        $YANA->getView()->setFunction('updateCheck', array($this, '_updateCheck'));
    }

    /**
     * create administration panel
     *
     * this function does not expect any arguments
     *
     * @type        config
     * @template    INDEX_PLUGINS
     * @user        group: admin, level: 1
     *
     * @access      public
     */
    public function index_plugins()
    {
        $yana = Yana::getInstance();

        /* current state vars */
        $isDefault = Yana::getId() === Yana::getDefault('profile');
        $pluginManager = $yana->getPlugins();

        /**
         * The "options" menu imports a html-file for each plugin.
         * To do so, this section provides the menu with the full path names.
         */

        /* get list of all installed plugins */
        $pluginNames = $pluginManager->getPluginNames();
        $pluginDir = $pluginManager->getPluginDir();

        /* output vars */
        $plugins = array();

        /* get current security level */
        $permission = $yana->getVar('PERMISSION');

        assert('!isset($j); // Cannot redeclare var $j');
        assert('!isset($item); // Cannot redeclare var $item');
        foreach ($pluginNames as $j => $item)
        {
            /* $j is a counter variable */
            /* $item is the name of the plugin */

            /* get configuration */
            /* @var $pluginConfiguration PluginConfigurationClass */
            $pluginConfiguration = $pluginManager->getPluginConfiguration($item);

            /* check if plugin is active */
            if ($pluginManager->isDefaultActive($item)) {
                $active = 2;
            } elseif ($pluginManager->isActive($item)) {
                $active = 1;
            } else {
                $active = 0;
            }

            /* plugin title */
            $pluginTitle = $pluginConfiguration->getTitle();

            /* initialize new entry */
            $plugins[$j] = array
            (
                'ID' => $item,
                /* icon to be shown next to menu entry */
                'IMAGE' => $pluginConfiguration->getIcon(),
                /* indicates if plugin is active */
                'ACTIVE' => $active,
                'NAME' => $pluginTitle
            );

            /* get setup information */
            if ($active !== 0) {
                assert('!isset($method); // Cannot redeclare var $method');
                /* @var $setup PluginMenuEntry */
                foreach ($pluginConfiguration->getMenuEntries('setup') as $action => $setup)
                {
                    // check if action requires to be run in safe-mode
                    /* @var $method PluginConfigurationMethod */
                    $method = $pluginConfiguration->getMethod($action);
                    $requiresDefault = $method->getSafeMode();

                    // evaluate visibility
                    if ($requiresDefault === true && !$isDefault || $requiresDefault === false && $isDefault) {
                        continue;
                    }
                    /* @var $title string */
                    assert('!isset($title); // Cannot redeclare var $title');
                    $title = $pluginTitle;
                    if ($setup->getTitle()) {
                        $title = $setup->getTitle();
                    } elseif ($method->getTitle()) {
                        $title = $method->getTitle();
                    }
                    /* add configuration entry */
                    $plugins[$j]['SETUP'][] = array
                    (
                        'ACTION' => $action,
                        'TITLE' => $title
                    );
                    unset($title);
                } // end foreac
                unset($method);
            }
        } /* end foreach */
        unset($j);
        unset($item);
        uasort($plugins, array($this, '_sort'));

        $yana->setVar('USER_IS_EXPERT', $this->_getIsExpert());
        $yana->setVar('PLUGINS', $plugins);
    }

    /**
     * get user expert mode
     *
     * @access  private
     * @return  bool
     * @ignore
     * @todo    review and replace this function when refactoring user management classes
     */
    private function _getIsExpert()
    {
        if (!isset($this->_isExpert)) {
            // get current user name
            if (!isset($_SESSION['user_name'])) {
                return false;
            } else {
                $userName = (string) $_SESSION['user_name'];
            }
            // get database connection
            $database = SessionManager::getDatasource();
            // get current user-mode
            if ($database->select("user.$userName.user_is_expert")) {
                $this->_isExpert = true;
            } else {
                $this->_isExpert = false;
            }
        }
        return $this->_isExpert;
    }

    /**
     * _sort
     *
     * @access  private
     * @param   array  $a
     * @param   array  $b
     * @return  int
     * @ignore
     */
    private function _sort($a, $b)
    {
        if (@$a['ACTIVE'] < $b['ACTIVE']) {
            return 1;
        } elseif (@$a['ACTIVE'] == $b['ACTIVE']) {
            if ($a['ID'] < $b['ID']) {
                return -1;
            } elseif ($a['ID'] > $b['ID']) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return -1;
        }
    }

    /**
     * Activate / deactivate plugins
     *
     * This computes the user form data provided by $_POST.
     * Where the provided key is the id of a plugin and the value
     * is wether bool(true) to activate or bool(false) to deactive
     * the plugin.
     *
     * Note: it does'nt matter here, if a plugin named $key really
     * exists, since the PluginManager-Class does this checking for us.
     *
     * parameters taken:
     *
     * <ul>
     * <li> array plugins     new list of active plugins</li>
     * <li> array pluginlist  list of all plugins</li>
     * </ul>
     *
     * Expected outcome:
     *
     * Plugins in $pluginlist not mentioned in $plugins are deactivated and vice versa.
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    MESSAGE
     * @onsuccess   goto: index
     * @onerror     goto: index
     * @safemode    true
     *
     * @access      public
     * @param       array  $pluginlist  all plugins
     * @param       array  $plugins     activated plugins
     * @return      bool
     */
    public function save_pluginlist(array $pluginlist, array $plugins)
    {
        $pluginManager = \Yana\Plugins\Manager::getInstance();
        foreach($pluginlist as $plugin)
        {
            /* We don't mind, wether $plugin is a plugin or not, since
             * the PluginManager does this checking for us.
             */
            try {
                if (in_array($plugin, $plugins)) {
                    $pluginManager->setActive($plugin, \Yana\Plugins\ActivityEnumeration::ACTIVE);
                } else {
                    $pluginManager->setActive($plugin, \Yana\Plugins\ActivityEnumeration::INACTIVE);
                }
            } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                throw new InvalidInputWarning();
            }
        }
        /* save changes and refresh the plugin cache */
        return $this->refresh_pluginlist();
    }

    /**
     * refresh the plugin cache
     *
     * this function does not expect any arguments
     *
     * Use this after you installed / uninstalled a plugin to refresh the cache.
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    MESSAGE
     * @onsuccess   goto: index
     * @onerror     goto: index
     * @safemode    true
     *
     * @access      public
     * @return      bool
     */
    public function refresh_pluginlist()
    {
        $pluginManager = \Yana\Plugins\Manager::getInstance();
        if ($pluginManager->refreshPluginFile()) {
            SessionManager::refreshPluginSecuritySettings();
            \Yana\Plugins\Menu::clearCache();
            return true;
        } else {
            return false;
        }
    }

    /**
     * event handler
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    MESSAGE
     * @onsuccess   goto: index
     * @onerror     goto: index
     * @safemode    true
     *
     * @access      public
     * @param       string  $id  new profile id
     */
    public function config_create_profile($id)
    {
        global $YANA;
        $configFile = $YANA->getResource('system:/config/profiledir/default_config.sml');
        $REF = $configFile->getVars();
        $profileDir = $YANA->getResource("system:/config/profiledir");
        $profileDir = $profileDir->getPath();
        $newProfile = new \Yana\Files\SML("{$profileDir}{$id}.cfg", CASE_MIXED);
        if ($newProfile->exists()) {
            $error = new \Yana\Core\Exceptions\AlreadyExistsException();
            throw $error->setId($id);
        }
        try {
            $newProfile->create();
        } catch (\Exception $e) {
            $message = "Directory is not writable or permission denied.";
            $code = \Yana\Log\TypeEnumeration::ERROR;
            $error = new \Yana\Core\Exceptions\Files\NotCreatedException($message, $code, $e);
            throw $error->setFilename($newProfile->getPath());
        }
        if (!$newProfile->setVars($REF)) {
            throw new Error();
        }
        if (!$newProfile->write()) {
            $message = "Changes to profile could not be saved.";
            $code = \Yana\Log\TypeEnumeration::ERROR;
            $error = new \Yana\Core\Exceptions\Files\NotWriteableException($message, $code);
            throw $error->setFilename($newProfile->getPath());
        }
    }

    /**
     * event handler
     *
     * @type        config
     * @user        level: 1
     * @template    MESSAGE
     * @onsuccess   goto: index
     * @onerror     goto: index
     *
     * @access      public
     * @return      bool
     */
    public function config_usermode()
    {
        /* this function expects no arguments */

        // get database connection
        $database = SessionManager::getDatasource();

        // get current user name
        if (!isset($_SESSION['user_name'])) {
            return false;
        } else {
            $userName = (string) $_SESSION['user_name'];
        }

        // get current user-mode
        $userMode = $database->select("user.$userName.user_is_expert");

        // negate current user-mode setting
        if (!empty($userMode)) {
            $userMode = false;
        } else {
            $userMode = true;
        }

        // error - update operation failed
        if (!$database->update("user.$userName.user_is_expert", $userMode)) {
            \Yana\Log\LogManager::getLogger()->addLog("Unable to update user '$userName'.");
            return false;
        }

        // error - unable to write changes
        if (!$database->write()) {
            \Yana\Log\LogManager::getLogger()->addLog("Unable to commit update to user '$userName'.");
            return false;
        }

        // success
        Yana::getInstance()->clearCache();
        return true;
    }

    /**
     * Display "about" screen
     *
     * parameters taken:
     *
     * <ul>
     * <li> string type    on of "plugin"|"skin"|"language"</li>
     * <li> string target  name of file that contains information</li>
     * </ul>
     *
     * @type        read
     * @user        level: 1
     * @template    ABOUT_TEMPLATE
     * @onerror     goto: index, text: Yana\Core\Exceptions\Files\NotFoundException
     * @language    admin
     *
     * @access      public
     * @param       string  $type    type of requested about page (plugin, skin, language)
     * @param       string  $target  id of chosen plugin, skin or language pack
     * @return      bool
     */
    public function about($type, $target)
    {
        global $YANA;
        $info = array();
        switch ($type)
        {
            case "plugin":
                $pluginConfiguration = $YANA->getPlugins()->getPluginConfiguration($target);
                $info = array
                (
                    'NAME' => $pluginConfiguration->getTitle(),
                    'LAST_CHANGE' => $pluginConfiguration->getLastModified(),
                    'VERSION' => $pluginConfiguration->getVersion(),
                    'LOGO' => $pluginConfiguration->getPreviewImage(),
                    'AUTHOR' => $pluginConfiguration->getAuthor(),
                    'UPDATE' => $pluginConfiguration->getUrl(),
                    'DESCRIPTION' => $pluginConfiguration->getText()
                );
            break;
            case "skin":
                $skin = new \Yana\Views\Skin($target);
                $info = array
                (
                    'NAME' => $skin->getTitle(),
                    'LAST_CHANGE' => $skin->getLastModified(),
                    'LOGO' => $skin->getPreviewImage(),
                    'AUTHOR' => $skin->getAuthor(),
                    'CONTACT' => $skin->getUrl(),
                    'DESCRIPTION' => $skin->getText()
                );
            break;
            case "language":
                $info = $YANA->getLanguage()->getInfo($target);
            break;
            default:
                return false;
        }
        if (!empty($info)) {
            $YANA->setVar("INFO", $info);
            return true;
        } else {
            return false;
        }
    }

    /**
     * <<smarty function>> updateCheck
     *
     * This checks for updates and returns the result.
     * If the server is not reachable it returns a link instead.
     *
     * Note: since version look-ups can be very time consuming
     * (e.g. if the server is slow or temporarily unreachable)
     * this function caches the results for 8 hours before
     * searching for updates again.
     *
     * @static
     * @access  public
     * @param   array   $params
     * @return  int
     * @since   2.9.11
     * @ignore
     */
    public static function _updateCheck(array $params)
    {
        assert('isset($GLOBALS["YANA"]); // Global var $YANA not set');
        global $YANA;

        /* cache results */
        $tempDir = $YANA->getVar('TEMPDIR');
        $tempFile = "{$tempDir}updatecheck." . @$_SESSION['language'] . ".tmp";
        if (is_file($tempFile) && filemtime($tempFile) > time() - 28800 /* = 8h */) {
            return file_get_contents($tempFile);
        }

        /* create link to check for new version */
        $url = Yana::getDefault('UPDATE_SERVER');
        $url = str_replace(YANA_LEFT_DELIMITER . '$VERSION' . YANA_RIGHT_DELIMITER, YANA_VERSION, $url);
        $url = str_replace(YANA_LEFT_DELIMITER . '$IS_STABLE' . YANA_RIGHT_DELIMITER, YANA_IS_STABLE, $url);
        $url = str_replace(YANA_LEFT_DELIMITER . '$LANG' . YANA_RIGHT_DELIMITER, @$_SESSION['language'], $url);
        $href = str_replace(YANA_LEFT_DELIMITER . '$AS_NUMBER' . YANA_RIGHT_DELIMITER, '', $url);
        $url = str_replace(YANA_LEFT_DELIMITER . '$AS_NUMBER' . YANA_RIGHT_DELIMITER, 'true', $url);
        $url = html_entity_decode($url);
        $link = '<a href="' . $href .  '" target="_blank">' . $YANA->getLanguage()->getVar('INDEX_13') . '</a>';

        assert('!isset($urlInfo); // Cannot redeclare var $urlInfo');
        assert('!isset($errno); // Cannot redeclare var $errno');
        assert('!isset($errstr); // Cannot redeclare var $errstr');
        assert('!isset($latestVersion); // Cannot redeclare var $latestVersion');
        $latestVersion = "";

        /*
         * 1) try using url_fopen
         */
        if (!ini_get('allow_url_fopen') == true) {
            $latestVersion = mb_substr(file_get_contents($url), 0, 20);
            if (empty($latestVersion)) {

                file_put_contents($tempFile, $link);
                return $link;
            }

        /*
         * 2) try to connect using a socket
         *
         * this will only be done if step 1) failed
         */
        } else {

            $urlInfo = parse_url($url);

            if ($urlInfo !== false) {
                $fsock = @fsockopen($urlInfo['host'], 80, $errno, $errstr, 30);
            }

            if ($urlInfo !== false && ($fsock) != false) {
                if (!empty($errno)) {
                    $message = 'Update-check failed to open connection to server. Reason: ' . $errstr;
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                    @fclose($fsock);
                    file_put_contents($tempFile, $link);
                    return $link;
                }
                unset($errno, $errstr);

                /* send request header */
                @fputs($fsock, "GET " . (isset($urlInfo['path']) ? $urlInfo['path'] : '/') . (isset($urlInfo['query']) ? '?' . $urlInfo['query'] : '') . " HTTP/1.0\r\n");
                @fputs($fsock, "HOST: " . $urlInfo['host'] . "\r\n");
                @fputs($fsock, "Connection: close\r\n\r\n");

                /* read response */
                while (!@feof($fsock))
                {
                    $latestVersion .= @fread($fsock, 1024);
                }
                @fclose($fsock);
                unset($fsock);

                /* cut off header data */
                $latestVersion = preg_replace('/^.*?\r\n\r\n(.*)$/si', '$1', $latestVersion);
                $latestVersion = mb_substr($latestVersion, 0, 20);

                /* print reply */
                if (empty($latestVersion)) {
                    file_put_contents($tempFile, $link);
                    return $link;
                }
                unset($urlInfo);

            /*
             * 3) return link, users will have to click it
             *
             * this will only be done if steps 1) and 2) failed
             */
            } else {
                file_put_contents($tempFile, $link);
                return $link;
            }
        } // end if

        /**
         * Compare versions and return result;
         */
        if (version_compare(YANA_VERSION, $latestVersion) < 0) {
            $link = $YANA->getLanguage()->getVar('INDEX_15') . ': ' .
                    htmlspecialchars($latestVersion, ENT_COMPAT, 'UTF-8') .
                    ' <a href="' . $href .  '" target="_blank">' . $YANA->getLanguage()->getVar('INDEX_16') . '</a>';
            file_put_contents($tempFile, $link);
            return $link;

        } else {
            $link = $YANA->getLanguage()->getVar('INDEX_14') . ': ' . YANA_VERSION;
            file_put_contents($tempFile, $link);
            return $link;
        }

    }

}

?>