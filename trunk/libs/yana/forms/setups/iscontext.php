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
 * <<interface>> Form settings that depend on the type of form used.
 *
 * @package     yana
 * @subpackage  form
 */
interface IsContext
{

    /**
     * Get context name.
     *
     * @return  string
     */
    public function getContextName();

    /**
     * get form value
     *
     * @param   string  $key  id of value to retrieve
     * @return  mixed
     */
    public function getValue($key);

    /**
     * Get form values.
     *
     * @return  array
     */
    public function getValues();

    /**
     * Set form value.
     *
     * @param   string  $key    id of value to set
     * @param   mixed   $value  new value
     * @return  $this
     */
    public function setValue($key, $value);

    /**
     * Set form values.
     *
     * @param   array  $values  new values
     * @return  $this
     */
    public function setValues(array $values);

    /**
     * Add new form values.
     *
     * Replaces existing values, adds new values and keeps values that haven't been changed in the request.
     *
     * @param   array  $values  new values
     * @return  $this
     */
    public function addValues(array $values);

    /**
     * Update form row.
     *
     * Replaces existing values, adds new values and keeps values that haven't been changed in the request.
     * If the row does not exist, it is created.
     *
     * @param   string  $key  valid identifier
     * @param   array   $row  new values
     * @return  $this
     */
    public function updateRow($key, array $row);

    /**
     * Replace form rows.
     *
     * @param   array  $rows  new values
     * @return  $this
     */
    public function setRows(array $rows);

    /**
     * Get rows.
     *
     * @return  \Yana\Forms\RowIterator
     */
    public function getRows();

    /**
     * Get rows.
     *
     * @return  array
     */
    public function getRow();

    /**
     * Set footer text.
     *
     * @param   string  $footer  any text or HTML
     * @return  $this
     */
    public function setFooter($footer);

    /**
     * Get footer text.
     *
     * @return  string
     */
    public function getFooter();

    /**
     * Set header text.
     *
     * @param   string  $header  any text or HTML
     * @return  $this
     */
    public function setHeader($header);

    /**
     * Get header text.
     *
     * @return  string
     */
    public function getHeader();

    /**
     * set export action
     *
     * @param   string  $action action name
     * @return  $this
     */
    public function setAction($action);

    /**
     * get export action
     *
     * @return  string
     */
    public function getAction();

    /**
     * Get unique list of column names.
     *
     * @return  bool
     */
    public function hasColumnName($columnName);

    /**
     * Returns bool(true) if the list of column names is not empty.
     *
     * Returns bool(false) if there are no names in the list.
     *
     * @return  bool
     */
    public function hasColumnNames();

    /**
     * Get unique list of column names.
     *
     * @return  array
     */
    public function getColumnNames();

    /**
     * Set unique list of column names.
     *
     * This does not check if the columns do exist.
     * If the list is left empty. The form is meant to auto-detect the abvailable columns.
     *
     * @param   array  $columnNames  list of identifiers
     * @return  $this
     */
    public function setColumnNames(array $columnNames);

    /**
     * Add name of column to list.
     *
     * Note that the name will be changed to lower case.
     *
     * @param   string  $columnName  must be valid identifier
     * @return  $this
     */
    public function addColumnName($columnName);

}

?>