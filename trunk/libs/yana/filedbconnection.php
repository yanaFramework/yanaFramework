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

/**
 * FileDbConnection
 *
 * Mapper for sql statements to SML-file commands.
 * It implements only a required subset of the interface
 * of PEAR MDB2 as needed by the DbStream class.
 *
 * @access      public
 * @package     yana
 * @subpackage  db
 *
 * @ignore
 */
class FileDbConnection extends \Yana\Core\Object
{

    /**#@+
     * @ignore
     * @access  private
     */

    /** @var Counter     */ private $_autoIncrement = null;
    /** @var \Yana\Db\Ddl\Database */ private $_schema = null;
    /** @var string      */ private $_database = "";
    /** @var \Yana\Db\Ddl\Table    */ private $_table = null;
    /** @var string      */ private $_tableName = "";
    /** @var array       */ private $_src = array();
    /** @var array       */ private $_idx = array();
    /** @var string      */ private $_sort = "";
    /** @var bool        */ private $_desc = false;
    /** @var bool        */ private $_autoCommit = true;
    /** @var array       */ private $_cache = array();
    /** @var DbQuery     */ private $_query = null;
    /** @var int         */ private $_limit = 0;
    /** @var int         */ private $_offset = 0;

    /**#@-*/
    /**
     * @ignore
     * @static
     * @access  private
     * @var     string
     */
    private static $_baseDir = null;

    /**
     * constructor
     *
     * @param  \Yana\Db\Ddl\Database  $schema  database schema
     */
    public function __construct(\Yana\Db\Ddl\Database $schema)
    {
        if (!isset(self::$_baseDir)) {
            self::setBaseDirectory();
        }

        $this->_schema = $schema;
        $this->_database = $schema->getName();
        assert('!empty($this->_database); // database name must not be empty');

        if (!is_dir(self::$_baseDir . $this->_database)) {
            mkdir(self::$_baseDir . $this->_database);
            chmod(self::$_baseDir . $this->_database, 0700);
        }
        if (is_readable(self::$_baseDir . $this->_database)) {
            $this->_src[$this->_database] = array();
            $this->_idx[$this->_database] = array();
            $this->_cache = array();
        }
    }

    /**
     * set base directory
     *
     * Set directory where database files are to be stored.
     * Note: the directory must be read- and writeable.
     *
     * @access  public
     * @static
     * @param   string  $directory  new base directory
     * @ignore
     */
    public static function setBaseDirectory($directory = null)
    {
        // if no directory given load default directory from config
        if (empty($directory)) {
            $directory = \Yana\Db\Ddl\DDL::getDirectory();
        }
        assert('is_dir($directory); // Wrong type for argument 1. Directory expected');
        self::$_baseDir = "$directory";
    }

    /**
     * activate/deactive auto-commit
     *
     * @access  public
     * @param   bool  $mode  on / off
     * @return  int
     */
    public function autoCommit($mode = false)
    {
        assert('is_bool($mode); // Wrong type for argument 1. Boolean expected.');

        if ($mode) {
            $this->_autoCommit = true;
        } else {
            $this->_autoCommit = false;
        }
        return 1;
    }

    /**
     * dummy for compatibility
     *
     * Not implemented. Returns 1.
     *
     * @access  public
     * @param   string  $dummy  module name
     * @return  int
     * @ignore
     */
    public function loadModule($dummy = "")
    {
        return 1;
    }

    /**
     * begin transaction
     *
     * This deactives auto-commit, so the following statements will wait for commit or rollback.
     *
     * @access  public
     * @return  int
     */
    public function beginTransaction()
    {
        $this->_autoCommit = false;
        return 1;
    }

    /**
     * rollback current transaction
     *
     * @access  public
     * @return  int
     */
    public function rollback()
    {
        $this->_cache = array();
        $this->_src[$this->_database][$this->_tableName]->reset();
        $this->_idx[$this->_database][$this->_tableName]->rollback();
        return 1;
    }

    /**
     * commit current transaction
     *
     * @access  public
     * @return  bool
     */
    public function commit()
    {
        return $this->_write(true);
    }

    /**
     * get list of database objects
     *
     * Returns a list of all 'tables' inside the current database,
     * or a list of all available 'databases' as a numeric array.
     * Returns MDB2_ERROR_UNSUPPORTED otherwise.
     *
     * @access  public
     * @param   string  $type  valid values are 'tables' and 'databases'
     * @return  array
     */
    public function getListOf($type)
    {
        assert('is_string($type); // Wrong type for argument 1. String expected.');

        switch($type)
        {
            case 'tables':
                return $this->listTables();
            break;
            case 'databases':
                return self::listDatabases();
            break;
            default:
                if (!defined('MDB2_ERROR_UNSUPPORTED')) {
                    /** @ignore */
                    define('MDB2_ERROR_UNSUPPORTED', -6);
                }
                return MDB2_ERROR_UNSUPPORTED;
            break;
        }
    }

    /**
     * get list of databases
     *
     * @access  public
     * @return  array
     */
    public function listDatabases()
    {
        return \Yana\Db\Ddl\DDL::getListOfFiles();
    }

    /**
     * get list of tables in current database
     *
     * @access  public
     * @param   string  $database  dummy for compatibility
     * @return  array
     */
    public function listTables($database = null)
    {
        return $this->_schema->getTableNames();
    }

    /**
     * get list of functions
     *
     * @access  public
     * @return  array
     */
    public function listFunctions()
    {
        return $this->_schema->getFunctionNames();
    }

    /**
     * get list of functions
     *
     * @access  public
     * @param   string  $database  dummy for compatibility
     * @return  array
     */
    public function listSequences($database = null)
    {
        return $this->_schema->getSequenceNames();
    }

    /**
     * get list of columns
     *
     * @access  public
     * @param   string  $table  table name
     * @return  array
     */
    public function listTableFields($table)
    {
        $table = $this->_schema->getTable($table);
        $columns = array();
        if ($table instanceof \Yana\Db\Ddl\Table) {
            $columns = $table->getColumnNames();
        }
        return $columns;
    }

    /**
     * get list of indexes
     *
     * @access  public
     * @param   string  $table  table name
     * @return  array
     */
    public function listTableIndexes($table)
    {
        $table = $this->_schema->getTable($table);
        $indexes = array();
        if ($table instanceof \Yana\Db\Ddl\Table) {
            foreach ($table->getIndexes() as $name)
            {
                if (is_string($name)) {
                    $indexes[] = $name;
                }
            }
        }
        return $indexes;
    }

