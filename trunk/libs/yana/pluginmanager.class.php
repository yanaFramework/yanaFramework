<?php
/**
 * YANA library
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
 * <<Singleton>> <<Mediator>> PluginManager
 *
 * This class implements communication between plugins and provides access to virtual drives
 * and local registries which may be defined on a per plugin basis.
 *
 * {@internal
 * Note that this implements the Mediator pattern.
 *
 * This is not to be mixed with the Observer pattern:
 * The plugin does not inform the PluginManager that it has changed it's state and
 * requests it to reflect that by changing the application state.
 *
 * Instead the PluginManager recieves a new system event (function call) and broadcasts a
 * request to all  subscribing plugins to change their state accordingly and not vice versa.
 * }}
 *
 * Code example for "broadcasting" an event to all plugins (= calling a function):
 * <code>
 * $manager = PluginManager::getInstance();
 * try {
 *   $result = $manager->broadcastEvent('newState', $arguments);
 * } catch (Exception $e) {
 *   // put error handling here
 * }
 * </code>
 *
 * The code above will call the function "newState" with the given arguments on any registered
 * plugin that implements this method and return the last computed result (presenting the new
 * state of the application).
 *
 * Each plugin may cascade the event by triggering a new application event the same way as
 * described above. Also each plugin may abort the chain of operation at any time by either
 * returning FALSE or throwing an exception.
 *
 * @access      public
 * @name        PluginManager
 * @package     yana
 * @subpackage  core
 */
class PluginManager extends Singleton implements IsReportable
{
    /**#@+
     * class constants
     *
     * @ignore
     */

    const METHODS = 0;
    const PLUGINS = 1;
    const IMPLEMENTATIONS = 2;
    const OVERWRITTEN = 3;
    const ACTIVE = 4;

    /**#@-*/

    /**
     * This is a place-holder for the singleton's instance
     *
     * @access  private
     * @static
     * @var     object
     */
    private static $_instance = null;

    /**#@+
     * @ignore
     * @access  private
     */

    /**
     * @var string
     */
    private static $_pluginDir = "plugins/";

    /**
     * @var string
     */
    private static $_path = "config/pluginconfig.cfg";

    /**
     * @var bool
     */
    private $_isLoaded = false;

    /**
     * configuration
     * @var array
     */
    private $_config = array();

    /**
     * result of last handled action
     *
     * @var bool
     * @static
     */
    private static $_lastResult = null;

    /**
     * name of currently handled event
     *
     * @var string
     * @static
     */
    private static $_lastEvent = "";

    /**
     * name of initially handled event
     *
     * @var string
     * @static
     */
    private static $_firstEvent = "";

    /**
     * definition of next event in queue
     *
     * @var PluginEventRoute
     * @static
     */
    private static $_nextEvent = null;

    /**
     * virtual drive
     * @var array
     */
    private $_drive = array();

    /**
     * plugin objects
     * @var array
     */
    private $_plugins = array();

    /**
     * currently loaded plugins
     * @var array
     */
    private $_loadedPlugins = array();

    /**#@-*/
    /**#@+
     * class constants
     *
     * @ignore
     */

    const PREFIX = 'plugin_';

    /**#@-*/

    /**
     * get instance of this class
     *
     * Creates an instance if there is none.
     * Then it returns a reference to this (single) instance.
     *
     * @access  public
     * @static
     * @return  PluginManager
     */
    public static function &getInstance()
    {
        assert('isset(self::$_pluginDir);');
        assert('isset(self::$_path);');
        if (!isset(self::$_instance)) {
            self::$_instance = new PluginManager();
        }
        return self::$_instance;
    }

    /**
     * <<Singleton>> Constructor
     *
     * Creates and initializes a new instance of this class.
     * Note: this constructor is private. You may want to
     * call the static PluginManager::getInstance() method instead.
     *
     * @name    PluginManager::__construct()
     * @ignore
     */
    private function __construct()
    {
        // intentionally left blank
    }

