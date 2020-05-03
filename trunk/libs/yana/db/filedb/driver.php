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
 *
 * @package     yana
 * @subpackage  db
 */
class Driver extends \Yana\Db\FileDb\AbstractDriver
{

    /**
     * @var  \Yana\Files\SML[][]
     */
    private $_src = array();

    /**
     * @var  array
     */
    private $_idx = array();

    /** @var  array
     */
    private $_cache = array();

    /**
     * @var  \Yana\Db\Queries\IsParser
     */
    private $_sqlParser = null;
 
    /**
     * @var string
     */
    private static $_baseDir = null;

    /**
     * <<constructor>> Initialize query parser.
     *
     * @param  \Yana\Db\Queries\IsParser  $parser  to handle SQL statements
     */
    public function __construct(\Yana\Db\Queries\IsParser $parser)
    {
        // @codeCoverageIgnoreStart
        if (!isset(self::$_baseDir)) {
            // if no directory given load default directory from config
            self::setBaseDirectory(\Yana\Db\Ddl\DDL::getDirectory());
        }
        // @codeCoverageIgnoreEnd
        $this->_setSqlParser($parser);

        $this->_setSchema($parser->getSchema());
        $databaseName = $this->_getSchema()->getName();
        assert($databaseName > "", 'database name must not be empty');
        $this->_setDatabaseName($databaseName);

        if (!is_dir(self::$_baseDir . $this->_getDatabaseName())) {
            // @codeCoverageIgnoreStart
            mkdir(self::$_baseDir . $this->_getDatabaseName());
            chmod(self::$_baseDir . $this->_getDatabaseName(), 0700);
            // @codeCoverageIgnoreEnd
        }
        $this->rollback();
    }

    /**
     * Returns SQL parser.
     *
     * @return  \Yana\Db\Queries\IsParser
     */
    protected function _getSqlParser()
    {
        return $this->_sqlParser;
    }

    /**
     * Inject SQL parser.
     *
     * @param   \Yana\Db\Queries\IsParser  $parser  to handle SQL statements
     * @return  self
     */
    protected function _setSqlParser(\Yana\Db\Queries\IsParser $parser)
    {
        $this->_sqlParser = $parser;
        return $this;
    }

    /**
     * Set directory where database files are to be stored.
     *
     * Note: the directory must be read- and writeable.
     *
     * @param  string  $directory  new base directory
     * @ignore
     */
    public static function setBaseDirectory($directory)
    {
        assert(is_dir($directory), 'Wrong type for argument 1. Directory expected');
        self::$_baseDir = "$directory";
    }

    /**
     * Get directory where database files are to be stored.
     *
     * @return  string
     * @ignore
     */
    public static function getBaseDirectory()
    {
        return self::$_baseDir;
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
        $this->_setAutoCommit(false);
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
        $this->_src[$this->_getDatabaseName()] = array();
        $this->_idx[$this->_getDatabaseName()] = array();
        return true;
    }

