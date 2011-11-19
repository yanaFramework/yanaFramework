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

namespace Yana\Db\Queries\Parsers;

/**
 * Query-Parser.
 *
 * This class allows you to parse a SQL-Statement into a query object.
 *
 * Example:
 * <code>
 * $connection = \Yana::connect('my_database');
 * $parser = new Parser($connection);
 * $selectQuery = $parser->parseSQL("Select * from myTable where id = 1;");
 * </code>
 *
 * @package     yana
 * @subpackage  db
 */
class Parser extends \Yana\Core\Object
{

    /**
     * @var \DbStream 
     */
    private $_database = null;

    /**
     * Set up the database to build the queries upon.
     *
     * @param  \DbStream  $database 
     */
    public function __construct(\DbStream $database)
    {
        $this->_database = $database;
    }

    /**
     * @return \DbStream
     */
    protected function _getDatabase()
    {
        return $this->_database;
    }

    /**
     * parse SQL query into query object
     *
     * This is the opposite of __toString().
     * It takes a SQL query string as input and returns
     * a query object of the specific type that
     * corresponds to the given type of query.
     *
     * The result object is always a subclass of {@see \Yana\Db\Queries\AbstractQuery}.
     *
     * @param   string     $sqlStmt   SQL statement
     * @return  \Yana\Db\Queries\AbstractQuery
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     */
    public function parseSQL($sqlStmt)
    {
        assert('is_string($sqlStmt); // Wrong argument type argument 1. String expected');
        $sqlStmt = trim($sqlStmt);
        $parser = new \SQL_Parser();
        $syntaxTree = $parser->parse($sqlStmt); // get abstract syntax tree (AST)
        if (is_array($syntaxTree) && !empty($syntaxTree['command'])) {
            switch ($syntaxTree['command'])
            {
                case 'select':
                    switch (true)
                    {
                        case preg_match('/^select\s+1\s+/i', $sqlStmt):
                            return $this->_parseSelectExists($syntaxTree);
                        case preg_match('/^select\s+count\(/i', $sqlStmt):
                            return $this->_parseSelectCount($syntaxTree);
                        default:
                            return $this->_parseSelect($syntaxTree);
                    }
                    break;
                case 'insert':
                    return $this->_parseInsert($syntaxTree);
                case 'update':
                    return $this->_parseUpdate($syntaxTree);
                case 'delete':
                    return $this->_parseDelete($syntaxTree);
            }
        }
        throw new \Yana\Core\Exceptions\InvalidArgumentException("Invalid or unknown SQL statement: $sqlStmt.", E_USER_WARNING);
    }

    /**
     * Parse SQL query into query object.
     *
     * @param   array  $syntaxTree  SQL statement
     * @return  \Yana\Db\Queries\Delete
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     * @throws  \Yana\Db\Exceptions\TableNotFoundException      when the table does not exist
     * @throws  YanaDbQueriesExceptions$1        when one of the columns does not exist
     */
    protected function _parseDelete(array $syntaxTree)
    {
        $table = current($syntaxTree['tables']); // array of table names
        $where = @$syntaxTree['where_clause']; // array of left operand, operator, right operand
        $orderBy = @$syntaxTree['sort_order']; // list of columns (keys) and asc/desc (value)

        /*
         * 1) set table
         */
        $query = new \Yana\Db\Queries\Delete($this->_getDatabase());
        $query->setTable($table);

        /*
         * 2) set order by + direction
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
         * 3) where clause
         */
        if (!empty($where)) {
            $query->setWhere($this->_parseWhere($where));
        }
        return $query;
    }

    /**
     * Parse SQL query into query object.
     *
     * @param   array  $syntaxTree  SQL statement
     * @return  \Yana\Db\Queries\Insert
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException    when the statement contains illegal values
     */
    protected function _parseInsert(array $syntaxTree)
    {
        $table = current($syntaxTree['tables']); // array of table names
        $keys = $syntaxTree['columns']; // array of column names
        $values = $syntaxTree['values']; // array of value settings
        $set = array(); // combined array of $keys and $values

        $query = new \Yana\Db\Queries\Insert($database);
        $query->setTable($table);

        // combine arrays of keys and values
        $set = $this->_parseSet($query, $keys, $values);
        if (empty($set)) {
            $message = 'SQL syntax error. The statement contains illegal values.';
            throw new \Yana\Core\Exceptions\InvalidSyntaxException($message);
        }
        unset($keys, $values);

        // set values
        $query->setValues($set);

        // check security constraint
        if ($query->getExpectedResult() !== \Yana\Db\ResultEnumeration::ROW) {
            if (!$query->table->getColumn($query->table->getPrimaryKey())->isAutoFill()) {
                $message = "SQL security restriction. Cannot insert a table (only rows).";
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
            }
        }
        return $query;
    }

