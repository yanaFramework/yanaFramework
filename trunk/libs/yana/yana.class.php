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
 * @access      public
 * @package     yana
 * @subpackage  core
 */
final class Yana extends Singleton implements IsReportable
{
    /**
     * This is a place-holder for the singleton's instance
     *
     * @access  private
     * @static
     * @var     Yana
     */
    private static $_instance = null;

    /**
     * Name of system configuration file
     *
     * @access  private
     * @static
     * @var     array
     */
    private static $_config = array();

    /**
     * profile id
     *
     * @access  private
     * @static
     * @var string
     */
    private static $_id = null;

    /**
     * action parameter
     *
     * @access  private
     * @static
     * @var string
     */
    private static $_action = null;

    /**
     * safe-mode settings
     *
     * false = default-mode (use profile settings)
       true  = safe-mode    (use default profile)
     *
     * @access  protected
     * @var     bool
     * @ignore
     */
    protected $_isSafeMode = null;

    /**#@+
     * This variable is <<readonly>>
     * @access  public
     */

    /**
     * to communicate with plugins
     * @var PluginManager
     */
    public $plugins = null;

    /**
     * to load language strings
     * @var Language
     */
    public $language = null;

    /**
     * to load skins and templates
     * @var Skin
     */
    public $skin = null;

    /**
     * to read and write data to the global registry
     * @var Registry
     */
    public $registry = null;

    /**
     * to read and write user data and permissions
     * @var SessionManager
     */
    public $session = null;

    /**
     * the currently selected template
     * @var SmartView
     */
    public $view = null;

    /**#@-*/

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
     * @access  public
     * @static
     * @return  Yana
     */
    public static function &getInstance()
    {
        if (!isset(self::$_instance)) {
            /* auto-load configuration file */
            if (empty(self::$_config)) {
                self::setConfiguration();
            }
            self::$_instance = new Yana();
        }
        return self::$_instance;
    }

    /**
     * <<Singleton>> Constructor
     *
     * This function creates a new instance of the framework.
     * Note that you may only operate one instance at a time.
     *
     * @uses    new Yana()
     * @access  private
     * @throws  NotReadableException    when Registry file is not readable
     * @throws  InvalidSyntaxException  when Registry file could not be read or contains invalid syntax
     */
    private function __construct()
    {
        // The pluginManager holds repositories for interfaces and implementations of plugins.
        $this->_getPlugins();

        // Build configuration. These settings may be read later by using Yana::getVar().
        $this->_getRegistry(); // may throw NotReadableException, InvalidSyntaxException

        // Prepare template. This is an auxiliary class that provides access to output-specific functions.
        $this->_getView();

        // Create  translation-repository. This setting can be accessed via Yana::$language.
        $this->_getLanguage();

        // Create skin-repository. This setting can be accessed via Yana::$skin.
        $this->_getSkin();
    }

    /**
     * application is in safe-mode
     *
     * @access  protected
     * @return  bool
     * @ignore
     */
    protected function isSafemode()
    {
        if (!isset($this->isSafemode)) {
            $eventConfiguration = $this->_getPlugins()->getEventConfiguration($this->getAction());
            if ($eventConfiguration instanceof PluginConfigurationMethod) {
                $this->_isSafeMode = ($eventConfiguration->getSafemode() === true);
            } else {
                $this->_isSafeMode = !empty(self::$_config['DEFAULT']['EVENT'][PluginAnnotationEnumeration::SAFEMODE]);
            }
        }
        return $this->_isSafeMode;
    }

    /**
     * activate CD-ROM settings
     *
     * Sets the configuration to CD-ROM settings.
     * Configuration is expected to be loaded prior to calling this function.
     *
     * @access  private
     * @static
     */
    private static function _activateCDApplication()
    {
        assert('isset(self::$_config); // Configuration must be loaded first');
        if (!file_exists(YANA_CDROM_DIR)) {
            mkdir(YANA_CDROM_DIR);
            chmod(YANA_CDROM_DIR, 0777);
        }
        $configDir = self::$_config['CONFIGDIR'];
        self::_setRealPaths(YANA_CDROM_DIR);
        if (!file_exists(self::$_config['TEMPDIR'])) {
            mkdir(self::$_config['TEMPDIR']);
            chmod(self::$_config['TEMPDIR'], 0777);
        }
        if (!file_exists(self::$_config['CONFIGDIR'])) {
            $configSrc = new Dir($configDir);
            $configSrc->copy(self::$_config['CONFIGDIR'], true, 0777, true, null, '/^(?!\.blob$)/i', true);
            unset($configSrc);
        }
        unset($configDir);
    }

