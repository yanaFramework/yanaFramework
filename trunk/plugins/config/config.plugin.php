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

namespace Plugins\Config;

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
class ConfigPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * is user expert mode
     *
     * @var  bool
     */
    private $_isExpert = null;

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
     */
    public function index()
    {
        $YANA = $this->_getApplication();
        /* @var $YANA \Yana\Application */

        // create options for select-boxes
        $YANA->setVar('LANGUAGEFILES', $YANA->getLanguage()->getLanguages());
        $YANA->setVar('SKINFILES', $YANA->getSkin()->getSkins());
        // create a list of profiles
        /* @var $profileDirectory \Yana\Files\Dir */
        assert(!isset($profileDirectory), 'Cannot redeclare var $profileDirectory');
        $profileDirectory = $YANA->getResource('system:/config/profiledir');
        assert(!isset($profiles), 'Cannot redeclare var $profiles');
        $profiles = array();
        assert(!isset($profile), 'Cannot redeclare var $profile');
        foreach ($profileDirectory->listFiles('*.cfg') as $profile)
        {
            $profiles[$profile] = mb_substr($profile, 0, mb_strrpos($profile, "."));
        }
        // store list
        $YANA->setVar('PROFILES', $profiles);
        unset($profileDirectory, $profile, $profiles);

        $this->index_plugins();

        $YANA->setVar('USER_IS_EXPERT', $this->_getIsExpert());
        $language = $YANA->getLanguage();
        $updateChecker = new \Plugins\Config\UpdateChecker($language, $YANA->getDefault('UPDATE_SERVER'));
        $cacheDirPath = $YANA->getVar('TEMPDIR') . '/pluginConfig/';
        $cacheDir = new \Yana\Files\Dir($cacheDirPath);
        $cacheLifetime = 28800; // 8 hours
        $cacheAdapter = new \Yana\Data\Adapters\FileCacheAdapter($cacheDir, $cacheLifetime);
        $updateChecker->setCache($cacheAdapter);

        $view = $YANA->getView();
        try {
            $view->setFunction('updateCheck', $updateChecker);

        } catch (\Yana\Views\Managers\RegistrationException $e) {
            $view->unsetFunction('updateCheck');
            $view->setFunction('updateCheck', $updateChecker);
            unset($e);
        }
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
        $yana = $this->_getApplication();

        /* current state vars */
        $isDefault = $yana->getProfileId() === $yana->getDefault('profile');
        $pluginManager = $yana->getPlugins();

        /**
         * The "options" menu imports a html-file for each plugin.
         * To do so, this section provides the menu with the full path names.
         */

        /* get list of all installed plugins */
        $pluginNames = $pluginManager->getPluginNames();

        /* output vars */
        $plugins = array();

        assert(!isset($j), 'Cannot redeclare var $j');
        assert(!isset($item), 'Cannot redeclare var $item');
        foreach ($pluginNames as $j => $item)
        {
            /* $j is a counter variable */
            /* $item is the name of the plugin */

            /* get configuration */
            /* @var $pluginConfiguration PluginConfigurationClass */
            $pluginConfiguration = $pluginManager->getPluginConfiguration($item);

            /* check if plugin is active */
            if ($pluginManager->isActiveByDefault($item)) {
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
                'NAME' => $pluginTitle,
                'SETUP' => array()
            );

            /* get setup information */
            if ($active !== 0) {
                assert(!isset($method), 'Cannot redeclare var $method');
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
                    assert(!isset($title), 'Cannot redeclare var $title');
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
     */
    private function _getIsExpert()
    {
        if (!isset($this->_isExpert)) {
            // get current user name
            $userName = $this->_getSession()->getCurrentUserName();
            if ($userName === "") {
                return false;
            }
            $this->_isExpert = (bool) $this->_getSecurityFacade()->loadUser($userName)->isExpert();
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
     * @throws      \Yana\Core\Exceptions\NotFoundException  when a selected plugin was not found
     */
    public function save_pluginlist(array $pluginlist, array $plugins)
    {
        $pluginManager = $this->_getPluginsFacade();
        foreach($pluginlist as $plugin)
        {
            /* We don't mind, wether $plugin is a plugin or not, since
             * the PluginManager does this checking for us.
             */
            if (in_array($plugin, $plugins)) {
                $pluginManager->activate($plugin); // may throw NotFoundException
            } else {
                $pluginManager->deactivate($plugin); // may throw NotFoundException
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
        $this->_getApplication()->refreshSettings(); // may throw exceptions
        return true;
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
        $YANA = $this->_getApplication();
        $configFile = $YANA->getResource('system:/config/profiledir/default_config.sml');
        $REF = $configFile->getVars();
        $profileDir = $YANA->getResource("system:/config/profiledir");
        $profileDir = $profileDir->getPath();
        // Make sure the file name is lower-case so that case doesn't matter later
        $newProfile = new \Yana\Files\SML($profileDir . \Yana\Util\Strings::toLowerCase($id) . ".cfg", CASE_MIXED);
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
        $newProfile->setVars($REF);
        try {
            $newProfile->write();

        } catch (\Exception $e) {
            $message = "Changes to profile could not be saved.";
            $code = \Yana\Log\TypeEnumeration::ERROR;
            $error = new \Yana\Core\Exceptions\Files\NotWriteableException($message, $code, $e);
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

        $userName = $this->_getSession()->getCurrentUserName();
        if ($userName === "") {
            return false;
        }

        try {
            $user = $this->_getSecurityFacade()->loadUser($userName); // may throw exception
            $user->setExpert(!$user->isExpert()); // negate current user-mode setting
            $user->saveChanges(); // may throw exception

        } catch (Exception $e) { // error - update operation failed

            $this->_getApplication()->getLogger()->addLog("Unable to update user '$userName'.");
            return false;
        }

        // success
        $this->_getApplication()->clearCache();
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
        /* @var $YANA \Yana\Application */
        $YANA = $this->_getApplication();
        $info = array(
            'VERSION' => '1.0'
        );
        switch ($type)
        {
            case "plugin":
                $metaData = $YANA->getPlugins()->getPluginConfiguration($target);
                $info['VERSION'] = $metaData->getVersion();
                $info['UPDATE'] = $metaData->getUrl();
            break;
            case "skin":
                $skin = new \Yana\Views\Skins\Skin($target);
                $metaData = $skin->getMetaData();
                $info['CONTACT'] = $metaData->getUrl();
            break;
            case "language":
                $metaData = $YANA->getLanguage()->getMetaData($target);
                $info['CONTACT'] = $metaData->getUrl();
            break;
            default:
                return false;
        }
        $languageManager = $YANA->getLanguage();
        $language = $languageManager->getLanguage();
        $country = $languageManager->getCountry();
        // fill fields
        $info['NAME'] = $metaData->getTitle();
        $info['LAST_CHANGE'] = $metaData->getLastModified();
        $info['LOGO'] = $metaData->getPreviewImage();
        $info['AUTHOR'] = $metaData->getAuthor();
        $info['DESCRIPTION'] = $metaData->getText($language, $country);

        $YANA->setVar("INFO", $info);
        return true;
    }

}

?>