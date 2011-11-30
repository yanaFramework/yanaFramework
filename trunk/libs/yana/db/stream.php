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
 * <<decorator>> A database abstraction api, that uses PEAR MDB2.
 *
 * @package     yana
 * @subpackage  db
 */
class Connection extends \Yana\Db\AbstractConnection
{

    /**
     * @var  array
     */
    private $_reservedSqlKeywords = null;

    /**
     * Create a new instance.
     *
     * Each database connection depends on a schema file describing the database.
     * These files are to be found in config/db/*.db.xml
     *
     * @param   \Yana\Db\Ddl\Database       $schema  schema name or schema in database definition language
     * @param   \Yana\Db\ConnectionFactory  $server  Connection to a database server
     * @throws  \Yana\Db\ConnectionException  when connection to database failed
     */
    public function __construct(\Yana\Db\Ddl\Database $schema, \Yana\Db\ConnectionFactory $server = null)
    {
        // fall back to default connection
        if (is_null($server)) {
            $server = new \Yana\Db\ConnectionFactory();
        }

        // open database connection
        $this->database = $server->getConnection();
        $this->dsn = $server->getDsn();

        // Error: Unable to connect to database
        if (!\MDB2::isConnection($this->database)) {
            throw new \Yana\Db\ConnectionException();
        }

        parent::__construct($schema);
    }

