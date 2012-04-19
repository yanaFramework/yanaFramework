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
 * @ignore
 */

namespace Yana\Db\Helpers\Triggers;

/**
 * <<collection>> This class is meant to be used to evaluate PHP-style row-level triggers.
 *
 * @package     yana
 * @subpackage  db
 */
class TriggerCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Initializes the collection.
     *
     * @param   \Yana\Db\Helpers\Triggers\IsTrigger[]  $items  list of triggers to execute
     */
    public function __construct(array $items = array())
    {
        foreach ($items as $key => $item)
        {
            $this->offsetSet($key, $item);
        }
    }

    /**
     * Insert or replace item.
     *
     * Examples of usage:
     * <code>
     * $collection[$offset] = $item;
     * $collection->_offsetSet($offset, $item);
     * </code>
     *
     * @param   scalar                               $key   offset
     * @param   \Yana\Db\Helpers\Triggers\IsTrigger  $item  constraint to add to the collection
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException 
     * @return  \Yana\Db\Helpers\Triggers\IsTrigger
     */
    public function offsetSet($key, $item)
    {
        if (!$item instanceof \Yana\Db\Helpers\Triggers\IsTrigger) {
            $message = "Item must be instance of \Yana\Db\Helpers\Triggers\IsTrigger.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message. \E_USER_ERROR);
        }
        return $this->_offsetSet($key, $item);
    }

    /**
     * Fire all triggers.
     *
     * This executes all triggers in the order in which they were inserted.
     */
    public function __invoke()
    {
        foreach ($this->toArray() as $trigger)
        {
            $trigger();
        }
    }

}

?>