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
abstract class AbstractRegistryLoader extends \Yana\Core\Object implements \Serializable, \Yana\Plugins\Loaders\IsRegistryLoader
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
     * @throws  \Yana\Core\Exceptions\UndefinedPropertyException  when no such file is defined
     */
    public function __get($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');

        $collection = $this->_getRegistries();

        $resource = $collection[$name];
        if (!$resource instanceof \Yana\Files\IsReadable) {
            // recursive search
            $drive = substr($name, 0, strpos($name, ':/'));
            if (!isset($collection[$drive]) || !isset($collection[$drive]->$name)) {
                $resource = parent::__get($name); // throws exception
            }
            $resource = $collection[$drive]->$name;
        }
        return $resource;
    }

    /**
     * Loads registry definitions from a list of names.
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
                $collection[$name] = $this->loadDrive($name);
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