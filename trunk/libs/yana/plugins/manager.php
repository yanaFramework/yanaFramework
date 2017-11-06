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
 * <<Singleton>> <<Mediator>> Plugin-manager.
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
class Manager extends \Yana\Core\AbstractSingleton implements \Yana\Report\IsReportable, \Yana\Log\IsLogable
{

    use \Yana\Log\HasLogger;

    /**
     * @var \Yana\Files\IsDir
     */
    private static $_pluginDir = null;

    /**
     * @var \Yana\Files\IsTextFile
     */
    private static $_path = null;

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
     * @var \Yana\Plugins\Repositories\Repository
     */
    private $_repository = null;

    /**
     * @var \Yana\Plugins\Dependencies\Container
     */
    private $_dependencies = null;

    /**
     * Get dependency injection container.
     *
     * Defaults to NULL.
     *
     * @return  \Yana\Plugins\Dependencies\Container
     */
    public function getDependencies()
    {
        return $this->_dependencies;
    }

    /**
     * Inject a dependency container.
     *
     * The container and its dependencies will be passed on to any plugins the manager loads.
     *
     * @param   \Yana\Plugins\Dependencies\Container  $dependencies  to inject
     * @return  \Yana\Plugins\Manager
     */
    public function attachDependencies(\Yana\Plugins\Dependencies\Container $dependencies)
    {
        $this->_dependencies = $dependencies;
        return $this;
    }