    /**
     * commit current transaction
     *
     * @return  bool
     */
    public function commit()
    {
        $this->_write(true);
        return true;
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
     * @return  array
     */
    public function listTables()
    {
        return $this->_getSchema()->getTableNames();
    }

    /**
     * get list of functions
     *
     * @return  array
     */
    public function listFunctions()
    {
        return $this->_getSchema()->getFunctionNames();
    }

    /**
     * get list of functions
     *
     * @param   string  $database  dummy for compatibility
     * @return  array
     */
    public function listSequences($database = null)
    {
        return $this->_getSchema()->getSequenceNames();
    }

    /**
     * get list of columns
     *
     * @param   string  $table  table name
     * @return  array
     */
    public function listTableFields($table)
    {
        $table = $this->_getSchema()->getTable($table);
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
        $table = $this->_getSchema()->getTable($table);
        $indexes = array();
        if ($table instanceof \Yana\Db\Ddl\Table) {
            foreach ($table->getIndexes() as $index)
            {
                $indexes[] = $index->getName();
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

        $this->_setQuery($query);

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
                throw new \Yana\Db\Queries\Exceptions\NotSupportedException($message, \Yana\Log\TypeEnumeration::ERROR);
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
            assert(is_array($value), 'Invalid argument $value: array expected');
            $row = (array) $value;
        }
        assert(is_array($row), 'is_array($row)');
        $row = \array_change_key_case($row, \CASE_LOWER);

        /* @var $foreign \Yana\Db\Ddl\ForeignKey */
        assert(!isset($foreign), 'Cannot redeclare var $fkey');
        foreach ($table->getForeignKeys() as $foreign)
        {
            $isPartialMatch = !is_null($columnName) || $foreign->getMatch() === \Yana\Db\Ddl\KeyMatchStrategyEnumeration::PARTIAL;
            $isFullMatch = is_null($columnName) && $foreign->getMatch() === \Yana\Db\Ddl\KeyMatchStrategyEnumeration::FULL;
            $targetTable = mb_strtolower($foreign->getTargetTable());
            $fTable = $this->_getSchema()->getTable($targetTable);
            if ($fTable instanceof \Yana\Db\Ddl\Table) {
                foreach ($foreign->getColumns() as $sourceColumn => $targetColumn)
                {
                    assert(is_string($sourceColumn), 'is_string($sourceColumn)');

                    $isPrimaryKey = false;
                    if (empty($targetColumn)) {
                        $isPrimaryKey = true;
                        $targetColumn = $fTable->getPrimaryKey();
                    }
                    assert(is_string($targetColumn), 'is_string($targetColumn)');
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
                        if (!isset($this->_src[$this->_getDatabaseName()][$targetTable])) {
                            $filename = $this->_getFilename($database, 'sml', $targetTable);
                            $sml = $this->_createSmlFile($filename);
                            $sml->failSafeRead();
                            $this->_src[$this->_getDatabaseName()][$targetTable] = $sml;
                        }
                        $sml = $this->_src[$this->_getDatabaseName()][$targetTable];
                        assert($sml instanceof \Yana\Files\SML);

                        assert(isset($row[$sourceColumn]), 'isset($row[$sourceColumn])');
                        assert(is_scalar($row[$sourceColumn]), 'Value for foreign key must be scalar');

                        if ($isPrimaryKey && $sml->getVar($targetColumn . '.' . $row[$sourceColumn])) {
                            $isMatch = true;
                        } else {
                            assert(!isset($_id), 'Cannot redeclare var $_id');
                            assert(!isset($_row), 'Cannot redeclare var $_row');
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
                                \Yana\Log\TypeEnumeration::ERROR, $row);
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
        $this->_setSortColumns($query->getOrderBy());
        $this->_setDescendingSortColumns($query->getDescending());
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
            assert(!isset($joinCondition), 'Cannot redeclare var $joinCondition');
            assert(!isset($resultset), 'Cannot redeclare var $resultset');
            assert(!isset($listOfResultSets), 'Cannot redeclare var $listOfResultSets');
            $listOfResultSets = array();
            foreach ($joins as $joinCondition)
            {
                /** @var \Yana\Db\Queries\JoinCondition $joinCondition */
                $resultset = $this->_join(
                    $joinCondition->getSourceTableName(),
                    $joinCondition->getJoinedTableName(),
                    $joinCondition->getForeignKey(),
                    $joinCondition->getTargetKey(),
                    $columns,
                    $where,
                    $joinCondition->isLeftJoin()
                );
                if (!empty($resultset)) {
                    $listOfResultSets[] = $resultset;
                }
                unset($resultset);
            } // end foreach
            unset($joinCondition);
            /*
             * 1.2.3.2) merge resultsets
             */
            $result = array();
            if (!empty($listOfResultSets)) {
                assert(!isset($item), 'Cannot redeclare var $item');
                foreach ($listOfResultSets as $item)
                {
                    if (!empty($item)) {
                        $result = \Yana\Util\Hashtable::merge($result, $item);
                    }
                }
                unset($item);
            } // end if
            unset($tableB, $listOfResultSets);
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

        $this->_setSortColumns($query->getOrderBy());
        $this->_setDescendingSortColumns($query->getDescending());

        $row = mb_strtoupper($query->getRow());
        if ($row === '*') {
            $message = "Cannot update entry. No primary key provided.";
            throw new \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException($message);
        }

        if (!$this->_checkForeignKeys($this->_getTable(), $set, $query->getColumn())) {
            $message = "Foreign key check failed on table '{$this->_getTable()->getName()}' for " .
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

        assert(!isset($primaryKey), 'Cannot redeclare var $primaryKey');
        $primaryKey = mb_strtoupper($this->_getTable()->getPrimaryKey());

        /* get reference to Index file */
        assert(!isset($idxfile), 'Cannot redeclare var $idxfile');
        $idxfile = $this->_getIndexFile();

        assert(!isset($columnName), 'Cannot redeclare var $columnName');
        assert(!isset($column), 'Cannot redeclare var $column');
        foreach ($this->_getTable()->getColumns() as $column)
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
                    assert(!isset($tmp), 'Cannot redeclare var $tmp');
                    $tmp = $idxfile->getVar($columnName, $set[$columnName]);
                    /*
                     * Error - unique constraint has already been breached by some
                     * previous operation.
                     * This may occur, when a unique constraint is added, that
                     * didn't exis before.
                     */
                    if (is_array($tmp) && count($tmp) > 1) {
                        assert(!isset($log), 'Cannot redeclare var $log');
                        $log = "SQL WARNING: The column {$columnName} " .
                                "has an unique constraint, but multiple " .
                                "rows with the same name have been found. " .
                                "This conflict can not be solved automatically. " .
                                "Please edit and update the affected table.";
                        \Yana\Log\LogManager::getLogger()->addLog($log, \Yana\Log\TypeEnumeration::WARNING, array('affected rows:' => $tmp));
                        unset($log);
                    }
                    /*
                     * error - constraint is breached
                     */
                    if (!empty($tmp) && strcasecmp($tmp, $row) !== 0) {
                        $message = "Cannot update entry with column {$columnName}" .
                                "= " . $set[$columnName] . ". The column has an unique constraint " .
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
            $row = mb_strtoupper((string) $set[$primaryKey]);
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
        assert(!isset($columnName), 'Cannot redeclare var $columnName');
        assert(!isset($column), 'Cannot redeclare var $column');
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
        assert(!isset($currentRow), 'Cannot redeclare var $currentRow');
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
        $set = (array) $query->getValues();
        $primaryValue = $query->getRow();
        if ($primaryValue !== '*') {
            $set[$this->_getTable()->getPrimaryKey()] = $query->getRow();
        }

        if (empty($set)) {
            $message = 'The insert statement contains no values to be inserted.';
            throw new \Yana\Db\Queries\Exceptions\InvalidSyntaxException($message);
        }
        $set = \array_change_key_case($set, CASE_UPPER);

        if (!$this->_checkForeignKeys($this->_getTable(), $set)) {
            $message = "Foreign key check failed on table '{$this->_getTable()->getName()}' for " .
                    "row " . print_r($set, true);
            throw new \Yana\Db\Queries\Exceptions\InconsistencyException($message);
        }

        assert(!isset($primaryKey), 'Cannot redeclare var $primaryKey');
        $primaryKey = mb_strtoupper($this->_getTable()->getPrimaryKey());

        if ($this->_getTable()->getColumn($primaryKey)->isAutoIncrement()) {
            $this->_increment($set);
        }

        if (isset($set[$primaryKey])) {
            $primaryValue = $set[$primaryKey];
            unset($set[$primaryKey]);
        } elseif ($primaryValue === '*') {
            $message = "Cannot insert entry. No primary key provided.";
            throw new \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException($message);
        }

        /* get reference to SML file */
        assert(!isset($smlfile), 'Cannot redeclare var $smlfile');
        $smlfile = $this->_getSmlFile();
        /* get reference to Index file */
        assert(!isset($idxfile), 'Cannot redeclare var $idxfile');
        $idxfile = $this->_getIndexFile();

        /* create column */
        assert(!isset($column), 'Cannot redeclare var $column');
        assert(!isset($columnName), 'Cannot redeclare var $columnName');
        foreach ($this->_getTable()->getColumns() as $column)
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
                    /*
                     * Error - unique constraint has already been breached by some
                     * previous operation.
                     * This may occur, when a unique constraint is added, that didn't
                     * exist before.
                     */
                    if ($idxfile->hasVar($columnName, $set[$columnName])) {
                        $message = "SQL ERROR: Cannot insert entry with column " . $columnName .
                            " = " . $set[$columnName] . ". The column has an unique constraint " .
                            "and another entry with the same value already exists.";
                        throw new \Yana\Db\Queries\Exceptions\DuplicateValueException($message);
                    }
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
                    $idxfile->create($columnName, array($primaryValue, $set[$columnName]));
                } // end if
            } // end if
        } // end foreach
        unset($column, $columnName);
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
        $this->_setSortColumns($query->getOrderBy());
        $this->_setDescendingSortColumns($query->getDescending());
        $limit = $query->getLimit();

        $smlfile = $this->_getSmlFile();
        $idxfile = $this->_getIndexFile();

        assert(!isset($rows), 'Cannot redeclare var $rows');
        $rows = $this->_get(array($this->_getTable()->getPrimaryKey()), $where, array(), 0, $limit);

        if (empty($rows)) {
            /* error */

            if ($query->getExpectedResult() === \Yana\Db\ResultEnumeration::ROW) {
                $message = "Unable to delete the row. The row you tried to delete was not found.";
                throw new \Yana\Core\Exceptions\NotFoundException($message);
            }
            return new \Yana\Db\FileDb\Result(array());
        }

        assert(!isset($primaryKey), 'Cannot redeclare var $primaryKey');
        $primaryKey = mb_strtoupper($this->_getTable()->getPrimaryKey());
        assert(!isset($row), 'Cannot redeclare var $row');
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
     * @param   string  $sqlStmt  some SQL statement
     * @param   int     $limit    the maximum number of rows in the resultset
     * @param   int     $offset   the row to start from
     * @return  \Yana\Db\FileDb\Result
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException     if the query is invalid or could not be parsed
     * @throws  \Yana\Db\Queries\Exceptions\NotSupportedException  if PEAR SQL-Parser is not installed or not found
     */
    public function sendQueryString($sqlStmt, $limit = 0, $offset = 0)
    {
        assert(is_string($sqlStmt), 'Wrong type for argument 1. String expected');

        // parse SQL
        $dbQuery = $this->_getSqlParser()->parseSQL($sqlStmt); // throws exception

        // route to query handling
        $dbQuery->setOffset($offset);
        $dbQuery->setLimit($limit);
        return $this->sendQueryObject($dbQuery);
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
        if (is_null($value)) {
            return YANA_DB_DELIMITER . YANA_DB_DELIMITER;

        } elseif (is_scalar($value)) {
            if (is_string($value)) {
                $value = stripslashes("$value");
                $value = str_replace('\\', '\\\\', $value);
                $value = str_replace(YANA_DB_DELIMITER, '\\' . YANA_DB_DELIMITER, $value);
                $value = str_replace("\n", '\n', $value);
                $value = str_replace("\r", '\r', $value);
                $value = str_replace("\f", '\f', $value);
                $value = str_replace(chr(0), '', $value);
            };
            return YANA_DB_DELIMITER . "$value" . YANA_DB_DELIMITER;

        } else {
            return $this->quote(\json_encode($value));
        }
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
        assert(is_string($value), 'Wrong type for argument 1. String expected');
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
        assert(is_string($tableName), 'Wrong type for argument 1. String expected');
        assert(!empty($tableName), 'Wrong type for argument 1. String must not be empty');
        $tableName = mb_strtolower(trim("$tableName"));

        // if is cached, set current table from cache
        if (isset($this->_src[$this->_getDatabaseName()][$tableName])) {
            $this->_setTableName($tableName);
            $table = $this->_getSchema()->getTable($tableName);
            assert($table instanceof \Yana\Db\Ddl\Table, 'Table not found in Schema');
            $this->_setTable($table);
            return $this;
        }

        /*
         * get associated data-source for selected table
         */
        assert(!isset($table), 'Cannot redeclare $table');
        $table = $this->_getSchema()->getTable($tableName);

        if (!$table instanceof \Yana\Db\Ddl\Table) {
            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException("No such table '$tableName'.");
        }
        $database = $this->_getSourceDatabaseNameForTable($table);

        // set current table
        $this->_setTableName($tableName);
        $this->_setTable($table);

        /*
         * open source file(s)
         */
        $this->_setSmlFile($database);
        $this->_setIndexFile($database);

        assert($table instanceof \Yana\Db\Ddl\Table, 'Table not found in Schema');
        assert(is_string($tableName), 'Unexpected result: $tableName must be a string');
        assert($tableName > "", 'Unexpected result: $tableName must not be empty');
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
        assert(!isset($parent), 'Cannot redeclare $parent');
        $parent = $table->getParent();
        assert(!isset($database), 'Cannot redeclare $database');
        // get data source from parent (if it exists)
        $databaseName = ($parent instanceof \Yana\Db\Ddl\Database) ? $parent->getName() : $this->_getSchema()->getName();
        unset($parent);

        if (is_null($databaseName)) {
            $databaseName = $this->_getDatabaseName();
            assert(!empty($databaseName), 'Unexpected result: $database must not be empty');
        }
        assert(is_string($databaseName), 'Unexpected result: $databaseName must be a string');
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
        if (is_null($this->_getAutoIncrement())) {
            $name = __CLASS__ . '\\' . $this->_getDatabaseName() . '\\' . $this->_getTableName();
            try {
                $sequence = new \Yana\Db\FileDb\Sequence($name);
            } catch (\Yana\Db\Queries\Exceptions\NotFoundException $e) {
                \Yana\Db\FileDb\Sequence::create($name);
                $sequence = new \Yana\Db\FileDb\Sequence($name);
                unset($e);
            }
            unset($name);
            $this->_setAutoIncrement($sequence);
        }

        /*
         * 2) simulate auto-increment
         */
        $primaryKey = \mb_strtoupper($this->_getTable()->getPrimaryKey());
        if (empty($set[$primaryKey])) {
            $index = $this->_getAutoIncrement()->getNextValue();
            $set[$primaryKey] = $index;
        }
    }

    /**
     * Simulate select count(*) from ... where ...
     *
     * @param   array  $where   where clausel
     * @return  int
     */
    private function _length(array $where): int
    {
        /* if table does not exist, then there is nothing to get */
        if (!isset($this->_src[$this->_getDatabaseName()][$this->_getTableName()])) {
            return 0;
        }

        if (empty($where)) {
            $smlfile =& $this->_src[$this->_getDatabaseName()][$this->_getTableName()];
            /*
             * note: the first index is the primary key - NOT the table name
             */
            return $smlfile->length($this->_getTable()->getPrimaryKey());
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
        assert(is_int($offset), 'Wrong type for argument 3. Integer expected');
        assert(is_int($limit), 'Wrong type for argument 4. Integer expected');
        assert($offset >= 0, 'Invalid argument 3. Must be a positive integer');
        assert($limit >= 0, 'Invalid argument 4. Must be a positive integer');

        $limit = (int) $limit;
        $offset = (int) $offset;

        // if table does not exist, then there is nothing to get
        if (!isset($this->_src[$this->_getDatabaseName()][$this->_getTableName()])) {
            return array();
        }

        // initialize vars
        $result     = array();
        $smlfile    = $this->_getSmlFile();
        $primaryKey = mb_strtoupper($this->_getTable()->getPrimaryKey($this->_getTableName()));
        $data       = $smlfile->getVar($primaryKey);

        // if the target table is empty ...
        if (!is_array($data)) {
            return array(); // ... then return an empty result-set
        }

        // 1) add primary key column
        assert(!isset($i), 'Cannot redeclare var $i');
        // implements a table-scan
        foreach (array_keys($data) as $i)
        {
            $data[$i][$primaryKey] = $i;
        }
        unset($i);

        // 2) pre-sort data
        $this->_doSort($data);

        // 3) apply where clause
        switch ($this->_getQuery()->getExpectedResult())
        {
            case \Yana\Db\ResultEnumeration::TABLE:
            case \Yana\Db\ResultEnumeration::COLUMN:
                $doCollapse = count($columns) > 1;
            break;
            default:
                $doCollapse = false;
            break;
        }
        assert(!isset($current), 'Cannot redeclare var $current');
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
        assert(!isset($alias), 'Cannot redeclare var $alias');
        assert(!isset($column), 'Cannot redeclare var $column');
        foreach ($columns as $alias => $column)
        {
            if (is_array($column)) {
                $column = $column[1];
            }
            $column = mb_strtoupper((string) $column);
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
        if (count($this->_getDescendingSortColumns()) > 0 && count($this->_getSortColumns()) === 0) {
            $result = array_reverse($result);
        /*
         * If a column has been provided to sort entries by, then the resultset will get sorted
         * by it.
         */
        } elseif (count($this->_getSortColumns()) > 0) {
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
            $columns = $this->_getSortColumns();
        }
        if (is_null($desc)) {
            $desc = $this->_getDescendingSortColumns();
        }
        if (count($columns) === 0 || !is_array($a) || !is_array($b)) {
            return 0;
        }

        $sortArray = array_shift($columns);
        $sort = mb_strtoupper((string) $sortArray[1]);
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

        assert(is_int($result) && $result >= -1 && $result <= 1, 'unexpected result');

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
     * Commit transaction.
     *
     * @param   bool  $commit on / off
     * @return  \Yana\Db\FileDb\Result
     * @throws  \Yana\Db\DatabaseException  when changes have not been saved
     * @codeCoverageIgnore
     */
    protected function _write($commit = false)
    {
        /* pessimistic scanning */
        assert(is_bool($commit), 'Wrong argument type for argument 1. Boolean expected.');

        $this->_cache = array();

        /* wait for commit */
        if (!$commit && !$this->_isAutoCommit()) {
            return new \Yana\Db\FileDb\Result(array());
        }

        /* commit */
        foreach ($this->_src[$this->_getDatabaseName()] as $table)
        {
            /* @var $table \SML */
            if (!$table->exists()) {

                $table->create(); // auto-create missing file
            }
            try {
                $table->write();

            } catch (\Exception $e) {
                throw new \Yana\Db\DatabaseException('unable to save changes', \Yana\Log\TypeEnumeration::WARNING, $e);

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
        assert(is_string($tableA), 'Wrong argument type for argument 1. String expected.');
        assert(is_string($tableB), 'Wrong argument type for argument 2. String expected.');
        assert(is_string($columnA), 'Wrong argument type for argument 3. String expected.');
        assert(is_string($columnB), 'Wrong argument type for argument 4. String expected.');

        /* prepare input */
        $tableA  = mb_strtoupper("$tableA");
        $columnA = mb_strtoupper("$columnA");
        $tableB  = mb_strtoupper("$tableB");
        $columnB = mb_strtoupper("$columnB");

        /* the following implements the productive process */

        /* get a cursor on A */
        try {
            $this->_select($tableA); // may throw NotFoundException
            $tableADef = $this->_getSchema()->getTable($tableA);
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
            $tableBDef = $this->_getSchema()->getTable($tableB);
            $columnBDef = $tableBDef->getColumn($columnB);
            $bIsPk = $columnBDef->isPrimaryKey();
            $pkB = mb_strtoupper($tableBDef->getPrimaryKey());
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            return array();
        }
        $SMLB = $this->_getSmlFile();
        $cursorB = $SMLB->getVarByReference($pkB);

        /* notify me if results are not valid */
        assert(is_bool($aIsPk), 'unexpected result $aIsPk must be a boolean');
        assert(is_bool($bIsPk), 'unexpected result $bIsPk must be a boolean');
        assert(is_string($pkA), 'unexpected result $pkA must be a string');
        assert(is_string($pkB), 'unexpected result $pkB must be a string');

        /* clean up */
        $this->_select($tableA);

        /* this information is needed to optimize performance */
        $indexA =  null;
        /* @var $indexB \FileDbIndex */
        $indexB =  null;
        if ($columnADef->hasIndex()) {
            $indexA =& $this->_idx[$this->_getDatabaseName()][$this->_getTableName()];
        }
        if ($columnBDef->hasIndex()) {
            $indexB =& $this->_idx[$this->_getDatabaseName()][$this->_getTableName()];
        }

        if (empty($columns)) {
            $columns = null;
        }

        /* $columnA must be foreign key */
        assert((bool) $columnADef->isForeignKey() || (bool) $columnBDef->isForeignKey(),
            "Joining table '{$tableA}' with '{$tableB}' might fail. Column '{$columnA}' is not a foreign key in table '{$tableA}'.");
        /* $columnB must be a key */
        assert((bool) $bIsPk || (bool) $columnBDef->IsUnique() || (bool) $aIsPk || $columnADef->IsUnique(),
            "Joining table '{$tableA}' with '{$tableB}' might return ambigious results. ".
            "Column '{$columnB}' is not unique in table '{$tableB}'.");
        /* $columnA and $columnB must be of same type */
        assert(
            $columnBDef->getType() === \Yana\Db\Ddl\ColumnTypeEnumeration::REFERENCE ||
            $columnADef->getType() === \Yana\Db\Ddl\ColumnTypeEnumeration::REFERENCE ||
            $columnADef->getType() == $columnBDef->getType(),
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
        assert(!isset($row), 'Cannot redeclare var $row');
        assert(!isset($id), 'Cannot redeclare var $id');
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
                $keyA = mb_strtoupper((string) $id);
            } elseif (isset($row[$columnA])) {
                $keyA = mb_strtoupper((string) $row[$columnA]);
            } else {
                $keyA = null;
            }

            /* case 1: $columnB is a primary key */
            if (is_null($keyA)) {
                $joinedValueExists = false;
            } elseif ($bIsPk === true) {
                if (isset($cursorB[$keyA])) {
                    $joinedValueExists = true;
                    assert(!isset($_value), 'Cannot redeclare var $_value');
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

            assert(!isset($newResult), '!isset($newResult)');
            assert(!isset($currentValue), '!isset($currentValue)');
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

        assert(is_array($resultSet), 'is_array($resultSet)');
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
        assert(!isset($i), 'Cannot redeclare var $i');
        assert(!isset($current), 'Cannot redeclare var $current');
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
        assert(count($where) === 3, 'Where clause must have exactly 3 items: left + right operands + operator');
        $leftOperand = array_shift($where);
        $operator = array_shift($where);
        $rightOperand = array_shift($where);

        /**
         * 1) is sub-clause
         */
        switch ($operator)
        {
            case \Yana\Db\Queries\OperatorEnumeration::OR:
                return $this->_doWhere($current, $leftOperand) || $this->_doWhere($current, $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::AND:
                return $this->_doWhere($current, $leftOperand) && $this->_doWhere($current, $rightOperand);

        }

        /**
         * 2) is singular clause
         */
        $table = null;
        if (is_array($leftOperand)) { // content is: table.column
            $tableName = array_shift($leftOperand); // get table name
            $leftOperand = array_shift($leftOperand); // get just the column
            $table = $this->_getSchema()->getTable($tableName);
            assert($table instanceof \Yana\Db\Ddl\Table, 'Table not found');
            unset($tableName);
        } else {
            $table = $this->_getTable();
        }
        if (isset($current[mb_strtoupper((string) $leftOperand)])) {
            $value = $current[mb_strtoupper((string) $leftOperand)];
        } elseif ($table !== $ignoreTable) {
            $value = null;
        } else {
            return true; // the table is not checked - used for ON-clause during outer joins
        }
        /* handle non-scalar values */
        if (!is_null($value) && !is_scalar($value)) {
            $value = \Yana\Files\SML::encode($value);
        }
        /* switch by operator */
        switch ($operator)
        {
            case '<>':
                // fall through
                $operator = \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL;
            case \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL:
                // fall through
            case '==':
            case \Yana\Db\Queries\OperatorEnumeration::EQUAL:
                $column = $table->getColumn($leftOperand);
                assert($column instanceof \Yana\Db\Ddl\Column, 'Column not found');
                if (is_null($rightOperand)) {
                    return is_null($value) xor $operator === '!=';
                }
                if ($column->isPrimaryKey() || $column->isForeignKey()) {
                    return strcasecmp($value, $rightOperand) === 0 xor $operator === \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL;
                }
                return (strcmp($value, $rightOperand) === 0) xor $operator === \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL;

            case \Yana\Db\Queries\OperatorEnumeration::NOT_LIKE:
                $operator = \Yana\Db\Queries\OperatorEnumeration::NOT_REGEX;
                // fall through
            case \Yana\Db\Queries\OperatorEnumeration::LIKE:
                $rightOperand = preg_quote($rightOperand, '/');
                $rightOperand = str_replace('%', '.*', $rightOperand);
                $rightOperand = str_replace('_', '.?', $rightOperand);
                // fall through
            case \Yana\Db\Queries\OperatorEnumeration::REGEX:
                return preg_match('/^' . $rightOperand . '$/is', $value) === 1 xor $operator === \Yana\Db\Queries\OperatorEnumeration::NOT_REGEX;

            case \Yana\Db\Queries\OperatorEnumeration::LESS:
                return ($value < $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::GREATER:
                return ($value > $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::LESS_OR_EQUAL:
                return ($value <= $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::GREATER_OR_EQUAL:
                return ($value >= $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::IN:
                if ($rightOperand instanceof \Yana\Db\Queries\Select) {
                    $rightOperand = $rightOperand->getResults();
                }
                return (bool) in_array($value, $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::NOT_IN:
                if ($rightOperand instanceof \Yana\Db\Queries\Select) {
                    $rightOperand = $rightOperand->getResults();
                }
                return !in_array($value, $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::EXISTS:
                return ($rightOperand instanceof \Yana\Db\Queries\SelectExist) && $rightOperand->doesExist();

            case \Yana\Db\Queries\OperatorEnumeration::NOT_EXISTS:
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
        if (!isset($this->_idx[$this->_getDatabaseName()][$this->_getTableName()])) {
            $message = "Index-file not found for databae " . $this->_getDatabaseName() .
                " table " . $this->_getTableName() . ". Is the directory writable?";
            throw new \Yana\Db\DatabaseException($message, \Yana\Log\TypeEnumeration::ERROR);
        }
        return $this->_idx[$this->_getDatabaseName()][$this->_getTableName()];
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
        $idxfile = $this->_createIndex($smlfile, $filename);
        $this->_idx[$this->_getDatabaseName()][$this->_getTableName()] = $idxfile;
    }

    /**
     * Overwrite this with null-object in unit tests to avoid side-effects.
     *
     * @param   \Yana\Files\SML  $smlfile   data (SML object)
     * @param   string           $filename  filename
     * @return  \Yana\Db\FileDb\Index
     * @ignore
     * @codeCoverageIgnore
     */
    protected function _createIndex(\Yana\Files\SML $smlfile, string $filename): \Yana\Db\FileDb\Index
    {
        return new \Yana\Db\FileDb\Index($this->_getTable(), $smlfile, $filename);
    }

    /**
     * initialize and return current SML file by reference
     *
     * @return  \Yana\Files\SML
     */
    private function _getSmlFile()
    {
        return $this->_src[$this->_getDatabaseName()][$this->_getTableName()];
    }

    /**
     * create SML file
     *
     * @param   string  $database  database name
     * @throws  \Yana\Core\Exceptions\NotReadableException  when the SML source file could not be read
     */
    private function _setSmlFile($database)
    {
        if (!isset($this->_src[$this->_getDatabaseName()][$this->_getTableName()])) {
            $filename = $this->_getFilename($database, 'sml');
            $isCreated = false;
            $smlfile = $this->_createSmlFile($filename, $isCreated);
            if ($isCreated && $database != $this->_getDatabaseName()) {
                $filename = $this->_getFilename($this->_getDatabaseName(), 'sml');
                $smlfile = $this->_createSmlFile($filename);
            }
            $smlfile->failSafeRead();

            $this->_src[$this->_getDatabaseName()][$this->_getTableName()] = $smlfile;
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
        assert(is_string($filename), 'Invalid argument $filename: string expected');

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
        assert(is_string($database), 'Invalid argument $database: string expected');
        assert(is_string($extension), 'Invalid argument $extension: string expected');
        assert(is_string($tableName), 'Invalid argument $tableName: string expected');

        if (empty($tableName)) {
            $tableName = $this->_getTableName();
        }

        return realpath(self::$_baseDir) . '/' . $database . '/' . $tableName . '.' . $extension;
    }

}

?>