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
 *
 * @ignore
 */

namespace Yana\Forms;

/**
 * <<interface>> Form build command.
 *
 * @package     yana
 * @subpackage  form
 */
interface IsBuilder extends \Yana\Data\Adapters\IsCacheable
{

    /**
     * Get name of database file.
     *
     * @return  string
     */
    public function getFile();

    /**
     * Get id of form.
     *
     * @return  string
     */
    public function getId(): string;

    /**
     * Set name of form to use.
     *
     * If you wish to extract a sub-form, give the full path separate the names with a dot.
     * Example: "form.subform".
     *
     * @param   string  $id  valid form name
     * @return  $this
     */
    public function setId(string $id);

    /**
     * Get name of table.
     *
     * @return  string
     */
    public function getTable();

    /**
     * Set table to choose from database.
     *
     * @param   string  $table  valid table name
     * @return  $this 
     */
    public function setTable($table);

    /**
     * Get white-listed column names.
     *
     * @return  array
     */
    public function getShow();

    /**
     * Set list of columns, that should be shown in the form.
     *
     * @param   array  $show  white-listed column names.
     * @return  $this 
     */
    public function setShow(array $show);

    /**
     * Get black-listed column names.
     *
     * @return  array
     */
    public function getHide();

    /**
     * Set list of columns, that should NOT be shown in the form.
     *
     * @param   array  $hide  black-listed column names.
     * @return  $this 
     */
    public function setHide(array $hide);

    /**
     * Get where clause.
     *
     * @return  string|array
     */
    public function getWhere();

    /**
     * Set sequence for SQL-where clause.
     *
     * The syntax is as follows:
     * <ol>
     * <li> left operand </li>
     * <li> operator </li>
     * <li> right operand </li>
     * </ol>
     *
     * List of supported operators:
     * <ul>
     * <li> and, or (indicates that both operands are sub-clauses) </li>
     * <li> =, !=, <, <=, >, >=, like, regexp </li>
     * </ul>
     *
     * Example:
     * <code>
     * array(
     *     array('col1', '=', 'val1'),
     *     'and',
     *     array(
     *         array('col2', '<', 1),
     *         'or',
     *         array('col2', '>', 3)
     *     )
     * )
     * </code>
     *
     * @param   array  $where  valid where clause
     * @return  $this
     * @see     \Yana\Db\Queries\SelectExist::setWhere()
     */
    public function setWhere(array $where);

    /**
     * Get name of column to sort by.
     *
     * @return  string
     */
    public function getSort();

    /**
     * Set name of column to sort entries by.
     *
     * @param   string  $sort  valid column name
     * @return  $this 
     */
    public function setSort($sort);

    /**
     * Check if contents are sorted descending order.
     *
     * @return  bool
     */
    public function isDescending();

    /**
     * Set sorting order for entries.
     *
     * @param   bool  $desc  true = descending, false = ascending
     * @return  $this 
     */
    public function setDescending($desc);

    /**
     * Get number of 1st page to show.
     *
     * @return  int
     */
    public function getPage();

    /**
     * Set number of 1st page to show.
     *
     * @param   int  $page  positive number (default = 0)
     * @return  $this 
     */
    public function setPage($page);

    /**
     * Get number of entries to view per page.
     *
     * The default is 20.
     *
     * @return  int
     */
    public function getEntries();

    /**
     * Set number of entries to view per page.
     *
     * @param   int  $entries  positive number (default = 20)
     * @return  $this 
     */
    public function setEntries($entries);

    /**
     * Get name of action.
     *
     * @return  string
     */
    public function getOninsert();

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @param   string  $oninsert  form action name
     * @return  $this 
     */
    public function setOninsert($oninsert);

    /**
     * Get name of action.
     *
     * @return  string
     */
    public function getOnupdate();

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @param   string  $onupdate  form action name
     * @return  $this 
     */
    public function setOnupdate($onupdate);

    /**
     * Get name of action.
     *
     * @return  string
     */
    public function getOndelete();

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @param   string  $ondownload  form action name
     * @return  $this 
     */
    public function setOndelete($ondelete);

    /**
     * Get name of action.
     *
     * @return  string
     */
    public function getOnsearch();

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @param   string  $onsearch  form action name
     * @return  $this 
     */
    public function setOnsearch($onsearch);

    /**
     * Get name of action.
     *
     * @return  string
     */
    public function getOndownload();

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @param   string  $ondownload  form action name
     * @return  $this 
     */
    public function setOndownload($ondownload);

    /**
     * Get name of action.
     *
     * @return  string
     */
    public function getOnexport();

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @param   string  $onexport  form action name
     * @return  $this 
     */
    public function setOnexport($onexport);

    /**
     * Get index of selected layout.
     *
     * @return  int
     */
    public function getLayout();

    /**
     * Set layout.
     *
     * Where multiple layouts are available to present the result, this allows to choose the prefered one.
     *
     * @param   int  $layout  positive number (default = 0)
     * @return  $this 
     */
    public function setLayout($layout);

    /**
     * <<magic>> Invoke the function.
     *
     * @return  \Yana\Forms\Facade
     * @throws  \Yana\Core\Exceptions\BadMethodCallException    when a parameter is missing
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when a paraemter is not valid
     */
    public function __invoke();

}

?>