    /**
     * execute a single query
     *
     * @access  public
     * @param   DbQuery  &$dbQuery  query object
     * @return  FileDbResult
     * @since   2.9.3
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given query is invalid
     */
    public function dbQuery(&$dbQuery)
    {
        /**
         * Add this line for debugging purposes
         *
         * error_log((string) $dbQuery . "\n", 3, 'test.log');
         */
        $this->_query =& $dbQuery;
        $offset = $this->_query->getOffset();
        $limit  = $this->_query->getLimit();

        switch (true)
        {
            /*
             * 1) SELECT statement
             */
            case $dbQuery instanceof DbSelect:
                $id = $this->_query->toId();
                /*
                 * 1.1) result is cached
                 */
                if (isset($this->_cache[$id])) {
                    /*
                     * 1.1.1) return result
                     */
                    return $this->_cache[$id];
                }
                /*
                 * 1.2) result is not cached
                 */
                try {
                    $this->_select($this->_query->getTable());
                } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                    return new FileDbResult(null, "SQL ERROR: Table '" . $this->_query->getTable() . "' is unknown.");
                }
                /*
                 * 1.2.1) analyse query object
                 */
                $this->_sort = $this->_query->getOrderBy();
                $this->_desc = $this->_query->getDescending();
                $columns = $this->_query->getColumns();
                $where = $this->_query->getWhere();
                $having = $this->_query->getHaving();
                $joins = $this->_query->getJoins();
                /*
                 * 1.2.2) no joined tables (simple request)
                 */
                if (empty($joins)) {
                    /*
                     * 1.2.2.1) fetch result
                     */
                    $result = $this->_get($columns, $where, $having, $offset, $limit);

                /*
                 * 1.2.3) contains joined tables (complex request)
                 */
                } else {
                    /**
                     * {@internal
                     * Note:
                     * Natural joins cannot occur, because DbQuery always adds
                     * column associations when two tables are joined.
                     * Joining tables without an valid association between them
                     * is prohibited and will issue an error.
                     *
                     * As a result we can skip scanning for natural joins here
                     * to improve performance. :-)
                     * }}
                     */

                    /*
                     * 1.2.3.1) resolve where clause
                     */
                    assert('!isset($tableB); // Cannot redeclare var $tableB');
                    assert('!isset($clause); // Cannot redeclare var $tableB');
                    assert('!isset($resultset); // Cannot redeclare var $resultset');
                    assert('!isset($listOfResultSets); // Cannot redeclare var $listOfResultSets');
                    $listOfResultSets = array();
                    foreach ($joins as $tableB => $clause)
                    {
                        $resultset = $this->_join($this->_tableName, $tableB, $clause[0],
                                                  $clause[1], $columns, $where, $clause[2]);
                        if (!empty($resultset)) {
                            $listOfResultSets[] = $resultset;
                        }
                    } // end foreach
                    /*
                     * 1.2.3.2) merge resultsets
                     */
                    $result = array();
                    if (!empty($listOfResultSets)) {
                        assert('!isset($item); // Cannot redeclare var $item');
                        foreach ($listOfResultSets as $item)
                        {
                            if (!empty($item)) {
                                $result = \Yana\Util\Hashtable::merge($result, $item);
                            }
                        }
                        unset($item);
                    } // end if
                    unset($tableB, $clause, $listOfResultSets, $resultset);
                    /*
                     * 1.2.3.3) sorting and limiting
                     */
                    $this->_doSort($result);
                    if (!empty($having)) {
                        $this->_doHaving($result, $having);
                    }
                    $this->_doLimit($result, $offset, $limit);
                } // end if
                $result = new FileDbResult($result);
                /*
                 * 1.2.4) move to cache
                 */
                $this->_cache[$id] = $result;

                /*
                 * 1.2.5) return result
                 */
                return $result;
            break;
            /*
             * 2) SELECT count(*) statement
             */
            case $dbQuery instanceof DbSelectCount:
                $id = $this->_query->toId();
                /*
                 * 1.1) result is cached
                 */
                if (isset($this->_cache[$id])) {
                    /*
                     * 1.1.1) return result
                     */
                    return $this->_cache[$id];
                }
                /*
                 * 1.2) result is not cached
                 */
                try {
                    $this->_select($this->_query->getTable());
                } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                    $message = "SQL ERROR: Table '" . $this->_query->getTable() . "' is unknown.";
                    return new FileDbResult(null, $message);
                }
                $where = $this->_query->getWhere();

                /*
                 * 1.2.1) look up
                 */
                $length = $this->_length($where);

