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
 */
class Select extends \Yana\Db\Queries\SelectCount implements \Yana\Db\Queries\IsSelectQuery
{

    /**
     * @var int
     * @ignore
     */
    protected $type = \Yana\Db\Queries\TypeEnumeration::SELECT;

    /**
     * @var array
     * @ignore
     */
    protected $having = array();

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
     * @return  $this 
     */
    public function resetQuery()
    {
        parent::resetQuery();
        $this->having = array();
        return $this;
    }

    /**
     * Set source columns.
     *
     * This sets the list of columns to retrieve, like in
     * SELECT col1, col2, ... colN FROM ...
     *
     * Note that this applies only to select statements,
     * not insert, update or delete.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * Note that, depending on the number of columns you wish to
     * retrieve, the datatype of the result may differ.
     *
     * Getting 1 column from 1 row will just return the
     * value of that cell, e.g. int(1). Getting multiple columns
     * from 1 row will return an array containing the values,
     * e.g. array('col1'=>1, 'col2'=>2, 'col3'=>3).
     *
     * Getting 1 column from multiple rows will return an
     * one-dimensional array of these values.
     * Getting multiple columns from multiple rows will
     * return a two-dimensional array of rows, where each
     * row is an associative array containing the values
     * of the selected columns.
     *
     * Examples:
     * <code>
     * // select 1 column
     * $dbq->setColumns(array('foo'));
     * // same as:
     * $dbq->setColumn('foo');
     *
     * // select multiple columns
     * $dbq->setColumns(array('foo1', 'foo2'));
     *
     * // select multiple columns from different tables
     * // 1) join with table2
     * $dbq->setInnerJoin('table2');
     * // 2) select columns from current table and table2
     * $dbq->setColumns(array('foo1', 'table2.foo2'));
     * </code>
     *
     * @param   array  $columns  list of columns
     * @since   2.9.6
     * @name    DbQuery::setColumns()
     * @see     DbQuery::setColumn()
     * @throws  \Yana\Db\Queries\Exceptions\InvalidSyntaxException   if table has not been initialized
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if the base table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the given column is not found
     * @return  $this 
     */
    public function setColumns(array $columns = array())
    {
        $this->resetId();

        /*
         * 1) select all columns
         */
        if (empty($columns)) {

            $this->column = array();
            if ($this->getRow() !== '*') {
                $this->expectedResult = \Yana\Db\ResultEnumeration::ROW;

            } elseif ($this->expectedResult !== \Yana\Db\ResultEnumeration::ROW) {
                $this->expectedResult = \Yana\Db\ResultEnumeration::TABLE;
            }
            return $this;
        }

        /*
         * 2) single column
         */
        if (count($columns) === 1) {
            $column = array_pop($columns);
            assert(is_string($column), 'String expected');
            $this->setColumn($column);
            return $this;

        }

        /*
         * 3) select specific columns
         */

        // error - wrong order of commands, need to set up table first
        if ($this->getTable() === "") {
            throw new \Yana\Db\Queries\Exceptions\InvalidSyntaxException("Cannot set columns - need to set table first!");
        }

        $result = array();
        assert(!isset($column), 'Cannot redeclare var $column');
        assert(!isset($alias), 'Cannot redeclare var $alias');
        foreach ($columns as $alias => $column)
        {
            $alias = mb_strtoupper((string) $alias);
            $result[$alias] = $this->_getColumnArray($column); // throws exception
        }
        $this->column = $result;

        $this->expectedResult = ($this->getRow() === '*') ? \Yana\Db\ResultEnumeration::TABLE : \Yana\Db\ResultEnumeration::ROW;
        return $this;
    }

