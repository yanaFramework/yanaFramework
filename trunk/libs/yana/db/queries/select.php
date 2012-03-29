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
 */
class Select extends \Yana\Db\Queries\SelectCount
{
    /**#@+
     * @ignore
     */

    /**
     * @var int
     */
    protected $type = \Yana\Db\Queries\TypeEnumeration::SELECT;

    /**
     * @var array
     */
    protected $having = array();

    /**
     * @var int
     */
    protected $offset = 0;

    /** #@- */

    /**
     * reset query
     *
     * Resets all properties of the query object, except
     * for the database connection and the properties
     * "table", "type", "useInheritance".
     *
     * This function allows you to "recycle" a query object
     * and reuse it without creating another one. This can
     * help to improve the performance of your application.
     *
     * @return  \Yana\Db\Queries\Select 
     */
    public function resetQuery()
    {
        parent::resetQuery();
        $this->having = array();
        $this->offset = 0;
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
     * @return  \Yana\Db\Queries\Select 
     */
    public function setColumns(array $columns = array())
    {
        $this->id = null;

        /*
         * 1) select all columns
         */
        if (empty($columns)) {

            $this->column = array();
            if ($this->row === '*') {
                if ($this->expectedResult !== \Yana\Db\ResultEnumeration::ROW) {
                    $this->expectedResult = \Yana\Db\ResultEnumeration::TABLE;
                }
            } else {
                $this->expectedResult = \Yana\Db\ResultEnumeration::ROW;
            }
            return;
        }

        /*
         * 2) single column
         */
        if (count($columns) === 1) {
            $this->setColumn(array_pop($columns));
            return;

        }

        /*
         * 3) select specific columns
         */

        // error - wrong order of commands, need to set up table first
        if (empty($this->tableName)) {
            throw new \Yana\Db\Queries\Exceptions\InvalidSyntaxException("Cannot set columns - need to set table first!");
        }

        $result = array();
        assert('!isset($column); // Cannot redeclare var $column');
        assert('!isset($alias); // Cannot redeclare var $alias');
        foreach ($columns as $alias => $column)
        {
            $alias = mb_strtoupper($alias);
            $result[$alias] = $this->_getColumnArray($column); // throws exception
        }
        $this->column = $result;

        if ($this->row === '*') {
            $this->expectedResult = \Yana\Db\ResultEnumeration::TABLE;
        } else {
            $this->expectedResult = \Yana\Db\ResultEnumeration::ROW;
        }
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
     * @param   scalar  $alias   optional column alias
     * @name    DbQuery::setColumns()
     * @see     DbQuery::setColumn()
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if a given argument is invalid
     * @throws  \Yana\Core\Exceptions\NotFoundException         if the given table or column is not found
     * @return  \Yana\Db\Queries\Select 
     */
    public function addColumn($column, $alias = "")
    {
        assert('is_string($column); // Wrong argument type argument 1. String expected');

        // reset query id
        $this->id = null;

        $columnDefinition = $this->_getColumnArray($column); // throws exception

        // add value
        if (empty($alias)) {
            $this->column[] = $columnDefinition;
        } else {
            $this->column[$alias] = $columnDefinition;
        }

        if ($this->row === '*') {
            $this->expectedResult = \Yana\Db\ResultEnumeration::TABLE;
        } else {
            $this->expectedResult = \Yana\Db\ResultEnumeration::ROW;
        }
        return $this;
    }

    /**
     * get column array
     *
     * This takes a column name like "table.column" or just "column", checks validity and
     * returns in both cases: array("table", "column").
     *
     * When the table name is missing, it is determined automatically, unless the column name
     * is ambigious.
     *
     * @param   strinng  $column  column name
     * @return  array
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if the table was not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the column was not found
     */
    private function _getColumnArray($column)
    {
        if (is_array($column)) {
            return $column;
        }
        assert('is_string($column); // Wrong argument type argument 1. String expected');
        if (strpos($column, '.')) {
            list($tableName, $column) = explode('.', $column);
        } else {
            $tableName = $this->getParentByColumn($column);
            if ($tableName === false) {
                $tableName = $this->tableName;
            }
        }

        // check if column definition is valid
        if (YANA_DB_STRICT) {
            $dbSchema = $this->db->getSchema();
            if (!$dbSchema->isTable($tableName)) {
                throw new \Yana\Db\Queries\Exceptions\TableNotFoundException("Table not found '" . $tableName . "'.",
                    E_USER_WARNING);

            }
            if (!$dbSchema->getTable($tableName)->isColumn($column)) {
                throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException("Column '$column' not found in table " .
                    "'$tableName'.", E_USER_WARNING);
            }
        }
        return array($tableName, mb_strtolower($column));
    }

    /**
     * Returns the currently address as a string.
     *
     * If none has been selected yet, an empty string is returned.
     *
     * @return  string
     */
    public function getArrayAddress()
    {
        assert('is_string($this->arrayAddress);');
        return $this->arrayAddress;
    }

    /**
     * set column to sort the resultset by
     *
     * @param   array  $orderBy  list of column names
     * @param   array  $desc     sort descending (true=yes, false=no)
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException  when the base table does not exist
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException when the column does not exist
     * @return  \Yana\Db\Queries\Select
     */
    public function setOrderBy($orderBy, $desc = array())
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
    public function getOrderBy()
    {
        return parent::getOrderBy();
    }

    /**
     * Returns an array of boolean values: true = descending, false = ascending.
     *
     * @return  array
     */
    public function getDescending()
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
     * @return  \Yana\Db\Queries\Select
     */
    public function setHaving(array $having = array())
    {
        $this->id = null; // clear cached query id

        if (empty($having)) {
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
     * @return  \Yana\Db\Queries\Select
     */
    public function addHaving(array $having, $isMandatory = true)
    {
        assert('is_bool($isMandatory); // Wrong type for argument 2. Boolean expected');
        // clear cached query id
        $this->id = null;
        $having = $this->parseWhereArray($having); // throws exception
        if ($isMandatory) {
            $operator = 'and';
        } else {
            $operator = 'or';
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
    public function getHaving()
    {
        assert('is_array($this->having);');
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
     * @return  \Yana\Db\Queries\Select
     */
    public function setLimit($limit)
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
     * This restriction does not apply if you use {link DbQuery::sendQuery()}.
     *
     * Note: For security reasons all delete queries will automatically
     * have an offset of 0.
     *
     * @param   int  $offset  offset for this query
     * @return  \Yana\Db\Queries\Select
     */
    public function setOffset($offset)
    {
        assert('is_int($offset); // Wrong argument type for argument 1. Integer expected.');
        $this->id = null;
        if ($offset >= 0) {
            $this->offset = (int) $offset;
        }
        return $this;
    }

    /**
     * Build a SQL-query.
     *
     * @param   string $stmt sql statement
     * @return  string
     */
    protected function toString($stmt = "SELECT %COLUMN% FROM %TABLE% %WHERE% %HAVING% %ORDERBY%")
    {
        /* replace %HAVING% */
        if (strpos($stmt, '%HAVING%') !== false) {
            $having = $this->getHaving();

            if (is_array($having) && count($having) > 0) {
                $having = $this->convertWhereToString($having);
                if (!empty($having)) {
                    $having = 'HAVING ' . $having;
                }
            } else {
                $having = "";
            }
            assert('is_string($having); // Unexpected value $having');
            if (!empty($having)) {
                $stmt = str_replace('%HAVING%', trim($having), $stmt);
            } else {
                $stmt = str_replace(' %HAVING%', '', $stmt);
            }
            unset($having);
        }

        return parent::toString($stmt);

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
     * @return  string
     * @name    \Yana\Db\Queries\Select::toCSV()
     * @throws  \Yana\Core\Exceptions\InvalidValueException  if the database query is incomplete or invalid
     */
    public function toCSV($colSep = ';', $rowSep = "\n", $hasHeader = true)
    {
        assert('is_string($colSep); // Wrong argument type for argument 1. String expected.');
        assert('is_string($rowSep); // Wrong argument type for argument 2. String expected.');
        assert('is_bool($hasHeader); // Wrong argument type for argument 4. Boolean expected.');
        $csv = "";
        $resultset = $this->getResults(); // array of data
        switch ($this->getExpectedResult())
        {
            // handle cells
            case \Yana\Db\ResultEnumeration::CELL:
                // create header
                if ($hasHeader) {
                    $header = $this->getColumnTitles();
                    $csv .= $this->_rowToCsv($header, $colSep, $rowSep);
                }
                // create body
                return $csv . $this->_valueToCSV($resultset) . "$rowSep";
            // handle rows
            case \Yana\Db\ResultEnumeration::ROW:
                // create header
                if ($hasHeader) {
                    $header = $this->getColumnTitles();
                    if (empty($header)) {
                        foreach (array_keys($resultset) as $title)
                        {
                            $header[] = $title;
                        }
                    }
                    $csv .= self::_rowToCsv($header, $colSep, $rowSep);
                }
                // create body
                return $csv . self::_rowToCsv($resultset, $colSep, $rowSep);
            // handle tables and columns
            case \Yana\Db\ResultEnumeration::COLUMN:
            case \Yana\Db\ResultEnumeration::TABLE:
                // create header
                if ($hasHeader) {
                    $header = $this->getColumnTitles();
                    if (empty($header) && !empty($resultset)) {
                        foreach (array_keys(current($resultset)) as $title)
                        {
                            $header[] = $title;
                        }
                    }
                    $csv .= self::_rowToCsv($header, $colSep, $rowSep);
                }
                // create body
                foreach ($resultset as $row)
                {
                    if (is_array($row)) {
                        $csv .= self::_rowToCsv($row, $colSep, $rowSep);
                    } else {
                        $csv .= self::_valueToCSV($row);
                    }
                }
                return $csv;
            default:
                /* Query is incomplete or invalid.
                 * This may occur if no table is selected.
                 */
                $message = "Unable to create CSV string. Your query is invalid.";
                throw new \Yana\Core\Exceptions\InvalidValueException($message, E_USER_WARNING);
        }
    }

    /**
     * Returns the CSV contents as a single-line string.
     *
     * @param   array   $row          row data
     * @param   string  $colSep       column seperator
     * @param   string  $rowSep       row seperator
     * @return  string
     * @see     \Yana\Db\Queries\Select::toCSV()
     */
    private static function _rowToCsv(array $row, $colSep, $rowSep)
    {
        assert('is_string($colSep); // Wrong argument type for argument 2. String expected.');
        assert('is_string($rowSep); // Wrong argument type for argument 3. String expected.');
        $csv = "";
        foreach ($row as $value)
        {
            if (!empty($csv)) {
                $csv .= "$colSep";
            }
            $csv .= self::_valueToCSV($value);
        }
        return $csv . "$rowSep";
    }

    /**
     * Returns an escaped string for the given value.
     *
     * @param   mixed   $value        data
     * @return  string
     * @see     \Yana\Db\Queries\Select::toCSV()
     */
    private static function _valueToCSV($value)
    {
        return '"' . str_replace('"', '""', print_r($value, true)) . '"';
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
    public function getColumnTitles()
    {
        $dbSchema = $this->db->getSchema();
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
     * Joins the currently selected table with another (by using a left join).
     *
     * If $key1 is not provided, the function will automatically search for
     * a suitable foreign key, that refers to $tableName.
     * If $key2 is not provided, the function will automatically look up
     * the primary key of $tableName and use it instead.
     *
     * @param   string $tableName  name of another table to join the current table with
     * @param   string $key1       name of the foreign key in current table
     *                             (when omitted the API will look up the key in the structure file)
     * @param   string $key2       name of the key in foreign table that is referenced
     *                             (may be omitted if it is the primary key)
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   if a provided table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if a provided column is not found
     * @return  \Yana\Db\Queries\Select
     */
    public function setLeftJoin($tableName, $key1 = null, $key2 = null)
    {
        parent::setJoin($tableName, $key1, $key2, true);
        return $this;
    }

    /**
     * get the number of entries
     *
     * This sends the query statement to the database and returns the results.
     * The return type depends on the query settings, see {@see DbQuery::getExpectedResult()}.
     *
     * @return  int
     */
    public function countResults()
    {
        switch ($this->expectedResult)
        {
            case \Yana\Db\ResultEnumeration::ROW:
            case \Yana\Db\ResultEnumeration::CELL:
                return (int) $this->doesExist();
            break;
            case \Yana\Db\ResultEnumeration::TABLE:
                return count($this->getResults());
            break;
            default:
                return 0;
            break;
        }
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
        $result = $this->sendQuery();

        if ($this->db->isError($result)) {
            \Yana\Log\LogManager::getLogger()->addLog("Statement '$this' on database failed", E_USER_WARNING, $result);
            return null;
        }
        $returnedType = $this->getExpectedResult();
        $table = $this->db->getSchema()->getTable($this->getTable());
        assert('$table instanceof \Yana\Db\Ddl\Table;');

        assert('!isset($output); // Cannot redeclare var $output');
        $output = array();
        $id = $table->getPrimaryKey();

        if (!defined('MDB2_FETCHMODE_ASSOC')) {
            /** @ignore */
            define('MDB2_FETCHMODE_ASSOC', 2);
        }

        for ($i = 0; $i < $result->numRows(); $i++)
        {
            $row = $result->fetchRow(MDB2_FETCHMODE_ASSOC, $i);
            // Error: unexpected result
            if (!is_array($row)) {

                \Yana\Log\LogManager::getLogger()->addLog("Returned data for statement '$this' must be an array. " .
                    "Instead database returned the following value '{$row}'. " .
                    "The result was considered to be an error.");
                break;

            }
            switch ($returnedType)
            {
                case \Yana\Db\ResultEnumeration::TABLE:
                    assert('!isset($rowId); // Cannot redeclare var $rowId');
                    if (isset($row[$id])) {
                        $rowId = mb_strtoupper($row[$id]);
                    } else {
                        $rowId = $i;
                    }
                    assert('!isset($refKey); /* Cannot redeclare var $refKey */');
                    if (!empty($output[$rowId])) {
                        $output[$rowId] = array($output[$rowId]);
                        $output[$rowId][1] = false;
                        $refKey =& $output[$rowId][1];
                    } else {
                        $refKey =& $output[$rowId];
                    }
                    unset($rowId);
                break;
                case $returnedType === \Yana\Db\ResultEnumeration::COLUMN:
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
            assert('!isset($alias); // Cannot redeclare var $alias');
            assert('!isset($columnName); // Cannot redeclare var $columnName');
            assert('!isset($value); // Cannot redeclare var $value');
            assert('!isset($column); // Cannot redeclare var $column');
            foreach ($row as $alias => $value)
            {
                if (!is_null($value)) {
                    $columnName = $this->getColumnByAlias($alias);
                    assert('!isset($arrayAddress); // Cannot redeclare var $arrayAddress');
                    $arrayAddress = '';
                    // check input
                    switch ($returnedType)
                    {
                        case \Yana\Db\ResultEnumeration::TABLE:
                        case \Yana\Db\ResultEnumeration::ROW:
                            // get name of parent table (if any)
                            try {
                                assert('!isset($currentTable); // Cannot redeclare var $currentTable');
                                $currentTable = $this->getTableByColumn($columnName);
                                assert('$currentTable instanceof \Yana\Db\Ddl\Table;');
                                // get column definition
                                $column = $currentTable->getColumn($columnName);
                                unset($currentTable);
                            } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                                $message = "Your database has a column named '$columnName', " .
                                    "which should not exist according to your database schema file. " .
                                    "The unexpected column will be ignored.";
                                \Yana\Log\LogManager::getLogger()->addLog($message, E_USER_NOTICE, $e->getMessage());
                                continue;
                            }
                        break;
                        case \Yana\Db\ResultEnumeration::CELL:
                            $arrayAddress = $this->getArrayAddress();
                            if (empty($arrayAddress)) {
                                $arrayAddress = '';
                            }
                        // fall through
                        case \Yana\Db\ResultEnumeration::COLUMN:
                            $column = $table->getColumn($this->getColumn());
                        break;
                        default:
                            throw new \Yana\Core\Exceptions\InvalidArgumentException("Syntax error. " .
                                "The input '{$key}' is not a valid key address.");
                        break;
                    } // end switch
                    // decode cell
                    assert('$column instanceof \Yana\Db\Ddl\Column;');
                    $value = $column->interpretValue($value, $arrayAddress, $this->db->getDBMS());
                    unset($columnName, $arrayAddress, $currentTable, $column);
                }
                // handle results
                switch ($returnedType)
                {
                    case \Yana\Db\ResultEnumeration::TABLE:
                        $refKey[mb_strtoupper($alias)] = $value;
                    break;
                    case \Yana\Db\ResultEnumeration::ROW:
                        $output[mb_strtoupper($alias)] = $value;
                    break;
                    case \Yana\Db\ResultEnumeration::CELL:
                        $output = $value;
                    break;
                    case \Yana\Db\ResultEnumeration::COLUMN:
                        $output[$rowId] = $value;
                    break;
                    default:
                        throw new \Yana\Core\Exceptions\InvalidArgumentException("Syntax error. " .
                            "The input '{$key}' is not a valid key address.");
                    break;
                } // end switch
            } // end foreach (column)
            unset($alias, $value, $refKey);
        } // end foreach (row)
        return $output;
    }

}

?>