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
        $tables = $this->_mapTableList($syntaxTree); // array of table names
        $tableJoins = $this->_mapTableJoins($syntaxTree); // array of join types
        $unparsedTableJoinClauses = $this->_mapTableJoinClause($syntaxTree); // array of join on clauses
        $columnsAst = $this->_mapColumnList($syntaxTree); // array of column syntax trees
        $columns = $this->_mapColumnListToListOfIdentifiers($columnsAst); // array of column names
        // array of left operand, operator, right operand
        $unparsedWhere = (!empty($syntaxTree['where_clause'])) ? $syntaxTree['where_clause'] : array();
        // list of columns (keys) and asc/desc (value)
        $orderBy = (!empty($syntaxTree['sort_order'])) ? $syntaxTree['sort_order'] : array();

        if (empty($tables) || !is_array($tables)) {
            $message = "SQL-statement has no table names.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        /*
         * 1) set table
         */
        $database = $this->_getDatabase();
        $query = new \Yana\Db\Queries\Select($database);

        $table = current($tables);
        if (is_array($table) && isset($table["table"])) {
            $table = $table["table"];
        }
        $query->setTable((string) $table);

        $query->setColumns($columns);

        if (!empty($unparsedWhere)) {
            $where = $this->_parseWhere($unparsedWhere);
            $query->setWhere($where);
        }
        unset($where, $unparsedWhere);

        $tableJoinClauses = array();
        if (!empty($unparsedTableJoinClauses) && \is_array($unparsedTableJoinClauses)) {
            foreach ($unparsedTableJoinClauses as $unparsedTableJoinClause) {
                $tableJoinClauses[] = $this->_parseWhere($unparsedTableJoinClause);
            }
        }
        unset($unparsedTableJoinClause, $unparsedTableJoinClauses);

        /*
         * Resolve natural join to inner joins by automatically finding appropriate keys.
         */
        if (count($tableJoins) > 0) {
            assert('!isset($i); // Cannot redeclare variable $i');
            assert('!isset($join); // Cannot redeclare variable $join');
            $dbSchema = $database->getSchema();
            $tablesAlreadyJoined = array();

            foreach ($tableJoins as $i => $join)
            {
                $previousTableName = $tables[$i];
                if (is_array($previousTableName) && isset($previousTableName["table"])) {
                    $previousTableName = $previousTableName["table"];
                }
                $i++;
                $tableName = $tables[$i];
                if (is_array($tableName) && isset($tableName["table"])) {
                    $tableName = $tableName["table"];
                }
                $tablesAlreadyJoined[] = $previousTableName;

                $isLeftJoin = false;
                // switch by type of join
                switch (\strtolower($join))
                {
                    case 'natural join':
                        $query->setNaturalJoin($tableName);
                    break;

                    case 'cross join':
                        $message = "Cross joins are currently not supported.";
                        throw new \Yana\Db\Queries\Exceptions\NotSupportedException($message, \Yana\Log\TypeEnumeration::WARNING);

                    case 'right join':
                    case 'right outer join':
                        $message = "Right joins are currently not supported.";
                        throw new \Yana\Db\Queries\Exceptions\NotSupportedException($message, \Yana\Log\TypeEnumeration::WARNING);

                    case 'left join':
                    case 'left outer join':
                        $isLeftJoin = true;
                        // fall through
                    case 'inner join':
                    case 'join':
                    default:
                        $where = !empty($tableJoinClauses) ? array_shift($tableJoinClauses) : $query->getWhere();
                        $success = $this->_parseJoin($query, $tablesAlreadyJoined, $tableName, $where, $isLeftJoin);
                        if (!$success) {
                            $message = "SQL error: accidental cross-join detected in statement '{$query}'." .
                                "\n\t\tThe statement has been ignored.";
                            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::INFO);
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
            assert('!isset($columnName); // Cannot redeclare variable $columnName');
            assert('!isset($direction); // Cannot redeclare variable $direction');
            foreach ($orderBy as $columnName => $direction)
            {
                $orderByColumns[] = $columnName;
                $orderByDirections[] = $direction == 'desc';
            }
            unset($columnName, $direction);
            $query->setOrderBy($orderByColumns, $orderByDirections);
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
     * @param   array                    $baseTables   names of base tables
     * @param   string                   $joinedTable  name of table to join
     * @param   array                    $where       where clause (will be scanned)
     * @param   bool                     $isLeftJoin  treat as left join (currently no full outer joins supported)
     * @return  bool
     * @ignore
     * @throws  \Yana\Db\Queries\Exceptions\NotSupportedException  when valid SQL is encountered that is unfortunately not supported by the query builder
     */
    private function _parseJoin(\Yana\Db\Queries\Select $query, array $baseTables, $joinedTable, array $where, $isLeftJoin = false)
    {
        assert('is_string($joinedTable); // Wrong argument type for argument 2. String expected.');
        assert('is_bool($isLeftJoin); // Wrong argument type for argument 4. String expected.');
        if (empty($where)) {
            /* We can't join two tables if there is no join-condition.
             * Or, to be more precise, we "could" but we don't "want" to.
             * Since all that would do is trigger an exception claiming "accidental cross-join detected" and we really don't need that.
             *
             * Yes, I know cross-joins are technically valid SQL.
             * In practice, however, unless you explicitely write "A cross join B", 9 times out of 10 you probably didn't actually mean to.
             */
            $message = 'Table-joins without an on-clause are currently not supported.';
            throw new \Yana\Db\Queries\Exceptions\NotSupportedException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
        $leftOperand = $where[0];
        $rightOperand = $where[2];

        if (strtolower($where[1]) != "=" || !is_array($leftOperand) || !is_array($rightOperand)) {
            /* This section is for situations where we encounter SQL like "JOIN B ON c = d AND e = f".
             * One example would be compound primary/foreign keys, which we currently don't support.
             */
            $message = 'On-clauses in table joins have to be given as T1.COLUMN1 = T2.COLUMN2. More complex conditions are currently not supported.';
            throw new \Yana\Db\Queries\Exceptions\NotSupportedException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        /* We identify which part of the condition is the joined table.
         * Joined table is always table "B".
         * This convention makes the later steps a little easier.
         */
        if (strcasecmp((string) $leftOperand[0], $joinedTable) === 0) {
            $tableA  = $rightOperand[0]; // right operand is the base table
            $columnA = $rightOperand[1];
            $tableB  = $leftOperand[0];
            $columnB = $leftOperand[1];

        } elseif (strcasecmp((string) $rightOperand[0], $joinedTable) === 0) {
            $tableA  = $leftOperand[0]; // left operand is the base table
            $columnA = $leftOperand[1];
            $tableB  = $rightOperand[0];
            $columnB = $rightOperand[1];

        } else {
             // If we get to this statement, neither part of the condition was the joined table
            return false;
        }

        /* At this point we know, that rightOperand = target table.
         * We now check if leftOperand = base table.
         */
        if (!\in_array($tableA, $baseTables)) {
             // If we get to this statement, neither part of the condition was the base table
            return false; // not found
        }

        /* We are now certain that the $tableA is indeed one of the base tables and $tableB is the target table.
         * Since this is the case, we may now safely add the join, knowing that it will not fail.
         */
        try {
            if ($isLeftJoin) {
                $query->setLeftJoin($tableB, $columnB, $tableA, $columnA);
            } else {
                $query->setInnerJoin($tableB, $columnB, $tableA, $columnA);
            }

            // If nobody has thrown an exception at this point, we announce the tables married. Good luck to the newly wed.
            return true;

        // @codeCoverageIgnoreStart
        } catch (\Yana\Db\Queries\Exceptions\QueryException $e) {

            /* So, it seems one of the table-column pairs was a no-show at this wedding ceremony. Cold feet?
             * Be it as it may, with at least one half of the package missing, there can be no happy-ever-after.
             *
             * Note: unless assertions are switched off or are set to not stop execution, you cannot reach this statement.
             */
            $message = "Unable to join tables '" . $tableA . "' and '" . $tableB . "'. Cause: " . $e->getMessage();
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
        // @codeCoverageIgnoreEnd
    }

}

?>