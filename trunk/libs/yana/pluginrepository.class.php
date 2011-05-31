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

/**
 * Plugin Configuration Repository.
 *
 * This class is meant to hold the configuration for various aspects of the stored plugins.
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 */
class PluginRepository extends Object
{

    /**
     * Collection of {@see PluginConfigurationMethod}s.
     *
     * @access  private
     * @var     PluginMethodCollection
     */
    private $_methods = null;

    /**
     * Collection of {@see PluginConfigurationClass}es.
     *
     * @access  private
     * @var     PluginClassCollection
     */
    private $_plugins = null;

    /**
     * Priority list of methods.
     *
     * @access  private
     * @var     array
     */
    private $_implementations = array();

    /**
     * Initialize instance.
     */
    public function __construct()
    {
        $this->_plugins = new PluginClassCollection();
        $this->_methods = new PluginMethodCollection();
    }

    /**
     * Check if a plugin with the given name exists.
     *
     * @access  public
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
     * @access  public
     * @param   PluginConfigurationClass  $plugin  configuration to add
     * @return  PluginRepository
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
     * @access  public
     * @return  PluginClassCollection
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }

    /**
     * Check if a method with the given name exists.
     *
     * @access  public
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
     * @access  public
     * @param   PluginConfigurationMethod  $method  configuration to add
     * @return  PluginRepository
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
     * @access  public
     * @return  PluginMethodCollection
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
     * @access  public
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
     * @access  public
     * @param   PluginConfigurationClass  $class    implements the given method
     * @param   PluginConfigurationMethod $method   implemented by the given class
     * @return  PluginRepository 
     */
    public function setImplementation(PluginConfigurationMethod $method, PluginConfigurationClass $class)
    {
        $methodName = mb_strtolower($method->getMethodName());
        $this->_implementations[$methodName][$class->getId()] = $class->getPriority();
        return $this;
    }

    /**
     * Unregister an implementing class for a method.
     * 
     * @access  public
     * @param   PluginConfigurationMethod $method   remove the implementation of this function
     * @param   string                    $classId  plugin identifier
     * @return  PluginRepository 
     */
    public function unsetImplementation(PluginConfigurationMethod $method, $classId)
    {
        $methodName = mb_strtolower($method->getMethodName());
        if (isset($this->_implementations[$methodName][$classId])) {
            unset($this->_implementations[$methodName][$classId]);
        }
        return $this;
    }

}

?>