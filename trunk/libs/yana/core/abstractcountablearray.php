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
declare(strict_types=1);

namespace Yana\Core;

/**
 * Collection base class.
 *
 * @package     yana
 * @subpackage  core
 */
abstract class AbstractCountableArray extends \Yana\Core\StdObject implements \Yana\Core\IsCountableArray
{

    /**
     * list of items to work on
     *
     * @var     array
     */
    private $_items = array();

    /**
     * Get item list.
     *
     * @return  array
     */
    protected function _getItems()
    {
        return $this->_items;
    }

    /**
     * Set item list.
     *
     * @param  array  $items  items to place in array
     */
    protected function _setItems(array $items)
    {
        $this->_items = $items;
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
        return count($this->_getItems());
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
        assert('is_scalar($offset); // $offset expected to be Scalar');
        $items = $this->_getItems();
        return isset($items[$offset]);
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
        assert('is_scalar($offset); // $offset expected to be Scalar');
        $items = $this->_getItems();
        if (isset($items[$offset])) {
            return $items[$offset];
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
     * @return  mixed
     */
    public function offsetSet($offset, $value)
    {
        assert('is_null($offset) || is_scalar($offset); // $offset expected to be Scalar');
        $items = $this->_getItems();
        if (!is_null($offset)) {
            $items[$offset] = $value;
        } else {
            $items[] = $value;
        }
        $this->_setItems($items);
        return $value;
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
     * @param  scalar  $offset  index of item to remove
     */
    public function offsetUnset($offset)
    {
        assert('is_scalar($offset); // $offset expected to be Scalar');
        $items = $this->_getItems();
        if (isset($items[$offset])) {
            unset($items[$offset]);
        }
        $this->_setItems($items);
    }

}

?>