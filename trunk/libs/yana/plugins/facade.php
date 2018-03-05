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
 * <<Singleton>> <<Mediator>> Plugin facade.
 *
 * This class implements communication between plugins and provides access to virtual drives
 * and local registries which may be defined on a per plugin basis.
 *
 * {@internal
 * Note that this implements the Mediator pattern.
 *
 * This is not to be mixed with the Observer pattern:
 * The plugin does not inform the facade that it has changed it's state and
 * requests it to reflect that by changing the application state.
 *
 * Instead the facade recieves a new system event (function call) and broadcasts a
 * request to all  subscribing plugins to change their state accordingly and not vice versa.
 * }}
 *
 * Code example for "broadcasting" an event to all plugins (= calling a function):
 * <code>
 * $facade = \Yana\Plugins\Facade::getInstance();
 * try {
 *   $result = $facade->sendEvent('newState', $arguments);
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
 * @package     yana
 * @subpackage  plugins
 */
class Facade extends \Yana\Core\AbstractSingleton implements \Yana\Report\IsReportable, \Yana\Log\IsLogable
{

    use \Yana\Log\HasLogger;

    /**
     * @var \Yana\Files\IsDir
     */
    private static $_pluginDirectory = null;

    /**
     * definition of next event in queue
     *
     * @var \Yana\Plugins\Configs\EventRoute
     */
    private static $_nextEvent = null;

    /**
     * Event dispatching strategy.
     *
     * @var \Yana\Plugins\Events\IsDispatcher
     */
    private static $_dispatcher = null;

    /**
     * virtual drive
     *
     * @var \Yana\Plugins\Loaders\IsRegistryLoader
     */
    private $_registryLoader = null;

    /**
     * @var \Yana\Plugins\Repositories\IsRepository
     */
    private $_repository = null;

    /**
     * @var \Yana\Plugins\Dependencies\IsContainer
     */
    private $_dependencies = null;

    /**
     * @var \Yana\Plugins\Loaders\IsLoader
     */
    private $_pluginLoader = null;

    /**
     * Get dependency injection container.
     *
     * Defaults to NULL.
     *
     * @return  \Yana\Plugins\Dependencies\IsContainer
     */
    public function getDependencies()
    {
        return $this->_dependencies;
    }

    /**
     * Inject a dependency container.
     *
     * The container and its dependencies will be passed on to any plugins the facade loads.
     *
     * @param   \Yana\Plugins\Dependencies\IsContainer  $dependencies  to inject
     * @return  $this
     */
    public function attachDependencies(\Yana\Plugins\Dependencies\IsContainer $dependencies)
    {
        $this->_dependencies = $dependencies;
        return $this;
    }

    /**
     * Create instance of plugin loader.
     *
     * @param   \Yana\Application  $application  injected dependency
     * @return  \Yana\Plugins\Loaders\IsLoader
     */
    protected function _createPluginLoader(\Yana\Application $application)
    {
        $container = new \Yana\Plugins\Dependencies\PluginContainer($application, $this->getDependencies()->getSession());
        return new \Yana\Plugins\Loaders\PluginLoader($this->getPluginDirectory(), $container);
    }

    /**
     * Get instance of registry loader.
     *
     * @return  \Yana\Plugins\Loaders\IsRegistryLoader
     */
    protected function _getRegistryLoader()
    {
        if (!isset($this->_registryLoader)) {
            $this->_registryLoader = new \Yana\Plugins\Loaders\RegistryLoader($this->getPluginDirectory());
        }
        return $this->_registryLoader;
    }

    /**
     * Get instance of registry loader.
     *
     * @return  \Yana\Plugins\Events\Dispatcher
     */
    protected static function _getDispatcher()
    {
        if (!isset(self::$_dispatcher)) {
            self::$_dispatcher = new \Yana\Plugins\Events\Dispatcher();
        }
        return self::$_dispatcher;
    }

    /**
     * Set plugin source directory.
     *
     * The plugin directory is the place, where all plugins reside.
     * By default this is "plugins/".
     *
     * @param   \Yana\Files\IsDir  $pluginDirectory  path to plugin base directory
     * @throws  \Yana\Core\Exceptions\NotFoundException  when on of the given paths is invalid
     * @ignore
     */
    public static function setPluginDirectory(\Yana\Files\IsDir $pluginDirectory)
    {
        if (!$pluginDirectory->exists()) {
            $message = "No such directory: '" . $pluginDirectory->getPath() . "'.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\NotFoundException($message, $level);
        }

        self::$_pluginDirectory = $pluginDirectory;
    }

    /**
     * Get configuration repository.
     *
     * @return  \Yana\Plugins\Repositories\IsRepository
     */
    private function _getRepository()
    {
        if (empty($this->_repository)) {
            $this->_repository = $this->rebuildPluginRepository();
        }
        return $this->_repository;
    }

    /**
     * Get list of plugin configurations.
     *
     * Returns an associative array, where the keys are the plugin-names and the values are instances
     * of \Yana\Plugins\Configs\ClassConfiguration.
     *
     * @return  \Yana\Plugins\Configs\IsClassCollection
     */
    public function getPluginConfigurations()
    {
        return $this->_getRepository()->getPlugins();
    }

    /**
     * Broadcast an event to all plugins.
     *
     * This function looks up an event that you provide with the argument $event, and sends it to all
     * plugins that are in the event's group of recipients.
     *
     * Note: that "sending an event" actually means "calling a function that serves as an event handler".
     *
     * @param   string             $action       identifier of the requested action
     * @param   array              $args         list of arguments as associative array
     * @param   \Yana\Application  $application  facade
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\NotReadableException    when an existing VDrive definition is not readable
     * @throws  \Yana\Core\Exceptions\InvalidActionException  when the event is undefined
     * @throws  \Exception                                    plugins may throw arbitrary exceptions on failure
     */
    public function sendEvent($action, array $args, \Yana\Application $application)
    {
        assert('is_string($action); // Invalid argument $action: string expected');

        // event must be defined
        $config = $this->getEventConfiguration($action);
        if (!($config instanceof \Yana\Plugins\Configs\IsMethodConfiguration)) {
            $error = new \Yana\Core\Exceptions\InvalidActionException(); // if the event is not defined, we throw an exception
            $error->setAction($action);
            throw $error;
        }

        // In preparation of calling the plugin implementation, we store the list of arguments that are to be passed
        $config->setEventArguments($args);
        // we start by identifying the plugins that are subscribing to the event
        $listofAllPluginsSubcribedToEvent = $this->_getRepository()->getSubscribers($action);
        // remove those that are not active
        assert('!isset($listofPluginsSubcribedToEvent); // cannot redeclare variable $listofPluginsSubcribedToEvent');
        $listofPluginsSubcribedToEvent = array();
        assert('!isset($pluginName); // cannot redeclare variable $pluginName');
        foreach ($listofAllPluginsSubcribedToEvent as $pluginName)
        {
            if ($this->isActive($pluginName)) {
                $listofPluginsSubcribedToEvent[] = $pluginName;
            }
        }
        unset($pluginName);

        // before we load the plugins (and thus call a constructor) we need to load the configurations required by them
        $this->_getRegistryLoader()->loadRegistries($listofPluginsSubcribedToEvent);
        // so far we only know the names of the plugins - next we actually need to load their implementation, the plugin-loader lets us do that
        $this->_pluginLoader = $this->_createPluginLoader($application);
        // next, by using the plugin loader, we instantiate all plugins that have subscribed to this event
        assert('!isset($element); // cannot redeclare variable $element');
        $subscribers = $this->_pluginLoader->loadPlugins($listofPluginsSubcribedToEvent);
        // Finally we need to load a dispatch strategy to notify all subscribers of the event
        $dispatcher = self::_getDispatcher();
        // and finally we send the even to all subscribers
        $result = $dispatcher->sendEvent($subscribers, $config);
        // Done! Now we return the result of the call
        return $result;
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
        return self::_getDispatcher()->getLastResult();
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
        return self::_getDispatcher()->getLastEvent();
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
        return self::_getDispatcher()->getFirstEvent();
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
            $dispatcher = self::_getDispatcher();
            $event = $dispatcher->getFirstEvent();
            $result = $dispatcher->getLastResult();
            $methods = $this->getEventConfigurations();
            if ($methods->offsetExists($event)) {
                /* @var $method \Yana\Plugins\Configs\IsMethodConfiguration */
                $method = $methods[$event];
                self::$_nextEvent = $result !== false ? $method->getOnSuccess() : $method->getOnError();
            }
        }
        return self::$_nextEvent;
    }

    /**
     * Rescan plugin directory and refresh the plugin cache.
     *
     * Returns the built repository on success.
     *
     * @return  \Yana\Plugins\Repositories\IsRepository
     */
    public function rebuildPluginRepository()
    {
        $builder = new \Yana\Plugins\Repositories\Builder();
        $builder->addDirectory($this->getPluginDirectory());
        $builder->attachLogger($this->getLogger());
        $this->_repository = $builder->getRepository();
        return $this->_repository;
    }

    /**
     * Check if plugin is active.
     *
     * Returns bool(true) if the plugin identified by $pluginName exists
     * and is active and bool(false) otherwise.
     *
     * @param   string  $pluginName  identifier for the plugin
     * @return  bool
     */
    public function isActive($pluginName)
    {
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');

        $isActive = $this->isActiveByDefault($pluginName);
        if (!$isActive && !is_null($this->getDependencies())) {
            $adapter = $this->getDependencies()->getPluginAdapter();
            if ($adapter->offsetExists($pluginName)) {
                $plugin = $adapter->offsetGet($pluginName);
                assert($plugin instanceof \Yana\Plugins\Data\IsEntity);
                $isActive = $plugin->isActive();
            }
        }
        return $isActive;
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
     */
    public function isActiveByDefault($pluginName)
    {
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');
        return $this->_getRepository()->getPlugins()->isActiveByDefault($pluginName);
    }

    /**
     * Activate a plugin.
     *
     * @param   string  $pluginName  identifier for the plugin to be de-/activated
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no plugin with the given name is found
     * @return  $this
     */
    public function activate($pluginName)
    {
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');
        $this->_setActiveStatus($pluginName, true);
        return $this;
    }

    /**
     * Dectivate a plugin.
     *
     * @param   string  $pluginName  identifier for the plugin to be de-/activated
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no plugin with the given name is found
     * @return  $this
     */
    public function deactivate($pluginName)
    {
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');
        $this->_setActiveStatus($pluginName, false);
        return $this;
    }

    /**
     * Activate/deactivate plugin.
     *
     * @param  string  $pluginName  identifier for the plugin to be de-/activated
     * @param  bool    $isActive    new status of plugin (true = active, false = inactive)
     */
    private function _setActiveStatus($pluginName, $isActive)
    {
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');
        assert('is_bool($isActive); // Invalid argument $isActive: bool expected');

        $adapter = $this->getDependencies()->getPluginAdapter();
        if ($adapter->offsetExists($pluginName)) {
            $entity = $adapter->offsetGet($pluginName);
        } else {
            $entity = new \Yana\Plugins\Data\Entity();
            $entity->setId($pluginName);
        }
        $entity->setActive((bool) $isActive);
        $adapter->saveEntity($entity);
    }

    /**
     * Access the drive of a plugin by using it's name.
     *
     * @param   string  $name  name of plugin or resource
     * @return  \Yana\Files\IsReadable
     * @throws  \Yana\Core\Exceptions\UndefinedPropertyException  if no such resource exists
     */
    public function __get($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        return $this->_getRegistryLoader()->$name;
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
        $txt = "Plugin list is empty.\n";

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
        }
        return $txt;
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
    public function getPluginDirectory()
    {
        if (!isset(self::$_pluginDirectory)) {
            // @codeCoverageIgnoreStart
            self::$_pluginDirectory = new \Yana\Files\Dir("plugins/");
            // @codeCoverageIgnoreEnd
        }
        return self::$_pluginDirectory;
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
     */
    public function getEventType($eventName = null)
    {
        if (is_null($eventName)) {
            $eventName = self::getLastEvent();
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
     * If no such event exists, this function will return NULL instead.
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
        return $this->_getRepository()->getEvents();
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
        return $this->_getRepository()->isEvent($eventName);
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
        return $this->_pluginLoader instanceOf \Yana\Plugins\Loaders\IsLoader && $this->_pluginLoader->isLoaded($pluginName);
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
     * $facade = \Yana\Plugins\Facade::getInstance();
     * $report = $facade->getReport();
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
     * @ignore
     */
    public function getReport(\Yana\Report\IsReport $report = null)
    {
        if (is_null($report)) {
            $report = \Yana\Report\Xml::createReport(__CLASS__);
        }
        $report->addText("Plugin directory: " . $this->getPluginDirectory()->getPath());
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