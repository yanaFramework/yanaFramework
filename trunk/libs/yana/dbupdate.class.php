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
 * This class is a query builder that can be used to build SQL statements to update existing
 * rows or cells in a database-table.
 *
 * Note: this class does NOT untaint input data for you.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DbUpdate extends DbInsert
{
    /**
     * select type identifier
     *
     * @access  protected
     * @var int
     * @ignore
     */
    protected $type = DbQuery::UPDATE;

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
     * @access  public
     * @param   string  $column         column
     * @return  bool
     * @throws  DbEventLog                if table has not been initialized
     * @throws  InvalidArgumentException  if a given argument is invalid
     * @throws  NotFoundException         if the given column is not found in the table
     */
    public function setColumn($column = '*')
    {
        parent::setColumn($column);
    }

    /**
     * get the currently selected column
     *
     * Returns the lower-cased name of the currently
     * selected column.
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
     * @access  public
     * @param   int     $i  index of column to get
     * @return  string
     */
    public function getColumn($i = 0)
    {
        return parent::getColumn($i);
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
        settype($orderBy, 'array');
        settype($desc, 'array');
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
     * @access  public
     * @param   array  $where  where clause
     * @throws  NotFoundException         when a column is not found
     * @throws  InvalidArgumentException  when the where-clause contains invalid values
     */
    public function setWhere(array $where = array())
    {
        parent::setWhere($where);
    }

    /**
     * get the currently set where clause
     *
     * Returns the current where clause.
     *
     * @access  public
     * @return  array
     */
    public function getWhere()
    {
        return parent::getWhere();
    }

    /**
     * check profile constraint
     *
     * @access  protected
     * @param   mixed   &$value value
     * @return  bool
     * @since   2.9.3
     * @ignore
     */
    protected function checkProfile(&$value)
    {
        if (!$this->currentTable()->hasProfile()) {
            return true;
        }

        $table = $this->getTable();
        $row = $this->getRow();

        if ($row === '*') {
            $message = "SQL security restriction. Cannot update a table (only rows and cells).";
            trigger_error($message, E_USER_WARNING);
            return false;
        }
        /*
         * build query: select profile_id from table where id = "foo"
         */
        assert('!isset($select); /* Cannot redeclare variable $select */');
        $select = new DbSelect($this->db);
        $select->setTable($table);
        $select->setRow($row);
        $select->setColumn("profile_id");
        $select->setLimit(1);
        $result = $select->sendQuery();
        unset($select);
        /*
         * handle result
         */
        if ($this->db->isError($result)) {
            $message = "Unable to update entry {$table}.{$row}.\n\t\t".$result->getMessage();
            trigger_error($message, E_USER_WARNING);
            return false;
        }

        if (!defined('MDB2_FETCHMODE_ASSOC')) {
            /** @ignore */
            define('MDB2_FETCHMODE_ASSOC', 2);
        }
        $resultRow = $result->fetchRow(MDB2_FETCHMODE_ASSOC, 0);
        assert('is_array($resultRow); /* unexpected result: $resultRow */');
        $profileId = array_pop($resultRow);
        $session = SessionManager::getInstance();
        if ($session->checkPermission($profileId) !== true) {
            Message::report("", "InsufficientRightsWarning");
            return false;
        }
        switch ($this->getExpectedResult())
        {
            case YANA_DB_ROW:
                if (isset($value['profile_id']) && $value['profile_id'] != $profileId) {
                    Log::report("Security restriction. " .
                        "The profile id of an entry may not be changed.", E_USER_WARNING);
                    return false;
                } else {
                    return true;
                }
            break;
            case YANA_DB_CELL:
                if (strcasecmp($this->getColumn(), 'profile_id') === 0) {
                    Log::report("Security restriction. " .
                        "The profile id of an entry may not be changed.", E_USER_WARNING);
                    return false;
                } else {
                    return true;
                }
            break;
            default:
                return false;
            break;
        }
    }

    /**
     * Get old values
     *
     * For update and delete queries this function will retrieve and return the unmodified values.
     *
     * @access  public
     * @return  mixed
     */
    public function getOldValues()
    {
        return parent::getOldValues();
    }

    /**
     * send query to server
     *
     * This sends the query to the database and returns a result-object.
     *
     * @access  public
     * @return  FileDbResult
     * @since   2.9.3
     * @ignore
     */
    public function sendQuery()
    {
        $message = "Updating entry '{$this->tableName}.{$this->row}'.";
        Log::report($message, E_USER_NOTICE, $this->getOldValues());

        // send query
        return parent::sendQuery();
    }

    /**
     * build a SQL-query
     *
     * @access  public
     * @param   string $stmt sql statement
     * @return  string
     */
    public function toString($stmt = "UPDATE %TABLE% SET %SET% %WHERE%")
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
            if ($this->expectedResult === YANA_DB_ROW) {
                if (is_array($this->values)) {
                    assert('!isset($column); // Cannot redeclare $column');
                    assert('!isset($value);  // Cannot redeclare $value');
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
                    trigger_error("Cannot build update statement. No valid values provided.", E_USER_WARNING);
                    return "";
                }
            } elseif ($this->expectedResult === YANA_DB_CELL) {
                if (is_array($this->values)) {
                    $set = $this->getColumn() . ' = ' . $this->db->quote(json_encode($this->values));
                } else {
                    $set = $this->getColumn() . ' = ' . $this->db->quote($this->values);
                }
            } else {
                trigger_error("Cannot build update statement. No row or cell selected for update.", E_USER_WARNING);
                return "";
            }
            $stmt = str_replace('%SET%', $set, $stmt);
            unset($set);
        }

        return parent::toString($stmt);

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
     * @return  DbUpdate
     * @throws  InvalidArgumentException  if the query is invalid or could not be parsed
     */
    public static function parseSQL($sqlStmt, DbStream $database)
    {
        // this is a parser/lexer, that parses a given SQL string into an AST
        if (!is_array($sqlStmt)) {
            assert('is_string($sqlStmt); // Wrong argument type for argument 1. String expected.');
            $parser = new SQL_Parser();
            $sqlStmt = $parser->parse($sqlStmt); // get abstract syntax tree (AST)
        }

        // security check: where clause must not be empty
        if (empty($sqlStmt['where_clause'])) {
            $message = "SQL security restriction. Cannot update a table (only rows and cells).";
            throw new InvalidArgumentException($message, E_USER_WARNING);
        }

        $table = current($sqlStmt['tables']); // array of table names
        $keys = $sqlStmt['columns']; // array of column names
        $values = $sqlStmt['values']; // array of value settings
        $where = $sqlStmt['where_clause']; // array of left operand, operator, right operand
        $set = array(); // combined array of $keys and $values

        $query = new self($database);
        $query->setTable($table);

        // combine arrays of keys and values
        $set = $query->parseSet($keys, $values);
        if (empty($set)) {
            $message = 'SQL syntax error. The statement contains illegal values.';
            throw new DbWarningLog($message);
        }
        unset($keys, $values);

        $query->setWhere($query->parseWhere($where));
        $expectedResult = $query->getExpectedResult();
        $query->setValues($set);

        // check security constraint
        if ($expectedResult !== YANA_DB_ROW && $expectedResult !== YANA_DB_CELL) {
            $message = "SQL security restriction. Cannot update a table (only rows and cells).";
            throw new InvalidArgumentException($message, E_USER_WARNING);
        }
        return $query;
    }
}

?>