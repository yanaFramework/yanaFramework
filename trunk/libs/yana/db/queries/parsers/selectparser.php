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
 * Internal Query-Parser.
 *
 * @package     yana
 * @subpackage  db
 */
class SelectParser extends \Yana\Db\Queries\Parsers\AbstractParser implements \Yana\Db\Queries\Parsers\IsParser
{

    /**
     * Parse SQL query into query object.
     *
     * @param   array  $syntaxTree  SQL statement
     * @return  \Yana\Db\Queries\Select
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException      if the query is invalid or could not be parsed
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException  when the tables are not found
     */
    public function parseStatement(array $syntaxTree)
    {
        $tables = $syntaxTree['tables']; // array of table names
        $column = $syntaxTree['columns']; // array of column names
        // array of left operand, operator, right operand
        $unparsedWhere = (!empty($syntaxTree['where_clause'])) ? $syntaxTree['where_clause'] : array();
        // array of left operand, operator, right operand
        $having = (!empty($syntaxTree['having_clause'])) ? $syntaxTree['having_clause'] : array();
        // list of columns (keys) and asc/desc (value)
        $orderBy = (!empty($syntaxTree['sort_order'])) ? $syntaxTree['sort_order'] : array();

        if (empty($tables)) {
            $message = "SQL-statement has no table names: $syntaxTree.";
            return \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }

        /*
         * 1) set table
         */
        $database = $this->_getDatabase();
        $query = new \Yana\Db\Queries\Select($database);
        $query->setTable(current($tables));
        $query->setColumns($column);

        if (!empty($unparsedWhere)) {
            $where = $this->_parseWhere($unparsedWhere);
            $query->setWhere($where);
        }
        unset($where, $unparsedWhere);

        /*
         * Resolve natural join to inner joins by automatically finding appropriate keys.
         */
        if (!empty($syntaxTree['table_join'])) {
            assert('!isset($i)', ' Cannot redeclare variable $i');
            assert('!isset($join)', ' Cannot redeclare variable $join');
            $dbSchema = $database->getSchema();
            $i = 0;
            foreach ($syntaxTree['table_join'] as $join)
            {
                $i++;
                $tableNameA = $tables[$i - 1];
                $tableNameB = $tables[$i];
                $isLeftJoin = false;
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
                        assert('!isset($columnsB)', ' Cannot redeclare variable $columnsB');
                        $columnsB = $tableB->getColumnNames();
                        assert('!isset($colA)', ' Cannot redeclare variable $colA');
                        foreach ($tableA->getColumnNames() as $columnA)
                        {
                            if (in_array($columnA, $columnsB)) {
                                $query->addWhere(array($tableNameA, $columnA), '=', array($tableNameB, $columnA));
                            }
                        } // end foreach
                        unset($tableNameA, $tableA, $columnsB, $tableNameB, $tableB, $columnA);
                    break;
                    case 'right join':
                    case 'right outer join':
                        // flip operands: $tableA <-> $tableB
                        assert('!isset($_)', ' Cannot redeclare var $_');
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
                        $where = $query->getWhere();
                        $success = $this->_parseJoin($query, $tableNameA, $tableNameB, $where, $isLeftJoin);
                        if (!$success) {
                            $message = "SQL error: accidental cross-join detected in statement '{$query}'." .
                                "\n\t\tThe statement has been ignored.";
                            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_NOTICE);
                        }
                        continue;
                    break;
                }
            } // end foreach
            unset($i, $join, $isLeftJoin);
        } // end if

        /*
         * 3) set order by and direction
         */
        if (!empty($orderBy)) {
            $orderByColumns = array();
            $orderByDirections = array();
            assert('!isset($columnName)', ' Cannot redeclare variable $columnName');
            assert('!isset($direction)', ' Cannot redeclare variable $direction');
            foreach ($orderBy as $columnName => $direction)
            {
                $orderByColumns[] = $columnName;
                $orderByDirections[] = $direction == 'desc';
            }
            unset($columnName, $direction);
            $query->setOrderBy($orderByColumns, $orderByDirections);
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
        assert('is_string($leftTable)', ' Wrong argument type for argument 1. String expected.');
        assert('is_string($rightTable)', ' Wrong argument type for argument 2. String expected.');
        assert('is_bool($isLeftJoin)', ' Wrong argument type for argument 4. String expected.');
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

}

?>