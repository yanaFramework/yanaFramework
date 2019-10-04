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
 * <<abstract>> Transient drive cache-wrapper.
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
abstract class AbstractRegistryLoader extends \Yana\Core\StdObject implements \Serializable, \Yana\Plugins\Loaders\IsRegistryLoader
{

    /**
     * Virtual drives.
     *
     * @var \Yana\VDrive\RegistryCollection
     */
    private $_registries = null;

    /**
     * Path to base directory of plugins.
     *
     * @var \Yana\Files\IsDir
     */
    private $_pluginDirectory = null;

    /**
     * <<constructor>> Initialize dependencies.
     *
     * @param  \Yana\Files\IsDir  $pluginDirectory  will be passed on to drive
     */
    public function __construct(\Yana\Files\IsDir $pluginDirectory)
    {
        $this->_pluginDirectory = $pluginDirectory;
    }

    /**
     * Get collection of the virtual drives that have been created.
     * 
     * @return  \Yana\VDrive\RegistryCollection
     */
    protected function _getRegistries()
    {
        if (!isset($this->_registries)) {
            $this->_registries = new \Yana\VDrive\RegistryCollection();
        }
        return $this->_registries;
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
     * Access the drive of a plugin by using it's name.
     *
     * @param   string  $name  name of plugin
     * @return  \Yana\Files\IsReadable
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no such file is defined
     */
    public function __get($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');

        return $this->getFileObjectFromRegistry($name);
    }

    /**
     * Access the drive of a plugin by using it's name.
     *
     * @param   string  $name  name of plugin
     * @return  \Yana\Files\IsReadable
     * @throws  \Yana\Core\Exceptions\NotFoundException     when no such file is defined
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing virtual drive definition is not readable
     */
    public function getFileObjectFromRegistry($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');

        $collection = $this->_getRegistries();

        $resource = $collection[$name];
        if (!$resource instanceof \Yana\Files\IsReadable) {
            // recursive search
            $registryName = substr($name, 0, strpos($name, ':/'));
            if (!isset($collection[$registryName])) {
                $this->_cacheRegistryObject($this->loadRegistry($registryName)); // may throw Exception
            }
            /* @var $registry \Yana\VDrive\IsRegistry */
            $registry = $collection->offsetGet($registryName);
            $resource = $registry->getResource($name); // may throw NotFoundException
        }
        return $resource;
    }

    /**
     * Store object in registry collection.
     *
     * @param \Yana\VDrive\IsRegistry $registry
     */
    private function _cacheRegistryObject(\Yana\VDrive\IsRegistry $registry)
    {
        $this->_getRegistries()->offsetSet($registry->getDriveName(), $registry);
    }

    /**
     * Loads registry definitions from a list of names.
     *
     * Loaded instances are cached. Therefore calling this function twice returns the same instances.
     *
     * @param   array  $registries  list of registry identifiers
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     * @return  \Yana\VDrive\RegistryCollection
     */
    public function loadRegistries(array $registries)
    {
        $collection = $this->_getRegistries();

        foreach ($registries as $name)
        {
            // skip if the drive is already loaded
            if (isset($collection[$name])) {
                continue;
            }

            try {
                $registry = $this->loadRegistry($name);
                $this->_cacheRegistryObject($registry);
            } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                // skip file
            }
        }
        return $collection;
    }

    /**
     * Unserializes the plugin directoy and container holding dependencies.
     *
     * @param  string  $serialized  array
     */
    public function unserialize($serialized)
    {
        $this->_pluginDirectory = unserialize($serialized);
    }

    /**
     * Serializes array of plugin directory and container holding dependencies.
     *
     * @return  string
     */
    public function serialize()
    {
        return \serialize($this->_pluginDirectory);
    }

}

?>