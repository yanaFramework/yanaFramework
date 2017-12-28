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
 * <<interface>> Plugin Configuration Repository.
 *
 * @package     yana
 * @subpackage  plugins
 */
interface IsRepository
{

    /**
     * Check if a plugin with the given name exists.
     *
     * @param   string  $plugin  identifier
     * @return  bool
     */
    public function isPlugin($plugin);

    /**
     * Add plugin configuration.
     *
     * @param   \Yana\Plugins\Configs\ClassConfiguration  $plugin  configuration to add
     * @return  $this
     */
    public function addPlugin(\Yana\Plugins\Configs\ClassConfiguration $plugin);

    /**
     * Get list of plugin configurations.
     *
     * Returns a collection object that may be used as an array.
     *
     * @return  \Yana\Plugins\Configs\IsClassCollection
     */
    public function getPlugins();

    /**
     * Check if a method with the given name exists.
     *
     * @param   string  $method  identifier
     * @return  bool
     */
    public function isEvent($method);

    /**
     * Add plugin-method configuration.
     *
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration  $method  configuration to add
     * @return  $this
     */
    public function addEvent(\Yana\Plugins\Configs\IsMethodConfiguration $method);

    /**
     * Get list of method configurations.
     *
     * Returns a collection object that may be used as an array.
     *
     * @return  \Yana\Plugins\Configs\MethodCollection
     */
    public function getEvents();

    /**
     * Get list of plugin priorities for a method name.
     *
     * If the event is not registered, the function returns NULL.
     * Otherwise it returns a list of items of {@see PluginPriorityEnumeration}.
     *
     * @param   string $methodName  name of the event to check for
     * @return  array
     */
    public function getImplementations($methodName);

    /**
     * Register that the given class implements the given method.
     * 
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration $method   implemented by the given class
     * @param   \Yana\Plugins\Configs\IsClassConfiguration  $class    implements the given method
     * @return  $this
     */
    public function setImplementation(\Yana\Plugins\Configs\IsMethodConfiguration $method, \Yana\Plugins\Configs\IsClassConfiguration $class);

    /**
     * Unregister an implementing class for a method.
     * 
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration $method   remove the implementation of this function
     * @param   string                                      $classId  plugin identifier
     * @return  $this
     */
    public function unsetImplementation(\Yana\Plugins\Configs\IsMethodConfiguration $method, $classId);

}

?>