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
 * <<builder>> Plugin configuration repository builder.
 *
 * This class produces a configuration repository by scanning a directory.
 *
 * @package     yana
 * @subpackage  plugins
 */
class RepositoryBuilder extends \Yana\Plugins\AbstractRepositoryBuilder
{

    /**
     * List of found plugin directories.
     *
     * @var  array
     */
    private $_plugins = array();

    /**
     * Holds old repository for comparison.
     *
     * @var  array
     */
    private $_oldRepository = null;

    /**
     * Resets the instance that is currently build.
     */
    public function createNewRepository()
    {
        $this->_oldRepository = new \Yana\Plugins\Repository();
        parent::createNewRepository();
    }

    /**
     * Scan a directory for plugins.
     *
     * @param  string  $directory  path to scan
     */
    public function addDirectory($directory)
    {
        if (is_dir($directory)) {
            foreach (scandir($directory) as $plugin)
            {
                if ($plugin[0] !== '.' && is_dir($directory . '/' . $plugin)) {
                    $classFile = $directory . $plugin . "/" . $plugin . ".plugin.php";
                    if (is_file($classFile)) {
                        $this->_plugins[$plugin] = \Yana\Plugins\Manager::PREFIX . $plugin;
                        include_once "$classFile";
                    }
                }
            }
        }
    }

    /**
     * Set base repository to compare the plugin list with.
     *
     * @param  \Yana\Plugins\Repository  $repository  old repository for comparison
     */
    public function setBaseRepository(\Yana\Plugins\Repository $repository)
    {
        $this->_oldRepository = $repository;
    }

    /**
     * Build new repository.
     */
    protected function buildRepository()
    {
        $oldPlugins = $this->_oldRepository->getPlugins();
        $overwrittenMethods = array();

        // initialize list for later use (see step 3)
        $pluginsWithDefaultMethods = array();

        // clear cache
        \Yana::getInstance()->clearCache();

        // list of subscribing methods
        $subscribers = array();
        $builder = new \Yana\Plugins\Configs\Builder();

        /**
         * 1) build plugin repository
         */
        assert('!isset($reflectionClass); // Cannot redeclare var $reflectionClass');
        assert('!isset($className); // Cannot redeclare var $className');
        assert('!isset($config); // Cannot redeclare var $config');
        assert('!isset($id); // Cannot redeclare var $id');
        foreach ($this->_plugins as $id => $className)
        {
            $builder->createNewConfiguration();
            $builder->setReflection(new \Yana\Plugins\ReflectionClass($className));
            $pluginId = preg_replace('/^plugin_/', '', strtolower($className));
            $config = $builder->getPluginConfigurationClass();
            $config->setId($id);
            $this->object->addPlugin($config);

            // get name of parent plugin
            $parent = $config->getParent();

            /**
             * get active preset
             *
             * if the plugin's active state is unknown and there is a default state defined by the plugin,
             * use the setting defined by the plugin.
             */
            if ($oldPlugins->offsetExists($id)) {
                // copy settings from old plugin repository
                $config->setActive($oldPlugins->offsetGet($id)->getActive());
            }
            // ignore methods if plugin is not active
            if ($config->getActive() === \Yana\Plugins\ActivityEnumeration::INACTIVE) {
                continue;
            }
            /**
             * 2) build method repository
             */
            foreach ($config->getMethods() as $methodName => $method)
            {
                /* @var $method \Yana\Plugins\Configs\MethodConfiguration */
                // skip default event handlers (will be handled in step 3)
                if ($methodName == 'catchAll') {
                    $pluginsWithDefaultMethods[$id] = $config;
                    continue;
                }
                $methodName = mb_strtolower($methodName);

                $isOverwrite = $method->getOverwrite();
                $isSubscriber = $method->getSubscribe();

                // add method to index
                if ((!$this->object->isMethod($methodName) || $isOverwrite) && !$isSubscriber) {
                    $this->object->addMethod($method);
                } elseif ($isSubscriber) {
                    $subscribers[$methodName][] = $method; // will be used later
                }

                // overwrite method configuration of base plugin
                if ($isOverwrite && !empty($parent)) {
                    $overwrittenMethods[$methodName][$parent] = true;
                    $this->object->unsetImplementation($method, $parent);
                }

                // add to implementations
                if (!isset($overwrittenMethods[$methodName][$pluginId])) {
                    $this->object->setImplementation($method, $config);
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
        $mulitcastGroups = \Yana::getDefault("multicast_groups");
        assert('is_array($mulitcastGroups);');
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
        assert('!isset($methodName); // Cannot redeclare var $methodName');
        assert('!isset($methodConfig); // Cannot redeclare var $methodConfig');
        foreach ($this->object->getMethods() as $methodName => $methodConfig)
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

                $this->object->setImplementation($methodConfig, $pluginConfig);
            }
            unset($pluginName, $pluginConfig);
        }
        unset($methodName, $config);
    }

}

?>