    /**
     * combine a list of keys and values
     *
     * Returns the row-array on success.
     * On failure an empty array is returned.
     *
     * @param   \Yana\Db\Queries\Insert $query   Query object to modify
     * @param   array                   $keys    keys
     * @param   array                   $values  values
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given column does not exist
     * @ignore
     */
    private function _parseSet(\Yana\Db\Queries\AbstractQuery $query, array $keys, array $values)
    {
        assert('count($keys) == count($values);');
        // prepare values
        assert('!isset($value); // Cannot redeclare var $value');
        assert('!isset($i); // Cannot redeclare var $i');
        foreach ($values as $i => $value)
        {
            if (array_key_exists('value', $value)) {
                $values[$i] = $value['value'];
            }
        }
        unset($i, $value);
        // combine keys and values
        $set = array();
        $table = $this->_getDatabase()->getSchema()->getTable($this->getTable($query->getTable()));
        assert('!isset($column); // Cannot redeclare var $column');
        assert('!isset($i); // Cannot redeclare var $i');
        for ($i = 0; $i < count($keys); $i++)
        {
            $column = $table->getColumn($keys[$i]);
            if (! $column instanceof \Yana\Db\Ddl\Column) {
                throw new \Yana\Core\Exceptions\InvalidArgumentException("Column '" . $keys[$i] . "' does not exist " .
                    "in table '" . $query->getTable() . "'.", E_USER_WARNING);
            }
            if ($column->getType() === 'array') {
                $set[mb_strtoupper($keys[$i])] = json_decode($values[$i]);
            } else {
                $set[mb_strtoupper($keys[$i])] = $values[$i];
            }
        } // end foreach
        unset($i, $column);

        assert('is_array($set);');
        return $set;
    }