    /**
     * set path configuration
     *
     * The plugin configuration file contains interface-settings for all plugins.
     * The plugin directory is the place, where all plugins reside.
     *
     * Example:
     * <code>
     * PluginManager::setPath("config/plugins.cfg", "plugins/");
     * </code>
     *
     * @access  public
     * @static
     * @param   string  $configurationFile  path to plugin configuration file (plugins.cfg)
     * @param   string  $pluginDirectory    path to plugin base directory
     * @throws  NotFoundException           when on of the given paths is invalid
     * @ignore
     */
    public static function setPath($configurationFile, $pluginDirectory)
    {
        assert('is_string($configurationFile); // Wrong type for argument 1. String expected');
        assert('is_string($pluginDirectory); // Invalid argument 2. String expected');
        assert('is_dir($pluginDirectory); // Invalid argument 2. Directory expected');

        if (!is_dir($pluginDirectory)) {
            throw new NotFoundException("No such directory: '$pluginDirectory'.", E_USER_ERROR);
        }

        self::$_path = $configurationFile;
        self::$_pluginDir = $pluginDirectory;
    }

    /**
     * get path to plugin configuration file
     *
     * The plugin configuration file contains interface-settings for all plugins.
     * Returns the path relative to the application root directory.
     *
     * @access  public
     * @static
     * @return  string
     */
    public static function getConfigFilePath()
    {
        return self::$_path;
    }

    /**
     * Get configuration array.
     *
     * @access  private
     * @return  array
     */
    private function &_getConfig()
    {
        if (empty($this->_config) && file_exists(self::$_path)) {
            $this->_config = unserialize(file_get_contents(self::$_path));
        }
        return $this->_config;
    }

    /**
     * Get list of plugin configurations.
     *
     * Returns an associative array, where the keys are the plugin-names and the values are instances
     * of PluginConfigurationClass.
     *
     * @access  public
     * @return  PluginConfigurationClass[]
     */
    public function getPluginConfigurations()
    {
        $config = $this->_getConfig();
        return $config[self::PLUGINS];
    }

    /**
     * Get implementations configuration array.
     *
     * If the event is not registered, the function returns NULL.
     *
     * @access  private
     * @return  array
     */
    private function _getImplementationConfig($event)
    {
        $config = $this->_getConfig();
        return (isset($config[self::IMPLEMENTATIONS][$event])) ? $config[self::IMPLEMENTATIONS][$event] : null;
    }

    /**
     * Get activity state configuration.
     *
     * Returns an item of PluginActivityEnumeration.
     * If the plugin does not exist, the function always returns
     * PluginActivityEnumeration::INACTIVE.
     *
     * @access  private
     * @param   $pluginName
     * @return  int
     */
    private function _getActivityState($pluginName)
    {
        $config = $this->_getConfig();
        if (isset($config[self::ACTIVE][$pluginName])) {
            return $config[self::ACTIVE][$pluginName];
        } else {
            return PluginActivityEnumeration::INACTIVE;
        }
    }

    /**
     * get path to plugin directory
     *
     * The plugin directory is the place, where all plugins reside.
     * Returns the path relative to the application root directory.
     *
     * @access  public
     * @static
     * @return  string
     */
    public static function getPluginDirectoryPath()
    {
        return self::$_pluginDir;
    }

    /**
     * broadcast an event to all plugins
     *
     * This function looks up an event that you provide
     * with the argument $event, and sends it to all
     * plugins that are in the event's group of recipients.
     *
     * Note: that "handle an event" actually means "calling
     * a function that serves as an event handler".
     * You may pass arguments to this function by using
     * the argument $ARGUMENTS, which is supposed to be
     * an associative array.
     *
     * @access  public
     * @param   string  $event  identifier of the occured event
     * @param   array   $args   list of arguments
     * @return  mixed
     * @throws  NotReadableException  when an existing VDrive definition is not readable
     * @throws  InvalidActionError    when the event is undefined
     */
    public function broadcastEvent($event, array $args)
    {
        assert('is_string($event); // Invalid argument $event: string expected');

        // event must be defined
        $config = $this->getEventConfiguration($event);
        if (!($config instanceof PluginConfigurationMethod)) {
            $error = new InvalidActionError();
            $error->setData(array('ACTION' => $event));
            throw new $error;
        }

        if (empty(self::$_firstEvent)) {
            self::$_firstEvent = $event;
        }
        self::$_lastEvent = $event;
        $eventSubscribers = $this->_getEventSubscribers($event);
        $this->_loadPlugins(array_keys($eventSubscribers));
        self::$_lastResult = true;

        $config->setEventArguments($args);

        assert('!isset($element); /* cannot redeclare variable $element */');
        foreach ($this->_plugins as $element)
        {
            $lastResult = $config->sendEvent($element);
            if ($lastResult === false) {
                self::$_lastResult = false;
                break;
            }
            if ($config->hasMethod($element)) {
                self::$_lastResult = $lastResult;
            }
        }
        unset($element);

        return self::$_lastResult;
    }

