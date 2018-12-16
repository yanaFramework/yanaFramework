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
class SelectExist extends \Yana\Db\Queries\AbstractQuery
{

    /**
     * @var int
     * @ignore
     */
    protected $type = \Yana\Db\Queries\TypeEnumeration::EXISTS;

    /**
     * set where clause
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
     * @param   array  $where  here clause
     * @throws  \Yana\Core\Exceptions\NotFoundException         when a column is not found
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the where-clause contains invalid values
     * @return  \Yana\Db\Queries\SelectExist 
     */
    public function setWhere(array $where = array())
    {
        parent::setWhere($where);
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
     * @return  \Yana\Db\Queries\SelectExist 
     */
    public function addWhere(array $where)
    {
        parent::addWhere($where);
        return $this;
    }

    /**
     * Returns the current where clause.
     *
     * @return  array
     */
    public function getWhere()
    {
        return parent::getWhere();
    }

    /**
     * Joins the selected table with another (by using an inner join).
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
     * @throws  \Yana\Core\Exceptions\NotFoundException  if a provided table or column is not found
     * @return  \Yana\Db\Queries\SelectExist
     */
    public function setInnerJoin($joinedTableName, $targetKey = null, $sourceTableName = null, $foreignKey = null)
    {
        parent::setJoin($joinedTableName, $targetKey, $sourceTableName, $foreignKey, false);
        return $this;
    }

    /**
     * remove table  of joined tables
     *
     * Calling this function will remove the given table from the query.
     * Note: you may not remove the base table.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $table  name of table to remove
     * @return  \Yana\Db\Queries\SelectExist
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the table does not exist
     */
    public function unsetJoin($table)
    {
        assert('is_string($table); // Wrong type for argument 1. String expected');
        $table = mb_strtolower($table);

        if (YANA_DB_STRICT && !$this->getDatabase()->getSchema()->isTable($table)) {
            throw new \Yana\Core\Exceptions\NotFoundException("The table '$table' is unknown.", \Yana\Log\TypeEnumeration::WARNING);
        }

        unset($this->joins[$table]);
        return $this;
    }

    /**
     * Get foreign key column.
     *
     * @param   string  $table  joined table
     * @return  \Yana\Db\Queries\JoinCondition
     * @throws  \Yana\Db\Queries\Exceptions\NotFoundException  when the target table is not joined
     */
    public function getJoin($table)
    {
        assert('is_string($table); // Wrong type for argument 1. String expected');
        $table = mb_strtolower($table);

        if (!isset($this->joins[$table])) {            
            throw new \Yana\Db\Queries\Exceptions\NotFoundException("The table '$table' is not joined.", \Yana\Log\TypeEnumeration::WARNING);
        }
        return $this->joins[$table];
    }

    /**
     * Get a list of all joined tables.
     *
     * Returns an array where the keys are the names of the joined tables.
     * Each item is an array of two column names, where the first is the column in the base table
     * and the second is the column in the target table.
     *
     * The array will be empty if there are now table-joins in the current query.
     *
     * @return  \Yana\Db\Queries\JoinCondition[]
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * Returns list of table names including those joined in the statement.
     *
     * @return  array
     */
    protected function getTables()
    {
        return array_merge(array($this->getTable()), array_keys($this->getJoins()));
    }

    /**
     * Get the number of entries.
     *
     * This sends the query statement to the database and returns bool(true)
     * if the requested database object exists and bool(false) otherwise.
     *
     * @return  bool
     */
    public function doesExist()
    {
        try {
            $result = $this->sendQuery();
        } catch (\Yana\Db\Queries\Exceptions\QueryException $e) {
            return false;
        }

        $i = $result->fetchRow(0);
        return !empty($i);
    }

    /**
     * Build a SQL-query.
     *
     * @param   string  $stmt  sql statement template
     * @return  string
     */
    protected function toString($stmt = "SELECT 1 FROM %TABLE% %WHERE%")
    {

        /* prepare where clause */
        $where = $this->getWhere();

        if (is_array($where) && !empty($where)) {
            $where = $this->convertWhereToString($where);
            if (!empty($where)) {
                $where = 'WHERE ' . $where;
            }
        } else {
            $where = "";
        }

        /* 1. replace %TABLE% */
        if (!empty($this->joins)) {
            $table = $this->getDatabase()->quoteId(YANA_DATABASE_PREFIX . $this->getTable());

            assert('!isset($tableName); // cannot redeclare variable $tableName');
            assert('!isset($join); // cannot redeclare variable $join');
            foreach ($this->joins as $tableName => $join)
            {
                /* add table-join */
                switch (true)
                {
                    case $join->isLeftJoin():
                        $table .= ' LEFT JOIN ' . $this->getDatabase()->quoteId(YANA_DATABASE_PREFIX . $tableName);
                    break;
                    case $join->isInnerJoin():
                        $table .= ' JOIN ' . $this->getDatabase()->quoteId(YANA_DATABASE_PREFIX . $tableName);
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
                        $table .= ' ON ' .
                            ($join->getSourceTableName() > "" ? $this->getDatabase()->quoteId(YANA_DATABASE_PREFIX . $join->getSourceTableName()) . '.' : "") .
                            $this->getDatabase()->quoteId($join->getForeignKey()) .
                            ' = ' .
                            ($join->getJoinedTableName() > "" ? $this->getDatabase()->quoteId(YANA_DATABASE_PREFIX . $join->getJoinedTableName()) . '.' : "") .
                            $this->getDatabase()->quoteId($join->getTargetKey());
                    break;
                }
            } /* end foreach */
            unset($tableName, $join);

            $stmt = str_replace('%TABLE%', $table, $stmt);
        }

        /* 2. replace %WHERE% */
        assert('is_string($where); // Unexpected value $where');
        if (!empty($where)) {
            $stmt = str_replace('%WHERE%', trim($where), $stmt);
        } else {
            $stmt = str_replace(' %WHERE%', '', $stmt);
        }
        unset($where);

        return parent::toString($stmt);

    }

}

?>