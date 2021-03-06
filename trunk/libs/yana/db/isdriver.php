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

namespace Yana\Db;

/**
 * <<interface>> Implement this for adapters/wrappers for database drivers.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsDriver extends \Yana\Core\IsObject
{

    /**
     * begin transaction.
     *
     * This deactives auto-commit, so the following statements will wait for commit or rollback.
     *
     * @return  bool
     */
    public function beginTransaction();

    /**
     * rollback current transaction.
     *
     * @return  bool
     */
    public function rollback();

    /**
     * commit current transaction.
     *
     * @return  bool
     */
    public function commit();

    /**
     * get list of databases.
     *
     * @return  array
     */
    public function listDatabases();

    /**
     * get list of tables in current database.
     *
     * @return  array
     */
    public function listTables();

    /**
     * get list of functions.
     *
     * @return  array
     */
    public function listFunctions();

    /**
     * get list of sequences.
     *
     * @param   string  $database  dummy for compatibility
     * @return  array
     */
    public function listSequences($database = null);

    /**
     * get list of columns.
     *
     * @param   string  $table  table name
     * @return  array
     */
    public function listTableFields($table);

    /**
     * get list of indexes.
     *
     * @param   string  $table  table name
     * @return  array
     */
    public function listTableIndexes($table);

    /**
     * Execute a single query.
     *
     * @param   \Yana\Db\Queries\AbstractQuery  $dbQuery  query object
     * @return  \Yana\Db\IsResult
     * @since   2.9.3
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given query is invalid
     */
    public function sendQueryObject(\Yana\Db\Queries\AbstractQuery $dbQuery);

    /**
     * Execute a single query.
     *
     * @param   string  $sqlStmt  SQL statement
     * @param   int     $limit    the maximum number of rows in the resultset
     * @param   int     $offset   the row to start from
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException     if the query is invalid or could not be parsed
     * @throws  \Yana\Db\Queries\Exceptions\NotSupportedException  if PEAR SQL-Parser is not installed or not found
     */
    public function sendQueryString($sqlStmt, $limit = 0, $offset = 0);

    /**
     * quote a value.
     *
     * Returns the quoted values as a string
     * surrounded by double-quotes.
     *
     * @param   mixed  $value value too qoute
     * @return  string
     * @ignore
     */
    public function quote($value);

    /**
     * quote an identifier.
     *
     * Returns the quotes Id as a string
     * surrounded by double-quotes.
     *
     * @param   string  $value  value
     * @return  string
     * @ignore
     */
    public function quoteIdentifier($value);

}

?>