    /**
     * Get result of last action handler
     *
     * Returns the result of the last successfully handled action.
     * Returns bool(false) if there was an error.
     * Returns NULL if no action was handled yet.
     *
     * @access  public
     * @static
     * @return  mixed
     */
    public static function getLastResult()
    {
        return self::$_lastResult;
    }

    /**
     * Get the previously handled event
     *
     * Returns the name of the current or previously handled event.
     *
     * If there has been no previous event, the function will return an empty string.
     *
     * @access  public
     * @static
     * @return  string
     */
    public static function getLastEvent()
    {
        return self::$_lastEvent;
    }

    /**
     * Get the initially handled event
     *
     * Returns the name of the currently handled event.
     *
     * If there has been no previous event, the function will return an empty string.
     *
     * @access  public
     * @return  string
     */
    public function getFirstEvent()
    {
        return self::$_firstEvent;
    }

    /**
     * Get the next event in queue
     *
     * If the last action has a successor, this function returns the definition
     * of the next action in the queue.
     *
     * If there is no action, the function will return NULL.
     *
     * @access  public
     * @return  PluginEventRoute
     */
    public function getNextEvent()
    {
        if (!isset(self::$_nextEvent)) {
            $event = $this->getFirstEvent();
            $result = self::getLastResult();
            $methods = $this->getEventConfigurations();
            /* @var $method PluginConfigurationMethod */
            $method = $methods[$event];
            if ($result !== false) {
                self::$_nextEvent = $method->getOnSuccess();
            } else {
                self::$_nextEvent = $method->getOnError();
            }
        }
        return self::$_nextEvent;
    }

