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

namespace Yana\Db\Queries;

/**
 * <<interface>> database query.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsQuery
{

    /**
     * Returns the query's database connection object.
     *
     * @return \Yana\Db\IsConnection
     */
    public function getDatabase(): \Yana\Db\IsConnection;

    /**
     * Reset query.
     *
     * Resets all properties of the query object, except
     * for the database connection and the properties
     * "table", "type", "useInheritance".
     *
     * This function allows you to "recycle" a query object
     * and reuse it without creating another one. This can
     * help to improve the performance of your application.
     *
     * @return  $this
     */
    public function resetQuery();

    /**
     * get the currently selected type of statement
     *
     * Returns currently selected constant.
     *
     * @return  int
     */
    public function getType(): int;

    /**
     * find out which kind of result is expected
     *
     * Returns currently selected constant.
     *
     * <ul>
     *  <li> DbResultEnumeration::UNKNOWN - no input </li>
     *  <li> DbResultEnumeration::TABLE   - table only </li>
     *  <li> DbResultEnumeration::ROW     - table + row </li>
     *  <li> DbResultEnumeration::COLUMN  - table + column </li>
     *  <li> DbResultEnumeration::CELL    - table + row + column </li>
     * </ul>
     *
     * Note: DbResultEnumeration::CELL means to refer to exactly 1 column.
     * When retrieving multiple columns from a row,
     * use DbResultEnumeration::ROW instead.
     *
     * @return  int
     * @since   2.9.3
     */
    public function getExpectedResult(): int;

    /**
     * Activate / deactivate automatic handling of inheritance.
     *
     * The query builder is able to detect if one table inherits
     * from another and if so, it will auto-join both tables.
     * In this case, selecting a row from the offspring table will
     * also return all entries of the corresponding row in the
     * parent table.
     *
     * However: while this usually comes in handy, there are some
     * rare situations where you won't want this to be done.
     * E.g. when copying rows from one table to another.
     *
     * This function allows you to enable or disable this feature.
     * It is enabled by default.
     *
     * Note: you have to set this before you set the table property.
     * Otherwise it will have no effect.
     *
     * @param   bool  $state  true = on, false = off
     * @return  $this
     */
    public function useInheritance(bool $state);

    /**
     * get table by column name
     *
     * If multiple tables are joined (either automatically or manually)
     * you may use this function to get the source table for a certain row.
     *
     * @param   string  $columnName  name of a column
     * @return  \Yana\Db\Ddl\Table
     * @throws  \Yana\Db\Queries\Exceptions\TableNotSetException     if table has not been initialized
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if no column with the given name has been found
     */
    public function getTableByColumn(string $columnName): \Yana\Db\Ddl\Table;

    /**
     * Get the parent of a table.
     *
     * This function provides information on entity inheritance
     * within the database's data structure.
     *
     * If $table extends another table, then this
     * will return the name of the parent table as a string.
     *
     * It will return bool(false) if there is no such parent.
     *
     * If the argument $tableName is empty, or not provided, the
     * currently selected table (see {link \Yana\Db\Queries\AbstractQuery::setTable()})
     * is used instead.
     *
     * @param   string  $tableName  name of a table
     * @since   2.9.6
     * @return  string
     */
    public function getParent(string $tableName = ""): ?\Yana\Db\Ddl\Table;

    /**
     * Set source table.
     *
     * For statements like "Select * from [table]" this is the table name.
     * If your query uses multiple tables (via a join) this is the name of the base-table (the first table in the list).
     *
     * @param   string  $table  table name to use in query
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException  when the table does not exist
     * @return  $this
     */
    public function setTable(string $table);

    /**
     * Get the name of the currently selected table.
     *
     * Returns the name of the currently selected table.
     * If none has been selected yet, an empty string is returned.
     *
     * @return  string
     */
    public function getTable(): string;

    /**
     * Set source row.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * Note: does not check if row exists.
     *
     * Currently you may only request 1 row or all.
     * To search for all rows, use the wildcard '*'.
     *
     * @param   scalar  $row  set source row
     * @throws  \Yana\Db\Queries\Exceptions\TableNotSetException     if table has not been initialized
     * @return  $this
     */
    public function setRow($row);

    /**
     * Get the currently selected row.
     *
     * Returns the lower-cased name of the currently
     * selected column, or bool(false) if none has been
     * selected yet.
     *
     * If none has been selected, '*' is returned.
     *
     * @return  string
     */
    public function getRow(): string;

    /**
     * Resolve key address to determine table, column and row.
     *
     * @param   string  $key  resolve key address to determine table, column and row
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if the given table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the given column is not found
     * @throws  \Yana\Db\Queries\Exceptions\InconsistencyException   when a foreign key check detects invalid database values
     * @throws  \Yana\Db\Queries\Exceptions\TargetNotFoundException  when no target can be found for the given key
     * @return  $this
     */
    public function setKey(string $key);

    /**
     * Get the currently selected limit.
     *
     * Note: This setting will not be part of the sql statement produced by __toString().
     * Use the API's $limit and $offset parameter instead when sending
     * the query.
     *
     * This restriction does not apply if you use
     * {link \Yana\Db\AbstractQuery::sendQuery()}.
     *
     * Note: For security reasons all delete queries will automatically
     * be limited to 1 row at a time.
     *
     * @return  int
     * @since   2.9.3
     */
    public function getLimit(): int;

    /**
     * Get the currently selected offset.
     *
     * Note: This setting will not be part of the sql statement produced by __toString().
     * Use the API's $limit and $offset parameter instead when sending the query.
     *
     * This restriction does not apply if you use sendQuery().
     *
     * @return  int
     * @since   2.9.3
     */
    public function getOffset(): int;

    /**
     * Get unique id.
     *
     * @return  string
     */
    public function toId(): string;

    /**
     * Send query to database-server.
     *
     * Returns a result-object.
     *
     * @return  \Yana\Db\IsResult
     * @since   2.9.3
     */
    public function sendQuery(): \Yana\Db\IsResult;

}

?>