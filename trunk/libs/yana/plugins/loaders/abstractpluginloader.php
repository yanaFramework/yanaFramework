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

namespace Yana\Plugins\Loaders;

/**
 * <<abstract>> Transient plugin cache-wrapper.
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
abstract class AbstractPluginLoader extends \Yana\Core\StdObject implements \Serializable, \Yana\Plugins\Loaders\IsPluginLoader
{

    /**
     * plugin objects
     *
     * @var \Yana\Plugins\Collection
     */
    private $_plugins = null;

    /**
     * Array of Booleans, to keep track of what plugins have been loaded.
     *
     * @var array
     */
    private $_loadedPlugins = array();

    /**
     * Is true when the load() function has been called at least once (to allow lazy loading from the outside).
     *
     * @var bool
     */
    private $_isLoaded = false;

    /**
     * Path to base directory of plugins.
     *
     * @var \Yana\Files\IsDir
     */
    private $_pluginDirectory = null;

    /**
     * Required as dependency to build plugins.
     *
     * @var \Yana\Plugins\Dependencies\IsPluginContainer
     */
    private $_container = null;

    /**
     * <<constructor>> Initialize dependencies.
     *
     * @param  \Yana\Files\IsDir                             $pluginDirectory  will be passed on to plugin
     * @param  \Yana\Plugins\Dependencies\IsPluginContainer  $container        will be passed on to plugin
     */
    public function __construct(\Yana\Files\IsDir $pluginDirectory, \Yana\Plugins\Dependencies\IsPluginContainer $container)
    {
        $this->_pluginDirectory = $pluginDirectory;
        $this->_container = $container;
    }

    /**
     * Get collection of plugin instances that have been created.
     * 
     * @return  \Yana\Plugins\Collection
     */
    protected function _getPlugins()
    {
        if (!isset($this->_plugins)) {
            $this->_plugins = new \Yana\Plugins\Collection();
        }
        return $this->_plugins;
    }

    /**
     * 
     * @return \Yana\Files\IsDir
     */
    protected function _getPluginDirectory()
    {
        return $this->_pluginDirectory;
    }

    /**
     * @return  \Yana\Plugins\Dependencies\IsPluginContainer
     */
    protected function _getContainer()
    {
        return $this->_container;
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
     * Loads plugins from a list of names.
     *
     * @param   array  $plugins  list of plugin names
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     * @return  \Yana\Plugins\Collection
     */
    public function loadPlugins(array $plugins)
    {
        $this->_loadedPlugins = array();
        $collection = $this->_getPlugins();
        foreach ($plugins as $name)
        {
            // skip if the plugin is already loaded
            if (isset($collection[$name])) {
                continue;
            }
            // load base class, if it exists
            try {
                $collection[$name] = $this->loadPlugin($name);

            } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                unset($e); // ignore plugins that are not found
            }
            $this->_loadedPlugins[$name] = true;
        }
        $this->_isLoaded = true;
        return $collection;
    }

    /**
     * Unserializes the plugin directoy and container holding dependencies.
     *
     * @param  string  $serialized  array
     */
    public function unserialize($serialized)
    {
        $array = unserialize($serialized);
        $this->_pluginDirectory = $array[0];
        $this->_container = $array[1];
    }

    /**
     * Serializes array of plugin directory and container holding dependencies.
     *
     * @return  string
     */
    public function serialize()
    {
        return \serialize(array($this->_pluginDirectory, $this->_container));
    }

}

?>