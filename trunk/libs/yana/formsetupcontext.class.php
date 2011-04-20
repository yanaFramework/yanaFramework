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
 * Form settings that depend on the type of form used.
 *
 * A context is a number of settings that apply to a form in a specified scenario.
 * E.g. using an insert-context a form may have other contents than in an edit- or search-context.
 *
 * @access      public
 * @package     yana
 * @subpackage  form
 */
class FormSetupContext extends Object
{

    /**
     * Context name.
     *
     * @access  private
     * @var     string
     */
    private $_name = "";

    /**
     * Form action name.
     *
     * @access  private
     * @var     string
     */
    private $_action = "";

    /**
     * Rows with values.
     *
     * @access  private
     * @var     FormRowIterator
     */
    private $_rows = null;

    /**
     * Values if there are no rows.
     *
     * @access  private
     * @var     array
     */
    private $_values = array();

    /**
     * Optional list of included column names.
     *
     * Empty = all.
     *
     * @access  private
     * @var     array
     */
    private $_columnNames = array();

    /**
     * Initialize instance.
     *
     * @accesss  public
     * @param    string  $name  context id
     */
    public function __construct($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $this->_name = (string) $name;
        $this->_rows = new FormRowIterator();
    }

    /**
     * Get context name.
     *
     * @accesss  public
     * @return   string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * get form value
     *
     * @access  public
     * @param   string  $key  id of value to retrieve
     * @return  mixed
     */
    public function getValue($key)
    {
        assert('is_string($key); // Wrong argument type argument 1. String expected');
        return Hashtable::get($this->_values, strtolower($key));
    }

    /**
     * Get form values.
     *
     * @access  public
     * @return  array
     */
    public function getValues()
    {
        assert('is_array($this->_values); // Member "values" is expected to be an array.');
        return $this->_values;
    }

    /**
     * Set form value.
     *
     * @access  public
     * @param   string  $key    id of value to set
     * @param   mixed   $value  new value
     * @return  FormSetup
     */
    public function setValue($key, $value)
    {
        assert('is_string($key); // Wrong argument type argument 1. String expected');
        $this->_values[$key] = $value;
        return $this;
    }

    /**
     * Set form values.
     *
     * @access  public
     * @param   array  $values  new values
     * @return  FormSetup
     */
    public function setValues(array $values)
    {
        $this->_values = $values;
        return $this;
    }

    /**
     * Add new form values.
     *
     * Replaces existing values, adds new values and keeps values that haven't been changed in the request.
     *
     * @access  public
     * @param   array  $values  new values
     * @return  FormSetup
     */
    public function addValues(array $values)
    {
        $this->_values = $values + $this->_values;
        return $this;
    }

    /**
     * Update form row.
     *
     * Replaces existing values, adds new values and keeps values that haven't been changed in the request.
     * If the row does not exist, it is created.
     *
     * @access  public
     * @param   array  $row  new values
     * @return  FormSetup
     */
    public function updateRow($key, array $row)
    {
        $updatedRow = $row + (array) $this->getRows()->offsetGet($key);
        $this->getRows()->offsetSet($key, $updatedRow);
        return $this;
    }

    /**
     * Get rows.
     *
     * @access  public
     * @return  FormRowIterator
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * Get rows.
     *
     * @access  public
     * @return  FormRowIterator
     */
    public function getRow()
    {
        return $this->_rows->current();
    }

    /**
     * set export action
     *
     * @access  public
     * @param   string  $action action name
     * @return  FormSetup
     */
    public function setAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->_action = $action;
        return $this;
    }

    /**
     * get export action
     *
     * @access  public
     * @return  string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Get unique list of column names.
     *
     * @access  public
     * @return  array
     */
    public function getColumnNames()
    {
        return $this->_columnNames;
    }

    /**
     * Set unique list of column names.
     *
     * This does not check if the columns do exist.
     * If the list is left empty. The form is meant to auto-detect the abvailable columns.
     *
     * @access  public
     * @param   array  $columnNames  list of identifiers
     * @return  FormSetup
     */
    public function setColumnNames(array $columnNames)
    {
        $this->_columnNames = $columnNames;
        return $this;
    }

}

?>