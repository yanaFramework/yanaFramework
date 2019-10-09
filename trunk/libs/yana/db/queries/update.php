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
 * This class is a query builder that can be used to build SQL statements to update existing
 * rows or cells in a database-table.
 *
 * Note: this class does NOT untaint input data for you.
 *
 * @package     yana
 * @subpackage  db
 */
class Update extends \Yana\Db\Queries\Insert implements \Yana\Db\Queries\IsUpdateQuery
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
     * @param   string  $column  column name or '*' for 'all'
     * @return  $this
     * @throws  \Yana\Db\Queries\Exceptions\InvalidSyntaxException   if table has not been initialized
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       if a given argument is invalid
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the given column is not found in the table
     * @return  $this
     */
    public function setColumn(string $column = '*')
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
     * @param   scalar  $i  index of column to get
     * @return  string
     */
    public function getColumn($i = null): string
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
    public function getArrayAddress(): string
    {
        return parent::getArrayAddress();
    }

    /**
     * set column to sort the resultset by
     *
     * @param   array  $orderBy  list of column names
     * @param   array  $desc     sort descending (true=yes, false=no)
     * @throws  \Yana\Core\Exceptions\NotFoundException  when a column or table does not exist
     * @return  $this
     */
    public function setOrderBy(array $orderBy, array $desc = array())
    {
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
    public function getOrderBy(): array
    {
        return parent::getOrderBy();
    }

    /**
     * Returns an array of booleans: true = descending, false = ascending.
     *
     * @return  array
     */
    public function getDescending(): array
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
     * @return  $this
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
     * @return  $this
     */
    public function addWhere(array $where)
    {
        parent::addWhere($where);
        return $this;
    }

    /**
     * get the currently set where clause
     *
     * Returns the current where clause.
     *
     * @return  array
     */
    public function getWhere(): array
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
    protected function checkProfile(&$value): bool
    {
        if (!$this->currentTable()->hasProfile()) {
            return true;
        }

        $table = $this->getTable();
        $row = $this->getRow();

        if ($row === '*') {
            assert(!isset($message), 'Cannot redeclare $message');
            assert(!isset($level), 'Cannot redeclare $level');
            $message = "SQL security restriction. Cannot update a table (only rows and cells).";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            \Yana\Log\LogManager::getLogger()->addLog($message, $level);
            return false;
        }
        /*
         * build query: select profile_id from table where id = "foo"
         */
        assert(!isset($select), 'Cannot redeclare variable $select');
        $select = new \Yana\Db\Queries\Select($this->getDatabase());
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
        assert(is_array($resultRow), 'unexpected result: $resultRow');
        $profileId = array_pop($resultRow);
        $builder = new \Yana\ApplicationBuilder();
        $security = $builder->buildApplication()->getSecurity();
        if ($security->checkRules($profileId) !== true) {
            assert(!isset($message), 'Cannot redeclare $message');
            assert(!isset($level), 'Cannot redeclare $level');
            $message = "The login is valid, but the access rights are not enough to access the function.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Security\InsufficientRightsException($message, $level);
        }
        switch ($this->getExpectedResult())
        {
            case \Yana\Db\ResultEnumeration::ROW:
                if (isset($value['profile_id']) && $value['profile_id'] != $profileId) {
                    assert(!isset($message), 'Cannot redeclare $message');
                    assert(!isset($level), 'Cannot redeclare $level');
                    $message = "Security restriction. The profile id of an entry may not be changed.";
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                    return false;
                }
                return true;

            case \Yana\Db\ResultEnumeration::CELL:
                if (strcasecmp($this->getColumn(), 'profile_id') === 0) {
                    assert(!isset($message), 'Cannot redeclare $message');
                    assert(!isset($level), 'Cannot redeclare $level');
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
    public function getOldValues(): array
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
    public function sendQuery(): \Yana\Db\IsResult
    {
        assert(!isset($message), 'Cannot redeclare $message');
        $message = "Updating entry '" . $this->getTable() . "." . $this->getRow() . "'.";
        \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::INFO, $this->getOldValues());

        // send query
        return parent::sendQuery();
    }

    /**
     * Build a SQL-query.
     *
     * @return  string
     */
    protected function toString(): string
    {
        $serializer = new \Yana\Db\Queries\QuerySerializer();
        return $serializer->fromUpdateQuery($this);
    }

}

?>