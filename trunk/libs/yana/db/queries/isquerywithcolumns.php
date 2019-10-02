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

namespace Yana\Db\Queries;

/**
 * <<interface>> Queries that have a where clause.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsQueryWithColumns extends \Yana\Db\Queries\IsQuery
{

    /**
     * Set source columns.
     *
     * This sets the list of columns to retrieve, like in
     * SELECT col1, col2, ... colN FROM ...
     *
     * Note that this applies only to select statements,
     * not insert, update or delete.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * Note that, depending on the number of columns you wish to
     * retrieve, the datatype of the result may differ.
     *
     * Getting 1 column from 1 row will just return the
     * value of that cell, e.g. int(1). Getting multiple columns
     * from 1 row will return an array containing the values,
     * e.g. array('col1'=>1, 'col2'=>2, 'col3'=>3).
     *
     * Getting 1 column from multiple rows will return an
     * one-dimensional array of these values.
     * Getting multiple columns from multiple rows will
     * return a two-dimensional array of rows, where each
     * row is an associative array containing the values
     * of the selected columns.
     *
     * Examples:
     * <code>
     * // select 1 column
     * $dbq->setColumns(array('foo'));
     * // same as:
     * $dbq->setColumn('foo');
     *
     * // select multiple columns
     * $dbq->setColumns(array('foo1', 'foo2'));
     *
     * // select multiple columns from different tables
     * // 1) join with table2
     * $dbq->setInnerJoin('table2');
     * // 2) select columns from current table and table2
     * $dbq->setColumns(array('foo1', 'table2.foo2'));
     * </code>
     *
     * @param   array  $columns  list of columns
     * @since   2.9.6
     * @name    DbQuery::setColumns()
     * @see     DbQuery::setColumn()
     * @throws  \Yana\Db\Queries\Exceptions\InvalidSyntaxException   if table has not been initialized
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if the base table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the given column is not found
     * @return  $this 
     */
    public function setColumns(array $columns = array());

    /**
     * Returns lower-cased names of the selected columns as a numeric array of strings.
     *
     * If none has been selected, an empty array is returned.
     *
     * @return  array
     */
    public function getColumns(): array;

    /**
     * Add source columns.
     *
     * This adds an item to the list of columns to retrieve, like in
     * SELECT col1, col2, ... colN FROM ...
     *
     * The column gets appended, not overwritten.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $column  column name
     * @param   string  $alias   optional column alias
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if a given argument is invalid
     * @throws  \Yana\Core\Exceptions\NotFoundException         if the given table or column is not found
     * @return  $this 
     */
    public function addColumn(string $column, string $alias = "");

}

?>