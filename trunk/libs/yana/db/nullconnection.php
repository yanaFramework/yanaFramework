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

namespace Yana\Db;

/**
 * Mock database API for unit tests.
 *
 * @package     yana
 * @subpackage  db
 * @ignore
 */
class NullConnection extends \Yana\Core\StdObject implements \Yana\Db\IsConnection
{

    /**
     * @var  string
     */
    private $_dbms = \Yana\Db\DriverEnumeration::GENERIC;

    /**
     * @var  \Yana\Db\Ddl\Database 
     */
    private $_schema = null;

    /**
     * @var \Yana\Db\Helpers\IsSqlKeywordChecker
     */
    private $_sqlKeywordChecker = null;

    /**
     * Initialize schema.
     * 
     * @param  \Yana\Db\Ddl\Database                 $schema  optional schema
     * @param  string                                $dbms    optional DBMS
     * @param  \Yana\Db\Helpers\IsSqlKeywordChecker  $sqlKeywordChecker  a class that checks if a given string is a reserved SQL keyword
     */
    public function __construct(\Yana\Db\Ddl\Database $schema = null, string $dbms = \Yana\Db\DriverEnumeration::GENERIC, \Yana\Db\Helpers\IsSqlKeywordChecker $sqlKeywordChecker = null)
    {
        // @codeCoverageIgnoreStart
        if (!is_null($sqlKeywordChecker)) {
            $this->_sqlKeywordChecker = $sqlKeywordChecker;
        }
        // @codeCoverageIgnoreEnd
        if (is_null($schema)) {
            $schema = new \Yana\Db\Ddl\Database('null');
        }
        $this->_schema = $schema;
        $this->_dbms = $dbms;
    }

    /**
     * Get database schema.
     *
     * @return  \Yana\Db\Ddl\Database
     */
    public function getSchema(): \Yana\Db\Ddl\Database
    {
        return $this->_schema;
    }

    /**
     * Set the name of the chosen DBMS as a lower-cased string.
     *
     * @param   string  $dbms  chosen DBMS
     * @return  $this
     */
    public function setDBMS(string $dbms)
    {
        $this->_dbms = strtolower($dbms);
        return $this;
    }

    /**
     * Returns the name of the chosen DBMS as a lower-cased string.
     *
     * @return  string
     */
    public function getDBMS(): string
    {
        return $this->_dbms;
    }

    /**
     * Returns a class that checks if a given string is a reserved SQL keyword.
     *
     * We need this functionality for quoting the names of IBM DB2 database object names.
     *
     * @return  \Yana\Db\Helpers\IsSqlKeywordChecker
     */
    protected function _getSqlKeywordChecker()
    {
        if (!isset($this->_sqlKeywordChecker)) {
            // @codeCoverageIgnoreStart
            $this->_sqlKeywordChecker = \Yana\Db\Helpers\SqlKeywordChecker::createFromApplicationDefault();
            // @codeCoverageIgnoreEnd
        }
        return $this->_sqlKeywordChecker;
    }

    /**
     * Commits the current transaction.
     *
     * @return  $this
     */
    public function commit()
    {
        return $this;
    }

    /**
     * Get values from the database.
     *
     * @param   string|\Yana\Db\Queries\Select  $key      the address of the value(s) to retrieve
     * @param   array                           $where    where clause
     * @param   array                           $orderBy  a list of columns to order the resultset by
     * @param   int                             $offset   the number of the first result to be returned
     * @param   int                             $limit    maximum number of results to return
     * @param   bool|array                      $desc     if true results will be ordered in descending,
     *                                                    otherwise in ascending order
     * @return  mixed
     */
    public function select($key, array $where = array(), $orderBy = array(), int $offset = 0, int $limit = 0, $desc = array())
    {
        return array();
    }

    /**
     * Update a row or cell.
     *
     * @param   string|\Yana\Db\Queries\Update  $key    the address of the row that should be updated
     * @param   mixed                           $value  value
     * @return  $this
     */
    public function update($key, $value = array())
    {
        return $this;
    }

    /**
     * Update or insert row.
     *
     * @param   string  $key    the address of the row that should be inserted|updated
     * @param   mixed   $value  value
     * @return  $this
     */
    public function insertOrUpdate(string $key, $value = array())
    {
        return $this;
    }