    /**
     * set directory references to real paths
     *
     * @access  private
     * @static
     * @param   string  $cwd  current working directory
     */
    private static function _setRealPaths($cwd)
    {
        $cwd .= '/';
        self::$_config['TEMPDIR'] = $cwd . self::$_config['TEMPDIR'];
        self::$_config['CONFIGDIR'] = $cwd . self::$_config['CONFIGDIR'];
        self::$_config['CONFIGDRIVE'] = $cwd . self::$_config['CONFIGDRIVE'];
        self::$_config['PLUGINFILE'] = $cwd . self::$_config['PLUGINFILE'];
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
     * @param   string  $filename  path to system.config
     * @access  public
     * @static
     */
    public static function setConfiguration($filename = null)
    {
        if ($filename === null) {
            $filename = dirname(__FILE__) . "/../../config/system.config.xml";
        }
        assert('is_string($filename);   // Wrong type for argument 1. String expected');
        assert('is_file($filename);     // Invalid argument 1. Input is not a file.');
        assert('is_readable($filename); // Invalid argument 1. Configuration file is not readable.');
        // get System Config file
        self::$_config = SXML::getFile($filename, CASE_UPPER);
        // load CD-ROM application settings on demand
        if (YANA_CDROM === true) {
            self::_activateCDApplication();
        } else {
            self::_setRealPaths(getcwd());
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
     * @access  public
     * @param   string  $action  script action parameter
     * @param   array   $args    array of passed arguments
     * @return  bool
     */
    public function callAction($action = "", array $args = null)
    {
        assert('is_string($action); // Wrong argument type argument 1. String expected');

        /**
         * 1) check for default arguments
         */
        if (empty($action)) {
            $action = $this->getAction();
        }
        if (is_null($args)) {
            $args = Request::getVars();
        }

        /**
         * 2) create session manager instance
         *
         * The SessionManager class is used to manage user information
         * and resolve permissions.
         */
        if (!isset($this->session)) {
            $this->session = SessionManager::getInstance();
        }

        /**
         * 3) load language strings
         */
        assert('!isset($eventConfiguration); // Cannot redeclare var $eventConfiguration');
        assert('!isset($plugins); // Cannot redeclare var $plugins');
        $plugins = $this->_getPlugins();
        $eventConfiguration = $plugins->getEventConfiguration($action);
        if (!($eventConfiguration instanceof PluginConfigurationMethod)) {
            $error = new InvalidActionError();
            $error->setData(array('ACTION' => $action));
            return false;
        }

        assert('!isset($paths); // Cannot redeclare var $paths');
        $paths = $eventConfiguration->getPaths();
        if ($paths) {
            assert('!isset($language); // Cannot redeclare var $language');
            $language = $this->_getLanguage();
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
            $language = $this->_getLanguage();
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
            SmartView::addStyles($styles);
        }
        unset($styles);
        assert('!isset($scripts); // Cannot redeclare var $scripts');
        $scripts = $eventConfiguration->getScripts();
        if ($scripts) {
            SmartView::addScripts($scripts);
        }
        unset($scripts);

        assert('!isset($template); // Cannot redeclare var $template');
        $template = $eventConfiguration->getTemplate();
        if (is_file($template)) {
            $this->skin->setFile($action, $template);
            $this->view->setTemplate($action);
        }
        unset($template, $eventConfiguration);

        /**
         * 4) handle event
         *
         * Returns bool(true) on success and bool(false) otherwise.
         */
        try {

            $result = $plugins->broadcastEvent($action, $args);

        } catch (Exception $e) {
            $message = get_class($e) . ': ' . $e->getMessage() . ' Thrown in ' . $e->getFile() .
                ' on line ' . $e->getLine();
            trigger_error($message, $e->getCode());
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
     * get current action
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
     * @access  protected
     * @return  string
     * @ignore
     */
    protected function getAction()
    {
        if (!isset(self::$_action)) {
            $action = Request::getVars('action');
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
                case isset($action) && !$this->_getPlugins()->isEvent($action):
                    $error = new InvalidActionError();
                    $error->setData(array('ACTION' => $action));
                // fall through
                case empty($action):
                    assert('!empty(self::$_config["DEFAULT"]["HOMEPAGE"]); // Configuration missing default homepage.');
                    $action = self::$_config['DEFAULT']['HOMEPAGE'];
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
     * get registry
     *
     * This returns the registry. If none exists, a new instance is created.
     *
     * @access  private
     * @return  Registry
     * @throws  NotReadableException    when Registry file is not readable
     * @throws  InvalidSyntaxException  when Registry file could not be read or contains invalid syntax
     */
    private function _getRegistry()
    {
        if (!isset($this->registry)) {
            // path to cache file
            $cacheFile = self::$_config['TEMPDIR'] . 'registry_' . self::getId() . '.tmp';

            // get configuration mode
            Registry::useDefaults($this->isSafemode());

            if (YANA_CACHE_ACTIVE === true && file_exists($cacheFile)) {
                $this->registry = unserialize(file_get_contents($cacheFile));
                assert('$this->registry instanceof Registry;');
            } else {
                $this->registry = new Registry(self::$_config['CONFIGDRIVE'], "");
                $this->registry->setVar("ID", self::getId());
                $this->registry->mergeVars('*', self::$_config);
            }
            $request = Request::getVars();
            $this->registry->mergeVars('*', $request);
            $this->registry->setAsGlobal();

            // set user name
            if (!empty($_SESSION['user_name'])) {
                $this->registry->setVar("SESSION_USER_ID", $_SESSION['user_name']);
            }

            // set CD-ROM temp-dir
            if (YANA_CDROM === true) {
                $this->registry->setVar('YANA_CDROM_DIR', YANA_CDROM_DIR);
            }

            $this->registry->read();

            // create cache file
            if (YANA_CACHE_ACTIVE === true && !file_exists($cacheFile)) {
                file_put_contents($cacheFile, serialize($this->registry));
            }

            if (!empty($request['page'])) {
                $this->registry->setVar('PAGE', (int) $request['page']);
            }
            if (!empty($request['target'])) {
                $this->registry->setVar('TARGET', (string) $request['target']);
            }
            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $this->registry->setVar('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
            } else {
                $this->registry->setVar('REMOTE_ADDR', '0.0.0.0');
            }
            if (!empty($_SERVER['HTTP_REFERER'])) {
                $referer = preg_replace("/(.*\/).*(\?.*)?/", "\\1", $_SERVER['HTTP_REFERER']);
                $this->registry->setVar("REFERER", $referer);
            }
        }
        return $this->registry;
    }

    /**
     * get plugin-manager
     *
     * This returns the plugin manager. If none exists, a new instance is created.
     *
     * @access  private
     * @return  PluginManager
     */
    private function _getPlugins()
    {
        if (!isset($this->plugins)) {
            $cacheFile = self::$_config['PLUGINCACHE'];
            PluginManager::setPath(self::$_config['PLUGINFILE'], self::$_config['PLUGINDIR']);

            if (YANA_CACHE_ACTIVE === true && file_exists($cacheFile)) {
                $this->plugins = unserialize(file_get_contents($cacheFile));
                assert('$this->plugins instanceof PluginManager;');

            } else {
                $this->plugins = PluginManager::getInstance();
                file_put_contents($cacheFile, serialize($this->plugins));
            }
        }
        return $this->plugins;
    }

    /**
     * get view
     *
     * This returns the view component. If none exists, a new instance is created.
     *
     * @access  private
     * @return  SmartView
     */
    private function _getView()
    {
        if (!isset($this->view)) {
            $this->view = new SmartView();
            $this->setVar("ACTION", $this->getAction());
        }
        return $this->view;
    }

    /**
     * get language
     *
     * This returns the language component. If none exists, a new instance is created.
     *
     * @access  private
     * @return  Language
     */
    private function _getLanguage()
    {
        if (!isset($this->language)) {
            $languageDir = $this->getVar('LANGUAGEDIR');
            $this->language = Language::getInstance();
            $this->language->addDirectory($languageDir);
            $this->language->setLocale(self::$_config['DEFAULT']['LANGUAGE']);
            if (isset($_SESSION['language'])) {
                try {
                    $this->language->setLocale($_SESSION['language']);
                } catch (InvalidArgumentException $e){
                    unset($_SESSION['language']);
                }
            }
            $this->language->readFile('default');
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
        return $this->language;
    }

    /**
     * get skin
     *
     * This returns the skin component. If none exists, a new instance is created.
     *
     * @access  private
     * @return  Skin
     */
    private function _getSkin()
    {
        if (!isset($this->skin)) {
            $registry = $this->_getRegistry();
            $registry->mount('system:/skincache.textfile');
            $cacheFile = $registry->getResource('system:/skincache.textfile');

            Skin::setBaseDirectory($registry->getResource('system:/skin'));

            if (YANA_CACHE_ACTIVE === true && $cacheFile->exists()) {
                assert('!isset($skin); // Cannot redeclare var $skin');
                $this->skin = unserialize(file_get_contents($cacheFile->getPath()));
                assert('$this->skin instanceof Skin;');

            } else {
                $this->skin = Skin::getInstance($this->getVar('PROFILE.SKIN'));
                $this->skin->selectMainSkin();

                if (YANA_CACHE_ACTIVE === true) {
                    $cacheFile->create();
                    $cacheFile->setContent(serialize($this->skin));
                    $cacheFile->write();
                }
            }
        }
        return $this->skin;
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
     * @access  public
     * @static
     * @return  string
     */
    public static function getId()
    {
        if (!isset(self::$_id)) {
            $id = Request::getVars('id');
            if (!empty($id)) {
                self::$_id = mb_strtolower($id);
            } elseif (!empty(self::$_config['DEFAULT']['PROFILE'])) {
                self::$_id = self::$_config['DEFAULT']['PROFILE'];
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
     * @uses    $YANA->getVar('CONTAINER1.CONTAINER2.DATA')
     * @access  public
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     * @name    Yana::getVar()
     * @see     Yana::setVarByReference()
     * @see     Yana::setVar()
     */
    public function getVar($key = '*')
    {
        assert('is_scalar($key); /* Wrong argument type for argument 1. String expected. */');
        $registry = $this->_getRegistry();
        return $registry->getVar("$key");
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
     * @uses    $YANA->setVar('CONTAINER1.CONTAINER2.DATA', $value)
     * @access  public
     * @param   string  $key     adress of data in memory (case insensitive)
     * @param   mixed   &$value  new value (may be scalar value or array)
     * @return  bool
     * @name    Yana::setVarByReference()
     * @see     Yana::setVar()
     * @see     Yana::getVar()
     */
    public function setVarByReference($key, &$value)
    {
        assert('is_scalar($key); /* Wrong argument type for argument 1. String expected. */');
        /* settype to STRING */
        $key = (string) $key;
        $registry = $this->_getRegistry();
        return $registry->setVarByReference($key, $value);
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
     * @uses    $YANA->setVar('CONTAINER1.CONTAINER2.DATA', $value)
     * @access  public
     * @param   string  $key    adress of data in memory (case insensitive)
     * @param   mixed   $value  new value (may be scalar value or array)
     * @return  bool
     * @name    Yana::setVar()
     * @see     Yana::setVarByReference()
     * @see     Yana::getVar()
     */
    public function setVar($key, $value)
    {
        return $this->setVarByReference($key, $value);
    }

    /**
     * sets the type of a var on registry (memory shared by all plugins)
     *
     * @uses    $YANA->setType('CONTAINER1.CONTAINER2.DATA', 'string')
     * @access  public
     * @param   string  $key   adress of data in memory (case insensitive)
     * @param   string  $type  new type of variable
     * @return  bool
     */
    public function setType($key, $type)
    {
        assert('is_scalar($key);  /* Wrong argument type for argument 1. String expected. */');
        assert('is_string($type); /* Wrong argument type for argument 2. String expected. */');
        /* settype to STRING */
        $key  = (string) $key;
        $type = (string) $type;
        $registry = $this->_getRegistry();
        return $registry->setType($key, $type);
    }

    /**
     * remove var from registry
     *
     * Removes var from registry (memory shared by all plugins).
     * Returns bool(true) on success and bool(false) on error.
     *
     * Example:
     * <code>
     * // set foo.bar
     * $YANA->setVar('foo.bar', 'Hello World');
     * // remove foo.bar
     * $YANA->unsetVar('foo.bar');
     * // foo.bar now is false
     * var_dump($YANA->getVar('foo.bar'));
     * </code>
     *
     * @uses    $YANA->unsetVar('CONTAINER1.CONTAINER2.DATA')
     * @access  public
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  bool
     */
    public function unsetVar($key)
    {
        assert('is_scalar($key); /* Wrong argument type for argument 1. String expected. */');
        /* settype to STRING */
        $key = (string) $key;
        $registry = $this->_getRegistry();
        return $registry->unsetVar($key);
    }

    /**
     * merges value in registry
     *
     * Merges the value at adresse $key with the provided array data.
     * If a key already exists, it is replaced by the new data.
     *
     * Example:
     * <code>
     * $bar = array('foo' => 'bar');
     * $YANA->mergeVars('FOO', $bar);
     * // outputs 'bar'
     * print $YANA->getVar('FOO.FOO');
     * </code>
     *
     * @uses    $YANA->mergeVars('CONTAINER1.CONTAINER2.DATA', $array)
     * @access  public
     * @param   string  $key    adress of data in memory (case insensitive)
     * @param   array   $array  associative array to merge
     * @return  bool
     */
    public function mergeVars($key, array $array)
    {
        assert('is_scalar($key);  /* Wrong argument type for argument 1. String expected. */');
        $registry = $this->_getRegistry();
        return $registry->mergeVars("$key", $array);
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
     * @access  public
     * @param   string  $path  virtual file path
     * @return  FileSystemResource
     */
    public function getResource($path)
    {
        return $this->_getRegistry()->getResource($path);
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
     * Message::report('Error 404', E_USER_ERROR);
     * $YANA->exitTo();
     *
     * // same as:
     * $YANA->exitTo('');
     *
     * // Use special event 'null' if you just want to
     * // view the error message and exit the script
     * // without handling another event.
     * // ( You may translate this to: "exit to 'nowhere'" )
     * Message::report('Error 500', E_USER_ERROR);
     * $YANA->exitTo('null');
     *
     * // output message and route to 'login' page
     * Message::report('Access denied', E_USER_ERROR);
     * $YANA->exitTo('login');
     * </code>
     *
     * Please note: any code followed after a call to this function
     * will never be executed.
     *
     * @access  public
     * @param   string  $event  upcoming event to route to
     * @since   2.9.0 RC2
     */
    public function exitTo($event = 'null')
    {
        assert('is_string($event); // Wrong argument type argument 1. String expected');
        $event = mb_strtolower("$event");

        /*
         * save log-files (if any)
         *
         * By default this will output any messages to a table of the database named 'log'.
         */
        $this->_writeLog();
        $level = $this->_prepareMessages();
        $view = $this->_getView();

        /*
         * is an AJAX request
         */
        if (Request::getVars('is_ajax_request')) {
            $view->showMessage($level, 'null', 'STDOUT');
            exit(0);
        }

        /*
         * output a message and DO NOT RELOCATE, when
         *   1) headers are already sent, OR
         *   2) the template explicitely requests a message, OR
         *   3) the special 'NULL-event' (no event) is requested.
         */
        if ($event === 'null' || self::getDefault('MESSAGE') === true || headers_sent() === true) {

            $view->showMessage($level, $event);
            exit(0);
        }

        /*
         * save message and relocate.
         */
        $stdout = $this->getVar('STDOUT');
        if (!is_array($stdout)) {
            unset($_SESSION['STDOUT']);
        } else {
            $_SESSION['STDOUT'] = $stdout;
        }

        header("Location: " . SmartUtility::url("action=$event", true));
        exit(0);
    }

    /**
     * provides GUI from current data
     *
     * @access  public
     */
    public function outputResults()
    {
        /* 0 initialize vars */
        $pluginManager = $this->_getPlugins();
        $event = $pluginManager->getFirstEvent();
        $result = $pluginManager->getLastResult();
        $eventConfiguration = $pluginManager->getEventConfiguration($event);
        if ($eventConfiguration instanceof PluginConfigurationMethod) {
            $template = $eventConfiguration->getTemplate();

        } else {
            // error - unable to continue
            return;
        }
        unset($eventConfiguration);

        /**
         * resolve selected templates
         */

        /* 1) the reserved template 'NULL' is an alias
         * for 'no template' and will surpress any output.
         *
         * This may mean the plugin will create some output
         * itself using print(), or it is a triggered
         * cron-job that is not meant to produce any output
         * at all.
         */
        if (strcasecmp($template, 'NULL') === 0) {

            if (Request::getVars('is_ajax_request') && !is_bool($result)) {
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
                    print json_encode($result); // print results
                }
            }
            // don't produce any output;
            return;
        }

        /* 2) the reserved template 'MESSAGE' is a special
         * template that produces a text message.
         *
         * The text usually is an ID of some text.
         * The actual message is stored in the language files
         * and the translated message will be read from there
         * depending on the user's prefered language setting.
         */
        if (strcasecmp($template, 'MESSAGE') === 0 || ($result === false && ReportAbstract::countMessages() === 0)) {

            // get vars
            assert('!isset($pluginManager); // Cannot redeclare var $pluginManager');
            $pluginManager = $this->_getPlugins();
            $route = $pluginManager->getNextEvent();
            if (!is_array($route)) {
                $route = array();
            }

            // create default message if there is none
            if (Message::countMessages() === 0) {
                if (!empty($route[PluginAnnotationEnumeration::TEXT])) {
                    $messageClass = $route[PluginAnnotationEnumeration::TEXT];
                } else {
                    if ($result !== false) {
                        $messageClass = 'SuccessMessage';
                    } else {
                        $messageClass = 'Error';
                    }
                }

                if (class_exists($messageClass)) {
                    new $messageClass();
                }
            }

            // if not other destination is defined, route back to default homepage
            if (!isset($route[PluginAnnotationEnumeration::GO])) {
                $route[PluginAnnotationEnumeration::GO] = self::getDefault("homepage");
            }

            $this->exitTo($route[PluginAnnotationEnumeration::GO]);

        /* 3) all other template settings go here
         */
        } else {

            $view = $this->_getView();
            if (!empty(self::$_config['DEFAULT']['EVENT'][mb_strtoupper(PluginAnnotationEnumeration::TEMPLATE)])) {
                $baseTemplate = self::$_config['DEFAULT']['EVENT'][mb_strtoupper(PluginAnnotationEnumeration::TEMPLATE)];
                $view->setPath($baseTemplate);
            }
            /* register templates with view sub-system */
            $view->setTemplate($template);
            /* there is a special var called 'STDOUT' that is used to output messages */
            if (!empty($_SESSION['STDOUT']['MESSAGES']) && is_array($_SESSION['STDOUT']['MESSAGES'])) {
                $this->setVar('STDOUT', $_SESSION['STDOUT']);
                unset($_SESSION['STDOUT']);
            }

            /* print message queue to client */
            $this->_prepareMessages();

            /* print the page to the client */
            echo $view->toString();

            /**
             * save log-files (if any)
             *
             * By default this will output any messages
             * to a table of the database named 'log'.
             */
            $this->_writeLog();
        }

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
     * @access  public
     * @static
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     */
    public static function getDefault($key)
    {
        assert('is_scalar($key); /* Wrong argument type for argument 1. String expected. */');
        if (!isset(self::$_config['DEFAULT'])) {
            return null;
        }
        $key = mb_strtoupper("$key");
        if (isset(self::$_config['DEFAULT'][$key])) {
            return self::$_config['DEFAULT'][$key];
        } else {
            return Hashtable::get(self::$_config['DEFAULT'], $key);
        }
    }

    /**
     * clear system cache
     *
     * Deletes all temporary files in the 'cache/' directory.
     *
     * This includes templates and preinitialized instances of system objects.
     * Use this function where system settings or profile systems are changed,
     * to make sure changes are applied without delay.
     *
     * @access  public
     * @static
     */
    public static function clearCache()
    {
        SmartTemplate::clearCache();
        PluginMenu::clearCache();
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
     * @access  public
     * @static
     * @param   string|DDLDatabase  $source  name of the database schema file (see config/db/*.xml),
     *                                       or instance of DDLDatabase
     * @return  DbStream
     */
    public static function connect($source)
    {
        if ($source instanceof DDLDatabase) {
            $schema = $source;
        } else {
            assert('is_string($source); // Wrong argument type for argument 1. String expected.');
            $schema = XDDL::getDatabase($source);
        }
        if (YANA_DATABASE_ACTIVE) {
            $db = new DbStream($schema);
        } else {
            $db = new FileDb($schema);
        }
        return $db;
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
     * @access  public
     * @param   ReportXML  $report  base report
     * @return  ReportXML
     * @name    Yana::getReport()
     * @ignore
     */
    public function getReport(ReportXML $report = null)
    {
        if (is_null($report)) {
            $report = ReportXML::createReport(__CLASS__);
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

        /**
         * 4) Add a list of MD5 checksums for several important files
         */
        $subreport = $report->addReport("System-integrity check");
        $message = "The following list contains the MD5 checksums of several important files. " .
            "Compare these with your own list to see, " .
            "if any of these files have recently been modified without your knowledge.";
        $subreport->addText($message);

        if (is_dir('manual')) {
            $message = "You do not need to copy the directory 'manual' to your website. " .
                "It is not required to run the program. You might want to remove it to safe space.";
            $subreport->addNotice($message);
        }

        foreach (glob('./*.php') as $root)
        {
            $root = basename($root);
            if (!in_array($root, array('index.php', 'library.php', 'cli.php'))) {
                $message = "Unexpected file '" . $root . "' found. " .
                    "If you did'nt place this file here, " .
                    "it might be the result of an hijacking attempt. " .
                    "You should consider removing this file.";
                $subreport->addWarning($message);
            } else {
                $subreport->addText("{$root} = " . md5_file($root));
            }
        } /* end foreach */
        foreach (glob(dirname(__FILE__) . '/*.php') as $root)
        {
            $subreport->addText("{$root} = " . md5_file($root));
        }

        /**
         *  5) Add subreports
         */
        foreach ($this as $name => $member)
        {
            if (is_object($member) && $member instanceof IsReportable) {
                $subReport = $report->addReport("$name");
                $member->getReport($subReport);
            }
        }

        /**
         * 6) check icons
         */
        $subreport = $report->addReport("Check icons");
        SmartUtility::loadSmilies();
        $smilies = $this->getVar('SMILIES');
        assert('!isset($dir); // Cannot redeclare var $dir');
        $registry = $this->_getRegistry();
        $dir = $registry->getResource('system:/smile');
        if (count($smilies)==0) {
            $message = "No Icons found. Please check if the given directory is correct: '" .
                $dir->getPath() . "'.";
            $subreport->addWarning($message);
        } else {
            $subreport->addText(count($smilies) . " Icons found in directory '" . $dir->getPath() . "'.");
            $subreport->addText("No problems found: Directory setting seems to be correct.");
        }
        unset($dir);

        return $report;
    }

    /**
     * iterate through message queue
     *
     * @access  private
     * @return  string
     * @ignore
     */
    private function _prepareMessages()
    {
        $messageClass = "";
        $isFinal = false;
        $stdout = array();

        assert('!isset($messages); // Cannot redeclare variable $messages');
        if (defined('YANA_ERROR_REPORTING') && YANA_ERROR_REPORTING === YANA_ERROR_ON) {
            $messages = ReportAbstract::getMessages();
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
                if ($message instanceof Error) {
                    $messageClass = "error";
                    $isFinal = true;
                } elseif ($message instanceof SuccessMessage) {
                    $messageClass = "message";
                    $isFinal = true;
                } elseif ($message instanceof Warning) {
                    $messageClass = "warning";
                } elseif (empty($messageClass)) {
                    $messageClass = "alert";
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
     * flush events to log
     *
     * @access  private
     * @ignore
     */
    private function _writeLog()
    {
        // skip if logging is turned off
        if (!$this->getVar('PROFILE.LOGGING')) {
            return;
        }

        // get message-queue
        $messages = Log::getMessages();
        assert('is_array($messages); // unexpected result: List of messages is not an array');

        // skip if message-queue is empty
        if (empty($messages)) {
            return;
        }

        $db = self::connect('log');
        $logChanged = false;
        $messageCount = 0;

        assert('!isset($previousLog); /* cannot redeclare variable $previousLog */');
        $previousLog = $db->select("log.?");

        assert('!isset($message); /* cannot redeclare variable $message */');
        assert('!isset($newLog); // Cannot redeclare var $newLog');
        foreach ($messages as $message)
        {
            assert('$message instanceof Log; // unexpected result: Entry is not a subclass of Log');
            $newLog = Log::getLog($message, "log_");
            assert('is_array($newLog); // unexpected result: Log entry is expected to be an array');

            // check if new log entry is valid
            if (empty($newLog)) {
                continue;
            }

            // do not create duplicate entries
            if (Log::logEquals($newLog, $previousLog, "log_") === true) {
                continue;
            }

            // abort if insert failed
            if ($db->insert("log", $newLog) === false) {
                trigger_error("Unable to insert log. Insert on table 'log' failed.", E_USER_NOTICE);
                continue;
            }

            $previousLog = $newLog;
            $logChanged = true;
            $messageCount++;
        } // end foreach ($message)
        unset($message);

        // skip if nothing has changed
        if ($logChanged !== true) {
            return;
        }

        // abort if database commit failed
        if ($db->commit() === false) {
            trigger_error("Unable to write log. Commit on table 'log' failed.", E_USER_WARNING);
            return;
        }

        // get profile settings
        assert('!isset($logLength); // Cannot redeclare var $logLength');
        $maxLogLength = (int) $this->getVar('PROFILE.LOG_LENGTH');
        if ($maxLogLength < 1) {
            $maxLogLength = 50;
        }

        // finished, if number of log entries is still smaller than maximum
        if ($db->length('log') <= $maxLogLength) {
            return;
        }

        // get profile settings
        assert('!isset($useMail); // Cannot redeclare var $useMail');
        $useMail = (bool) $this->getVar('PROFILE.LOG.USE_MAIL');
        assert('!isset($logMail); // Cannot redeclare var $logMail');
        $logMail = $this->getVar('PROFILE.LOG.MAIL');

        // remove old entries
        if ($useMail && $logMail) {

            if (!empty($logMail)) {
                $mail = new FormMailer();
                $mail->content = $db->select('log', array(), array('LOG_ID'));
                $mail->subject = 'JOURNAL';
                $mail->send($logMail);
                unset($mail);
            }
            unset($logMail);

            // truncate table log
            if ($db->remove("log", array(), 0) === false) {
                trigger_error("Unable to remove entry from table 'log'.", E_USER_WARNING);
                return;
            }

            // commit changes
            if ($db->commit() === false) {
                trigger_error("Unable to commit changes to table 'log'.", E_USER_WARNING);
                return;
            }

        } else {
            $logIds = $db->select("log.*.log_id", array(), "", $maxLogLength, $db->length('log') + $messageCount, true);
            if (is_array($logIds)) {
                foreach ($logIds as $id)
                {
                    if (is_scalar($id) && !empty($id)) {
                        if ($db->remove("log.{$id}") === false) {
                            trigger_error("Unable to remove entry from table 'log'.", E_USER_WARNING);
                            return null;
                        }
                    }
                } /* end for */
                $db->commit();
            } else {
                $db->rollback();
            }
        }
    }

}

?>