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
 * <<wrapper, facade>> A context-sensitive form wrapper.
 *
 * This class is meant to provide a context-aware form objects, by binding a form,
 * it's current context and identifying the fields that apply to it.
 *
 * @access      public
 * @package     yana
 * @subpackage  form
 * @ignore
 */
class FormContextSensitiveWrapper extends FormFieldFacadeCollection implements Iterator
{

    /**
     * Form structure and setup.
     *
     * @access  private
     * @var     FormFacade
     */
    private $_form;

    /**
     * Form context to take the field list from.
     *
     * @access  private
     * @var     FormFieldFacadeCollection
     */
    private $_context;

    /**
     * Initialize a field collection from a given context.
     *
     * @access  public
     * @param   FormFacade        $form     form structure and setup
     * @param   FormSetupContext  $context  form context to take the field list from
     */
    public function __construct(FormFacade $form, FormSetupContext $context)
    {
        $this->_form = $form;
        $this->_context = $context;
        $this->_buildFormFieldCollection($context);
    }

    /**
     * Relay function call to wrapped object.
     *
     * @access  public
     * @param   string  $name       method name
     * @param   array   $arguments  list of arguments to pass to function
     * @return  mixed
     * @throws  NotImplementedException  when the function is not found
     */
    public function __call($name, $arguments)
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
     * @access  public
     * @return  FormSetupContext
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
     * @access  public
     * @return  scalar
     */
    public function getPrimaryKey()
    {
        return $this->getContext()->getRows()->key();
    }

    /**
     * Build a field collection from a given context.
     *
     * @access  private
     * @param   FormSetupContext  $context  form context to take the field list from
     */
    private function _buildFormFieldCollection(FormSetupContext $context)
    {
        $table = $this->_form->getTable();
        foreach ($context->getColumnNames() as $columnName)
        {
            try {
                $column = $table->getColumn($columnName);
            } catch (NotFoundException $e) {
                continue; // skip invalid column definition
            }
            $field = null;
            if ($this->_form->isField($columnName)) {
                $field = $this->_form->getField($columnName);
            }
            $facade = new FormFieldFacade($this, $column, $field);
            $this->offsetSet($columnName, $facade);
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
     * @access  public
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
     * @access  public
     * @return  int
     */
    public function getRowCount()
    {
        return $this->_context->getRows()->count();
    }

    /**
     * Advances the pointer one row.
     *
     * @access  public
     */
    public function nextRow()
    {
        $this->_context->getRows()->next();
    }

}

?>