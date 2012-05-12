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

namespace Yana\Db\FileDb;

/**
 * FileDb-Driver.
 *
 * Mapper for sql statements to SML-file commands.
 * It implements only a required subset of the interface
 * of PEAR MDB2 as needed by the DbStream class.
 *
 * @package     yana
 * @subpackage  db
 */
class Driver extends \Yana\Core\Object implements \Yana\Db\IsDriver
{

    /**
     * @var \Yana\Db\FileDb\Counter
     */
    private $_autoIncrement = null;

    /**
     * @var \Yana\Db\Ddl\Database
     */
    private $_schema = null;

    /**
     * @var string
     */
    private $_database = "";

    /**
     * @var \Yana\Db\Ddl\Table
     */
    private $_table = null;

    /**
     * @var string
     */
    private $_tableName = "";

    /**
     * @var \Yana\Files\SML[][]
     */
    private $_src = array();

    /**
     * @var array
     */
    private $_idx = array();

    /**
     * @var string
     */
    private $_sort = "";

    /**
     * @var bool
     */
    private $_desc = false;

    /**
     * @var bool
     */
    private $_autoCommit = true;

    /** @var array
     */
    private $_cache = array();

    /**
     * @var \Yana\Db\Queries\AbstractQuery
     */
    private $_query = null;

    /**
     * @var int
     */
    private $_limit = 0;

    /**
     * @var int
     */
    private $_offset = 0;

    /**
     * @var string
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
        $this->rollback();
    }

    /**
     * Set directory where database files are to be stored.
     *
     * Note: the directory must be read- and writeable.
     *
     * @param  string  $directory  new base directory
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
     * @return \Yana\Db\Queries\IsParser
     */
    protected function _newSqlParser()
    {
        return \Yana\Db\Queries\Parser(\Yana::connect($this->_database));
    }

    /**
     * begin transaction
     *
     * This deactives auto-commit, so the following statements will wait for commit or rollback.
     *
     * @return  bool
     */
    public function beginTransaction()
    {
        $this->_autoCommit = false;
        return true;
    }

    /**
     * rollback current transaction
     *
     * @return  bool
     */
    public function rollback()
    {
        $this->_cache = array();
        $this->_src[$this->_database] = array();
        $this->_idx[$this->_database] = array();
        return true;
    }

    /**
     * commit current transaction
     *
     * @return  bool
     */
    public function commit()
    {
        return $this->_write(true);
    }

    /**
     * get list of databases
     *
     * @return  array
     */
    public function listDatabases()
    {
        return \Yana\Db\Ddl\DDL::getListOfFiles();
    }

    /**
     * get list of tables in current database
     *
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
     * @return  array
     */
    public function listFunctions()
    {
        return $this->_schema->getFunctionNames();
    }

    /**
     * get list of functions
     *
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
     * Execute a single query.
     *
     * @param   \Yana\Db\Queries\AbstractQuery  $query  query object
     * @return  \Yana\Db\FileDb\Result
     * @throws  \Yana\Db\Queries\Exceptions\NotSupportedException  when given query is invalid
     * @throws  \Yana\Core\Exceptions\DataException                on failure
     */
    public function sendQueryObject(\Yana\Db\Queries\AbstractQuery $query)
    {
        /**
         * Add this line for debugging purposes
         *
         * error_log((string) $query . "\n", 3, 'test.log');
         */

        $this->_query = $query;

        switch (true)
        {
            /*
             * 1) SELECT statement
             */
            case $query instanceof \Yana\Db\Queries\Select:
                return $this->_executeSelectQuery($query);

            /*
             * 2) SELECT count(*) statement
             */
            case $query instanceof \Yana\Db\Queries\SelectCount:
                return $this->_executeSelectCountQuery($query);

            /*
             * 3) SELECT 1 statement
             */
            case $query instanceof \Yana\Db\Queries\SelectExist:
                return $this->_executeSelectExistQuery($query);

            /*
             * 4) UPDATE statement
             */
            case $query instanceof \Yana\Db\Queries\Update:
                return $this->_executeUpdateQuery($query);

            /*
             * 5) INSERT INTO statement
             */
            case $query instanceof \Yana\Db\Queries\Insert:
                return $this->_executeInsertQuery($query);
            /*
             * 6) DELETE statement
             */
            case $query instanceof \Yana\Db\Queries\Delete:
                return $this->_executeDeleteQuery($query);

            /*
             * 7) invalid or unknown statement
             */
            default:
                $message = "Invalid or unknown SQL statement: {$query}.";
                throw new \Yana\Db\Queries\Exceptions\NotSupportedException($message, E_USER_ERROR);
        }

    }

