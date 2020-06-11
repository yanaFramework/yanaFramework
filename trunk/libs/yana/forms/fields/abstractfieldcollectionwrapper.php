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

namespace Yana\Forms\Fields;

/**
 * <<abstract>> A context-sensitive form wrapper.
 *
 * This class is meant to provide a context-aware form object by binding a form to
 * its current context and identifying the fields that apply to it.
 *
 * Note: this implements the Iterator interface, since it extends a collection,
 * and all collections in this framework MUST implement this interface.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
abstract class AbstractFieldCollectionWrapper extends \Yana\Core\StdObject implements \Yana\Forms\Fields\IsFieldCollectionWrapper
{

    /**
     * Form structure and setup.
     *
     * @var  \Yana\Forms\Facade
     */
    private $_form = null;

    /**
     * Form context to take the field list from.
     *
     * @var  \Yana\Forms\Setups\IsContext
     */
    private $_context = null;

    /**
     * Collection of field facades.
     *
     * @var  \Yana\Forms\Fields\FieldCollection
     */
    private $_collection = null;

    /**
     * Initialize a field collection from a given context.
     *
     * Note that this will automatically create a field list based on the table associated with the form,
     * and save it as new context collection items.
     *
     * So it DOES modify the given context. This will initialize the context and is INTENTIONAL.
     * However, pre-existing entries in the context will not be changed.
     *
     * @param   \Yana\Forms\Facade            $form     form structure and setup
     * @param   \Yana\Forms\Setups\IsContext  $context  form context to take the field list from
     */
    public function __construct(\Yana\Forms\Facade $form, \Yana\Forms\Setups\IsContext $context)
    {
        $this->_form = $form;
        $this->_context = $context;
    }

    /**
     * Returns collection of field facades.
     *
     * @return  \Yana\Forms\Fields\FieldCollection
     */
    protected function _getCollection()
    {
        if (!isset($this->_collection)) {
            $builder = new \Yana\Forms\Fields\FieldCollectionBuilder();
            $this->_collection = $builder($this);
        }
        return $this->_collection;
    }

    /**
     * Relay function call to wrapped object.
     *
     * @param   string  $name       method name
     * @param   array   $arguments  list of arguments to pass to function
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when the function is not found
     */
    public function __call($name, array $arguments)
    {
        if (method_exists($this->_context, $name)) {
            return call_user_func_array(array($this->_context, $name), $arguments);
        } else {
            return call_user_func_array(array($this->_form, $name), $arguments);
        }
    }

    /**
     * Get form context.
     *
     * @return  \Yana\Forms\Setups\IsContext
     */
    public function getContext(): \Yana\Forms\Setups\IsContext
    {
        return $this->_context;
    }

    /**
     * Get form facade.
     *
     * @return  \Yana\Forms\Facade
     */
    public function getForm(): \Yana\Forms\Facade
    {
        return $this->_form;
    }

    /**
     * Set a list of items
     *
     * @param   array  $items  list of items to work on
     * @return  $this
     * @codeCoverageIgnore
     */
    public function setItems(array $items = array())
    {
        $this->_getCollection()->setItems($items);
        return $this;
    }

    /**
     * Get current item.
     *
     * @return  \Yana\Forms\Fields\IsField
     * @throws  \Yana\Core\Exceptions\OutOfBoundsException  if the iterator is out of bounds
     * @codeCoverageIgnore
     */
    public function current()
    {
        return $this->_getCollection()->current();
    }

    /**
     * Increment iterator to next item.
     *
     * @codeCoverageIgnore
     */
    public function next()
    {
        $this->_getCollection()->next();
    }

    /**
     * Get field key.
     *
     * May return NULL if there is no key.
     *
     * @return  string
     * @codeCoverageIgnore
     */
    public function key()
    {
        return $this->_getCollection()->key();
    }

    /**
     * Check if iterator position is valid.
     *
     * @return  bool
     * @codeCoverageIgnore
     */
    public function valid()
    {
        return $this->_getCollection()->valid();
    }

    /**
     * Rewind iterator.
     *
     * @codeCoverageIgnore
     */
    public function rewind()
    {
        $this->_getCollection()->rewind();
    }

    /**
     * Return the number of items in the collection.
     *
     * If the collection is empty, it returns 0.
     *
     * @return  int
     * @codeCoverageIgnore
     */
    public function count()
    {
        return $this->_getCollection()->count();
    }

    /**
     * Get item list.
     *
     * @return  array
     * @codeCoverageIgnore
     */
    public function toArray()
    {
        return $this->_getCollection()->toArray();
    }

    /**
     * Check if item exists.
     *
     * @param   string  $offset  index of item to test
     * @return  bool
     * @codeCoverageIgnore
     */
    public function offsetExists($offset)
    {
        assert(is_string($offset), 'Invalid argument type: $offset. String expected.');
        return $this->_getCollection()->offsetExists($offset);
    }

    /**
     * Return item at offset.
     *
     * @param   string  $offset  index of item to retrieve
     * @return  \Yana\Forms\Fields\IsField
     * @codeCoverageIgnore
     */
    public function offsetGet($offset)
    {
        assert(is_string($offset), 'Invalid argument type: $offset. String expected.');
        return $this->_getCollection()->offsetGet($offset);
    }

    /**
     * Insert or replace item.
     *
     * @param   string                      $offset  index of item to replace
     * @param   \Yana\Forms\Fields\IsField  $value   new value of item
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given value is not valid
     * @return  \Yana\Forms\Fields\IsField
     */
    public function offsetSet($offset, $value)
    {
        assert(is_string($offset), 'Invalid argument type: $offset. String expected.');
        return $this->_getCollection()->offsetSet($offset, $value);
    }

    /**
     * Remove item from collection.
     *
     * @param  string  $offset  index of item to remove
     * @codeCoverageIgnore
     */
    public function offsetUnset($offset)
    {
        assert(is_string($offset), 'Invalid argument type: $offset. String expected.');
        $this->_getCollection()->offsetUnset($offset);
    }

}

?>