                /*
                 * 1.2.3) create result
                 */
                if (is_int($length)) {
                    $result = new FileDbResult(array(array($length)));
                    /*
                     * 1.2.4) move to cache
                     */
                    $this->_cache[$id] = $result;
                    /*
                     * 1.2.5) return result
                     */
                    return $result;
                    $this->_cache[$id] = $result;
                } else {
                    return new FileDbResult(null, 'SQL ERROR: Syntax error.');
                }
            break;
            /*
             * 3) SELECT 1 statement
             */
            case $dbQuery instanceof DbSelectExist:
                $id = $this->_query->toId();
                /*
                 * 1.1) result is cached
                 */
                if (isset($this->_cache[$id])) {
                    /*
                     * 1.1.1) return result
                     */
                    return $this->_cache[$id];
                }
                /*
                 * 1.2) result is not cached
                 */
                try {
                    $this->_select($this->_query->getTable());
                } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                    $message = "SQL ERROR: Table '" . $this->_query->getTable() . "' is unknown.";
                    return new FileDbResult(null, $message);
                }
                $where = $this->_query->getWhere();

                /*
                 * 1.2.1) look up
                 */
                $length = $this->_length($where);

                if ($length > 0) {
                    $result = new FileDbResult(array(1));
                } else {
                    $result = new FileDbResult(array());
                }
                /*
                 * 1.2.3) move to cache
                 */
                $this->_cache[$id] = $result;
                /*
                 * 1.2.4) return result
                 */
                return $result;
            break;
            /*
             * 4) UPDATE statement
             */
            case $dbQuery instanceof DbUpdate:
                try {
                    $this->_select($this->_query->getTable());
                } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                    $message = "SQL ERROR: Table '" . $this->_query->getTable() . "' is unknown.";
                    return new FileDbResult(null, $message);
                }
                $set = $this->_query->getValues();
                $where = $this->_query->getWhere();
                $this->_sort = $this->_query->getOrderBy();
                $this->_desc = $this->_query->getDescending();

                $row = mb_strtoupper($this->_query->getRow());
                if ($row === '*') {
                    $message = "SQL ERROR: Cannot update entry. No primary key provided.";
                    return new FileDbResult(null, $message);
                }

                /* update cell */
                if ($this->_query->getExpectedResult() === DbResultEnumeration::CELL) {
                    $column = mb_strtoupper($this->_query->getColumn());
                    if ($column !== '*') {
                        $set = array($column => $set);
                    } else {
                        return new FileDbResult(null, "SQL ERROR: Syntax error.");
                    }
                    unset($column);

                /* update row */
                } else {
                    $set = \Yana\Util\Hashtable::changeCase($set, CASE_UPPER);
                }

                assert('!isset($primaryKey); // Cannot redeclare var $primaryKey');
                $primaryKey = mb_strtoupper($this->_table->getPrimaryKey());

                /* get reference to Index file */
                assert('!isset($idxfile); // Cannot redeclare var $idxfile');
                $idxfile = $this->_getIndexFile();

                assert('!isset($columnName); // Cannot redeclare var $columnName');
                assert('!isset($column); // Cannot redeclare var $column');
                foreach ($this->_table->getColumns() as $column)
                {
                    $columnName = mb_strtoupper($column->getName());
                    if (isset($set[$columnName])) {
                        /*
                         * check unique constraint
                         *
                         * Unique constraint is breached if:
                         * 1) an entry with the same key already exists AND
                         * 2) the existing entry is not the same as the one which
                         *    is currently being updated
                         */
                        if ($column->isUnique() === true) {
                            assert('!isset($tmp); // Cannot redeclare var $tmp');
                            $tmp = $idxfile->get($column, $set[$column]);
                            /*
                             * Error - unique constraint has already been breached by some
                             * previous operation.
                             * This may occur, when a unique constraint is added, that
                             * didn't exis before.
                             */
                            if (is_array($tmp) && count($tmp) > 1) {
                                assert('!isset($log); // Cannot redeclare var $log');
                                $log = "SQL WARNING: The column {$column} " .
                                    "has an unique constraint, but multiple " .
                                    "rows with the same name have been found. " .
                                    "This conflict can not be solved automatically. " .
                                    "Please edit and update the affected table.";
                                \Yana\Log\LogManager::getLogger()->addLog($log, E_USER_WARNING, array('affected rows:' => $tmp));
                                unset($log);
                            }
                            /*
                             * error - constraint is breached
                             */
                            if (!empty($tmp) && strcasecmp($tmp, $row) !== 0) {
                                $message = "SQL ERROR: Cannot update entry with column {$column}" .
                                    "= ".$set[$column].". The column has an unique constraint " .
                                    "and another entry with the same value already exists.";
                                return new FileDbResult(null, $message);
                            }
                            unset($tmp);
                        }
                    } // end if
                } // end foreach
                unset($column, $columnName);

                $smlfile =& $this->_getSmlFile();

                /* if primary key is renamed, the old one has to be replaced */
                if (isset($set[$primaryKey]) && strcasecmp($row, $set[$primaryKey]) !== 0) {
                    $smlfile->remove("$primaryKey.$row");
                    $row = mb_strtoupper($set[$primaryKey]);
                    unset($set[$primaryKey]);
                    $set = array($row => $set);
                } elseif ($row != "") {
                    $set = array($row => $set);
                } else {
                    return new FileDbResult(null, "SQL ERROR: Syntax error.");
                } // end if

                /*
                 * remove old array values -
                 * but only if a new value is set
                 */
                assert('!isset($columnName); // Cannot redeclare var $columnName');
                assert('!isset($column); // Cannot redeclare var $column');
                foreach ($set[$row] as $columnName => $column)
                {
                    if (is_array($column)) {
                        $smlfile->setVar("$primaryKey.$row.$columnName", array());
                    } elseif (is_null($column)) {
                        $smlfile->remove("$primaryKey.$row.$columnName");
                    }
                } // end foreach
                unset($column, $columnName);

                // get row
                assert('!isset($currentRow); // Cannot redeclare var $currentRow');
                $currentRow =& $smlfile->getVarByReference($primaryKey);
                if (!empty($currentRow)) {
                    /* update row */
                    $currentRow = \Yana\Util\Hashtable::merge($currentRow, $set);

                    /* after data has been changed, reorganize all indexes */
                    $idxfile->create();

                    return $this->_write();
                } else {

                    return new FileDbResult(null, "unable to save changes");
                } // end if
            break;
            /*
             * 5) INSERT INTO statement
             */
            case $dbQuery instanceof DbInsert:
                try {
                    $this->_select($this->_query->getTable());
                } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                    $message = "SQL ERROR: Table '" . $this->_query->getTable() . "' is unknown.";
                    return new FileDbResult(null, $message);
                }
                $set = $this->_query->getValues();
                assert('!isset($primaryKey); // Cannot redeclare var $primaryKey');
                $primaryKey = $this->_table->getPrimaryKey();
                if (empty($set)) {
                    return new FileDbResult(null, 'SQL ERROR: The statement contains illegal values.');
                } elseif ($this->_table->getColumn($primaryKey)->isAutoIncrement()) {
                    $this->_increment($set);
                }

                if (isset($set[$primaryKey])) {
                    $primaryValue = $set[$primaryKey];
                    unset($set[$primaryKey]);
                } else {
                    $primaryValue = $this->_query->getRow();
                    if ($primaryValue === '*') {
                        $message = "SQL ERROR: Cannot insert entry. No primary key provided.";
                        return new FileDbResult(null, $message);
                    }
                }

                /* get reference to SML file */
                assert('!isset($smlfile); // Cannot redeclare var $smlfile');
                $smlfile = $this->_getSmlFile();
                /* get reference to Index file */
                assert('!isset($idxfile); // Cannot redeclare var $idxfile');
                $idxfile = $this->_getIndexFile();

                /* create column */
                assert('!isset($column); // Cannot redeclare var $column');
                assert('!isset($columnName); // Cannot redeclare var $columnName');
                foreach ($this->_table->getColumns() as $column)
                {
                    $columnName = mb_strtoupper($column->getName());
                    if (isset($set[$columnName])) {
                        /*
                         * check unique constraint
                         *
                         * Unique constraint is breached if:
                         * 1) an entry with the same key already exists
                         */
                        if ($column->isUnique() === true) {
                            assert('!isset($tmp); /* Cannot redeclare var $tmp */');
                            $tmp = $idxfile->get($column, $set[$column]);
                            /*
                             * Error - unique constraint has already been breached by some
                             * previous operation.
                             * This may occur, when a unique constraint is added, that didn't
                             * exist before.
                             */
                            if (is_array($tmp) && count($tmp) > 1) {
                                assert('!isset($log); // Cannot redeclare var $log');
                                $log = "SQL WARNING: The column {$column} " .
                                    "has an unique constraint, but multiple " .
                                    "rows with the same name have been found. " .
                                    "This conflict can not be solved automatically. " .
                                    "Please edit and update the affected table.";
                                \Yana\Log\LogManager::getLogger()->addLog($log, E_USER_WARNING, array('affected rows:' => $tmp));
                                unset($log);
                            }
                            /*
                             * error - constraint is breached
                             */
                            if (!empty($tmp)) {
                                $message = "SQL ERROR: Cannot insert entry with column {$column} " .
                                    "= " . $set[$column] . ". The column has an unique constraint " .
                                    "and another entry with the same value already exists.";
                                return new FileDbResult(null, $message);
                            }
                            unset($tmp);
                        } // end if
                        /*
                         * update indexes
                         *
                         * Index needs to be updated if:
                         * 1) any column which explicitely has an index is updated OR
                         * 2) any column which implicitely requires an index is updated
                         *   (currently that is the case for columns using unique constraints)
                         */
                        if ($column->hasIndex() === true || $column->isUnique() === true) {
                            $idxfile->create($column, array($primaryValue, $set[$column]));
                        } // end if
                    } // end if
                } // end foreach
                unset($column);
                if (is_array($smlfile->getVar("{$primaryKey}.{$primaryValue}"))) {
                    $message = "SQL ERROR: Cannot insert entry with primary key = " .
                        "{$primaryValue}. Another entry with this key already exists.";
                    return new FileDbResult(null, $message);
                } else {
                    $smlfile->setVar("$primaryKey.$primaryValue", $set);
                    return $this->_write();
                } // end if
            break;
            /*
             * 6) DELETE statement
             */
            case $dbQuery instanceof DbDelete:
                try {
                    $this->_select($this->_query->getTable());
                } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                    $message = "SQL ERROR: Table '" . $this->_query->getTable() . "' is unknown.";
                    return new FileDbResult(null, $message);
                }

                $where = $this->_query->getWhere();
                $this->_sort = $this->_query->getOrderBy();
                $this->_desc = $this->_query->getDescending();
                $limit = $this->_query->getLimit();

                $smlfile = $this->_getSmlFile();
                $idxfile = $this->_getIndexFile();

                assert('!isset($rows); // Cannot redeclare var $rows');
                $rows = $this->_get(array($this->_table->getPrimaryKey()), $where, array(), 0, $limit);

                if (empty($rows)) {
                    /* error */

                    if ($dbQuery->getExpectedResult() === DbResultEnumeration::ROW) {
                        return new FileDbResult(null, "SQL-ERROR: unable to delete; entry does not exist");
                    } else {
                        return new FileDbResult(array());
                    }
                } else {
                    assert('!isset($primaryKey); // Cannot redeclare var $primaryKey');
                    $primaryKey = mb_strtoupper($this->_table->getPrimaryKey());
                    assert('!isset($row); // Cannot redeclare var $row');
                    foreach ($rows as $row)
                    {
                        if (!$smlfile->remove($primaryKey.'.'.$row[$primaryKey])) {
                            return new FileDbResult(null, "unable to save changes");
                        } // end if
                    } // end foreach
                    unset($primaryKey, $rows, $row);

                    /* after data has been changed, reorganize all indexes */
                    $idxfile->create();

                    return $this->_write();
                } // end if
            break;
            /*
             * 7) invalid or unknown statement
             */
            default:
                $message = "Invalid or unknown SQL statement: {$this->_query}.";
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_ERROR);
            break;
        }

    }

    /**
     * execute a single query
     *
     * @access  public
     * @param   string  $sqlStmt  one SQL statement to execute
     * @param   int     $offset   the row to start from
     * @param   int     $limit    the maximum number of rows in the resultset
     * @return  FileDbResult
     */
    public function limitQuery($sqlStmt, $offset = 0, $limit = 0)
    {
        /*
         * settype to STRING
         *            INTEGER
         *            INTEGER
         */
        $sqlStmt = (string) $sqlStmt;
        $offset  = (int)    $offset;
        $limit   = (int)    $limit;

        /*
         * 1) parse SQL
         */
        $dbQuery = new DbQueryParser(Yana::connect($this->_database));
        /*
         * 2) error - throw a line if invalid or (possibly) hazardous statement is encountered
         */
        if (!$dbQuery->parseSQL($sqlStmt)) {
            trigger_error("Invalid or unknown SQL statement: $sqlStmt.", E_USER_ERROR);

        /*
         * 3) route to query handling
         */
        } else {
            $dbQuery->setOffset($offset);
            $dbQuery->setLimit($limit);
            return $this->dbQuery($dbQuery, $offset, $limit);
        }
    }

    /**
     * execute a single query
     *
     * alias of limitQuery() with $offset and $limit params stripped
     *
     * @access  public
     * @param   string  $sqlStmt    sql statement
     * @return  FileDbResult
     */
    public function query($sqlStmt)
    {
        assert('is_string($sqlStmt); // Wrong type for argument 1. String expected');
        $offset = $this->_offset;
        $limit = $this->_limit;
        $this->_offset = $this->_limit = 0;
        return $this->limitQuery("$sqlStmt", $offset, $limit);
    }

    /**
     * Set the limit and offset for next query
     *
     * This sets the limit and offset values for the next query.
     * After the query is executed, these values will be reset to 0.
     *
     * @param   int $limit  set the limit for query
     * @param   int $offset set the offset for query
     * @return  int
     */
    public function setLimit($limit, $offset = null)
    {
        assert('is_string($limit); // Wrong type for argument 1. Integer expected');
        assert('is_null($offset) || is_int($offset); // Wrong type for argument 2. Integer expected');
        if ($limit >= 0) {
            $this->_limit = (int) $limit;
        }
        if (!is_null($offset) && $offset >= 0) {
            $this->_offset = (int) $offset;
        }
        return 1;
    }

    /**
     * quote a value
     *
     * Returns the quoted values as a string
     * surrounded by double-quotes.
     *
     * @access  public
     * @param   mixed  $value value too qoute
     * @return  string
     * @ignore
     * @deprecated
     */
    public function quoteSmart($value)
    {
        return $this->quote($value);
    }

    /**
     * quote a value
     *
     * Returns the quoted values as a string
     * surrounded by double-quotes.
     *
     * @access  public
     * @param   mixed  $value value too qoute
     * @return  string
     * @ignore
     */
    public function quote($value)
    {
        return \Yana\Db\Export\DataFactory::quoteValue($value);
    }

    /**
     * quote an identifier
     *
     * Returns the quotes Id as a string
     * surrounded by double-quotes.
     *
     * @access  public
     * @param   string  $value  value
     * @return  string
     * @ignore
     */
    public function quoteIdentifier($value)
    {
        assert('is_string($value); // Invalid argument 1 in ' . __METHOD__ . '(). String expected.');
        return (string) $value;
    }

    /**
     * _select
     *
     * loads a table and returns table name
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  private
     * @param   string  $tableName  teble name
     * @throws  \Yana\Core\Exceptions\NotFoundException  if selected table does not exist
     * @ignore
     */
    private function _select($tableName)
    {
        assert('is_string($tableName); // Wrong type for argument 1. String expected');
        assert('!empty($tableName); // Wrong type for argument 1. String must not be empty');
        $tableName = mb_strtolower(trim("$tableName"));

        // if is cached, set current table from cache
        if (isset($this->_src[$this->_database][$tableName])) {
            $this->_tableName = $tableName;
            $this->_table = $this->_schema->getTable($tableName);
            return;
        }

        /*
         * get associated data-source for selected table
         */
        assert('!isset($table); // Cannot redeclare $table');
        $table = $this->_schema->getTable($tableName);

        if (!$table instanceof \Yana\Db\Ddl\Table) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such table '$tableName'.");
        }
        assert('!isset($parent); // Cannot redeclare $parent');
        $parent = $table->getParent();
        assert('!isset($database); // Cannot redeclare $database');
        // get data source from parent (if it exists)
        if (!$parent instanceof \Yana\Db\Ddl\Database) {
            $database = $this->_schema->getName();
        } else {
            $database = $parent->getName();
        }
        unset($parent);

        if (is_null($database)) {
            $database = $this->_database;
        }
        assert('is_string($database); // Unexpected result: $database must be a string');
        assert('!empty($this->_database); // Unexpected result: $database must not be empty');

        // set current table
        $this->_tableName = $tableName;
        $this->_table = $table;

        /*
         * open source file(s)
         */
        $this->_setSmlFile($database);
        $this->_setIndexFile($database);

        assert('$this->_table instanceof \Yana\Db\Ddl\Table; // Table not found in Schema');
        assert('is_string($this->_tableName); // Unexpected result: $this->_tableName must be a string');
        assert('$this->_tableName !== ""; // Unexpected result: $this->_tableName must not be empty');
    }

    /**
     * _increment
     *
     * simulate MySQL's auto-increment feature
     *
     * @access  private
     * @param   array  &$set    new value of this property
     * @ignore
     */
    private function _increment(array &$set)
    {
        /*
         * 1) initialize counter
         */
        if (is_null($this->_autoIncrement)) {
            try {
                $name = __CLASS__ . '\\' . $this->_database . '\\' . $this->_tableName;
                $this->_autoIncrement = new \Yana\Db\FileDb\Sequence($name);
            } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                \Yana\Db\FileDb\Sequence::create($name);
                $this->_autoIncrement = new \Yana\Db\FileDb\Sequence($name);
                unset($e);
            }
            unset($name);
        }

        /*
         * 2) simulate auto-increment
         */
        $primaryKey = $this->_table->getPrimaryKey();
        if (empty($set[$primaryKey])) {
            $index = $this->_autoIncrement->getNextValue();
            $set[$primaryKey] = $index;
        }
    }

    /**
     * _length
     *
     * simulate select count(*) from ... where ...
     *
     * @access  private
     * @param   array  $where   where clausel
     * @return  int
     * @ignore
     */
    private function _length(array $where)
    {
        /* if table does not exist, then there is nothing to get */
        if (!isset($this->_src[$this->_database][$this->_tableName])) {
            return 0;
        }

        if (empty($where)) {
            $smlfile =& $this->_src[$this->_database][$this->_tableName];
            /*
             * note: the first index is the primary key - NOT the table name
             */
            return $smlfile->length($this->_table->getPrimaryKey());
        } else {
            $result = $this->_get(array(), $where);
            return count($result);
        }
    }

    /**
     * _get
     *
     * @access  private
     * @param   array  $columns     columns
     * @param   array  $where       where clause
     * @param   array  $having      having clause
     * @param   int    $offset      offset
     * @param   int    $limit       limit
     * @return  array
     * @ignore
     */
    private function _get(array $columns = array(), array $where = array(), array $having = array(), $offset = 0, $limit = 0)
    {
        assert('is_int($offset); // Wrong type for argument 3. Integer expected');
        assert('is_int($limit);  // Wrong type for argument 4. Integer expected');
        assert('$offset >= 0;    // Invalid argument 3. Must be a positive integer');
        assert('$limit >= 0;     // Invalid argument 4. Must be a positive integer');
        /*
         * settype to INTEGER
         *            INTEGER
         */
        $limit = (int) $limit;
        $offset = (int) $offset;

        // if table does not exist, then there is nothing to get
        if (!isset($this->_src[$this->_database][$this->_tableName])) {
            return array();
        }

        // initialize vars
        $result     =  array();
        $smlfile    =& $this->_getSmlFile();
        $primaryKey =  mb_strtoupper($this->_table->getPrimaryKey($this->_tableName));
        $data       =  $smlfile->getVar($primaryKey);

        // if the target table is empty ...
        if (!is_array($data)) {
            return array(); // ... then return an empty result-set
        }

        // 1) add primary key column
        assert('!isset($i); // Cannot redeclare var $i');
        // implements a table-scan
        foreach (array_keys($data) as $i)
        {
            $data[$i][$primaryKey] = $i;
        }
        unset($i);

        // 2) pre-sort data
        $this->_doSort($data);

        // 3) apply where clause
        switch ($this->_query->getExpectedResult())
        {
            case DbResultEnumeration::TABLE:
            case DbResultEnumeration::COLUMN:
                $doCollapse = count($columns) > 1;
            break;
            default:
                $doCollapse = false;
            break;
        }
        assert('!isset($current); // Cannot redeclare var $current');
        foreach ($data as $current)
        {
            if (!is_array($current)) {
                continue;
            }
            // implements the resultset
            if ($this->_doWhere($current, $where) === true) {
                $this->_buildResultset($result, $columns, $current, array(), $doCollapse);
            }
        } // end foreach
        unset($current);

        // check having clause
        if (!empty($having)) {
            $this->_doHaving($result, $having);
        }

        // 4) limit results
        $this->_doLimit($result, $offset, $limit);

        // 5) return resultset
        return $result;
    }

    /**
     * build result-set from retrieved raw row-set
     *
     * This adds column aliases and builds the result set with the given raw data on the row retrieved.
     *
     * @access  private
     * @param   array  &$resultSet  result-set the row will get appended to
     * @param   array  $columns     list of: alias => (table name, column name)
     * @param   array  $rowSet      retrieved row-set to append
     * @param   array  $joinedRow   joined row-set
     * @param   bool   $collapse    should duplicate entries be collapsed to a single row?
     */
    private function _buildResultset(array &$result, $columns, array $rowSet, array $joinedRow, $collapse)
    {
        if (empty($columns)) {
            $result[] = \Yana\Util\Hashtable::merge($joinedRow, $rowSet);
            return;
        }
        if ($collapse) {
            $lastResult = count($result);
        }
        assert('!isset($alias); // Cannot redeclare var $alias');
        assert('!isset($column); // Cannot redeclare var $column');
        foreach ($columns as $alias => $column)
        {
            if (is_array($column)) {
                $column = $column[1];
            }
            $column = mb_strtoupper($column);
            if (!is_string($alias)) {
                $alias = $column;
            }
            $value = null;
            if (isset($rowSet[$column])) {
                $value = $rowSet[$column];
            } elseif (isset($joinedRow[$column])) {
                $value = $joinedRow[$column];
            }
            if ($collapse) {
                if (!isset($result[$lastResult])) {
                    $result[$lastResult] = array($alias => $value);
                } else {
                    $result[$lastResult][$alias] = $value;
                }
            } else {
                $result[] = array($alias => $value);
            }
        }
        unset($alias, $column);
    }

    /**
     * sort by column
     *
     * @access  private
     * @param   array  &$result result
     * @ignore
     */
    private function _doSort(array &$result)
    {
        /*
         * If no sort criteria is provided, the array will be returned in the same order
         * in which the entries where inserted. If descending order is requested, the
         * order will be reversed, so the latest entries will come first and the oldest last.
         */
        if (!empty($this->_desc) && empty($this->_sort)) {
            $result = array_reverse($result);
        /*
         * If a column has been provided to sort entries by, then the resultset will get sorted
         * by it.
         */
        } elseif (!empty($this->_sort)) {
            uasort($result, array($this, '_sort'));
        }
    }

    /**
     * _sort
     *
     * @access  private
     * @param   array  $a           1st row
     * @param   array  $b           2nd row
     * @param   array  $columns     sorting columns
     * @param   array  $desc        sorting order
     * @return  int
     * @ignore
     */
    private function _sort(array $a, array $b, array $columns = null, array $desc = null)
    {
        if (is_null($columns)) {
            $columns = $this->_sort;
        }
        if (is_null($desc)) {
            $desc = $this->_desc;
        }
        if (count($columns) === 0) {
            return 0;
        } else {
            $sort = array_shift($columns);
            $sort = mb_strtoupper($sort[1]);
            $isDescending = array_shift($desc);

            if (!is_array($a) || !is_array($b)) {
                return 0;
            } elseif (!isset($a[$sort]) && !isset($b[$sort])) {
                $result = $this->_sort($a, $b, $columns, $desc);
            } elseif (!isset($a[$sort])) {
                $result = -1;
            } elseif (!isset($b[$sort])) {
                $result = 1;
            } elseif ($a[$sort] < $b[$sort]) {
                $result = -1;
            } elseif ($a[$sort] > $b[$sort]) {
                $result = 1;
            } else {
                if (count($columns) > 0) {
                    $result = $this->_sort($a, $b, $columns, $desc);
                } else {
                    $result = 0;
                }
            } // end if
            assert('is_int($result) && $result >= -1 && $result <= 1; // unexpected result');

            if ($isDescending) {
                return - $result;
            } else {
                return $result;
            } // end if
        } // end if
    }

    /**
     * limits and offsets
     *
     * @access  private
     * @param   array  &$result     result
     * @param   int    $offset      offset
     * @param   int    $limit       limit
     * @ignore
     */
    private function _doLimit(array &$result, $offset, $limit)
    {
        if ($limit > 0 || $offset > 0) {
            if ($limit <= 0) {
                $limit = count($result);
            }
            if ($offset < 0) {
                $offset = 0;
            }
            $result = array_slice($result, $offset, $limit);
        }
    }

    /**
     * commit transaction
     *
     * @access  private
     * @param   bool  $commit on / off
     * @return  FileDbResult
     * @ignore
     */
    private function _write($commit = false)
    {
        /* pessimistic scanning */
        assert('is_bool($commit); /* Wrong argument type for argument 1. Boolean expected. */');

        /* optimistic type casting */
        /* settype to BOOLEAN */
        $commit = (bool) $commit;

        $this->_cache = array();

        /* wait for commit */
        if (!$commit && !$this->_autoCommit) {
            return new FileDbResult(array());
        }

        /* commit */
        foreach ($this->_src[$this->_database] as $table)
        {
            if (!$table->exists()) {
                // auto-create missing file
                $table->create();
            }
            if (!$table->write()) {
                // error
                return new FileDbResult(null, 'unable to save changes');
            }
        } /* end for */
        unset($table);
        /* success */
        return new FileDbResult(array());
    }

    /**
     * _join
     *
     * This implements joining two tables.
     *
     * Params are to be read as:
     * <pre>
     * SELECT $columns FROM $tableA
     *     (INNER|LEFT) JOIN $tableB ON $tableA.$columnA = $tableB.$columnB
     *     WHERE $where
     * </pre>
     *
     * @access  private
     * @param   string  $tableA      base table
     * @param   string  $tableB      target table
     * @param   string  $columnA     foreign key in table A
     * @param   string  $columnB     (primary) key in table B
     * @param   array   $columns     column list
     * @param   array   $where       where clause
     * @param   bool    $isLeftJoin  is left join
     * @return  array
     * @ignore
     */
    private function _join($tableA, $tableB, $columnA, $columnB, array $columns, array $where, $isLeftJoin)
    {
        assert('is_string($tableA);  // Wrong argument type for argument 1. String expected.');
        assert('is_string($tableB);  // Wrong argument type for argument 2. String expected.');
        assert('is_string($columnA); // Wrong argument type for argument 3. String expected.');
        assert('is_string($columnB); // Wrong argument type for argument 4. String expected.');

        /* prepare input */
        $tableA  = mb_strtoupper("$tableA");
        $columnA = mb_strtoupper("$columnA");
        $tableB  = mb_strtoupper("$tableB");
        $columnB = mb_strtoupper("$columnB");

        /* the following implements the productive process */

        /* get a cursor on A */
        try {
            $this->_select($tableA); // may throw NotFoundException
            $tableADef = $this->_schema->getTable($tableA);
            $columnADef = $tableADef->getColumn($columnA);
            $aIsPk = $columnADef->isPrimaryKey();
            $pkA = mb_strtoupper($tableADef->getPrimaryKey());
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            return array();
        }
        $SMLA =& $this->_getSmlFile();
        $cursorA = $SMLA->getByReference($pkA);
        if (is_null($cursorA)) {
            return array(); // table A has no entries -> resultset is empty
        }

        /* get a cursor on B */
        try {
            $this->_select($tableB); // may throw NotFoundException
            $tableBDef = $this->_schema->getTable($tableB);
            $columnBDef = $tableBDef->getColumn($columnB);
            $bIsPk = $columnBDef->isPrimaryKey();
            $pkB = mb_strtoupper($tableBDef->getPrimaryKey());
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            return array();
        }
        $SMLB =& $this->_getSmlFile();
        $cursorB = $SMLB->getByReference($pkB);

        /* notify me if results are not valid */
        assert('is_bool($aIsPk); // unexpected result $aIsPk must be a boolean');
        assert('is_bool($bIsPk); // unexpected result $bIsPk must be a boolean');
        assert('is_string($pkA); // unexpected result $pkA must be a string');
        assert('is_string($pkB); // unexpected result $pkB must be a string');

        /* clean up */
        $this->_select($tableA);

        /* this information is needed to optimize performance */
        $indexA =  null;
        $indexB =  null;
        if ($columnADef->hasIndex()) {
            $indexA =& $this->_idx[$this->_database][$this->_tableName];
        }
        if ($columnBDef->hasIndex()) {
            $indexB =& $this->_idx[$this->_database][$this->_tableName];
        }

        if (empty($columns)) {
            $columns = null;
        }

        /* $columnA must be foreign key */
        assert('$columnADef->isForeignKey() ||$columnBDef->isForeignKey() ; // ' .
            "Joining table '{$tableA}' with '{$tableB}' might fail. " .
            "Column '{$columnA}' is not a foreign key in table '{$tableA}'.");
        /* $columnB must be a key */
        assert('$bIsPk || $columnBDef->IsUnique() || $aIsPk || $columnADef->IsUnique(); // ' .
            "Joining table '{$tableA}' with '{$tableB}' might return ambigious results. ".
            "Column '{$columnB}' is not unique in table '{$tableB}'.");
        /* $columnA and $columnB must be of same type */
        assert('$columnBDef->getType() === "reference" || $columnADef->getType() === "reference" || ' .
            '$columnADef->getType() == $columnBDef->getType(); // ' .
            "Note: data type of column '{$tableA}.{$columnA}' ({$columnADef->getType()}) does not match ".
            "column '{$tableB}.{$columnB}' ({$columnBDef->getType()}). This might lead to unexpected results.");

        /**
         * iterate through tables
         *
         * {@internal
         *
         * Note: there are several cases for table A and B
         *
         * - case 1: $columnB is a primary key
         *           enables direct look-up of values in B
         *           with the best possible performance - this should be the default
         *           O(n) running time
         * - case 2: $columnB has an index
         *           enables indirect look-up of values in B via the index
         *           still good performance (currently we assume $columnB is unique)
         *           O(n) running time
         * - case 3: other
         *           will result in expensive recursive table-scans - this scenario should be avoided!
         *           worst case O(n*m) running time
         * }}
         */
        $resultSet = array();
        assert('!isset($row); /* Cannot redeclare var $row */');
        assert('!isset($id);  /* Cannot redeclare var $id */');
        foreach ($cursorA as $id => $row) // get next row from table A
        {
            $value = array();
            if (!is_array($row)) {
                $row = array();
            }
            if (!$isLeftJoin && !$aIsPk && !(isset($row[$columnA]) && is_scalar($row[$columnA]))) {
                continue; // foreign key in table A is NULL
            }

            /* get value of foreign key */
            if ($aIsPk) {
                $row[$columnA] = $id;
                $keyA = mb_strtoupper($id);
            } elseif (isset($row[$columnA])) {
                $keyA = mb_strtoupper($row[$columnA]);
            } else {
                $keyA = null;
            }

            /* case 1: $columnB is a primary key */
            if (is_null($keyA)) {
                $joinedValueExists = false;
            } elseif ($bIsPk === true) {
                if (isset($cursorB[$keyA])) {
                    $joinedValueExists = true;
                    assert('!isset($_value); /* Cannot redeclare var $_value */');
                    $_value = \Yana\Util\Hashtable::merge($row, $cursorB[$keyA]);
                    $_value[$columnB] = $keyA;
                    $value[] = $_value;
                    unset($_value);
                } else {
                    $joinedValueExists = false;
                }
            /* case 2: $columnB has an index */
            } elseif (!is_null($indexB)) {
                $keyA = $indexB->get($columnB, $keyA);
                if (is_scalar($keyA) && isset($cursorB[$keyA])) {
                    $joinedValueExists = true;
                    $value             = array($cursorB[$keyA]);
                } elseif (is_array($keyA) && !empty($keyA)) {
                    $joinedValueExists = true;
                    $value = array();
                    foreach ($keyA as $currentKey)
                    {
                        $value[] =& $cursorB[$currentKey];
                    }
                } else {
                    $joinedValueExists = false;
                    $value = array();
                }
            /* other */
            } else {
                $value = array();
                $joinedValueExists = false;
                foreach ($cursorB as $idB => $rowB)
                {
                    if (isset($rowB[$columnB]) && strcmp($rowB[$columnB], $row[$columnA]) === 0) {
                        $joinedValueExists = true;
                        $row[$pkB]         = $idB;
                        $value[]           = $rowB;
                    }
                }
                if (empty($value)) {
                    $value = null;
                }
            }

            $ignoreTable = null;
            if (!$joinedValueExists) {
                if ($isLeftJoin) {
                    $ignoreTable = $tableBDef;
                    $value = array(array()); // dummy table for left-join
                } else {
                    continue; // values do not match
                }
            }

            assert('!isset($newResult);');
            assert('!isset($currentValue);');
            foreach ($value as $currentValue)
            {
                /* add primary key to result - otherwise it would be missing */
                $currentValue[$pkA] = $id;
                if ($this->_doWhere($currentValue, $where, $ignoreTable) === true) {
                    $this->_buildResultset($resultSet, $columns, $currentValue, $row, true);
                }
            } // end foreach value
            unset($newResult, $currentValue);

        } // end foreach row
        unset($id, $row);

        assert('is_array($resultSet);');
        return $resultSet;
    }

    /**
     * implements having-clause
     *
     * Removes non-matching rows from the resultset.
     *
     * @access  private
     * @param   array  &$result  result set
     * @param   array  &$having  having-clause
     * @ignore
     */
    private function _doHaving(array &$result, array &$having)
    {
        assert('!isset($i); // Cannot redeclare var $i');
        assert('!isset($current); // Cannot redeclare var $current');
        foreach ($result as $i => $current)
        {
            if ($this->_doWhere($current, $having) !== true) {
                unset($result[$i]);
            }
        }
        unset($i, $current);
    }

    /**
     * implements where-clause
     *
     * Each where clause is an array of 3 entries:
     * <ol>
     * <li> left operand </li>
     * <li> operator </li>
     * <li> right operand </li>
     * </ol>
     *
     * List of supported operators:
     * <ul>
     * <li> and, or (indicates that both operands are sub-clauses) </li>
     * <li> =, <>, !=, <, <=, >, >=, like, regexp </li>
     * </ul>
     *
     * Note that not all DBMS support the operator "regexp".
     * Also note that this simulation uses the Perl-compatible regular
     * expressions syntax (PCRE).
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
     * The example above translates to: col1 = 'val1' and (col2 < 1 or col2 > 3).
     *
     * The function returns bool(true) if the where clause matches $current,
     * returns bool(false) otherwise.
     *
     * @access  private
     * @param   array     $current      dataset that is to be checked
     * @param   array     $where        where clause (left operand, right, operand, operator)
     * @param   \Yana\Db\Ddl\Table  $ignoreTable  used to set an overwrite for tables during outer joins
     * @return  bool
     * @ignore
     */
    private function _doWhere(array $current, array $where, \Yana\Db\Ddl\Table $ignoreTable = null)
    {
        if (empty($where)) {
            return true;
        }
        /* if all required information is provided */
        assert('count($where) === 3; // Where clause must have exactly 3 items: left + right operands + operator');
        $leftOperand = array_shift($where);
        $operator = array_shift($where);
        $rightOperand = array_shift($where);

        /**
         * 1) is sub-clause
         */
        switch ($operator)
        {
            case 'or':
                return $this->_doWhere($current, $leftOperand) || $this->_doWhere($current, $rightOperand);
            break;
            case 'and':
                return $this->_doWhere($current, $leftOperand) && $this->_doWhere($current, $rightOperand);
            break;
        }

        /**
         * 2) is singular clause
         */
        $table = null;
        if (is_array($leftOperand)) { // content is: table.column
            $tableName = array_shift($leftOperand); // get table name
            $leftOperand = array_shift($leftOperand); // get just the column
            $table = $this->_schema->getTable($tableName);
            assert('$table instanceof \Yana\Db\Ddl\Table; // Table not found: ' . $tableName);
            unset($tableName);
        } else {
            $table = $this->_table;
        }
        if (isset($current[mb_strtoupper($leftOperand)])) {
            $value = $current[mb_strtoupper($leftOperand)];
        } elseif ($table !== $ignoreTable) {
            $value = null;
        } else {
            return true; // the table is not checked - used for ON-clause during outer joins
        }
        /* handle non-scalar values */
        if (!is_scalar($value)) {
            $value = SML::encode($value);
        }
        /* switch by operator */
        switch ($operator)
        {
            case '<>':
                $operator = '!=';
            case '!=':
            case '=':
                $column = $table->getColumn($leftOperand);
                assert('$column instanceof \Yana\Db\Ddl\Column; // Column not found: ' . $leftOperand);
                if (is_null($rightOperand)) {
                    return is_null($value) xor $operator === '!=';
                }
                if ($column->isPrimaryKey() || $column->isForeignKey()) {
                    return strcasecmp($value, $rightOperand) === 0 xor $operator === '!=';
                }
                return (strcmp($value, $rightOperand) === 0) xor $operator === '!=';
            break;

            case 'like':
                $rightOperand = preg_quote($rightOperand, '/');
                $rightOperand = str_replace('%', '.*', $rightOperand);
                $rightOperand = str_replace('_', '.?', $rightOperand);
            case 'regexp':
                return preg_match('/^' . $rightOperand . '$/is', $value) === 1;
            break;

            case '<':
                return ($value < $rightOperand);
            break;

            case '>':
                return ($value > $rightOperand);
            break;

            case '<=':
                return ($value <= $rightOperand);
            break;

            case '>=':
                return ($value >= $rightOperand);
            break;

            case 'in':
                if ($rightOperand instanceof DbSelect) {
                    $rightOperand = $rightOperand->getResults();
                }
                return (bool) in_array($value, $rightOperand);
            break;

            case 'not in':
                if ($rightOperand instanceof DbSelect) {
                    $rightOperand = $rightOperand->getResults();
                }
                return !in_array($value, $rightOperand);
            break;

            case 'exists':
                if ($rightOperand instanceof DbSelectExist) {
                    return $rightOperand->doesExist();
                } else {
                    return false;
                }
            break;

            case 'not exists':
                if ($rightOperand instanceof DbSelectExist) {
                    return !$rightOperand->doesExist();
                } else {
                    return false;
                }
            break;

            default:
                return true;
            break;
        } // end switch
    }

    /**
     * initialize and return current index file by reference
     *
     * @access  private
     * @return  FileDbIndex
     * @ignore
     */
    private function &_getIndexFile()
    {
        $idxfile =& $this->_idx[$this->_database][$this->_tableName];
        if (!is_object($idxfile)) {
            trigger_error("No index-file found.", E_USER_ERROR);
        }
        return $idxfile;
    }

    /**
     * create index file
     *
     * @access  private
     * @param   string  $database   database name
     * @ignore
     */
    private function _setIndexFile($database = null)
    {
        $filename = $this->_getFilename($database, 'idx');
        $smlfile =& $this->_getSmlFile();
        $idxfile = new FileDbIndex($this->_table, $smlfile, $filename);
        $this->_idx[$this->_database][$this->_tableName] =& $idxfile;
    }

    /**
     * initialize and return current SML file by reference
     *
     * @access  private
     * @return  SML
     * @ignore
     */
    private function &_getSmlFile()
    {
        return $this->_src[$this->_database][$this->_tableName];
    }

    /**
     * create SML file
     *
     * @access  private
     * @param   string  $database  database name
     * @throws  \Yana\Core\Exceptions\NotReadableException  when the SML source file could not be read
     * @ignore
     */
    private function _setSmlFile($database)
    {
        if (!isset($this->_src[$this->_database][$this->_tableName])) {
            $filename = $this->_getFilename($database, 'sml');
            $smlfile = new SML($filename, CASE_UPPER);
            if (!$smlfile->exists()) {
                $smlfile->create();
                if ($database != $this->_database) {
                    $filename = $this->_getFilename($this->_database, 'sml');
                    $smlfile = new SML($filename, CASE_UPPER);
                    if (!$smlfile->exists()) {
                        $smlfile->create();
                    }
                }
            }
            $smlfile->failSafeRead();

            $this->_src[$this->_database][$this->_tableName] =& $smlfile;
        }
    }

    /**
     * return filename
     *
     * @access  private
     * @param   string  $database   database name
     * @param   string  $extension  extension
     * @return  string
     * @ignore
     */
    private function _getFilename($database, $extension)
    {
        return realpath(self::$_baseDir) . '/' . $database . '/' . $this->_tableName . '.' . $extension;
    }

    /**
     * compare with another object
     *
     * Returns bool(true) if this object and $anotherObject
     * are equal and bool(false) otherwise.
     *
     * Two instances are considered equal if and only if
     * they are both objects of the same class and they both
     * refer to the same structure file.
     *
     * @access   public
     * @param    \Yana\Core\IsObject  $anotherObject object to compare
     * @return   string
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        if ($anotherObject instanceof $this) {
            if (!isset($this->_schema) || !isset($anotherObject->_schema)) {
                return isset($this->_schema) === isset($anotherObject->_schema);

            } elseif ($this->_schema->equals($anotherObject->_schema)) {

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}

?>