    /**
     * Check foreign keys constraints.
     *
     * This should be called before updating or inserting a row or a cell.
     * Note: This checks only outgoing - not incoming foreign keys!
     *
     * Returns bool(true) if all foreign key constraints are satisfied and bool(false) otherwise.
     *
     * @param    \Yana\Db\Ddl\Table  $table       table definition
     * @param    mixed               $value       row or cell value
     * @param    string              $columnName  name of updated column (when updating a cell)
     * @return   bool
     */
    private function _checkForeignKeys(\Yana\Db\Ddl\Table $table, $value, $columnName = null)
    {
        $row = array();
        if (!is_null($columnName)) {
            $row = array($columnName => $value);
        } else {
            assert('is_array($value); // Invalid argument $value: array expected');
            $row = (array) $value;
        }
        assert('is_array($row);');
        $row = \array_change_key_case($row, \CASE_LOWER);

        /* @var $foreign \Yana\Db\Ddl\ForeignKey */
        assert('!isset($foreign); /* Cannot redeclare var $fkey */');
        foreach ($table->getForeignKeys() as $foreign)
        {
            $isPartialMatch = !is_null($columnName) || $foreign->getMatch() === \Yana\Db\Ddl\KeyMatchStrategyEnumeration::PARTIAL;
            $isFullMatch = is_null($columnName) && $foreign->getMatch() === \Yana\Db\Ddl\KeyMatchStrategyEnumeration::FULL;
            $targetTable = mb_strtolower($foreign->getTargetTable());
            $fTable = $this->_schema->getTable($targetTable);
            if ($fTable instanceof \Yana\Db\Ddl\Table) {
                foreach ($foreign->getColumns() as $sourceColumn => $targetColumn)
                {
                    assert('is_string($sourceColumn);');

                    $isPrimaryKey = false;
                    if (empty($targetColumn)) {
                        $isPrimaryKey = true;
                        $targetColumn = $fTable->getPrimaryKey();
                    }
                    assert('is_string($targetColumn);');
                    $sourceColumn = \mb_strtolower($sourceColumn);
                    $targetColumn = \mb_strtolower($targetColumn);

                    /*
                    * If the referenced row does not exist,
                    * check if there is one recently inserted in cache
                    */
                    if (isset($row[$sourceColumn])) {
                        $isMatch = false;
                        // check if foreign key does match
                        $database = $this->_getSourceDatabaseNameForTable($fTable);

                        // load tablespace
                        if (!isset($this->_src[$this->_database][$targetTable])) {
                            $filename = $this->_getFilename($database, 'sml', $targetTable);
                            $sml = $this->_createSmlFile($filename);
                            $sml->failSafeRead();
                            $this->_src[$this->_database][$targetTable] = $sml;
                        }
                        $sml = $this->_src[$this->_database][$targetTable];
                        assert($sml instanceof \Yana\Files\SML);

                        assert('isset($row[$sourceColumn]);');
                        assert('is_scalar($row[$sourceColumn]); // Value for foreign key must be scalar');

                        if ($isPrimaryKey && $sml->getVar($targetColumn . '.' . $row[$sourceColumn])) {
                            $isMatch = true;
                        } else {
                            assert('!isset($_id); // Cannot redeclare var $_id');
                            assert('!isset($_row); // Cannot redeclare var $_row');
                            foreach ((array) $sml->getVar($fTable->getPrimaryKey()) as $_id => $_row)
                            {
                                $_row[$fTable->getPrimaryKey()] = $_id;
                                $_row = \array_change_key_case($_row, \CASE_LOWER);
                                if (!isset($_row[$targetColumn])) {
                                    continue;
                                }
                                if (strcasecmp($_row[$targetColumn], $row[$sourceColumn]) === 0) {
                                    $isMatch = true;
                                    break;
                                }
                            }
                            unset($_id, $_row);
                        }
                        if ($isMatch && $isPartialMatch) {

                            // for a partial match it is enough if at least one of the keys matches
                            return true;

                        } elseif (!$isMatch) {
                            \Yana\Log\LogManager::getLogger()->addLog("Update on table '{$table->getName()}' failed. " .
                                "Foreign key constraint mismatch. " .
                                "The value '{$row[$sourceColumn]}' for attribute '{$sourceColumn}' " .
                                "refers to a non-existing entry in table '{$targetTable}'. ",
                                E_USER_ERROR, $row);
                            return false;
                        }
                    } elseif ($isFullMatch) {
                        // for a full match the column must not be null
                        return false;
                    }
                } // end foreach (column)
                unset($targetTable, $fkey, $ufkey);
            } // end if (table exists)
        } // end foreach (reference)
        return true;
    }

