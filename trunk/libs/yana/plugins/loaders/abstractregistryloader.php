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
declare(strict_types=1);

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
     * Keys are plugin names, values are drive names.
     *
     * @var array
     */
    private $_pluginNameToDriveNameMap = array();

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
    protected function _getRegistries(): \Yana\VDrive\RegistryCollection
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
    protected function _getPluginDirectory(): \Yana\Files\IsDir
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
        assert(is_string($name), 'Wrong type for argument 1. String expected');

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
    public function getFileObjectFromRegistry(string $name): \Yana\Files\IsReadable
    {
        $collection = $this->_getRegistries();

        $resource = $collection[$name];
        if (!$resource instanceof \Yana\Files\IsReadable) {
            // recursive search
            $registryName = strpos($name, ':/') !== false ? substr($name, 0, (int) strpos($name, ':/')) : $name;
            if (!isset($collection[$registryName])) {
                $this->_cacheRegistryObject("", $this->loadRegistry($registryName)); // may throw Exception
            }
            /* @var $registry \Yana\VDrive\IsRegistry */
            $registry = $collection->offsetGet($registryName);
            $resource = $registry->getResource($name); // may throw NotFoundException
        }
        return $resource;
    }

    /**
     * Returns bool(true) if a plugin by this name is cached.
     *
     * @param   string  $pluginName  case sensitive
     * @return  bool
     */
    protected function _isPluginRegistryCached(string $pluginName): bool
    {
        return isset($this->_pluginNameToDriveNameMap[$pluginName]);
    }

    /**
     * Returns name of drive or NULL if it wasn't found.
     *
     * @param   string  $pluginName  to look up
     * @return  string|null
     */
    private function _getCachedDriveName(string $pluginName): ?string
    {
        return $this->_isPluginRegistryCached($pluginName) ? $this->_pluginNameToDriveNameMap[$pluginName] : null;
    }

    /**
     * Returns bool(true) if a plugin by this name is cached.
     *
     * @param   string  $pluginName  case sensitive
     * @return  \Yana\VDrive\IsRegistry
     * @throws  \Yana\Plugins\Loaders\RegistryNotFoundException  when no such registry was found
     */
    protected function _getCachedRegistryObject(string $pluginName): \Yana\VDrive\IsRegistry
    {
        $driveName = $this->_getCachedDriveName($pluginName);
        $registries = $this->_getRegistries();
        if (!$registries->offsetExists((string) $driveName)) {
            throw new \Yana\Plugins\Loaders\RegistryNotFoundException(
                "No such virtual drive: '$driveName' for plugin '$pluginName'", \Yana\Log\TypeEnumeration::WARNING);
        }
        return $registries->offsetGet($driveName);
    }

    /**
     * Store object in registry collection.
     *
     * @param   string                   $pluginName  case sensitive
     * @param   \Yana\VDrive\IsRegistry  $registry    object to store
     */
    protected function _cacheRegistryObject(string $pluginName, \Yana\VDrive\IsRegistry $registry)
    {
        $driveName = $registry->getDriveName();
        $registries = $this->_getRegistries();
        if (!$registries->offsetExists($driveName)) {
            $this->_pluginNameToDriveNameMap[$pluginName > "" ? $pluginName : $driveName] = $driveName;
            $registries->offsetSet($driveName, $registry);
        }
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
    public function loadRegistries(array $registries): \Yana\VDrive\RegistryCollection
    {
        $collection = $this->_getRegistries();

        foreach ($registries as $name)
        {
            // skip if the drive is already loaded
            if ($this->_isPluginRegistryCached($name)) {
                continue;
            }

            try {
                $registry = $this->loadRegistry($name);
                $this->_cacheRegistryObject($name, $registry);
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