    /**
     * Set path configuration.
     *
     * The plugin configuration file contains interface-settings for all plugins.
     * The plugin directory is the place, where all plugins reside.
     *
     * Example:
     * <code>
     * \Yana\Plugins\Manager::setPath("config/plugins.cfg", "plugins/");
     * </code>
     *
     * @param   \Yana\Files\IsTextFile  $configurationFile  path to plugin configuration file (plugins.cfg)
     * @param   \Yana\Files\IsDir       $pluginDirectory    path to plugin base directory
     * @throws  \Yana\Core\Exceptions\NotFoundException  when on of the given paths is invalid
     * @ignore
     */
    public static function setPath(\Yana\Files\IsTextFile $configurationFile, \Yana\Files\IsDir $pluginDirectory)
    {
        if (!$pluginDirectory->exists()) {
            $message = "No such directory: '" . $pluginDirectory->getPath() . "'.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\NotFoundException($message, $level);
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
     * @return  \Yana\Files\IsTextFile
     */
    public static function getConfigFilePath()
    {
        if (!isset(self::$_path)) {
            self::$_path = new \Yana\Files\File("config/pluginconfig.cfg");
        }
        return self::$_path;
    }

    /**
     * Get configuration manager.
     *
     * @return  \Yana\Plugins\Repositories\Repository
     */
    private function _getRepository()
    {
        if (empty($this->_repository)) {
            $file = self::getConfigFilePath();
            if ($file->exists() && !$file->read()->isEmpty()) {
                $this->_repository = unserialize($file->getContent());
            } else {
                $this->_repository = new \Yana\Plugins\Repositories\Repository();
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
     * @param   string             $event        identifier of the occured event
     * @param   array              $args         list of arguments
     * @param   \Yana\Application  $application  facade
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\NotReadableException    when an existing VDrive definition is not readable
     * @throws  \Yana\Core\Exceptions\InvalidActionException  when the event is undefined
     */
    public function broadcastEvent($event, array $args, \Yana\Application $application)
    {
        assert('is_string($event); // Invalid argument $event: string expected');

        // event must be defined
        $config = $this->getEventConfiguration($event);
        if (!($config instanceof \Yana\Plugins\Configs\IsMethodConfiguration)) {
            $error = new \Yana\Core\Exceptions\InvalidActionException();
            $error->setAction($event);
            throw $error;
        }

        if (empty(self::$_firstEvent)) {
            self::$_firstEvent = $event;
        }
        self::$_lastEvent = $event;
        $eventSubscribers = $this->_getEventSubscribers($event);
        $this->_loadPlugins(array_keys($eventSubscribers), $application);
        self::$_lastResult = true;

        $config->setEventArguments($args);

        assert('!isset($element); // cannot redeclare variable $element');
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
            /* @var $method \Yana\Plugins\Configs\IsMethodConfiguration */
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
     * @return  self
     * @throws  \Yana\Core\Exceptions\NotReadableException   when an existing VDrive definition is not readable
     * @throws  \Yana\Core\Exceptions\NotWriteableException  when the repository file can't be written
     */
    public function refreshPluginFile()
    {
        $builder = new \Yana\Plugins\Repositories\Builder();
        $builder->addDirectory($this->getPluginDir());
        $builder->setBaseRepository($this->_getRepository());
        $builder->attachLogger($this->getLogger());
        $repository = $builder->getRepository();

        $file = self::getConfigFilePath();
        $file->setContent(serialize($repository));
        switch (false)
        {
            // create repository cache
            case $file->exists() || $file->create():
            case $file->write():
                // an error occured - unable to write cache file
                $message = "Repository file '" . $file->getPath() . "' not writeable";
                $code = \Yana\Log\TypeEnumeration::ERROR;
                throw new \Yana\Core\Exceptions\NotWriteableException($message, $code);
        }
        // cache has been written and is not empty
        // actuate current config setting
        $this->_repository = $repository;
        return $this;
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
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');
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
        assert('is_string($pluginName); // Wrong type for argument 1. String expected');
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
     * @return  \Yana\Plugins\Manager
     */
    public function setActive($pluginName, $state = \Yana\Plugins\ActivityEnumeration::ACTIVE)
    {
        $plugins = $this->_getRepository()->getPlugins();
        if (isset($plugins[$pluginName])) {
            $plugin = $plugins[$pluginName];
            if ($plugin->getActive() === \Yana\Plugins\ActivityEnumeration::DEFAULT_ACTIVE) {
                $message = "Changing activity state of plugin '$pluginName' with setting: 'always active' is not allowed.";
                throw new \Yana\Core\Exceptions\InvalidValueException($message);
            }
            $plugin->setActive($state);
        } else {
            throw new \Yana\Core\Exceptions\NotFoundException("No such plugin: '$pluginName'.");
        }
        return $this;
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
        assert('is_string($key); // Invalid argument $key: string expected');
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');

        $pluginName = (string) $pluginName;
        $key = (string) $key;

        if (isset($this->_drive[$pluginName])) {
            return $this->_drive[$pluginName]->getResource($key);
        } else {
            $message = "There is no plugin named '" . $pluginName . "'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
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
     * @return  \Yana\Files\IsDir
     */
    public function getPluginDir()
    {
        if (!isset(self::$_pluginDir)) {
            self::$_pluginDir = new \Yana\Files\Dir("plugins/");
        }
        return self::$_pluginDir;
    }

    /**
     * Get plugin configuration.
     *
     * Creates and returns a configuration object,
     * reflecting the implementing plugin class.
     *
     * @param   string  $pluginName   plugin name
     * @return  \Yana\Plugins\Configs\IsClassConfiguration
     * @since   3.1.0
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     */
    public function getPluginConfiguration($pluginName)
    {
        assert('is_string($pluginName); // Wrong type for argument 1. String expected');

        /**
         * @todo check if this is necessary
         * $this->_loadPlugin($pluginName); 
         */
        $pluginConfig = $this->getPluginConfigurations();
        if (isset($pluginConfig[$pluginName])) {
            return $pluginConfig[$pluginName];
        } else {
            $className = \Yana\Plugins\PluginNameMapper::toClassNameWithNamespace($pluginName);
            return new \Yana\Plugins\Configs\ClassConfiguration($className);
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
        assert('is_string($eventName); // Wrong type for argument 1. String expected');

        $methodsConfig = $this->getEventConfigurations();
        if (isset($methodsConfig[$eventName])) {
            /* String */ $type = $methodsConfig[$eventName]->getType();
        } else {
            assert('!isset($defaultEvent); // Cannot redeclare var $defaultEvent');
            /* array */ $defaultEvent = $this->getDependencies()->getDefaultEvent();
            assert('is_array($defaultEvent);');
            if (is_array($defaultEvent) && isset($defaultEvent[\Yana\Plugins\Annotations\Enumeration::TYPE])) {
                /* string */ $type = $defaultEvent[\Yana\Plugins\Annotations\Enumeration::TYPE];
            } else {
                /* string */ $type = "default";
            }
            unset($defaultEvent);
        }
        assert('is_scalar($type); // Postcondition mismatch. Return type is supposed to be a string.');
        return (string) $type;
    }

    /**
     * Get the event configuration.
     *
     * @param   string  $eventName  identifier of the wanted event
     * @return  \Yana\Plugins\Configs\IsMethodConfiguration
     */
    public function getEventConfiguration($eventName)
    {
        assert('is_string($eventName); // Invalid argument $eventName: string expected');
        return $this->getEventConfigurations()->offsetGet($eventName);
    }

    /**
     * Get list of event configurations.
     *
     * @return  \Yana\Plugins\Configs\MethodCollection
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
        assert('is_string($eventName); // Invalid argument $eventName: string expected');
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
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');
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
        assert('is_string($event); // Invalid argument $event: string expected');
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
     * @param   array              $plugins      list of plugin names
     * @param   \Yana\Application  $application  facade to bind plugins to
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     * @ignore
     */
    private function _loadPlugins(array $plugins, \Yana\Application $application)
    {
        foreach ($plugins as $name)
        {
            $this->_loadPlugin($name, $application);
        }
        $this->_isLoaded = true;
    }

    /**
     * Load a plugin.
     *
     * @param   string             $name         Must be valid identifier. Consists of chars, numbers and underscores.
     * @param   \Yana\Application  $application  facade to bind plugin to
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     * @ignore
     */
    private function _loadPlugin($name, \Yana\Application $application)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        if (!isset($this->_plugins[$name])) {
            $pluginDir = $this->getPluginDir();

            // load virtual drive, if it exists
            assert('!isset($driveFile); // Cannot redeclare var $driveFile');
            $driveFile = \Yana\Plugins\PluginNameMapper::toVDriveFilenameWithDirectory($name, $pluginDir);

            if (is_file($driveFile)) {
                $this->_drive[$name] = new \Yana\VDrive\Registry($driveFile, $this->getPluginDir()->getPath() . $name . "/");
                $this->_drive[$name]->read();
            }
            unset($driveFile);
            // load base class, if it exists
            try {
                $container = new \Yana\Plugins\Dependencies\PluginContainer($application, $this->getDependencies()->getSession());
                $this->_plugins[$name] =
                    \Yana\Plugins\AbstractPlugin::loadPlugin($name, $pluginDir, $container);

            } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                unset($e); // ignore plugins that are not found
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
        $report->addText("Plugin directory: " . $this->getPluginDir()->getPath());
        $methodsConfig = $this->getEventConfigurations();

        assert($methodsConfig instanceof \Yana\Plugins\Configs\MethodCollection);
        $methodsConfig->getReport($report);

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