    /**
     * Execute a SELECT query.
     *
     * @param   \Yana\Db\Queries\Select  $query  query object
     * @return  \Yana\Db\FileDb\Result
     * @throws  \Yana\Core\Exceptions\DataException  on failure
     */
    private function _executeSelectQuery(\Yana\Db\Queries\Select $query)
    {
        $id = $query->toId();
        $offset = $query->getOffset();
        $limit = $query->getLimit();
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
        $this->_select($query->getTable()); // throws exception
        /*
         * 1.2.1) analyse query object
         */
        $this->_sort = $query->getOrderBy();
        $this->_desc = $query->getDescending();
        $columns = $query->getColumns();
        $where = $query->getWhere();
        $having = $query->getHaving();
        $joins = $query->getJoins();
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
                $resultset = $this->_join($this->_tableName, $tableB, $clause[0], $clause[1], $columns, $where, $clause[2]);
                if (!empty($resultset)) {
                    $listOfResultSets[] = $resultset;
                }
                unset($resultset);
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
            unset($tableB, $clause, $listOfResultSets);
            /*
             * 1.2.3.3) sorting and limiting
             */
            $this->_doSort($result);
            if (!empty($having)) {
                $this->_doHaving($result, $having);
            }
            $this->_doLimit($result, $offset, $limit);
        } // end if
        $result = new \Yana\Db\FileDb\Result($result);
        /*
         * 1.2.4) move to cache
         */
        $this->_cache[$id] = $result;

