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

namespace Yana\Plugins\Repositories;

/**
 * <<builder>> Plugin configuration repository builder.
 *
 * This class produces a configuration repository by scanning a directory.
 *
 * @package     yana
 * @subpackage  plugins
 */
class Builder extends \Yana\Plugins\Repositories\AbstractBuilder
{

    /**
     * List of found plugin directories.
     *
     * @var  array
     */
    private $_plugins = array();

    /**
     * Scan a directory for plugins.
     *
     * @param  \Yana\Files\IsDir  $directory  path to scan
     */
    public function addDirectory(\Yana\Files\IsDir $directory)
    {
        if ($directory->exists()) {
            foreach ($directory->listDirectories() as $plugin)
            {
                $classFile = \Yana\Plugins\PluginNameMapper::toClassFilenameWithDirectory($plugin, $directory);
                if (is_file($classFile)) {
                    $this->_plugins[$plugin] = \Yana\Plugins\PluginNameMapper::toClassNameWithNamespace($plugin);
                    include_once "$classFile";
                }
            }
        }
    }

    /**
     * Build new repository.
     */
    protected function buildRepository()
    {
        $overwrittenMethods = array();

        // initialize list for later use (see step 3)
        $pluginsWithDefaultMethods = array();

        // list of subscribing methods
        $subscribers = array();
        $configBuilder = new \Yana\Plugins\Configs\Builder();
        $configBuilder->attachLogger($this->getLogger());

        /**
         * 1) build plugin repository
         */
        assert(!isset($reflectionClass), 'Cannot redeclare var $reflectionClass');
        assert(!isset($className), 'Cannot redeclare var $className');
        assert(!isset($config), 'Cannot redeclare var $config');
        assert(!isset($id), 'Cannot redeclare var $id');
        foreach ($this->_plugins as $id => $className)
        {
            $configBuilder->createNewConfiguration();
            $configBuilder->setReflection(new \Yana\Plugins\Annotations\ReflectionClass($className));
            $pluginId = preg_replace('/^plugin_/', '', strtolower($className));
            /* @var $config \Yana\Plugins\Configs\ClassConfiguration */
            $config = $configBuilder->getPluginConfigurationClass();
            $config->setId($id);
            $this->object->addPlugin($config);

            // get name of parent plugin
            $parent = $config->getParent();

            /**
             * 2) build method repository
             */
            foreach ($config->getMethods() as $methodName => $method)
            {
                $methodName = mb_strtolower($methodName);
                /* @var $method \Yana\Plugins\Configs\IsMethodConfiguration */
                // skip default event handlers (will be handled in step 3)
                if ($methodName === 'catchall') {
                    $pluginsWithDefaultMethods[$id] = $config;
                } elseif ($methodName[0] === '_') {
                    continue;
                }

                $isOverwrite = $method->getOverwrite();
                $isSubscriber = $method->getSubscribe();

                // add method to index
                if ((!$this->object->isEvent($methodName) || $isOverwrite) && !$isSubscriber) {
                    $this->object->addEvent($method);
                } elseif ($isSubscriber) {
                    $subscribers[$methodName][] = $method; // will be used later
                }

                // overwrite method configuration of base plugin
                if ($isOverwrite && !empty($parent)) {
                    $overwrittenMethods[$methodName][$parent] = true;
                    $this->object->unsubscribe($method, $parent);
                }

                // add to implementations
                if (!isset($overwrittenMethods[$methodName][$pluginId])) {
                    $this->object->subscribe($method, $config);
                }
            } // end foreach method
            unset($isOverwrite, $isSubscriber, $methodName, $method);
        } // end foreach plugin
        unset($id, $parent, $configBuilder);

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
        assert(!isset($builder), 'Cannot redeclare var $builder');
        assert(!isset($application), 'Cannot redeclare var $application');
        $builder = new \Yana\ApplicationBuilder();
        $application = $builder->buildApplication();
        $mulitcastGroups = $application->getDefault("multicast_groups");
        unset($builder, $application);
        assert(is_array($mulitcastGroups), 'is_array($mulitcastGroups)');
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
        } // end if

        // load configuration settings for each method and build list of implementing classes
        assert(!isset($methodName), 'Cannot redeclare var $methodName');
        assert(!isset($methodConfig), 'Cannot redeclare var $methodConfig');
        foreach ($this->object->getEvents() as $methodName => $methodConfig)
        {
            // get type of current event
            $baseType = $methodConfig->getType();
            $baseGroup = $methodConfig->getGroup();

            // copy properties from subscribers
            if (!empty($subscribers[$methodName])) {
                assert(!isset($subscriberConfig), 'Cannot redeclare var $subscriberConfig');
                foreach ($subscribers[$methodName] as $subscriberConfig)
                {
                    $methodConfig->addSubscription($subscriberConfig);
                }
                unset($subscriberConfig);
            }

            assert(!isset($pluginName), 'Cannot redeclare var $pluginName');
            assert(!isset($pluginConfig), 'Cannot redeclare var $pluginConfig');
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

                $this->object->subscribe($methodConfig, $pluginConfig);
            }
            unset($pluginName, $pluginConfig);
        }
        unset($methodName, $config);
    }

}

?>