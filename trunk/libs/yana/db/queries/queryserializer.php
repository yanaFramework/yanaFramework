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
class QuerySerializer extends \Yana\Db\Queries\AbstractQuerySerializer
{

    /**
     * Convert table to quoted string.
     *
     * @param   \Yana\Db\Queries\IsQuery  $query  source query
     * @return  string
     */
    protected function _toTableString(\Yana\Db\Queries\IsQuery $query): string
    {
        return $query->getDatabase()->quoteId(YANA_DATABASE_PREFIX . (string) $query->getTable());
    }

    /**
     * Convert array of filters to where clause.
     *
     * @param   \Yana\Db\Queries\IsQueryWithWhereClause  $query  source query
     * @return  string
     */
    protected function _toSetClause(\Yana\Db\Queries\IsQueryWithValue $query): string
    {
        assert(!isset($set), 'Cannot redeclare $set');
        $set = "";
        assert(!isset($values), 'Cannot redeclare $values');
        $values = $query->getValues();
        if (!$this->_isCollectingParametersForBinding()) {
            assert(!isset($valueConverter), 'Cannot redeclare $valueConverter');
            $valueConverter = new \Yana\Db\Helpers\ValueConverter($query->getDatabase()->getDBMS());
            $valueConverter->setQuotingAlgorithm(new \Yana\Db\Sql\Quoting\ConnectionAlgorithm($query->getDatabase()));
            if ($query->getExpectedResult() === \Yana\Db\ResultEnumeration::ROW && \is_array($values)) {
                $values = $valueConverter->convertRowToString($query->getDatabase()->getSchema()->getTable($query->getTable()), $values);
            } elseif ($query instanceof \Yana\Db\Queries\IsQueryWithColumn) {
                assert(!isset($table), 'Cannot redeclare $table');
                $table = $query->getDatabase()->getSchema()->getTable($query->getTable());
                $values = $valueConverter->convertValueToString($values, $table->getColumn($query->getColumn())->getType());
                unset($table);
            } else {
                $values = $valueConverter->convertValueToString($values, \Yana\Db\Ddl\ColumnTypeEnumeration::STRING);
            }
        }
        switch ($query->getExpectedResult())
        {
            case \Yana\Db\ResultEnumeration::ROW:
                if (\is_array($values)) {
                    assert(!isset($column), 'Cannot redeclare $column');
                    assert(!isset($value), 'Cannot redeclare $value');
                    foreach ($values as $column => $value)
                    {
                        if ($set !== '') {
                            $set .= ', ';
                        }
                        $set .= $query->getDatabase()->quoteId(\YANA_DATABASE_PREFIX . $query->getTable()) . '.'
                            . $query->getDatabase()->quoteId($column) . ' = ';
                        if ($this->_isCollectingParametersForBinding()) {
                            $set .= '?';
                            $params[] = $value;
                        } else {
                            $set .= $value;
                        }
                    }
                    unset($column, $value);
                } else {
                    throw new \Yana\Core\Exceptions\InvalidArgumentException("No valid values provided.", \Yana\Log\TypeEnumeration::WARNING);
                }
                unset($values);
            break;
            case \Yana\Db\ResultEnumeration::CELL:
                $set = $query->getDatabase()->quoteId(\YANA_DATABASE_PREFIX . $query->getTable()) . '.'
                    . $query->getDatabase()->quoteId($query->getColumn()) . ' = ';
                if ($this->_isCollectingParametersForBinding()) {
                    $set .= '?';
                    $params[] = $values;
                } else {
                    $set .= $values;
                }
            break;
            default:
                throw new \Yana\Core\Exceptions\InvalidArgumentException("No row or cell selected for update.", \Yana\Log\TypeEnumeration::WARNING);
        }
        return $set;
    }

    /**
     * Convert filters to where clause.
     *
     * @param   \Yana\Db\Queries\IsQueryWithWhereClause  $query  source query
     * @return  string
     */
    protected function _toWhereClause(\Yana\Db\Queries\IsQueryWithWhereClause $query): string
    {
        $whereClause = '';
        $where = $query->getWhere();
        if (count($where) > 0) {
            $whereClause = ' WHERE ' . $this->_convertFilterStatementToString($query->getDatabase(), $where);
        }

        return $whereClause;
    }

