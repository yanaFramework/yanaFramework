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
 * This class is a query builder that can be used to build SQL statements to delete an existing
 * row in a database-table.
 *
 * @package     yana
 * @subpackage  database
 */
class DbDelete extends DbQuery
{

    /**
     * @var int
     * @ignore
     */
    protected $type = DbQueryTypeEnumeration::DELETE;

    /**
     * Set column to sort the resultset by.
     *
     * @param   array  $orderBy  column name / list of column names
     * @param   array  $desc     sort descending (true=yes, false=no)
     * @throws  NotFoundException  when a column or table does not exist
     * @return  DbDelete 
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
     * Check if resultset is sorted in descending order.
     *
     * Returns an array of boolean values: true = descending, false = ascending.
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
     * @throws  NotFoundException         when a column is not found
     * @throws  InvalidArgumentException  when the where-clause contains invalid values
     * @return  DbDelete 
     */
    public function setWhere(array $where = array())
    {
        parent::setWhere($where);
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
     * Set a limit for this query.
     *
     * Note: This setting will not be part of the sql statement produced by __toString().
     * Use the API's $limit and $offset parameter instead when sending
     * the query.
     *
     * This restriction does not apply if you use sendQuery().
     *
     * @param   int  $limit  limit for this query
     * @return  bool
     * @throws  InvalidArgumentException  when limit is not positive
     * @return  DbDelete 
     */
    public function setLimit($limit)
    {
        parent::setLimit($limit);
        return $this;
    }

    /**
     * For update and delete queries this function will retrieve and return the unmodified values.
     *
     * @return  mixed
     */
    public function getOldValues()
    {
        return parent::getOldValues();
    }

    /**
     * Sends the query to the database server and returns a result-object.
     *
     * @return  FileDbResult
     * @since   2.9.3
     * @ignore
     */
    public function sendQuery()
    {
        // logging: backup entry before deleting it
        $message = "Deleting entry '{$this->tableName}.{$this->row}'.";
        Log::report($message, E_USER_NOTICE, $this->getOldValues());

        // send query
        $result = parent::sendQuery();

        // delete old files and upload new
        if (!$this->db->isError($result)) {
            $files = $this->currentTable()->getFileColumns();
            if (!empty($files)) {
                $this->deleteFiles($files);
            }
        }

        // return result object
        return $result;
    }

    /**
     * Build a SQL-query.
     *
     * @param   string $stmt sql statement
     * @return  string
     */
    public function __toString($stmt = "DELETE FROM %TABLE% %WHERE% %ORDERBY%")
    {
        return parent::__toString($stmt);
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
     * @return  DbDelete
     * @throws  InvalidArgumentException  if the query is invalid or could not be parsed
     */
    public static function parseSQL($sqlStmt, DbStream $database)
    {
        // this is a parser/lexer, that parses a given SQL string into an AST
        if (!is_array($sqlStmt)) {
            assert('is_string($sqlStmt); // Wrong argument type for argument 1. String expected.');
            $parser = new \SQL_Parser();
            $sqlStmt = $parser->parse($sqlStmt); // get abstract syntax tree (AST)
        }

        $table = current($sqlStmt['tables']); // array of table names
        $where = @$sqlStmt['where_clause']; // array of left operand, operator, right operand
        $orderBy = @$sqlStmt['sort_order']; // list of columns (keys) and asc/desc (value)

        /*
         * 1) set table
         */
        $query = new self($database);
        $query->setTable($table);

        /*
         * 2) set order by + direction
         */
        if (!empty($orderBy)) {
            assert('!isset($columnName); // Cannot redeclare variable $columnName');
            assert('!isset($direction); // Cannot redeclare variable $direction');
            foreach ($orderBy as $columnName => $direction)
            {
                $query->addOrderBy($columnName, $direction == 'desc');
            }
            unset($columnName, $direction);
        }

        /*
         * 3) where clause
         */
        if (!empty($where)) {
            $query->setWhere($query->parseWhere($where));
        }
        return $query;
    }
}

?>