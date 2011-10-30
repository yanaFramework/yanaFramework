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
class DbSelectCount extends DbSelectExist
{

    /**
     * @var int
     * @ignore
     */
    protected $type = DbQueryTypeEnumeration::COUNT;

    /**
     * set source column
     *
     * Checks if the column exists and sets the source column
     * of the query to the given value.
     *
     * @param   string  $column           column name
     * @name    DbQuery::setColumn()
     * @see     DbQuery::setColumns()
     * @throws  DbEventLog                                      if table has not been initialized
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if a given argument is invalid
     * @throws  \Yana\Core\Exceptions\NotFoundException         if the given column is not found in the table
     * @return  DbSelectCount 
     */
    public function setColumn($column = '*')
    {
        parent::setColumn($column);
        return $this;
    }

    /**
     * set array address
     *
     * Applies to columns of type 'array' only.
     *
     * You may provide the array key inside the value of the column that you wish to get.
     * If it is a multidimensional array, you may traverse in deeper dimensions by linking
     * keys with a dot '.' - for example: "foo.bar" gets $result['foo']['bar'].
     *
     * Note: this will not check if the key that you provided is
     * a valid key or if it really points to a value. If it is not,
     * the resultset will be empty.
     *
     * @param   string  $arrayAddress   array address
     * @name    DbQuery::setArrayAddress()
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if a given argument is invalid
     * @return  DbSelectCount 
     * @ignore
     */
    public function setArrayAddress($arrayAddress = "")
    {
        parent::setArrayAddress($arrayAddress);
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
     * @param   int     $i  index of column to get
     * @return  string
     */
    public function getColumn($i = null)
    {
        return parent::getColumn($i);
    }

    /**
     * Returns lower-cased names of the selected columns as a numeric array of strings.
     *
     * If none has been selected, an empty array is returned.
     *
     * @return  array
     */
    public function getColumns()
    {
        return parent::getColumns();
    }

    /**
     * Build a SQL-query.
     *
     * @param   string  $stmt  sql statement template
     * @return  string
     */
    protected function toString($stmt = "SELECT count(%COLUMN%) FROM %TABLE% %WHERE%")
    {
        /* replace %COLUMN% */
        if ($this->getColumn() === '*') {
            $stmt = str_replace('%COLUMN%', '*', $stmt);

        } else {
            assert('!isset($column); // Cannot redeclare $column');
            $column = "";

            assert('!isset($alias); // Cannot redeclare var $alias');
            assert('!isset($item); // Cannot redeclare var $item');
            foreach ($this->getColumns() as $alias => $item)
            {
                if (is_array($item)) {
                    if ($column !== "") {
                        $column .= ', ';
                    }
                    $column .= $this->db->quoteId(YANA_DATABASE_PREFIX.$item[0]) . '.' . $this->db->quoteId($item[1]);
                    if (is_string($alias)) {
                        $column .= " as " .$this->db->quoteId($alias);
                    }
                    /* When selecting a column, the framework automaticall adds the primary key as second column.
                     * This second column must be dropped for sub-queries or otherwise the query will fail.
                     */
                    if ($this->isSubQuery && $this->getExpectedResult() === DbResultEnumeration::COLUMN) {
                        break;
                    }
                }
            }
            unset($alias, $item);

            $stmt = str_replace('%COLUMN%', $column, $stmt);
            unset($column);
        }

        return parent::toString($stmt);

    }

    /**
     * Get the number of entries.
     *
     * This sends the query statement to the database and returns the results.
     * The return type depends on the query settings, see {@see DbQuery::getExpectedResult()}.
     *
     * @return  int
     */
    public function countResults()
    {
        $result = $this->sendQuery();
        if ($this->db->isError($result)) {
            throw new DbWarningLog("Statement '$this' on database failed", E_USER_WARNING, $result);
            return 0;
        }

        if (!defined('MDB2_FETCHMODE_ORDERED')) {
            /** @ignore */
            define('MDB2_FETCHMODE_ORDERED', 1);
        }
        $i = $result->fetchRow(MDB2_FETCHMODE_ORDERED, 0);
        if (is_null($i) || !isset($i[0])) {
            return 0;
        } else {
            return (int) $i[0];
        }
    }

    /**
     * parse SQL query into query object
     *
     * This is the opposite of __toString().
     * It takes a SQL query string as input and returns
     * a query object of the specific type that
     * corresponds to the given type of query.
     *
     * The result object is always a subclass of DbQuery.
     *
     * @param   string    $sqlStmt   SQL statement
     * @param   DbStream  $database  database connection
     * @return  DbSelectCount
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     * @throws  ParserError                                     when the SQL statement is invalid
     */
    public static function parseSQL($sqlStmt, DbStream $database)
    {
        // this is a parser/lexer, that parses a given SQL string into an AST
        if (!is_array($sqlStmt)) {
            assert('is_string($sqlStmt); // Wrong argument type for argument 1. String expected.');
            $parser = new \SQL_Parser();
            $sqlStmt = $parser->parse($sqlStmt); // get abstract syntax tree (AST)
        }

        $query = new self($database);

        // retrieve table
        $tables = $sqlStmt['tables'];
        if (empty($tables)) {
            return new \Yana\Core\Exceptions\InvalidArgumentException("SQL-statement has no table names: $sqlStmt.", E_USER_WARNING);
        } elseif (count($tables) > 1) {
            $message = "Row-Counts are not supported on joined tables.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
        $query->setTable(current($tables));

        // retrieve column
        $function = current($sqlStmt['set_function']); // array of column names
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
        if (!empty($sqlStmt['where_clause'])) {
            // array of left operand, operator, right operand
            $query->setWhere($query->parseWhere($sqlStmt['where_clause']));
        }

        return $query;
    }

}

?>