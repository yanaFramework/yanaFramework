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

/**
 * database API
 *
 * this class is a database abstraction api, that uses pear db
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DbStream extends \Yana\Core\Object implements Serializable
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /** @var  MDB2_Driver_Common */  protected $database = null;
    /** @var  string */  protected $name = "";
    /** @var  array  */  protected $dsn = array();

    /**#@-*/
    /**#@+
     * @ignore
     * @access  private
     */

    /** @var  array  */  private $_queue = array();
    /** @var  array  */  private $_cache = array();
    /** @var  array  */  private $_joins = array();
    /** @var  array  */  private $_reservedSqlKeywords = null;
    /** @var  array  */  private $_lastModified = array();
    /** @var  string */  private $_lastModifiedPath = "db_last_modified.tmp";
    /** @var  string */  private static $_tempDir = "cache/";

    /**#@-*/

    /**
     * database schema
     *
     * The database schema that is used in the current session.
     *
     * Please note that you should not change this schema unless
     * you REALLY know what you are doing.
     *
     * @var     DDLDatabase
     * @access  protected
     * @ignore
     */
    protected $schema  = null;

    /**
     * create a new instance
     *
     * Each database connection depends on a schema file describing the database.
     * These files are to be found in config/db/*.db.xml
     *
     * @param   string|DDLDatabase  $schema  schema name or schema in database definition language
     * @param   DbServer            $server  Connection to a database server
     * @throws  DbConnectionError     when connection to database failed
     * @throws  NotFound              when structure file is not found
     * @throws  NotReadableException  when trying to reverse-engineer database structure,
     *                                but the database's schema is unknown or not readable
     */
    public function __construct($schema = null, DbServer $server = null)
    {
        // fall back to default connection
        if (is_null($server)) {
            $server = new DbServer();
        }

        // open database connection
        $this->database = $server->getConnection();
        $this->dsn = $server->getDsn();

        // Error: Unable to connect to database
        if (!MDB2::isConnection($this->database)) {
            throw new DbConnectionError();
        }

        if ($schema instanceof DDLDatabase) {
            $this->schema = $schema;
        } else {
            assert('is_string($schema); // Wrong argument type $schema. String expected');
            $this->name = (string) $schema;
        }
    }

    /**
     * Set path to temporary directory.
     *
     * The target directory must be read- and writable.
     *
     * @access  public
     * @static
     * @param   string  $dir  absolute path to temp-directory
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
     * @access  public
     * @static
     * @return  string
     */
    public static function getTempDir()
    {
        return self::$_tempDir;
    }

    /**
     * Get database schema.
     *
     * Returns the schema of the database, containing info about tables, columns, forms aso.
     *
     * If no schema file is available, this framework has the ability to
     * reverse engineer the database at runtime.
     *
     * @access public
     * @return DDLDatabase
     * @throws DBError when reverse-engineering failed
     */
    public function getSchema()
    {
        if (!isset($this->schema)) {
            if ($this->name) {
                $source = $this->name;
                assert('is_string($source); // Invalid member type. Name is supposed to be a string.');
                // load file
                $schema = XDDL::getDatabase($source);
            } else {
                // auto-discover / reverse engineering
                $schema = DDLDatabaseFactory::createDatabase($this->database); // may throw DBError
            }
            $this->schema = $schema;
        }
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
     * @access  public
     * @param   string  $name  name of a database object
     * @return  DDL
     */
    public function __get($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        return $this->getSchema()->{$name};
    }

    /**
     * magic function call
     *
     * Calls a function on the selected database schema and returns the result.
     *
     * @access  public
     * @param   string  $name       name
     * @param   array   $arguments  arguments
     * @return  mixed
     */
    public function __call($name, array $arguments)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        return call_user_func_array(array($this->getSchema(), $name), $arguments);
    }

    /**
     * magic is set
     *
     * Returns true if a named object with the given name exists in the database schema.
     *
     * @access  public
     * @param   string  $name  name of a database object
     * @return  bool
     */
    public function __isset($name)
    {
        return ($this->__get($name) !== null);
    }

    /**
     * get the DSN
     *
     * This function returns an associative array containing
     * information on the current connection or bool(false) on error.
     *
     * See {@link DbServer::getDsn()} for more details.
     *
     * @access  public
     * @name    DbStream::getDsn()
     * @return  array
     * @since   2.9.8
     */
    public function getDsn()
    {
        if (is_array($this->dsn)) {
            return $this->dsn;

        } else {
            return false;
        }
    }

    /**
     * get name of database management system
     *
     * Returns the name of the chosen DBMS as a lower-cased string.
     *
     * @access  public
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
     * Alias of DbStream::write()
     *
     * @access  public
     * @return  bool
     * @see     DbStream::write()
     */
    public function commit()
    {
        return $this->write();
    }

    /**
     * Commit current transaction
     *
     * This writes all changes to the database
     *
     * @access  public
     * @return  bool
     * @name    DbStream::write()
     * @throws  NotWriteableException  when the database or table is locked
     */
    public function write()
    {
        if (!$this->_isWriteable()) {
            throw new NotWriteableException('Operation aborted, not writeable.', E_USER_NOTICE);
        }

        /* Buffer empty */
        if (count($this->_queue) == 0) {
            return true;
        }

        // start transaction
        $dbConnection = $this->getConnection();
        $dbConnection->beginTransaction();
        $dbSchema = $this->getSchema();

        assert('!isset($i); /* Cannot redeclare $i */');
        for ($i = 0; $i < count($this->_queue); $i++)
        {
            /*
             * 1) get query object
             */
            /* @var $dbQuery DbQuery */
            if (is_array($this->_queue[$i]) && isset($this->_queue[$i][0])) {
                $dbQuery =& $this->_queue[$i][0];
            } else {
                $dbQuery =& $this->_queue[$i];
            }

            // skip empty queries
            if (empty($dbQuery)) {
                continue;
            }

            /*
             * 2) get arguments for trigger
             */
            if (is_array($this->_queue[$i]) && isset($this->_queue[$i][1]) && is_array($this->_queue[$i][1])) {
                $args = $this->_queue[$i][1];
            } else {
                $args = null;
            }

            if (defined('YANA_ERROR_REPORTING') && YANA_ERROR_REPORTING === YANA_ERROR_LOG) {
                \Yana\Log\LogManager::getLogger()->addLog("$dbQuery");
            }

            /*
             * 3.a) handle query object
             */
            if (is_object($dbQuery)) {
                switch ($dbQuery->getType())
                {
                    /*
                     * 3.1) delete a row
                     */
                    case DbQueryTypeEnumeration::DELETE:
                        /* send request to database */
                        $result = $dbQuery->sendQuery();
                    break;
                    /*
                     * 3.2) update a row
                     */
                    case DbQueryTypeEnumeration::UPDATE:
                        /* send request to database */
                        $result = $dbQuery->sendQuery();
                    break;
                    /*
                     * 3.3) insert a row
                     */
                    case DbQueryTypeEnumeration::INSERT:
                        if ($this->getDBMS() === 'mssql' && $dbQuery->getRow() !== '*') {
                            /**
                             * MSSQL compatibility
                             *
                             * {@internal
                             * MSSQL does not allow entries of identity
                             * tables to be inserted with a certain primary
                             * key unless explicitely told to do so.
                             * This exotic behavior does not apply to any
                             * other DBMS.
                             * }}
                             */
                            assert('!isset($tableName); // Cannot redeclare var $tableName');
                            $tableName = $dbQuery->getTable();
                            assert('!isset($table); // Cannot redeclare var $table');
                            $table = $dbSchema->getTable($tableName);
                            assert('$table instanceof DDLTable; // No such table');
                            assert('!isset($column); // Cannot redeclare var $column');
                            $column = $table->getColumn($table->getPrimaryKey());
                            assert('$column instanceof DDLColumn; // No such column');
                            /* test if is identity table */
                            if ($column->isAutoIncrement()) {
                                /* set identity restriction off */
                                $this->query('SET IDENTITY_INSERT [' . YANA_DATABASE_PREFIX.$tableName . '] ON;');
                                $result = $dbQuery->sendQuery();
                                /* reset identity restriction */
                                $this->query('SET IDENTITY_INSERT [' . YANA_DATABASE_PREFIX.$tableName . '] OFF;');
                            } else {
                                $result = $dbQuery->sendQuery();
                            }
                            unset($tableName, $table, $column);
                        } else {
                            /* send request to database */
                            $result = $dbQuery->sendQuery();
                        }
                    break;
                    default:
                        /* send request to database */
                        $result = $dbQuery->sendQuery();
                    break;
                } // end switch (type)


            /*
             * 3.b) handle query string
             */
            } elseif (is_string($dbQuery)) {
                $result = $this->query($dbQuery);

            /*
             * 3.c) error - invalid argument type
             */
            } else {
                \Yana\Log\LogManager::getLogger()->addLog("The value '$dbQuery' is not a valid query.", E_USER_WARNING);
                return false;
            }

            /*
             * 4.1) error - query failed
             */
            assert('!isset($success); // Cannot redeclare var $success');
            if ($this->isError($result)) {
                /*
                 * 4.1.2) rollback on error
                 */
                \Yana\Log\LogManager::getLogger()->addLog("Failed: $dbQuery", E_USER_WARNING, $result->getMessage());
                $result = $dbConnection->rollback();
                /*
                 * 4.1.3) when rollback failed, create entry in logs
                 */
                if ($this->isError($result)) {
                    \Yana\Log\LogManager::getLogger()->addLog("Unable to rollback changes. Database might contain corrupt data.", E_USER_ERROR);
                }
                return false;
            }
            /*
             * 4.2) query was successfull
             */


            /*
             * 4.2.2) fire trigger(s)
             */
            assert('(is_array($args) && isset($args[0])) || is_null($args); ' .
                    '// Expecting $args[][] to be an array');
            if (is_array($args)) {
                $this->_executeTrigger($args);
            }
            unset($args);

            unset($success, $tableName, $column);
        } // end foreach (query)
        unset($i);

        /*
         * 5) commit changes
         *
         * The time when the database was last modified
         * is updated, to provide protection from race
         * conditions where two transaction try to modify
         * the same data.
         */
        $result = $dbConnection->commit();
        /*
         * 5.1) commit failed
         */
        if ($this->isError($result)) {
            \Yana\Log\LogManager::getLogger()->addLog("Failed: $dbQuery", E_USER_WARNING, $result->getMessage());
            return false;
        }
        /*
         * 5.2) commit successful
         */
        if (YANA_DB_STRICT && !empty($this->_lastModified)) {
            file_put_contents(self::getTempDir() . $this->_lastModifiedPath, serialize($this->_lastModified));
        }
        $this->_queue = array();
        return true;
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
     * @access  public
     * @param   string|DbSelect  $key      the address of the value(s) to retrieve
     * @param   array            $where    where clause
     * @param   array            $orderBy  a list of columns to order the resultset by
     * @param   int              $offset   the number of the first result to be returned
     * @param   int              $limit    maximum number of results to return
     * @param   bool             $desc     if true results will be ordered in descending,
     *                                     otherwise in ascending order
     * @return  mixed
     * @throws  \Yana\Core\InvalidArgumentException  when one of the given arguments is not valid
     */
    public function select($key, array $where = array(), $orderBy = array(), $offset = 0, $limit = 0, $desc = false)
    {
        /* 1) handle $key array */
        if (is_object($key) && $key instanceof DbSelect) {
            assert('func_num_args() === 1; // Too many arguments. Only 1 argument expected.');
            $selectQuery =& $key;
        } // end if

        /*
         * 2) build query
         */
        if (!isset($selectQuery)) {
            $selectQuery = new DbSelect($this);
            assert('is_string($key); // Wrong argument type for argument 1. String expected.');

            $selectQuery->setKey($key);
            $tableName = $selectQuery->getTable();

            if (!empty($where)) {
                $selectQuery->setWhere($where);
            }
            if (!empty($orderBy) || $desc === true) {
                $selectQuery->setOrderBy($orderBy, $desc);
            }

            /*
             * 2.1) resolve joined tables
             */
            if (isset($this->_joins[$tableName]) && is_array($this->_joins[$tableName])) {
                assert('!isset($table2);  // Cannot redeclare var $table2');
                assert('!isset($columns); // Cannot redeclare var $columns');
                foreach ($this->_joins[$tableName] as $table2 => $columns)
                {
                    try {
                        $selectQuery->setInnerJoin($table2, $columns[0], $columns[1]);
                    } catch (\Exception $e) {
                        \Yana\Log\LogManager::getLogger()->addLog("Unable to join tables '{$tableName}' and " .
                            "'{$table2}'. Cause: " . $e->getMessage(), E_USER_WARNING);
                        return false;
                    }
                }
                unset($table2, $columns);
            }

            /*
             * 2.2) set limit and offset
             */
            if ($offset > 0) {
                $selectQuery->setOffset($offset);
            }
            if ($limit > 0) {
                $selectQuery->setLimit($limit);
            }

        } // end if

        return $selectQuery->getResults();
    }

    /**
     * update a row or cell
     *
     * update $value at position $key
     *
     * If $key does not exist, bool(false) is returned.
     *
     * This function returns bool(true) on success
     * and bool(false) on error.
     *
     * Note, that this function does not auto-commit.
     * This means, changes to the database will NOT be saved
     * until you call $DbStream->write().
     *
     * The argument $key may also be an object of type DbUpdate.
     * If so, no additional parameters need to be present.
     * This is a shortcut, which e.g. allows you to prepare
     * a query as an object and reuse it with multiple arguments.
     *
     * @access  public
     * @param   string|DbUpdate  $key    the address of the row that should be updated
     * @param   mixed            $value  value
     * @return  bool
     * @name    DbStream::update()
     * @see     DbStream::insertOrUpdate()
     * @see     DbStream::insert()
     * @since   2.9.5
     * @throws  \Yana\Core\InvalidArgumentException  when either the given $key or $value is invalid
     */
    public function update($key, $value = array())
    {
        if (!$this->_isWriteable()) {
            trigger_error('Operation aborted, not writeable.', E_USER_NOTICE);
            return false;
        }

        /*
         * 1) input is query object
         */
        if (is_object($key) && $key instanceof DbUpdate) {

            assert('func_num_args() === 1; // Too many arguments in ' . __METHOD__ . '(). Only 1 argument expected.');
            $updateQuery =& $key;

        /*
         * 2) input is key address
         */
        } else {

            /*
             * 2.1) check input
             */
            assert('is_string($key); // wrong argument type for argument 1, string expected');


            if ($key == '') {
                return false;
            } else {
                $key = mb_strtolower("$key");
            }

            /*
             * 2.2) build query
             */
            $updateQuery = new DbUpdate($this);
            $updateQuery->setKey($key);
            $updateQuery->setValues($value);

        } // end if


        // get properties
        $tableName = $updateQuery->getTable();
        $row = $updateQuery->getRow();
        $column = $updateQuery->getColumn();
        $value = $updateQuery->getValues(); // get values by reference

        assert('!isset($table); /* Cannot redeclare var $table */');
        $table = $this->getSchema()->getTable($tableName);

        /*
         * 2) check whether the row has been modified since last access
         */
        if (YANA_DB_STRICT) {
            if (isset($_SESSION['transaction_isolation_created'])) {
                if ($this->_getLastModified($tableName, $row) > $_SESSION['transaction_isolation_created']) {
                    \Yana\Log\LogManager::getLogger()->addLog("The user was trying to save form data, which has changed " .
                        "since he accessed the corrsponding form. The operation has been aborted, " .
                        "as updating this row would have overwritten changes made by another user.", E_USER_WARNING);
                    throw new FormTimeoutWarning();
                }
            }
        }

        /*
         * 3.2.3) check if element is inside an array
         *
         * If true, get the previous array and merge both.
         */
        if ($updateQuery->getExpectedResult() === DbResultEnumeration::CELL) {
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

        // get values by reference
        $value = $updateQuery->getValues();

        /*
         * 3.4) error - updating table / column is illegal
         */
        assert('!isset($expectedResult); /* Cannot redeclare var $expectedResult */');
        $expectedResult = $updateQuery->getExpectedResult();
        if ($expectedResult !== DbResultEnumeration::ROW && $expectedResult !== DbResultEnumeration::CELL) {
            throw new \Yana\Core\InvalidArgumentException("Query is invalid. " .
                "Updating a table or column is illegal. Operation aborted.");
        }

        /*
         * 3.4) before update: check constraints and triggers
         */
        assert('!isset($constraint); // Cannot redeclare var $constraint');
        assert('!isset($triggerArgs); /* Cannot redeclare var $triggerArgs */');
        $triggerArgs = array();
        if ($column == '*') {
            $constraint = $value;
        } else {
            $constraint = array($column => $value);
        }
        if (DbStructureGenerics::checkConstraint($table, $constraint) === false) {
            \Yana\Log\LogManager::getLogger()->addLog("Cannot set values. Constraint check failed for: '$updateQuery'.");
            return false;

        } else {
            DbStructureGenerics::onBeforeUpdate($table, $column, $value, $updateQuery->getRow());
            $triggerArgs[] = array(
                DbQueryTypeEnumeration::UPDATE,
                $table,
                $column,
                $value,
                $updateQuery->getRow()
            );

        }

        /*
         * 4) untaint input
         */
        switch ($updateQuery->getExpectedResult())
        {
            case DbResultEnumeration::ROW:
                $value = $table->sanitizeRow($value, $this->getDBMS(), false);
            break;
            case DbResultEnumeration::CELL:
                if ($table->getColumn($column)->getType() !== 'array') {
                    assert('$table->isColumn($column);');
                    $value = $table->getColumn($column)->sanitizeValue($value, $this->getDBMS());
                }
            break;
            default:
                // error - may only update or insert rows and cells
                return false;
            break;
        }

        if (!$this->checkForeignKeys($table, $value, $column)) {
            return false;
        }

        /*
         * 5) move values to cache
         */
        if ($column === '*') {
            $this->_cache[$tableName][$row] = $value;
        } else {
            $this->_cache[$tableName][$row][$column] = $value;
        }
        unset($arrayAddress);

        /*
         * 6) add SQL statement to queue
         */
        $this->_queue[] = array($updateQuery, $triggerArgs);

        return true;
    }

    /**
     * alias of DbStream::insertOrUpdate()
     *
     * @access  public
     * @param   string|DbInsert  $key    the address of the row that should be updated|inserted
     * @param   mixed            $value  value
     * @return  bool
     * @name    DbStream::updateOrInsert()
     * @see     DbStream::insertOrUpdate()
     * @see     DbStream::insert()
     * @see     DbStream::update()
     * @since   2.9.5
     * @ignore
     */
    public function updateOrInsert($key, $value = array())
    {
        return $this->insertOrUpdate($key, $value);
    }

    /**
     * update or insert row
     *
     * insert $value at position $key
     *
     * If $key already exists, the previous value
     * gets updated, else the value is created.
     * If you do not like this behaviour, take a look
     * at the functions {@link DbStream::update() update()}
     * and {@link DbStream::insert() insert()} instead,
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
     * unless you call $DbStream->write().
     *
     * The argument $key may also be an object of type DbInsert.
     * If so, no additional parameters need to be present.
     * This is a shortcut, which e.g. allows you to prepare
     * a query as an object and reuse it with multiple arguments.
     *
     * @access  public
     * @param   string|DbInsert  $key    the address of the row that should be inserted|updated
     * @param   mixed            $value  value
     * @return  bool
     * @name    DbStream::insertOrUpdate()
     * @see     DbStream::insert()
     * @see     DbStream::update()
     * @since   2.9.5
     */
    public function insertOrUpdate($key, $value = array())
    {
        $isInsert = null;

        /*
         * 1) input is query object
         */
        if (is_object($key)) {

            assert('func_num_args() === 1; // Too many arguments in ' .__METHOD__ . '(). Only 1 argument expected.');
            $dbQuery =& $key;
            if ($dbQuery instanceof DbUpdate) {
                $isInsert = false;

            } elseif ($dbQuery instanceof DbInsert) {
                $isInsert = true;

            } else {
                trigger_error("Unable to insert or update row. Invalid query.", E_USER_WARNING);
                return false;
            }

        /*
         * 2) input is key address
         */
        } else {

            /*
             * 2.1) check input
             */
            assert('is_string($key); // wrong argument type for argument 1, string expected');

            if ($key == '') {
                return false;

            } else {
                $key = mb_strtolower("$key");
            }

            /*
             * 2.2) build query
             */
            $_key = explode('.', $key);
            assert('!isset($table); /* Cannot redeclare var $table */');
            $table = $_key[0];
            assert('!isset($row); /* Cannot redeclare var $row */');
            $row = '*';
            if (isset($_key[1])) {
                $row = $_key[1];
            }
            unset($_key);
            if ($row !== '*' && (isset($this->_cache[$table][$row]) || $this->exists("$table.$row"))) {
                $dbQuery = new DbUpdate($this);
                $isInsert = false;

            } else {
                $dbQuery = new DbInsert($this);
                $isInsert = true;
            }
            unset($table, $row);

            // assign target-table, -row and values
            $dbQuery->setKey($key);
            $dbQuery->setValues($value);

        } // end if

        /*
         * 3) call function
         */
        if ($isInsert === true) {
            return $this->insert($dbQuery);

        } else {
            return $this->update($dbQuery);

        }
    }

    /**
     * insert row
     *
     * Insert $value at position $key.
     *
     * This function returns bool(true) on success
     * and bool(false) on error. If $key already exists,
     * the function will return bool(false).
     *
     * Note, that this function does not auto-commit.
     * This means, changes to the database will NOT be saved
     * unless you call $DbStream->write().
     *
     * The argument $key may also be an object of type DbInsert.
     * If so, no additional parameters need to be present.
     * This is a shortcut, which e.g. allows you to prepare
     * a query as an object and reuse it with multiple arguments.
     *
     * @access  public
     * @param   string|DbInsert  $key    the address of the row that should be inserted
     * @param   mixed            $value  value
     * @return  bool
     * @name    DbStream::insert()
     * @see     DbStream::insertOrUpdate()
     * @see     DbStream::update()
     * @since   2.9.5
     * @throws  \Yana\Core\InvalidArgumentException  when either $key or $value is invalid
     * @throws  NotWriteableException                when the table or database is locked
     */
    public function insert($key, $value = array())
    {
        if (!$this->_isWriteable()) {
            throw new NotWriteableException('Operation aborted, not writeable.', E_USER_NOTICE);
        }

        /*
         * 1) input is query object
         */
        if (is_object($key) && $key instanceof DbInsert) {

            assert('func_num_args() === 1; // Too many arguments. Only 1 argument expected.');
            $insertQuery =& $key;

        /*
         * 2) input is key address
         */
        } else {

            /*
             * 2.1) check input
             */
            assert('is_string($key); // wrong argument type for argument 1, string expected');

            if ($key == '') {
                return false;
            }

            $key = mb_strtolower($key);

            /*
             * 2.2) build query
             */
            $insertQuery = new DbInsert($this);
            $insertQuery->setKey($key);
            $insertQuery->setValues($value);


        } // end if

        // get properties
        $tableName = $insertQuery->getTable();
        $row = $insertQuery->getRow();
        $value = $insertQuery->getValues(); // get values by reference

        assert('!isset($table); /* Cannot redeclare var $table */');
        $table = $this->getSchema()->getTable($tableName);

        /*
         * 3.1) error - inserting updating table / column is illegal
         */
        assert('!isset($triggerArgs); /* Cannot redeclare var $triggerArgs */');
        $triggerArgs = array();
        $expectedResult = $insertQuery->getExpectedResult();
        if ($expectedResult !== DbResultEnumeration::ROW) {
            throw new \Yana\Core\InvalidArgumentException("Query is invalid. " .
                "Can only insert a row, not a table, cell or column.");
        }

        /*
         * 3.2) error - constraint check failed
         */
        if (DbStructureGenerics::checkConstraint($table, $value) === false) {
            \Yana\Log\LogManager::getLogger()->addLog("Insert on table '{$tableName}' failed. " .
                "Constraint check failed.", E_USER_WARNING, $value);
            return false;
        }
        /*
         * 3.3) fire trigger
         */
        DbStructureGenerics::onBeforeInsert($table, $value, $insertQuery->getRow());
        $triggerArgs[] = array(
            DbQueryTypeEnumeration::INSERT,
            $table,
            $value,
            $insertQuery->getRow()
        );

        /*
         * 4) check input
         */

        /*
         * 4.1) untaint input
         */
        $value = $table->sanitizeRow($value, $this->getDBMS(), true);

        if (!$this->checkForeignKeys($table, $value)) {
            return false;
        }

        /*
         * 5) move values to cache
         */
        $this->_cache[$tableName][$row] = $value;
        unset($arrayAddress);

        /*
         * 6) add SQL statement to queue
         */
        $this->_queue[] = array($insertQuery, $triggerArgs);

        return true;
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
     * of type DbDelete. If so, no additional parameters need to be
     * present. This is a shortcut, which e.g. allows you to prepare
     * a query as an object and reuse it with multiple arguments.
     *
     * @access  public
     * @param   string|DbDelete  $key    the address of the row that should be removed
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

        /*
         * 1) input is query object
         */
        if (is_object($key) && $key instanceof DbDelete) {

            assert('func_num_args() === 1; // Too many arguments in ' . __METHOD__ . '(). Only 1 argument expected.');
            $deleteQuery =& $key;
            $tableName = $deleteQuery->getTable();

        /*
         * 2) input is key address
         */
        } else {

            // Build query
            assert('is_string($key); // Wrong argument type $key. String expected.');
            assert('!isset($deleteQuery); // Cannot redeclare var $deleteQuery');
            $deleteQuery = new DbDelete($this);
            $deleteQuery->setLimit($limit);
            $deleteQuery->setKey($key);
            $tableName = $deleteQuery->getTable();
            $deleteQuery->setWhere($where);

        } // end if
        /*  @var $deleteQuery DbDelete */

        assert('!isset($table); // Cannot redeclare var $table');
        $table = $this->getSchema()->getTable($tableName);

        /*
         * get old row for logging an generic triggers
         */
        assert('!isset($oldRows); /* Cannot redeclare var $oldRows */');
        $oldRows = $deleteQuery->getOldValues();

        /*
         * abort: nothing to delete
         */
        if (empty($oldRows)) {
            return true;
        }

        if ($limit === 1) {
            $oldRows = array($oldRows);
        }

        /*
         * loop through deleted rows
         */
        assert('!isset($triggerArgs); /* Cannot redeclare var $triggerArgs */');
        $triggerArgs = array();
        assert('!isset($oldRow); /* Cannot redeclare var $oldRow */');
        foreach ($oldRows as $oldRow)
        {
            // fire trigger
            DbStructureGenerics::onBeforeDelete($table, $oldRow, $deleteQuery->getRow());
            // save trigger settings for onAfterDelete
            $triggerArgs[] = array(
                DbQueryTypeEnumeration::DELETE,
                $table,
                $oldRow,
                $deleteQuery->getRow()
            );
        }
        unset($oldRow);

        /*
         * 5) add query to queue
         */
        $this->_queue[] = array($deleteQuery, $triggerArgs);

        /* return true to indicate the request was successfull */
        return true;
    }

    /**
     * join the resultsets for two tables
     *
     * Results in an INNER JOIN $table1, $table2 WHERE $table1.$key1 = $table2.$key2 .
     *
     * Note that if you ommit the parameters $key1 and $key2, the API will try to determine
     * the foreign key and target key itself by looking up the foreign key in the database's
     * structure file. The first foreign key association that matches will be used.
     *
     * Also note that two tables may only be joined via one pair of columns - not two or more.
     * Instead if you may add additional rules to the where clause as you see fit.
     *
     * Note that joins are permanent. So in opposition to what you might have learned from
     * common SQL statements and other APIs, you do not need to repeat joins for each query.
     * Instead, this API "remembers" what it was told and once set your joins will automatically
     * be used each time you query the table until you explicitly remove it.
     *
     * To remove all perviously set joins from a table, use the following function call:
     * <code> $dbStream->join('myTable'); </code>
     * As you can see above, if the second argument ($table2) is ommited, all joins bound
     * to 'myTable' are released.
     *
     * Also note, that the wildcard '*' may be used to refer to the "least recently used" table.
     * This is a shortcut that you may use in your scripts.
     *
     * @access  public
     * @param   string $table1  name of the table to join another one with
     * @param   string $table2  name of another table to join table1 with
     *          (when omitted will remove all previously set joins from table1)
     * @param   string $key1    name of the foreign key in table1 that references table2
     *          (when omitted the API will look up the key in the structure file)
     * @param   string $key2    name of the key in table2 that is referenced from table1
     *          (may be omitted if it is the primary key)
     * @throws  \Yana\Core\InvalidArgumentException  when a required argument is missing
     */
    public function join($table1, $table2 = "", $key1 = "", $key2 = "")
    {
        assert('is_string($table1); // Wrong argument type for argument 1. String expected.');
        assert('is_string($table2); // Wrong argument type for argument 2. String expected.');
        assert('is_string($key1); // Wrong argument type for argument 3. String expected.');
        assert('is_string($key2); // Wrong argument type for argument 4. String expected.');

        /*
         * base table
         */
        if (empty($table1) || $table1 === "*") {
            throw new \Yana\Core\InvalidArgumentException("Wrong parameter count in " . __METHOD__ . "(). " .
                "Unable to join tables, since no table has been selected for function join.");
        }
        $table1 = mb_strtolower($table1);

        /*
         * target table
         */
        if (empty($table2)) {
            /* if second argument is omitted, release association */
            if (isset($this->_joins[$table1])) {
                $this->_joins[$table1] = array();
            }
            return;
        }
        $table2 = mb_strtolower($table2);

        /*
         * source column
         */
        if (empty($key1)) {
            $key1 = null;
        } else {
            $key1 = mb_strtolower($key1);
        }

        /*
         * target column
         */
        if (empty($key2)) {
            $key2 = null;
        } else {
            $key2 = mb_strtolower($key2);
        }

        /*
         * 3) reset old association
         */
        if (isset($this->_joins[$table1][$table2])) {
            unset($this->_joins[$table1][$table2]);
        }

        /*
         * 4) create new association
         */
        $this->_joins[$table1][$table2] = array($key1, $key2);
    }

    /**
     * optional API bypass
     *
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
     * $dbStream->query($sqlStmt, $offset, $limit);
     * // 2nd synopsis
     * $dbStream->query($dbQuery);
     * </code>
     *
     * Note that when providing the DbQuery object, the $limit and $offset arguments are
     * ignored.
     *
     * @access  public
     * @param   string|DbQuery  $sqlStmt  one SQL statement (or a query object) to execute
     * @param   int             $offset   the row to start from
     * @param   int             $limit    the maximum numbers of rows in the resultset
     * @return  mixed
     * @throws  \Yana\Core\InvalidArgumentException if the SQL statement is not valid
     */
    public function query($sqlStmt, $offset = 0, $limit = 0)
    {
        assert('is_int($offset) && $offset >= 0; // Invalid argument $offset. Must be a positive integer.');
        assert('is_int($limit) && $limit >= 0; // Invalid argument $limit. Must be a positive integer.');
        /*
         * 1) check sql statement
         */
        if (is_object($sqlStmt) && $sqlStmt instanceof DbQuery) {
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
            throw new \Yana\Core\InvalidArgumentException('Argument $sqlStmt is expected to be a string.');
        }
        $reg = "/;.*(?:select|insert|delete|update|create|alter|grant|revoke).*$/is";
        if (strpos($sqlStmt, ';') !== false && preg_match($reg, $sqlStmt)) {
            $message = "A semicolon has been found in the current input '{$sqlStmt}', " .
                "indicating multiple queries.\n\t\t As this might be the result of a hacking attempt " .
                "it is prohibited for security reasons and the queries won't be executed.";
            throw new \Yana\Core\InvalidArgumentException($message);
        }

        $dbConnection = $this->getConnection();
        /*
         * 3) send query to database
         */
        if ($offset > 0 || $limit > 0) {
            $dbConnection->setLimit($limit, $offset);
        }
        return $dbConnection->query($sqlStmt);
    }

    /**
     * get the number of entries inside a table
     *
     * Counts and returns the rows of $table.
     *
     * You may also provide the parameter $table as an object of type DbSelectCount.
     * In this case the second argument should be empty.
     * Instead, add a where clause to your query object.
     *
     * Returns 0 if the table is empty or does not exist.
     *
     * @access  public
     * @param   string|DbSelectCount  $table  name of a table
     * @param   array                 $where  optional where clause
     * @return  int
     */
    public function length($table, array $where = array())
    {
        /*
         * 1) input is query object
         */
        if (is_object($table) && $table instanceof DbSelectCount) {

            assert('func_num_args() === 1; // Too many arguments in ' . __METHOD__ . '(). Only 1 argument expected.');
            $countQuery =& $table;

        /*
         * 2) input is table name
         */
        } else {

            assert('is_string($table); // Wrong argument type $table. String expected.');

            // build query
            try {
                $countQuery = new DbSelectCount($this);
                $countQuery->setTable($table); // throws NotFoundException
                $countQuery->setWhere($where);
            } catch (NotFoundException $e) {
                return 0;
            }
        }

        return $countQuery->countResults();
    }

    /**
     * check wether a certain table has no entries
     *
     * Note: if no table is provided, the most recently used
     * table will be tested instead.
     *
     * @access  public
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
     * @uses    $DbStream->exists('table.5')
     *
     * @access  public
     * @param   string|DbSelectExist  $key    adress to check
     * @param   array                 $where  optional where clause
     * @return  bool
     */
    public function exists($key, array $where = array())
    {
        /*
         * 1) input is query object
         */
        if ($key instanceof DbSelectExist) {
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
        $existQuery = new DbSelectExist($this);
        $existQuery->setKey($key);
        if (!empty($where)) {
            $existQuery->setWhere($where);
        }

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
     * @uses $DbStream->isWriteable()
     *
     * @access  public
     * @return  bool
     */
    public function isWriteable()
    {
        return !$this->getSchema()->isReadonly();
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
     * @uses $DbStream->importSQL('some_file.sql')
     *
     * @access  public
     * @param   string|array  $sqlFile filename which contain the SQL statments or an nummeric array of SQL statments.
     * @return  bool
     * @name    DbStream::importsql()
     * @throws  NotWriteableException                when database is readonly
     * @throws  DbWarning                            when database has pending transaction
     * @throws  \Yana\Core\InvalidArgumentException  when argument $sqlFile has an invalid value
     * @throws  NotReadableException                 when SQL file does not exist or is not readable
     * @throws  NotWriteableException                when database is not writeable
     */
    public function importSQL($sqlFile)
    {
        assert('is_string($sqlFile) || is_array($sqlFile); //'.
               'Wrong argument type argument 1. String or array expected');
        // not implemented for type 'generic'
        if ($this->getDBMS() === 'generic') {
            return true;
        }
        // check input and database settings
        if (empty($sqlFile)) {
            throw new \Yana\Core\InvalidArgumentException("Argument \$sqlFile is empty in " . __METHOD__ . "().", E_USER_NOTICE);
        }
        if (!empty($this->_queue)) {
            $message = "Cannot import SQL statements in " . __METHOD__ . "().\n\t\tThere is a pending transaction" .
                "that needs to be committed before proceeding.";
            throw new DbWarning($message, E_USER_NOTICE);
        }
        if ($this->_isWriteable() !== true) {
            $message = "Database connection is not available. Check your connection settings.";
            throw new NotWriteableException($message, E_USER_NOTICE);
        }
        if ($this->getSchema()->isReadonly()) {
            throw new NotWriteableException("Database is readonly. SQL import aborted.", E_USER_NOTICE);
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
                throw new NotReadableException("The file '{$sqlFile}' is not readable.", E_USER_NOTICE);
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
     * get database name
     *
     * This function returns the name of the database as a string.
     *
     * @access  public
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
     * @access  protected
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
     * isError
     *
     * @access  public
     * @param   mixed   $result  result
     * @return  bool
     * @ignore
     */
    public function isError($result)
    {
        if ($result instanceof FileDbResult) {
            return $result->isError();
        } else {
            return MDB2::isError($result);
        }
    }

    /**
     * quote a value
     *
     * Returns the quoted values as a string
     * surrounded by delimiters, depending on
     * the DBMS selected.
     *
     * @access  public
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
     * @access  public
     * @param   mixed  $value   value
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
            /*
             * 1) never quote
             */
            case 'generic':
                return $value;
            break;
            /*
             * 2) always quote
             */
            case 'mysql':
            case 'mysqli':
            case 'postgresql':
            case 'mssql':
                return $this->getConnection()->quoteIdentifier($value);
            break;
            /*
             * 3) quote only where necessary
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
     * check if database is writeable
     *
     * @access  private
     * @return  bool
     * @ignore
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
     * returns true if $name is a known SQL keyword and false otherwise
     *
     * implements quick-search
     * + assume that the input is sorted
     * + assume that the input is upper case
     *
     * this algorithm has O(log(n)) running time
     *
     * @access  private
     * @param   string  $name  SQL keyword
     * @return  bool
     * @ignore
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
        if (\Yana\Util\Hashtable::quickSearch($this->_reservedSqlKeywords, $name) === false) {
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
     * @access  private
     * @param   string  $table  table
     * @param   string  $row    row
     * @return  int
     * @ignore
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
     * @access  public
     * @name    DbStream::reset()
     */
    public function reset()
    {
        $this->_queue = array();
        $this->_cache = array();
    }

    /**
     * Alias of DbStream::reset()
     *
     * @access  public
     * @see     DbStream::reset()
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
     * @access   public
     * @param    \Yana\Core\IsObject $anotherObject  another object to compare
     * @return   string
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        if ($anotherObject instanceof $this) {
            if ($this->getName()->equals($anotherObject->getName()) && $this->database == $anotherObject->database) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Check foreign keys constraints
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
     * @access   protected
     * @param    DDLTable $table       table definition
     * @param    mixed    $value       row or cell value
     * @param    string   $columnName  name of updated column (when updating a cell)
     * @return   bool
     * @ignore
     */
    protected function checkForeignKeys(DDLTable $table, $value, $columnName = null)
    {
        // ignore when strict checks are turned off
        if (!YANA_DB_STRICT) {
            return true;
        }
        if (!is_null($columnName)) {
            $value = array($columnName => $value);
        }
        $dbSchema = $this->getSchema();
        /* @var $foreign DDLForeignKey */
        assert('!isset($foreign); /* Cannot redeclare var $fkey */');
        foreach ($table->getForeignKeys() as $foreign)
        {
            $isPartialMatch = !is_null($columnName) || $foreign->getMatch() === DDLKeyMatchStrategyEnumeration::PARTIAL;
            $isFullMatch = is_null($columnName) && $foreign->getMatch() === DDLKeyMatchStrategyEnumeration::FULL;
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
                            foreach ($this->_cache[$targetTable] as $id => $row)
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
                            $dbExist = new DbSelectExist($this);
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
        } // end foreach
        unset($targetTable, $fkey, $ufkey);
        return true;
    }

    /**
     * execute trigger after commit
     *
     * @access  private
     * @param   array  $list    arguments
     */
    private function _executeTrigger(array $list)
    {
        foreach ($list as $args)
        {
            assert('is_array($args); // Expecting $triggerArgs to array of trigger arugments');
            assert('!empty($args); // List of arguments may not be empty');
            switch ($args[0])
            {
                case DbQueryTypeEnumeration::INSERT:
                    assert('count($args) === 4;');
                    assert('is_array($args[2]);');
                    DbStructureGenerics::onAfterInsert($args[1], $args[2], $args[3]);
                break;
                case DbQueryTypeEnumeration::UPDATE:
                    assert('count($args) === 5;');
                    DbStructureGenerics::onAfterUpdate($args[1], $args[2], $args[3], $args[4]);
                break;
                case DbQueryTypeEnumeration::DELETE:
                    assert('count($args) === 4;');
                    assert('is_array($args[2]);');
                    DbStructureGenerics::onAfterDelete($args[1], $args[2], $args[3]);
                break;
            }
        }
    }

    /**
     * serialize this object to a string
     *
     * Returns the serialized object as a string.
     *
     * @access  public
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
     * @access  public
     * @param   string  $string  string to unserialize
     */
    public function unserialize($string)
    {
        foreach (unserialize($string) as $key => $value)
        {
            $this->$key = $value;
        }
    }

    /**
     * get database connection
     *
     * @access  protected
     * @return  MDB2_Driver_Common
     * @ignore
     */
    protected function getConnection()
    {
        if (!isset($this->database)) {
            $dbServer = new DbServer($this->dsn);
            $this->database = $dbServer->getConnection();
        }
        return $this->database;
    }

}

?>