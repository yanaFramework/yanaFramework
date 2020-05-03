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
abstract class AbstractQuery extends \Yana\Db\Queries\AbstractConnectionWrapper implements \Yana\Db\Queries\IsQuery
{

    /**
     * @var string
     */
    private $_id = null;

    /**#@+
     * @ignore
     */

    /**
     * @var int
     */
    protected $type = \Yana\Db\Queries\TypeEnumeration::UNKNOWN;

    /**
     * @var int
     */
    protected $expectedResult = \Yana\Db\ResultEnumeration::UNKNOWN;

    /**
     * @var \Yana\Db\Queries\IsJoinCondition[]
     */
    protected $joins = array();

    /**
     * @var bool
     */
    protected $isSubQuery = false;

    /** #@- */

    /**
     * @var string
     */
    private $_tableName = "";

    /**
     * @var string
     */
    private $_row = '*';

    /**
     * @var array
     */
    protected $column = array();

    /**
     * @var array
     */
    private $_profile = array();

    /**
     * @var array
     */
    private $_rowId = array();

    /**
     * @var array
     */
    private $_where = array();

    /**
     * @var array
     */
    private $_orderBy = array();

    /**
     * @var array
     */
    private $_desc = array();

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
    private $_arrayAddress = '';

    /**
     * @var bool
     */
    private $_useInheritance = true;

    /**
     * @var array
     */
    private $_parentTables = array();

    /**
     * @var array
     */
    private $_tableByColumn = array();

    /**
     * @var \Yana\Db\Ddl\Table
     */
    private $_table = null;

