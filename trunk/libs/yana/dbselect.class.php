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
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DbSelect extends DbSelectCount
{
    /**#@+
     * @access  protected
     * @ignore
     */

    /** @var int   */ protected $type = DbQueryTypeEnumeration::SELECT;

    /** @var array */ protected $having = array();
    /** @var int   */ protected $offset = 0;

    /**#@-*/

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
     * @access  public
     * @since   2.9.4
     */
    public function resetQuery()
    {
        parent::resetQuery();
        $this->having = array();
        $this->offset = 0;
    }

    /**
     * set source columns
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
     * @access  public
     * @param   array  $columns  list of columns
     * @since   2.9.6
     * @name    DbQuery::setColumns()
     * @see     DbQuery::setColumn()
     * @throws  DbEventLog                if table has not been initialized
     * @throws  InvalidArgumentException  if a given argument is invalid
     * @throws  NotFoundException         if the given table or column is not found
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
                if ($this->expectedResult !== DbResultEnumeration::ROW) {
                    $this->expectedResult = DbResultEnumeration::TABLE;
                }
            } else {
                $this->expectedResult = DbResultEnumeration::ROW;
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
            throw new DbEventLog("Cannot set columns - need to set table first!");
        }

        $result = array();
        assert('!isset($column); // Cannot redeclare var $column');
        assert('!isset($alias); // Cannot redeclare var $alias');
        foreach ($columns as $alias => $column)
        {
            $alias = mb_strtoupper($alias);
            $result[$alias] = $this->_getColumnArray($column);
        }
        $this->column = $result;

        if ($this->row === '*') {
            $this->expectedResult = DbResultEnumeration::TABLE;
        } else {
            $this->expectedResult = DbResultEnumeration::ROW;
        }
    }

    /**
     * add source columns
     *
     * This adds an item to the list of columns to retrieve, like in
     * SELECT col1, col2, ... colN FROM ...
     *
     * The column gets appended, not overwritten.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @param   string  $column  column name
     * @param   scalar  $alias   optional column alias
     * @name    DbQuery::setColumns()
     * @see     DbQuery::setColumn()
     * @throws  DbEventLog                if table has not been initialized
     * @throws  InvalidArgumentException  if a given argument is invalid
     * @throws  NotFoundException         if the given table or column is not found
     */
    public function addColumn($column, $alias = "")
    {
        assert('is_string($column); // Wrong argument type argument 1. String expected');

        // reset query id
        $this->id = null;

        $columnDefinition = $this->_getColumnArray($column);

        // add value
        if (empty($alias)) {
            $this->column[] = $columnDefinition;
        } else {
            $this->column[$alias] = $columnDefinition;
        }

        if ($this->row === '*') {
            $this->expectedResult = DbResultEnumeration::TABLE;
        } else {
            $this->expectedResult = DbResultEnumeration::ROW;
        }
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
     * @access  private
     * @param   strinng  $column  column name
     * @return  array
     * @throws  NotFoundException  if the table or column was not found
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
                throw new NotFoundException("Table not found '" . $tableName . "'.",
                    E_USER_WARNING);

            }
            if (!$dbSchema->getTable($tableName)->isColumn($column)) {
                throw new NotFoundException("Column '$column' not found in table " .
                    "'$tableName'.", E_USER_WARNING);
            }
        }
        return array($tableName, mb_strtolower($column));
    }

    /**
     * get the currently selected array address
     *
     * Returns the currently address as a string.
     * If none has been selected yet, an empty string is returned.
     *
     * @access  public
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
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @param   array  $orderBy  list of column names
     * @param   array  $desc     sort descending (true=yes, false=no)
     * @throws  NotFoundException  when a column or table does not exist
     */
    public function setOrderBy($orderBy, $desc = array())
    {
        parent::setOrderBy($orderBy, $desc);
    }

    /**
     * get the list of columns the resultset is ordered by
     *
     * Returns a lower-cased list of column names.
     * If none has been set yet, then the list is empty.
     *
     * @access  public
     * @return  array
     */
    public function getOrderBy()
    {
        return parent::getOrderBy();
    }

    /**
     * check if resultset is sorted in descending order
     *
     * Returns an array of boolean values: true = descending, false = ascending.
     *
     * @access  public
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
     * @access  public
     * @param   array  $having  having clause
     * @throws  NotFoundException         when a column is not found
     * @throws  InvalidArgumentException  when the having-clause contains invalid values
     */
    public function setHaving(array $having = array())
    {
        $this->id = null; // clear cached query id

        if (empty($having)) {
            $this->having = array();
        } else {
            $this->having = $this->parseWhereArray($having);
        }
    }

    /**
     * add having clause (filter)
     *
     * The syntax is as follows:
     * array(0=>column,1=>value,2=>operator)
     * Where "operator" can be one of the following:
     * '=', 'REGEXP', 'LIKE', '<', '>', '!=', '<=', '>='
     *
     * @access  public
     * @param   array  $having       having clause
     * @param   bool   $isMandatory  switch between operators (true='AND', false='OR')
     * @throws  NotFoundException         when a column is not found
     * @throws  InvalidArgumentException  when the having-clause contains invalid values
     */
    public function addHaving(array $having, $isMandatory = true)
    {
        assert('is_bool($isMandatory); // Wrong type for argument 2. Boolean expected');
        // clear cached query id
        $this->id = null;
        $having = $this->parseWhereArray($having);
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
    }

    /**
     * get the currently set having clause
     *
     * Returns the current having clause.
     *
     * @access  public
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
     * produced by toString().
     * Use the API's $limit and $offset parameter instead when sending
     * the query.
     *
     * This restriction does not apply if you use sendQuery().
     *
     * @access  public
     * @param   int  $limit  limit for this query
     * @throws  InvalidArgumentException  when limit is not positive
     */
    public function setLimit($limit)
    {
        parent::setLimit($limit);
    }

    /**
     * set an offset for this query
     *
     * Note: This setting will not be part of the sql statement
     * produced by {link DbQuery::toString()}. Use the API's $limit and
     * $offset parameter instead when sending the query.
     *
     * This restriction does not apply if you use {link DbQuery::sendQuery()}.
     *
     * Note: For security reasons all delete queries will automatically
     * have an offset of 0.
     *
     * @access  public
     * @param   int  $offset  offset for this query
     * @return  bool
     * @since   2.9.3
     */
    public function setOffset($offset)
    {
        assert('is_int($offset); // Wrong argument type for argument 1. Integer expected.');
        $this->id = null;
        if (is_int($offset) && $offset >= 0) {
            $this->offset = $offset;
            return true;
        } else {
            return false;
        }
    }

    /**
     * build a SQL-query
     *
     * @access  public
     * @param   string $stmt sql statement
     * @return  string
     */
    public function toString($stmt = "SELECT %COLUMN% FROM %TABLE% %WHERE% %HAVING% %ORDERBY%")
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
     * get results as CSV
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
     * @access  public
     * @param   string  $colSep       column seperator
     * @param   string  $rowSep       row seperator
     * @param   bool    $hasHeader    add column names as first line (yes/no)
     * @return  string
     * @name    DbSelect::toCSV()
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
            case DbResultEnumeration::CELL:
                // create header
                if ($hasHeader) {
                    $header = $this->getColumnTitles();
                    $csv .= $this->_rowToCsv($header, $colSep, $rowSep);
                }
                // create body
                return $csv . $this->_valueToCSV($resultset) . "$rowSep";
            break;
            // handle rows
            case DbResultEnumeration::ROW:
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
            break;
            // handle tables and columns
            case DbResultEnumeration::COLUMN:
            case DbResultEnumeration::TABLE:
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
            break;
            default:
                /* Query is incomplete or invalid.
                 * This may occur if no table is selected.
                 */
                $message = "Unable to create CSV string. Your query is invalid.";
                throw new InvalidValueException($message, E_USER_WARNING);
            break;
        }
    }

    /**
     * get CSV for single row
     *
     * The function returns the CSV contents as a single-line string.
     *
     * @access  private
     * @param   array   $row          row data
     * @param   string  $colSep       column seperator
     * @param   string  $rowSep       row seperator
     * @return  string
     * @see     DbSelect::toCSV()
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
     * get CSV for single value
     *
     * The function returns an escaped string for the given value.
     *
     * @access  private
     * @param   mixed   $value        data
     * @return  string
     * @see     DbSelect::toCSV()
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
     * @access  public
     * @return  array
     * @see     DbSelect::toCSV()
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
     * set left join for two tables
     *
     * This will join the currently selected table with another (by using a left join).
     *
     * If $key1 is not provided, the function will automatically search for
     * a suitable foreign key, that refers to $tableName.
     * If $key2 is not provided, the function will automatically look up
     * the primary key of $tableName and use it instead.
     *
     * @access  public
     * @param   string $tableName  name of another table to join the current table with
     * @param   string $key1       name of the foreign key in current table
     *                             (when omitted the API will look up the key in the structure file)
     * @param   string $key2       name of the key in foreign table that is referenced
     *                             (may be omitted if it is the primary key)
     * @throws  NotFoundException  if a provided table or column is not found
     */
    public function setLeftJoin($tableName, $key1 = null, $key2 = null)
    {
        parent::setJoin($tableName, $key1, $key2, true);
    }

    /**
     * parse SQL query into query object
     *
     * This is the opposite of toString().
     * It takes a SQL query string as input and returns
     * a query object of the specific type that
     * corresponds to the given type of query.
     *
     * The result object is always a subclass of DbQuery.
     *
     * @access  public
     * @static
     * @param   string    $sqlStmt   SQL statement
     * @param   DbStream  $database  database connection
     * @return  DbSelect
     * @throws  InvalidArgumentException  if the query is invalid or could not be parsed
     * @throws  ParserError               when the SQL statement is invalid
     */
    public static function parseSQL($sqlStmt, DbStream $database)
    {
        // this is a parser/lexer, that parses a given SQL string into an AST
        if (!is_array($sqlStmt)) {
            assert('is_string($sqlStmt); // Wrong argument type for argument 1. String expected.');
            $parser = new \SQL_Parser();
            $sqlStmt = $parser->parse($sqlStmt); // get abstract syntax tree (AST)
        }

        $tables = $sqlStmt['tables']; // array of table names
        $column = $sqlStmt['columns']; // array of column names
        $where = @$sqlStmt['where_clause']; // array of left operand, operator, right operand
        $having = @$sqlStmt['having_clause']; // array of left operand, operator, right operand
        $orderBy = @$sqlStmt['sort_order']; // list of columns (keys) and asc/desc (value)

        if (empty($tables)) {
            return false;
        }

        /*
         * 1) set table
         */
        $query = new self($database);
        $query->setTable(current($tables));
        $query->setColumns($column);
        $query->setWhere($query->parseWhere($where));

        /*
         * Resolve natural join to inner joins by automatically finding appropriate keys.
         */
        if (!empty($sqlStmt['table_join'])) {
            assert('!isset($i); // Cannot redeclare variable $i');
            assert('!isset($join); // Cannot redeclare variable $join');
            $dbSchema = $database->getSchema();
            $i = 0;
            foreach ($sqlStmt['table_join'] as $join)
            {
                $i++;
                $tableNameA = $tables[$i - 1];
                $tableNameB = $tables[$i];
                // switch by type of join
                switch ($join)
                {
                    case 'natural join':
                        $tableA = $dbSchema->getTable($tableNameA);
                        $tableB = $dbSchema->getTable($tableNameB);
                        // error: table not found
                        if (! $tableA instanceof DDLTable) {
                            throw new NotFoundException("Table '{$tableNameA}' not found.");
                        }
                        if (! $tableB instanceof DDLTable) {
                            throw new NotFoundException("Table '{$tableNameB}' not found.");
                        }
                        assert('!isset($columnsB); // Cannot redeclare variable $columnsB');
                        $columnsB = $tableB->getColumnNames();
                        assert('!isset($colA); // Cannot redeclare variable $colA');
                        foreach ($tableA->getColumnNames() as $columnA)
                        {
                            if (in_array($columnA, $columnsB)) {
                                $query->addWhere(array($tableNameA, $columnA), '=', array($tableNameB, $columnB));
                            }
                        } // end foreach
                        unset($tableNameA, $tableA, $columnsB, $tableNameB, $tableB, $columnA);
                    break;
                    case 'right join':
                    case 'right outer join':
                        // flip operands: $tableA <-> $tableB
                        assert('!isset($_); // Cannot redeclare var $_');
                        // $tableNameA <-> $tableNameB
                        $_ = $tableNameA;
                        $tableNameA = $tableNameB;
                        $tableNameB = $_;
                        // $tableA <-> $tableB
                        $_ = $tableA;
                        $tableA = $tableB;
                        $tableB = $_;
                        unset($_);
                        // fall through
                    case 'left join':
                    case 'left outer join':
                        $isLeftJoin = true;
                        // fall through
                    default:
                        $success = $query->parseJoin($tableNameA, $tableNameB, $query->where, empty($isLeftJoin));
                        if (!$success) {
                            $message = "SQL error: accidental cross-join detected in statement '{$sqlStmt}'." .
                                "\n\t\tThe statement has been ignored.";
                            throw new InvalidArgumentException($message, E_USER_NOTICE);
                        }
                        continue;
                    break;
                }
            } // end foreach
            unset($i, $join);
        } // end if

        /*
         * 3) set order by and direction
         */
        if (!empty($orderBy)) {
            assert('!isset($columnName); // Cannot redeclare variable $columnName');
            assert('!isset($direction); // Cannot redeclare variable $direction');
            foreach ($orderBy as $columnName => $direction)
            {
                $query->addOrderBy($columnName, $direction == 'desc');
            }
            unset($columnName, $direction);
        }

        /*
         * 5) set having clause
         */
        if (!empty($having)) {
            $query->setHaving($query->parseWhere($having));
        }

        return $query;
    }

    /**
     * try to auto-detect joined tables and on-clause
     *
     * This function tries to find an association between two tables in the given
     * where clause. If one is found, it completes and auto-adds a join-clause.
     * Returns bool(true) on success and bool(false) on failure.
     *
     * @access  protected
     * @param   string  $leftTable   name of base table
     * @param   string  $rightTable  name of table to join
     * @param   array   $where       where clause (will be scanned)
     * @param   bool    $isLeftJoin  treat as left join (currently no full outer joins supported)
     * @return  bool
     * @ignore
     */
    protected function parseJoin($leftTable, $rightTable, array $where, $isLeftJoin = false)
    {
        assert('is_string($leftTable); // Wrong argument type for argument 1. String expected.');
        assert('is_string($rightTable); // Wrong argument type for argument 2. String expected.');
        assert('is_bool($isLeftJoin); // Wrong argument type for argument 4. String expected.');
        if (empty($where)) {
            return false; // not found
        }
        $leftOperand = $where[0];
        $operator = strtolower($where[1]);
        $rightOperand = $where[2];
        switch ($operator)
        {
            case 'and':
            case 'or':
                return $this->parseJoin($leftTable, $rightTable, $leftOperand, $isLeftJoin) ||
                    $this->parseJoin($leftTable, $rightTable, $rightOperand, $isLeftJoin);
            break;
        }

        /* if is join-clause */
        if (is_array($leftOperand) && is_array($rightOperand)) {
            $tableA  = $leftOperand[0];
            $columnA = $leftOperand[1];
            $tableB  = $rightOperand[0];
            $columnB = $rightOperand[1];

            // flip operands
            $param1 = strcasecmp($tableA, $leftTable);
            $param2 = strcasecmp($tableB, $leftTable);
            if ($param1 !== 0 && $param2 === 0) {
                $tableB  = $leftOperand[0];
                $columnB = $leftOperand[1];
                $tableA  = $rightOperand[0];
                $columnA = $rightOperand[1];

            } elseif ($param1 !== 0) { // ignore when there is nothing to join
                return false; // not found
            }
            unset ($param1, $param2);
            /* At this point we know, that leftOperand = tableA.
             * We now check if rightOperand = tableB.
             */
            if (strcasecmp($tableB, $rightTable) !== 0) {
                return false; // not found
            }
            // tableA is already joined - now join tableB!
            try {
                if ($isLeftJoin) {
                    $this->setLeftJoin($tableB, $columnA, $columnB);
                } else {
                    $this->setInnerJoin($tableB, $columnA, $columnB);
                }
                return true; // found
            } catch (NotFoundException $e) {
                $message = "Unable to join tables '$tableA' and '$tableB'. Cause: " . $e->getMessage();
                throw new InvalidArgumentException($message, E_USER_WARNING);
            }
            unset($tables[$tableB], $where[$i]);
        }
        return false; // not found
    }

    /**
     * get the number of entries
     *
     * This sends the query statement to the database and returns the results.
     * The return type depends on the query settings, see {@see DbQuery::getExpectedResult()}.
     *
     * @access  public
     * @return  int
     */
    public function countResults()
    {
        switch ($this->expectedResult)
        {
            case DbResultEnumeration::ROW:
            case DbResultEnumeration::CELL:
                return (int) $this->doesExist();
            break;
            case DbResultEnumeration::TABLE:
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
     * @access  public
     * @return  mixed
     * @throws  InvalidArgumentException   when one of the given arguments is not valid
     */
    public function getResults()
    {
        $result = $this->sendQuery();

        if ($this->db->isError($result)) {
            Log::report("Statement '$this' on database failed", E_USER_WARNING, $result);
            return null;
        }
        $returnedType = $this->getExpectedResult();
        $table = $this->db->getSchema()->getTable($this->getTable());
        assert('$table instanceof DDLTable;');

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

                Log::report("Returned data for statement '$this' must be an array. " .
                    "Instead database returned the following value '{$row}'. " .
                    "The result was considered to be an error.");
                break;

            }
            switch ($returnedType)
            {
                case DbResultEnumeration::TABLE:
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
                case $returnedType === DbResultEnumeration::COLUMN:
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
                        case DbResultEnumeration::TABLE:
                        case DbResultEnumeration::ROW:
                            // get name of parent table (if any)
                            try {
                                assert('!isset($currentTable); // Cannot redeclare var $currentTable');
                                $currentTable = $this->getTableByColumn($columnName);
                                assert('$currentTable instanceof DDLTable;');
                                // get column definition
                                $column = $currentTable->getColumn($columnName);
                                unset($currentTable);
                            } catch (NotFoundException $e) {
                                $message = "Your database has a column named '$columnName', " .
                                    "which should not exist according to your database schema file. " .
                                    "The unexpected column will be ignored.";
                                Log::report($message, E_USER_NOTICE, $e->getMessage());
                                continue;
                            }
                        break;
                        case DbResultEnumeration::CELL:
                            $arrayAddress = $this->getArrayAddress();
                            if (empty($arrayAddress)) {
                                $arrayAddress = '';
                            }
                        // fall through
                        case DbResultEnumeration::COLUMN:
                            $column = $table->getColumn($this->getColumn());
                        break;
                        default:
                            throw new InvalidArgumentException("Syntax error. " .
                                "The input '{$key}' is not a valid key address.");
                        break;
                    } // end switch
                    // decode cell
                    assert('$column instanceof DDLColumn;');
                    $value = $column->interpretValue($value, $arrayAddress, $this->db->getDBMS());
                    unset($arrayAddress);
                }
                // handle results
                switch ($returnedType)
                {
                    case DbResultEnumeration::TABLE:
                        $refKey[mb_strtoupper($alias)] = $value;
                    break;
                    case DbResultEnumeration::ROW:
                        $output[mb_strtoupper($alias)] = $value;
                    break;
                    case DbResultEnumeration::CELL:
                        $output = $value;
                    break;
                    case DbResultEnumeration::COLUMN:
                        $output[$rowId] = $value;
                    break;
                    default:
                        throw new InvalidArgumentException("Syntax error. " .
                            "The input '{$key}' is not a valid key address.");
                    break;
                } // end switch
            } // end foreach (column)
            unset($columnName, $alias, $value, $refKey, $currentTable, $column);
        } // end foreach (row)
        return $output;
    }
}

?>