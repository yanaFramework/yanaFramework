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
 * <<abstract>> <<decorator>> Database API.
 *
 * Base class for database connection decorators.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractConnection extends \Yana\Core\Object implements \Serializable, \Yana\Db\IsConnection
{

    /**
     * @var  \MDB2_Driver_Common
     * @ignore
     */
    protected $database = null;

    /**
     * @var  string
     * @ignore
     */
    protected $name = "";

    /**
     * @var  array
     * @ignore
     */
    protected $dsn = array();

    /**
     * @var  array
     */
    private $_cache = array();

    /**
     * @var  array
     */
    private $_lastModified = array();

    /**
     * @var  string
     */
    private $_lastModifiedPath = "db_last_modified.tmp";

    /**
     * @var  string
     */
    private static $_tempDir = "cache/";

    /**
     * @var \Yana\Db\Transaction
     */
    protected $_transaction = null;

    /**
     * database schema
     *
     * The database schema that is used in the current session.
     *
     * Please note that you should not change this schema unless
     * you REALLY know what you are doing.
     *
     * @var  \Yana\Db\Ddl\Database
     * @ignore
     */
    protected $schema  = null;

    /**
     * Create a new instance.
     *
     * Each database connection depends on a schema file describing the database.
     * These files are to be found in config/db/*.db.xml
     *
     * @param   \Yana\Db\Ddl\Database  $schema  schema in database definition language
     */
    public function __construct(\Yana\Db\Ddl\Database $schema)
    {
        $this->schema = $schema;
        $this->reset();
    }

    /**
     * Set path to temporary directory.
     *
     * The target directory must be read- and writable.
     *
     * @param  string  $dir  absolute path to temp-directory
     */
    public static function setTempDir($dir)
    {
        assert('is_string($dir); // Invalid argument $dir: string expected');
        self::$_tempDir = $dir . '/';
    }

    /**
     * Get absolute path to temporary directory.
     *
     * The returned path always ends with a slash.
     * Note that this function does not check if the given path is valid.
     *
     * @return  string
     */
    public static function getTempDir()
    {
        return self::$_tempDir;
    }

    /**
     * Get database schema.
     *
     * @return  \Yana\Db\Ddl\Database
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * magic get
     *
     * Returns a database object definition from the schema.
     * If there is none, the function will return NULL.
     * The type of the returned object depends on the selected database object.
     *
     * Note that you can't get an unnamed database object via this function.
     *
     * @param   string  $name  name of a database object
     * @return  \Yana\Db\Ddl\DDL
     */
    public function __get($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');

        return $this->getSchema()->{$name};
    }

    /**
     * Calls a function on the selected database schema and returns the result.
     *
     * @param   string  $name       name
     * @param   array   $arguments  arguments
     * @return  mixed
     */
    public function __call($name, array $arguments)
    {
        assert('is_string($name); // Invalid argument $name: string expected');

        return call_user_func_array(array($this->getSchema(), $name), $arguments);
    }

    /**
     * Returns true if a named object with the given name exists in the database schema.
     *
     * @param   string  $name  name of a database object
     * @return  bool
     */
    public function __isset($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');

        return ($this->__get($name) !== null);
    }

    /**
     * Get the DSN.
     *
     * This function returns an associative array containing
     * information on the current connection or bool(false) on error.
     *
     * @return  array
     * @see     \Yana\Db\ConnectionFactory::getDsn()
     */
    public function getDsn()
    {
        $dsn = false;
        if (is_array($this->dsn)) {
            $dsn = $this->dsn;
        }
        return $dsn;
    }

    /**
     * Returns the name of the chosen DBMS as a lower-cased string.
     *
     * @return  string
     */
    public function getDBMS()
    {
        if (!empty($this->dsn['DBMS'])) {
            $dbms = strtolower($this->dsn['DBMS']);
            switch ($dbms)
            {
                // Mapping aliases (driver names) to real DBMS names
                case 'mysqli':
                    return "mysql";
                break;
                case 'pgsql':
                    return "postgresql";
                break;
                case 'fbsql':
                    return "frontbase";
                break;
                case 'ifx':
                    return "informix";
                break;
                case 'ibase':
                    return "interbase";
                break;
                case 'access':
                    return "msaccess";
                break;
                case 'oci8':
                    return "oracle";
                break;
                // any other
                default:
                    return $dbms;
                break;
            }
        } else {
            return "generic";
        }
    }

    /**
     * Commit current transaction and write all changes to the database.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\NotWriteableException  when the database or table is locked
     */
    public function commit()
    {
        $return = $this->_transaction->commit();
        $this->_transaction = new \Yana\Db\Transaction($this->schema);
        return $return;
    }

    /**
     * get values from the database
     *
     * This returns the values at adress $key
     * starting from $offset and limited to
     * $limit results.
     *
     * The $key parameter has three synopsis.
     * <ul>
     *     <li>
     *         If $key is a string, this parameter is interpreted
     *         as the address of the values you want to retrieve.
     *     </li>
     *     <li>
     *         The argument $key may also be an object of type DbSelect.
     *         If so, no additional parameters need to be present.
     *         This is a shortcut, which e.g. allows you to prepare
     *         a query as an object and reuse it with multiple arguments.
     *     </li>
     * </ul>
     *
     * Since version 2.8.5 the parameter $orderBy has two synopsis.
     * <ul>
     *     <li>
     *         $orderBy is the name of the column in the current table to order the resultset by.
     *     </li>
     *     <li>
     *         $orderBy is a numeric array of strings, where each element
     *         is the name of a column in the current table.
     *         The resultset will get ordered by the values of these columns
     *         in the direction in which they are provided.
     *         This feature became available in version 2.8.5
     *     </li>
     * </ul>
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
     * @param   string|\Yana\Db\Queries\Select  $key      the address of the value(s) to retrieve
     * @param   array            $where    where clause
     * @param   array            $orderBy  a list of columns to order the resultset by
     * @param   int              $offset   the number of the first result to be returned
     * @param   int              $limit    maximum number of results to return
     * @param   bool             $desc     if true results will be ordered in descending,
     *                                     otherwise in ascending order
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when one of the given arguments is not valid
     */
    public function select($key, array $where = array(), $orderBy = array(), $offset = 0, $limit = 0, $desc = false)
    {
        if (is_object($key) && $key instanceof \Yana\Db\Queries\Select) {

            assert('func_num_args() === 1; // Too many arguments. Only 1 argument expected.');
            $selectQuery =& $key;

        } else {

            $queryBuilder = new \Yana\Db\Queries\QueryBuilder($this);
            $selectQuery = $queryBuilder->select($key, $where, $orderBy, $offset, $limit, $desc);
        }

        return $selectQuery->getResults();
    }

    /**
     * Update a row or cell.
     *
     * Update $value at position $key. If $key does not exist, bool(false) is returned.
     * This function returns bool(true) on success and bool(false) on error.
     *
     * Note, that this function does not auto-commit.
     * This means, changes to the database will NOT be saved
     * until you call $AbstractConnection->commit().
     *
     * The argument $key may also be an object of type DbUpdate.
     * If so, no additional parameters need to be present.
     * This is a shortcut, which e.g. allows you to prepare
     * a query as an object and reuse it with multiple arguments.
     *
     * @param   string|\Yana\Db\Queries\Update  $key    the address of the row that should be updated
     * @param   mixed            $value  value
     * @return  bool
     * @name    AbstractConnection::update()
     * @see     AbstractConnection::insertOrUpdate()
     * @see     AbstractConnection::insert()
     * @since   2.9.5
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when either the given $key or $value is invalid
     */
    public function update($key, $value = array())
    {
        if (!$this->_isWriteable()) {
            trigger_error('Operation aborted, not writeable.', E_USER_NOTICE);
            return false;
        }

        if (is_object($key) && $key instanceof \Yana\Db\Queries\Update) { // input is query object

            assert('func_num_args() === 1; // Too many arguments in ' . __METHOD__ . '(). Only 1 argument expected.');
            $updateQuery = $key;

        } else { // input is key address

            /*
             * 2.1) check input
             */
            assert('is_string($key); // wrong argument type for argument 1, string expected');


            if ($key == '') {
                return false;
            } else {
                $key = mb_strtolower("$key");
            }

            $queryBuilder = new \Yana\Db\Queries\QueryBuilder($this);
            $updateQuery = $queryBuilder->update($key, $value);

        } // end if


        // get properties
        $tableName = $updateQuery->getTable();
        $row = $updateQuery->getRow();
        $column = $updateQuery->getColumn();
        $value = $updateQuery->getValues(); // get values by reference

        // check whether the row has been modified since last access
        if (YANA_DB_STRICT && isset($_SESSION['transaction_isolation_created']) &&
            $this->_getLastModified($tableName, $row) > $_SESSION['transaction_isolation_created']) {
            \Yana\Log\LogManager::getLogger()->addLog("The user was trying to save form data, which has changed " .
                "since he accessed the corrsponding form. The operation has been aborted, " .
                "as updating this row would have overwritten changes made by another user.", E_USER_WARNING);
            throw new FormTimeoutWarning();
        }

        /*
         * 3.2.3) check if element is inside an array
         *
         * If true, get the previous array and merge both.
         */
        if ($updateQuery->getExpectedResult() === \Yana\Db\ResultEnumeration::CELL) {
            assert('!isset($arrayAddress); // Cannot redeclare var $arrayAddress');
            $arrayAddress = $updateQuery->getArrayAddress();
            if (!empty($arrayAddress)) {
                assert('!isset($_value); // Cannot redeclare var $_value');
                assert('!isset($_col); // Cannot redeclare var $_col');
                $_col = mb_strtoupper($column);
                if (isset($this->_cache[$tableName][$row][$_col])) {
                    $_value = \Yana\Util\Hashtable::get($this->_cache[$tableName][$row][$_col], $arrayAddress);
                } else {
                    $_value = $this->select("$tableName.$row.$column");
                }
                unset($_col);
                if (!is_array($_value)) {
                    $_value = array();
                }
                \Yana\Util\Hashtable::set($_value, $arrayAddress, $value);
                $value =& $_value;
                $updateQuery->setValues($value);
                unset($_value);
            }
            unset($arrayAddress);
        }

        /*
         * 5) move values to cache
         */
        if ($column === '*') {
            $this->_cache[$tableName][$row] = $value;
        } else {
            $this->_cache[$tableName][$row][$column] = $value;
        }

        return $this->_transaction->update($updateQuery);
    }

    /**
     * update or insert row
     *
     * insert $value at position $key
     *
     * If $key already exists, the previous value
     * gets updated, else the value is created.
     * If you do not like this behaviour, take a look
     * at the functions {@link AbstractConnection::update() update()}
     * and {@link AbstractConnection::insert() insert()} instead,
     * which let you set the operation you want.
     *
     * Note that, as this function has to determine which
     * of both operations to take, it is somewhat slower
     * (approx. 5%) then calling the appropriate function
     * explicitly.
     *
     * This function returns bool(true) on success
     * and bool(false) on error. Note, that this
     * function does not auto-commit. This means,
     * changes to the database will NOT be saved
     * unless you call $AbstractConnection->commit().
     *
     * The argument $key may also be an object of type {@see \Yana\Db\Queries\Insert}.
     * If so, no additional parameters need to be present.
     * This is a shortcut, which e.g. allows you to prepare
     * a query as an object and reuse it with multiple arguments.
     *
     * @param   string|\Yana\Db\Queries\Insert  $key    the address of the row that should be inserted|updated
     * @param   mixed            $value  value
     * @return  bool
     * @name    AbstractConnection::insertOrUpdate()
     * @see     AbstractConnection::insert()
     * @see     AbstractConnection::update()
     * @since   2.9.5
     */
    public function insertOrUpdate($key, $value = array())
    {
        if (is_object($key)) { // input is query object

            assert('func_num_args() === 1; // Too many arguments in ' .__METHOD__ . '(). Only 1 argument expected.');
            $dbQuery =& $key;
            if ($dbQuery instanceof \Yana\Db\Queries\Update) {
                return $this->update($dbQuery);

            } elseif ($dbQuery instanceof \Yana\Db\Queries\Insert) {
                return $this->insert($dbQuery);

            } else {
                trigger_error("Unable to insert or update row. Invalid query.", E_USER_WARNING);
                return false;
            }

        } else { // input is key address

            assert('is_string($key); // wrong argument type for argument 1, string expected');

            if ($key == '') {
                return false;

            } else {
                $key = mb_strtolower("$key");
            }

            // extract primary key portion of $key
            $_key = explode('.', $key);
            assert('!isset($table); /* Cannot redeclare var $table */');
            $table = $_key[0];
            assert('!isset($row); /* Cannot redeclare var $row */');
            $row = '*';
            if (isset($_key[1])) {
                $row = $_key[1];
            }
            unset($_key);

            $queryBuilder = new \Yana\Db\Queries\QueryBuilder($this);
            if ($row !== '*' && (isset($this->_cache[$table][$row]) || $this->exists("$table.$row"))) {
                $dbQuery = $queryBuilder->update($key, $value);
                return $this->update($dbQuery);

            } else {
                $dbQuery = $queryBuilder->insert($key, $value);
                return $this->insert($dbQuery);
            }

        }
    }

    /**
     * Insert $value at position $key.
     *
     * This function returns bool(true) on success
     * and bool(false) on error. If $key already exists,
     * the function will return bool(false).
     *
     * Note, that this function does not auto-commit.
     * This means, changes to the database will NOT be saved
     * unless you call $AbstractConnection->commit().
     *
     * The argument $key may also be an object of type {@see \Yana\Db\Queries\Insert}.
     * If so, no additional parameters need to be present.
     * This is a shortcut, which e.g. allows you to prepare
     * a query as an object and reuse it with multiple arguments.
     *
     * @param   string|\Yana\Db\Queries\Insert  $key    the address of the row that should be inserted
     * @param   mixed            $value  value
     * @return  bool
     * @name    AbstractConnection::insert()
     * @see     AbstractConnection::insertOrUpdate()
     * @see     AbstractConnection::update()
     * @since   2.9.5
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when either $key or $value is invalid
     * @throws  \Yana\Core\Exceptions\NotWriteableException     when the table or database is locked
     */
    public function insert($key, $value = array())
    {
        if (!$this->_isWriteable()) {
            throw new \Yana\Core\Exceptions\NotWriteableException('Operation aborted, not writeable.', E_USER_NOTICE);
        }

        if (is_object($key) && $key instanceof \Yana\Db\Queries\Insert) { // input is query object

            assert('func_num_args() === 1; // Too many arguments. Only 1 argument expected.');
            $insertQuery = $key;

        } else { // input is key address

            $queryBuilder = new \Yana\Db\Queries\QueryBuilder($this);
            $insertQuery = $queryBuilder->insert($key, $value);

        }

        return $this->_transaction->insert($insertQuery);
    }

    /**
     * remove one row
     *
     * For security reasons all delete queries will automatically be limited to 1 row at a time.
     * While this might be seen as a limitation the far more valuable advantage is,
     * no user is able to destroy a whole table - wether by intention or by accident -
     * in only one query. (At least not via this API.)
     *
     * The function returns bool(true) on success and bool(false) on error.
     *
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
     * Since version 2.9.3 the argument $key may also be an object
     * of type {@see \Yana\Db\Queries\Delete}. If so, no additional parameters need to be
     * present. This is a shortcut, which e.g. allows you to prepare
     * a query as an object and reuse it with multiple arguments.
     *
     * @param   string|\Yana\Db\Queries\Delete  $key    the address of the row that should be removed
     * @param   array            $where  where clause
     * @param   int              $limit  maximum number of rows to remove
     * @return  bool
     */
    public function remove($key, array $where = array(), $limit = 1)
    {
        assert('is_int($limit); // Wrong argument type for argument 3. Integer expected.');
        assert('$limit >= 0; // Invalid argument 3. Value must be greater or equal 0.');

        if (!$this->_isWriteable()) {
            trigger_error('Operation aborted, not writeable.', E_USER_NOTICE);
            return false;
        }

        if (is_object($key) && $key instanceof \Yana\Db\Queries\Delete) { // input is query object

            assert('func_num_args() === 1; // Too many arguments in ' . __METHOD__ . '(). Only 1 argument expected.');
            $deleteQuery = $key;

        } else { // input is key address

            $queryBuilder = new \Yana\Db\Queries\QueryBuilder($this);
            $deleteQuery = $queryBuilder->remove($key, $where, $limit);

        }
        return $this->_transaction->remove($deleteQuery);
    }

    /**
     * Counts and returns the rows of $table.
     *
     * You may also provide the parameter $table as an object of type DbSelectCount.
     * In this case the second argument should be empty.
     * Instead, add a where clause to your query object.
     *
     * Returns 0 if the table is empty or does not exist.
     *
     * @param   string|\Yana\Db\Queries\SelectCount  $table  name of a table
     * @param   array                 $where  optional where clause
     * @return  int
     */
    public function length($table, array $where = array())
    {
        if (is_object($table) && $table instanceof \Yana\Db\Queries\SelectCount) { // input is query object

            assert('func_num_args() === 1; // Too many arguments in ' . __METHOD__ . '(). Only 1 argument expected.');
            $countQuery =& $table;

        } else { // input is table name

            assert('is_string($table); // Wrong argument type $table. String expected.');

            // build query
            try {

                $queryBuilder = new \Yana\Db\Queries\QueryBuilder($this);
                $countQuery = $queryBuilder->length($table, $where);

            } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                return 0;
            }
        }

        return $countQuery->countResults();
    }

    /**
     * Check wether a certain table has no entries.
     *
     * Returns bool(true) if $connection->length() == 0.
     *
     * @param   string  $table  name of a table
     * @return  bool
     */
    public function isEmpty($table)
    {
        assert('is_string($table); // Wrong argument type $table. String expected.');
        return ($this->length($table) == 0);
    }

    /**
     * Check wether a certain key exists
     *
     * Returns bool(true), if the adress $key (table, row, column) is defined, and otherwise bool(false).
     * If no argument is provided, the function returns bool(true)
     * if a database connection exists and bool(false) if not.
     *
     * You may also provide the parameter $key as an object of type DbSelectExist.
     *
     * @uses    $AbstractConnection->exists('table.5')
     *
     * @param   string|\Yana\Db\Queries\SelectExist  $key    adress to check
     * @param   array                 $where  optional where clause
     * @return  bool
     */
    public function exists($key, array $where = array())
    {
        /*
         * 1) input is query object
         */
        if ($key instanceof \Yana\Db\Queries\SelectExist) {
            assert('func_num_args() === 1; // Too many arguments in ' . __METHOD__ . '(). Only 1 argument expected.');
            return $key->doesExist();
        }

        /*
         * 2) input is key address
         */
        assert('is_string($key); // Wrong argument type for argument 1. String expected');
        $key = (string) $key;

        // check table
        if (mb_strpos($key, '.') === false) {
            return $this->getSchema()->isTable($key);
        }
        // build query to check key
        $queryBuilder = new \Yana\Db\Queries\QueryBuilder($this);
        $existQuery = $queryBuilder->exists($key, $where);

        // check if address exists
        return $existQuery->doesExist();
    }

    /**
     * check wether the current database is readonly
     *
     * This returns bool(false) if the database does not
     * exist, or the database property "readonly" is set
     * to bool(true) in the database's structure file.
     * Otherwise the function returns bool(true).
     *
     * @uses $AbstractConnection->isWriteable()
     *
     * @return  bool
     */
    public function isWriteable()
    {
        return !$this->getSchema()->isReadonly();
    }

    /**
     * get database name
     *
     * This function returns the name of the database as a string.
     *
     * @return  string
     * @ignore
     */
    public function __toString()
    {
        try {
            return $this->getSchema()->getName();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * get name of current database definition
     *
     * @return  string
     * @ignore
     */
    protected function getName()
    {
        if (!isset($this->name)) {
            $this->name = $this->getSchema()->getName();
        }
        return $this->name;
    }

    /**
     * quote a value
     *
     * Returns the quoted values as a string
     * surrounded by delimiters, depending on
     * the DBMS selected.
     *
     * @param   mixed  $value  value too quote
     * @return  string
     * @ignore
     */
    public function quote($value)
    {
        if (is_null($value)) {
            return 'NULL';
        }
        if (!is_scalar($value)) {
            trigger_error("Your SQL statement contains an unexpected non-scalar value: " .
            "(" . gettype($value) .") " . "'" . print_r($value, true) . "'", E_USER_NOTICE);
            $value = (string) $value;
        }
        return $this->getConnection()->quote($value);
    }

    /**
     * check if database is writeable
     *
     * @return  bool
     */
    private function _isWriteable()
    {
        if (!$this->isWriteable()) {
            \Yana\Log\LogManager::getLogger()->addLog("Database is not writeable. " .
                "Check if attribute readonly is set to true in structure file: ".$this->getName(), E_USER_WARNING);
            return false;
        } else {
            return true;
        }
    }

    /**
     * return when the row was last modified
     *
     * Check when the data has been last modified by any other than the current user.
     * Use this to handle conditions, where two or more users request the same form.
     * One of them submits his changes and recieves an affirmation. Now the form of
     * the other user contains invalid data, because meanwhile the data he is currently
     * editing has been changed.
     * If then the other also submits the form, the previously made changes of the other
     * user would be overwritten. We call this a "dirty-write".
     *
     * So instead, this function is to be used to check for any such conditions and
     * present the second user an appropriate error message, telling him that his form
     * data is no longer valid and ensure, the "dirty" data is not written to the
     * database.
     *
     * @param   string  $table  table
     * @param   string  $row    row
     * @return  int
     */
    private function _getLastModified($table, $row)
    {
        assert('is_string($table); // Wrong type for argument 1. String expected.');
        assert('is_string($row); // Wrong type for argument 2. String expected.');

        /* settype to STRING */
        $table = (string) $table;
        $row = (string) $row;
        $path = self::getTempDir() . $this->_lastModifiedPath;

        /*
         * 1) load file
         */
        if (empty($this->_lastModified) && file_exists($path)) {
            assert('!isset($lastModified); // Cannot redeclare var $lastModified');
            $lastModified = unserialize(file_get_contents($path));
            if (!is_array($lastModified)) {
                $lastModified = array();
                trigger_error("File content is not valid '{$path}'.", E_USER_NOTICE);
            }
            $this->_lastModified = $lastModified;
            unset($lastModified);
            assert('is_array($this->_lastModified);');
        }

        /*
         * 2) get IP to identify user
         */
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $userId = $_SERVER['REMOTE_ADDR'];
        } else {
            $userId = '*';
        }

        /*
         * 3) get time
         */
        assert('!isset($lastModified); // Cannot redeclare var $lastModified');
        $lastModified = 0;
        if (!empty($this->_lastModified) && isset($this->_lastModified[$table][$row])) {
            assert('!isset($array); // Cannot redeclare var $array');
            $array = $this->_lastModified[$table][$row];
            assert('is_array($array); // $array should be an array');
            assert('count($array) === 2; // $array should have 2 values: timestamp and user id');
            // check if row has been changed by a different user
            if ($userId !== array_pop($array)) {
                $lastModified = array_pop($array);
            }
            unset($array);
        } // end if (value exists)
        assert('is_int($lastModified);');

        /*
         * 4) update time
         */
        if (empty($this->_lastModified) || $lastModified !== time()) {
            $this->_lastModified[$table][$row] = array(time(), $userId);
        }

        /*
         * 6) return data
         */
        if ($lastModified > time()) {
            return (int) $lastModified;
        } else {
            return 0;
        }
    }

    /**
     * Reset the object to default values
     *
     * Resets the history for the last selected table,
     * resets the queue of pending SQL statements and
     * resets the database cache.
     *
     * @name  AbstractConnection::reset()
     */
    public function reset()
    {
        $this->_transaction = new \Yana\Db\Transaction($this->schema);
        $this->_cache = array();
    }

    /**
     * Alias of AbstractConnection::reset()
     *
     * @see  AbstractConnection::reset()
     */
    public function rollback()
    {
        return $this->reset();
    }

    /**
     * compare with another object
     *
     * Returns bool(true) if this object and $anotherObject
     * are equal and bool(false) otherwise.
     *
     * Two instances are considered equal if and only if
     * they are both objects of the same class and they both
     * refer to the same structure file and use equal database connections.
     *
     * @param    \Yana\Core\IsObject $anotherObject  another object to compare
     * @return   string
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        return (bool) ($anotherObject instanceof $this) && $this->getName()->equals($anotherObject->getName()) &&
            $this->database == $anotherObject->database;
    }

    /**
     * Check foreign keys constraints.
     *
     * This should be called before updating or inserting a row or a cell.
     *
     * Note: this provides a certain level of referential integrity
     * even with DBMS that do not support this feature at all, like
     * MySQL on MyISAM tables.
     *
     * Note: This checks only outgoing - not incoming foreign keys!
     *
     * Returns bool(true) if all foreign key constraints are satisfied and
     * bool(false) otherwise.
     *
     * @param    \Yana\Db\Ddl\Table  $table       table definition
     * @param    mixed               $value       row or cell value
     * @param    string              $columnName  name of updated column (when updating a cell)
     * @return   bool
     * @ignore
     */
    private function _checkForeignKeys(\Yana\Db\Ddl\Table $table, $value, $columnName = null)
    {
        // ignore when strict checks are turned off
        if (!YANA_DB_STRICT) {
            return true;
        }
        if (!is_null($columnName)) {
            $value = array($columnName => $value);
        }
        $dbSchema = $this->getSchema();
        /* @var $foreign \Yana\Db\Ddl\ForeignKey */
        assert('!isset($foreign); /* Cannot redeclare var $fkey */');
        foreach ($table->getForeignKeys() as $foreign)
        {
            $isPartialMatch = !is_null($columnName) || $foreign->getMatch() === \Yana\Db\Ddl\KeyMatchStrategyEnumeration::PARTIAL;
            $isFullMatch = is_null($columnName) && $foreign->getMatch() === \Yana\Db\Ddl\KeyMatchStrategyEnumeration::FULL;
            $targetTable = mb_strtolower($foreign->getTargetTable());
            $fTable = $dbSchema->getTable($targetTable);
            foreach ($foreign->getColumns() as $sourceColumn => $targetColumn)
            {
                if (empty($targetColumn)) {
                    $isPrimaryKey = true;
                    $targetColumn = $fTable->getPrimaryKey();
                } else {
                    $isPrimaryKey = false;
                }
                /*
                 * If the referenced row does not exist,
                 * check if there is one recently inserted in cache
                 */
                if (isset($value[$sourceColumn])) {
                    $isMatch = false;
                    // foreign key does match
                    if ($isPrimaryKey && isset($this->_cache[$targetTable][mb_strtolower($value[$sourceColumn])])) {
                        $isMatch = true;
                    } else {
                        // scan cache first
                        if (isset($this->_cache[$targetTable])) {
                            foreach ($this->_cache[$targetTable] as $row)
                            {
                                if (!isset($row[$targetColumn])) {
                                    continue;
                                }
                                if (strcasecmp($row[$targetColumn], $value[$sourceColumn]) === 0) {
                                    $isMatch = true;
                                    break;
                                }
                            }
                        }
                        // if not in cache, try the database
                        if (!$isMatch) {
                            $dbExist = new \Yana\Db\Queries\SelectExist($this);
                            $dbExist->setTable($targetTable);
                            $dbExist->setWhere(array($targetColumn, '=', mb_strtolower($value[$sourceColumn])));
                            $isMatch = $dbExist->doesExist();
                        }
                    }
                    if ($isMatch) {
                        if ($isPartialMatch) {
                            // for a partial match it is enough if at least one of the keys matches
                            return true;
                        }
                    } else {
                        \Yana\Log\LogManager::getLogger()->addLog("Update on table '{$table->getName()}' failed. " .
                            "Foreign key constraint mismatch. " .
                            "The value '{$value[$sourceColumn]}' for attribute '{$sourceColumn}' " .
                            "refers to a non-existing entry in table '{$targetTable}'. ",
                            E_USER_ERROR, $value);
                        return false;
                    }
                } elseif ($isFullMatch) {
                    // for a full match the column must not be null
                    return false;
                }
            }
            unset($targetTable, $fkey, $ufkey);
        } // end foreach
        return true;
    }

    /**
     * Import SQL from a file.
     *
     * The input parameter $sqlFile can wether be filename,
     * or a numeric array of SQL statements.
     *
     * Returns bool(true) on success or bool(false) on error.
     * Note that the statements are executed within a transaction.
     * If the function fails,
     *
     * @param   string|array  $sqlFile filename which contain the SQL statments or an nummeric array of SQL statments.
     * @return  bool
     */
    abstract public function importSQL($sqlFile);

    /**
     * Returns the quoted Id as a string.
     *
     * The id is surrounded by delimiters, depending on the DBMS selected.
     *
     * @param   mixed  $value  name of database object
     * @return  string
     */
    abstract public function quoteId($value);

    /**
     * serialize this object to a string
     *
     * Returns the serialized object as a string.
     *
     * @return  string
     */
    public function serialize()
    {
        // returns a list of key => value pairs
        $properties = get_object_vars($this);
        // remove the database connection object
        unset($properties['database']);
        // return the names
        return serialize($properties);
    }

    /**
     * Reinitializes the object.
     *
     * @param  string  $string  string to unserialize
     */
    public function unserialize($string)
    {
        foreach (unserialize($string) as $key => $value)
        {
            $this->$key = $value;
        }
    }

    /**
     * Get database connection.
     *
     * Subclasses should implement a lazy auto-reconnect in this function
     * for wakeup after unserializing the instance from cache.
     *
     * @return  \MDB2_Driver_Common
     */
    abstract protected function getConnection();

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
     * $db->query($sqlStmt, $offset, $limit);
     * // 2nd synopsis
     * $db->query($dbQueryObject);
     * </code>
     *
     * Note that when providing the DbQuery object, the $limit and $offset arguments are
     * ignored.
     *
     * @param   string|\Yana\Db\Queries\AbstractQuery  $sqlStmt  one SQL statement (or a query object) to execute
     * @param   int                                    $offset   the row to start from
     * @param   int                                    $limit    the maximum numbers of rows in the resultset
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException if the SQL statement is not valid
     */
    abstract public function query($sqlStmt, $offset = 0, $limit = 0);

    /**
     * Returns bool(true) if the object is an error result.
     *
     * @param   mixed  $result  result
     * @return  bool
     */
    abstract public function isError($result);

}

?>