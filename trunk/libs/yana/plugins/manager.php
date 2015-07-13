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

namespace Yana\Plugins;

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
 * $manager = \Yana\Plugins\Manager::getInstance();
 * try {
 *   $result = $manager->broadcastEvent('newState', $arguments);
 * } catch (\Exception $e) {
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
 * @name        PluginManager
 * @package     yana
 * @subpackage  plugins
 */
class Manager extends \Yana\Core\AbstractSingleton implements \Yana\Report\IsReportable
{

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
     * result of last handled action
     *
     * @var bool
     */
    private static $_lastResult = null;

    /**
     * name of currently handled event
     *
     * @var string
     */
    private static $_lastEvent = "";

    /**
     * name of initially handled event
     *
     * @var string
     */
    private static $_firstEvent = "";

    /**
     * definition of next event in queue
     *
     * @var \Yana\Plugins\Configs\EventRoute
     */
    private static $_nextEvent = null;

    /**
     * virtual drive
     *
     * @var array
     */
    private $_drive = array();

    /**
     * plugin objects
     *
     * @var array
     */
    private $_plugins = array();

    /**
     * currently loaded plugins
     *
     * @var array
     */
    private $_loadedPlugins = array();

    /**
     * @var \Yana\Plugins\Repository
     */
    private $_repository = null;

    /**#@+
     * class constants
     *
     * @ignore
     */

    const PREFIX = 'plugin_';

    /**#@-*/

    /**
     * Set path configuration.
     *
     * The plugin configuration file contains interface-settings for all plugins.
     * The plugin directory is the place, where all plugins reside.
     *
     * Example:
     * <code>
     *\Yana\Plugins\Manager::setPath("config/plugins.cfg", "plugins/");
     * </code>
     *
     * @param   string  $configurationFile  path to plugin configuration file (plugins.cfg)
     * @param   string  $pluginDirectory    path to plugin base directory
     * @throws  \Yana\Core\Exceptions\NotFoundException  when on of the given paths is invalid
     * @ignore
     */
    public static function setPath($configurationFile, $pluginDirectory)
    {
        assert('is_string($configurationFile)', ' Wrong type for argument 1. String expected');
        assert('is_string($pluginDirectory)', ' Invalid argument 2. String expected');
        assert('is_dir($pluginDirectory)', ' Invalid argument 2. Directory expected');

        if (!is_dir($pluginDirectory)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such directory: '$pluginDirectory'.", E_USER_ERROR);
        }

        self::$_path = $configurationFile;
        self::$_pluginDir = $pluginDirectory;
    }

    /**
     * Get path to plugin configuration file.
     *
     * The plugin configuration file contains interface-settings for all plugins.
     * Returns the path relative to the application root directory.
     *
     * @return  string
     */
    public static function getConfigFilePath()
    {
        return self::$_path;
    }

    /**
     * Get configuration manager.
     *
     * @return  \Yana\Plugins\Repository
     */
    private function _getRepository()
    {
        if (empty($this->_repository)) {
            if (file_exists(self::$_path)) {
                $this->_repository = unserialize(file_get_contents(self::$_path));
            } else {
                $this->_repository = new \Yana\Plugins\Repository();
            }
        }
        return $this->_repository;
    }

    /**
     * Get list of plugin configurations.
     *
     * Returns an associative array, where the keys are the plugin-names and the values are instances
     * of \Yana\Plugins\Configs\ClassConfiguration.
     *
     * @return  \Yana\Plugins\Configs\ClassCollection
     */
    public function getPluginConfigurations()
    {
        return $this->_getRepository()->getPlugins();
    }

    /**
     * Get path to plugin directory.
     *
     * The plugin directory is the place, where all plugins reside.
     * Returns the path relative to the application root directory.
     *
     * @return  string
     */
    public static function getPluginDirectoryPath()
    {
        return self::$_pluginDir;
    }

    /**
     * Broadcast an event to all plugins.
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
     * @param   string  $event  identifier of the occured event
     * @param   array   $args   list of arguments
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\NotReadableException    when an existing VDrive definition is not readable
     * @throws  \Yana\Core\Exceptions\InvalidActionException  when the event is undefined
     */
    public function broadcastEvent($event, array $args)
    {
        assert('is_string($event)', ' Invalid argument $event: string expected');

        // event must be defined
        $config = $this->getEventConfiguration($event);
        if (!($config instanceof \Yana\Plugins\Configs\MethodConfiguration)) {
            $error = new \Yana\Core\Exceptions\InvalidActionException();
            $error->setAction($event);
            throw $error;
        }

        if (empty(self::$_firstEvent)) {
            self::$_firstEvent = $event;
        }
        self::$_lastEvent = $event;
        $eventSubscribers = $this->_getEventSubscribers($event);
        $this->_loadPlugins(array_keys($eventSubscribers));
        self::$_lastResult = true;

        $config->setEventArguments($args);

        assert('!isset($element)', 'cannot redeclare variable $element');
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
     * Get result of last action handler.
     *
     * Returns the result of the last successfully handled action.
     * Returns bool(false) if there was an error.
     * Returns NULL if no action was handled yet.
     *
     * @return  mixed
     */
    public static function getLastResult()
    {
        return self::$_lastResult;
    }

    /**
     * Get the previously handled event.
     *
     * Returns the name of the current or previously handled event.
     *
     * If there has been no previous event, the function will return an empty string.
     *
     * @return  string
     */
    public static function getLastEvent()
    {
        return self::$_lastEvent;
    }

    /**
     * Get the initially handled event.
     *
     * Returns the name of the currently handled event.
     *
     * If there has been no previous event, the function will return an empty string.
     *
     * @return  string
     */
    public function getFirstEvent()
    {
        return self::$_firstEvent;
    }

    /**
     * Get the next event in queue.
     *
     * If the last action has a successor, this function returns the definition
     * of the next action in the queue.
     *
     * If there is no action, the function will return NULL.
     *
     * @return  \Yana\Plugins\Configs\EventRoute
     */
    public function getNextEvent()
    {
        if (!isset(self::$_nextEvent)) {
            $event = $this->getFirstEvent();
            $result = self::getLastResult();
            $methods = $this->getEventConfigurations();
            /* @var $method \Yana\Plugins\Configs\MethodConfiguration */
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
     * Rescan plugin directory and refresh the plugin cache.
     *
     * Returns bool(true) on sucess and bool(false) on error.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     *
     * @ignore
     */
    public function refreshPluginFile()
    {
        $builder = new \Yana\Plugins\RepositoryBuilder();
        $builder->addDirectory($this->getPluginDir());
        $builder->setBaseRepository($this->_getRepository());
        $repository = $builder->getRepository();

        // create repository cache
        if (file_put_contents(self::$_path, serialize($repository))) {
            // cache has been written and is not empty

            // actuate current config setting
            $this->_repository = $repository;
            return true;
        } else {
            // an error occured - unable to write cache file
            return false;
        }
    }

    /**
     * Check if plugin is active.
     *
     * Returns bool(true) if the plugin identified by $pluginName exists
     * and is active and bool(false) otherwise.
     *
     * @param   string  $pluginName  identifier for the plugin
     * @return  bool
     * @since   2.8.9
     */
    public function isActive($pluginName)
    {
        assert('is_string($pluginName)', ' Invalid argument $pluginName: string expected');
        $plugins = $this->_getRepository()->getPlugins();
        $active = null;
        if ($plugins->offsetExists($pluginName)) {
            $active = $plugins->offsetGet($pluginName)->getActive();
        }
        return $active === \Yana\Plugins\ActivityEnumeration::ACTIVE ||
            $active === \Yana\Plugins\ActivityEnumeration::DEFAULT_ACTIVE;
    }

    /**
     * Check if plugin is active by default.
     *
     * A plugin that is active by default cannot be deactivated via the configuration menu.
     *
     * Returns bool(true) if the plugin identified by $pluginName exists
     * and is active and bool(false) otherwise.
     *
     * @param   string  $pluginName  identifier for the plugin
     * @return  bool
     * @since   3.1.0
     */
    public function isDefaultActive($pluginName)
    {
        assert('is_string($pluginName)', ' Wrong type for argument 1. String expected');
        $plugins = $this->_getRepository()->getPlugins();
        $active = null;
        if ($plugins->offsetExists($pluginName)) {
            $active = $plugins->offsetGet($pluginName)->getActive();
        }
        return $active === \Yana\Plugins\ActivityEnumeration::DEFAULT_ACTIVE;
    }

    /**
     * Activate / deactive a plugin.
     *
     * @param   string  $pluginName   identifier for the plugin to be de-/activated
     * @param   int     $state        ActivityEnumeration::INACTIVE = off, ActivityEnumeration::ACTIVE = on
     * @throws  \Yana\Core\Exceptions\NotFoundException     when no plugin with the given name is found
     * @throws  \Yana\Core\Exceptions\InvalidValueException when trying to change a default plugin
     */
    public function setActive($pluginName, $state = \Yana\Plugins\ActivityEnumeration::ACTIVE)
    {
        $plugins = $this->_getRepository()->getPlugins();
        if ($plugins->offsetExists($pluginName)) {
            $plugin = $plugins->offsetGet($pluginName);
            if ($plugin->getActive() === \Yana\Plugins\ActivityEnumeration::DEFAULT_ACTIVE) {
                $message = "Changing activity state of plugin '$pluginName' with setting: 'always active' is not allowed.";
                throw new \Yana\Core\Exceptions\InvalidValueException($message);
            }
            $plugin->setActive($state);
        } else {
            throw new \Yana\Core\Exceptions\NotFoundException("No such plugin: '$pluginName'.");
        }
    }

    /**
     * Get a file from a virtual drive.
     *
     * Each plugin defines it's own virtual drive with files that are required
     * for it to function as intended.
     *
     * You may access the virtual drive of any plugin if you know the plugin's
     * name $pluginName and the name $key of the file you want.
     * This is usefull from plugins that extend the functionality of another.
     *
     * @param   string  $pluginName  identifier for the plugin
     * @param   string  $key         identifier for the file to get
     * @return  \Yana\Files\AbstractResource
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the plugin name is invalid
     */
    public function get($pluginName, $key)
    {
        assert('is_string($key)', ' Invalid argument $key: string expected');
        assert('is_string($pluginName)', ' Invalid argument $pluginName: string expected');

        $pluginName = (string) $pluginName;
        $key = (string) $key;

        if (isset($this->_drive[$pluginName])) {
            return $this->_drive[$pluginName]->getResource($key);
        } else {
            $message = "There is no plugin named '" . $pluginName . "'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
    }

    /**
     * Access the drive of a plugin by using it's name.
     *
     * @param   string  $name  name of plugin
     * @return  \Yana\VDrive\VDrive
     */
    public function __get($name)
    {
        assert('is_string($name)', ' Wrong type for argument 1. String expected');
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
     * Check if a specific plugin is installed.
     *
     * This returns bool(true) if a plugin with the name
     * $pluginName exists and has currently been installed.
     * Otherwise it returns bool(false).
     *
     * @param   string  $pluginName  identifier for the plugin
     * @return  bool
     */
    public function isInstalled($pluginName)
    {
        assert('is_bool($this->_isLoaded);');
        return (bool) ($this->_isLoaded && isset($this->_plugins[mb_strtolower("$pluginName")]));
    }

    /**
     * Returns the plugins and their properties as plain text.
     *
     * @return  string
     *
     * @ignore
     */
    public function __toString()
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
     * Get the name of the directory where plugins are installed.
     *
     * This returns a string value. By default the plugin install
     * path is "plugins/". Still you should note, that you are
     * strongly encouraged to use this function rather than using
     * hard-wired pathnames in your source-code.
     *
     * @return  string
     */
    public function getPluginDir()
    {
        assert('is_string(self::$_pluginDir);');
        return self::$_pluginDir;
    }

    /**
     * Get plugin configuration.
     *
     * Creates and returns a configuration object,
     * reflecting the implementing plugin class.
     *
     * @param   string  $pluginName   plugin name
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     * @since   3.1.0
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     */
    public function getPluginConfiguration($pluginName)
    {
        assert('is_string($pluginName)', ' Wrong type for argument 1. String expected');

        $this->_loadPlugin($pluginName); /** @todo check if this is necessary */
        $pluginConfig = $this->getPluginConfigurations();
        if (isset($pluginConfig[$pluginName])) {
            return $pluginConfig[$pluginName];
        } else {
            return new \Yana\Plugins\Configs\ClassConfiguration(self::PREFIX . $pluginName);
        }
    }

    /**
     * Returns a numeric array with the names of all available plugins.
     *
     * @return  array
     * @since   3.1.0
     */
    public function getPluginNames()
    {
        return array_keys($this->getPluginConfigurations()->toArray());
    }

    /**
     * Get the type of an event.
     *
     * Returns the type of the event identified by $eventName
     * as a string.
     *
     * If $eventName is not provided the current event is used.
     *
     * If no such event is defined, the default value is returned.
     *
     * @param   string  $eventName  identifier of the wanted event
     * @return  string
     * @ignore
     */
    public function getEventType($eventName = null)
    {
        if (is_null($eventName)) {
            $eventName = self::$_lastEvent;
        }
        assert('is_string($eventName)', ' Wrong type for argument 1. String expected');

        $methodsConfig = $this->getEventConfigurations();
        if (isset($methodsConfig[$eventName])) {
            /* String */ $type = $methodsConfig[$eventName]->getType();
        } else {
            assert('!isset($defaultEvent)', ' Cannot redeclare var $defaultEvent');
            /* array */ $defaultEvent = \Yana\Application::getDefault("EVENT");
            assert('is_array($defaultEvent);');
            if (is_array($defaultEvent) && isset($defaultEvent[\Yana\Plugins\Annotations\Enumeration::TYPE])) {
                /* string */ $type = $defaultEvent[\Yana\Plugins\Annotations\Enumeration::TYPE];
            } else {
                /* string */ $type = "default";
            }
            unset($defaultEvent);
        }
        assert('is_scalar($type)', ' Postcondition mismatch. Return type is supposed to be a string.');
        return "$type";
    }

    /**
     * Get the event configuration.
     *
     * @param   string  $eventName  identifier of the wanted event
     * @return  \Yana\Plugins\Configs\MethodConfiguration
     */
    public function getEventConfiguration($eventName)
    {
        assert('is_string($eventName)', ' Invalid argument $eventName: string expected');
        return $this->getEventConfigurations()->offsetGet($eventName);
    }

    /**
     * Get list of event configurations.
     *
     * @return  PluginMethodCollection
     */
    public function getEventConfigurations()
    {
        return $this->_getRepository()->getMethods();
    }

    /**
     * Check if event is defined.
     *
     * Returns bool(true) if the given string matches the name
     * of an defined event and bool(false) otherwise.
     *
     * @param   string  $eventName  identifier of the event
     * @return  bool
     */
    public function isEvent($eventName)
    {
        assert('is_string($eventName)', ' Invalid argument $eventName: string expected');
        return $this->_getRepository()->isMethod($eventName);
    }

    /**
     * Check if plugin is currently loaded.
     *
     * @param   string  $pluginName  identifier of the plugin to check
     * @return  bool
     */
    public function isLoaded($pluginName)
    {
        assert('is_string($pluginName)', ' Invalid argument $pluginName: string expected');
        return isset($this->_loadedPlugins[mb_strtolower("$pluginName")]);
    }

    /**
     * Get event subscribers.
     *
     * @param   string  $event  event
     * @return  array
     *
     * @ignore
     */
    private function _getEventSubscribers($event)
    {
        assert('is_string($event)', ' Invalid argument $event: string expected');
        $this->_loadedPlugins = array();

        $config = $this->_getRepository()->getImplementations($event);

        foreach (array_keys($config) as $pluginName)
        {
            $this->_loadedPlugins[$pluginName] = true;
        }
        arsort($config);
        return $config;
    }

    /**
     * Loads plugins from a list of names.
     *
     * If no list is provided, all known plugins are loaded.
     *
     * @param   array  $plugins list of plugin names
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     * @ignore
     */
    private function _loadPlugins(array $plugins)
    {
        foreach ($plugins as $name)
        {
            $this->_loadPlugin($name);
        }
        $this->_isLoaded = true;
    }

    /**
     * Load a plugin.
     *
     * @param   string  $name  Must be valid identifier. Consists of chars, numbers and underscores.
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     * @ignore
     */
    private function _loadPlugin($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        if (!isset($this->_plugins[$name])) {
            $pluginDir = $this->getPluginDir();
            // load virtual drive, if it exists
            assert('!isset($driveFile)', ' Cannot redeclare var $driveFile');
            $driveFile = "$pluginDir$name/$name.drive.xml";
            if (is_file($driveFile)) {
                $this->_drive[$name] = new \Yana\VDrive\Registry($driveFile, $this->getPluginDir() . $name . "/");
                $this->_drive[$name]->read();
            }
            unset($driveFile);
            // load base class, if it exists
            assert('!isset($classFile)', ' Cannot redeclare var $classFile');
            $classFile = "$pluginDir$name/$name.plugin.php";
            if (is_file($classFile)) {
                include_once "$classFile";
            }
            unset($classFile);
            // instantiate class, if it exists
            if (class_exists(\Yana\Plugins\Manager::PREFIX . $name)) {
                $class = \Yana\Plugins\Manager::PREFIX . $name;
                $this->_plugins[$name] = new $class();
            }
        } else {
            /* plugin is already loaded */
        }
    }

    /**
     * get a report
     *
     * Returns a \Yana\Report\Xml object, which you may print, transform or output to a file.
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
     * $manager = \Yana\Plugins\Manager::getInstance();
     * $report = $manager->getReport();
     * $errors = $report->getErrors();
     * if (empty($errors)) {
     * print 'all fine';
     * } else {
     * print 'The following errors were reported:'.print_r($errors, 1);
     * }
     * </code>
     *
     * @param   \Yana\Report\IsReport  $report  base report
     * @return  \Yana\Report\IsReport
     * @name    \Yana\Plugins\Manager::getReport()
     * @ignore
     */
    public function getReport(\Yana\Report\IsReport $report = null)
    {
        if (is_null($report)) {
            $report = \Yana\Report\Xml::createReport(__CLASS__);
        }
        $report->addText("Plugin directory: " . \Yana\Plugins\Manager::$_pluginDir);
        $methodsConfig = $this->getEventConfigurations();

        if (empty($methodsConfig)) {
            $report->addWarning("Cannot perform check! No interface definitions found.");

        } else {
            $skin = \Yana\Application::getInstance()->getSkin();

            /**
             * loop through interface definitions
             */
            foreach ($methodsConfig as $key => $element)
            {
                // @todo  check if $element is really an array (and not a \Yana\Plugins\Configs\MethodConfiguration)
                if (!is_array($element)) {
                    continue;
                }

                $subReport = $report->addReport("$key");

                /**
                 * check for type attribute
                 */
                assert('!isset($type)', ' Cannot redeclare var $type');
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
                assert('!isset($template)', ' Cannot redeclare var $template');
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
                            $subReport->addText("Template: {$filename}");
                        }
                    } catch (\Yana\Core\Exceptions\NotFoundException $e) {
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
     * Returns the class name of the called class.
     *
     * @return string
     */
    protected static function _getClassName()
    {
        return __CLASS__;
    }

}

?>