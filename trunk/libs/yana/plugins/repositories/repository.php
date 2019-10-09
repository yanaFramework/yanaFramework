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
 * Plugin Configuration Repository.
 *
 * This class is meant to hold the configuration for various aspects of the stored plugins.
 *
 * @package     yana
 * @subpackage  plugins
 */
class Repository extends \Yana\Plugins\Repositories\AbstractRepository
{

    /**
     * Check if a plugin with the given name exists.
     *
     * @param   string  $plugin  identifier
     * @return  bool
     */
    public function isPlugin($plugin)
    {
        return $this->getPlugins()->offsetExists($plugin);
    }

    /**
     * Add plugin configuration.
     *
     * @param   \Yana\Plugins\Configs\ClassConfiguration  $plugin  configuration to add
     * @return  $this
     */
    public function addPlugin(\Yana\Plugins\Configs\ClassConfiguration $plugin)
    {
        $this->getPlugins()->offsetSet(null, $plugin);
        return $this;
    }

    /**
     * Check if a method with the given name exists.
     *
     * @param   string  $method  identifier
     * @return  bool
     */
    public function isEvent($method)
    {
        return $this->getEvents()->offsetExists($method);
    }

    /**
     * Add plugin-method configuration.
     *
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration  $method  configuration to add
     * @return  $this
     */
    public function addEvent(\Yana\Plugins\Configs\IsMethodConfiguration $method)
    {
        $this->getEvents()->offsetSet(null, $method);
        return $this;
    }

    /**
     * Get list of plugin priorities for a method name.
     *
     * If the event is not registered, the function returns an empty array.
     * Otherwise it returns a list of plugin IDs sorted by priority.
     *
     * @param   string $methodName  name of the event to check for
     * @return  array
     */
    public function getSubscribers($methodName)
    {
        assert(is_string($methodName), 'Invalid argument $methodName: string expected');
        return $this->_getQueue($methodName)->getSubscribers();
    }

    /**
     * Register that the given class implements the given method.
     * 
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration $event       implemented by the given class
     * @param   \Yana\Plugins\Configs\IsClassConfiguration  $subscriber  implements the given method
     * @return  $this
     */
    public function subscribe(\Yana\Plugins\Configs\IsMethodConfiguration $event, \Yana\Plugins\Configs\IsClassConfiguration $subscriber)
    {
        $queue = $this->_getQueue($event->getMethodName());
        $queue->subscribe($subscriber);
        return $this;
    }

    /**
     * Unregister an implementing class for a method.
     * 
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration  $event         remove the implementation of this function
     * @param   string                                       $subscriberId  plugin identifier
     * @return  $this
     */
    public function unsubscribe(\Yana\Plugins\Configs\IsMethodConfiguration $event, $subscriberId)
    {
        $queue = $this->_getQueue($event->getMethodName());
        $queue->unsubscribe($subscriberId);
        return $this;
    }

}

?>