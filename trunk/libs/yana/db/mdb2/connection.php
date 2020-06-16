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

namespace Yana\Db\Mdb2;

/**
 * <<decorator>> A database abstraction api, that uses PEAR MDB2.
 *
 * @package     yana
 * @subpackage  db
 */
class Connection extends \Yana\Db\AbstractConnection
{

    /**
     * @var  \Yana\Db\IsDriver
     */
    private $_driver = null;

    /**
     * @var  \Yana\Db\Helpers\SqlKeywordChecker
     */
    private $_sqlKeywordChecker = null;

    /**
     * @var  array
     */
    private $_dsn = array();

    /**
     * Create a new instance.
     *
     * Each database connection depends on a schema file describing the database.
     * These files are to be found in config/db/*.db.xml
     *
     * @param   \Yana\Db\Ddl\Database              $schema  schema name or schema in database definition language
     * @param   \Yana\Db\Mdb2\IsConnectionFactory  $server  Connection to a database server
     * @throws  \Yana\Db\Mdb2\PearDbException      when Pear MDB2 is not available
     * @throws  \Yana\Db\ConnectionException       when connection to database failed
     */
    public function __construct(\Yana\Db\Ddl\Database $schema, \Yana\Db\Mdb2\IsConnectionFactory $server = null)
    {
        // fall back to default connection
        if (is_null($server)) {
            $server = new \Yana\Db\Mdb2\ConnectionFactory(); // may throw \Yana\Db\ConnectionException
        }

        // open database connection
        $this->_setDriver($server->getConnection());
        $this->_dsn = $server->getDsn();

        parent::__construct($schema);
    }

    /**
     * Wrap and set the database driver.
     *
     * @param   \MDB2_Driver_Common  $driver  MDB2 database driver
     * @return  $this
     */
    protected function _setDriver(\MDB2_Driver_Common $driver)
    {
        $this->_driver = new \Yana\Db\Mdb2\Driver($driver);
        return $this;
    }

    /**
     * Get instance of SQL keyword checker.
     *
     * @return  \Yana\Db\Helpers\SqlKeywordChecker
     * @codeCoverageIgnore
     */
    protected function _getSqlKeywordChecker()
    {
        if (!isset($this->_sqlKeywordChecker)) {
            $this->_sqlKeywordChecker = \Yana\Db\Helpers\SqlKeywordChecker::createFromApplicationDefault();
        }
        return $this->_sqlKeywordChecker;
    }

    /**
     * Returns the name of the chosen DBMS as a lower-cased string.
     *
     * @return  string
     */
    public function getDBMS(): string
    {
        $dbms = \Yana\Db\DriverEnumeration::GENERIC;
        if (!empty($this->_dsn['DBMS'])) {
            $dbms = strtolower($this->_dsn['DBMS']);
        }
        return \Yana\Db\DriverEnumeration::mapAliasToDriver($dbms);
    }

    /**
     * Compare with another object.
     *
     * Returns bool(true) if this object and $anotherObject
     * are equal and bool(false) otherwise.
     *
     * Two instances are considered equal if and only if
     * they are both objects of the same class and they both use equal database connections and schema.
     *
     * @param   \Yana\Core\IsObject $anotherObject  another object to compare
     * @return  string
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        return (bool) parent::equals($anotherObject) && $anotherObject instanceof $this
            && $this->_driver->equals($anotherObject->_driver);
    }

    /**
     * Send a sql-statement directly to the PEAR database API, bypassing this API.
     *
     * Note: for security reasons this only sends one single SQL statement at a time.
     * This is done by checking the input for a semicolon followed by anything but
     * whitespace. If such input is found, an exception is thrown.
     *
     * While bypassing the API leaves nearly all of the input checking to you, this
     * is meant to prevent at least a minimum of the common SQL injection attacks.
     * A known attack is to try to terminate a current statement with ';' and afterwards
     * "inject" a second statement. The most common attack vector is unchecked form data.
     *
     * If you want to send a sequence of statements, call this function multiple times.
     *
     * The function will return bool(false) if the database connection or the
     * PEAR API is not available and otherwise will whatever PEAR sends back as the
     * result of your statement.
     *
     * @param   string  $sqlStmt  one SQL statement (or a query object) to execute
     * @param   int     $offset   the row to start from
     * @param   int     $limit    the maximum numbers of rows in the resultset
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Db\Queries\Exceptions\QueryException if the SQL statement is not valid
     */
    public function sendQueryString(string $sqlStmt, int $offset = 0, int $limit = 0): \Yana\Db\IsResult
    {
        assert($offset >= 0, 'Invalid argument $offset. Must be a positive integer.');
        assert($limit >= 0, 'Invalid argument $limit. Must be a positive integer.');

        /* Add this line for debugging purposes:
         * error_log($sqlStmt . " LIMIT $offset, $limit\n", 3, 'test.log');
         */

        // security check
        if (preg_match("/;[\s\-]*(?:select|insert|delete|update|create|alter|grant|revoke).*$/is", $sqlStmt)) {
            $message = "A semicolon has been found in the current input '{$sqlStmt}', " .
                "indicating multiple queries.\n\t\t As this might be the result of a hacking attempt " .
                "it is prohibited for security reasons and the queries won't be executed.";
            throw new \Yana\Db\Queries\Exceptions\SecurityException($message);
        }

        $connection = $this->_getDriver();
        $mdb2Result = $connection->sendQueryString($sqlStmt, $limit, $offset);
        assert($mdb2Result instanceof \Yana\Db\IsResult);

        return $mdb2Result;
    }

