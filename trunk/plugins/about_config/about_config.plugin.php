<?php
/**
 * Base configuration
 *
 * This plugin provides the basic functions to create
 * and modify custom profile settings.
 *
 * {@translation
 *
 *    de: Basiskonfiguration
 *
 *        Dieses Plugin stellt die grundlegenden Funktionen zur Verfügung
 *        um Profileinstellungen zu erzeugen oder zu modifizieren.
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @active     always
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * Configration profiles
 *
 * This plugin provides the basic functions to create
 * and modify custom profile settings.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_about_config extends StdClass implements IsPlugin
{

    /**
     * Default event handler
     *
     * returns bool(true) on success and bool(false) on error
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
     * create a menu to edit default settings of the framework
     *
     * this function does not expect any arguments
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    CONFIG_LAYOUT_TEMPLATE
     * @safemode    true
     * @menu        group: setup
     * @title       {lang id="configdisplay"}
     * @language    admin
     *
     * @access      public
     * @return      bool
     */
    public function config_default_layout()
    {
        /* this function expects no arguments */

        global $YANA;

        /* set a description text */
        $YANA->setVar("DESCRIPTION", $YANA->getLanguage()->getVar("DESCR_ADMIN"));

        /* check, if the output file is writeable */
        $configFile = $YANA->getResource('system:/config/profiledir/default_config.sml');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());

        return true;
    }

    /**
     * create a menu to edit default settings of the framework
     *
     * this function does not expect any arguments
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    CONFIG_FILESET_TEMPLATE
     * @safemode    true
     * @menu        group: setup
     * @title       {lang id="configsetup"}
     * @language    admin
     *
     * @access      public
     * @return      bool
     */
    public function config_default_fileset ()
    {
        /* this function expects no arguments */

        global $YANA;

        /* set a description text */
        $YANA->setVar("DESCRIPTION", $YANA->getLanguage()->getVar("DESCR_ADMIN"));

        /* check, if the output file is writeable */
        $configFile = $YANA->getResource('system:/config/profiledir/default_config.sml');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());

        return true;
    }

    /**
     * create a menu to edit profile settings
     *
     * this function does not expect any arguments
     *
     * Note: if no profile specific settings are available,
     * the framework automatically falls back to its default settings.
     *
     * @type        config
     * @user        group: admin, level: 60
     * @template    CONFIG_LAYOUT_TEMPLATE
     * @menu        group: setup
     * @title       {lang id="configdisplay"}
     * @safemode    false
     * @language    admin
     *
     * @access      public
     * @return      bool
     */
    public function config_layout ()
    {
        /* this function expects no arguments */

        global $YANA;

        /* set a description text */
        $YANA->setVar("DESCRIPTION", $YANA->getLanguage()->getVar("DESCR_MOD"));

        /* check, if the output file is writeable */
        $configFile = $YANA->getResource('system:/config/profiledir/config.sml');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());

        return true;
    }

    /**
     * create a menu to edit profile settings
     *
     * this function does not expect any arguments
     *
     * Note: if no profile specific settings are available,
     * the framework automatically falls back to its default settings.
     *
     * @type        config
     * @user        group: admin, level: 60
     * @template    CONFIG_FILESET_TEMPLATE
     * @menu        group: setup
     * @title       {lang id="configsetup"}
     * @safemode    false
     * @language    admin
     *
     * @access      public
     * @return      bool
     */
    public function config_fileset ()
    {
        /* this function expects no arguments */

        global $YANA;

        /* set a description text */
        $YANA->setVar("DESCRIPTION", $YANA->getLanguage()->getVar("DESCR_MOD"));

        /* check, if the output file is writeable */
        $configFile = $YANA->getResource('system:/config/profiledir/config.sml');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());

        return true;
    }

    /**
     * save form data to the frameworks default configuration
     *
     * This function may be used by other plugins to provide configuration
     * menus with practically no coding. See manual cookbook for an example.
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    MESSAGE
     * @onsuccess   goto: index
     * @onerror     goto: index
     * @safemode    true
     *
     * @access      public
     * @param       array  $ARGS  user form data
     * @return      bool
     */
    public function set_config_default (array $ARGS)
    {
        global $YANA;

        /* first reinitialize the config file, so we can be sure it contains the most recent data */
        /* @var $configFile SML */
        assert('!isset($configFile); // Cannot redeclare var $configFile');
        $configFile = $YANA->getResource('system:/config/profiledir/default_config.sml');
        $configFile->read();

        /* then overwrite previous settings with new settings provided by the user form */
        $array = self::_genProfile($ARGS);
        assert('is_array($array); /* unexpected result: $array */');
        if (!is_array($array)) {
            return false;
        }
        $configFile->setVar('PROFILE', $array);
        /* flush the template cache, so changes will become visible immediately */
        $YANA->clearCache();

        /* write changes to disk, if it fails, issue an error and provide a log entry. */
        if (!$configFile->failSafeWrite()) {
            Log::report("Unable to write file", E_USER_WARNING, $configFile->getPath());
            return false;
        } else {
            return true;
        }
    }

    /**
     * save form data to the current profile's configuration
     *
     * This function may be used by other plugins to provide configuration
     * menus with practically no coding. See manual cookbook for an example.
     *
     * @type        config
     * @user        group: admin, level: 60
     * @template    MESSAGE
     * @onsuccess   goto: index
     * @onerror     goto: index
     * @safemode    false
     *
     * @access      public
     * @param       array  $ARGS  user form data
     * @return      bool
     */
    public function set_config_profile (array $ARGS)
    {
        global $YANA;
        $configFile = $YANA->getResource('system:/config/profiledir/config.sml');
        $configFile->read();
        $profile = self::_genProfile($ARGS);
        $configFile->setVar('PROFILE', $profile);
        $YANA->clearCache();

        if (!$configFile->failSafeWrite()) {
            Log::report("Unable to write file", E_USER_WARNING, $configFile->getPath());
            return false;
        } else {
            return true;
        }
    }

    /**
     * read log file
     *
     * this function does not expect any arguments
     *
     * @type        read
     * @user        group: admin, level: 100
     * @template    templates/log.html.tpl
     * @language    admin
     *
     * @access      public
     * @return      bool
     */
    public function config_read_log ()
    {
        return true;
    }

    /**
     * _genAdminFile
     *
     * array _genAdminFile(array $input)
     *
     * @access  private
     * @param   array   $input
     * @ignore
     */
    private static function _genProfile(array $input)
    {
        $input = array_change_key_case($input, CASE_UPPER);

        unset($input['ID']);
        unset($input['ACTION']);
        unset($input['TARGET']);
        unset($input['YANA_FORM_ID']);
        unset($input[mb_strtoupper(session_name())]);

        $profile = $GLOBALS['YANA']->getVar('PROFILE');
        foreach ($input as $key => $element)
        {
            if (preg_match("/\//", $key)) {
                $key = preg_replace("/\//", ".", $key);
                Hashtable::set($profile, $key, $element);
            } else {
                $profile[$key] = $element;
            }

        }
        return $profile;
    }

}

?>