    /**
     * Add source columns.
     *
     * This adds an item to the list of columns to retrieve, like in
     * SELECT col1, col2, ... colN FROM ...
     *
     * The column gets appended, not overwritten.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $column  column name
     * @param   string  $alias   optional column alias
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if a given argument is invalid
     * @throws  \Yana\Core\Exceptions\NotFoundException         if the given table or column is not found
     * @return  $this 
     */
    public function addColumn(string $column, string $alias = "")
    {
        // reset query id
        $this->resetId();

        $columnDefinition = $this->_getColumnArray($column); // throws exception

        // add value
        if (empty($alias)) {
            $this->column[] = $columnDefinition;
        } else {
            $this->column[$alias] = $columnDefinition;
        }

        $this->expectedResult = ($this->getRow() === '*') ? \Yana\Db\ResultEnumeration::TABLE : \Yana\Db\ResultEnumeration::ROW;
        return $this;
    }

    /**
     * Get column array.
     *
     * This takes a column name like "table.column" or just "column", checks validity and
     * returns in both cases: array("table", "column").
     *
     * If the input already is an array, it just returns the array as is.
     *
     * When the table name is missing, it is determined automatically, unless the column name
     * is ambigious.
     *
     * @param   string|array  $column  column name
     * @return  array
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if the table was not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the column was not found
     */
    private function _getColumnArray($column): array
    {
        if (is_array($column)) {
            return $column;
        }
        assert(is_string($column), 'Wrong argument type argument 1. String expected');
        if (strpos($column, '.')) {
            list($tableName, $column) = explode('.', $column);
        } else {
            $tableName = $this->getParentByColumn($column);
            if (!$tableName) {
                $tableName = $this->getTable();
            }
        }

        // check if column definition is valid
        if (YANA_DB_STRICT) {
            $dbSchema = $this->getDatabase()->getSchema();
            if (!$dbSchema->isTable($tableName)) {
                throw new \Yana\Db\Queries\Exceptions\TableNotFoundException("Table not found '" . $tableName . "'.", \Yana\Log\TypeEnumeration::WARNING);

            }
            if (!$dbSchema->getTable($tableName)->isColumn($column)) {
                throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException("Column '$column' not found in table " .
                    "'$tableName'.", \Yana\Log\TypeEnumeration::WARNING);
            }
        }
        return array($tableName, mb_strtolower($column));
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
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if a given argument is invalid
     * @return  $this
     */
    public function setArrayAddress(string $arrayAddress = "")
    {
        parent::setArrayAddress($arrayAddress);
        return $this;
    }

    /**
     * Returns the currently selected address as a string.
     *
     * If none has been selected yet, an empty string is returned.
     *
     * @return  string
     */
    public function getArrayAddress(): string
    {
        return parent::getArrayAddress();
    }

    /**
     * Add column to "order by"-clause.
     *
     * @param   string  $column  column name
     * @param   bool    $desc    sort descending (true=yes, false=no)
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   when the base table does not exist
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when the column does not exist
     * @return  $this
     */
    public function addOrderBy(string $column, bool $desc = false)
    {
        parent::addOrderBy($column, $desc);
        return $this;
    }

    /**
     * Set column to sort the resultset by.
     *
     * @param   array  $orderBy  list of column names
     * @param   array  $desc     sort descending (true=yes, false=no)
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException  when the base table does not exist
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException when the column does not exist
     * @return  $this
     */
    public function setOrderBy(array $orderBy, array $desc = array())
    {
        parent::setOrderBy($orderBy, $desc);
        return $this;
    }

    /**
     * get the list of columns the resultset is ordered by
     *
     * Returns a lower-cased list of column names.
     * If none has been set yet, then the list is empty.
     *
     * @return  array
     */
    public function getOrderBy(): array
    {
        return parent::getOrderBy();
    }

    /**
     * Returns an array of boolean values: true = descending, false = ascending.
     *
     * @return  array
     */
    public function getDescending(): array
    {
        return parent::getDescending();
    }

    /**
     * set having clause (filter)
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
     * To unset the having clause, call this function without
     * providing a parameter.
     *
     * The having clause uses the same syntax as the where clause.
     * The difference between 'where' and 'having' is that 'where' is executed during the execution
     * of the statement, while 'having' is executed on the result set.
     *
     * Thus 'having' may not access any columns which are not present in the result set, while
     * the 'where' clause may not access any column that is only present in the result set but not
     * in the table.
     *
     * If you have a choice you should always prefer 'where' over 'having', as using a where clause
     * usually is faster and consums less memory than using the same having clause.
     *
     * @param   array  $having  having clause
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   when a referenced table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when a referenced column is not found
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       when the having-clause contains invalid values
     * @return  $this
     */
    public function setHaving(array $having = array())
    {
        $this->resetId(); // clear cached query id

        if (count($having) === 0) {
            $this->having = array();
        } else {
            $this->having = $this->parseWhereArray($having); // throws exception
        }
        return $this;
    }

    /**
     * add having clause (filter)
     *
     * The syntax is as follows:
     * array(0=>column,1=>value,2=>operator)
     * Where "operator" can be one of the following:
     * '=', 'REGEXP', 'LIKE', '<', '>', '!=', '<=', '>='
     *
     * @param   array  $having       having clause
     * @param   bool   $isMandatory  switch between operators (true='AND', false='OR')
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   when a referenced table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when a referenced column is not found
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       when the having-clause contains invalid values
     * @return  $this
     */
    public function addHaving(array $having, bool $isMandatory = true)
    {
        // clear cached query id
        $this->resetId();
        $having = $this->parseWhereArray($having); // throws exception
        if ($isMandatory) {
            $operator = \Yana\Db\Queries\OperatorEnumeration::AND;
        } else {
            $operator = \Yana\Db\Queries\OperatorEnumeration::OR;
        }
        if (empty($this->having)) {
            $this->having = $having;
        } else {
            $this->having = array(
                $this->having,
                $operator,
                $having
            );
        }
        return $this;
    }

    /**
     * Returns the current having clause.
     *
     * @return  array
     */
    public function getHaving(): array
    {
        assert(is_array($this->having), 'is_array($this->having)');
        return $this->having;
    }

    /**
     * set a limit for this query
     *
     * Note: This setting will not be part of the sql statement
     * produced by __toString().
     * Use the API's $limit and $offset parameter instead when sending
     * the query.
     *
     * This restriction does not apply if you use sendQuery().
     *
     * @param   int  $limit  limit for this query
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when limit is not positive
     * @return  $this
     */
    public function setLimit(int $limit)
    {
        parent::setLimit($limit); // throws exception
        return $this;
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
    public function setOffset(int $offset)
    {
        return parent::setOffset($offset);
    }

    /**
     * Build a SQL-query.
     *
     * @return  string
     */
    protected function toString(): string
    {
        $serializer = new \Yana\Db\Queries\QuerySerializer();
        return $serializer->fromSelectQuery($this);
    }

    /**
     * Get results as CSV.
     *
     * This exports the data as a comma-seperated list of values.
     *
     * You may choose custom column and row delimiters by setting the parameters
     * to appropriate values.
     *
     * The first line always contains a header of column titles. To exclude this
     * information from the result, set $hasHeader to bool(false).
     *
     * Example output:
     * <pre>
     * "Name","Forename","Title","Name of Book"
     * "Smith","Steven, M.","Mr.","The ""Cookbook"" of Time"
     * "Higgings","Barbara","Ms.","A multiline guide
     * to the Galaxy"
     * </pre>
     *
     * The function returns the CSV contents as a multi-line string.
     * Note that the delimiters must be ASCII characters.
     *
     * The CSV format is defined in {@link http://www.rfc-editor.org/rfc/rfc4180.txt RFC 4180}.
     *
     * @param   string  $colSep       column seperator
     * @param   string  $rowSep       row seperator
     * @param   bool    $hasHeader    add column names as first line (yes/no)
     * @param   string  $stringDelim  any character that isn't the row or column seperator
     * @return  string
     * @name    \Yana\Db\Queries\Select::toCSV()
     * @throws  \Yana\Core\Exceptions\InvalidValueException  if the database query is incomplete or invalid
     */
    public function toCSV(string $colSep = ';', string $rowSep = "\n", bool $hasHeader = true, string $stringDelim = '"'): string
    {
        $csvGenerator = new \Yana\Util\Csv();
        $csvGenerator
            ->setColumnDelimiter($colSep)
            ->setRowDelimiter($rowSep)
            ->setHeader($hasHeader)
            ->setStringDelimiter($stringDelim);

        assert(!isset($header), 'Cannot redeclare var $header');
        $header = $this->getColumnTitles();

        switch ($this->getExpectedResult())
        {
            // handle cells
            case \Yana\Db\ResultEnumeration::CELL:
                return $csvGenerator->convertCellToCSV((string) $this->getResults(), (string) current($header));
            // handle rows
            case \Yana\Db\ResultEnumeration::ROW:
                return $csvGenerator->convertRowToCSV($this->getResults(), $header);
            // handle tables and columns
            case \Yana\Db\ResultEnumeration::COLUMN:
            case \Yana\Db\ResultEnumeration::TABLE:
                return $csvGenerator->convertTableToCSV($this->getResults(), $header);
            default:
                /* Query is incomplete or invalid.
                 * This may occur if no table is selected.
                 */
                $message = "Unable to create CSV string. Your query is invalid.";
                throw new \Yana\Core\Exceptions\InvalidValueException($message, E_USER_WARNING);
        }
    }

    /**
     * get list of column titles
     *
     * Returns the title attributes of all selected columns as a numeric
     * array.
     *
     * @return  array
     * @see     \Yana\Db\Queries\Select::toCSV()
     */
    public function getColumnTitles(): array
    {
        $dbSchema = $this->getDatabase()->getSchema();
        $titles = array(); // array of column titles
        foreach ($this->getColumns() as $column)
        {
            $column = $dbSchema->getTable($column[0])->getColumn($column[1]);
            $title = $column->getTitle();
            if (empty($title)) {
                $title = $column->getName();
            }
            $titles[] = $title;
        }
        return $titles;
    }

    /**
     * Joins two tables (by using a left join).
     *
     * If the target key is not provided, the function will automatically search for
     * a suitable foreign key in the source table, that refers to the foreign table.
     * If target  is not provided, the function will automatically look up
     * the primary key of $tableName and use it instead.
     *
     * @param   string $joinedTableName  name of the foreign table to join the source table with
     * @param   string $targetKey        name of the key in foreign table that is referenced
     *                                   (may be omitted if it is the primary key)
     * @param   string $sourceTableName  name of the source table
     * @param   string $foreignKey       name of the foreign key in source table
     *                                   (when omitted the API will look up the key in the schema file)
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if a provided table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if a provided column is not found
     * @return  $this
     */
    public function setLeftJoin($joinedTableName, $targetKey = null, $sourceTableName = null, $foreignKey = null)
    {
        parent::setJoin($joinedTableName, $targetKey, $sourceTableName, $foreignKey, true);
        return $this;
    }

    /**
     * Joins two tables (by using a natural join).
     *
     * This starts a natural join to all tables left of the join condition.
     *
     * In case of natural joins the query builder is by design meant to resolve and rewrite the natural join to an inner join and
     * create the necessary where clause automatically. The inner join behaves like a natural join.
     *
     * There is a good reason for this behavior:
     * To allow the database to resolve natural joins would create a (even though probably hard to exploit) security vulnerability in our software.
     *
     * Because, our query builder is given its own database schema.
     * This schema may exclude certain columns the software/user is not supposed to see.
     * If, however, we let the database deal with the natural join on its own, it may (since it is ignorant of these client-side "views" of its schema)
     * include the hidden columns in the natural join regardless.
     * With a bit of effort this would possibly enable a remote attacker to "guess" the contents of a hidden column.
     * Since we are aware of this potential issue (and since it's always better to be safe than sorry) we avoid this problem by rewriting
     * all natural joins based on the client's view of the database schema.
     *
     * @param   string $joinedTableName  name of the foreign table to join the source table with
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if the provided table is not found
     * @return  $this
     */
    public function setNaturalJoin($joinedTableName)
    {
        assert(!isset($schema), 'Cannot redeclare variable $schema');
        $schema = $this->getDatabase()->getSchema();
        assert(!isset($joinedTable), 'Cannot redeclare variable $joinedTable');
        $joinedTable = $schema->getTable($joinedTableName);
        // error: table not found
        if (! $joinedTable instanceof \Yana\Db\Ddl\Table) {
            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException("Table '" . $joinedTableName . "' not found.");
        }
        assert(!isset($columns), 'Cannot redeclare variable $columns');
        $columns = $joinedTable->getColumnNames();
        assert(!isset($foundAtLeastOneMatch), 'Cannot redeclare variable $foundAtLeastOneMatch');
        $foundAtLeastOneMatch = false;

        assert(!isset($tableName), 'Cannot redeclare variable $tableName');
        assert(!isset($table), 'Cannot redeclare variable $table');
        foreach ($this->getTables() as $tableName)
        {
            assert(!isset($columnName), 'Cannot redeclare variable $columnName');
            foreach ($schema->getTable($tableName)->getColumnNames() as $columnName)
            {
                if (in_array($columnName, $columns)) {
                    $foundAtLeastOneMatch = true;
                    $this->addWhere(array(array($tableName, $columnName), '=', array($joinedTableName, $columnName)));
                }
            }
            unset($columnName);
        } // end foreach
        unset($tableName, $columns);

        if (!$foundAtLeastOneMatch) {
            throw new \Yana\Db\Queries\Exceptions\ConstraintException(
                "Cannot join table '" . $joinedTableName . "'. " .
                "No matching column has been found."
            );
        }

        $this->addJoinCondition(new \Yana\Db\Queries\JoinCondition($joinedTableName, "", "", "", \Yana\Db\Queries\JoinTypeEnumeration::INNER_JOIN));
        return $this;
    }

    /**
     * Get the number of entries.
     *
     * This sends the query statement to the database and returns how many rows the result set would have.
     *
     * @return  int
     */
    public function countResults(): int
    {
        return $this->sendQuery()->countRows();
    }

    /**
     * get values from the database
     *
     * This sends the query statement to the database and returns the results.
     * The return type depends on the query settings, see {@see DbQuery::getExpectedResult()}.
     *
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when one of the given arguments is not valid
     */
    public function getResults()
    {
        $returnedType = $this->getExpectedResult();
        if ($returnedType === \Yana\Db\ResultEnumeration::UNKNOWN) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Syntax error. The input is not a valid key address.");
        }
        $result = $this->sendQuery();

        assert(!isset($output), 'Cannot redeclare var $output');
        $output = array();

        for ($i = 0; $i < $result->countRows(); $i++)
        {
            $row = $result->fetchRow($i);
            // Error: unexpected result
            if (!is_array($row)) {
                // @codeCoverageIgnoreStart

                /* This should be unreachable, unless there is either a bug in the database module,
                 * or somebody has been fiddling with the schema files, causing corrupted data.
                 */
                \Yana\Log\LogManager::getLogger()->addLog("Returned data for statement '$this' must be an array. " .
                    "Instead database returned the following value '{$row}'. " .
                    "The result was considered to be an error.");
                break;
                // @codeCoverageIgnoreEnd
            }
            switch ($returnedType)
            {
                case \Yana\Db\ResultEnumeration::TABLE:
                    $table = $this->getDatabase()->getSchema()->getTable($this->getTable());
                    assert($table instanceof \Yana\Db\Ddl\Table, '$table instanceof \Yana\Db\Ddl\Table');
                    $id = $table->getPrimaryKey();
                    assert(!isset($rowId), 'Cannot redeclare var $rowId');
                    $rowId = isset($row[$id]) ? mb_strtoupper((string) $row[$id]) : $i;
                    assert(!isset($refKey), 'Cannot redeclare var $refKey');
                    if (!empty($output[$rowId])) {
                        $output[$rowId] = array($output[$rowId]);
                        $output[$rowId][1] = false;
                        $refKey =& $output[$rowId][1];
                    } else {
                        $refKey =& $output[$rowId];
                    }
                    unset($rowId);
                break;
                case \Yana\Db\ResultEnumeration::COLUMN:
                    $table = $this->getDatabase()->getSchema()->getTable($this->getTable());
                    assert($table instanceof \Yana\Db\Ddl\Table, '$table instanceof \Yana\Db\Ddl\Table');
                    $id = $table->getPrimaryKey();
                    if (isset($row[$id])) {
                        $rowId = $row[$id];
                        if (count($row) > 1) {
                            unset($row[$id]);
                        }
                    } else {
                        $rowId = $i;
                    }
                break;
            }
            assert(!isset($alias), 'Cannot redeclare var $alias');
            assert(!isset($columnName), 'Cannot redeclare var $columnName');
            assert(!isset($value), 'Cannot redeclare var $value');
            assert(!isset($column), 'Cannot redeclare var $column');
            assert(!isset($arrayAddress), 'Cannot redeclare var $arrayAddress');
            foreach ($row as $alias => $value)
            {
                if (!is_null($value)) {
                    $columnName = $this->getColumnByAlias($alias);
                    $arrayAddress = '';
                    // check input
                    switch ($returnedType)
                    {
                        case \Yana\Db\ResultEnumeration::TABLE:
                        case \Yana\Db\ResultEnumeration::ROW:
                            // get name of parent table (if any)
                            try {
                                assert(!isset($currentTable), 'Cannot redeclare var $currentTable');
                                $currentTable = $this->getTableByColumn($columnName);
                                assert($currentTable instanceof \Yana\Db\Ddl\Table, '$currentTable instanceof \Yana\Db\Ddl\Table');
                                // get column definition
                                $column = $currentTable->getColumn($columnName);
                                unset($currentTable);
                            } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                                $message = "Your database has a column named '$columnName', " .
                                    "which should not exist according to your database schema file. " .
                                    "The unexpected column will be ignored.";
                                \Yana\Log\LogManager::getLogger()->addLog($message, E_USER_NOTICE, $e->getMessage());
                                continue 2;
                            }
                        break;
                        case \Yana\Db\ResultEnumeration::CELL:
                            $arrayAddress = $this->getArrayAddress();
                            if (empty($arrayAddress)) {
                                $arrayAddress = '';
                            }
                        // fall through
                        case \Yana\Db\ResultEnumeration::COLUMN:
                            $table = $this->getDatabase()->getSchema()->getTable($this->getTable());
                            assert($table instanceof \Yana\Db\Ddl\Table, '$table instanceof \Yana\Db\Ddl\Table');
                            $column = $table->getColumn($this->getColumn());
                        break;
                    } // end switch
                    // decode cell
                    assert($column instanceof \Yana\Db\Ddl\Column, '$column instanceof \Yana\Db\Ddl\Column');
                    $value = $column->interpretValue($value, $arrayAddress, $this->getDatabase()->getDBMS());
                    unset($columnName, $arrayAddress, $currentTable, $column);
                }
                // handle results
                switch ($returnedType)
                {
                    case \Yana\Db\ResultEnumeration::TABLE:
                        $refKey[mb_strtoupper((string) $alias)] = $value;
                    break;
                    case \Yana\Db\ResultEnumeration::ROW:
                        $output[mb_strtoupper((string) $alias)] = $value;
                    break;
                    case \Yana\Db\ResultEnumeration::CELL:
                        $output = $value;
                    break;
                    case \Yana\Db\ResultEnumeration::COLUMN:
                        $output[$rowId] = $value;
                    break;
                    default:
                        throw new \Yana\Core\Exceptions\InvalidArgumentException("Syntax error. " .
                            "The input is not a valid key address.");
                } // end switch
            } // end foreach (column)
            unset($alias, $value, $refKey);
        } // end foreach (row)
        return $output;
    }

}

?>