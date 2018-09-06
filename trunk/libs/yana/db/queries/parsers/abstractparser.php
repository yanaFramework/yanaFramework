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
//
//        switch ($operator)
//        {
//            case 'and':
//            case 'or':
//                $leftOperand = $this->_parseWhere($leftOperand);
//                $rightOperand = $this->_parseWhere($rightOperand);
//                return array($leftOperand, $operator, $rightOperand);
//            break;
//            // is test for existence
//            case 'is not':
//                $negate = !$negate;
//            case 'is':
//                $rightOperand = null;
//                if ($negate) {
//                    $operator = '!=';
//                } else {
//                    $operator = '=';
//                }
//            break;
//            case 'in':
//                if ($negate) {
//                    $operator = 'not in';
//                }
//            break;
//            case 'exists':
//                if ($negate) {
//                    $operator = 'not exists';
//                }
//            break;
//            case '<>':
//                $operator = '!=';
//            // fall through
//            case '!=':
//                if ($negate) {
//                    $operator = '=';
//                }
//            break;
//            case '=':
//                if ($negate) {
//                    $operator = '!=';
//                }
//            break;
//            case '<':
//                if ($negate) {
//                    $operator = '>=';
//                }
//            break;
//            case '<=':
//                if ($negate) {
//                    $operator = '>';
//                }
//            break;
//            case '>':
//                if ($negate) {
//                    $operator = '<=';
//                }
//            break;
//            case '>=':
//                if ($negate) {
//                    $operator = '<';
//                }
//            break;
//            case 'not like':
//            case 'like':
//            case 'regexp':
//                // intentionally left blank
//            break;
//            // other operators are currently not supported
//            default:
//                throw new \Yana\Core\Exceptions\InvalidArgumentException("Invalid where clause '$syntaxTree'.");
//        }
//
//        $leftOperand = $whereClause[0];
//        $operator = $whereClause[1];
//        $rightOperand = $whereClause[2];
//        // flip operands, where necessary
//        switch (true)
//        {
//            case is_array($rightOperand) && isset($rightOperand['type']) && $rightOperand['type'] === 'ident': // pre 0.7
//            case is_array($rightOperand) && isset($rightOperand['table']) && isset($rightOperand['column']):
//                $_rightOperand = $rightOperand;
//                $rightOperand = $leftOperand;
//                $leftOperand = $_rightOperand;
//                unset($_rightOperand);
//        }
//        if (is_array($rightOperand) && isset($rightOperand['type']) && $rightOperand['type'] === 'ident') {
//            $_rightOperand = $rightOperand;
//            $rightOperand = $leftOperand;
//            $leftOperand = $_rightOperand;
//            unset($_rightOperand);
//        }
//        // left operand must be identifier
//        if (is_array($leftOperand) && isset($leftOperand['type']) && $leftOperand['type'] !== 'ident') {
//            throw new \Yana\Core\Exceptions\InvalidArgumentException("Invalid where clause. Left operand must be identifier.");
//        }
//        if (is_array($leftOperand) && isset($leftOperand['value'])) {
//            $leftOperand = $leftOperand['value'];
//        }
//        if (is_string($leftOperand) && strpos($leftOperand, '.') !== false) {
//            $leftOperand = explode('.', $leftOperand);
//        }
//        // right operand may be identifier or value is column name
//        if (is_array($rightOperand) && isset($rightOperand['type']) && $rightOperand['type'] === 'ident') {
//            if (strpos($rightOperand['value'], '.') !== false) {
//                $rightOperand['value'] = explode('.', $rightOperand['value']);
//            }
//        } elseif (is_array($rightOperand) && isset($rightOperand['type']) && $rightOperand['type'] === 'command') {
//            $rightOperand['value'] = $this->parseSQL($rightOperand);
//        }
//        if (is_array($rightOperand) && isset($rightOperand['value'])) {
//            $rightOperand = $rightOperand['value'];
//        }
//
//        return array($leftOperand, $operator, $rightOperand);
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
                break;
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
     * Returns list of join types in FROM-clause.
     *
     * @param   array  $syntaxTree  abstract syntax tree as provided by SQL_Parser
     * @return  array
     */
    protected function _mapOnClauseFromTableJoins(array $syntaxTree)
    {
        $joins = array();
        // retrieve table
        if (isset($syntaxTree['from']['table_references']['table_join_clause']) && is_array($syntaxTree['from']['table_references']['table_join_clause'])) {

            /* Example:
             * <pre>
             * { "from" => 
             *   { "table_references" =>
             *     { "table_join_clause" =>
             *       [ "join", "inner join", "left join" ]
             *     }
             *   }
             * }
             * </pre>
             */
            foreach ($syntaxTree['from']['table_references']['table_join_clause'] as $onClause)
            {
                $joins[] = $this->_parseWhere($onClause);
            }
            unset($onClause);
        }
        return $joins;
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