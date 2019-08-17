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

namespace Yana\Db\Queries;

/**
 * Database query builder
 *
 * This class is a query builder that can be used to build SQL statements.
 *
 * Note: this class does NOT untaint input data for you.
 * It also does NOT automatically resolve foreign keys.
 * This is mentioned here for security reasons.
 *
 * Note that there are some special features that you may find usefull:
 *
 * First, this class is able to detect and resolve table inheritance.
 * Note that this feature is turned on by default. You may turn it of
 * using the function useInheritance(), if you want. Usually this is not
 * necessary.
 *
 * Second, this class allows you to navigate from one table to another along
 * definded foreign keys and also into columns of type array. This is done by
 * using the function setKey(). These keys form something similar to an
 * address within the database that consists of table.row.column. For example
 * foo.2.bar means select bar from foo where id = 2.
 * If the column is a foreign key, you may add another column inside the
 * referenced table, which itself may also be another foreign key.
 * If the column is an array, you may add an index inside that array, which you
 * want to return. You will find more details on that inside the developer's
 * cookbook in the manual.
 *
 * @package     yana
 * @subpackage  db
 * @since       2.9 RC1
 */
abstract class AbstractQuery extends \Yana\Db\Queries\AbstractConnectionWrapper
{

    /**#@+
     * @ignore
     */

    /**
     * @var string
     */
    private $_id = null;

    /**
     * @var int
     */
    protected $type = \Yana\Db\Queries\TypeEnumeration::UNKNOWN;

    /**
     * @var int
     */
    protected $expectedResult = \Yana\Db\Queries\TypeEnumeration::UNKNOWN;

    /**
     * @var string
     */
    protected $tableName = null;

    /**
     * @var string
     */
    protected $row = '*';

    /**
     * @var array
     */
    protected $column = array();

    /**
     * @var array
     */
    protected $profile = array();

    /**
     * @var array
     */
    protected $rowId = array();

    /**
     * @var array
     */
    protected $where = array();

    /**
     * @var array
     */
    protected $orderBy = array();

    /**
     * @var array
     */
    protected $desc = array();

    /**
     * @var int
     */
    protected $limit = 0;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var \Yana\Db\Queries\IsJoinCondition[]
     */
    protected $joins = array();

    /**
     * @var string
     */
    protected $arrayAddress = '';

    /**
     * @var bool
     */
    protected $useInheritance = true;

    /**
     * @var bool
     */
    protected $isSubQuery = false;

    /**
     * @var array
     */
    protected $parentTables = array();

    /**
     * @var array
     */
    protected $tableByColumn = array();

    /**
     * @var \Yana\Db\Ddl\Table
     */
    protected $table = null;

    /**
     * @var array
     */
    private $_oldValues = null;

    /** #@- */

    /**
     * magic get
     *
     * Returns a database object definition from the schema.
     * If there is none, the function will return NULL.
     * The type of the returned object depends on the selected database object.
     *
     * Note that you can't get an unnamed database object via this function.
     *
     * @param   string  $name   name of a database object
     * @return  \Yana\Db\Ddl\DDL
     */
    public function __get($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        return $this->getDatabase()->getSchema()->{$name};
    }