    /**
     * Insert $value at position $key.
     *
     * @param   string|\Yana\Db\Queries\Insert  $key  the address of the row that should be inserted
     * @param   array                           $row  associative array of values
     * @return  $this
     */
    public function insert($key, array $row = array())
    {
        return $this;
    }

    /**
     * Remove one row.
     *
     * @param   string|\Yana\Db\Queries\Delete  $key    the address of the row that should be removed
     * @param   array            $where  where clause
     * @param   int              $limit  maximum number of rows to remove
     * @return  $this
     */
    public function remove($key, array $where = array(), int $limit = 1)
    {
        return $this;
    }

    /**
     * Get the number of entries inside a table
     *
     * Returns 0 if the table is empty or does not exist.
     *
     * @param   string|\Yana\Db\Queries\SelectCount  $table  name of a table
     * @param   array                                $where  optional where clause
     * @return  int
     */
    public function length($table, array $where = array()): int
    {
        return 0;
    }

    /**
     * Check wether a certain table has no entries
     *
     * @param   string  $table  name of a table
     * @return  bool
     */
    public function isEmpty(string $table): bool
    {
        return true;
    }

    /**
     * Check wether a certain key exists.
     *
     * @param   string|\Yana\Db\Queries\SelectExist  $key    adress to check
     * @param   array                 $where  optional where clause
     * @return  bool
     */
    public function exists($key, array $where = array()): bool
    {
        return false;
    }

    /**
     * Check wether the current database is readonly.
     *
     * @return  bool
     */
    public function isWriteable(): bool
    {
        return true;
    }

    /**
     * Rollback the current transaction
     *
     * @return  $this
     */
    public function rollback()
    {
        return $this;
    }

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
    public function sendQueryString($sqlStmt, int $offset = 0, int $limit = 0)
    {
        return new \Yana\Db\FileDb\Result();
    }

    /**
     * Send a sql-statement directly to the database driver API.
     *
     * @param   \Yana\Db\Queries\AbstractQuery  $sqlStmt  one SQL statement (or a query object) to execute
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException if the SQL statement is not valid
     */
    public function sendQueryObject(\Yana\Db\Queries\AbstractQuery $sqlStmt)
    {
        return new \Yana\Db\FileDb\Result(array());
    }

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
    public function importSQL($sqlFile): bool
    {
        return true;
    }

    /**
     * Returns the quoted database identifier as a string.
     *
     * @param   mixed  $value  name of database object
     * @return  string
     */
    public function quoteId($value): string
    {
        if ($this->_needsQuoting($value)) {
            switch ($this->getDBMS())
            {
                case \Yana\Db\DriverEnumeration::MYSQL:
                    $leftDelimiter = $rightDelimiter = '`';
                    break;
                case \Yana\Db\DriverEnumeration::MSSQL:
                    $leftDelimiter = '[';
                    $rightDelimiter = ']';
                    break;
                default:
                    $leftDelimiter = '"';
                    $rightDelimiter = '"';
            }
            $value = str_replace($leftDelimiter, $leftDelimiter . $leftDelimiter, $value);
            if ($rightDelimiter !== $leftDelimiter) {
                $value = str_replace($rightDelimiter, $rightDelimiter . $rightDelimiter, $value);
            }
            return $leftDelimiter . $value . $rightDelimiter;
        } else {
            return $value;
        }
    }

    /**
     * Returns the quoted database identifier as a string.
     *
     * @param   mixed  $value  name of database object
     * @return  string
     */
    public function quote($value): string
    {
        $valueConverter = new \Yana\Db\Helpers\ValueConverter();
        return $valueConverter->convertValueToString($value, \Yana\Db\Ddl\ColumnTypeEnumeration::STRING);
    }

    /**
     * Returns bool(true) if the ID should be quoted.
     *
     * @param   string  $id  name of database object
     * @return  bool
     */
    private function _needsQuoting(string $id): bool
    {
        $isDb2Keyword = ($this->getDBMS() === \Yana\Db\DriverEnumeration::DB2 && $this->_getSqlKeywordChecker()->isSqlKeyword($id));
        $doesNeedQuoting = preg_match('/^[a-z][\w\-]*$/ui', $id) !== 1;
        return $isDb2Keyword || $doesNeedQuoting;
    }

}

?>