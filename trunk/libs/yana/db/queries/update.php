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
 * This class is a query builder that can be used to build SQL statements to update existing
 * rows or cells in a database-table.
 *
 * Note: this class does NOT untaint input data for you.
 *
 * @package     yana
 * @subpackage  db
 */
class Update extends \Yana\Db\Queries\Insert
{

    /**
     * select type identifier
     *
     * @var int
     * @ignore
     */
    protected $type = \Yana\Db\Queries\TypeEnumeration::UPDATE;

    /**
     * set source column
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * The second argument applies to columns of type 'array' only.
     * In such case you may provide the array key inside the value
     * of the column that you wish to get.
     * If it is a multidimensional array, you may traverse deper
     * dimensions by linking keys with a dot '.' - for example:
     * "foo.bar" gets $result['foo']['bar'].
     *
     * Note: this will not check if the key that you provided is
     * a valid key or if it really points to a value. If it is not,
     * the resultset will be empty.
     *
     * An E_USER_WARNING is issued if the second argument is
     * provided but the targeted column is not of type 'array'.
     *
     * @param   string  $column         column
     * @return  bool
     * @throws  \Yana\Db\Queries\Exceptions\InvalidSyntaxException   if table has not been initialized
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       if a given argument is invalid
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the given column is not found in the table
     * @return  \Yana\Db\Queries\Update
     */
    public function setColumn($column = '*')
    {
        parent::setColumn($column);
        return $this;
    }