    /**
     * magic function call
     *
     * Calls a function on the selected database schema and returns the result.
     *
     * @param   string  $name       name
     * @param   array   $arguments  arguments
     * @return  mixed
     */
    public function __call($name, array $arguments)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        return call_user_func_array(array($this->getDatabase()->getSchema(), $name), $arguments);
    }

    /**
     * magic is set
     *
     * Returns true if a named object with the given name exists in the database schema.
     *
     * @param   string  $name  name of a database object
     * @return  bool
     */
    public function __isset($name)
    {
        return ($this->__get($name) !== null);
    }

    /**
     * Reset query.
     *
     * Resets all properties of the query object, except
     * for the database connection and the properties
     * "table", "type", "useInheritance".
     *
     * This function allows you to "recycle" a query object
     * and reuse it without creating another one. This can
     * help to improve the performance of your application.
     *
     * @return  \Yana\Db\Queries\AbstractQuery
     */
    public function resetQuery()
    {
        $this->resetId();
        $this->row           = '*';
        $this->column        = array();
        $this->profile       = array();
        $this->rowId         = array();
        $this->where         = array();
        $this->orderBy       = null;
        $this->desc          = false;
        $this->limit         = 0;
        $this->joins         = array();
        $this->arrayAddress  = '';
        $this->parentTables  = array();
        $this->tableByColumn = array();
        $this->_oldValues    = null;
        return $this;
    }

    /**
     * Select the kind of statement.
     *
     * The argument type can be one of the following:
     * <ul>
     *  <li> \Yana\Db\Queries\TypeEnumeration::UNKNOWN = to reset type </li>
     *  <li> \Yana\Db\Queries\TypeEnumeration::SELECT = Select column from table ... </li>
     *  <li> \Yana\Db\Queries\TypeEnumeration::UPDATE = Update table ... </li>
     *  <li> \Yana\Db\Queries\TypeEnumeration::INSERT = Insert into table ... </li>
     *  <li> \Yana\Db\Queries\TypeEnumeration::DELETE = Delete from table where ... </li>
     *  <li> \Yana\Db\Queries\TypeEnumeration::EXISTS = Select 1 from ... where ... </li>
     *  <li> \Yana\Db\Queries\TypeEnumeration::COUNT  = Select count(*) from ...  </li>
     * </ul>
     *
     * Note: For security reasons all delete queries will automatically
     * set the limit to 1.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   int  $type  set the kind of statement
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException    when argument is not a valid constant
     * @throws  \Yana\Db\Queries\Exceptions\TableNotSetException  if table has not been initialized
     * @return  $this
     * @ignore
     */
    protected function setType($type)
    {
        $this->resetId();
        $table = $this->currentTable();

        switch ($type)
        {
            case \Yana\Db\Queries\TypeEnumeration::INSERT:
                if ($this->row === '*' && $this->expectedResult === \Yana\Db\ResultEnumeration::TABLE) {
                    if ($table->getColumn($table->getPrimaryKey())->isAutoFill()) {
                        $this->expectedResult = \Yana\Db\ResultEnumeration::ROW;
                    }
                }
                $this->type = $type;
            break;

            case \Yana\Db\Queries\TypeEnumeration::COUNT:
                if (is_array($this->column) && count($this->column) > 1) {
                    // @codeCoverageIgnoreStart
                    $message = "Cannot use query type 'length' with multiple columns.";
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
                    // @codeCoverageIgnoreEnd
                }
            case \Yana\Db\Queries\TypeEnumeration::UNKNOWN:
            case \Yana\Db\Queries\TypeEnumeration::SELECT:
            case \Yana\Db\Queries\TypeEnumeration::UPDATE:
            case \Yana\Db\Queries\TypeEnumeration::EXISTS:
                $this->type = $type;
            break;

            case \Yana\Db\Queries\TypeEnumeration::DELETE:
                $this->type = $type;
                $this->limit = 1;
            break;

            default:
                $message = "Argument 1 is invalid. The selected statement type is unknown.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        return $this;
    }

    /**
     * get the currently selected type of statement
     *
     * Returns currently selected constant.
     *
     * @return  int
     */
    public function getType()
    {
        assert('is_int($this->type); // Expecting member "type" to be an integer');
        return $this->type;
    }

    /**
     * find out which kind of result is expected
     *
     * Returns currently selected constant.
     *
     * <ul>
     *  <li> DbResultEnumeration::UNKNOWN - no input </li>
     *  <li> DbResultEnumeration::TABLE   - table only </li>
     *  <li> DbResultEnumeration::ROW     - table + row </li>
     *  <li> DbResultEnumeration::COLUMN  - table + column </li>
     *  <li> DbResultEnumeration::CELL    - table + row + column </li>
     * </ul>
     *
     * Note: DbResultEnumeration::CELL means to refer to exactly 1 column.
     * When retrieving multiple columns from a row,
     * use DbResultEnumeration::ROW instead.
     *
     * @return  int
     * @since   2.9.3
     * @ignore
     */
    public function getExpectedResult()
    {
        assert('is_int($this->type); // Expecting member "expectedResult" to be an integer');
        return $this->expectedResult;
    }

    /**
     * Activate / deactivate automatic handling of inheritance.
     *
     * The query builder is able to detect if one table inherits
     * from another and if so, it will auto-join both tables.
     * In this case, selecting a row from the offspring table will
     * also return all entries of the corresponding row in the
     * parent table.
     *
     * However: while this usually comes in handy, there are some
     * rare situations where you won't want this to be done.
     * E.g. when copying rows from one table to another.
     *
     * This function allows you to enable or disable this feature.
     * It is enabled by default.
     *
     * Note: you have to set this before you set the table property.
     * Otherwise it will have no effect.
     *
     * @param   bool  $state  true = on, false = off
     * @return  $this
     */
    public function useInheritance($state)
    {
        assert('is_bool($state); // Invalid argument $state: bool expected');
        $this->useInheritance = (bool) $state;
        return $this;
    }

    /**
     * set parent table
     *
     * The query builder will set this automatically, to indicate,
     * that one table inherits from another.
     *
     * @param   \Yana\Db\Ddl\Table  $table          table definition
     * @param   \Yana\Db\Ddl\Table  $parentTable    parent table
     * @since   2.9.6
     * @return  $this
     */
    private function _setParentTable(\Yana\Db\Ddl\Table $table, \Yana\Db\Ddl\Table $parentTable)
    {
        /**
         * add columns
         */
        $tableName = $parentTable->getName();
        foreach ($parentTable->getColumnNames() as $columnName)
        {
            $columnName = mb_strtoupper($columnName);
            if (!isset($this->tableByColumn[$columnName]) && !$table->isColumn($columnName)) {
                $this->tableByColumn[$columnName] = $tableName;
            }
        }
        unset($columnName);
        /**
         * add table
         */
        $tableName = mb_strtoupper($table->getName());
        $this->parentTables[$tableName] = $parentTable;
        return $this;
    }

    /**
     * get parent table by column name
     *
     * This function provides information on entity inheritance
     * within the database's data structure.
     *
     * If the table extends another table, and the column
     * is inherited one of the parent tables, then this function
     * will return the name of the parent table, where the
     * column was defined or re-defined.
     *
     * It will return bool(false) if there is no such parent.
     *
     * @param   string  $columnName  name of a column
     * @since   2.9.6
     * @return  string
     * @ignore
     */
    protected function getParentByColumn($columnName)
    {
        assert('is_string($columnName); // Wrong type for argument 1. String expected');
        $columnName = mb_strtoupper($columnName);
        if (isset($this->tableByColumn[$columnName])) {
            return $this->tableByColumn[$columnName];
        } else {
            return false;
        }
    }

    /**
     * get column name by alias
     *
     * This function looks up the column name for a given alias and returns it as
     * an upper-cases string.
     *
     * @param   string  $alias  column alias
     * @return  string
     * @ignore
     */
    protected function getColumnByAlias($alias)
    {
        $alias = mb_strtoupper($alias);
        if (!isset($this->column[$alias])) {
            return $alias;
        }
        $columnName = $this->column[$alias];
        if (is_array($columnName)) {
            $tableName = array_shift($columnName);
            $columnName = array_shift($columnName);
            $this->tableByColumn[$columnName] = $tableName;
            unset($tableName);
        }
        return mb_strtoupper($columnName);
    }

    /**
     * get table by column name
     *
     * If multiple tables are joined (either automatically or manually)
     * you may use this function to get the source table for a certain row.
     *
     * @param   string  $columnName  name of a column
     * @return  \Yana\Db\Ddl\Table
     * @throws  \Yana\Db\Queries\Exceptions\TableNotSetException     if table has not been initialized
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if no column with the given name has been found
     */
    public function getTableByColumn($columnName)
    {
        assert('is_string($columnName); // Wrong type for argument 1. String expected');
        $columnName = $this->getColumnByAlias($columnName);
        $dbSchema = $this->getDatabase()->getSchema();

        $table = null;
        // lazy loading: resolve source tables for requested column
        if (isset($this->tableByColumn[$columnName])) {
            $table = $dbSchema->getTable($this->tableByColumn[$columnName]); // When we are auto-resolving inheritance between tables

        } elseif ($this->currentTable()->isColumn($columnName)) {
            $table = $this->currentTable(); // may throw exception

        } elseif (!empty($this->joins)) {
            assert('!isset($joinedTable); // Cannot redeclare var $joinedTable');
            assert('!isset($joinCondition); // Cannot redeclare var $joinCondition');
            foreach ($this->joins as $joinCondition)
            {
                $joinedTable = $dbSchema->getTable($joinCondition->getJoinedTableName());
                if ($joinedTable->isColumn($columnName)) {
                    $table = $joinedTable;
                    break;
                }
                unset($joinedTable);
            }
            unset($joinCondition);
        }

        if (! $table instanceof \Yana\Db\Ddl\Table) { // happens when getTable() returned bool(false)
            $message = "Column '" . $columnName . "' is undefined.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException($message, $level);
        } else {
            return $table;
        }
    }

    /**
     * Get the parent of a table.
     *
     * This function provides information on entity inheritance
     * within the database's data structure.
     *
     * If $table extends another table, then this
     * will return the name of the parent table as a string.
     *
     * It will return bool(false) if there is no such parent.
     *
     * If the argument $table is empty, or not provided, the
     * currently selected table (see {link \Yana\Db\Queries\AbstractQuery::setTable()})
     * is used instead.
     *
     * @param   string  $table  name of a table
     * @since   2.9.6
     * @return  string
     */
    public function getParent($table = "")
    {
        assert('is_string($table); // Wrong type for argument 1. String expected');

        if ($table === "") {
            $table = $this->tableName;
        }
        $table = mb_strtoupper($table);

        $parent = false;
        if (isset($this->parentTables[$table])) {
            $parent = $this->parentTables[$table];
        }
        return $parent;
    }

    /**
     * Recursively detect the parent of a table.
     *
     * Two tables are considered to implement inheritance if there is a 1:1 relation between them,
     * where the primary key of the child table is a foreign key that references the primary key of its parent table.
     *
     * @param   \Yana\Db\Ddl\Table  $table    table
     * @since   2.9.6
     * @return  \Yana\Db\Queries\AbstractQuery
     * @ignore
     */
    protected function detectInheritance(\Yana\Db\Ddl\Table $table)
    {
        $tableName = $table->getName();
        $parents = array($tableName); /* to detect circular refrences */
        $dbSchema = $this->getDatabase()->getSchema();
        /**
         * recursively detect parents
         *
         * Inheritance occurs, when the primary key also is a foreign key and
         * the parent table is does not have itself as one of its descendants.
         */
        $primaryKey = mb_strtoupper($table->getPrimaryKey());
        $primaryKeyColumn = $table->getColumn($primaryKey);
        assert('$primaryKeyColumn instanceof \Yana\Db\Ddl\Column; // Misspelled primary key column: ' . $primaryKey);
        while ($primaryKeyColumn->isForeignKey())
        {
            $fTableKey = mb_strtoupper($table->getTableByForeignKey($primaryKey));
            // detect circular reference (when table is already in parent list)
            if (in_array($fTableKey, $parents)) {
                break;
            }

            $foreignTable = $dbSchema->getTable($fTableKey);
            assert('$foreignTable instanceof \Yana\Db\Ddl\Table; // Misspelled foreign key in table: ' . $tableName);
            $foreignKey = mb_strtoupper($foreignTable->getPrimaryKey());
            $this->setJoin($fTableKey, $foreignKey, $tableName, $primaryKey);
            $this->_setParentTable($table, $foreignTable);
            $table = $foreignTable;
            $primaryKey = $foreignKey;
            $primaryKeyColumn = $table->getColumn($primaryKey);
        }
        return $this;
    }

    /**
     * Join the resultsets for two tables.
     *
     * If the target key is not provided, the function will automatically search for
     * a suitable foreign key in the source table, that refers to the foreign table.
     * If target  is not provided, the function will automatically look up
     * the primary key of $tableName and use it instead.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string $joinedTableName  name of the foreign table to join the source table with
     * @param   string $targetKey        name of the key in foreign table that is referenced
     *                                   (may be omitted if it is the primary key)
     * @param   string $sourceTableName  name of the source table
     * @param   string $foreignKey       name of the foreign key in source table
     *                                   (when omitted the API will look up the key in the schema file)
     * @param   bool   $isLeftJoin       use left join instead of inner join
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if a provided table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ConstraintException      if no suitable column is found to create a foreign key
     * @return  \Yana\Db\Queries\AbstractQuery
     * @ignore
     */
    protected function setJoin($joinedTableName, $targetKey = null, $sourceTableName = null, $foreignKey = null, $isLeftJoin = false)
    {
        assert('is_string($joinedTableName); // Wrong type for argument 1. String expected');
        assert('is_null($targetKey) || is_string($targetKey); // Wrong type for argument 2. String expected');
        assert('is_null($sourceTableName) || is_string($sourceTableName); // Wrong type for argument 3. String expected');
        assert('is_null($foreignKey) || is_string($foreignKey); // Wrong type for argument 4. String expected');
        assert('is_bool($isLeftJoin); // Wrong type for argument 5. Boolean expected');

        $joinedTableName = mb_strtolower((string) $joinedTableName);
        $joinedTable = $this->getDatabase()->getSchema()->getTable($joinedTableName);
        $sourceTableName = $sourceTableName > "" ? mb_strtolower((string) $sourceTableName) : (string) $this->getTable();
        $sourceTable = $this->getDatabase()->getSchema()->getTable($sourceTableName); // may return NULL

        if (! $joinedTable instanceof \Yana\Db\Ddl\Table) {
            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException("Table not found '" . $joinedTableName . "'.");
        }

        if (! $sourceTable instanceof \Yana\Db\Ddl\Table) {
            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException("Table not found '" . $sourceTableName . "'.");
        }
        // error - no such column in current table
        assert('is_null($targetKey) || $joinedTable->isColumn($targetKey); // ' .
            "Cannot join tables '" . $sourceTableName . "' and '" . $joinedTableName . "'. " .
            "Field '" . $targetKey . "' does not exist in table '" . $joinedTableName . "'.");
        // error - no such column in referenced table
        assert('is_null($foreignKey) || $sourceTable->isColumn($foreignKey); // ' .
            "Cannot join tables '" . $sourceTableName . "' and '" . $joinedTableName . "'. " .
            "Field '" . $foreignKey . "' does not exist in table '" . $sourceTableName . "'.");

        // Try to auto-detect valid foreign key if possible
        if (is_null($targetKey) || is_null($foreignKey)) {

            if (!self::_findForeignKey($sourceTable, $joinedTable, $foreignKey, $targetKey)) {
                if (!self::_findForeignKey($joinedTable, $sourceTable, $foreignKey, $targetKey)) {
                    $message = "Cannot join tables '" . $sourceTableName . "' and '" . $joinedTableName . "'. " .
                        "No foreign key constraint has been found.";
                    throw new \Yana\Db\Queries\Exceptions\ConstraintException($message, \Yana\Log\TypeEnumeration::WARNING);
                } else {
                    $message = "Wrong join order. Please swap source and target table.";
                    throw new \Yana\Db\Queries\Exceptions\ConstraintException($message, \Yana\Log\TypeEnumeration::WARNING);
                }
            }
        }
        $targetKey = mb_strtolower((string) $targetKey); // lower-case input
        $foreignKey = mb_strtolower((string) $foreignKey); // lower-case input

        // expecting both keys to be resolved and valid at this point
        assert('$sourceTable->isColumn($foreignKey);');
        assert('$joinedTable->isColumn($targetKey);');

        // create new join condition
        assert('!isset($joinType); // Cannot redeclare variable $joinType');
        $joinType = ($isLeftJoin) ? \Yana\Db\Queries\JoinTypeEnumeration::LEFT_JOIN : \Yana\Db\Queries\JoinTypeEnumeration::INNER_JOIN;
        $this->addJoinCondition(new \Yana\Db\Queries\JoinCondition($joinedTableName, $targetKey, $sourceTableName, $foreignKey, $joinType));
        return $this;
    }

    /**
     * Add a join condition to the list of joined tables.
     *
     * @param   \Yana\Db\Queries\JoinCondition  $condition  to add
     * @return  $this
     */
    protected function addJoinCondition(\Yana\Db\Queries\JoinCondition $condition)
    {
        $this->resetId();
        $this->joins[$condition->getJoinedTableName()] = $condition;
        return $this;
    }

    /**
     * auto-detect valid foreign key to join two tables
     *
     * If $key1 is NULL it tries to detect a foreign key in $sourceTable that refers to $targetTable.
     * If $key2 is NULL it takes the primary key of $targetTable.
     *
     * Note that the two columns are passed by reference.
     *
     * The function returns bool(true) on success and bool(false) on error.
     *
     * Note: Detects foreign keys $source -> $target but NOT $source <- $target.
     * To detect both ($source <-> $target), call this function twice and swap the arguments.
     *
     * @param   \Yana\Db\Ddl\Table  $sourceTable  source table definition
     * @param   \Yana\Db\Ddl\Table  $targetTable  target table definition
     * @param   string              &$key1        source column
     * @param   string              &$key2        target column
     * @return  bool
     */
    private static function _findForeignKey(\Yana\Db\Ddl\Table $sourceTable, \Yana\Db\Ddl\Table $targetTable,
        &$key1 = null, &$key2 = null)
    {
        $tableName = $targetTable->getName();
        if (is_null($key1)) {

            /* if no key is provided, take the first association available */

            assert('!isset($foreignKeys); // Cannot redeclare variable $foreignKeys');
            $foreignKeys = $sourceTable->getForeignKeys();

            if (empty($foreignKeys)) {
                return false;
            }
            assert('!isset($foreignTable); // Cannot redeclare variable $foreignTable');
            assert('!isset($foreignKey); // Cannot redeclare variable $foreignKey');
            assert('!isset($foreignPrimaryKey); // Cannot redeclare variable $foreignPrimaryKey');
            assert('!isset($baseColumn); // Cannot redeclare variable $baseColumn');
            assert('!isset($foreignColumn); // Cannot redeclare variable $foreignColumn');
            /* @var $foreignKey \Yana\Db\Ddl\ForeignKey */
            foreach ($foreignKeys as $foreignKey)
            {
                assert($foreignKey instanceof \Yana\Db\Ddl\ForeignKey);
                $foreignTable = $foreignKey->getTargetTable();
                // skip if table doesn't match
                if ($tableName === $foreignTable) {
                    $foreignPrimaryKey = $targetTable->getPrimaryKey();
                    foreach ($foreignKey->getColumns() as $baseColumn => $foreignColumn)
                    {
                        if (empty($foreignColumn)) {
                            $foreignColumn = $foreignPrimaryKey;
                        }
                        $key1 = mb_strtolower($baseColumn);
                        $key2 = mb_strtolower($foreignColumn);
                        if ($key2 === $foreignPrimaryKey) {
                            break 2;
                        }
                    }
                    unset($baseColumn, $foreignColumn);
                    if (is_null($key2)) {
                        return false;
                    }
                    unset($foreignPrimaryKey);
                }
                unset($foreignTable);
            }
            unset($foreignKey, $foreignKeys);

        }

        if (is_null($key1)) {
            return false;
        }

        // if the target column is not provided, take the primary key instead
        if (is_null($key2)) {
            $key2 = $targetTable->getPrimaryKey();
        }

        return !is_null($key1) && !is_null($key2);
    }

    /**
     * Set source table.
     *
     * For statements like "Select * from [table]" this is the table name.
     * If your query uses multiple tables (via a join) this is the name of the base-table (the first table in the list).
     *
     * @param   string  $table  table name to use in query
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException  when the table does not exist
     * @return  \Yana\Db\Queries\AbstractQuery
     */
    public function setTable($table)
    {
        assert('is_string($table); // Wrong type for argument 1. String expected');
        $this->resetId();

        $tableName = mb_strtolower($table);
        $table = $this->getDatabase()->getSchema()->getTable($tableName);

        if (!($table instanceof \Yana\Db\Ddl\Table)) {
            $message = "The table '$tableName' is unknown.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException($message, $level);
        }

        // Auto-attach profile check to where clause if profile constraint is present.
        $this->profile = array();
        if ($table->hasProfile()) {
            $builder = new \Yana\ApplicationBuilder();
            $application = $builder->buildApplication();
            $this->profile = array('profile_id', '=', $application->getProfileId());
            unset($builder, $application);
        }

        // We expect the result to be a table.
        if ($this->expectedResult === \Yana\Db\ResultEnumeration::UNKNOWN) {
            $this->expectedResult = \Yana\Db\ResultEnumeration::TABLE;
        }

        // assign table name and definition
        $this->tableName = $tableName;
        $this->table = $table;

        /**
         * inheritance check
         *
         * Details: If one table inherits from another - that is if the primary
         * key is also a foreign key - then these are to be joined automatically.
         */
        if ($this->useInheritance) {
            $this->detectInheritance($this->table);
        }
        return $this;
    }

    /**
     * Get the name of the currently selected table.
     *
     * Returns the lower-cased name of the currently selected table, or bool(false) if none has been selected yet.
     *
     * @return  bool(false)|string
     */
    public function getTable()
    {
        if (is_string($this->tableName)) {
            return $this->tableName;
        } else {
            /* error: no table has been selected, yet */
            return false;
        }
    }

    /**
     * Get current table.
     *
     * @return  \Yana\Db\Ddl\Table
     * @throws  \Yana\Db\Queries\Exceptions\TableNotSetException  if table has not been initialized
     */
    protected function currentTable()
    {
        if (!isset($this->table)) {
            if (!$this->getTable()) {
                throw new \Yana\Db\Queries\Exceptions\TableNotSetException("Need to set table first!");
            }
            $this->table = $this->getDatabase()->getSchema()->getTable($this->getTable());
        }
        return $this->table;
    }

    /**
     * Set source column.
     *
     * Checks if the column exists and sets the source column of the query to the given value.
     *
     * @param   string  $column  column name or '*' for "all"
     * @param   string  $alias   optional column alias
     * @throws  \Yana\Db\Queries\Exceptions\TableNotSetException     if table has not been initialized
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       if a given argument is invalid
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the given column is not found in the table
     * @return  \Yana\Db\Queries\AbstractQuery
     * @ignore
     */
    protected function setColumn($column = '*')
    {
        return $this->setColumnWithAlias($column);
    }

    /**
     * Set source column.
     *
     * Checks if the column exists and sets the source column of the query to the given value.
     *
     * @param   string  $column  column name or '*' for "all"
     * @param   string  $alias   optional column alias
     * @throws  \Yana\Db\Queries\Exceptions\TableNotSetException     if table has not been initialized
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       if a given argument is invalid
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the given column is not found in the table
     * @return  \Yana\Db\Queries\AbstractQuery
     * @ignore
     */
    protected function setColumnWithAlias($column = '*', $alias = "")
    {
        assert('is_string($column); // Wrong type for argument 1. String expected');
        assert('is_string($alias); // Wrong type for argument 2. String expected');
        $this->resetId();

        /**
         * 1) wrong order of commands, need to set up table first
         */
        if (empty($this->tableName)) {
            throw new \Yana\Db\Queries\Exceptions\TableNotSetException("Cannot set column - need to set table first!");
        }

        /**
         * 2) select all columns
         */
        if ($column === '*' || $column === '') {

            /**
             * set column
             */
            $this->column = array();
            if ($this->row === '*') {
                if ($this->expectedResult !== \Yana\Db\ResultEnumeration::ROW) {
                    $this->expectedResult = \Yana\Db\ResultEnumeration::TABLE;
                }
            } else {
                $this->expectedResult = \Yana\Db\ResultEnumeration::ROW;
            }


        /*
         * 3) select one specific column
         */
        } else {

            /*
             * 3.1) extract table, where provided
             */
            assert('!isset($table); // Cannot redeclare var $table');
            if (strpos($column, '.')) {
                list($table, $column) = explode('.', $column);
                $this->setTable($table);
                unset($table);
            } else {
                $table = $this->getParentByColumn($column);
                if ($table !== false) {
                    $this->setTable($table);
                }
                unset($table);
            }

            /*
             * 3.2) invalid argument, not a column
             */
            if (YANA_DB_STRICT && !$this->currentTable()->isColumn($column)) {
                throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException("The column '$column' is not found in table " .
                    "'{$this->tableName}'.", \Yana\Log\TypeEnumeration::WARNING);
            }

            /*
             * 3.3) set column
             */
            assert('!isset($columnValue); // Cannot redeclare var $columnValue');
            $this->column = array();
            $alias = $alias > "" ? (string) $alias : 0;
            $this->column[$alias] = array($this->tableName, mb_strtolower($column));
            if ($this->row !== '*' || $this->getExpectedResult() === \Yana\Db\ResultEnumeration::ROW) {
                $this->expectedResult = \Yana\Db\ResultEnumeration::CELL;
            } else {
                if (!$this->currentTable()->getColumn($column)->isPrimaryKey()) {
                    $this->column[] = array($this->tableName, $this->table->getPrimaryKey());
                }
                $this->expectedResult = \Yana\Db\ResultEnumeration::COLUMN;
            }

        }
        return $this;
    }

    /**
     * Set array address.
     *
     * Applies to columns of type 'array' only.
     *
     * You may provide the array key inside the value of the column that you wish to get.
     * If it is a multidimensional array, you may traverse in deeper dimensions by linking
     * keys with a dot '.' - for example: "foo.bar" gets $result['foo']['bar'].
     *
     * Note: this will not check if the key that you provided is
     * a valid key or if it really points to a value. If it is not,
     * the resultset will be empty.
     *
     * @param   string  $arrayAddress   array address
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException    if a given argument is invalid
     * @throws  \Yana\Db\Queries\Exceptions\TableNotSetException  if table has not been initialized (Only in STRICT mode!)
     * @return  \Yana\Db\Queries\AbstractQuery
     * @ignore
     */
    protected function setArrayAddress($arrayAddress = "")
    {
        assert('is_string($arrayAddress); // Wrong type for argument 1. String expected');

        if (YANA_DB_STRICT && !empty($arrayAddress)) {
            /**
             * error - cannot set array address on a table
             */
            if ($this->expectedResult !== \Yana\Db\ResultEnumeration::CELL &&
                $this->expectedResult !== \Yana\Db\ResultEnumeration::COLUMN) {
                $message = "Array address may only be used on cells, not rows or tables.";
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
            }
            /*
             * error - not a column of type array
             */
            $columnName = $this->getColumn();
            $column = $this->currentTable()->getColumn($columnName);
            if ($column->getType() !== 'array') {
                throw new \Yana\Core\Exceptions\InvalidArgumentException("Array address can only be used on columns " .
                    "of type array. Found column of type '" . $column->getType() .
                    "' instead.", \Yana\Log\TypeEnumeration::WARNING);
            }
            unset($column, $columnName);
        }

        $this->arrayAddress = "$arrayAddress";
        return $this;
    }

    /**
     * Get the currently selected column.
     *
     * Returns the lower-cased name of the currently selected column.
     *
     * If none has been selected, '*' is returned.
     *
     * Version info: the argument $i became available in 2.9.6.
     * When multiple columns are selected, use this argument to
     * choose the index of the column you want. Where 0 is the
     * the first column, 1 is the second aso.
     * If the argument $i is not provided, the function returns
     * the first column.
     *
     * See {link \Yana\Db\AbstractQuery::getColumns()} to get a list of all
     * selected columns.
     *
     * @param   int     $i  index of column to get
     * @return  string
     * @ignore
     */
    protected function getColumn($i = null)
    {
        if (is_array($this->column)) {
            if (is_null($i)) {
                reset($this->column);
                $i = key($this->column);
            }
            if (isset($this->column[$i])) {
                if (is_array($this->column[$i])) {
                    if (isset($this->column[$i][1]) && is_string($this->column[$i][1])) {
                        return $this->column[$i][1];
                    }

                } elseif (is_string($this->column[$i])) {
                    return $this->column[$i];

                }
            }

        } elseif (is_string($this->column)) {
            return $this->column;

        }

        /* error: invalid property */
        return '*';
    }

    /**
     * Get the list of all selected columns.
     *
     * Returns the lower-cased names of the currently selected columns as a numeric array of strings.
     * If none has been selected, an empty array is returned.
     *
     * @return  array
     * @ignore
     */
    protected function getColumns()
    {
        $columns = array();
        if (is_array($this->column)) {
            $columns = $this->column;

        /*
         * catchable error: column is string (can be converted to array)
         */
        } elseif (is_string($this->column)) {
            $columns = array(array($this->tableName, $this->column));

        }
        // else: column has unexpected type

        return $columns;
    }

    /**
     * Set source row.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * Note: does not check if row exists.
     *
     * Currently you may only request 1 row or all.
     * To search for all rows, use the wildcard '*'.
     *
     * @param   scalar  $row  set source row
     * @throws  \Yana\Db\Queries\Exceptions\TableNotSetException     if table has not been initialized
     * @return  \Yana\Db\Queries\AbstractQuery
     */
    public function setRow($row)
    {
        assert('is_scalar($row); // Wrong argument type for argument 1. Scalar expected.');
        $this->resetId();

        /*
         * 1) wrong order of commands, need to set up table first
         */
        if (empty($this->tableName)) {
            throw new \Yana\Db\Queries\Exceptions\TableNotSetException("Cannot set row - need to set table first!");
        }
        $table = $this->currentTable();

        /*
         * 2) select all rows
         */
        if ($row === '' || $row === '*' || $row === '?') {

            /*
             * 2.1) reset row id
             */
            $this->rowId = array();
            /*
             * 2.2) set row
             */
            $this->row = '*';
            /*
             * 2.3) update type of expected result
             */
            $auto = $table->getColumn($table->getPrimaryKey());
            if ($row === '?' || ($this->type === \Yana\Db\Queries\TypeEnumeration::INSERT && $auto->isAutoFill())) {
                if (empty($this->column)) {
                    $this->expectedResult = \Yana\Db\ResultEnumeration::ROW;
                } else {
                    $this->expectedResult = \Yana\Db\ResultEnumeration::CELL;
                }
            } elseif (empty($this->column)) {
                $this->expectedResult = \Yana\Db\ResultEnumeration::TABLE;
            } else {
                $this->expectedResult = \Yana\Db\ResultEnumeration::COLUMN;
            }
            unset ($auto);

        /*
         * 3) select one specific row
         */
        } else {

            $row = mb_strtoupper("$row");

            /*
             * 3.1) update row id
             */
            $this->rowId = array(array($this->tableName, $table->getPrimaryKey()), '=', $row);
            /*
             * 3.2) set row
             */
            $this->row = mb_strtolower($row);
            /*
             * 3.3) update type of expected result
             */
            if (empty($this->column) || count($this->column) > 1) {
                $this->expectedResult = \Yana\Db\ResultEnumeration::ROW;
            } else {
                $this->expectedResult = \Yana\Db\ResultEnumeration::CELL;
            }
        }
        return $this;
    }

    /**
     * Get the currently selected row.
     *
     * Returns the lower-cased name of the currently
     * selected column, or bool(false) if none has been
     * selected yet.
     *
     * If none has been selected, '*' is returned.
     *
     * @return  string
     */
    public function getRow()
    {
        $row = '*';
        if (is_string($this->row)) {
            $row = mb_strtolower($this->row);
        }
        return $row;
    }

    /**
     * Resolve key address to determine table, column and row.
     *
     * @param   string  $key  resolve key address to determine table, column and row
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if the given table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the given column is not found
     * @throws  \Yana\Db\Queries\Exceptions\InconsistencyException   when a foreign key check detects invalid database values
     * @throws  \Yana\Db\Queries\Exceptions\TargetNotFoundException  when no target can be found for the given key
     * @return  \Yana\Db\Queries\AbstractQuery
     */
    public function setKey($key)
    {
        assert('is_scalar($key); // Wrong argument type for argument 1. String expected.');
        assert('preg_match("/^[\w\d-_]+(\.(\w[^\.]*|\*|\?)){0,}(\.\*)?$/i", $key);'
            . " // Syntax error. The key '{$key}' is not valid.");

        $key = preg_replace("/\.(\*)?$/", '', $key);
        $array = explode(".", $key);
        assert('!empty($array); // Invalid argument $key');
        $dbSchema = $this->getDatabase()->getSchema();

        // get table definition
        assert('!isset($table); // cannot redeclare variable $table');
        $table = $dbSchema->getTable($array[0]);
        if (! $table instanceof \Yana\Db\Ddl\Table) {
            $message = "Table not found '{$array[0]}' in schema '{$dbSchema->getName()}'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException($message, $level);
        }

        /*
         * 2) input is valid
         *
         * 2.1) resolve foreign keys to get true adress
         */
        if (count($array) > 3) {
            assert('!isset($column); // cannot redeclare variable $column');
            $column = $table->getColumn($array[2]);
            if (! $column instanceof \Yana\Db\Ddl\Column) {
                $message = "Column not found '{$array[2]}'";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException($message, $level);
            }
            assert('!isset($isArray); // cannot redeclare variable $isArray');
            $isArray = ($column->getType() === 'array');

            assert('!isset($a); // cannot redeclare variable $a');
            assert('!isset($foreignTable); // cannot redeclare variable $foreignTable');
            while (!$isArray && count($array) > 3 && $column->isForeignKey())
            {
                $a = $this->getDatabase()->select($array[0] . "." . $array[1] . "." . $array[2]);
                if (empty($a)) {
                    throw new \Yana\Db\Queries\Exceptions\InconsistencyException("Operation aborted due to invalid foreign key." .
                        " Unable to resolve foreign key '{$key}'." .
                        " This may mean one of the associated tables contains inconsistent data." .
                        " Check if foreign key association has been broken.");
                }
                $foreignTable = $table->getTableByForeignKey($array[2]);
                @array_shift($array);
                @array_shift($array);
                @array_shift($array);
                array_unshift($array, $a);
                array_unshift($array, $foreignTable);
                $table = $dbSchema->getTable($array[0]);
                assert('$table instanceof \Yana\Db\Ddl\Table; // Table not found');
                $column = $table->getColumn($array[2]);
                assert('$column instanceof \Yana\Db\Ddl\Column; // Column not found');
                $isArray = ($column->getType() === 'array');
            }
            unset($a, $foreignTable, $column);
            if ($isArray) {
                $this->arrayAddress = implode('.', array_slice($array, 3));
                $array = array_slice($array, 0, 3);
            } else {
                /* intentionally left blank */
            }
            if (!$isArray && count($array) > 3) {
                $message = "There is no database object that corresponds to the key '{$key}'.";
                throw new \Yana\Db\Queries\Exceptions\TargetNotFoundException($message);
            }
            unset($isArray);
        } // end if

        /*
         * 2.2) set new values
         */

        /*
         * 2.2.1) select table
         */
        $this->setTable($array[0]);

        /*
         * 2.2.2) select row (if any)
         */
        if (!isset($array[1]) || $array[1] === '') {
            $this->setRow('*');
        } else {
            $this->setRow($array[1]);
            if ($array[1] === '?') {
                // order by primary key
                $this->setOrderBy(array($table->getPrimaryKey($array[0])), array(true));
                $this->setLimit(1);
            }
        }

        /*
         * 2.2.3) select column (if any)
         */
        if (empty($array[2])) {
            $this->setColumn('*');
        } else {
            $this->setColumn($array[2]);
        }
        return $this;
    }

    /**
     * Add column to "order by"-clause.
     *
     * @param   string  $column  column name
     * @param   bool    $desc    sort descending (true=yes, false=no)
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   when the base table does not exist
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when the column does not exist
     * @return  \Yana\Db\Queries\AbstractQuery
     * @ignore
     */
    protected function addOrderBy($column, $desc = false)
    {
        assert('is_string($column); // Wrong argument type for argument 1. String expected.');
        assert('is_bool($desc); // Wrong argument type for argument 2. Boolean expected.');

        /*
         * 2.2.1) get base table
         */
        if (strpos($column, '.')) {
            list($tableName, $column) = explode('.', $column);
        } else {
            $tableName = $this->tableName;
        }
        $table = $this->getDatabase()->getSchema()->getTable($tableName);
        if (!($table instanceof \Yana\Db\Ddl\Table)) {
            $message = "No such table '" . $tableName . "'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException($message, $level);
        }

        /*
         * 2.2.2) check if column exists
         */
        if (!$table->isColumn($column)) {
            $message = "Column '$column' not found in table '" . $tableName . "'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException($message, $level);
        }
        $this->orderBy[] = array($tableName, mb_strtolower($column));
        $this->desc[] = $desc;
        return $this;
    }

    /**
     * Set column to sort the resultset by.
     *
     * @param   array  $orderBy  list of column names
     * @param   array  $desc     list of sort order (true=desc, false=asc)
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   when the base table does not exist
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when the column does not exist
     * @return  \Yana\Db\Queries\AbstractQuery
     * @ignore
     */
    protected function setOrderBy($orderBy, $desc = array())
    {
        settype($orderBy, 'array');
        settype($desc, 'array');
        $this->resetId();
        $this->orderBy = array();
        $this->desc = array();

        // reset when empty
        if (empty($orderBy)) {
            return $this;
        }

        foreach($orderBy as $i => $column)
        {
            $this->addOrderBy($column, !empty($desc[$i])); // may throw exception
        }
        return $this;
    }

    /**
     * Get the list of columns the resultset is ordered by.
     *
     * Returns a lower-cased list of column names.
     * If none has been set yet, then the list is empty.
     *
     * @return  array
     * @ignore
     */
    protected function getOrderBy()
    {
        assert('is_array($this->orderBy);');
        return $this->orderBy;
    }

    /**
     * Check if resultset is sorted in descending order.
     *
     * Returns an array of boolean values: true = descending, false = ascending.
     *
     * @return  array
     * @ignore
     */
    protected function getDescending()
    {
        assert('is_array($this->desc);');
        return $this->desc;
    }

    /**
     * Convert where clause to string.
     *
     * Returns the where condition clause as a string for printing.
     *
     * @param   array  $where  where clausel as an array
     * @return  string
     * @ignore
     */
    protected function convertWhereToString(array $where)
    {
        if (empty($where)) {
            return "";
        }
        /* if all required information is provided */
        assert('count($where) === 3; // Where clause must have exactly 3 items: left + right operands + operator');
        $leftOperand = $where[0];
        $operator = $where[1];
        $rightOperand = $where[2];

        /**
         * 1) is sub-clause
         */
        switch ($operator)
        {
            case 'or':
                return $this->convertWhereToString($leftOperand) . ' OR ' . $this->convertWhereToString($rightOperand);
            break;
            case 'and':
                return $this->convertWhereToString($leftOperand) . ' AND ' . $this->convertWhereToString($rightOperand);
            break;
        }

        /**
         * 2) is atomar clause
         */
        // left operand
        if (is_array($leftOperand)) {
            $leftOperand = $this->getDatabase()->quoteId(YANA_DATABASE_PREFIX.$leftOperand[0]) . '.' . $leftOperand[1];
        }
        // right operand
        if ($operator === 'exists' || $operator === 'not exists') {
            if ($rightOperand instanceof \Yana\Db\Queries\Select) {
                $rightOperand = "($rightOperand)";
            }
        } elseif ($operator === 'in' || $operator === 'not in') {
            assert('!isset($value); // cannot redeclare variable $value');
            assert('!isset($list); // cannot redeclare variable $list');
            if ($rightOperand instanceof \Yana\Db\Queries\Select) {
                $list = (string) $rightOperand;
            } else {
                $list = "";
                foreach ($rightOperand as $value)
                {
                    if (!empty($list)) {
                        $list .= ", ";
                    }
                    if (is_string($value)) {
                        $value = $this->getDatabase()->quote($value);
                    }
                    $list .= $value;
                }
            }
            $rightOperand = "($list)";
            unset($value, $list);
        } elseif (is_array($rightOperand)) {
            $rightOperand = $this->getDatabase()->quoteId(YANA_DATABASE_PREFIX.$rightOperand[0]) . '.' . $rightOperand[1];
        } elseif (is_string($rightOperand)) {
            $rightOperand = $this->getDatabase()->quote($rightOperand);
        } elseif (is_null($rightOperand)) {
            if ($operator == '=') {
                return $leftOperand . ' is null ';
            } elseif ($operator == '!=') {
                return $leftOperand . ' is not null ';
            } else {
                $message = "The invalid operator '" . $operator .
                    "' in your where clause has been ignored.";
                \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::INFO);
                return "";
            }
        }

        return $leftOperand . ' ' . $operator . ' ' . $rightOperand;
    }

    /**
     * Check contents of where clause.
     *
     * Returns the parsed and checked array.
     *
     * @param   array  $where         where clausel as an array
     * @param   array  $dontOptimize  will try to move primary key constraints from where clause unless told otherwise
     * @return  array
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   when a referenced table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when a referenced column is not found
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       when the clause contains invalid values
     * @ignore
     */
    protected function parseWhereArray(array $where, $dontOptimize = false)
    {
        if (empty($where)) {
            return array();
        }
        if (count($where) !== 3) {
            $message = "Invalid where clause.\n\t\tMalformed argument '" . print_r($where, true) . "'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $leftOperand = $where[0];
        $operator = strtolower($where[1]);
        $rightOperand = $where[2];

        /**
         * 1) is sub-clause
         */
        switch ($operator)
        {
            case 'and':
            case 'or':
                return array($this->parseWhereArray($leftOperand, true), $operator, $this->parseWhereArray($rightOperand, true));
            break;
        }

        /*
         * 2) is singular clause
         */

        /**
         * 2.1) handle left operator (must be column name)
         */
        if (is_array($leftOperand) && count($leftOperand) === 2) {
            $tableName = mb_strtolower(array_shift($leftOperand));
            $column = mb_strtolower(array_shift($leftOperand));

        } elseif (is_string($leftOperand)) {
            $tableName = $this->tableName;
            $column = mb_strtolower($leftOperand);

        } else {
            $message = "Missing column name in where clause.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }

        /**
         * check if table - column pair is valid
         */
        if (YANA_DB_STRICT) {
            $table = $this->getDatabase()->getSchema()->getTable($tableName);
            assert('is_string($column); // Unexpected result: $column. String expected.');

            if (! $table instanceof \Yana\Db\Ddl\Table) {
                $message = "Invalid where clause. The name '{$tableName}' is not a table.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Db\Queries\Exceptions\TableNotFoundException($message, $level);

            }
            if (!$table->isColumn($column)) {
                $message = "Invalid where clause. The name '{$column}' is not a column in table '{$tableName}'.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException($message, $level);
            }
            /**
             * check if the request is a table scan
             *
             * Reason: When scanning a whole table you may
             * search for primary keys - otherwise not.
             */
            $isTableScan = ($this->row === '*' || is_null($this->row) || $this->row === '?');
            if (!$isTableScan && $table->getColumn($column)->isPrimaryKey()) {
                $message = "Invalid where clause. " .
                    "You are trying to search for a primary key.\n\t\t" .
                    "This is not allowed, since it might cause results wether to be ambigious or empty.\n\t\t" .
                    "Turn strict checks off if you wish to continue without checking.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);

            }
        }
        $leftOperand = array($tableName, $column);
        unset($tableName, $table, $column);

        /**
         * 2.2) handle right operator (must be column name or string constant)
         */
        switch (true)
        {
            // is array
            case $operator === 'in' || $operator === 'not in':
                assert('is_array($rightOperand) || $rightOperand instanceof \Yana\Db\Queries\Select;');
            break;

            // is sub-query
            case $operator === 'exists' || $operator === 'not exists':
                assert('$rightOperand instanceof \Yana\Db\Queries\SelectExist;');
            break;

            // is column name
            case is_array($rightOperand) && count($rightOperand) === 2:

                $tableName = mb_strtolower(array_shift($rightOperand));
                $column = mb_strtolower(array_shift($rightOperand));

                /**
                 * check if table - column pair is valid
                 */
                if (YANA_DB_STRICT) {
                    $table = $this->getDatabase()->getSchema()->getTable($tableName);
                    if (! $table instanceof \Yana\Db\Ddl\Table) {
                        throw new \Yana\Db\Queries\Exceptions\TableNotFoundException("Invalid where clause. " .
                            "The name '{$tableName}' is not a table.", \Yana\Log\TypeEnumeration::WARNING);

                    } elseif (!$table->isColumn($column)) {
                        throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException("Invalid where clause. " .
                            "The name '{$column}' is not a column in table '{$tableName}'.",
                            \Yana\Log\TypeEnumeration::WARNING);
                    }
                } // end if (strict)
                $rightOperand = array($tableName, $column);
                unset($tableName, $column);
            break;

            // is string constant
            case !is_null($rightOperand):

                $rightOperand = (string) $rightOperand;

            break;

            // default: $rightOperand is NULL
        }

        /**
         * 2.3) handle operator
         */
        switch ($operator)
        {
            case '==':
            case '=':
                $operator = '=';
            break;
            case '<>':
            case '!=':
                $operator = '!=';
            break;
            case 'exists':
            case 'not exists':
                if (!($rightOperand instanceof \Yana\Db\Queries\SelectExist)) {
                    $message = "Invalid where clause.\n\t\t" .
                        "The operator '{$operator}' requires the right operand " .
                        "to be an instance of \Yana\Db\Queries\SelectExist.";
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
                }
            break;
            case 'in':
            case 'not in':
                if (!is_array($rightOperand)) {
                    if (!($rightOperand instanceof \Yana\Db\Queries\Select)) {
                        $message = "Invalid where clause.\n\t\t" .
                            "The operator '{$operator}' requires the right operand to be an array.";
                        $level = \Yana\Log\TypeEnumeration::WARNING;
                        throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
                    }
                }
            break;
            case 'like':
            case 'regexp':
            case '<':
            case '>':
            case '<=':
            case '>=':
                if (is_null($rightOperand)) {
                    $message = "Invalid where clause.\n\t\t" .
                        "The operator '{$operator}' is not supported when comparing a column with NULL.";
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
                }
            break;
            default:
                $message = "Invalid where clause.\n\t\tThe operator '{$operator}' is not supported.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
            break;
        }
        if ($rightOperand instanceof self) {
            $rightOperand->isSubQuery = true;
        }
        if (!$dontOptimize && empty($this->rowId) && $operator == '=' && is_string($rightOperand)) {
            $primaryKey = $this->currentTable()->getPrimaryKey();
            switch (true)
            {
                case is_array($leftOperand)  && strcasecmp($primaryKey, $leftOperand[1]) === 0:
                case !is_array($leftOperand) && strcasecmp($primaryKey, $leftOperand) === 0:
                    $this->setRow($rightOperand);
                    return array();
                break;
            }
            unset($primaryKey);
        }

        return array($leftOperand, $operator, $rightOperand);
    }

    /**
     * Set where clause.
     *
     * The syntax is as follows:
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
     * To unset the where clause, call this function without
     * providing a parameter.
     *
     * @param   array  $where  where clause
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   when a referenced table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when a referenced column is not found
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       when the where-clause contains invalid values
     * @ignore
     * @return  \Yana\Db\Queries\AbstractQuery
     */
    protected function setWhere(array $where = array())
    {
        // clear cached query id
        $this->resetId();

        $this->where = $this->parseWhereArray($where); // throws exception
        return $this;
    }

    /**
     * add where clause
     *
     * The syntax is as follows:
     * array(0=>column,1=>operator,2=>value)
     * Where "operator" can be one of the following:
     * '=', 'REGEXP', 'LIKE', '<', '>', '!=', '<=', '>='
     *
     * @param   array  $where  where clause
     * @throws  \Yana\Core\Exceptions\NotFoundException         when a column is not found
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the having-clause contains invalid values
     * @return  \Yana\Db\Queries\SelectExist 
     */
    protected function addWhere(array $where)
    {
        if (!empty($where)) {
            if (!empty($this->where)) {
                // clear cached query id
                $this->resetId();
                $this->where = array($this->parseWhereArray($where), 'and', $this->where);
            } else {
                $this->setWhere($where);
            }
        }
        return $this;
    }

    /**
     * Returns the current where clause.
     *
     * @return  array
     * @ignore
     */
    protected function getWhere()
    {
        if (!is_array($this->where)) {
            return array();
        }
        $where = $this->where;
        // automatically add profile constraint
        if (!empty($this->profile)) {
            if (empty($where)) {
                $where = $this->profile;
            } else {
                $where = array($this->profile, 'and', $where);
            }
        }
        // automatically add primary key selector
        if (!empty($this->rowId)) {
            if (empty($where)) {
                $where = $this->rowId;
            } else {
                $where = array($this->rowId, 'and', $where);
            }
        }
        if ($this->type === \Yana\Db\Queries\TypeEnumeration::EXISTS && !empty($this->column)) {
            assert('!isset($column); // Cannot redeclare var $column');
            foreach ($this->getColumns() as $column)
            {
                if (empty($where)) {
                    $where = array($column, '!=', null);
                } else {
                    $where = array(array($column, '!=', null), 'and', $where);
                }
            }
            unset($column);
        }
        return $where;
    }

    /**
     * Get the currently selected limit.
     *
     * Note: This setting will not be part of the sql statement produced by __toString().
     * Use the API's $limit and $offset parameter instead when sending
     * the query.
     *
     * This restriction does not apply if you use
     * {link \Yana\Db\AbstractQuery::sendQuery()}.
     *
     * Note: For security reasons all delete queries will automatically
     * be limited to 1 row at a time.
     *
     * @return  int
     * @since   2.9.3
     */
    public function getLimit()
    {
        assert('is_int($this->limit); // Expecting member "limit" to be an integer.');
        return (int) $this->limit;
    }

    /**
     * Set a limit for this query.
     *
     * Note: This setting will not be part of the sql statement produced by __toString().
     * Use the API's $limit and $offset parameter instead when sending
     * the query.
     *
     * This restriction does not apply if you use
     * {link \Yana\Db\AbstractQuery::sendQuery()}.
     *
     * @param   int  $limit  limit for this query
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when limit is not positive
     * @return  \Yana\Db\Queries\AbstractQuery
     */
    protected function setLimit($limit)
    {
        assert('is_int($limit); // Wrong argument type for argument 1. Integer expected.');
        $this->resetId();
        if ($limit < 0) {
            $message = "Limit must not be negative: '$limit'";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $this->limit = (int) $limit;
        return $this;
    }

    /**
     * Get the currently selected offset.
     *
     * Note: This setting will not be part of the sql statement produced by __toString().
     * Use the API's $limit and $offset parameter instead when sending the query.
     *
     * This restriction does not apply if you use sendQuery().
     *
     * @return  int
     * @since   2.9.3
     */
    public function getOffset()
    {
        assert('is_int($this->offset); // Expecting member "offset" to be an integer');
        return (int) $this->offset;
    }

    /**
     * Get unique id.
     *
     * @return  string
     * @ignore
     */
    public function toId()
    {
        if (!isset($this->_id)) {
            $this->_id = serialize(array($this->type, $this->tableName, $this->column, $this->row,
            $this->where, $this->orderBy, $this->having, $this->desc, $this->joins, $this->offset, $this->limit, $this->values));
        }
        return $this->_id;
    }

    /**
     * Resets query ID to null.
     *
     * @return  $this
     */
    protected function resetId()
    {
        $this->_id = null;
        return $this;
    }

    /**
     * Get old values.
     *
     * For update and delete queries this function will retrieve and return the unmodified values.
     *
     * @return  array
     * @ignore
     */
    protected function getOldValues()
    {
        if (!isset($this->_oldValues)) {
            $query = new \Yana\Db\Queries\Select($this->getDatabase());
            $query->setTable($this->getTable());
            $query->setRow($this->getRow());
            if ($this->getRow() === '*') {
                $query->setWhere($this->getWhere());
            }
            $oldValues = $this->getDatabase()->select($query);
            if ($query->getExpectedResult() === \Yana\Db\ResultEnumeration::ROW) {
                $oldValues = array($oldValues);
            }
            $this->_oldValues = $oldValues;
        }
        return $this->_oldValues;
    }

    /**
     * Send query to database-server.
     *
     * Returns a result-object.
     *
     * @return  \Yana\Db\IsResult
     * @since   2.9.3
     * @ignore
     */
    public function sendQuery()
    {
        return $this->getDatabase()->sendQueryObject($this);
    }

    /**
     * Delete old files.
     *
     * When a row is deleted or updated, blobs associated with it old values need to be removed.
     *
     * A list of these files was created before the row was deleted or updated.
     * After the statements was successfully carried out, the old files need to be removed.
     *
     * @param   array  $files  list of files that should be deleted
     * @return  \Yana\Db\Queries\AbstractQuery
     * @ignore
     */
    protected function deleteFiles(array $files = array())
    {
        // abort if there is nothing to do
        if (empty($files)) {
            return;
        }
        $values = $this->getOldValues();
        if (empty($values)) {
            return;
        }
        // iterate over list of file-columns
        foreach ($files as $column)
        {
            if (is_array($column) && !empty($column['error'])) {
                continue;
            }
            if (!$column instanceof \Yana\Db\Ddl\Column) {
                $column = $column['column'];
            }
            assert('$column instanceof \Yana\Db\Ddl\Column;');
            $columnName = mb_strtoupper($column->getName());
            // delete old files
            if (isset($values[$columnName]) && $values[$columnName] > "") {
                assert('is_string($values[$columnName]);');

                try {
                    \Yana\Db\Binaries\File::removeFile($values[$columnName]);

                } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                    // @codeCoverageIgnoreStart

                    // Create a database event log entry for each file that was not found.
                    assert('!isset($message); // Cannot redeclare var $message');
                    $message = $e->getMessage();
                    try {
                        $message = "Error while trying to delete a row in table '" .
                        $this->currentTable()->getName() . "': " . $message;
                    } catch (Exception $ex) {
                        $message = "Error while trying to delete a row: " . $message;
                    }
                    \Yana\Log\LogManager::getLogger()->addLog($message);
                    // @codeCoverageIgnoreEnd
                }
            }
        }
        return $this;
    }

    /**
     * @return  string
     * @codeCoverageIgnore
     */
    public function __toString()
    {
        try {
            return $this->toString();
        } catch (\Exception $e) {
            \Yana\Log\LogManager::getLogger()->addLog($e->getMessage(), $e->getCode(), $e);
            return "";
        }
    }

    /**
     * Build a SQL-query.
     *
     * @param   string  $stmt  sql statement template
     * @return  string
     */
    protected function toString($stmt = "")
    {
        /* 1. replace %TABLE% */
        if (strpos($stmt, '%TABLE%') !== false) {
            $table = $this->getTable();
            if (!is_string($table)) {
                return false;
            }
            $table = $this->getDatabase()->quoteId(YANA_DATABASE_PREFIX.$this->getTable());
            $stmt = str_replace('%TABLE%', $table, $stmt);
        }

        /* 2. replace %WHERE% */
        if (strpos($stmt, '%WHERE%') !== false) {
            assert('!isset($where); // Cannot redeclare var $where');
            $where = $this->getWhere();

            if (is_array($where) && count($where) > 0) {
                $where = $this->convertWhereToString($where);
                if (!empty($where)) {
                    $where = 'WHERE ' . $where;
                }
            } else {
                $where = "";
            }
            if (!empty($where)) {
                $stmt = str_replace('%WHERE%', trim($where), $stmt);
            } else {
                $stmt = str_replace(' %WHERE%', '', $stmt);
            }
            unset($where);
        }

        /* 3. replace %ORDERBY% */
        if (strpos($stmt, '%ORDERBY%') !== false) {
            assert('!isset($orderBy); // Cannot redeclare $orderBy');
            $orderBy = $this->getOrderBy();
            $desc = $this->getDescending();
            if (is_array($orderBy) && !empty($orderBy)) {
                assert('!isset($_orderBy); // Cannot redeclare var $_orderBy');
                $_orderBy = 'ORDER BY ';
                assert('!isset($i); // Cannot redeclare var $i');
                assert('!isset($element); // Cannot redeclare var $element');
                foreach ($orderBy as $i => $element)
                {
                    if (is_array($element)) {
                        $_orderBy .= $element[0] . '.' . $element[1];
                    } else {
                        $_orderBy .= $this->tableName . '.' . $element;
                    }
                    if (!empty($desc[$i])) {
                        $_orderBy .= ' DESC';
                    }
                    if (++$i < count($orderBy)) {
                        $_orderBy .= ', ';
                    }
                } /* end foreach */
                unset($i, $element); /* clean up garbage */
                $stmt = str_replace('%ORDERBY%', $_orderBy, $stmt);
                unset($_orderBy); /* clean up garbage */
            } else {
                $stmt = str_replace(' %ORDERBY%', '', $stmt);
            }
            unset($orderBy);
        }

        return $stmt;
    }

}

?>