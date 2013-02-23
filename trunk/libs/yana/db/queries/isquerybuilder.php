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
 * @ignore
 */

namespace Yana\Db\Queries;

/**
 * <<interface, builder>> Query builder.
 *
 * Implement this for each query builder instance.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsQueryBuilder
{

    /**
     * Create Select statement.
     *
     * @param   string  $key      the address of the value(s) to retrieve
     * @param   array   $where    where clause
     * @param   array   $orderBy  a list of columns to order the resultset by
     * @param   int     $offset   the number of the first result to be returned
     * @param   int     $limit    maximum number of results to return
     * @param   bool    $desc     if true results will be ordered in descending, otherwise in ascending order
     * @return  \Yana\Db\Queries\Select
     */
    public function select($key, array $where = array(), $orderBy = array(), $offset = 0, $limit = 0, $desc = false);

    /**
     * Create Update statement.
     *
     * @param   string  $key    the address of the row that should be updated
     * @param   mixed   $value  value
     * @return  \Yana\Db\Queries\Update
     */
    public function update($key, $value = array());

    /**
     * Create Insert statement.
     *
     * @param   string  $key    the address of the row that should be inserted
     * @param   mixed   $value  value
     * @return  \Yana\Db\Queries\Insert
     */
    public function insert($key, $value = array());

    /**
     * Create Delete statement.
     *
     * The parameter $where follows this syntax:
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
     * @param   string  $key    the address of the row that should be removed
     * @param   array   $where  where clause
     * @param   int     $limit  maximum number of rows to remove
     * @return  \Yana\Db\Queries\Delete
     */
    public function remove($key, array $where = array(), $limit = 1);

    /**
     * Create Select statement to count the number of entries inside a table.
     *
     * @param   string  $table  name of a table
     * @param   array   $where  optional where clause
     * @return  \Yana\Db\Queries\SelectCount
     * @throws  \Yana\Db\Exceptions\TableNotFoundException
     */
    public function length($table, array $where = array());

    /**
     * Create Select statement to check, wether a certain element exists.
     *
     * @param   string  $key    adress to check
     * @param   array   $where  optional where clause
     * @return  \Yana\Db\Queries\SelectExist
     */
    public function exists($key, array $where = array());

}

?>