    /**
     * Returns the lower-cased name of the currently selected column.
     *
     * If none has been selected, '*' is returned.
     *
     * Version info: the argument $i became available in 2.9.6.
     * When multiple columns are selected, use this argument to
     * choose the index of the column you want. Where 0 is the
     * the first column, 1 is the second aso.
     * If the argument $i is not provided, the function returns
     * the first column.
     *
     * @param   int  $i  index of column to get
     * @return  string
     */
    public function getColumn($i = 0)
    {
        return parent::getColumn($i);
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
     * @throws  \Yana\Core\Exceptions\NotFoundException  when a column or table does not exist
     * @return  \Yana\Db\Queries\Update
     */
    public function setOrderBy($orderBy, $desc = array())
    {
        settype($orderBy, 'array');
        settype($desc, 'array');
        parent::setOrderBy($orderBy, $desc);
        return $this;
    }

    /**
     * Get the list of columns the resultset is ordered by.
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
     * Returns an array of booleans: true = descending, false = ascending.
     *
     * @return  array
     */
    public function getDescending()
    {
        return parent::getDescending();
    }

    /**
     * Set where clause.
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
     * @param   array  $where  where clause
     * @throws  \Yana\Core\Exceptions\NotFoundException         when a column is not found
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the where-clause contains invalid values
     * @return  \Yana\Db\Queries\Update
     */
    public function setWhere(array $where = array())
    {
        parent::setWhere($where);
        return $this;
    }

    /**
     * get the currently set where clause
     *
     * Returns the current where clause.
     *
     * @return  array
     */
    public function getWhere()
    {
        return parent::getWhere();
    }

    /**
     * check profile constraint
     *
     * @param   mixed   &$value value
     * @return  bool
     * @since   2.9.3
     * @ignore
     * @throws  \Yana\Core\Exceptions\Security\InsufficientRightsException  when the user may not access the profile
     */
    protected function checkProfile(&$value)
    {
        if (!$this->currentTable()->hasProfile()) {
            return true;
        }

        $table = $this->getTable();
        $row = $this->getRow();

        if ($row === '*') {
            assert('!isset($message); // Cannot redeclare $message');
            assert('!isset($level); // Cannot redeclare $level');
            $message = "SQL security restriction. Cannot update a table (only rows and cells).";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            \Yana\Log\LogManager::getLogger()->addLog($message, $level);
            return false;
        }
        /*
         * build query: select profile_id from table where id = "foo"
         */
        assert('!isset($select); // Cannot redeclare variable $select');
        $select = new \Yana\Db\Queries\Select($this->db);
        $select->setTable($table);
        $select->setRow($row);
        $select->setColumn("profile_id");
        $select->setLimit(1);
        try {
            $result = $select->sendQuery();
        } catch (\Yana\Db\Queries\Exceptions\QueryException $e) {
            return false;
        }
        unset($select);

        $resultRow = $result->fetchRow(0);
        assert('is_array($resultRow); // unexpected result: $resultRow');
        $profileId = array_pop($resultRow);
        $builder = new \Yana\ApplicationBuilder();
        $security = $builder->buildApplication()->getSecurity();
        if ($security->checkRules($profileId) !== true) {
            assert('!isset($message); // Cannot redeclare $message');
            assert('!isset($level); // Cannot redeclare $level');
            $message = "The login is valid, but the access rights are not enough to access the function.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Security\InsufficientRightsException($message, $level);
        }
        switch ($this->getExpectedResult())
        {
            case \Yana\Db\ResultEnumeration::ROW:
                if (isset($value['profile_id']) && $value['profile_id'] != $profileId) {
                    assert('!isset($message); // Cannot redeclare $message');
                    assert('!isset($level); // Cannot redeclare $level');
                    $message = "Security restriction. The profile id of an entry may not be changed.";
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                    return false;
                }
                return true;

            case \Yana\Db\ResultEnumeration::CELL:
                if (strcasecmp($this->getColumn(), 'profile_id') === 0) {
                    assert('!isset($message); // Cannot redeclare $message');
                    assert('!isset($level); // Cannot redeclare $level');
                    $message = "Security restriction. The profile id of an entry may not be changed.";
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                    return false;
                }
                return true;

            default:
                return false;
        }
    }

    /**
     * Will retrieve and return the unmodified values.
     *
     * Returns a list of affected rows.
     *
     * @return  array
     */
    public function getOldValues()
    {
        return parent::getOldValues();
    }

    /**
     * Sends the query to the database server and returns a result-object.
     *
     * @return  \Yana\Db\IsResult
     * @since   2.9.3
     * @ignore
     */
    public function sendQuery()
    {
        assert('!isset($message); // Cannot redeclare $message');
        assert('!isset($level); // Cannot redeclare $level');
        $message = "Updating entry '{$this->tableName}.{$this->row}'.";
        $level = \Yana\Log\TypeEnumeration::INFO;
        \Yana\Log\LogManager::getLogger()->addLog($message, $level, $this->getOldValues());

        // send query
        return parent::sendQuery();
    }

    /**
     * Build a SQL-query.
     *
     * @param   string $stmt sql statement
     * @return  string
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     */
    protected function toString($stmt = "UPDATE %TABLE% SET %SET% %WHERE%")
    {
        /*
         * replace %SET%
         *
         * Note: this is done here, since all other types
         * of statements do not have this token.
         */
        if (strpos($stmt, '%SET%') !== false) {
            assert('!isset($set); // Cannot redeclare $set');
            $set = "";
            if ($this->expectedResult === \Yana\Db\ResultEnumeration::ROW) {
                if (is_array($this->values)) {
                    assert('!isset($column); // Cannot redeclare $column');
                    assert('!isset($value); // Cannot redeclare $value');
                    foreach ($this->values as $column => $value)
                    {
                        if (is_null($value)) {
                            continue;
                        }
                        if ($set !== '') {
                            $set .= ', ';
                        }
                        if (is_array($value)) {
                            $set .= $column . ' = ' . $this->db->quote(json_encode($value));
                        } else {
                            $set .= $column . ' = ' . $this->db->quote($value);
                        }
                    }
                    unset($column, $value);
                } else {
                    assert('!isset($message); // Cannot redeclare $message');
                    assert('!isset($level); // Cannot redeclare $level');
                    $message = "No valid values provided in statement: " . $stmt;
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
                }
            } elseif ($this->expectedResult === \Yana\Db\ResultEnumeration::CELL) {
                if (is_array($this->values)) {
                    $set = $this->getColumn() . ' = ' . $this->db->quote(json_encode($this->values));
                } else {
                    $set = $this->getColumn() . ' = ' . $this->db->quote($this->values);
                }
            } else {
                assert('!isset($message); // Cannot redeclare $message');
                assert('!isset($level); // Cannot redeclare $level');
                $message = "No row or cell selected for update in statement: " . $stmt;
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
            }
            $stmt = str_replace('%SET%', $set, $stmt);
            unset($set);
        }

        return parent::toString($stmt);
    }

}

?>