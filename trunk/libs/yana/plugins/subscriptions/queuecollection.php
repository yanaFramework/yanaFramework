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
class QueueCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Add a new connection to the collection.
     *
     * @param   scalar  $offset  method name
     * @param   \Yana\Plugins\Subscriptions\IsQueue  $value  connection that shoud be added
     * @return  \Yana\Plugins\Subscriptions\IsQueue
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not a mapper
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof \Yana\Plugins\Subscriptions\IsQueue) {
            $message = "Instance of \Yana\Plugins\Subscriptions\IsQueue expected.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        return $this->_offsetSet($offset, $value);
    }
}

?>