        /*
         * 1.2.5) return result
         */
        return $result;
    }

    /**
     * Execute a SELECT count(*) query.
     *
     * @param   \Yana\Db\Queries\SelectCount  $query  query object
     * @return  \Yana\Db\FileDb\Result
     * @throws  \Yana\Core\Exceptions\DataException  on failure
     */
    private function _executeSelectCountQuery(\Yana\Db\Queries\SelectCount $query)
    {
        $id = $query->toId();
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
        $this->_select($query->getTable()); // throws exception
        $where = $query->getWhere();

        /*
         * 1.2.1) look up
         */
        $length = $this->_length($where);

        /*
         * 1.2.3) create result
         */
        if (!is_int($length)) {
            throw new \Yana\Db\Queries\Exceptions\InvalidSyntaxException('Syntax error.');
        }
        $result = new \Yana\Db\FileDb\Result(array(array($length)));
        /*
         * 1.2.4) move to cache
         */
        $this->_cache[$id] = $result;
        /*
         * 1.2.5) return result
         */
        return $result;
    }

    /**
     * Execute a SELECT 1 query.
     *
     * @param   \Yana\Db\Queries\SelectExist  $query  query object
     * @return  \Yana\Db\FileDb\Result
     * @throws  \Yana\Core\Exceptions\DataException  on failure
     */
    private function _executeSelectExistQuery(\Yana\Db\Queries\SelectExist $query)
    {
        $id = $query->toId();
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
        $this->_select($query->getTable()); // throws exception
        $where = $query->getWhere();

        /*
         * 1.2.1) look up
         */
        $length = $this->_length($where);

        if ($length > 0) {
            $result = new \Yana\Db\FileDb\Result(array(1));
        } else {
            $result = new \Yana\Db\FileDb\Result(array());
        }
        /*
         * 1.2.3) move to cache
         */
        $this->_cache[$id] = $result;
        /*
         * 1.2.4) return result
         */
        return $result;
    }

    /**
     * Execute an UPDATE statement.
     *
     * @param   \Yana\Db\Queries\Update  $query  query object
     * @return  \Yana\Db\FileDb\Result
     * @throws  \Yana\Core\Exceptions\DataException  on failure
     */
    private function _executeUpdateQuery(\Yana\Db\Queries\Update $query)
    {
        $this->_select($query->getTable()); // throws exception
        $set = $query->getValues();

        $this->_sort = $query->getOrderBy();
        $this->_desc = $query->getDescending();

        $row = mb_strtoupper($query->getRow());
        if ($row === '*') {
            $message = "Cannot update entry. No primary key provided.";
            throw new \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException($message);
        }

        if (!$this->_checkForeignKeys($this->_table, $set, $query->getColumn())) {
            $message = "Foreign key check failed on table '{$this->_table->getName()}' for " .
                    "row " . print_r($set, true);
            throw new \Yana\Db\Queries\Exceptions\ConstraintException($message);
        }

        /* update cell */
        if ($query->getExpectedResult() === \Yana\Db\ResultEnumeration::CELL) {
            $column = mb_strtoupper($query->getColumn());
            if ($column === '*') {
                $message = "Syntax error. No column name provided in update statement.";
                throw new \Yana\Db\Queries\Exceptions\InvalidSyntaxException($message);
            }
            $set = array($column => $set);
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
                    $tmp = $idxfile->getVar($column, $set[$column]);
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
                        $message = "Cannot update entry with column {$column}" .
                                "= " . $set[$column] . ". The column has an unique constraint " .
                                "and another entry with the same value already exists.";
                        throw new \Yana\Db\Queries\Exceptions\DuplicateValueException($message);
                    }
                    unset($tmp);
                }
            } // end if
            unset($columnName);
        } // end foreach
        unset($column);

        $smlfile = $this->_getSmlFile();

        /* if primary key is renamed, the old one has to be replaced */
        if (isset($set[$primaryKey]) && strcasecmp($row, $set[$primaryKey]) !== 0) { // updating primary key
            $smlfile->remove("$primaryKey.$row"); // remove old row
            $row = mb_strtoupper($set[$primaryKey]);
            unset($set[$primaryKey]);
            $set = array($row => $set); // insert as new
        } elseif ($row != "") { // inserting a new row
            $set = array($row => $set);
        } else { // missing primary key
            $message = "Syntax error. No primary key given.";
            throw new \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException($message);
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
        $currentRow = & $smlfile->getVarByReference($primaryKey);
        if (empty($currentRow)) {
            $message = "Unable to save changes because the selected row was not found. Table may be corrupt.";
            throw new \Yana\Db\DatabaseException($message);
        }
        /* update row */
        $currentRow = \Yana\Util\Hashtable::merge($currentRow, $set);

        /* after data has been changed, reorganize all indexes */
        $idxfile->create();

        return $this->_write();
    }

    /**
     * Execute an INSERT statement.
     *
     * @param   \Yana\Db\Queries\Insert  $query  query object
     * @return  \Yana\Db\FileDb\Result
     * @throws  \Yana\Core\Exceptions\DataException  on failure
     */
    private function _executeInsertQuery(\Yana\Db\Queries\Insert $query)
    {
        $this->_select($query->getTable());
        $set = $query->getValues();

        if (!$this->_checkForeignKeys($this->_table, $set)) {
            $message = "Foreign key check failed on table '{$this->_table->getName()}' for " .
                    "row " . print_r($set, true);
            throw new \Yana\Db\Queries\Exceptions\InconsistencyException($message);
        }

        assert('!isset($primaryKey); // Cannot redeclare var $primaryKey');
        $primaryKey = $this->_table->getPrimaryKey();
        if (empty($set)) {
            $message = 'The statement contains illegal values.';
            throw new \Yana\Db\Queries\Exceptions\InvalidSyntaxException($message);
        }

        if ($this->_table->getColumn($primaryKey)->isAutoIncrement()) {
            $this->_increment($set);
        }

        if (isset($set[$primaryKey])) {
            $primaryValue = $set[$primaryKey];
            unset($set[$primaryKey]);
        } else {
            $primaryValue = $query->getRow();
            if ($primaryValue === '*') {
                $message = "Cannot insert entry. No primary key provided.";
                throw new \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException($message);
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
                    $tmp = $idxfile->getVar($column, $set[$column]);
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
                        throw new \Yana\Db\Queries\Exceptions\DuplicateValueException($message);
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
            throw new \Yana\Db\Queries\Exceptions\DuplicateValueException($message);
        }

        $smlfile->setVar("$primaryKey.$primaryValue", $set);
        return $this->_write();
    }

    /**
     * Execute a DELETE statement.
     *
     * @param   \Yana\Db\Queries\Delete  $query  query object
     * @return  \Yana\Db\FileDb\Result
     * @throws  \Yana\Core\Exceptions\DataException  on failure
     */
    private function _executeDeleteQuery(\Yana\Db\Queries\Delete $query)
    {
        $this->_select($query->getTable()); // throws exception

        $where = $query->getWhere();
        $this->_sort = $query->getOrderBy();
        $this->_desc = $query->getDescending();
        $limit = $query->getLimit();

        $smlfile = $this->_getSmlFile();
        $idxfile = $this->_getIndexFile();

        assert('!isset($rows); // Cannot redeclare var $rows');
        $rows = $this->_get(array($this->_table->getPrimaryKey()), $where, array(), 0, $limit);

        if (empty($rows)) {
            /* error */

            if ($query->getExpectedResult() === \Yana\Db\ResultEnumeration::ROW) {
                $message = "Unable to delete the row. The row you tried to delete was not found.";
                throw new \Yana\Core\Exceptions\NotFoundException($message);
            }
            return new \Yana\Db\FileDb\Result(array());
        }

        assert('!isset($primaryKey); // Cannot redeclare var $primaryKey');
        $primaryKey = mb_strtoupper($this->_table->getPrimaryKey());
        assert('!isset($row); // Cannot redeclare var $row');
        foreach ($rows as $row)
        {
            if (!$smlfile->remove($primaryKey . '.' . $row[$primaryKey])) {
                throw new \Yana\Db\DatabaseException("Unable to save changes.");
            }
        } // end foreach
        unset($primaryKey, $rows, $row);

        /* after data has been changed, reorganize all indexes */
        $idxfile->create();

        return $this->_write();
    }

    /**
     * Execute a single query.
     *
     * @param   string  $sqlStmt  one SQL statement to execute
     * @param   int     $offset   the row to start from
     * @param   int     $limit    the maximum number of rows in the resultset
     * @return  \Yana\Db\FileDb\Result
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     */
    public function limitQuery($sqlStmt, $offset = 0, $limit = 0)
    {
        // parse SQL
        $queryParser = $this->_newSqlParser();
        $dbQuery = $queryParser->parseSQL((string) $sqlStmt); // throws exception

        // route to query handling
        $dbQuery->setOffset((int) $offset);
        $dbQuery->setLimit((int) $limit);
        return $this->sendQueryObject($dbQuery);
    }

    /**
     * Execute a single query.
     *
     * Alias of limitQuery() with $offset and $limit params stripped.
     *
     * @param   string  $sqlStmt  some SQL statement
     * @return  \Yana\Db\FileDb\Result
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     */
    public function sendQueryString($sqlStmt)
    {
        assert('is_string($sqlStmt); // Wrong type for argument 1. String expected');
        $offset = (int) $this->_offset;
        $limit = (int) $this->_limit;
        $this->_offset = $this->_limit = 0; // reset for next query

        // parse SQL
        $queryParser = $this->_newSqlParser();
        $dbQuery = $queryParser->parseSQL($sqlStmt); // throws exception

        // route to query handling
        $dbQuery->setOffset($offset);
        $dbQuery->setLimit($limit);
        return $this->sendQueryObject($dbQuery);
    }

    /**
     * Set the limit and offset for next query
     *
     * This sets the limit and offset values for the next query.
     * After the query is executed, these values will be reset to 0.
     *
     * @param   int $limit  set the limit for query
     * @param   int $offset set the offset for query
     * @return  bool
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
        return true;
    }

    /**
     * quote a value
     *
     * Returns the quoted values as a string
     * surrounded by double-quotes.
     *
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
     * Loads a table and returns table name.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $tableName  table name
     * @return  \Yana\Db\FileDb\Driver
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException  if selected table does not exist
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
            assert('$this->_table instanceof \Yana\Db\Ddl\Table; // Table not found in Schema');
            return $this;
        }

        /*
         * get associated data-source for selected table
         */
        assert('!isset($table); // Cannot redeclare $table');
        $table = $this->_schema->getTable($tableName);

        if (!$table instanceof \Yana\Db\Ddl\Table) {
            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException("No such table '$tableName'.");
        }
        $database = $this->_getSourceDatabaseNameForTable($table);

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
        return $this;
    }

    /**
     * Get the source database for the table given.
     *
     * A schema/database may include other databases/schemas that define their own tables.
     * These tables will then be known within the namespace of the importing database.
     *
     * Still the included tables refer to their own database name.
     * Thus a tablespace is not necessarily always found in the database's directory, but
     * in the directory of the table's parent database.
     *
     * This function thus looks up and returns the correct database name of a table.
     *
     * @param   \Yana\Db\Ddl\Table  $table  table to look up
     * @return  string
     * @throws  \Yana\Core\Exceptions\NotFoundException
     */
    private function _getSourceDatabaseNameForTable(\Yana\Db\Ddl\Table $table)
    {
        assert('!isset($parent); // Cannot redeclare $parent');
        $parent = $table->getParent();
        assert('!isset($database); // Cannot redeclare $database');
        // get data source from parent (if it exists)
        $databaseName = ($parent instanceof \Yana\Db\Ddl\Database) ? $parent->getName() : $this->_schema->getName();
        unset($parent);

        if (is_null($databaseName)) {
            assert('!empty($this->_database); // Unexpected result: $database must not be empty');
            $databaseName = $this->_database;
        }
        assert('is_string($databaseName); // Unexpected result: $databaseName must be a string');
        return $databaseName;
    }

    /**
     * Simulate MySQL's auto-increment feature.
     *
     * @param   array  &$set    new value of this property
     * @throws  \Yana\Db\Queries\Exceptions\NotFoundException  when unable to load the sequence
     */
    private function _increment(array &$set)
    {
        /*
         * 1) initialize counter
         */
        if (is_null($this->_autoIncrement)) {
            $name = __CLASS__ . '\\' . $this->_database . '\\' . $this->_tableName;
            try {
                $sequence = new \Yana\Db\FileDb\Sequence($name);
            } catch (\Yana\Db\Queries\Exceptions\NotFoundException $e) {
                \Yana\Db\FileDb\Sequence::create($name);
                $sequence = new \Yana\Db\FileDb\Sequence($name);
                unset($e);
            }
            unset($name);
            $this->_autoIncrement = $sequence;
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
     * Simulate select count(*) from ... where ...
     *
     * @param   array  $where   where clausel
     * @return  int
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
     * Executes a Select-statement.
     *
     * @param   array  $columns     columns
     * @param   array  $where       where clause
     * @param   array  $having      having clause
     * @param   int    $offset      offset
     * @param   int    $limit       limit
     * @return  array
     */
    private function _get(array $columns = array(), array $where = array(), array $having = array(), $offset = 0, $limit = 0)
    {
        assert('is_int($offset); // Wrong type for argument 3. Integer expected');
        assert('is_int($limit);  // Wrong type for argument 4. Integer expected');
        assert('$offset >= 0;    // Invalid argument 3. Must be a positive integer');
        assert('$limit >= 0;     // Invalid argument 4. Must be a positive integer');

        $limit = (int) $limit;
        $offset = (int) $offset;

        // if table does not exist, then there is nothing to get
        if (!isset($this->_src[$this->_database][$this->_tableName])) {
            return array();
        }

        // initialize vars
        $result     = array();
        $smlfile    = $this->_getSmlFile();
        $primaryKey = mb_strtoupper($this->_table->getPrimaryKey($this->_tableName));
        $data       = $smlfile->getVar($primaryKey);

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
            case \Yana\Db\ResultEnumeration::TABLE:
            case \Yana\Db\ResultEnumeration::COLUMN:
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
     * @param  array  &$result result
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
     * Sorting function used for uasort().
     *
     * @param   array  $a        1st row
     * @param   array  $b        2nd row
     * @param   array  $columns  sorting columns
     * @param   array  $desc     sorting order
     * @return  int
     */
    private function _sort(array $a, array $b, array $columns = null, array $desc = null)
    {
        if (is_null($columns)) {
            $columns = $this->_sort;
        }
        if (is_null($desc)) {
            $desc = $this->_desc;
        }
        if (count($columns) === 0 || !is_array($a) || !is_array($b)) {
            return 0;
        }

        $sortArray = array_shift($columns);
        $sort = mb_strtoupper($sortArray[1]);
        $isDescending = array_shift($desc);
        unset($sortArray);

        switch (true)
        {

            case (!isset($a[$sort]) && !isset($b[$sort])):
                $result = $this->_sort($a, $b, $columns, $desc);
                break;

            case (!isset($a[$sort])):
                $result = -1;
                break;

            case (!isset($b[$sort])):
                $result = 1;
                break;

            case ($a[$sort] < $b[$sort]):
                $result = -1;
                break;

            case ($a[$sort] > $b[$sort]):
                $result = 1;
                break;

            case count($columns) > 0:
                $result = $this->_sort($a, $b, $columns, $desc);
                break;

            default:
                $result = 0;
                break;

        } // end switch

        assert('is_int($result) && $result >= -1 && $result <= 1; // unexpected result');

        return ($isDescending) ? - $result : $result;
    }

    /**
     * limits and offsets
     *
     * @param  array  &$result  result
     * @param  int    $offset   offset
     * @param  int    $limit    limit
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
     * @param   bool  $commit on / off
     * @return  \Yana\Db\FileDb\Result
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
            return new \Yana\Db\FileDb\Result(array());
        }

        /* commit */
        foreach ($this->_src[$this->_database] as $table)
        {
            /* @var $table \SML */
            if (!$table->exists()) {

                $table->create(); // auto-create missing file
            }
            if (!$table->write()) {

                throw new \Yana\Db\DatabaseException('unable to save changes');

            }
        } /* end for */
        unset($table);
        /* success */
        return new \Yana\Db\FileDb\Result(array());
    }

    /**
     * This implements joining two tables.
     *
     * Params are to be read as:
     * <pre>
     * SELECT $columns FROM $tableA
     *     (INNER|LEFT) JOIN $tableB ON $tableA.$columnA = $tableB.$columnB
     *     WHERE $where
     * </pre>
     *
     * @param   string  $tableA      base table
     * @param   string  $tableB      target table
     * @param   string  $columnA     foreign key in table A
     * @param   string  $columnB     (primary) key in table B
     * @param   array   $columns     column list
     * @param   array   $where       where clause
     * @param   bool    $isLeftJoin  is left join
     * @return  array
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
        $SMLA = $this->_getSmlFile();
        $cursorA = $SMLA->getVarByReference($pkA);
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
        $SMLB = $this->_getSmlFile();
        $cursorB = $SMLB->getVarByReference($pkB);

        /* notify me if results are not valid */
        assert('is_bool($aIsPk); // unexpected result $aIsPk must be a boolean');
        assert('is_bool($bIsPk); // unexpected result $bIsPk must be a boolean');
        assert('is_string($pkA); // unexpected result $pkA must be a string');
        assert('is_string($pkB); // unexpected result $pkB must be a string');

        /* clean up */
        $this->_select($tableA);

        /* this information is needed to optimize performance */
        $indexA =  null;
        /* @var $indexB \FileDbIndex */
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
                $keyA = $indexB->getVar($columnB, $keyA);
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
     * Implements having-clause.
     *
     * Removes non-matching rows from the resultset.
     *
     * @param  array  &$result  result set
     * @param  array  &$having  having-clause
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
     * Implements where-clause.
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
     * @param   array               $current      dataset that is to be checked
     * @param   array               $where        where clause (left operand, right, operand, operator)
     * @param   \Yana\Db\Ddl\Table  $ignoreTable  used to set an overwrite for tables during outer joins
     * @return  bool
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

            case 'and':
                return $this->_doWhere($current, $leftOperand) && $this->_doWhere($current, $rightOperand);

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
            $value = \Yana\Files\SML::encode($value);
        }
        /* switch by operator */
        switch ($operator)
        {
            case '<>':
                // fall through
                $operator = '!=';
            case '!=':
                // fall through
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

            case 'like':
                $rightOperand = preg_quote($rightOperand, '/');
                $rightOperand = str_replace('%', '.*', $rightOperand);
                $rightOperand = str_replace('_', '.?', $rightOperand);
                // fall through
            case 'regexp':
                return preg_match('/^' . $rightOperand . '$/is', $value) === 1;

            case '<':
                return ($value < $rightOperand);

            case '>':
                return ($value > $rightOperand);

            case '<=':
                return ($value <= $rightOperand);

            case '>=':
                return ($value >= $rightOperand);

            case 'in':
                if ($rightOperand instanceof \Yana\Db\Queries\Select) {
                    $rightOperand = $rightOperand->getResults();
                }
                return (bool) in_array($value, $rightOperand);

            case 'not in':
                if ($rightOperand instanceof \Yana\Db\Queries\Select) {
                    $rightOperand = $rightOperand->getResults();
                }
                return !in_array($value, $rightOperand);

            case 'exists':
                return ($rightOperand instanceof \Yana\Db\Queries\SelectExist) && $rightOperand->doesExist();

            case 'not exists':
                return ($rightOperand instanceof \Yana\Db\Queries\SelectExist) && !$rightOperand->doesExist();

            default:
                return true;
        } // end switch
    }

    /**
     * initialize and return current index file by reference
     *
     * @return  \Yana\Db\FileDb\Index
     */
    private function _getIndexFile()
    {
        if (!isset($this->_idx[$this->_database][$this->_tableName])) {
            $message = "Index-file not found for databae {$this->_database} table {$this->_tableName}. Is the directory writable?";
            throw new \Yana\Db\DatabaseException($message, E_USER_ERROR);
        }
        return $this->_idx[$this->_database][$this->_tableName];
    }

    /**
     * create index file
     *
     * @param  string  $database  database name
     */
    private function _setIndexFile($database)
    {
        $filename = $this->_getFilename($database, 'idx');
        $smlfile = $this->_getSmlFile();
        $idxfile = new \Yana\Db\FileDb\Index($this->_table, $smlfile, $filename);
        $this->_idx[$this->_database][$this->_tableName] = $idxfile;
    }

    /**
     * initialize and return current SML file by reference
     *
     * @return  \Yana\Files\SML
     */
    private function _getSmlFile()
    {
        return $this->_src[$this->_database][$this->_tableName];
    }

    /**
     * create SML file
     *
     * @param   string  $database  database name
     * @throws  \Yana\Core\Exceptions\NotReadableException  when the SML source file could not be read
     */
    private function _setSmlFile($database)
    {
        if (!isset($this->_src[$this->_database][$this->_tableName])) {
            $filename = $this->_getFilename($database, 'sml');
            $isCreated = false;
            $smlfile = $this->_createSmlFile($filename, $isCreated);
            if ($isCreated && $database != $this->_database) {
                $filename = $this->_getFilename($this->_database, 'sml');
                $smlfile = $this->_createSmlFile($filename);
            }
            $smlfile->failSafeRead();

            $this->_src[$this->_database][$this->_tableName] = $smlfile;
        }
    }

    /**
     * Creates and returns a new SML instance.
     *
     * if the file does not exist, it is created.
     *
     * @param  string  $filename
     * @param  bool    &$isCreated  
     * @return \Yana\Files\SML
     */
    private function _createSmlFile($filename, &$isCreated = false)
    {
        assert('is_string($filename); // Invalid argument $filename: string expected');

        $smlfile = new \Yana\Files\SML($filename, CASE_UPPER);
        if (!$smlfile->exists()) {
            $isCreated = true;
            $smlfile->create();
        }
        return $smlfile;
    }

    /**
     * Return path to database SML file.
     *
     * @param   string  $database   database name in lower-cased letters
     * @param   string  $extension  extension
     * @param   string  $tableName  name of the table in lower-cased letters
     * @return  string
     */
    private function _getFilename($database, $extension, $tableName = "")
    {
        assert('is_string($database); // Invalid argument $database: string expected');
        assert('is_string($extension); // Invalid argument $extension: string expected');
        assert('is_string($tableName); // Invalid argument $tableName: string expected');

        if (empty($tableName)) {
            $tableName = $this->_tableName;
        }

        return realpath(self::$_baseDir) . '/' . $database . '/' . $tableName . '.' . $extension;
    }

    /**
     * Compare with another object.
     *
     * Returns bool(true) if this object and $anotherObject
     * are equal and bool(false) otherwise.
     *
     * Two instances are considered equal if and only if
     * they are both objects of the same class and they both
     * refer to the same structure file.
     *
     * @param    \Yana\Core\IsObject  $anotherObject object to compare
     * @return   string
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        if ($anotherObject instanceof $this) {
            if (!isset($this->_schema) || !isset($anotherObject->_schema)) {
                return isset($this->_schema) === isset($anotherObject->_schema);
            }
            return (bool) $this->_schema->equals($anotherObject->_schema);
        }
        return false;
    }

}

?>