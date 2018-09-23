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
 * @ignore
 */

namespace Yana\Db\Queries\Parsers;

/**
 * <<abstract>> Internal Query-Parser.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractParser extends \Yana\Core\Object implements \Yana\Db\Queries\Parsers\IsParser
{

    /**
     * @var \Yana\Db\IsConnection 
     */
    private $_database = null;

    /**
     * Set up the database to build the queries upon.
     *
     * @param  \Yana\Db\IsConnection  $database 
     */
    public function __construct(\Yana\Db\IsConnection $database)
    {
        $this->_database = $database;
    }

    /**
     * @return \Yana\Db\IsConnection
     */
    protected function _getDatabase()
    {
        return $this->_database;
    }

    /**
     * Resolves the where clause and returns the parsed array.
     *
     * Example input from: WHERE dbo.t1.c1 = dbo.t2.c2 AND dbo.t3.c3 = 2
     *
     * { "args" =>
     *   [
     *     { "database" => "dbo", "table" => "t1", "column" => "c1" }
     *     { "database" => "dbo", "table" => "t2", "column" => "c2" }
     *     { "database" => "dbo", "table" => "t3", "column" => "c3" }
     *     { "value" => "2", "type" => "int_val" }
     *   ],
     *   "ops" =>
     *   [
     *     "=",
     *     "AND",
     *     "="
     *   ]
     *
     * The output syntax is as follows: ([column] [operator] [value]) ( AND (...))*
     *
     * Example output for: WHERE dbo.t1.c1 = dbo.t2.c2 AND dbo.t3.c3 = 2
     * [
     *   [ ["t1", "c1"], "=", ["t2", "c2"] ],
     *   "and",
     *   [ ["t3", "c3"], "=", 2 ]
     * ]
     *   
     *
     * @param   array  $syntaxTree  where clause
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given where-clause is invalid
     * @ignore
     */
    protected function _parseWhere(array $syntaxTree)
    {
        if (empty($syntaxTree)) {
            return array(); // empty where clause
        }

        $arguments = array();
        $operators = array();
        $isNegated = false;
        foreach ($syntaxTree as $key => $value)
        {
            switch ($key)
            {
                case 'arg_1': // pre 0.7 version
                    $arguments[0] = $value;
                break;
                case 'arg_2': // pre 0.7 version
                    $arguments[1] = $value;
                break;
                case 'args':
                    $arguments = $value;
                break;
                case 'op': // pre 0.7 version
                    $operators = array($value);
                break;
                case 'ops':
                    $operators = $value;
                break;
                case 'neg':
                    $isNegated = !empty($syntaxTree['neg']);
                break;
            }
        }
        unset($key, $value);

        $whereClause = $this->_parseWhereByArguments($operators, $arguments, $isNegated);
        return $whereClause;
    }

    /**
     * 
     * @param   array  $operators  list of operators like =, >, <, LIKE, aso
     * @param   array  $arguments  list of arguments like column references or values
     * @param   bool   $isNegated  whether or not the clause is preceded by NOT
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when an unknown operator is encountered
     */
    protected function _parseWhereByArguments(array $operators, array $arguments, $isNegated)
    {

        $where = array();

        while (count($operators) > 0)
        {
            $operator = $this->_mapOperator(\array_shift($operators), $isNegated);

            switch ($operator)
            {
                case 'not exists':
                case 'exists':
                    $operand = $this->_mapArgument(\array_shift($arguments));
                    $where = array($operand, $operator, null);
                    unset($operand);
                break;
                case 'not in':
                case 'in':
                case '!=':
                case '=':
                case '<':
                case '<=':
                case '>':
                case '>=':
                case 'not like':
                case 'like':
                case 'regexp':
                    $leftOperand = $this->_mapArgument(\array_shift($arguments));
                    $rightOperand = $this->_mapArgument(\array_shift($arguments));
                    $where = array($leftOperand, $operator, $rightOperand);
                    unset($leftOperand, $rightOperand);
                break;
                case 'and':
                case 'or':
                    $where = array($where, $operator, $this->_parseWhereByArguments($operators, $arguments, $isNegated));
                break 2;
                // other operators are currently not supported
                default:
                    throw new \Yana\Core\Exceptions\InvalidArgumentException("Unsupported operator in where clause: " . $operator);
            }
            unset($operator);
        }

        return $where;
    }

    /**
     * Map original operator to 
     *
     * @param   string  $operator
     * @param   bool    $isNegated
     * @return  string
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException
     */
    protected function _mapOperator($operator, $isNegated)
    {
        assert('is_bool($isNegated); // Invalid argument type: $isNegated. Boolean expected.');

        switch (\strtolower($operator))
        {
            // is test for existence
            case 'is not':
                $isNegated = !$isNegated;
            // fall through
            case 'is':
                $operator = $isNegated ? '!=' : '=';
            break;
            case 'not in':
                $isNegated = !$isNegated;
            // fall through
            case 'in':
                $operator = $isNegated ? 'not in' : 'in';
            break;
            case 'not exists':
                $isNegated = !$isNegated;
            // fall through
            case 'exists':
                $operator = $isNegated ? 'not exists' : 'exists';
            break;
            case '!=':
                $isNegated = !$isNegated;
            // fall through
            case '=':
                $operator = $isNegated ? '!=' : '=';
            break;
            case '<':
                if ($isNegated) {
                    $operator = '>=';
                }
            break;
            case '<=':
                if ($isNegated) {
                    $operator = '>';
                }
            break;
            case '>':
                if ($isNegated) {
                    $operator = '<=';
                }
            break;
            case '>=':
                if ($isNegated) {
                    $operator = '<';
                }
            break;
            case 'not like':
                $isNegated = !$isNegated;
            // fall through
            case 'like':
                $operator = ($isNegated) ? 'not like' : 'like';
            break;
            case 'and':
                $operator = ($isNegated) ? 'or' : 'and';
            break;
            case 'or':
                $operator = ($isNegated) ? 'and' : 'or';
            break;
            case 'regexp':
                // intentionally left blank
            break;
            // other operators are currently not supported
            default:
                throw new \Yana\Core\Exceptions\InvalidArgumentException("Unsupported operator in where clause: " . $operator);
        }
        return $operator;
    }

    /**
     * Map parser tree to where clause argument.
     *
     * @param   mixed  $argument  unparsed argument
     * @return  mixed
     */
    protected function _mapArgument($argument)
    {
        $value = null;
        if (\is_array($argument)) {

            if (isset($argument['column']) && isset($argument['table']) && $argument['table'] > "") {
                $value = array((string) $argument['table'], (string) $argument['column']);

            } elseif (isset($argument['column'])) {
                $value = (string) $argument['column'];

            } elseif (isset($argument['value'])) {
                $value = $argument['value'];
            }
        } elseif (\is_scalar($argument)) {
            $value = $argument;
        }
        return $value;
    }

    /**
     * Returns list of tables in FROM-clause.
     *
     * @param   array  $syntaxTree  abstract syntax tree as provided by SQL_Parser
     * @return  array
     */
    protected function _mapTableList(array $syntaxTree)
    {
        $tables = array();
        // retrieve table
        if (isset($syntaxTree['from']['table_references']['table_factors']) && is_array($syntaxTree['from']['table_references']['table_factors'])) {

            /* Example:
             * <pre>
             * { "from" => 
             *   { "table_references" =>
             *     { "table_factors" =>
             *       [
             *          { "database" => "schema", "table" => "name", "alias" => "foo" }
             *       ]
             *     }
             *   }
             * }
             * </pre>
             */
            $tables = $syntaxTree['from']['table_references']['table_factors'];
        }
        return $tables;
    }

    /**
     * Returns list of join types in FROM-clause.
     *
     * @param   array  $syntaxTree  abstract syntax tree as provided by SQL_Parser
     * @return  array
     */
    protected function _mapTableJoins(array $syntaxTree)
    {
        $joins = array();
        // retrieve table
        if (isset($syntaxTree['from']['table_references']['table_join']) && is_array($syntaxTree['from']['table_references']['table_join'])) {

            /* Example:
             * <pre>
             * { "from" => 
             *   { "table_references" =>
             *     { "table_join" =>
             *       [ "join", "inner join", "left join" ]
             *     }
             *   }
             * }
             * </pre>
             */
            $joins = $syntaxTree['from']['table_references']['table_join'];
        }
        return $joins;
    }

    /**
     * Returns the ON-clause associated with table joins, if there is any.
     *
     * The syntax of the returned AST is the same as that of the WHERE clause.
     *
     * @param   array  $syntaxTree  abstract syntax tree as provided by SQL_Parser
     * @return  array
     */
    protected function _mapTableJoinClause(array $syntaxTree)
    {
        $joins = array();
        // retrieve table
        if (isset($syntaxTree['from']['table_references']['table_join_clause']) && is_array($syntaxTree['from']['table_references']['table_join_clause'])) {

            /* Example:
             * <pre>
             * { "args" =>
             *     [
             *         {
             *             "database" => ""
             *             "table" => "t"
             *             "column" => "ftid"
             *             "alias" => ""
             *         },
             *         {
             *             "database" => ""
             *             "table" => "ft"
             *             "column" => "ftid"
             *             "alias" => ""
             *         }
             *     ],
             *     "ops" => [ "=" ]
             * )
             * </pre>
             */
            $joins = $syntaxTree['from']['table_references']['table_join_clause'];
        }
        return $joins;
    }

    /**
     * Returns list of columns in DML statements.
     *
     * @param   array  $syntaxTree  abstract syntax tree as provided by SQL_Parser
     * @return  array
     * @codeCoverageIgnore
     */
    protected function _mapColumnNames(array $syntaxTree)
    {
        $columns = array();
        // retrieve columns
        if (isset($syntaxTree['column_names']) && is_array($syntaxTree['column_names'])) {

            $columns = $syntaxTree['column_names'];
        } elseif (isset($syntaxTree['columns']) && is_array($syntaxTree['columns'])) {

            $columns = $syntaxTree['columns'];
        }
        return $columns;
    }

    /**
     * Returns list of tables in DML statements.
     *
     * @param   array  $syntaxTree  abstract syntax tree as provided by SQL_Parser
     * @return  array
     * @codeCoverageIgnore
     */
    protected function _mapTableNames(array $syntaxTree)
    {
        $tables = array();
        // retrieve tables
        if (isset($syntaxTree['table_names']) && is_array($syntaxTree['table_names'])) {

            $tables = $syntaxTree['table_names'];
        } elseif (isset($syntaxTree['tables']) && is_array($syntaxTree['tables'])) {

            $tables = $syntaxTree['tables'];
        }
        return $tables;
    }

    /**
     * Returns a single table name in DML statements.
     *
     * @param   array  $syntaxTree  abstract syntax tree as provided by SQL_Parser
     * @return  string
     */
    protected function _mapTableName(array $syntaxTree)
    {
        $tables = $this->_mapTableNames($syntaxTree);
        $tableName = (is_array($tables) && count($tables) > 0) ? current($tables) : "";
        if (!is_string($tableName)) {
            $tableName = is_array($tableName) && isset($tableName['table']) ? (string) $tableName['table'] : "";
        }
        return $tableName;
    }

    /**
     * Returns list of columns in SELECT statements.
     *
     * @param   array  $syntaxTree  abstract syntax tree as provided by SQL_Parser
     * @return  array
     */
    protected function _mapColumnList(array $syntaxTree)
    {
        $columns = array();
        // retrieve table
        if (isset($syntaxTree['select_expressions']) && is_array($syntaxTree['select_expressions'])) {

            /* Example:
             * <pre>
             * [
             *   { "args =>
             *     [
             *        { "name" => "count",
             *          "arg" => [ "*" ]
             *          "type" => [ "*" ]
             *        }
             *     ]
             *   },
             *   { "args =>
             *     [
             *        { "value" => "1",
             *          "type" => [ "int_val" ]
             *        }
             *     ]
             *   },
             * ]
             * </pre>
             */
            $columns = $syntaxTree['select_expressions'];
        }
        return $columns;
    }

    /**
     * Returns list of columns in SELECT statements.
     *
     * @param   array  $syntaxTree  abstract syntax tree as provided by SQL_Parser
     * @return  array
     */
    protected function _mapColumnListToListOfIdentifiers(array $syntaxTree)
    {
        $columns = array();
        foreach ($syntaxTree as $column)
        {
            if (isset($column['args'][0]['column'])) {
                $columns[] = (string) $column['args'][0]['column'];
            }
        }
        unset($column);

        return $columns;
    }

}

?>