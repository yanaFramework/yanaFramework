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
interface IsSelectQuery extends
    \Yana\Db\Queries\IsQueryWithWhereClause,
    \Yana\Db\Queries\IsQueryWithHavingClause,
    \Yana\Db\Queries\IsQueryWithJoins,
    \Yana\Db\Queries\IsQueryWithColumn,
    \Yana\Db\Queries\IsQueryWithColumns,
    \Yana\Db\Queries\IsQueryWithOrderClause,
    \Yana\Db\Queries\IsQueryWithArrayAddress,
    \Yana\Db\Queries\IsQueryWithOffsetClause,
    \Yana\Db\Queries\IsQueryActingAsSubSelect
{

    /**
     * Get results as CSV.
     *
     * This exports the data as a comma-seperated list of values.
     *
     * You may choose custom column and row delimiters by setting the parameters
     * to appropriate values.
     *
     * The first line always contains a header of column titles. To exclude this
     * information from the result, set $hasHeader to bool(false).
     *
     * Example output:
     * <pre>
     * "Name","Forename","Title","Name of Book"
     * "Smith","Steven, M.","Mr.","The ""Cookbook"" of Time"
     * "Higgings","Barbara","Ms.","A multiline guide
     * to the Galaxy"
     * </pre>
     *
     * The function returns the CSV contents as a multi-line string.
     * Note that the delimiters must be ASCII characters.
     *
     * The CSV format is defined in {@link http://www.rfc-editor.org/rfc/rfc4180.txt RFC 4180}.
     *
     * @param   string  $colSep       column seperator
     * @param   string  $rowSep       row seperator
     * @param   bool    $hasHeader    add column names as first line (yes/no)
     * @param   string  $stringDelim  any character that isn't the row or column seperator
     * @return  string
     * @name    \Yana\Db\Queries\Select::toCSV()
     * @throws  \Yana\Core\Exceptions\InvalidValueException  if the database query is incomplete or invalid
     */
    public function toCSV(string $colSep = ';', string $rowSep = "\n", bool $hasHeader = true, string $stringDelim = '"'): string;

    /**
     * get list of column titles
     *
     * Returns the title attributes of all selected columns as a numeric
     * array.
     *
     * @return  array
     * @see     \Yana\Db\Queries\Select::toCSV()
     */
    public function getColumnTitles(): array;

    /**
     * Joins two tables (by using a left join).
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
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if a provided table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if a provided column is not found
     * @return  $this
     */
    public function setLeftJoin($joinedTableName, $targetKey = null, $sourceTableName = null, $foreignKey = null);

    /**
     * Joins two tables (by using a natural join).
     *
     * This starts a natural join to all tables left of the join condition.
     *
     * In case of natural joins the query builder is by design meant to resolve and rewrite the natural join to an inner join and
     * create the necessary where clause automatically. The inner join behaves like a natural join.
     *
     * There is a good reason for this behavior:
     * To allow the database to resolve natural joins would create a (even though probably hard to exploit) security vulnerability in our software.
     *
     * Because, our query builder is given its own database schema.
     * This schema may exclude certain columns the software/user is not supposed to see.
     * If, however, we let the database deal with the natural join on its own, it may (since it is ignorant of these client-side "views" of its schema)
     * include the hidden columns in the natural join regardless.
     * With a bit of effort this would possibly enable a remote attacker to "guess" the contents of a hidden column.
     * Since we are aware of this potential issue (and since it's always better to be safe than sorry) we avoid this problem by rewriting
     * all natural joins based on the client's view of the database schema.
     *
     * @param   string $joinedTableName  name of the foreign table to join the source table with
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if the provided table is not found
     * @return  $this
     */
    public function setNaturalJoin($joinedTableName);

    /**
     * Get the number of entries.
     *
     * This sends the query statement to the database and returns bool(true)
     * if the requested database object exists and bool(false) otherwise.
     *
     * @return  bool
     */
    public function doesExist(): bool;

    /**
     * Get the number of entries.
     *
     * This sends the query statement to the database and returns how many rows the result set would have.
     *
     * @return  int
     */
    public function countResults(): int;

    /**
     * get values from the database
     *
     * This sends the query statement to the database and returns the results.
     * The return type depends on the query settings, see {@see DbQuery::getExpectedResult()}.
     *
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when one of the given arguments is not valid
     */
    public function getResults();
}

?>