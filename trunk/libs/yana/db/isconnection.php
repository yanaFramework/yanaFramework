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

namespace Yana\Db;

/**
 * <<interface>> Database API.
 *
 * This is an interface for implementing a schema-aware database abstraction layer.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsConnection
{

    /**
     * Get database schema.
     *
     * @return  \Yana\Db\Ddl\Database
     */
    public function getSchema();

    /**
     * Returns the name of the chosen DBMS as a lower-cased string.
     *
     * @return  string
     */
    public function getDBMS();

    /**
     * Commits the current transaction.
     *
     * @return  \Yana\Db\IsConnection
     */
    public function commit();

    /**
     * Get values from the database.
     *
     * @param   string|\Yana\Db\Queries\Select  $key      the address of the value(s) to retrieve
     * @param   array            $where    where clause
     * @param   array            $orderBy  a list of columns to order the resultset by
     * @param   int              $offset   the number of the first result to be returned
     * @param   int              $limit    maximum number of results to return
     * @param   array             $desc     if true results will be ordered in descending,
     *                                     otherwise in ascending order
     * @return  mixed
     */
    public function select($key, array $where = array(), $orderBy = array(), $offset = 0, $limit = 0, $desc = array());

    /**
     * Update a row or cell.
     *
     * @param   string|\Yana\Db\Queries\Update  $key    the address of the row that should be updated
     * @param   mixed                           $value  value
     * @return  \Yana\Db\IsConnection
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when either the given $key or $value is invalid
     * @throws  \Yana\Core\Exceptions\NotWriteableException     when the table or database is locked
     */
    public function update($key, $value = array());

    /**
     * Update or insert row.
     *
     * @param   string|\Yana\Db\Queries\Insert  $key    the address of the row that should be inserted|updated
     * @param   mixed                           $value  value
     * @return  \Yana\Db\IsConnection
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the query is neither an insert, nor an update statement
     * @throws  \Yana\Core\Exceptions\NotWriteableException     when the table or database is locked
     */
    public function insertOrUpdate($key, $value = array());

    /**
     * Insert $value at position $key.
     *
     * @param   string|\Yana\Db\Queries\Insert  $key  the address of the row that should be inserted
     * @param   array                           $row  associative array of values
     * @return  \Yana\Db\IsConnection
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when either $key or $value is invalid
     * @throws  \Yana\Core\Exceptions\NotWriteableException     when the table or database is locked
     */
    public function insert($key, array $row = array());

    /**
     * Remove one row.
     *
     * @param   string|\Yana\Db\Queries\Delete  $key    the address of the row that should be removed
     * @param   array            $where  where clause
     * @param   int              $limit  maximum number of rows to remove
     * @return  \Yana\Db\IsConnection
     * @throws  \Yana\Core\Exceptions\NotWriteableException  when the table or database is locked
     */
    public function remove($key, array $where = array(), $limit = 1);

    /**
     * Get the number of entries inside a table
     *
     * Returns 0 if the table is empty or does not exist.
     *
     * @param   string|\Yana\Db\Queries\SelectCount  $table  name of a table
     * @param   array                                $where  optional where clause
     * @return  int
     */
    public function length($table, array $where = array());

    /**
     * Check wether a certain table has no entries
     *
     * @param   string  $table  name of a table
     * @return  bool
     */
    public function isEmpty($table);

    /**
     * Check wether a certain key exists.
     *
     * @param   string|\Yana\Db\Queries\SelectExist  $key    adress to check
     * @param   array                 $where  optional where clause
     * @return  bool
     */
    public function exists($key, array $where = array());

    /**
     * Check wether the current database is readonly.
     *
     * @return  bool
     */
    public function isWriteable();

    /**
     * Rollback the current transaction
     *
     * @see  AbstractConnection::reset()
     */
    public function rollback();

    /**
     * Send a sql-statement directly to the database driver API.
     *
     * This is meant to send one single SQL statement at a time.
     * If you want to send a sequence of statements, call this function multiple times.
     *
     * @param   string  $sqlStmt  one SQL statement (or a query object) to execute
     * @param   int     $offset   the row to start from
     * @param   int     $limit    the maximum numbers of rows in the resultset
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException if the SQL statement is not valid
     */
    public function sendQueryString($sqlStmt, $offset = 0, $limit = 0);

    /**
     * Send a sql-statement directly to the database driver API.
     *
     * @param   \Yana\Db\Queries\AbstractQuery  $sqlStmt  one SQL statement (or a query object) to execute
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException if the SQL statement is not valid
     */
    public function sendQueryObject(\Yana\Db\Queries\AbstractQuery $sqlStmt);

    /**
     * Import SQL from a file.
     *
     * The input parameter $sqlFile can wether be filename,
     * or a numeric array of SQL statements.
     *
     * Returns bool(true) on success or bool(false) on error.
     * Note that the statements are executed within a transaction.
     *
     * @param   string|array  $sqlFile filename which contain the SQL statments or an nummeric array of SQL statments.
     * @return  bool
     */
    public function importSQL($sqlFile);

    /**
     * Returns the quoted database identifier as a string.
     *
     * @param   mixed  $value  name of database object
     * @return  string
     */
    public function quoteId($value): string;

    /**
     * Returns the quoted value as a string.
     *
     * @param   mixed  $value  name of database object
     * @return  string
     */
    public function quote($value): string;

}

?>