    /**
     * Send a sql-statement directly to the PEAR database API.
     *
     * This only sends one single SQL statement.
     * If you want to send a sequence of statements, call this function multiple times.
     *
     * The function will return bool(false) if the database connection or the
     * PEAR API is not available and otherwise will whatever PEAR sends back as the
     * result of your statement.
     *
     * @param   \Yana\Db\Queries\AbstractQuery  $query  one SQL statement (or a query object) to execute
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Db\Queries\Exceptions\QueryException if the SQL statement is not valid
     */
    public function sendQueryObject(\Yana\Db\Queries\AbstractQuery $query)
    {
        $offset = $query->getOffset();
        $limit = $query->getLimit();
        $sqlStmt = (string) $query;

        return $this->sendQueryString($sqlStmt, $offset, $limit);
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
     * An error is encountered and a notice is issued, if:
     * <ul>
     * <li> the file does not exist or is not readable </li>
     * <li> the $sqlFile parameter is empty </li>
     * <li> the database connection is not available </li>
     * <li> the parameter "readonly" on the database structure file is set to "true" </li>
     * <li> at least one database statement failed (does not issue a notice) </li>
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
    public function importSQL($sqlFile): bool
    {
        assert(is_string($sqlFile) || is_array($sqlFile), 'Wrong argument type: $sqlFile. String or array expected');
        assert(!empty($sqlFile), 'Argument \$sqlFile must not be empty.');

        if ($this->getSchema()->isReadonly()) {
            throw new \Yana\Core\Exceptions\NotWriteableException("Database is readonly. SQL import aborted.", \Yana\Log\TypeEnumeration::INFO);
        }

        $transaction = $this->_getTransaction();
        if (!$transaction->isEmpty()) {
            $message = "Cannot import SQL statements.\n\t\tThere is a pending transaction that needs to be committed " .
                "before proceeding.";
            throw new \Yana\Db\DatabaseException($message, \Yana\Log\TypeEnumeration::INFO);
        }

        $success = true;
        if (!is_array($sqlFile)) { // input is string

            if (!is_readable("$sqlFile")) {
                throw new \Yana\Core\Exceptions\NotReadableException("The file '{$sqlFile}' is not readable.", \Yana\Log\TypeEnumeration::INFO);
            }
            $rawData = file_get_contents($sqlFile);
            // remove comments and line breaks
            $rawData = preg_replace("/\s*\#[^\n]*/i", "", $rawData);
            $rawData = preg_replace("/\s*\-\-[^\n]*/i", "", $rawData);
            $rawData = preg_replace("/;\s*\n\s*/i", "[NEXT_COMMAND]", $rawData);
            $rawData = preg_replace("/\s/", " ", $rawData);
            if (empty($rawData)) {
                \Yana\Log\LogManager::getLogger()->addLog("SQL import canceled. File is empty.", \Yana\Log\TypeEnumeration::INFO, $sqlFile);
                return false;
            }
            // add items
            $sqlFile = explode("[NEXT_COMMAND]", $rawData);
        }
        assert(\is_array($sqlFile), 'Invalid result. Array expected for $sqlFile');

        assert(!isset($sqlStatement), 'Cannot redeclare var $sqlStatement');
        foreach ($sqlFile as $sqlStatement)
        {
            $transaction->sql(new \Yana\Db\Queries\Sql($this, (string) $sqlStatement));
        }
        unset($sqlStatement);

        try {
            $this->commit(); // may throw exception
        } catch (\Exception $e) {
            $success = false;
            unset($e);
        }
        $message = "SQL import " . (($success) ? "was successful." : "has failed");
        $level = \Yana\Log\TypeEnumeration::INFO;
        \Yana\Log\LogManager::getLogger()->addLog($message, $level, $sqlFile);
        return (bool) $success;
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
    public function quoteId($value): string
    {
        assert(is_string($value), 'Wrong argument type for argument 1. String expected.');
        $value = (string) $value;

        // @codeCoverageIgnoreStart
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
            case \Yana\Db\Mdb2\DriverEnumeration::MYSQL:
            case \Yana\Db\Mdb2\DriverEnumeration::POSTGRESQL:
            case \Yana\Db\Mdb2\DriverEnumeration::MSSQL:
            case 'mysql':
            case 'postgresql':
                return $this->_getDriver()->quoteIdentifier($value);

            /* quote only where necessary
             *
             * Note that "isSqlKeyword()" has O(log(n)) running time.
             */
            default:
                if (strpos($value, ' ') !== false || $this->_getSqlKeywordChecker()->isSqlKeyword($value) === true) {
                    return $this->_getDriver()->quoteIdentifier($value);
                }

                return $value;
        } // end switch
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get database connection.
     *
     * @return  \Yana\Db\IsDriver
     */
    protected function _getDriver(): \Yana\Db\IsDriver
    {
        if (!isset($this->_driver)) {
            // @codeCoverageIgnoreStart
            $dbServer = new \Yana\Db\Mdb2\ConnectionFactory($this->_dsn);
            $this->_setDriver($dbServer->getConnection());
            // @codeCoverageIgnoreEnd
        }
        return $this->_driver;
    }

}

?>