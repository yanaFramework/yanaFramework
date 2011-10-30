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

namespace Yana\Core;

/**
 * Collection base class.
 *
 * @package     yana
 * @subpackage  core
 */
abstract class AbstractCollection extends \Yana\Core\Object implements \Yana\Core\IsCollection
{

    /**
     * list of items to work on
     *
     * @var     array
     */
    private $_items = array();

    /**
     * Set a list of items
     *
     * @param   array $items  list of items to work on
     */
    public function setItems(array $items = array())
    {
        $this->_items = array();
        foreach ($items as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * Get current item.
     *
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\OutOfBoundsException  if the iterator is out of bounds
     */
    public function current()
    {
        if ($this->valid()) {
            return current($this->_items);
        } else {
            throw new \Yana\Core\Exceptions\OutOfBoundsException("Iterator index out of bounds");
        }
    }

    /**
     * Increment iterator to next item.
     */
    public function next()
    {
        next($this->_items);
    }

    /**
     * Get field key.
     *
     * @return  string
     */
    public function key()
    {
        return key($this->_items);
    }

    /**
     * Check if iterator position is valid.
     *
     * @return  bool
     */
    public function valid()
    {
        return !is_null(key($this->_items));
    }

    /**
     * Rewind iterator.
     */
    public function rewind()
    {
        reset($this->_items);
    }

    /**
     * Return the number of items in the collection.
     *
     * If the collection is empty, it returns 0.
     *
     * @return  int
     */
    public function count()
    {
        return count($this->_items);
    }

    /**
     * Get item list.
     *
     * @return  array
     */
    public function toArray()
    {
        return $this->_items;
    }

    /**
     * Check if item exists.
     *
     * Example:
     * <code>
     * $bool = isset($collection[$offset]);
     * $bool = $collection->offsetExists($offset);
     * </code>
     *
     * @param   scalar  $offset  index of item to test
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return isset($this->_items[$offset]);
    }

    /**
     * Return item at offset.
     *
     * Example:
     * <code>
     * $item = $collection[$offset];
     * $item = $collection->offsetGet($offset);
     * </code>
     *
     * @param   scalar  $offset  index of item to retrieve
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        if (isset($this->_items[$offset])) {
            return $this->_items[$offset];
        } else {
            return null;
        }
    }

    /**
     * Insert or replace item.
     *
     * Implement this function in your sub-class as follows:
     * <code>
     * if ($yourTypeCheckHere) {
     *     $this->_offsetSet($offset, $item);
     * } else {
     *     throw new \Yana\Core\Exceptions\InvalidArgumentException();
     * }
     * </code>
     * 
     *
     * Example:
     * <code>
     * $collection[$offset] = $item;
     * parent::_offsetSet($offset, $item);
     * </code>
     *
     * @param   scalar  $offset  index of item to replace
     * @param   mixed   $value   new value of item
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid collection item
     */
    protected function _offsetSet($offset, $value)
    {
        $this->_items[$offset] = $value;
    }

    /**
     * Remove item from collection.
     *
     * Does nothing if the item does not exist.
     *
     * Example:
     * <code>
     * unset($collection[$offset]);
     * $collection->offsetUnset($offset);
     * </code>
     *
     * @param   scalar  $offset  index of item to remove
     */
    public function offsetUnset($offset)
    {
        if (isset($this->_items[$offset])) {
            unset($this->_items[$offset]);
        }
    }

}

?>