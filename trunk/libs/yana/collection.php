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
 * Collection base class.
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 */
class Collection extends \Yana\Core\Object implements Iterator, Countable, ArrayAccess
{

    /**
     * list of items to work on
     *
     * @access  private
     * @var     array
     */
    private $_items = array();

    /**
     * Set a list of items
     *
     * @access  public
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
     * @access  public
     * @return  mixed
     * @throws  OutOfBoundsException  if the iterator is out of bounds
     */
    public function current()
    {
        if ($this->valid()) {
            return current($this->_items);
        } else {
            throw new OutOfBoundsException("Iterator index out of bounds");
        }
    }

    /**
     * Increment iterator to next item.
     *
     * @access  public
     */
    public function next()
    {
        next($this->_items);
    }

    /**
     * get field key
     *
     * @access  public
     * @return  string
     */
    public function key()
    {
        return key($this->_items);
    }

    /**
     * Check if iterator position is valid.
     *
     * @access  public
     * @return  bool
     */
    public function valid()
    {
        return !is_null(key($this->_items));
    }

    /**
     * Rewind iterator.
     *
     * @access  public
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
     * @access  public
     * @return  int
     */
    public function count()
    {
        return count($this->_items);
    }

    /**
     * Get item list.
     *
     * @access  public
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
     * @access  public
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
     * @access  public
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
     * Example:
     * <code>
     * $collection[$offset] = $item;
     * $collection->offsetSet($offset, $item);
     * </code>
     *
     * @access  public
     * @param   scalar  $offset  index of item to replace
     * @param   mixed   $value   new value of item
     */
    public function offsetSet($offset, $value)
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
     * @access  public
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