    /**
     * Parse SQL query into query object.
     *
     * @param   array  $syntaxTree  SQL statement
     * @return  \Yana\Db\Queries\Update
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     * @throws  YanaDbQueriesExceptions$1         when the query contains invalid syntax
     */
    protected function _parseUpdate(array $syntaxTree)
    {
        // security check: where clause must not be empty
        if (empty($syntaxTree['where_clause'])) {
            $message = "SQL security restriction. Cannot update a table (only rows and cells).";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }

        $table = current($syntaxTree['tables']); // array of table names
        $keys = $syntaxTree['columns']; // array of column names
        $values = $syntaxTree['values']; // array of value settings
        $where = $syntaxTree['where_clause']; // array of left operand, operator, right operand
        $set = array(); // combined array of $keys and $values

        $query = new \Yana\Db\Queries\Update($this->_getDatabase());
        $query->setTable($table);

        // combine arrays of keys and values
        $set = $this->_parseSet($query, $keys, $values);
        if (empty($set)) {
            $message = 'SQL syntax error. The statement contains illegal values.';
            throw new \Yana\Db\Queries\Exceptions\InvalidSyntaxException($message);
        }
        unset($keys, $values);

        $query->setWhere($this->_parseWhere($where));
        $expectedResult = $query->getExpectedResult();
        $query->setValues($set);

        // check security constraint
        if ($expectedResult !== \Yana\Db\ResultEnumeration::ROW && $expectedResult !== \Yana\Db\ResultEnumeration::CELL) {
            $message = "SQL security restriction. Cannot update a table (only rows and cells).";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
        return $query;
    }

    /**
     * Parse SQL query into query object.
     *
     * @param   array  $syntaxTree  SQL statement
     * @return  \Yana\Core\Exceptions\InvalidArgumentException
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     * @throws  YanaDbQueriesExceptions$1         when the query contains invalid syntax
     */
    protected function _parseSelectExists(array $syntaxTree)
    {
        $query = new \Yana\Db\Queries\SelectExists($database);

        // retrieve table
        $tables = $syntaxTree['tables'];
        if (empty($tables)) {
            $message = "SQL-statement has no table names: $syntaxTree.";
            return new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        } elseif (count($tables) > 1) {
            $message = "Checks for existence are not supported on joined tables.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
        $query->setTable(current($tables));

        // retrieve where clause
        if (!empty($syntaxTree['where_clause'])) {
            // array of left operand, operator, right operand
            $query->setWhere($this->_parseWhere($syntaxTree['where_clause']));
        }

        return $query;
    }

    /**
     * Parse SQL query into query object.
     *
     * @param   array  $syntaxTree  SQL statement
     * @return  \Yana\Db\Queries\SelectCount
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     * @throws  YanaDbQueriesExceptions$1         when the query contains invalid syntax
     */
    protected function _parseSelectCount(array $syntaxTree)
    {
        $query = new \Yana\Db\Queries\SelectCount($database);

        // retrieve table
        $tables = $syntaxTree['tables'];
        if (empty($tables)) {
            $message = "SQL-statement has no table names: $syntaxTree.";
            return new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        } elseif (count($tables) > 1) {
            $message = "Row-Counts are not supported on joined tables.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
        $query->setTable(current($tables));

        // retrieve column
        $function = current($syntaxTree['set_function']); // array of column names
        if ($function['name'] !== 'count') {
            $message = "Funktion 'count' expected for 'Select count(foo) ...'-statement. " .
                "Found '{$function['name']}' instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
        $column = current($function['arg']);
        if ($column != '*') {
            $query->setColumn($column);
        }

        // retrieve where clause
        if (!empty($syntaxTree['where_clause'])) {
            // array of left operand, operator, right operand
            $query->setWhere($this->_parseWhere($syntaxTree['where_clause']));
        }

        return $query;
    }

    /**
     * Parse SQL query into query object.
     *
     * @param   array  $syntaxTree  SQL statement
     * @return  DbSelect
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     * @throws  YanaDbQueriesExceptions$1         when the tables are not found
     * @throws  ParserError                                     when the SQL statement is invalid
     */
    protected function _parseSelect(array $syntaxTree)
    {
        $tables = $syntaxTree['tables']; // array of table names
        $column = $syntaxTree['columns']; // array of column names
        $where = @$syntaxTree['where_clause']; // array of left operand, operator, right operand
        $having = @$syntaxTree['having_clause']; // array of left operand, operator, right operand
        $orderBy = @$syntaxTree['sort_order']; // list of columns (keys) and asc/desc (value)

        if (empty($tables)) {
            $message = "SQL-statement has no table names: $syntaxTree.";
            return \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }

        /*
         * 1) set table
         */
        $query = new \Yana\Db\Queries\Select($database);
        $query->setTable(current($tables));
        $query->setColumns($column);
        $query->setWhere($this->_parseWhere($where));

        /*
         * Resolve natural join to inner joins by automatically finding appropriate keys.
         */
        if (!empty($syntaxTree['table_join'])) {
            assert('!isset($i); // Cannot redeclare variable $i');
            assert('!isset($join); // Cannot redeclare variable $join');
            $dbSchema = $database->getSchema();
            $i = 0;
            foreach ($syntaxTree['table_join'] as $join)
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
                        if (! $tableA instanceof \Yana\Db\Ddl\Table) {
                            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException("Table '{$tableNameA}' not found.");
                        }
                        if (! $tableB instanceof \Yana\Db\Ddl\Table) {
                            throw new \Yana\Db\Queries\Exceptions\TableNotFoundException("Table '{$tableNameB}' not found.");
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
                        $success = $this->_parseJoin(!$query, $tableNameA, $tableNameB, $query->where, empty($isLeftJoin));
                        if (!$success) {
                            $message = "SQL error: accidental cross-join detected in statement '{$query}'." .
                                "\n\t\tThe statement has been ignored.";
                            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_NOTICE);
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
            $query->setHaving($this->_parseWhere($having));
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
     * @param   \Yana\Db\Queries\Select  $query       query to modify
     * @param   string                   $leftTable   name of base table
     * @param   string                   $rightTable  name of table to join
     * @param   array                    $where       where clause (will be scanned)
     * @param   bool                     $isLeftJoin  treat as left join (currently no full outer joins supported)
     * @return  bool
     * @ignore
     */
    private function _parseJoin(\Yana\Db\Queries\Select $query, $leftTable, $rightTable, array $where, $isLeftJoin = false)
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
                return $this->_parseJoin($query, $leftTable, $rightTable, $leftOperand, $isLeftJoin) ||
                    $this->_parseJoin($query, $leftTable, $rightTable, $rightOperand, $isLeftJoin);
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
                    $query->setLeftJoin($tableB, $columnA, $columnB);
                } else {
                    $query->setInnerJoin($tableB, $columnA, $columnB);
                }
                return true; // found
            } catch (\Yana\Db\Queries\Exceptions\QueryException $e) {
                $message = "Unable to join tables '$tableA' and '$tableB'. Cause: " . $e->getMessage();
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
            }
        }
        return false; // not found
    }

    /**
     * Resolves the where clause and returns the parsed array.
     *
     * The syntax is as follows: ([column] [operator] [value]) ( AND (...))*
     *
     * @param   array  $syntaxTree  where clause
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given where-clause is invalid
     * @ignore
     */
    private function _parseWhere(array $syntaxTree)
    {
        if (empty($syntaxTree)) {
            return array(); // empty where clause
        }

        $leftOperand = $syntaxTree['arg_1'];
        $operator = $syntaxTree['op'];
        $rightOperand = $syntaxTree['arg_2'];
        $negate = !empty($syntaxTree['neg']);
        switch ($operator)
        {
            case 'and':
            case 'or':
                $leftOperand = $this->_parseWhere($leftOperand);
                $rightOperand = $this->_parseWhere($rightOperand);
                return array($leftOperand, $operator, $rightOperand);
            break;
            // is test for existence
            case 'is':
                $rightOperand = null;
                if ($negate) {
                    $operator = '!=';
                } else {
                    $operator = '=';
                }
            break;
            case 'in':
                if ($negate) {
                    $operator = 'not in';
                }
            break;
            case 'exists':
                if ($negate) {
                    $operator = 'not exists';
                }
            break;
            case '<>':
                $operator = '!=';
            // fall through
            case '!=':
                if ($negate) {
                    $operator = '=';
                }
            break;
            case '=':
                if ($negate) {
                    $operator = '!=';
                }
            break;
            case '<':
                if ($negate) {
                    $operator = '>=';
                }
            break;
            case '<=':
                if ($negate) {
                    $operator = '>';
                }
            break;
            case '>':
                if ($negate) {
                    $operator = '<=';
                }
            break;
            case '>=':
                if ($negate) {
                    $operator = '<';
                }
            break;
            case 'like':
            case 'regexp':
                // intentionally left blank
            break;
            // other operators are currently not supported
            default:
                throw new \Yana\Core\Exceptions\InvalidArgumentException("Invalid where clause '$syntaxTree'.");
            break;
        }

        /* a) flip operands, where necessary */
        if ($rightOperand['type'] === 'ident') {
            $_rightOperand = $rightOperand;
            $rightOperand = $leftOperand;
            $leftOperand = $_rightOperand;
            unset($_rightOperand);
        }
        // left operand must be identifier
        if ($leftOperand['type'] !== 'ident') {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Invalid where clause '$syntaxTree'.");
        }
        $leftOperand = $leftOperand['value'];
        if (strpos($leftOperand, '.') !== false) {
            $leftOperand = explode('.', $leftOperand);
        }
        // right operand may be identifier or value
        // a) is column name
        if ($rightOperand['type'] === 'ident') {
            if (strpos($rightOperand['value'], '.') !== false) {
                $rightOperand['value'] = explode('.', $rightOperand['value']);
            }
        } elseif ($rightOperand['type'] === 'command') {
            $rightOperand['value'] = $this->parseSQL($rightOperand);
        }
        $rightOperand = $rightOperand['value'];

        return array($leftOperand, $operator, $rightOperand);
    }

}

?>