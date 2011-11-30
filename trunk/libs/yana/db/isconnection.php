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
     * Get the DSN.
     *
     * This function returns an associative array containing
     * information on the current connection or bool(false) on error.
     *
     * @return  array
     * @see     \Yana\Db\ConnectionFactory::getDsn()
     */
    public function getDsn();

    /**
     * Returns the name of the chosen DBMS as a lower-cased string.
     *
     * @return  string
     */
    public function getDBMS();

    /**
     * Commits the current transaction.
     *
     * @return  bool
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
     * @param   bool             $desc     if true results will be ordered in descending,
     *                                     otherwise in ascending order
     * @return  mixed
     */
    public function select($key, array $where = array(), $orderBy = array(), $offset = 0, $limit = 0, $desc = false);

    /**
     * Update a row or cell.
     *
     * @param   string|\Yana\Db\Queries\Update  $key    the address of the row that should be updated
     * @param   mixed                           $value  value
     * @return  bool
     */
    public function update($key, $value = array());

    /**
     * Update or insert row.
     *
     * @param   string|\Yana\Db\Queries\Insert  $key    the address of the row that should be inserted|updated
     * @param   mixed                           $value  value
     * @return  bool
     */
    public function insertOrUpdate($key, $value = array());

    /**
     * Insert $value at position $key.
     *
     * @param   string|\Yana\Db\Queries\Insert  $key    the address of the row that should be inserted
     * @param   mixed            $value  value
     * @return  bool
     */
    public function insert($key, $value = array());

    /**
     * Remove one row.
     *
     * @param   string|\Yana\Db\Queries\Delete  $key    the address of the row that should be removed
     * @param   array            $where  where clause
     * @param   int              $limit  maximum number of rows to remove
     * @return  bool
     */
    public function remove($key, array $where = array(), $limit = 1);

    /**
     * Join the resultsets for two tables.
     *
     * @param   string $table1  name of the table to join another one with
     * @param   string $table2  name of another table to join table1 with
     *          (when omitted will remove all previously set joins from table1)
     * @param   string $key1    name of the foreign key in table1 that references table2
     *          (when omitted the API will look up the key in the structure file)
     * @param   string $key2    name of the key in table2 that is referenced from table1
     *          (may be omitted if it is the primary key)
     */
    public function join($table1, $table2 = "", $key1 = "", $key2 = "");

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

}

?>