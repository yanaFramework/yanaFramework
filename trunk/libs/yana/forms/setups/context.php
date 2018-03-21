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

namespace Yana\Forms\Setups;

/**
 * Form settings that depend on the type of form used.
 *
 * A context is a number of settings that apply to a form in a specified scenario.
 * E.g. using an insert-context a form may have other contents than in an edit- or search-context.
 *
 * @package     yana
 * @subpackage  form
 */
class Context extends \Yana\Core\Object implements \Yana\Forms\Setups\IsContext
{

    /**
     * Context name.
     *
     * @var  string
     */
    private $_contextName = "";

    /**
     * Form action name.
     *
     * @var  string
     */
    private $_action = "";

    /**
     * Footer text.
     *
     * @var  string
     */
    private $_footer = "";

    /**
     * Header text.
     *
     * @var  string
     */
    private $_header = "";

    /**
     * Rows with values.
     *
     * @var  \Yana\Forms\RowIterator
     */
    private $_rows = null;

    /**
     * Values if there are no rows.
     *
     * @var  array
     */
    private $_values = array();

    /**
     * Optional list of included column names.
     *
     * Empty = all.
     *
     * @var  array
     */
    private $_columnNames = array();

    /**
     * Initialize instance.
     *
     * @param   string  $name  context id
     */
    public function __construct($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $this->_contextName = (string) $name;
        $this->_rows = new \Yana\Forms\RowIterator();
    }

    /**
     * Get context name.
     *
     * @return  string
     */
    public function getContextName()
    {
        return $this->_contextName;
    }

    /**
     * get form value
     *
     * @param   string  $key  id of value to retrieve
     * @return  mixed
     */
    public function getValue($key)
    {
        assert('is_string($key); // Wrong argument type argument 1. String expected');
        return \Yana\Util\Hashtable::get($this->_values, strtolower($key));
    }

    /**
     * Get form values.
     *
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
     * @param   string  $key    id of value to set
     * @param   mixed   $value  new value
     * @return  self
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
     * @param   array  $values  new values
     * @return  self
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
     * @param   array  $values  new values
     * @return  self
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
     * @param   string  $key  valid identifier
     * @param   array   $row  new values
     * @return  self
     */
    public function updateRow($key, array $row)
    {
        $updatedRow = \Yana\Util\Hashtable::changeCase($row, CASE_UPPER) + (array) $this->getRows()->offsetGet($key);
        $this->getRows()->offsetSet($key, $updatedRow);
        return $this;
    }

    /**
     * Replace form rows.
     *
     * @param   array  $rows  new values
     * @return  self
     */
    public function setRows(array $rows)
    {
        $this->getRows()->setItems(\Yana\Util\Hashtable::changeCase($rows, CASE_UPPER));
        return $this;
    }

    /**
     * Get rows.
     *
     * @return  \Yana\Forms\RowIterator
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * Get rows.
     *
     * @return  array
     */
    public function getRow()
    {
        return $this->_rows->current();
    }

    /**
     * Set footer text.
     *
     * @param   string  $footer  any text or HTML
     * @return  \Yana\Forms\Setups\Context
     */
    public function setFooter($footer)
    {
        assert('is_string($footer); // Invalid argument $footer: string expected');
        $this->_footer = (string) $footer;
        return $this;
    }

    /**
     * Get footer text.
     *
     * @return  string
     */
    public function getFooter()
    {
        return $this->_footer;
    }

    /**
     * Set header text.
     *
     * @param   string  $header  any text or HTML
     * @return  \Yana\Forms\Setups\Context
     */
    public function setHeader($header)
    {
        assert('is_string($header); // Invalid argument $footer: string expected');
        $this->_header = (string) $header;
        return $this;
    }

    /**
     * Get header text.
     *
     * @return  string
     */
    public function getHeader()
    {
        return $this->_header;
    }

    /**
     * set export action
     *
     * @param   string  $action action name
     * @return  self
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
     * @return  string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Get unique list of column names.
     *
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
     * @param   array  $columnNames  list of identifiers
     * @return  self
     */
    public function setColumnNames(array $columnNames)
    {
        $this->_columnNames = $columnNames;
        return $this;
    }

}

?>