    /**
     * Send a sql-statement directly to the PEAR database API, bypassing this API.
     *
     * Note: for security reasons this only sends one single SQL statement at a time.
     * This is done by checking the input for a semicolon followed by anything but
     * whitespace. If such input is found, an E_USER_WARNING is issued and the
     * function will return bool(false).
     *
     * While bypassing the API leaves nearly all of the input checking to you, this
     * is meant to prevent at least a minimum of the common SQL injection attacks.
     * A known attack is to try to terminate a current statement with ';' and afterwards
     * "inject" their own stuff as a second statement. The common attack vector usually
     * is unchecked form data.
     *
     * If you want to send a sequence of statements, call this function multiple times.
     *
     * The function will return bool(false) if the database connection or the
     * PEAR API is not available and otherwise will whatever PEAR sends back as the
     * result of your statement.
     *
     * Note: when database usage is disabled via the administrator's menu,
     * the PEAR-DB API can not be used and this function will return bool(false).
     *
     * The $offset and $limit arguments became available in version 2.8.8
     *
     * Since version 2.9.3 this function has a second synopsis:
     * You may provide a DbQuery object instead of the SQL statement.
     *
     * <code>
     * $connection->query($sqlStmt, $offset, $limit);
     * // 2nd synopsis
     * $connection->query($dbQuery);
     * </code>
     *
     * Note that when providing the DbQuery object, the $limit and $offset arguments are
     * ignored.
     *
     * @param   string|\Yana\Db\Queries\AbstractQuery  $sqlStmt  one SQL statement (or a query object) to execute
     * @param   int             $offset   the row to start from
     * @param   int             $limit    the maximum numbers of rows in the resultset
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException if the SQL statement is not valid
     */
    public function query($sqlStmt, $offset = 0, $limit = 0)
    {
        assert('is_int($offset) && $offset >= 0; // Invalid argument $offset. Must be a positive integer.');
        assert('is_int($limit) && $limit >= 0; // Invalid argument $limit. Must be a positive integer.');
        /*
         * 1) check sql statement
         */
        if (is_object($sqlStmt) && $sqlStmt instanceof \Yana\Db\Queries\AbstractQuery) {
            $offset = $sqlStmt->getOffset();
            $limit = $sqlStmt->getLimit();
            $sqlStmt = (string) $sqlStmt;
            /*
             * Add this line for debugging purposes
             *
             * error_log($sqlStmt . " LIMIT $offset, $limit\n", 3, 'test.log');
             */
        }
        if (!is_string($sqlStmt)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException('Argument $sqlStmt is expected to be a string.');
        }
        $reg = "/;.*(?:select|insert|delete|update|create|alter|grant|revoke).*$/is";
        if (strpos($sqlStmt, ';') !== false && preg_match($reg, $sqlStmt)) {
            $message = "A semicolon has been found in the current input '{$sqlStmt}', " .
                "indicating multiple queries.\n\t\t As this might be the result of a hacking attempt " .
                "it is prohibited for security reasons and the queries won't be executed.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        $connection = $this->getConnection();
        /*
         * 3) send query to database
         */
        if ($offset > 0 || $limit > 0) {
            $connection->setLimit($limit, $offset);
        }
        return $connection->query($sqlStmt);
    }

    /**
     * import SQL from a file
     *
     * The input parameter $sqlFile can wether be filename,
     * or a numeric array of SQL statements.
     *
     * Returns bool(true) on success or bool(false) on error.
     * Note that the statements are executed within a transaction.
     * If the function fails,
     *
     * An error is encountered and an E_USER_NOTICE is issued, if:
     * <ul>
     * <li> the file does not exist or is not readable </li>
     * <li> the $sqlFile parameter is empty </li>
     * <li> the database connection is not available </li>
     * <li> the parameter "readonly" on the database structure file is set to "true" </li>
     * <li> at least one database statement failed (does not issue an E_USER_NOTICE) </li>
     * <li> there are uncommited statements in the queue </li>
     * </ul>
     *
     * @param   string|array  $sqlFile filename which contain the SQL statments or an nummeric array of SQL statments.
     * @return  bool
     * @throws  \Yana\Db\DatabaseException                      when database has pending transaction
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when argument $sqlFile has an invalid value
     * @throws  \Yana\Core\Exceptions\NotReadableException      when SQL file does not exist or is not readable
     * @throws  \Yana\Core\Exceptions\NotWriteableException     when database is not writeable
     */
    public function importSQL($sqlFile)
    {
        assert('is_string($sqlFile) || is_array($sqlFile); // Wrong argument type: $sqlFile. String or array expected');
        assert('!empty($sqlFile); // Argument \$sqlFile must not be empty.');
        if (!empty($this->_queue)) {
            $message = "Cannot import SQL statements.\n\t\tThere is a pending transaction that needs to be committed " .
                "before proceeding.";
            throw new \Yana\Db\DatabaseException($message, E_USER_NOTICE);
        }
        if ($this->_isWriteable() !== true) {
            $message = "Database connection is not available. Check your connection settings.";
            throw new \Yana\Core\Exceptions\NotWriteableException($message, E_USER_NOTICE);
        }
        if ($this->getSchema()->isReadonly()) {
            throw new \Yana\Core\Exceptions\NotWriteableException("Database is readonly. SQL import aborted.", E_USER_NOTICE);
        }

        // input is array
        if (is_array($sqlFile)) {
            $this->_queue = $sqlFile;
            try {
                $success = $this->write();
            } catch (\Exception $e) {
                $success = false;
            }
            if ($success !== false) {
                \Yana\Log\LogManager::getLogger()->addLog("SQL import was successful.", E_USER_NOTICE, $sqlFile);
                return true;
            } else {
                \Yana\Log\LogManager::getLogger()->addLog("SQL import failed.", E_USER_NOTICE, $sqlFile);
                return false;
            }

        } else { // input is string

            if (!is_readable("$sqlFile")) {
                throw new \Yana\Core\Exceptions\NotReadableException("The file '{$sqlFile}' is not readable.", E_USER_NOTICE);
            }
            $raw_data = file_get_contents($sqlFile);
            // remove comments and line breaks
            $raw_data = preg_replace("/\s*\#[^\n]*/i", "", $raw_data);
            $raw_data = preg_replace("/\s*\-\-[^\n]*/i", "", $raw_data);
            $raw_data = preg_replace("/;\s*\n\s*/i", "[NEXT_COMMAND]", $raw_data);
            $raw_data = preg_replace("/\s/", " ", $raw_data);
            if (empty($raw_data)) {
                \Yana\Log\LogManager::getLogger()->addLog("SQL import canceled. File is empty.", E_USER_NOTICE, $sqlFile);
                return false;
            }
            // add items
            $this->_queue = explode("[NEXT_COMMAND]", $raw_data);
            if ($this->write() !== false) {
                \Yana\Log\LogManager::getLogger()->addLog("SQL import was successful.", E_USER_NOTICE, $raw_data);
                return true;
            } else {
                \Yana\Log\LogManager::getLogger()->addLog("SQL import failed.", E_USER_NOTICE, $raw_data);
                return false;
            }
        }
    }

    /**
     * isError
     *
     * @param   mixed   $result  result
     * @return  bool
     * @ignore
     */
    public function isError($result)
    {
        if ($result instanceof \Yana\Db\FileDb\Result) {
            return $result->isError();
        } else {
            return \MDB2::isError($result);
        }
    }

    /**
     * smart id quoting
     *
     * Returns the quotes Id as a string
     * surrounded by delimiters, depending on
     * the DBMS selected.
     *
     * Implements a blacklist approach to automated quoting.
     * This will only quote such ids, which are a known
     * SQL keyword.
     *
     * @param   mixed  $value  value
     * @return  string
     * @ignore
     */
    public function quoteId($value)
    {
        assert('is_string($value); // Wrong argument type for argument 1. String expected.');
        $value = (string) $value;

        /*
         * check DBMS
         *
         * In general, quoting is required, where the identifier is identical to a
         * reserved keyword of the database software.
         *
         * Under other circumstances it depends on the DMBS. Either you "may",
         * or you "should not", or you "must not" quote the id - depending on the
         * DBMS you use.
         *
         * So this will take the shortcut for DBMS where you don't need to care,
         * while taking the long path only for all the other DBMS, where this is required.
         */
        switch ($this->getDBMS())
        {
            // always quote
            case 'mysql':
            case 'mysqli':
            case 'postgresql':
            case 'mssql':
                return $this->getConnection()->quoteIdentifier($value);
            break;
            /* quote only where necessary
             *
             * Note that "isSqlKeyword()" has O(log(n)) running time.
             */
            default:
                if (strpos($value, ' ') !== false || $this->_isSqlKeyword($value) === true) {
                    return $this->getConnection()->quoteIdentifier($value);

                } else {
                    return $value;
                }
            break;
        } // end switch
    }

    /**
     * returns true if $name is a known SQL keyword and false otherwise
     *
     * implements quick-search
     * + assume that the input is sorted
     * + assume that the input is upper case
     *
     * this algorithm has O(log(n)) running time
     *
     * @param   string  $name  SQL keyword
     * @return  bool
     */
    private function _isSqlKeyword($name)
    {
        assert('is_string($value); // Wrong argument type for argument 1. String expected.');

        if (is_null($this->_reservedSqlKeywords)) {
            global $YANA;
            /* Load list of reserved SQL keywords (required for smart id quoting) */
            if (isset($YANA)) {
                $file = $YANA->getResource('system:/config/reserved_sql_keywords.file');
                $this->_reservedSqlKeywords = file($file->getPath());
            } else {
                $this->_reservedSqlKeywords = array();
            }
            if (!is_array($this->_reservedSqlKeywords)) {
                $this->_reservedSqlKeywords = array();
            }
        } elseif (empty($this->_reservedSqlKeywords)) {
            return false;
        }

        $name = mb_strtoupper($name);
        return (bool) (\Yana\Util\Hashtable::quickSearch($this->_reservedSqlKeywords, $name) !== false);
    }

    /**
     * get database connection
     *
     * @return  \MDB2_Driver_Common
     * @ignore
     */
    protected function getConnection()
    {
        if (!isset($this->database)) {
            $dbServer = new \Yana\Db\ConnectionFactory($this->dsn);
            $this->database = $dbServer->getConnection();
        }
        return $this->database;
    }

}

?>