    /**
     * @var array
     */
    private $_oldValues = null;

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
        assert(is_string($name), 'Wrong type for argument 1. String expected');
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
        assert(is_string($name), 'Wrong type for argument 1. String expected');
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
     * @return  $this;
     */
    public function resetQuery()
    {
        $this->resetId();
        $this->_row           = '*';
        $this->column         = array();
        $this->_profile       = array();
        $this->_rowId         = array();
        $this->_where         = array();
        $this->_orderBy       = array();
        $this->_desc          = array();
        $this->_limit         = 0;
        $this->_offset        = 0;
        $this->joins          = array();
        $this->_arrayAddress  = '';
        $this->_parentTables  = array();
        $this->_tableByColumn = array();
        $this->_oldValues     = null;
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
                if ($this->_row === '*' && $this->expectedResult === \Yana\Db\ResultEnumeration::TABLE) {
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
                $this->_limit = 1;
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
    public function getType(): int
    {
        assert(is_int($this->type), 'Expecting member "type" to be an integer');
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
    public function getExpectedResult(): int
    {
        assert(is_int($this->type), 'Expecting member "expectedResult" to be an integer');
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
    public function useInheritance(bool $state)
    {
        $this->_useInheritance = $state;
        return $this;
    }

    /**
     * Check if automatic handling of inheritance is active.
     *
     * @return bool
     */
    protected function isUsingInheritance(): bool
    {
        return $this->_useInheritance;
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
            if (!isset($this->_tableByColumn[$columnName]) && !$table->isColumn($columnName)) {
                $this->_tableByColumn[$columnName] = $tableName;
            }
        }
        unset($columnName);
        /**
         * add table
         */
        $tableName = mb_strtoupper($table->getName());
        $this->_parentTables[$tableName] = $parentTable;
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
     * It will return an empty string if there is no such parent.
     *
     * @param   string  $columnName  name of a column
     * @since   2.9.6
     * @return  string
     * @ignore
     */
    protected function getParentByColumn(string $columnName): string
    {
        $ucColumnName = mb_strtoupper($columnName);
        $parentTable = "";
        if (isset($this->_tableByColumn[$ucColumnName])) {
            $parentTable = $this->_tableByColumn[$ucColumnName];
        }
        return $parentTable;
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
            $this->_tableByColumn[$columnName] = $tableName;
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
    public function getTableByColumn(string $columnName): \Yana\Db\Ddl\Table
    {
        $columnName = $this->getColumnByAlias($columnName);
        $dbSchema = $this->getDatabase()->getSchema();

        $table = null;
        // lazy loading: resolve source tables for requested column
        if (isset($this->_tableByColumn[$columnName])) {
            $table = $dbSchema->getTable($this->_tableByColumn[$columnName]); // When we are auto-resolving inheritance between tables

        } elseif ($this->currentTable()->isColumn($columnName)) {
            $table = $this->currentTable(); // may throw exception

        } elseif (!empty($this->joins)) {
            assert(!isset($joinedTable), 'Cannot redeclare var $joinedTable');
            assert(!isset($joinCondition), 'Cannot redeclare var $joinCondition');
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
     * If the argument $tableName is empty, or not provided, the
     * currently selected table (see {link \Yana\Db\Queries\AbstractQuery::setTable()})
     * is used instead.
     *
     * @param   string  $tableName  name of a table
     * @since   2.9.6
     * @return  string
     */
    public function getParent(string $tableName = ""): ?\Yana\Db\Ddl\Table
    {
        if ($tableName === "") {
            $tableName = $this->_tableName;
        }
        $ucTableName = mb_strtoupper($tableName);

        $parent = null;
        if (isset($this->_parentTables[$ucTableName])) {
            $parent = $this->_parentTables[$ucTableName];
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
     * @return  $this
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
        $primaryKey = mb_strtoupper((string) $table->getPrimaryKey());
        $primaryKeyColumn = $table->getColumn($primaryKey);
        assert($primaryKeyColumn instanceof \Yana\Db\Ddl\Column, 'Misspelled primary key column: ' . $primaryKey);
        while ($primaryKeyColumn->isForeignKey())
        {
            $fTableKey = mb_strtoupper($table->getTableByForeignKey($primaryKey));
            // detect circular reference (when table is already in parent list)
            if (in_array($fTableKey, $parents)) {
                break;
            }

            $foreignTable = $dbSchema->getTable($fTableKey);
            assert($foreignTable instanceof \Yana\Db\Ddl\Table, 'Misspelled foreign key in table: ' . $tableName);
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
     * @return  $this
     * @ignore
     */
    protected function setJoin($joinedTableName, $targetKey = null, $sourceTableName = null, $foreignKey = null, $isLeftJoin = false)
    {
        assert(is_string($joinedTableName), 'Wrong type for argument 1. String expected');
        assert(is_null($targetKey) || is_string($targetKey), 'Wrong type for argument 2. String expected');
        assert(is_null($sourceTableName) || is_string($sourceTableName), 'Wrong type for argument 3. String expected');
        assert(is_null($foreignKey) || is_string($foreignKey), 'Wrong type for argument 4. String expected');
        assert(is_bool($isLeftJoin), 'Wrong type for argument 5. Boolean expected');

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
        assert(is_null($targetKey) || $joinedTable->isColumn($targetKey),
            "Cannot join tables '" . $sourceTableName . "' and '" . $joinedTableName . "'. " .
            "Field '" . $targetKey . "' does not exist in table '" . $joinedTableName . "'.");
        // error - no such column in referenced table
        assert(is_null($foreignKey) || $sourceTable->isColumn($foreignKey),
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
        assert((bool) $sourceTable->isColumn($foreignKey), '$sourceTable->isColumn($foreignKey)');
        assert((bool) $joinedTable->isColumn($targetKey), '$joinedTable->isColumn($targetKey)');

        // create new join condition
        assert(!isset($joinType), 'Cannot redeclare variable $joinType');
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

            assert(!isset($foreignKeys), 'Cannot redeclare variable $foreignKeys');
            $foreignKeys = $sourceTable->getForeignKeys();

            if (empty($foreignKeys)) {
                return false;
            }
            assert(!isset($foreignTable), 'Cannot redeclare variable $foreignTable');
            assert(!isset($foreignKey), 'Cannot redeclare variable $foreignKey');
            assert(!isset($foreignPrimaryKey), 'Cannot redeclare variable $foreignPrimaryKey');
            assert(!isset($baseColumn), 'Cannot redeclare variable $baseColumn');
            assert(!isset($foreignColumn), 'Cannot redeclare variable $foreignColumn');
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
     * @return  $this
     */
    public function setTable(string $table)
    {
        assert(is_string($table), 'Wrong type for argument 1. String expected');
        $this->resetId();

        $tableName = mb_strtolower($table);
        $table = $this->getDatabase()->getSchema()->getTable($tableName);

        if (!($table instanceof \Yana\Db\Ddl\Table)) {
            $message = "The table '$tableName' is unknown.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException($message, $level);
        }

        // Auto-attach profile check to where clause if profile constraint is present.
        $this->_profile = array();
        if ($table->hasProfile()) {
            $builder = new \Yana\ApplicationBuilder();
            $application = $builder->buildApplication();
            $this->_profile = array('profile_id', '=', $application->getProfileId());
            unset($builder, $application);
        }

        // We expect the result to be a table.
        if ($this->expectedResult === \Yana\Db\ResultEnumeration::UNKNOWN) {
            $this->expectedResult = \Yana\Db\ResultEnumeration::TABLE;
        }

        // assign table name and definition
        $this->_tableName = $tableName;
        $this->_table = $table;

        /**
         * inheritance check
         *
         * Details: If one table inherits from another - that is if the primary
         * key is also a foreign key - then these are to be joined automatically.
         */
        if ($this->isUsingInheritance()) {
            $this->detectInheritance($this->_table);
        }
        return $this;
    }

    /**
     * Get the name of the currently selected table.
     *
     * Returns the name of the currently selected table.
     * If none has been selected yet, an empty string is returned.
     *
     * @return  string
     */
    public function getTable() : string
    {
        return $this->_tableName;
    }

    /**
     * Get current table.
     *
     * @return  \Yana\Db\Ddl\Table
     * @throws  \Yana\Db\Queries\Exceptions\TableNotSetException  if table has not been initialized
     */
    protected function currentTable(): \Yana\Db\Ddl\Table
    {
        if (!isset($this->_table)) {
            if (!$this->getTable()) {
                throw new \Yana\Db\Queries\Exceptions\TableNotSetException("Need to set table first!");
            }
            $this->_table = $this->getDatabase()->getSchema()->getTable($this->getTable());
        }
        return $this->_table;
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
     * @return  $this
     * @ignore
     */
    protected function setColumn(string $column = '*')
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
     * @return  $this
     * @ignore
     */
    protected function setColumnWithAlias($column = '*', $alias = "")
    {
        assert(is_string($column), 'Wrong type for argument 1. String expected');
        assert(is_string($alias), 'Wrong type for argument 2. String expected');
        $this->resetId();

        /**
         * 1) wrong order of commands, need to set up table first
         */
        if (empty($this->_tableName)) {
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
            if ($this->_row === '*') {
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
            assert(!isset($table), 'Cannot redeclare var $table');
            if (strpos($column, '.')) {
                list($table, $column) = explode('.', $column);
                $this->setTable($table);
            } else {
                $table = $this->getParentByColumn($column);
                if ($table > "") {
                    $this->setTable($table);
                }
            }
            unset($table);

            /*
             * 3.2) invalid argument, not a column
             */
            if (YANA_DB_STRICT && !$this->currentTable()->isColumn($column)) {
                throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException("The column '$column' is not found in table " .
                    "'{$this->_tableName}'.", \Yana\Log\TypeEnumeration::WARNING);
            }

            /*
             * 3.3) set column
             */
            assert(!isset($columnValue), 'Cannot redeclare var $columnValue');
            $this->column = array();
            $alias = $alias > "" ? (string) $alias : 0;
            $this->column[$alias] = array($this->_tableName, mb_strtolower($column));
            if ($this->_row !== '*' || $this->getExpectedResult() === \Yana\Db\ResultEnumeration::ROW) {
                $this->expectedResult = \Yana\Db\ResultEnumeration::CELL;
            } else {
                if (!$this->currentTable()->getColumn($column)->isPrimaryKey()) {
                    $this->column[] = array($this->_tableName, $this->_table->getPrimaryKey());
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
     * @return  $this
     * @ignore
     */
    protected function setArrayAddress(string $arrayAddress = "")
    {
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

        $this->_arrayAddress = "$arrayAddress";
        return $this;
    }

    /**
     * Returns the currently selected address as a string.
     *
     * If none has been selected yet, an empty string is returned.
     *
     * @return  string
     * @ignore
     */
    protected function getArrayAddress(): string
    {
        return $this->_arrayAddress;
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
     * @param   scalar  $i  index of column to get
     * @return  string
     * @ignore
     */
    protected function getColumn($i = null): string
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
    protected function getColumns(): array
    {
        $columns = array();
        if (is_array($this->column)) {
            $columns = $this->column;

        /*
         * catchable error: column is string (can be converted to array)
         */
        } elseif (is_string($this->column)) {
            $columns = array(array($this->_tableName, $this->column));

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
     * @return  $this
     */
    public function setRow($row)
    {
        assert(is_scalar($row), 'Wrong argument type for argument 1. Scalar expected.');
        $this->resetId();

        /*
         * 1) wrong order of commands, need to set up table first
         */
        if (empty($this->_tableName)) {
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
            $this->_rowId = array();
            /*
             * 2.2) set row
             */
            $this->_row = '*';
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
            $this->_rowId = array(array($this->_tableName, $table->getPrimaryKey()), '=', $row);
            /*
             * 3.2) set row
             */
            $this->_row = $row;
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
    public function getRow(): string
    {
        $row = '*';
        if (is_string($this->_row)) {
            $row = mb_strtolower($this->_row);
        }
        return (string) $row;
    }

    /**
     * Resolve key address to determine table, column and row.
     *
     * @param   string  $key  resolve key address to determine table, column and row
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if the given table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the given column is not found
     * @throws  \Yana\Db\Queries\Exceptions\InconsistencyException   when a foreign key check detects invalid database values
     * @throws  \Yana\Db\Queries\Exceptions\TargetNotFoundException  when no target can be found for the given key
     * @return  $this
     */
    public function setKey(string $key)
    {
        assert((bool) preg_match("/^[\w\d\-_]+(\.(\w[^\.]*|\*|\?)){0,}(\.\*)?$/i", $key), "Syntax error. The key '{$key}' is not valid.");

        $key = preg_replace("/\.(\*)?$/", '', $key);
        $array = explode(".", $key);
        assert(!empty($array), 'Invalid argument $key');
        $dbSchema = $this->getDatabase()->getSchema();

        // get table definition
        assert(!isset($table), 'cannot redeclare variable $table');
        $table = $dbSchema->getTable($array[0]);
        if (!$table instanceof \Yana\Db\Ddl\Table) {
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
            assert(!isset($column), 'cannot redeclare variable $column');
            $column = $table->getColumn($array[2]);
            if (! $column instanceof \Yana\Db\Ddl\Column) {
                $message = "Column not found '{$array[2]}'";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException($message, $level);
            }
            assert(!isset($isArray), 'cannot redeclare variable $isArray');
            $isArray = ($column->getType() === 'array');

            assert(!isset($a), 'cannot redeclare variable $a');
            assert(!isset($foreignTable), 'cannot redeclare variable $foreignTable');
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
                assert($table instanceof \Yana\Db\Ddl\Table, 'Table not found');
                $column = $table->getColumn($array[2]);
                assert($column instanceof \Yana\Db\Ddl\Column, 'Column not found');
                $isArray = ($column->getType() === 'array');
            }
            unset($a, $foreignTable, $column);
            if ($isArray) {
                $this->_arrayAddress = implode('.', array_slice($array, 3));
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
                // We call setOrderBy() and not addOrderBy() because we order by primary key - ergo any other column to order by would have no effect
                $this->setOrderBy(array($table->getPrimaryKey($array[0])), array(true)); // order by primary key in descending direction
                $this->setLimit(1); // return only the top row
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
     * @return  $this
     * @ignore
     */
    protected function addOrderBy(string $column, bool $desc = false)
    {
        /* get base table */
        if (strpos($column, '.')) {
            list($tableName, $column) = explode('.', $column);
        } else {
            $tableName = $this->_tableName;
        }
        $table = $this->getDatabase()->getSchema()->getTable($tableName);
        if (!($table instanceof \Yana\Db\Ddl\Table)) {
            $message = "No such table '" . $tableName . "'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException($message, $level);
        }

        /* check if column exists */
        if (!$table->isColumn($column)) {
            $message = "Column '$column' not found in table '" . $tableName . "'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException($message, $level);
        }
        $this->_orderBy[] = array($tableName, mb_strtolower($column));
        $this->_desc[] = $desc;
        return $this;
    }

    /**
     * Set column to sort the resultset by.
     *
     * @param   array  $orderBy  list of column names
     * @param   array  $desc     list of sort order (true=desc, false=asc)
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   when the base table does not exist
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when the column does not exist
     * @return  $this
     * @ignore
     */
    protected function setOrderBy(array $orderBy, array $desc = array())
    {
        $this->resetId();
        $this->_orderBy = array();
        $this->_desc = array();

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
    protected function getOrderBy(): array
    {
        return $this->_orderBy;
    }

    /**
     * Check if resultset is sorted in descending order.
     *
     * Returns an array of boolean values: true = descending, false = ascending.
     *
     * @return  array
     * @ignore
     */
    protected function getDescending(): array
    {
        return $this->_desc;
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
        $operator = strtolower((string) $where[1]);
        $rightOperand = $where[2];

        /**
         * 1) is sub-clause
         */
        switch ($operator)
        {
            case \Yana\Db\Queries\OperatorEnumeration::AND:
            case \Yana\Db\Queries\OperatorEnumeration::OR:
                return array($this->parseWhereArray($leftOperand, true), $operator, $this->parseWhereArray($rightOperand, true));
        }

        /*
         * 2) is singular clause
         */

        /**
         * 2.1) handle left operator (must be column name)
         */
        if (is_array($leftOperand) && count($leftOperand) === 2) {
            $tableName = mb_strtolower((string) array_shift($leftOperand));
            $column = mb_strtolower((string) array_shift($leftOperand));

        } elseif (is_string($leftOperand)) {
            $tableName = $this->_tableName;
            $column = mb_strtolower((string) $leftOperand);

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
            assert(is_string($column), 'Unexpected result: $column. String expected.');

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
            $isTableScan = ($this->_row === '*' || is_null($this->_row) || $this->_row === '?');
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
            case $operator === \Yana\Db\Queries\OperatorEnumeration::IN || $operator === \Yana\Db\Queries\OperatorEnumeration::NOT_IN:
                assert(is_array($rightOperand) || $rightOperand instanceof \Yana\Db\Queries\Select, 'is_array($rightOperand) || $rightOperand instanceof \Yana\Db\Queries\Select');
            break;

            // is sub-query
            case $operator === \Yana\Db\Queries\OperatorEnumeration::EXISTS || $operator === \Yana\Db\Queries\OperatorEnumeration::NOT_EXISTS:
                assert($rightOperand instanceof \Yana\Db\Queries\SelectExist, '$rightOperand instanceof \Yana\Db\Queries\SelectExist');
            break;

            // is column name
            case is_array($rightOperand) && count($rightOperand) === 2:

                $tableName = mb_strtolower((string) array_shift($rightOperand));
                $column = mb_strtolower((string) array_shift($rightOperand));

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
            case \Yana\Db\Queries\OperatorEnumeration::EQUAL:
                $operator = \Yana\Db\Queries\OperatorEnumeration::EQUAL;
            break;
            case '<>':
            case \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL:
                $operator = \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL;
            break;
            case \Yana\Db\Queries\OperatorEnumeration::EXISTS:
            case \Yana\Db\Queries\OperatorEnumeration::NOT_EXISTS:
                if (!($rightOperand instanceof \Yana\Db\Queries\SelectExist)) {
                    $message = "Invalid where clause.\n\t\t" .
                        "The operator '{$operator}' requires the right operand " .
                        "to be an instance of \Yana\Db\Queries\SelectExist.";
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
                }
            break;
            case \Yana\Db\Queries\OperatorEnumeration::IN:
            case \Yana\Db\Queries\OperatorEnumeration::NOT_IN:
                if (!is_array($rightOperand)) {
                    if (!($rightOperand instanceof \Yana\Db\Queries\Select)) {
                        $message = "Invalid where clause.\n\t\t" .
                            "The operator '{$operator}' requires the right operand to be an array.";
                        $level = \Yana\Log\TypeEnumeration::WARNING;
                        throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
                    }
                }
            break;
            case \Yana\Db\Queries\OperatorEnumeration::LIKE:
            case \Yana\Db\Queries\OperatorEnumeration::REGEX:
            case \Yana\Db\Queries\OperatorEnumeration::LESS:
            case \Yana\Db\Queries\OperatorEnumeration::GREATER:
            case \Yana\Db\Queries\OperatorEnumeration::LESS_OR_EQUAL:
            case \Yana\Db\Queries\OperatorEnumeration::GREATER_OR_EQUAL:
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
        if (!$dontOptimize && empty($this->_rowId) && $operator == \Yana\Db\Queries\OperatorEnumeration::EQUAL && is_string($rightOperand)) {
            $primaryKey = $this->currentTable()->getPrimaryKey();
            switch (true)
            {
                case is_array($leftOperand)  && strcasecmp($primaryKey, (string) $leftOperand[1]) === 0:
                case !is_array($leftOperand) && strcasecmp($primaryKey, (string) $leftOperand) === 0:
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
     * @return  $this
     */
    protected function setWhere(array $where = array())
    {
        // clear cached query id
        $this->resetId();

        $this->_where = $this->parseWhereArray($where); // throws exception
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
     * @return  $this
     */
    protected function addWhere(array $where)
    {
        if (!empty($where)) {
            if (!empty($this->_where)) {
                // clear cached query id
                $this->resetId();
                $this->_where = array($this->parseWhereArray($where), \Yana\Db\Queries\OperatorEnumeration::AND, $this->_where);
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
    protected function getWhere(): array
    {
        if (!is_array($this->_where)) {
            return array();
        }
        $where = $this->_where;
        // automatically add profile constraint
        if (!empty($this->_profile)) {
            if (empty($where)) {
                $where = $this->_profile;
            } else {
                $where = array($this->_profile, \Yana\Db\Queries\OperatorEnumeration::AND, $where);
            }
        }
        // automatically add primary key selector
        if (!empty($this->_rowId)) {
            if (empty($where)) {
                $where = $this->_rowId;
            } else {
                $where = array($this->_rowId, \Yana\Db\Queries\OperatorEnumeration::AND, $where);
            }
        }
        if ($this->type === \Yana\Db\Queries\TypeEnumeration::EXISTS && !empty($this->column)) {
            assert(!isset($column), 'Cannot redeclare var $column');
            foreach ($this->getColumns() as $column)
            {
                if (empty($where)) {
                    $where = array($column, \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL, null);
                } else {
                    $where = array(
                        array($column, \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL, null), \Yana\Db\Queries\OperatorEnumeration::AND, $where
                    );
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
    public function getLimit(): int
    {
        return $this->_limit;
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
     * @return  $this
     */
    protected function setLimit(int $limit)
    {
        $this->resetId();
        if ($limit < 0) {
            $message = "Limit must not be negative: '$limit'";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $this->_limit = $limit;
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
    public function getOffset(): int
    {
        return $this->_offset;
    }

    /**
     * Set an offset for this query.
     *
     * Note: This setting will not be part of the sql statement
     * produced by __toString(). Use the API's $limit and
     * $offset parameter instead when sending the query.
     *
     * This restriction does not apply if you use sendQuery().
     *
     * @param   int  $offset  offset for this query
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when offset is not positive
     * @return  $this
     */
    protected function setOffset(int $offset)
    {
        $this->resetId();
        if ($offset < 0) {
            $message = "Offset must not be negative: '$offset'";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $this->_offset = $offset;
        return $this;
    }

    /**
     * Get unique id.
     *
     * @return  string
     * @ignore
     */
    public function toId(): string
    {
        if (!isset($this->_id)) {
            $this->_id = serialize(array($this->type, $this->_tableName, $this->column, $this->_row,
            $this->_where, $this->_orderBy, $this->having, $this->_desc, $this->joins, $this->_offset, $this->_limit, $this->values));
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
    protected function getOldValues(): array
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
    public function sendQuery(): \Yana\Db\IsResult
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
     * @return  $this
     * @ignore
     */
    protected function deleteFiles(array $files = array())
    {
        // abort if there is nothing to do
        if (empty($files)) {
            return $this;
        }
        $values = $this->getOldValues();
        if (empty($values)) {
            return $this;
        }
        // iterate over list of file-columns
        foreach ($files as $file)
        {
            if ($file instanceof \Yana\Http\Uploads\File) {
                continue;
            }
            $column = $file->getTargetColumn();
            if (!$column instanceof \Yana\Db\Ddl\Column) {
                continue;
            }
            $columnName = mb_strtoupper($column->getName());
            // delete old files
            if (isset($values[$columnName]) && $values[$columnName] > "") {
                assert(is_string($values[$columnName]), 'is_string($values[$columnName])');

                try {
                    \Yana\Db\Binaries\File::removeFile($values[$columnName]);

                } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                    // @codeCoverageIgnoreStart

                    // Create a database event log entry for each file that was not found.
                    assert(!isset($message), 'Cannot redeclare var $message');
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
     * @return  string
     */
    abstract protected function toString(): string;

}

?>