    /**
     * Convert where clause to string.
     *
     * Returns the where condition clause as a string for printing.
     *
     * @param  \Yana\Db\IsConnection  $connection  to quote Ids
     * @param   array                 $where       where clausel as an array
     * @return  string
     */
    private function _convertFilterStatementToString(\Yana\Db\IsConnection $connection, array $where): string
    {
        if (count($where) === 0) {
            return "";
        }
        /* if all required information is provided */
        assert(count($where) === 3, 'Where clause must have exactly 3 items: left + right operands + operator');
        $leftOperand = $where[0];
        $operator = $where[1];
        $rightOperand = $where[2];

        /**
         * 1) is sub-clause
         */
        switch ($operator)
        {
            case  \Yana\Db\Queries\OperatorEnumeration::OR:
                return $this->_convertFilterStatementToString($connection, $leftOperand)
                    . ' OR ' . $this->_convertFilterStatementToString($connection, $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::AND:
                return $this->_convertFilterStatementToString($connection, $leftOperand)
                    . ' AND ' . $this->_convertFilterStatementToString($connection, $rightOperand);
        }

        /**
         * 2) is atomar clause
         */
        // left operand
        if (is_array($leftOperand)) {
            $leftOperand = $connection->quoteId(YANA_DATABASE_PREFIX . $leftOperand[0]) . '.' . $leftOperand[1];
        }
        // right operand
        if ($operator === \Yana\Db\Queries\OperatorEnumeration::EXISTS || $operator === \Yana\Db\Queries\OperatorEnumeration::NOT_EXISTS) {
            if ($rightOperand instanceof \Yana\Db\Queries\Select) {
                $rightOperand = "(" . $this->fromSelectQuery($rightOperand) . ")";
            }
        } elseif ($operator === \Yana\Db\Queries\OperatorEnumeration::IN || $operator === \Yana\Db\Queries\OperatorEnumeration::NOT_IN) {
            assert(!isset($list), 'cannot redeclare variable $list');
            $list = "";
            if ($rightOperand instanceof \Yana\Db\Queries\Select) {
                $list = $this->fromSelectQuery($rightOperand);
            } else {
                assert(!isset($value), 'cannot redeclare variable $value');
                foreach ($rightOperand as $value)
                {
                    if ($list > "") {
                        $list .= ", ";
                    }
                    if ($this->_isCollectingParametersForBinding()) {
                        $this->_bindQueryParameter($value);
                        $list .= '?';
                    } else {
                        $list .= $connection->quote($value);
                    }
                }
                unset($value);
            }
            $rightOperand = "(" . $list . ")";
            unset($list);
        } elseif (is_array($rightOperand)) {
            $rightOperand = $connection->quoteId(YANA_DATABASE_PREFIX . $rightOperand[0]) . '.' . $rightOperand[1];
        } elseif (is_string($rightOperand)) {
            if ($this->_isCollectingParametersForBinding()) {
                $this->_bindQueryParameter($rightOperand);
                $rightOperand = '?';
            } else {
                $rightOperand = $connection->quote($rightOperand);
            }
        } elseif (is_null($rightOperand)) {
            if ($operator == \Yana\Db\Queries\OperatorEnumeration::EQUAL) {
                return $leftOperand . ' is null ';
            } elseif ($operator == \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL) {
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
     * Convert order-by settings of query to string.
     *
     * @param   \Yana\Db\Queries\IsQueryWithOrderClause  $query  source query
     * @return  string
     */
    protected function _toOrderBy(\Yana\Db\Queries\IsQueryWithOrderClause $query): string
    {
        assert(!isset($orderByString), 'Cannot redeclare var $orderByString');
        $orderByString = "";

        assert(!isset($tableName), 'Cannot redeclare var $tableName');
        $tableName = $query->getTable();
        assert(!isset($orderBy), 'Cannot redeclare var $orderBy');
        $orderBy = $query->getOrderBy();
        assert(!isset($desc), 'Cannot redeclare var $desc');
        $desc = $query->getDescending();
        if (count($orderBy) > 0) {
            $orderByString = ' ORDER BY ';
            assert(!isset($i), 'Cannot redeclare var $i');
            assert(!isset($element), 'Cannot redeclare var $element');
            foreach ($orderBy as $i => $element)
            {
                if (is_array($element)) {
                    $orderByString .= $element[0] . '.' . $element[1];
                } else {
                    $orderByString .= $tableName . '.' . $element;
                }
                if (!empty($desc[$i])) {
                    $orderByString .= ' DESC';
                }
                if (++$i < count($orderBy)) {
                    $orderByString .= ', ';
                }
            } /* end foreach */
            unset($i, $element); /* clean up garbage */
        }

        return $orderByString;
    }

    /**
     * Create comma-separated list of column names.
     *
     * @param   \Yana\Db\Queries\IsQueryWithWhereClause  $query  source query
     * @return  string
     */
    protected function _toKeyClause(\Yana\Db\Queries\IsQueryWithValue $query): string
    {
        assert(!isset($keys), 'Cannot redeclare $keys');
        $keys = "";
        // quote id's to avoid conflicts with reserved keywords
        assert(!isset($key), 'Cannot redeclare var $key');
        foreach (array_keys($query->getValues()) as $key)
        {
            if ($keys != "") {
                $keys .= ", ";
            }
            $keys .= $query->getDatabase()->quoteId($key);
        }
        unset($key);

        return $keys;
    }

    /**
     * Create comma-separated list of values.
     *
     * @param   \Yana\Db\Queries\IsQueryWithWhereClause  $query  source query
     * @return  string
     */
    protected function _toValueClause(\Yana\Db\Queries\IsQueryWithValue $query): string
    {
        assert(!isset($values), 'Cannot redeclare $values');
        if ($this->_isCollectingParametersForBinding()) {
            $values = array();
            foreach ($query->getValues() as $value)
            {
                $values[] = '?';
                $this->_bindQueryParameter((string) $value);
            }
        } else {
            assert(!isset($table), 'Cannot redeclare $table');
            $table = $query->getDatabase()->getSchema()->getTable($query->getTable());
            assert(!isset($valueConverter), 'Cannot redeclare $valueConverter');
            $valueConverter = new \Yana\Db\Helpers\ValueConverter($query->getDatabase()->getDBMS());
            $valueConverter->setQuotingAlgorithm(new \Yana\Db\Sql\Quoting\ConnectionAlgorithm($query->getDatabase()));
            $values = $valueConverter->convertRowToString($table, \array_change_key_case($query->getValues(), CASE_LOWER));
            unset($table, $valueConverter);
        }

        return implode(", ", $values);
    }

    /**
     * Create list of table joins.
     *
     * @param   \Yana\Db\Queries\IsQueryWithWhereClause  $query  source query
     * @return  string
     */
    protected function _toJoinClause(\Yana\Db\Queries\IsQueryWithJoins $query): string
    {
        assert(!isset($joins ), 'cannot redeclare variable $joins ');
        $joins = "";
        assert(!isset($connection), 'cannot redeclare variable $connection');
        $connection = $query->getDatabase();

        assert(!isset($join), 'cannot redeclare variable $join');
        foreach ($query->getJoins() as $join)
        {
            /** @var \Yana\Db\Queries\JoinCondition $join */

            /* add table-join */
            switch (true)
            {
                case $join->isLeftJoin():
                    $joins .= ' LEFT JOIN ' . $connection->quoteId(YANA_DATABASE_PREFIX . $join->getJoinedTableName());
                break;
                case $join->isInnerJoin():
                    $joins .= ' JOIN ' . $connection->quoteId(YANA_DATABASE_PREFIX .  $join->getJoinedTableName());
                break;
            };
            if ($join->getForeignKey() === "" || $join->getTargetKey() === "") {
                continue; // nothing to add to on-clause
            }
            /* add on-clause */
            switch (true)
            {
                case $join->isLeftJoin():
                case $join->isInnerJoin():
                    $joins .= ' ON ' .
                        ($join->getSourceTableName() > "" ? $connection->quoteId(YANA_DATABASE_PREFIX . $join->getSourceTableName()) . '.' : "") .
                        $connection->quoteId($join->getForeignKey()) .
                        ' = ' .
                        ($join->getJoinedTableName() > "" ? $connection->quoteId(YANA_DATABASE_PREFIX . $join->getJoinedTableName()) . '.' : "") .
                        $connection->quoteId($join->getTargetKey());
                break;
            }
        } /* end foreach */
        unset($join);

        return $joins;
    }

    /**
     * Convert to column name.
     *
     * @param   \Yana\Db\Queries\IsQueryWithColumn  $query  source query
     * @return  string
     */
    protected function _toColumnName(\Yana\Db\Queries\IsQueryWithColumn $query): string
    {
        assert(!isset($columnName), 'Cannot redeclare $columnName');
        $columnName = $query->getColumn();
        if ($query->getColumn() === '*') {
            return $columnName;
        }
        return $query->getDatabase()->quoteId(YANA_DATABASE_PREFIX . $query->getTable()) . '.' . $query->getDatabase()->quoteId($columnName);
    }

    /**
     * Convert to list of column names.
     *
     * @param   \Yana\Db\Queries\IsSelectQuery  $query  source query
     * @return  string
     */
    protected function _toColumnList(\Yana\Db\Queries\IsSelectQuery $query): string
    {
        if ($query->getColumn() === '*') {
            return '*';
        }
        assert(!isset($columnName), 'Cannot redeclare $columnName');
        $columnName = "";

        assert(!isset($connection), 'Cannot redeclare var $connection');
        $connection = $query->getDatabase();
        assert(!isset($alias), 'Cannot redeclare var $alias');
        assert(!isset($item), 'Cannot redeclare var $item');
        foreach ($query->getColumns() as $alias => $item)
        {
            // @codeCoverageIgnoreStart
            if (!is_array($item)) {
                continue; // Should be unreachable
            }
            // @codeCoverageIgnoreEnd
            if ($columnName !== "") {
                $columnName .= ', ';
            }
            $columnName .= $connection->quoteId(YANA_DATABASE_PREFIX . $item[0]) . '.' . $connection->quoteId($item[1]);
            if (is_string($alias) && $alias > "") {
                $columnName .= " as " . $connection->quoteId($alias);
            }
            /* When selecting a column, the framework automatically adds the primary key as second column.
             * This second column must be dropped for sub-queries or otherwise the query will fail.
             */
            if ($query->isSubSelect() && $query->getExpectedResult() === \Yana\Db\ResultEnumeration::COLUMN) {
                break;
            }
        }
        unset($alias, $item);

        return $columnName;
    }

    /**
     * Convert filters to having clause.
     *
     * @param   \Yana\Db\Queries\IsQueryWithHavingClause  $query  source query
     * @return  string
     */
    protected function _toHavingClause(\Yana\Db\Queries\IsQueryWithHavingClause $query): string
    {
        $havingClause = '';
        $having = $query->getHaving();
        if (count($having) > 0) {
            $havingClause = ' HAVING ' . $this->_convertFilterStatementToString($query->getDatabase(), $having);
        }

        return $havingClause;
    }

    /**
     * Convert Insert query to SQL string.
     *
     * Result INSERT INTO ... (...) VALUES (...)
     *
     * @param   \Yana\Db\Queries\IsInsertQuery  $query  source query
     * @return  string
     */
    public function fromInsertQuery(\Yana\Db\Queries\IsInsertQuery $query): string
    {
        return "INSERT INTO " . $this->_toTableString($query) .
                " (" . $this->_toKeyClause($query) . ") VALUES (" . $this->_toValueClause($query) . ")";
    }

    /**
     * Convert Update query to SQL string.
     *
     * Result UPDATE ... SET ... WHERE ...
     * 
     * @param   \Yana\Db\Queries\IsUpdateQuery  $query  source query
     * @return  string
     */
    public function fromUpdateQuery(\Yana\Db\Queries\IsUpdateQuery $query): string
    {
        return "UPDATE " . $this->_toTableString($query) .
                " SET " . $this->_toSetClause($query) .
                $this->_toWhereClause($query);
    }

    /**
     * Convert Delete query to SQL string.
     *
     * Result DELETE FROM ... WHERE ... ORDER BY ...
     * 
     * @param   \Yana\Db\Queries\IsDeleteQuery  $query  source query
     * @return  string
     */
    public function fromDeleteQuery(\Yana\Db\Queries\IsDeleteQuery $query): string
    {
        return "DELETE FROM " .
                $this->_toTableString($query) .
                $this->_toWhereClause($query) .
                $this->_toOrderBy($query);
    }

    /**
     * Convert Select-1 query to SQL string.
     *
     * Result SELECT 1 FROM ... JOIN ... WHERE ...
     * 
     * @param   \Yana\Db\Queries\IsExistsQuery  $query  source query
     * @return  string
     */
    public function fromExistsQuery(\Yana\Db\Queries\IsExistsQuery $query): string
    {
        return "SELECT 1 FROM " .
                $this->_toTableString($query) .
                $this->_toJoinClause($query) .
                $this->_toWhereClause($query);
    }

    /**
     * Convert Select-Count query to SQL string.
     *
     * Result SELECT count(...) FROM ... JOIN ... WHERE ...
     * 
     * @param   \Yana\Db\Queries\IsCountQuery  $query  source query
     * @return  string
     */
    public function fromCountQuery(\Yana\Db\Queries\IsCountQuery $query): string
    {
        return "SELECT count(" . $this->_toColumnName($query) . ") FROM " .
                $this->_toTableString($query) .
                $this->_toJoinClause($query) .
                $this->_toWhereClause($query);
    }

    /**
     * Convert Select-Count query to SQL string.
     *
     * Result SELECT ... FROM ... JOIN ... WHERE ... HAVING ... ORDER BY ...
     * 
     * @param   \Yana\Db\Queries\IsSelectQuery  $query  source query
     * @return  string
     */
    public function fromSelectQuery(\Yana\Db\Queries\IsSelectQuery $query): string
    {
        return "SELECT " . $this->_toColumnList($query) . " FROM " .
                $this->_toTableString($query) .
                $this->_toJoinClause($query) .
                $this->_toWhereClause($query) .
                $this->_toHavingClause($query) .
                $this->_toOrderBy($query);
    }
}

?>