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

namespace Yana\Plugins\Events;

/**
 * Implements strategy to dispatch events.
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class Dispatcher extends \Yana\Core\StdObject implements \Serializable, \Yana\Plugins\Events\IsDispatcher
{

    /**
     * result of last handled action
     *
     * @var bool
     */
    private $_lastResult = null;

    /**
     * name of currently handled event
     *
     * @var string
     */
    private $_lastEvent = "";

    /**
     * name of initially handled event
     *
     * @var string
     */
    private $_firstEvent = "";

    /**
     * Notify subscribing plugins of event.
     *
     * Here, to "send an event" actually means "calling a function that serves as an event handler".
     * The function will keep track of events called and the generated results.
     *
     * Note that subscribing plugins may throw exceptions of their own.
     *
     * @param   \Yana\Plugins\Collection                     $subscribers  list of plugins that should be called
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration  $event        identifier of the occured event
     * @return  mixed
     * @throws  \Exception  plugins may throw arbitrary exceptions on failure
     */
    public function sendEvent(\Yana\Plugins\Collection $subscribers, \Yana\Plugins\Configs\IsMethodConfiguration $event)
    {
        // If this is the original, first event in this call, we chalk this up for reference
        if (empty($this->_firstEvent)) {
            $this->_firstEvent = $event->getMethodName();
        }
        // Either way, we make sure that we keep track of any ongoing event.
        $this->_lastEvent = $event->getMethodName();
        // By default we consider all events to have been executed successfully unless told otherwise
        $this->_lastResult = true;

        // next we find and instantiate all plugins that have subscribed to this event and proceed to call them one by one.
        assert(!isset($element), 'cannot redeclare variable $element');
        foreach ($subscribers as $element)
        {
            $lastResult = $this->_sendEvent($element, $event); // call the subscribing plugin
            // finally we check what the outcome of this call was - if it returned FALSE (=failure), we abort
            if ($lastResult === false) {
                $this->_lastResult = false;
                break;
            }
            // we will not cache results if the plugin does not actually implement the event (and thus listens only via catchAll())
            if ($event->hasMethod($element)) { // Returns TRUE if $elements implements the method represented by $event
                $this->_lastResult = $lastResult;
            }
        }
        unset($element);

        // and here we go: all done - and the result of the event evaluation is ready to be returned to the callee.
        return $this->_lastResult;
    }

    /**
     * Send event to target.
     *
     * @param   \Yana\IsPlugin                               $subscriber  implements event handler
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration  $event       describes the call interface of the event
     * @return  mixed
     * @codeCoverageIgnore
     */
    protected function _sendEvent(\Yana\IsPlugin $subscriber, \Yana\Plugins\Configs\IsMethodConfiguration $event)
    {
        return $event->sendEvent($subscriber);
    }

    /**
     * Get result of last event handler.
     *
     * Returns the result of the last successfully handled event.
     * Returns bool(false) if there was an error.
     * Returns NULL if no event was handled yet.
     *
     * @return  mixed
     */
    public function getLastResult()
    {
        return $this->_lastResult;
    }

    /**
     * Get the previously handled event.
     *
     * Returns the name of the current or previously handled event.
     * If there has been no previous event, the function will return an empty string.
     *
     * @return  string
     */
    public function getLastEvent()
    {
        return $this->_lastEvent;
    }

    /**
     * Get the initially handled event.
     *
     * Returns the name of the currently handled event.
     * If there has been no previous event, the function will return an empty string.
     *
     * @return  string
     */
    public function getFirstEvent()
    {
        return $this->_firstEvent;
    }

    /**
     * Unserializes the plugin directoy and container holding dependencies.
     *
     * @param  string  $serialized  array
     */
    public function unserialize($serialized)
    {
        unset($serialized); // intentionally blank
    }

    /**
     * Serializes array of plugin directory and container holding dependencies.
     *
     * @return  string
     */
    public function serialize()
    {
        return ""; // intentionally blank
    }

}

?>