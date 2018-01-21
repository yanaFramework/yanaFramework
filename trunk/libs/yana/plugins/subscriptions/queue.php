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

namespace Yana\Plugins\Subscriptions;

/**
 * Standard priority queue.
 *
 * Sorted by priority attribute of subscribers.
 *
 * @package     yana
 * @subpackage  plugins
 */
class Queue extends \Yana\Core\Object implements \Yana\Plugins\Subscriptions\IsQueue
{

    /**
     * Priority list of methods.
     *
     * @var  array
     */
    private $_subscribers = array();

    /**
     * Get list of plugin priorities for a method name.
     *
     * If the event is not registered, the function returns NULL.
     * Otherwise it returns a list of items of {@see PluginPriorityEnumeration}.
     *
     * @return  array
     */
    public function getSubscribers()
    {
        return array_keys($this->_subscribers);
    }

    /**
     * Register that the given class implements the given method.
     * 
     * @param   \Yana\Plugins\Configs\IsClassConfiguration  $class  implements the given event as a method
     * @return  $this
     */
    public function subscribe(\Yana\Plugins\Configs\IsClassConfiguration $class)
    {
        $this->_subscribers[$class->getId()] = $class->getPriority();
        arsort($this->_subscribers);
        return $this;
    }

    /**
     * Unregister an subscribing class.
     * 
     * @param   string  $classId  identifies the plugin
     * @return  $this
     */
    public function unsubscribe($classId)
    {
        assert('is_string($classId); // Invalid argument $classId: string expected');
        if (isset($this->_subscribers[$classId])) {
            unset($this->_subscribers[$classId]);
        }
        return $this;
    }

}

?>