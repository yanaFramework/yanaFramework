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

use Yana\Plugins\Configs;

/**
 * Plugin Configuration Repository.
 *
 * This class is meant to hold the configuration for various aspects of the stored plugins.
 *
 * @package     yana
 * @subpackage  plugins
 */
class Repository extends \Yana\Core\Object
{

    /**
     * Collection of {@see \Yana\Plugins\Configs\MethodConfiguration}s.
     *
     * @var  \Yana\Plugins\Configs\MethodCollection
     */
    private $_methods = null;

    /**
     * Collection of {@see \Yana\Plugins\Configs\ClassConfiguration}s.
     *
     * @var  \Yana\Plugins\Configs\ClassCollection
     */
    private $_plugins = null;

    /**
     * Priority list of methods.
     *
     * @var  array
     */
    private $_implementations = array();

    /**
     * Initialize instance.
     */
    public function __construct()
    {
        $this->_plugins = new \Yana\Plugins\Configs\ClassCollection();
        $this->_methods = new \Yana\Plugins\Configs\MethodCollection();
    }

    /**
     * Check if a plugin with the given name exists.
     *
     * @param   string  $plugin  identifier
     * @return  bool
     */
    public function isPlugin($plugin)
    {
        return $this->_plugins->offsetExists($plugin);
    }

    /**
     * Add plugin configuration.
     *
     * @param   \Yana\Plugins\Configs\ClassConfiguration  $plugin  configuration to add
     * @return  \Yana\Plugins\Repositories\Repository
     */
    public function addPlugin($plugin)
    {
        $this->_plugins[] = $plugin;
        return $this;
    }

    /**
     * Get list of plugin configurations.
     *
     * Returns a collection object that may be used as an array.
     *
     * @return  \Yana\Plugins\Configs\ClassCollection
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }

    /**
     * Check if a method with the given name exists.
     *
     * @param   string  $method  identifier
     * @return  bool
     */
    public function isMethod($method)
    {
        return $this->_methods->offsetExists($method);
    }

    /**
     * Add plugin-method configuration.
     *
     * @param   \Yana\Plugins\Configs\MethodConfiguration  $method  configuration to add
     * @return  \Yana\Plugins\Repositories\Repository
     */
    public function addMethod($method)
    {
        $this->_methods[] = $method;
        return $this;
    }

    /**
     * Get list of method configurations.
     *
     * Returns a collection object that may be used as an array.
     *
     * @return  \Yana\Plugins\Configs\MethodCollection
     */
    public function getMethods()
    {
        return $this->_methods;
    }

    /**
     * Get list of plugin priorities for a method name.
     *
     * If the event is not registered, the function returns NULL.
     * Otherwise it returns a list of items of {@see PluginPriorityEnumeration}.
     *
     * @param   string $methodName  name of the event to check for
     * @return  array
     */
    public function getImplementations($methodName)
    {
        assert('is_string($methodName); // Invalid argument $methodName: string expected');
        return (isset($this->_implementations[$methodName])) ? $this->_implementations[$methodName] : array();
    }

    /**
     * Register that the given class implements the given method.
     * 
     * @param   \Yana\Plugins\Configs\ClassConfiguration  $class    implements the given method
     * @param   \Yana\Plugins\Configs\MethodConfiguration $method   implemented by the given class
     * @return  \Yana\Plugins\Repositories\Repository 
     */
    public function setImplementation(Configs\MethodConfiguration $method, Configs\ClassConfiguration $class)
    {
        $methodName = mb_strtolower($method->getMethodName());
        $this->_implementations[$methodName][$class->getId()] = $class->getPriority();
        return $this;
    }

    /**
     * Unregister an implementing class for a method.
     * 
     * @param   \Yana\Plugins\Configs\MethodConfiguration $method   remove the implementation of this function
     * @param   string                    $classId  plugin identifier
     * @return  \Yana\Plugins\Repositories\Repository 
     */
    public function unsetImplementation(Configs\MethodConfiguration $method, $classId)
    {
        $methodName = mb_strtolower($method->getMethodName());
        if (isset($this->_implementations[$methodName][$classId])) {
            unset($this->_implementations[$methodName][$classId]);
        }
        return $this;
    }

}

?>