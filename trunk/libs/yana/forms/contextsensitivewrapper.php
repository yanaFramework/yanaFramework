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

namespace Yana\Forms;

/**
 * <<wrapper, facade>> A context-sensitive form wrapper.
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
class ContextSensitiveWrapper extends \Yana\Forms\Fields\FacadeCollection
{

    /**
     * Form structure and setup.
     *
     * @var  \Yana\Forms\Facade
     */
    private $_form;

    /**
     * Form context to take the field list from.
     *
     * @var  \Yana\Forms\Fields\FacadeCollection
     */
    private $_context;

    /**
     * Initialize a field collection from a given context.
     *
     * Note that this will automatically create a field list based on the table associated with the form,
     * and save it as new context collection items.
     *
     * So it DOES modify the given context. This will initialize the context and is INTENTIONAL.
     * However, pre-existing entries in the context will not be changed.
     *
     * @param   \Yana\Forms\Facade          $form     form structure and setup
     * @param   \Yana\Forms\Setups\Context  $context  form context to take the field list from
     */
    public function __construct(\Yana\Forms\Facade $form, \Yana\Forms\Setups\Context $context)
    {
        $this->_form = $form;
        $this->_context = $context;
        $this->_buildFormFieldCollection($context);
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
     * @return  \Yana\Forms\Setups\Context
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * Get primary key of the current row.
     *
     * If there is no current row, the function returns NULL instead.
     *
     * @return  scalar
     */
    public function getPrimaryKey()
    {
        return $this->getContext()->getRows()->key();
    }

    /**
     * Build a field collection from a given context.
     *
     * @param   \Yana\Forms\Setups\Context  $context  form context to take the field list from
     */
    private function _buildFormFieldCollection(\Yana\Forms\Setups\Context $context)
    {
        try {
            foreach ($context->getColumnNames() as $columnName)
            {
                if (!$this->offsetExists($columnName)) {
                    $this->_buildFormFieldFacade($columnName);
                }
            }
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            // No table - collection will remain unchanged (probably empty)
        }
    }

    /**
     * Find a column in the table and create a field facade based on that.
     *
     * @param  string  $columnName  must be valid column in table associated with form
     * @throws  \Yana\Core\Exceptions\NotFoundException  when there is no such table
     */
    private function _buildFormFieldFacade($columnName)
    {
        $table = $this->_form->getTable(); // May throw NotFoundException when there is no table
        try {
            $column = $table->getColumn($columnName); // May throw NotFoundException when there is no such column

            if ($this->_form->isField($columnName)) {
                $field = $this->_form->getField($columnName);
            }
            $facade = new \Yana\Forms\Fields\Facade($this, $column, $field);
            $this->offsetSet($columnName, $facade);
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            // do nothing
        }
    }

    /**
     * Check if the form has rows.
     *
     * Rows are sets of values for forms, that have a table-structure.
     *
     * Returns bool(true) if the form has at least 1 row.
     * Returns bool(false) if the form is empty.
     * Always returns bool(false) if the form does not have rows at all,
     * e.g. if it is an insert- or search-form (this is: it is using an insert- or search-context).
     *
     * @return  bool
     */
    public function hasRows()
    {
        return $this->getRowCount() > 0;
    }

    /**
     * Returns the number of rows.
     *
     * If the form has no rows, the function returns int(0).
     *
     * @return  int
     */
    public function getRowCount()
    {
        return $this->_context->getRows()->count();
    }

    /**
     * Advances the pointer one row.
     *
     * If there is no next row, this does nothing.
     *
     * @return  $this
     */
    public function nextRow()
    {
        $this->_context->getRows()->next();
        return $this;
    }

}

?>