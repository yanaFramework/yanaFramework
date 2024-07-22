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
 * <<interface>> This class represents a join condition in the form of JoinedTable.TargetKey = SourceTable.ForeignKey.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsQueryWithJoins extends \Yana\Db\Queries\IsQuery
{

    /**
     * Joins the selected table with another (by using an inner join).
     *
     * If the target key is not provided, the function will automatically search for
     * a suitable foreign key in the source table, that refers to the foreign table.
     * If target  is not provided, the function will automatically look up
     * the primary key of $tableName and use it instead.
     *
     * @param   string $joinedTableName  name of the foreign table to join the source table with
     * @param   string $targetKey        name of the key in foreign table that is referenced
     *                                   (may be omitted if it is the primary key)
     * @param   string $sourceTableName  name of the source table
     * @param   string $foreignKey       name of the foreign key in source table
     *                                   (when omitted the API will look up the key in the schema file)
     * @throws  \Yana\Core\Exceptions\NotFoundException  if a provided table or column is not found
     * @return  $this
     */
    public function setInnerJoin(string $joinedTableName, ?string $targetKey = null, ?string $sourceTableName = null, ?string $foreignKey = null);

    /**
     * remove table  of joined tables
     *
     * Calling this function will remove the given table from the query.
     * Note: you may not remove the base table.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $table  name of table to remove
     * @return  $this
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the table does not exist
     */
    public function unsetJoin(string $table);

    /**
     * Get foreign key column.
     *
     * @param   string  $table  joined table
     * @return  \Yana\Db\Queries\JoinCondition
     * @throws  \Yana\Db\Queries\Exceptions\NotFoundException  when the target table is not joined
     */
    public function getJoin(string $table): \Yana\Db\Queries\JoinCondition;

    /**
     * Get a list of all joined tables.
     *
     * Returns an array where the keys are the names of the joined tables.
     * Each item is an array of two column names, where the first is the column in the base table
     * and the second is the column in the target table.
     *
     * The array will be empty if there are now table-joins in the current query.
     *
     * @return  \Yana\Db\Queries\JoinCondition[]
     */
    public function getJoins(): array;
}

?>