    /**
     * refresh plugin file
     *
     * Rescan plugin directory and refresh the plugin cache.
     *
     * Returns bool(true) on sucess and bool(false) on error.
     *
     * @access  public
     * @return  bool
     * @throws  NotReadableException  when an existing VDrive definition is not readable
     *
     * @ignore
     */
    public function refreshPluginFile()
    {
        $pluginDir = $this->getPluginDir();
        $plugins = array();
        assert('!isset($classFile); // Cannot redeclare var $classFile');
        foreach (scandir(self::$_pluginDir) as $plugin)
        {
            if ($plugin[0] !== '.' && is_dir(self::$_pluginDir . '/' . $plugin)) {
                $classFile = $pluginDir . $plugin . "/" . $plugin . ".plugin.php";
                if (is_file($classFile)) {
                    $plugins[$plugin] = "plugin_" . $plugin;
                    include_once "$classFile";
                }
            }
        }
        unset($plugin, $classFile, $pluginDir);

        // output var
        $newPluginFile = array
        (
            self::PLUGINS => array(),
            self::METHODS => array(),
            self::OVERWRITTEN => array(),
            self::IMPLEMENTATIONS => array(),
            self::ACTIVE => array()
        );

        /* @var $config array */
        assert('!isset($config); // Cannot redeclare var $config');
        $config = $this->_getConfig();
        // copy settings from old plugin repository
        if (isset($config[self::ACTIVE])) {
            $newPluginFile[self::ACTIVE] = $config[self::ACTIVE];
        }
        unset($config);

        // initialize list for later use (see step 3)
        $pluginsWithDefaultMethods = array();
        $pluginGroups = array();

        // clear cache
        SmartTemplate::clearCache();

        // list of subscribing methods
        $subscribers = array();
        $builder = new PluginConfigurationBuilder();

        /**
         * 1) build plugin repository
         */
        assert('!isset($reflectionClass); // Cannot redeclare var $reflectionClass');
        assert('!isset($className); // Cannot redeclare var $className');
        assert('!isset($config); // Cannot redeclare var $config');
        assert('!isset($id); // Cannot redeclare var $id');
        foreach ($plugins as $id => $className)
        {
            $builder->createNewConfiguration();
            $builder->setReflection(new PluginReflectionClass($className));
            $config = $builder->getPluginConfigurationClass();
            $newPluginFile[self::PLUGINS][$id] = $config;

            // get name of parent plugin
            $parent = $config->getParent();

            /**
             * get active preset
             *
             * if the plugin's active state is unknown and there is a default state defined by the plugin,
             * use the setting defined by the plugin.
             */
            if (!isset($newPluginFile[self::ACTIVE][$id])) {
                $newPluginFile[self::ACTIVE][$id] = $config->getActive();
            }
            // ignore methods if plugin is not active
            if ($newPluginFile[self::ACTIVE][$id] === PluginActivityEnumeration::INACTIVE) {
                continue;
            }
            /**
             * 2) build method repository
             */
            foreach ($config->getMethods() as $methodName => $method)
            {
                // skip default event handlers (will be handled in step 3)
                if ($methodName == 'catchAll') {
                    $pluginsWithDefaultMethods[$id] = $config;
                    continue;
                }

                $isOverwrite = $method->getOverwrite();
                $isSubscriber = $method->getSubscribe();

                // add method to index
                if ((!isset($newPluginFile[self::METHODS][$methodName]) || $isOverwrite) && !$isSubscriber) {
                    $newPluginFile[self::METHODS][$methodName] = $method;
                } elseif ($isSubscriber) {
                    $subscribers[$methodName][] = $method; // will be used later
                }

                // overwrite method configuration of base plugin
                if ($isOverwrite && !empty($parent)) {
                    $newPluginFile[self::OVERWRITTEN][$methodName][$parent] = true;
                    if (isset($newPluginFile[self::IMPLEMENTATIONS][$methodName][$parent])) {
                        unset($newPluginFile[self::IMPLEMENTATIONS][$methodName][$parent]);
                    }
                }

                // add to implementations
                if (!isset($newPluginFile[self::OVERWRITTEN][$methodName][$className])) {
                    $newPluginFile[self::IMPLEMENTATIONS][$methodName][$id] = $config->getPriority();
                }
            } // end foreach method
            unset($isOverwrite, $isSubscriber, $methodName, $method);
        } // end foreach plugin
        unset($id, $name, $parent);

        /**
         * 3) join default event handlers to event implementations
         *
         * A plugin may define a function named "catchAll" to catch all events.
         * These event handlers need to be added as recipients to any event
         * defintion of the corresponding group and type of the implementing
         * plugin.
         */

        /**
         * plugin multicast-groups configuration
         */
        $mulitcastGroups = Yana::getDefault("MULTICAST_GROUPS");
        // default value
        if (empty($mulitcastGroups)) {
            $mulitcastGroups = array
            (
                'read' => array
                (
                    'security' => true,
                    'library' => true,
                    'read' => true,
                    'primary' => true,
                    'default' => true
                ),
                'write' => array
                (
                    'security' => true,
                    'library' => true,
                    'write' => true,
                    'primary' => true,
                    'default' => true
                ),
                'config' => array
                (
                    'security' => true,
                    'library' => true,
                    'config' => true
                ),
                'primary' => array
                (
                    'security' => true,
                    'library' => true,
                    'primary' => true
                ),
                'default' => array
                (
                    'security' => true,
                    'library' => true,
                    'default' => true
                ),
                'security' => array
                (
                    'security' => true,
                    'library' => true
                ),
                'library' => array
                (
                )
            );
        } else {
            $mulitcastGroups = Hashtable::changeCase($mulitcastGroups, CASE_LOWER);
        } // end if

        // load configuration settings for each method and build list of implementing classes
        assert('!isset($methodName); // Cannot redeclare var $methodName');
        assert('!isset($methodConfig); // Cannot redeclare var $methodConfig');
        foreach ($newPluginFile[self::METHODS] as $methodName => $methodConfig)
        {
            // get type of current event
            $baseType = $methodConfig->getType();
            $baseGroup = $methodConfig->getGroup();

            // copy properties from subscribers
            if (!empty($subscribers[$methodName])) {
                assert('!isset($subscriberConfig); // Cannot redeclare var $subscriberConfig');
                foreach ($subscribers[$methodName] as $subscriberConfig)
                {
                    $methodConfig->addSubscription($subscriberConfig);
                }
                unset($subscriberConfig);
            }

            assert('!isset($pluginName); // Cannot redeclare var $pluginName');
            assert('!isset($pluginConfig); // Cannot redeclare var $pluginConfig');
            foreach ($pluginsWithDefaultMethods as $pluginName => $pluginConfig)
            {
                // get type of current plugin
                $currentType = $pluginConfig->getType();
                $currentGroup = $pluginConfig->getGroup();

                // skip if group doesn't match
                if (!empty($currentGroup) && $baseGroup != $currentGroup) {
                    continue;
                }

                // skip if type is not in group of recipients
                if (empty($mulitcastGroups[$baseType][$currentType])) {
                    continue;
                }

                $newPluginFile[self::IMPLEMENTATIONS][$methodName][$pluginName] = $pluginConfig->getPriority();
            }
            unset($pluginName, $pluginConfig);
        }
        unset($methodName, $config);

        // create repository cache
        if (file_put_contents(self::$_path, serialize($newPluginFile))) {
            // cache has been written and is not empty

            // actuate current config setting
            $this->_config = $newPluginFile;
            return true;
        } else {
            // an error occured - unable to write cache file
            return false;
        }
    }

