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
 * <<abstract>> Plugin Configuration Repository.
 *
 * This class is meant to hold the configuration for various aspects of the stored plugins.
 *
 * @package     yana
 * @subpackage  plugins
 */
abstract class AbstractRepository extends \Yana\Core\StdObject implements \Yana\Plugins\Repositories\IsRepository//, \Serializable
{

    /**
     * Collection of {@see \Yana\Plugins\Configs\MethodConfiguration}s.
     *
     * @var  \Yana\Plugins\Configs\IsMethodCollection
     */
    private $_events = null;

    /**
     * Collection of {@see \Yana\Plugins\Configs\ClassConfiguration}s.
     *
     * @var  \Yana\Plugins\Configs\IsClassCollection
     */
    private $_plugins = null;

    /**
     * Priority list of methods.
     *
     * @var  \Yana\Plugins\Subscriptions\QueueCollection
     */
    private $_queues = null;

    /**
     * Initialize instance.
     */
    public function __construct()
    {
        $this->_setPlugins(new \Yana\Plugins\Configs\ClassCollection());
        $this->_setEvents(new \Yana\Plugins\Configs\MethodCollection());
    }

    /**
     * Set method collection.
     *
     * @param   \Yana\Plugins\Configs\IsMethodCollection  $events  collection
     * @return  $this
     */
    protected function _setEvents(\Yana\Plugins\Configs\IsMethodCollection $events)
    {
        $this->_events = $events;
        return $this;
    }

    /**
     * Set class collection.
     *
     * @param   \Yana\Plugins\Configs\IsClassCollection  $plugins  collection
     * @return  $this
     */
    protected function _setPlugins(\Yana\Plugins\Configs\IsClassCollection $plugins)
    {
        $this->_plugins = $plugins;
        return $this;
    }

    /**
     * Get list of plugin configurations.
     *
     * Returns a collection object that may be used as an array.
     *
     * @return  \Yana\Plugins\Configs\IsClassCollection
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }

    /**
     * Get list of method configurations.
     *
     * Returns a collection object that may be used as an array.
     *
     * @return  \Yana\Plugins\Configs\MethodCollection
     */
    public function getEvents()
    {
        return $this->_events;
    }

    /**
     * Returns queue collection.
     *
     * @return  \Yana\Plugins\Subscriptions\QueueCollection
     */
    protected function _getQueues()
    {
        if (!isset($this->_queues)) {
            $this->_queues = new \Yana\Plugins\Subscriptions\QueueCollection();
        }
        return $this->_queues;
    }

    /**
     * Returns queue corresponding to method name.
     *
     * Creates one if none exists.
     *
     * @param   string $methodName  name of the event to check for
     * @return  \Yana\Plugins\Subscriptions\IsQueue
     */
    protected function _getQueue($methodName)
    {
        $queues = $this->_getQueues();
        $id = mb_strtolower($methodName);
        if (!isset($queues[$id])) {
            $queues[$id] = new \Yana\Plugins\Subscriptions\Queue();
        }
        return $queues[$id];
    }

//    /**
//     * Returns a serialized string representation of this object.
//     *
//     * @return  string
//     */
//    public function serialize()
//    {
//        return \serialize(array($this->_events, $this->_plugins));
//    }
//
//    /**
//     * Unserialize instance.
//     *
//     * @param  string  $serialized  array containing properties
//     */
//    public function unserialize($serialized)
//    {
//        $array = \unserialize($serialized);
//        $this->_setEvents($array[0]);
//        $this->_setPlugins($array[1]);
//    }

}

?>