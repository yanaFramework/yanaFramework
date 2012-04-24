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

namespace Yana\Io\Adapters;

/**
 * <<abstract>> Entity class.
 *
 * An entity is a <<subject>> persisted on demand by an observer.
 *
 * @package     yana
 * @subpackage  core
 */
abstract class AbstractEntity extends \Yana\Core\Object implements \Yana\Io\Adapters\IsEntity
{

    /**
     * List of observers used to persist the entity's state.
     *
     * @var  \SplObserver[]
     */
    private $_observers = array();

    /**
     * Adds an object to the list of observers.
     *
     * If the observer is already on the list, the function does nothing.
     *
     * @param  \SplObserver  $observer  add this object
     */
    public function attach(\SplObserver $observer)
    {
        if ($observer !== null && !\in_array($observer, $this->_observers)) {
            $this->_observers[] = $observer;
        }
    }

    /**
     * Removes an observer from the list.
     *
     * If the observer is not on the list, the function does nothing.
     *
     * @param  \SplObserver  $observer  remove this object from the list of observers
     */
    public function detach(\SplObserver $observer)
    {
        $key = \array_search($observer, $this->_observers, true);
        if ($key !== false) {
            unset($this->_observers[$key]);
        }
    }

    /**
     * Notifies all active observers to check the state of the entity.
     */
    public function notify()
    {
        foreach ($this->_observers as $observer)
        {
            assert('$observer instanceof \SplObserver');
            /* @var $observer \SplObserver */
            $observer->update($this);
        }
        unset($observer);
    }

}

?>