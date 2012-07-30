<?php
/**
 * YANA library
 *
 * Primary controller class
 *
 * Software:  Yana PHP-Framework
 * Version:   {VERSION} - {DATE}
 * License:   GNU GPL  http://www.gnu.org/licenses/
 *
 * This program: can be redistributed and/or modified under the
 * terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 *
 * This notice MAY NOT be removed.
 *
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

/**
 * <<Facade>> <<Singleton>> Yana
 *
 * This is a primary controller and application loader for the Yana Framework.
 * It implements the "facade" pattern and thus delegates calls to underlying classes and methods.
 *
 * Example:
 * <code>
 * // get the current instance
 * global $YANA;
 * // handle request
 * $YANA->callAction($_REQUEST['action']);
 * // output results
 * $YANA->outputResults();
 * </code>
 *
 * @package     yana
 * @subpackage  core
 */
final class Yana extends \Yana\Core\AbstractSingleton
    implements \Yana\Report\IsReportable, \Yana\Log\IsLogable, \Yana\Core\IsVarContainer
{

    /**
     * This is a place-holder for the singleton's instance
     *
     * @var  Yana
     */
    private static $_instance = null;

    /**
     * System configuration file
     *
     * @var  \Yana\Util\XmlArray
     */
    private static $_config = null;

    /**
     * profile id
     *
     * @var  string
     */
    private static $_id = null;

    /**
     * action parameter
     *
     * @var  string
     */
    private static $_action = null;

    /**
     * safe-mode settings
     *
     * false = default-mode (use profile settings)
       true  = safe-mode    (use default profile)
     *
     * @var  bool
     * @ignore
     */
    protected $_isSafeMode = null;

    /**
     * to communicate with plugins
     *
     * @var  \Yana\Plugins\Manager
     */
    private $_plugins = null;

    /**
     * to load language strings
     *
     * @var  Language
     */
    private $_language = null;

    /**
     * to load skins and templates
     *
     * @var  \Yana\Views\Skin
     */
    private $_skin = null;

    /**
     * to read and write data to the global registry
     *
     * @var  \Yana\VDrive\Registry
     */
    private $_registry = null;

    /**
     * to read and write user data and permissions
     *
     * @var  SessionManager
     */
    private $_session = null;

    /**
     * the currently selected template
     *
     * @var  \Yana\Views\Manager
     */
    private $_view = null;

    /**
     * List of logger classes.
     *
     * @var  array
     */
    private $_loggers = array();

    /**
     * caches database connections
     *
     * @var  \Yana\Db\IsConnection[]
     */
    private static $_connections = array();

    /**
     * get instance of this class
     *
     * Creates an instance if there is none.
     * Then it returns a reference to this (single) instance.
     *
     * Example:
     * <code>
     * Yana::setConfiguration("config/system.config");
     * global $YANA;
     * $YANA = Yana::getInstance();
     * </code>
     *
     * Note: you only need to call Yana::setConfiguration() once, prior to the initialization of the framework
     * and only if you wish to use other then the default values.
     * Otherwise it's enough to use Yana::getInstance() without anything else.
     *
     * @return  Yana
     */
    public static function &getInstance()
    {
        if (!isset(self::$_instance)) {
            /* auto-load configuration file */
            if (empty(self::$_config)) {
                self::setConfiguration();
            }
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * <<Singleton>> Constructor
     *
     * This function creates a new instance of the framework.
     * Note that you may only operate one instance at a time.
     */
    private function __construct()
    {
        $this->_loggers = new \Yana\Log\LoggerCollection();
    }

    /**
     * application is in safe-mode
     *
     * @return  bool
     * @ignore
     */
    protected function isSafemode()
    {
        if (!isset($this->isSafemode)) {
            $eventConfiguration = $this->getPlugins()->getEventConfiguration($this->_getAction());
            if ($eventConfiguration instanceof \Yana\Plugins\Configs\MethodConfiguration) {
                $this->_isSafeMode = ($eventConfiguration->getSafemode() === true);
            } else {
                $this->_isSafeMode = !empty(self::$_config->default->event->{\Yana\Plugins\Annotations\Enumeration::SAFEMODE});
            }
        }
        return $this->_isSafeMode;
    }

    /**
     * activate CD-ROM settings
     *
     * Sets the configuration to CD-ROM settings.
     * Configuration is expected to be loaded prior to calling this function.
     */
    private static function _activateCDApplication()
    {
        assert('isset(self::$_config); // Configuration must be loaded first');
        if (!file_exists(YANA_CDROM_DIR)) {
            mkdir(YANA_CDROM_DIR);
            chmod(YANA_CDROM_DIR, 0777);
        }
        $configDir = (string) self::$_config->configdir;
        self::_setRealPaths(YANA_CDROM_DIR);
        $tempDir = (string) self::$_config->tempdir;
        if (!file_exists($tempDir)) {
            mkdir($tempDir);
            chmod($tempDir, 0777);
        }
        if (!file_exists($configDir)) {
            $configSrc = new \Yana\Files\Dir($configDir);
            $configSrc->copy($configDir, true, 0777, true, null, '/^(?!\.blob$)/i', true);
            unset($configSrc);
        }
        unset($configDir);
    }

    /**
     * set directory references to real paths
     *
     * @param  string  $cwd  current working directory
     */
    private static function _setRealPaths($cwd)
    {
        $cwd .= '/';
        self::$_config->tempdir = $cwd . (string) self::$_config->tempdir;
        self::$_config->configdir = $cwd . (string) self::$_config->configdir;
        self::$_config->configdrive = $cwd . (string) self::$_config->configdrive;
        self::$_config->pluginfile = $cwd . (string) self::$_config->pluginfile;
        \Yana\Db\AbstractConnection::setTempDir((string) self::$_config->tempdir);
    }

    /**
     * set up a system configuration file
     *
     * The system config file contains default- and startup-settings
     * to initialize this class.
     *
     * Example:
     * <code>
     * Yana::setConfiguration("config/system.config");
     * </code>
     *
     * @param  string  $filename  path to system.config
     */
    public static function setConfiguration($filename = null)
    {
        if ($filename === null) {
            $filename = __DIR__ . "/../../config/system.config.xml";
        }
        assert('is_string($filename);   // Wrong type for argument 1. String expected');
        assert('is_file($filename);     // Invalid argument 1. Input is not a file.');
        assert('is_readable($filename); // Invalid argument 1. Configuration file is not readable.');
        // get System Config file
        self::$_config = simplexml_load_file($filename, '\Yana\Util\XmlArray');
        // load CD-ROM application settings on demand
        if (YANA_CDROM === true) {
            self::_activateCDApplication();
        } else {
            self::_setRealPaths(getcwd());
        }
        // initialize directories
        if (!empty(self::$_config->skindir)) {
            \Yana\Views\Skin::setBaseDirectory((string) self::$_config->skindir);
        }
        if (isset(self::$_config->pluginfile)) {
            \Yana\Plugins\Manager::setPath((string) self::$_config->pluginfile, (string) self::$_config->plugindir);
        }
    }

    /**
     * execute an action
     *
     * Resolves event and calls plugin(s), with the given arguments.
     *
     * Example:
     * <code>
     * // handle current action
     * $YANA->callAction();
     * // same as above
     * $YANA->callAction($_REQUEST['action'], $_REQUEST);
     * // handle user defined event 'test'
     * $myArgs = array('foo' => 'bar');
     * $success = $YANA->callAction('test', $myArgs);
     * if ($success) {
     *     print "Success!\n";
     * } else {
     *     print "Encountered an error.\n";
     * }
     * </code>
     *
     * @param   string  $action  script action parameter
     * @param   array   $args    array of passed arguments
     * @return  bool
     * @throws  \Yana\Core\Exceptions\InvalidActionException  when the event is undefined
     */
    public function callAction($action = "", array $args = null)
    {
        assert('is_string($action); // Invalid argument $action: string expected');

        /**
         * 1) check for default arguments
         */
        if (empty($action)) {
            $action = $this->_getAction();
        }
        if (is_null($args)) {
            $args = \Yana\Core\Request::getVars();
        }

        /**
         * 2) load language strings
         */
        assert('!isset($eventConfiguration); // Cannot redeclare var $eventConfiguration');
        assert('!isset($plugins); // Cannot redeclare var $plugins');
        $plugins = $this->getPlugins();
        $eventConfiguration = $plugins->getEventConfiguration($action);
        if (!($eventConfiguration instanceof \Yana\Plugins\Configs\MethodConfiguration)) {
            $error = new \Yana\Core\Exceptions\InvalidActionException();
            $error->setAction($action);
            return false;
        }

        assert('!isset($paths); // Cannot redeclare var $paths');
        $paths = $eventConfiguration->getPaths();
        if ($paths) {
            assert('!isset($language); // Cannot redeclare var $language');
            $language = $this->getLanguage();
            // mount language directory, if it exists
            assert('!isset($langDir); // Cannot redeclare var $langDir');
            foreach ($eventConfiguration->getPaths() as $langDir)
            {
                $langDir = $langDir . "/languages/";
                if (is_dir($langDir)) {
                    $language->addDirectory($langDir);
                }
            }
            unset($langDir, $language);
        }
        unset($paths);
        // load language files
        assert('!isset($languages); // Cannot redeclare var $languages');
        $languages = $eventConfiguration->getLanguages();
        if ($languages) {
            assert('!isset($language); // Cannot redeclare var $language');
            $language = $this->getLanguage();
            assert('!isset($languageId); // Cannot redeclare var $languageId');
            foreach ($languages as $languageId)
            {
                $language->readFile($languageId);
            }
            unset($language, $languageId);
        }
        unset($languages);
        assert('!isset($styles); // Cannot redeclare var $styles');
        $styles = $eventConfiguration->getStyles();
        if ($styles) {
            $this->getView()->addStyles($styles);
        }
        unset($styles);
        assert('!isset($scripts); // Cannot redeclare var $scripts');
        $scripts = $eventConfiguration->getScripts();
        if ($scripts) {
            $this->getView()->addScripts($scripts);
        }
        unset($scripts);

        /**
         * 3) handle event
         *
         * Returns bool(true) on success and bool(false) otherwise.
         */
        try {

            $result = $plugins->broadcastEvent($action, $args);

        } catch (\Exception $e) {
            $message = get_class($e) . ': ' . $e->getMessage() . ' Thrown in ' . $e->getFile() .
                ' on line ' . $e->getLine();
            trigger_error($message, E_USER_WARNING);
            return false;
        }
        if ($result !== false) {
            /* Create timestamp to provide information for read-stability isolation level */
            $_SESSION['transaction_isolation_created'] = time();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get current action.
     *
     * @internal This also checks the action parameter for validity.
     *
     * Work-around for IE-bug.
     *
     * Example:
     * <code>
     * <form><button type="submit" name="a" value="1">2</button></form>
     * </code>
     *
     * IE sends a=2 instead of a=1. This is because IE automatically
     * handles button-tags as input tags and copies the caption text
     * to the value attribute. This is WRONG according to W3C.
     *
     * Solution:
     * <code>
     * <form><input type="submit" name="a[1]" value="2"/></form>
     * if ($a[1]) $a = 1;
     * </code>
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\InvalidActionException  when the event is undefined
     * @ignore
     */
    protected function _getAction()
    {
        if (!isset(self::$_action)) {
            $action = \Yana\Core\Request::getVars('action');
            // work-around for IE-bug
            if (is_array($action)) {
                if (count($action) === 1) {
                    // action[name]=1 -> action=name
                    reset($action); // rewind iterator
                    $action = key($action); // get first key
                }
            }
            // error checking
            switch (true)
            {
                case isset($action) && !is_string($action):
                case isset($action) && !$this->getPlugins()->isEvent($action):
                    $error = new \Yana\Core\Exceptions\InvalidActionException();
                    $error->setAction($action);
                // fall through
                case empty($action):
                    assert('!empty(self::$_config->default->homepage); // Configuration missing default homepage.');
                    $action = (string) self::$_config->default->homepage;
                // fall through
                default:
                    $action = mb_strtolower($action);
                break;
            }
            self::$_action = $action;
        }
        return self::$_action;
    }

    /**
     * Get session manager instance.
     *
     * The SessionManager class is used to manage user information
     * and resolve permissions.
     */
    public function getSession()
    {
        if (!isset($this->_session)) {
            $this->_session = SessionManager::getInstance();
        }
        return $this->_session;
    }

    /**
     * Get registry.
     *
     * This returns the registry. If none exists, a new instance is created.
     * These settings may be read later by using Yana::getVar().
     *
     * @return  \Yana\VDrive\Registry
     * @throws  \Yana\Core\Exceptions\NotReadableException    when Registry file is not readable
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when Registry file could not be read or contains invalid syntax
     */
    public function getRegistry()
    {
        if (!isset($this->_registry)) {
            // path to cache file
            $cacheFile = (string) self::$_config->tempdir . 'registry_' . self::getId() . '.tmp';

            // get configuration mode
            \Yana\VDrive\Registry::useDefaults($this->isSafemode());

            if (YANA_CACHE_ACTIVE === true && file_exists($cacheFile)) {
                $this->_registry = unserialize(file_get_contents($cacheFile));
                assert('$this->_registry instanceof \Yana\VDrive\Registry;');
            } else {
                $this->_registry = new \Yana\VDrive\Registry((string) self::$_config->configdrive, "");
                $this->_registry->setVar("ID", self::getId());
                $this->_registry->mergeVars('*', \Yana\Util\Hashtable::changeCase(self::$_config->toArray(), \CASE_UPPER));
            }
            $request = \Yana\Core\Request::getVars();
            $this->_registry->mergeVars('*', $request);
            $this->_registry->setAsGlobal();

            // set user name
            if (!empty($_SESSION['user_name'])) {
                $this->_registry->setVar("SESSION_USER_ID", $_SESSION['user_name']);
            }

            // set CD-ROM temp-dir
            if (YANA_CDROM === true) {
                $this->_registry->setVar('YANA_CDROM_DIR', YANA_CDROM_DIR);
            }

            $this->_registry->read();

            // create cache file
            if (YANA_CACHE_ACTIVE === true && !file_exists($cacheFile)) {
                file_put_contents($cacheFile, serialize($this->_registry));
            }

            if (!empty($request['page'])) {
                $this->_registry->setVar('PAGE', (int) $request['page']);
            }
            if (!empty($request['target'])) {
                $this->_registry->setVar('TARGET', (string) $request['target']);
            }
            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $this->_registry->setVar('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
            } else {
                $this->_registry->setVar('REMOTE_ADDR', '0.0.0.0');
            }
            if (!empty($_SERVER['HTTP_REFERER'])) {
                $referer = preg_replace("/(.*\/).*(\?.*)?/", "\\1", $_SERVER['HTTP_REFERER']);
                $this->_registry->setVar("REFERER", $referer);
            }
        }
        return $this->_registry;
    }

    /**
     * Get plugin-manager.
     *
     * This returns the plugin manager. If none exists, a new instance is created.
     * The pluginManager holds repositories for interfaces and implementations of plugins.
     *
     * @return  \Yana\Plugins\Manager
     */
    public function getPlugins()
    {
        if (!isset($this->_plugins)) {
            $cacheFile = (string) self::$_config->plugincache;

            if (YANA_CACHE_ACTIVE === true && file_exists($cacheFile)) {
                $this->_plugins = unserialize(file_get_contents($cacheFile));
                assert('$this->_plugins instanceof PluginManager;');

            } else {
                $this->_plugins = \Yana\Plugins\Manager::getInstance();
                if (!is_file(\Yana\Plugins\Manager::getConfigFilePath())) {
                    $this->_plugins->refreshPluginFile();
                }
                file_put_contents($cacheFile, serialize($this->_plugins));
            }
        }
        return $this->_plugins;
    }

    /**
     * Get view.
     *
     * This returns the view component. If none exists, a new instance is created.
     * This is an auxiliary class that provides access to output-specific functions.
     *
     * @return  \Yana\Views\Manager
     */
    public function getView()
    {
        if (!isset($this->_view)) {
            $factory = new \Yana\Views\EngineFactory(self::$_config->templates);
            $this->_view = $factory->createInstance();
            $this->setVar("ACTION", $this->_getAction());
        }
        return $this->_view;
    }

    /**
     * Get language translation-repository.
     *
     * This returns the language component. If none exists, a new instance is created.
     *
     * @return  Language
     */
    public function getLanguage()
    {
        if (!isset($this->_language)) {
            $languageDir = $this->getVar('LANGUAGEDIR');
            $this->_language = Language::getInstance();
            $this->_language->addDirectory($languageDir);
            $this->_language->setLocale((string) self::$_config->default->language);
            if (isset($_SESSION['language'])) {
                try {
                    $this->_language->setLocale($_SESSION['language']);
                } catch (\Yana\Core\Exceptions\InvalidArgumentException $e){
                    unset($_SESSION['language']);
                }
            }
            $this->_language->readFile('default');
            $array = array();
            foreach (glob("$languageDir*", GLOB_ONLYDIR) as $dir)
            {
                $array[basename($dir)] = 1;
            }
            $this->setVar('INSTALLED_LANGUAGES', $array);
            if (isset($_SESSION['language'])) {
                $this->setVar('SELECTED_LANGUAGE', $_SESSION['language']);
            }
        }
        return $this->_language;
    }

    /**
     * get skin
     *
     * This returns the skin component. If none exists, a new instance is created.
     *
     * @return  \Yana\Views\Skin
     */
    public function getSkin()
    {
        if (!isset($this->_skin)) {
            $registry = $this->getRegistry();
            $registry->mount('system:/skincache.text');
            $cacheFile = $registry->getResource('system:/skincache.text');

            if (YANA_CACHE_ACTIVE === true && $cacheFile->exists()) {
                assert('!isset($skin); // Cannot redeclare var $skin');
                $this->_skin = unserialize(file_get_contents($cacheFile->getPath()));
                assert('$this->_skin instanceof Skin;');

            } else {
                $this->_skin = new \Yana\Views\Skin($this->getVar('PROFILE.SKIN'));
                $this->_skin->selectMainSkin();

                if (YANA_CACHE_ACTIVE === true) {
                    $cacheFile->create();
                    $cacheFile->setContent(serialize($this->_skin));
                    $cacheFile->write();
                }
            }
        }
        return $this->_skin;
    }

    /**
     * get current profile id
     *
     * Returns the id of the profile the data of the current profile is to be associated with.
     *
     * This is a shortcut for $YANA->getVar('ID').
     *
     * However it is important to note a slight difference.
     * <ul>
     *   <li> $YANA->getVar('ID'):
     *     The value you get via  is
     *     available to all plugins and all plugins may read
     *     AND write this setting as the developer sees fit.
     *     This may mean, that this setting has been subject
     *     to changes by some plugin, e.g. to switch between
     *     profiles.
     *   </li>
     *   <li> Yana::getId():
     *     Always returns the original value, regardless of
     *     changes by plugins.
     *   </li>
     * </ul>
     *
     * You may want to decide for the behaviour you prefer
     * and choose either one or the other.
     *
     * @return  string
     */
    public static function getId()
    {
        if (!isset(self::$_id)) {
            $id = \Yana\Core\Request::getVars('id');
            if (!empty($id)) {
                self::$_id = mb_strtolower($id);
            } elseif (!empty(self::$_config->default->profile)) {
                self::$_id = (string) self::$_config->default->profile;
            } elseif (!empty($_REQUEST['id'])) {
                self::$_id = mb_strtolower($_REQUEST['id']);
            } else {
                self::$_id = 'default';
            }
        }
        return self::$_id;
    }

    /**
     * get value from registry
     *
     * Returns var from registry (memory shared by all plugins)
     *
     * Example:
     * <code>
     * $YANA->setVar('foo.bar', 'Hello World');
     * // outputs 'Hello World'
     * print $YANA->getVar('foo.bar');
     * </code>
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     * @name    Yana::getVar()
     * @see     Yana::setVarByReference()
     * @see     Yana::setVar()
     */
    public function getVar($key)
    {
        assert('is_scalar($key); // Invalid argument $key: scalar expected');
        $registry = $this->getRegistry();
        return $registry->getVar("$key");
    }

    /**
     * Returns all vars from the registry (memory shared by all plugins).
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  array
     */
    public function getVars()
    {
        return $this->getRegistry()->getVars();
    }

    /**
     * sets var on registry by Reference
     *
     * The "registry" is memory shared by all plugins.
     *
     * Example:
     * <code>
     * $bar = 'Hello';
     * $YANA->setVarByReference('foo.bar', $bar);
     * $bar .= ' World';
     * // outputs 'Hello World'
     * print $YANA->getVar('foo.bar');
     * </code>
     *
     * @param   string  $key     adress of data in memory (case insensitive)
     * @param   mixed   &$value  new value (may be scalar value or array)
     * @return  \Yana
     * @name    Yana::setVarByReference()
     * @see     Yana::setVar()
     * @see     Yana::getVar()
     */
    public function setVarByReference($key, &$value)
    {
        assert('is_scalar($key); // Invalid argument $key: scalar expected');
        $this->getRegistry()->setVarByReference((string) $key, $value);
        return $this;
    }

    /**
     * Replace all vars in the global registry by reference.
     *
     * @param   array  &$values  new set of values
     * @return  \Yana
     */
    public function setVarsByReference(array &$values)
    {
        $this->getRegistry()->setVarsByReference($values);
        return $this;
    }

    /**
     * sets var on registry
     *
     * The "registry" is memory shared by all plugins.
     *
     * Example:
     * <code>
     * $YANA->setVar('foo.bar', 'Hello World');
     * // outputs 'Hello World'
     * print $YANA->getVar('foo.bar');
     * </code>
     *
     * @param   string  $key    adress of data in memory (case insensitive)
     * @param   mixed   $value  new value (may be scalar value or array)
     * @return  \Yana
     * @name    Yana::setVar()
     * @see     Yana::setVarByReference()
     * @see     Yana::getVar()
     */
    public function setVar($key, $value)
    {
        $this->getRegistry()->setVar($key, $value);
        return $this;
    }

    /**
     * Replace all vars in the global registry.
     *
     * @param   array  $value  set of new values
     * @return  \Yana
     */
    public function setVars(array $value)
    {
        $this->getRegistry()->setVars($value);
        return $this;
    }

    /**
     * get a resource
     *
     * This function takes a virtual file or directory path and returns the resource.
     * The requested resource must be defined within the virtual drive.
     * See the manual for more details on the proper use of virtual drives.
     *
     * If the mountpoint for the requested resource does not exist, or doesn't return any results,
     * the function returns bool(false) instead and issues a warning.
     *
     * @param   string  $path  virtual file path
     * @return  \Yana\Files\AbstractResource
     */
    public function getResource($path)
    {
        assert('is_string($path); // Invalid argument $path: string expected');
        return $this->getRegistry()->getResource($path);
    }

    /**
     * exit the current script
     *
     * This will flush error messages and warnings to the screen,
     * write all reported errors (if any) to the framework's logs
     * and then exit the current script.
     * After that it will call itself again to handle the event
     * provided by the argument $event.
     *
     * You may use the special event 'null' to prevent the
     * framework from handling an event. In this case it will just
     * exit.
     *
     * If the argument $event is not provided, the default event
     * will be used instead.
     *
     * Examples:
     * <code>
     * global $YANA;
     *
     * // print an error and go to start page
     * new Message('Error 404', E_USER_ERROR);
     * $YANA->exitTo();
     *
     * // same as:
     * $YANA->exitTo('');
     *
     * // Use special event 'null' if you just want to
     * // view the error message and exit the script
     * // without handling another event.
     * // ( You may translate this to: "exit to 'nowhere'" )
     * new Message('Error 500', E_USER_ERROR);
     * $YANA->exitTo('null');
     *
     * // output message and route to 'login' page
     * new Message('Access denied', E_USER_ERROR);
     * $YANA->exitTo('login');
     * </code>
     *
     * Please note: any code followed after a call to this function
     * will never be executed.
     *
     * @param  string  $event  upcoming event to route to
     * @since  2.9.0 RC2
     */
    public function exitTo($event = 'null')
    {
        assert('is_string($event); // Invalid argument $event: string expected');
        $event = mb_strtolower("$event");

        /**
         * save log-files (if any)
         *
         * By default this will output any messages to a table of the database named 'log'.
         */
        $level = $this->_prepareMessages();
        $view = $this->getView();

        assert('!isset($template); // Cannot redeclare var $template');
        $templateName = 'id:MESSAGE';

        /**
         * is an AJAX request
         */
        if (\Yana\Core\Request::getVars('is_ajax_request')) {
            $event = 'null';
            $templateName = 'id:STDOUT';
        }

        /**
         * output a message and DO NOT RELOCATE, when
         *   1) headers are already sent, OR
         *   2) the template explicitely requests a message, OR
         *   3) the special 'NULL-event' (no event) is requested.
         */
        if ($event === 'null' || self::getDefault('MESSAGE') === true || headers_sent() === true) {

            $template = $view->createLayoutTemplate($templateName, '', $this->getVars());
            $template->setVar('ACTION', mb_strtolower("$event"));
            $template->setVar('STDOUT.LEVEL', mb_strtolower("$level"));

            exit((string) $template);
        }

        /**
         * save message and relocate.
         */
        $stdout = $this->getVar('STDOUT');
        if (!is_array($stdout)) {
            unset($_SESSION['STDOUT']);
        } else {
            $_SESSION['STDOUT'] = $stdout;
        }

        $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
        header("Location: " . $urlFormatter("action=$event", true));
        exit(0);
    }

    /**
     * Provides GUI from current data.
     */
    public function outputResults()
    {
        /* 0 initialize vars */
        $pluginManager = $this->getPlugins();
        $event = $pluginManager->getFirstEvent();
        $result = $pluginManager->getLastResult();
        $eventConfiguration = $pluginManager->getEventConfiguration($event);
        if (! $eventConfiguration instanceof \Yana\Plugins\Configs\MethodConfiguration) {
            return; // error - unable to continue
        }
        $template = $eventConfiguration->getTemplate();
        unset($eventConfiguration);

        switch (strtolower($template))
        {
            /**
             * 1) the reserved template 'NULL' is an alias for 'no template' and will prevent the use of HTML template files.
             *
             * This may mean the plugin has created some output itself using print(),
             * or it is a triggered cron-job that is not meant to produce any output at all,
             * or it has returned a value, that will be sent as a JSON encoded string.
             */
            case 'null':
                $this->_outputAsJson($result);
                break;
            /**
             * 2) the reserved template 'MESSAGE' is a special template that produces a text message.
             *
             * The text usually is an ID of some text.
             * The actual message is stored in the language files and the translated message will be read from there
             * depending on the user's prefered language setting.
             */
            case 'message':
                $this->_outputAsMessage();
                break;
            /**
             * 3) all other template settings go here
             */
            default:
                if ($result === false && \Yana\Core\Exceptions\AbstractException::countMessages() === 0) {
                    $this->_outputAsMessage();
                    return;
                }
                $this->_outputAsTemplate($template);
                break;
        }

    }

    /**
     * Output results as JSON.
     *
     * If the function returned a result, it will be printed as a JSON string.
     *
     * @param  mixed  $result  whatever the last called action returned
     */
    private function _outputAsJson($result)
    {
        $json = "";
        if (!headers_sent()) {
            header('Content-Type: text/plain');
            header('Content-Encoding: UTF-8');
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }
        if (!is_null($result)) {
            $json = json_encode($result);
        }
        print $json;
    }

    /**
     * Output a text message and relocate to next event.
     */
    private function _outputAsMessage()
    {
        $route = $this->getPlugins()->getNextEvent();
        $target = "";
        $messageClass = "";

        if ($route instanceof \Yana\Plugins\Configs\EventRoute) {
            // create default message if there is none
            if (Message::countMessages() === 0) {
                if ($route->getMessage()) {
                    $messageClass = $route->getMessage();
                } else {
                    if ($route->getCode() === \Yana\Plugins\Configs\EventRoute::CODE_SUCCESS) {
                        $messageClass = \Yana\Plugins\Configs\EventRoute::MSG_SUCCESS;
                    } else {
                        $messageClass = \Yana\Plugins\Configs\EventRoute::MSG_ERROR;
                    }
                }

                if (class_exists($messageClass)) {
                    new $messageClass();
                }
            }

            $target = $route->getTarget();
        }
        if (empty($target)) {
            // if no other destination is defined, route back to default homepage
            $target = self::getDefault("homepage");
            assert('!empty($target); // Configuration error: No default homepage set.');
            assert('is_string($target); // Configuration error: Default homepage invalid.');
        }

        $this->exitTo($target);
    }

    /**
     * Select the given template as output target and print the result page.
     *
     * @param  string  $template  a valid template identifier
     */
    private function _outputAsTemplate($template)
    {
        assert('is_string($template); // Invalid argument $template: string expected');
        $view = $this->getView();

        $baseTemplate = 'id:INDEX';

        $_template = mb_strtoupper(\Yana\Plugins\Annotations\Enumeration::TEMPLATE);
        if (!empty(self::$_config->default->event->$_template)) {
            $baseTemplate = (string) self::$_config->default->event->$_template;
        }
        if (!is_file($template) && !\Yana\Util\String::startsWith($template, 'id:')) {
            $template = "id:$template";
        }
        /* register templates with view sub-system */
        $template = $view->createLayoutTemplate($baseTemplate, $template, $this->getVars());
        /* there is a special var called 'STDOUT' that is used to output messages */
        if (!empty($_SESSION['STDOUT']['MESSAGES']) && is_array($_SESSION['STDOUT']['MESSAGES'])) {
            $this->setVar('STDOUT', $_SESSION['STDOUT']);
            unset($_SESSION['STDOUT']);
        }

        /* print message queue to client */
        $this->_prepareMessages();

        /* print the page to the client */
        print $template->fetch();
    }

    /**
     * get default configuration value
     *
     * Returns the default value for a given var if any,
     * returns NULL (not false!) if there is none.
     *
     * Example 1:
     * <code>
     * Yana::getDefault('CONTAINER1.CONTAINER2.DATA');
     * </code>
     *
     * Example 2:
     * <code>
     * if (!isset($foo)) {
     *     $foo = Yana::getDefault('FOO');
     * }
     * </code>
     *
     * Note: system default values are typically defined in the
     * 'default' section of the 'config/system.config' configurations file.
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     */
    public static function getDefault($key)
    {
        assert('is_scalar($key); // Invalid argument $key: scalar expected');
        $result = null;
        if (isset(self::$_config->default)) {
            $key = mb_strtolower("$key");
            if (isset(self::$_config->default->$key)) {
                $result = self::$_config->default->$key;
            } else {
                $values = self::$_config->default;
                foreach (explode('.', $key) as $i)
                {
                    if (!isset($values->$i)) {
                        return null;
                    }
                    $values = $values->$i;
                }
                unset($i);
                $result = $values;
            }
        }
        if ($result instanceof \Yana\Util\XmlArray) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * Deletes all temporary files in the 'cache/' directory.
     *
     * This includes templates and preinitialized instances of system objects.
     * Use this function where system settings or profile systems are changed,
     * to make sure changes are applied without delay.
     */
    public function clearCache()
    {
        // clear Menu Cache
        \Yana\Plugins\Menu::clearCache();

        // Clear Template cache
        $this->getView()->clearCache();

        // Get name of cache directory
        $dir = 'cache/';
        $registry = $this->getRegistry();

        if (isset($registry)) {
            $dir = $registry->getVar('TEMPDIR');
        }

        // Clear application cache
        foreach (glob($dir . '/*.tmp') as $filePath)
        {
            /* If file can't be deleted due to active write-protection
              (e.g. when running under Windows), check wether this can be fixed. */
            if (!is_writeable($filePath)) {
                chmod($filePath, 0666);
            }
            // If the file writeable now: try again to delete it
            if (is_writeable($filePath)) {
                unlink($filePath);
            }
        } // end foreach
    }

    /**
     * <<factory>> connect()
     *
     * Returns a ready-to-use database connection.
     *
     * Example:
     * <code>
     * // Connect to database using 'config/db/user.config'
     * $db = Yana::connect('user');
     * </code>
     *
     * @param   string|\Yana\Db\Ddl\Database  $schema  name of the database schema file (see config/db/*.xml),
     *                                                 or instance of \Yana\Db\Ddl\Database
     * @return  \Yana\Db\IsConnection
     */
    public static function connect($schema)
    {
        $connection = null;

        $schemaName = "";
        if (is_string($schema)) {
            $schemaName = strtolower($schema);
            if (isset(self::$_connections[$schemaName])) {
                return self::$_connections[$schemaName];
            }
            $tempDir = __DIR__ . '/../../cache/';
            if (isset(self::$_config) && isset(self::$_config->tempdir)) {
                $tempDir = (string) self::$_config->tempdir;
            }
            $cacheFile = $tempDir . 'ddl_' . $schemaName . '.tmp';
            if (YANA_CACHE_ACTIVE === true && is_file($cacheFile)) {
                $schema = unserialize(file_get_contents($cacheFile));
            } else {
                $schema = \Yana\Files\XDDL::getDatabase($schema);
                file_put_contents($cacheFile, serialize($schema));
            }
        }
        if (YANA_DATABASE_ACTIVE) {
            $connection = new \Yana\Db\Mdb2\Connection($schema);
        } else {
            $connection = new \Yana\Db\FileDb\Connection($schema);
        }
        if (!empty($schemaName)) {
            self::$_connections[$schemaName] = $connection;
        }
        return $connection;
    }

    /**
     * run diagnostics and get a system report
     *
     * This function runs full diagnosicts on all mounted sub-systems.
     *
     * Returns the a report object.
     *
     * Example:
     * <code>
     * <?xml version="1.0"?>
     * <report>
     *   <text>Base directory: foo/</text>
     *   <report>
     *     <title>bar.file</title>
     *     <text>Type: file</text>
     *     <text>Path: bar.txt</text>
     *     <error>Is not readable ...</error>
     *   </report>
     *   <report>
     *     <title>foo</title>
     *     <text>Type: dir</text>
     *     <text>Path: bar/foo/</text>
     *   </report>
     * </report>
     * </code>
     *
     * @param   \Yana\Report\IsReport  $report  base report
     * @return  \Yana\Report\IsReport
     * @name    Yana::getReport()
     * @ignore
     */
    public function getReport(\Yana\Report\IsReport $report = null)
    {
        if (is_null($report)) {
            $report = \Yana\Report\Xml::createReport(__CLASS__);
        }

        /**
         * 1) General system information
         */
        $report->addNotice("installed version of Yana Framework is: " . YANA_VERSION);
        $report->addNotice("installed version of PHP is: " . PHP_VERSION);
        $report->addNotice("current server time is: " . date("r", time()));
        $report->addNotice("running diagnostics on profile: " . self::getId());

        $subreport = $report->addReport("Testing installation");

        /**
         * 2) Check for availability of PEAR-DB
         */
        @include_once "MDB2.php";
        if (!class_exists("MDB2")) {
            $message = "PHP PEAR-MDB2 module not found. " .
                "Database plugins require PEAR-MDB2 and will not run unless you install it.";
            $subreport->addError($message);
        } else {
            $subreport->addText("PHP PEAR-MDB2 found (required to run database plugins)");
        }

        /**
         * 3) Check if primary controller is registered under the expected name
         */
        if (!isset($GLOBALS['YANA'])) {
            $message = "Unable to access Yana instance under global name 'YANA'. " .
                "The framework will not run properly. Please reinstall the application.";
            $subreport->addError($message);
        }

        /**
         * 4) Check availability of configuration file and configuration directory
         */
        if (YANA_CDROM === true) {
            if (!is_writeable(YANA_CDROM_DIR)) {
                $message = "Temporary directory " . YANA_CDROM_DIR . " is not writeable. " .
                    "Set access rights for directory '" .
                    YANA_CDROM_DIR . "' to 777, including all subdirectories and files.";
                $subreport->addError($message);
            }
        } else {
            if (!is_writeable($this->getVar('CONFIGDIR'))) {
                $message = "Configuration directory is not writeable. " .
                    "Set access rights for directory '" . $this->getVar('CONFIGDIR') .
                    "' to 777, including all subdirectories and files.";
                $subreport->addError($message);
            }
            if (!is_writeable($this->getVar('TEMPDIR'))) {
                $message = "Directory for temporary files is not writeable. " .
                    "Set access rights for directory '" . $this->getVar('TEMPDIR') .
                    "' to 777, including all subdirectories and files.";
                $subreport->addError($message);
            }
        }
        unset($subreport);

        /**
         * 4) Add a list of MD5 checksums for several important files
         */
        $systemIntegrityReport = $report->addReport("System-integrity check");
        $message = "The following list contains the MD5 checksums of several important files. " .
            "Compare these with your own list to see, " .
            "if any of these files have recently been modified without your knowledge.";
        $systemIntegrityReport->addText($message);

        if (is_dir('manual')) {
            $message = "You do not need to copy the directory 'manual' to your website. " .
                "It is not required to run the program. You might want to remove it to safe space.";
            $systemIntegrityReport->addNotice($message);
        }

        foreach (glob('./*.php') as $root)
        {
            $root = basename($root);
            if (!in_array($root, array('index.php', 'library.php', 'cli.php'))) {
                $message = "Unexpected file '" . $root . "' found. " .
                    "If you did'nt place this file here, " .
                    "it might be the result of an hijacking attempt. " .
                    "You should consider removing this file.";
                $systemIntegrityReport->addWarning($message);
            } else {
                $systemIntegrityReport->addText("{$root} = " . md5_file($root));
            }
        } // end foreach
        foreach (glob(dirname(__FILE__) . '/*.php') as $root)
        {
            $systemIntegrityReport->addText("{$root} = " . md5_file($root));
        }

        /**
         *  5) Add subreports
         */
        foreach ($this as $name => $member)
        {
            if (is_object($member) && $member instanceof \Yana\Report\IsReportable) {
                $memberReport = $report->addReport("$name");
                $member->getReport($memberReport);
            }
        }
        unset($memberReport);

        $iconIntegrityReport = $report->addReport('Searching for icon images');
        $registry = $this->getRegistry();
        /* @var $dir \Dir */
        assert('!isset($dir); // Cannot redeclare var $dir');
        $dir = $registry->getResource('system:/smile');
        $smilies = $dir->dirlist();
        if (count($smilies)==0) {
            $message = "No Icons found. Please check if the given directory is correct: '" .
                $dir->getPath() . "'.";
            $iconIntegrityReport->addWarning($message);
        } else {
            $iconIntegrityReport->addText(count($smilies) . " Icons found in directory '" . $dir->getPath() . "'.");
            $iconIntegrityReport->addText("No problems found: Directory setting seems to be correct.");
        }
        unset($dir);

        return $report;
    }

    /**
     * iterate through message queue
     *
     * @return  string
     */
    private function _prepareMessages()
    {
        $messageClass = "";
        $isFinal = false;
        $stdout = array();

        assert('!isset($messages); // Cannot redeclare variable $messages');
        $messages = array();
        if (defined('YANA_ERROR_REPORTING') && YANA_ERROR_REPORTING === YANA_ERROR_ON) {
            $messages = \Yana\Core\Exceptions\AbstractException::getMessages();
        } else {
            $messages = Message::getMessages();
        }
        assert('is_array($messages); // unexpected result: List of messages is not an array');

        if (empty($messages)) {
            return "";
        }

        // event logging
        assert('!isset($message); // Cannot redeclare variable $message');
        foreach ($messages as $message)
        {
            if (!$isFinal) {
                switch ($message->getCode())
                {
                    case \E_USER_ERROR:
                    case \E_ERROR:
                        $messageClass = \Yana\Core\Exceptions\ResultTypeEnumeration::ERROR;
                        $isFinal = true;
                        break;
                    case \E_USER_WARNING:
                    case \E_WARNING:
                        $messageClass = \Yana\Core\Exceptions\ResultTypeEnumeration::WARNING;
                        break;
                    case \E_USER_NONE:
                        $messageClass = \Yana\Core\Exceptions\ResultTypeEnumeration::MESSAGE;
                        $isFinal = true;
                        break;
                    case \E_NOTICE:
                    case \E_USER_DEPRECATED:
                    case \E_USER_NOTICE:
                    default:
                        $messageClass = \Yana\Core\Exceptions\ResultTypeEnumeration::ALERT;
                }
            }
            if ($message->getHeader() || $message->getText()) {
                $stdout[] = array(
                    'header' => $message->getHeader(),
                    'text' => $message->getText()
                );
            }
        } // end foreach (message)
        unset($message);
        if (!empty($stdout)) {
            $this->setVar('STDOUT.MESSAGES', $stdout);
            $this->setVar('STDOUT.LEVEL', $messageClass);
        }
        return $messageClass;
    }

    /**
     * Adds a logger to the class.
     *
     * @param  \Yana\Log\IsLogger  $logger  instance that will handle the logging
     */
    public function attachLogger(\Yana\Log\IsLogger $logger)
    {
        $this->_loggers[] = $logger;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger()
    {
        return $this->_loggers;
    }

}

?>