    /**
     * check if plugin is active
     *
     * Returns bool(true) if the plugin identified by $pluginName exists
     * and is active and bool(false) otherwise.
     *
     * @access  public
     * @param   string  $pluginName  identifier for the plugin
     * @return  bool
     * @since   2.8.9
     */
    public function isActive($pluginName)
    {
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');
        $active = $this->_getActivityState($pluginName);
        return $active === PluginActivityEnumeration::ACTIVE || $active === PluginActivityEnumeration::DEFAULT_ACTIVE;
    }

    /**
     * check if plugin is active by default
     *
     * A plugin that is active by default cannot be deactivated via the configuration menu.
     *
     * Returns bool(true) if the plugin identified by $pluginName exists
     * and is active and bool(false) otherwise.
     *
     * @access  public
     * @param   string  $pluginName  identifier for the plugin
     * @return  bool
     * @since   3.1.0
     */
    public function isDefaultActive($pluginName)
    {
        assert('is_string($pluginName); // Wrong type for argument 1. String expected');
        $active = $this->_getActivityState($pluginName);
        return $active === PluginActivityEnumeration::DEFAULT_ACTIVE;
    }

    /**
     * activate / deactive a plugin
     *
     * Sets the plugin identified by $pluginName
     * to active (1) or inactive (0).
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @param   string  $pluginName  identifier for the plugin to be de-/activated
     * @param   int     $state       0 = off, 1 = on, 2 = reserved (do not use)
     * @return  bool
     *
     * @ignore
     */
    public function setActive($pluginName, $state = PluginActivityEnumeration::ACTIVE)
    {
        assert('is_string($pluginName); // Wrong type for argument 1. String expected');
        assert('is_int($state); // Wrong type for argument 2. Integer expected');

        $pluginConfig = $this->getPluginConfigurations();
        $config =& $this->_getConfig();
        if (isset($pluginConfig[$pluginName])) {
            if ($config[self::ACTIVE][$pluginName] != PluginActivityEnumeration::DEFAULT_ACTIVE) {
                $config[self::ACTIVE][$pluginName] = $state;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * get a file from a virtual drive
     *
     * Each plugin defines it's own virtual drive with files that are required
     * for it to function as intended.
     *
     * You may access the virtual drive of any plugin if you know the plugin's
     * name $pluginName and the name $key of the file you want.
     * This is usefull from plugins that extend the functionality of another.
     *
     * @access  public
     * @param   string  $pluginName  identifier for the plugin
     * @param   string  $key         identifier for the file to get
     * @return  FileSystemResource
     * @throws  InvalidArgumentException
     */
    public function get($pluginName, $key)
    {
        assert('is_string($pluginName); // Wrong type for argument 1. String expected');
        assert('is_string($key); // Wrong type for argument 2. String expected');

        /* settype to STRING */
        $pluginName = (string) $pluginName;
        $key = (string) $key;

        if (isset($this->_drive[$pluginName])) {
            return $this->_drive[$pluginName]->getResource($key);
        } else {
            throw new InvalidArgumentException("There is no plugin named '".$pluginName."'.", E_USER_WARNING);
        }
    }

    /**
     * get a plugin's drive
     *
     * You may access the drive of a plugin by using it's name.
     *
     * @access  public
     * @param   string  $name  name of plugin
     * @return  VDrive
     */
    public function __get($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (!isset($this->_drive[$name])) {
            // recursive search
            $drive = substr($name, 0, strpos($name, ':/'));
            if (isset($this->_drive[$drive])) {
                $this->_drive[$name] = $this->_drive[$drive]->$name;
            } else {
                $this->_drive[$name] = null;
            }
        }
        return $this->_drive[$name];
    }

    /**
     * check if a specific plugin is installed
     *
     * This returns bool(true) if a plugin with the name
     * $pluginName exists and has currently been installed.
     * Otherwise it returns bool(false).
     *
     * @access  public
     * @param   string  $pluginName  identifier for the plugin
     * @return  bool
     */
    public function isInstalled($pluginName)
    {
        assert('is_bool($this->_isLoaded);');
        if ($this->_isLoaded && isset($this->_plugins[mb_strtolower("$pluginName")])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * toString
     *
     * @access  public
     * @return  string
     *
     * @ignore
     */
    public function toString()
    {
        $pluginConfig = $this->getPluginConfigurations();
        if (!empty($pluginConfig)) {
            $txt = "";
            foreach ($pluginConfig as $pluginName => $pluginConfig)
            {
                $txt .= "Plugin \"$pluginName\":\n" .
                "\t- active = " . (($this->isActive($pluginName)) ? "yes" : "no") . "\n" .
                "\t- type = " . $pluginConfig->getType() . "\n" .
                "\t- priority = " . $pluginConfig->getPriority() . "\n";
            }
            return $txt;
        } else {
            return "Plugin list is empty.\n";
        }
    }

    /**
     * get the name of the directory where plugins are installed
     *
     * This returns a string value. By default the plugin install
     * path is "plugins/". Still you should note, that you are
     * strongly encouraged to use this function rather than using
     * hard-wired pathnames in your source-code.
     *
     * @access  public
     * @return  string
     */
    public function getPluginDir()
    {
        assert('is_string(self::$_pluginDir);');
        return self::$_pluginDir;
    }

    /**
     * get plugin configuration
     *
     * Creates and returns a configuration object,
     * reflecting the implementing plugin class.
     *
     * @access  public
     * @param   string  $pluginName   plugin name
     * @return  PluginConfigurationClass
     * @since   3.1.0
     * @throws  NotReadableException  when an existing VDrive definition is not readable
     */
    public function getPluginConfiguration($pluginName)
    {
        assert('is_string($pluginName); // Wrong type for argument 1. String expected');

        $this->_loadPlugin($pluginName); /** @todo check if this is necessary */
        $pluginConfig = $this->getPluginConfigurations();
        if (isset($pluginConfig[$pluginName])) {
            return $pluginConfig[$pluginName];
        } else {
            return new PluginConfigurationClass(self::PREFIX . $pluginName);
        }
    }

    /**
     * get list of plugin names
     *
     * Returns a numeric array with a list of
     * all available plugins.
     *
     * @access  public
     * @return  array
     * @since   3.1.0
     */
    public function getPluginNames()
    {
        $pluginConfig = $this->getPluginConfigurations();
        return array_keys($pluginConfig);
    }

    /**
     * get the type of an event
     *
     * Returns the type of the event identified by $eventName
     * as a string.
     *
     * If $eventName is not provided the current event is used.
     *
     * If no such event is defined, the default value is returned.
     *
     * @access  public
     * @param   string  $eventName  identifier of the wanted event
     * @return  string
     * @ignore
     */
    public function getEventType($eventName = null)
    {
        if (is_null($eventName)) {
            $eventName = self::$_lastEvent;
        }
        assert('is_string($eventName); // Wrong type for argument 1. String expected');

        $methodsConfig = $this->getEventConfigurations();
        if (isset($methodsConfig[$eventName])) {
            /* String */ $type = $methodsConfig[$eventName]->getType();
        } else {
            assert('!isset($defaultEvent); // Cannot redeclare var $defaultEvent');
            /* array */ $defaultEvent = Yana::getDefault("EVENT");
            assert('is_array($defaultEvent);');
            if (is_array($defaultEvent) && isset($defaultEvent[PluginAnnotationEnumeration::TYPE])) {
                /* string */ $type = $defaultEvent[PluginAnnotationEnumeration::TYPE];
            } else {
                /* string */ $type = "default";
            }
            unset($defaultEvent);
        }
        assert('is_scalar($type); // Postcondition mismatch. Return type is supposed to be a string.');
        return "$type";
    }

    /**
     * get the event configuration
     *
     * @access  public
     * @param   string  $eventName  identifier of the wanted event
     * @return  PluginConfigurationMethod
     * @ignore
     */
    public function getEventConfiguration($eventName)
    {
        assert('is_string($eventName); // Invalid argument $eventName: string expected');
        $eventName = mb_strtolower("$eventName");
        $config = $this->getEventConfigurations();
        if (isset($config[$eventName])) {
            return $config[$eventName];
        } else {
            return null;
        }
    }

    /**
     * get list of event configurations
     *
     * @access  public
     * @return  array of PluginConfigurationMethods
     * @ignore
     */
    public function getEventConfigurations()
    {
        $config = $this->_getConfig();
        $methods = $config[self::METHODS];
        assert('is_array($methods); // List of methods not available');
        return $methods;
    }

    /**
     * check if event is defined
     *
     * Returns bool(true) if the given string matches the name
     * of an defined event and bool(false) otherwise.
     *
     * @access  public
     * @param   string  $eventName  identifier of the event
     * @return  bool
     */
    public function isEvent($eventName)
    {
        assert('is_string($eventName); // Invalid argument $eventName: string expected');
        $eventName = mb_strtolower("$eventName");
        $eventConfig = $this->getEventConfiguration($eventName);
        return !is_null($eventConfig);
    }

    /**
     * check if plugin is currently loaded
     *
     * @access  public
     * @param   string  $pluginName  identifier of the plugin to check
     * @return  bool
     */
    public function isLoaded($pluginName)
    {
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');
        return isset($this->_loadedPlugins[mb_strtolower("$pluginName")]);
    }

    /**
     * get event subscribers
     *
     * @access  private
     * @param   string  $event  event
     * @return  array
     *
     * @ignore
     */
    private function _getEventSubscribers($event)
    {
        assert('is_string($event); // Invalid argument $event: string expected');
        $this->_loadedPlugins = array();

        $result = array();
        $config = $this->_getImplementationConfig($event);

        if (!empty($config)) {
            foreach ($config as $pluginName => $priority)
            {
                if ($this->isActive($pluginName)) {
                    $this->_loadedPlugins[$pluginName] = true;
                    $result[$pluginName] = $priority;
                }
            }
        }
        arsort($result);
        return $result;
    }

    /**
     * _loadPlugins
     *
     * Loads plugins from a list of names.
     * If no list is provided, all known plugins are loaded.
     *
     * @access  private
     * @param   array  $plugins list of plugin names
     * @throws  NotReadableException  when an existing VDrive definition is not readable
     * @ignore
     */
    private function _loadPlugins(array $plugins)
    {
        $pluginDir = $this->getPluginDir();

        foreach ($plugins as $name)
        {
            $this->_loadPlugin($name);
        }
        $this->_isLoaded = true;
    }

    /**
     * Load a plugin
     *
     * @access  private
     * @param   string  $name  Must be valid identifier. Consists of chars, numbers and underscores.
     * @throws  NotReadableException  when an existing VDrive definition is not readable
     * @ignore
     */
    private function _loadPlugin($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        if (!isset($this->_plugins[$name])) {
            $pluginDir = $this->getPluginDir();
            // load virtual drive, if it exists
            assert('!isset($driveFile); // Cannot redeclare var $driveFile');
            $driveFile = "$pluginDir$name/$name.drive.xml";
            if (is_file($driveFile)) {
                $this->_drive[$name] = new Registry($driveFile, $this->getPluginDir() . $name . "/");
                $this->_drive[$name]->read();
            }
            unset($driveFile);
            // load base class, if it exists
            assert('!isset($classFile); // Cannot redeclare var $classFile');
            $classFile = "$pluginDir$name/$name.plugin.php";
            if (is_file($classFile)) {
                include_once "$classFile";
            }
            unset($classFile);
            // instantiate class, if it exists
            if (class_exists(PluginManager::PREFIX . $name)) {
                $class = PluginManager::PREFIX . $name;
                $this->_plugins[$name] = new $class();
            }
        } else {
            /* plugin is already loaded */
        }
    }

    /**
     * get a report
     *
     * Returns a ReportXML object, which you may print, transform or output to a file.
     *
     * Example:
     * <code>
     * <?xml version="1.0"?>
     * <report>
     *   <text>Plugin directory: plugins/</text>
     *   <report>
     *     <title>index</title>
     *     <error>File 'index.html' does not exist.</error>
     *   </report>
     *   <report>
     *     <title>foo</title>
     *     <text>Path: foo.html</text>
     *     <text>language: bar</text>
     *   </report>
     * </report>
     * </code>
     *
     * <code>
     * $manager = PluginManager::getInstance();
     * $report = $manager->getReport();
     * $errors = $report->getErrors();
     * if (empty($errors)) {
     * print 'all fine';
     * } else {
     * print 'The following errors were reported:'.print_r($errors, 1);
     * }
     * </code>
     *
     * @access  public
     * @param   ReportXML  $report  base report
     * @return  ReportXML
     * @name    PluginManager::getReport()
     * @ignore
     */
    public function getReport(ReportXML $report = null)
    {
        if (is_null($report)) {
            $report = ReportXML::createReport(__CLASS__);
        }
        $report->addText("Plugin directory: " . PluginManager::$_pluginDir);
        $methodsConfig = $this->getEventConfigurations();

        if (empty($methodsConfig)) {
            $report->addWarning("Cannot perform check! No interface definitions found.");

        } else {
            $skin = Yana::getInstance()->getSkin();

            /**
             * loop through interface definitions
             */
            foreach ($methodsConfig as $key => $element)
            {
                // @todo  check if $element is really an array (and not a PluginConfigurationMethod)
                if (!is_array($element)) {
                    continue;
                }

                $subReport = $report->addReport("$key");

                /**
                 * check for type attribute
                 */
                assert('!isset($type); // Cannot redeclare var $type');
                $type = $element->getType();
                if (empty($type)) {
                    $subReport->addWarning("The mandatory attribute 'type' is missing.");
                } else {
                    $subReport->addText('Type: ' . $type);
                }
                unset($type);

                /**
                 * check if template file exists
                 */
                assert('!isset($template); // Cannot redeclare var $template');
                $template = $element->getTemplate();
                $tplMessage = strcasecmp($template, "message");
                if (!empty($template) && strcasecmp($template, "null") !== 0 && $tplMessage !== 0) {
                    try {
                        $filename = $skin->getFile($template);
                        if (!file_exists($filename)) {
                            $subReport->addError("The chosen template '" . $template . "' is not available. " .
                                "Please check if reference and filename for this template are correct and " .
                                "all files have been installed correctly.");
                        } else {
                            $subReport->addText("Template: $filename");
                        }
                    } catch (NotFoundException $e) {
                        $subReport->addError("The definition of template '" . $template . "' contains errors: " .
                            $e->getMessage());
                    }
                }
                unset($template);
            } // end foreach
        } // end if

        return $report;
    }

    /**
     * Reinitialize instance.
     *
     * @access  public
     */
    public function __wakeup()
    {
        self::$_instance = $this;
    }

}

?>