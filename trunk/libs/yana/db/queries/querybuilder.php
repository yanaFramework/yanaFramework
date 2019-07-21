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

namespace Yana\Db\Queries;

/**
 * <<builder>> Query builder.
 *
 * This internal helper class is meant to help facades and decorators
 * to create new query-object through simplified APIs.
 *
 * @package     yana
 * @subpackage  db
 */
class QueryBuilder extends \Yana\Core\Object implements \Yana\Db\Queries\IsQueryBuilder
{

    /**
     * @var \Yana\Db\IsConnection 
     */
    private $_connection = null;

    /**
     * <<construct>> Create a new instance.
     *
     * @param  \Yana\Db\IsConnection $connection  open database connection
     */
    public function __construct(\Yana\Db\IsConnection $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Create Select statement.
     *
     * This selects the values at adress $key starting from $offset and limited to $limit results.
     *
     * $orderBy may either be:
     * <ul>
     *     <li>
     *         the name of the column in the current table to order the resultset by.
     *     </li>
     *     <li>
     *         a numeric array of strings, where each element
     *         is the name of a column in the current table.
     *         The resultset will get ordered by the values of these columns
     *         in the direction in which they are provided.
     *     </li>
     * </ul>
     *
     * The parameter $where follows this syntax:
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
     * @param   string  $key      the address of the value(s) to retrieve
     * @param   array   $where    where clause
     * @param   array   $orderBy  a list of columns to order the resultset by
     * @param   int     $offset   the number of the first result to be returned
     * @param   int     $limit    maximum number of results to return
     * @param   bool    $desc     if true results will be ordered in descending, otherwise in ascending order
     * @return  \Yana\Db\Queries\Select
     */
    public function select($key, array $where = array(), $orderBy = array(), $offset = 0, $limit = 0, $desc = false)
    {
        assert('is_string($key); // Wrong argument type for argument 1. String expected.');
        $selectQuery = new \Yana\Db\Queries\Select($this->_connection);

        $selectQuery->setKey($key);

        if (!empty($where)) {
            $selectQuery->setWhere($where);
        }
        if (!empty($orderBy) || $desc === true) {
            $selectQuery->setOrderBy($orderBy, $desc);
        }

        /*
         * 2.2) set limit and offset
         */
        if ($offset > 0) {
            $selectQuery->setOffset($offset);
        }
        if ($limit > 0) {
            $selectQuery->setLimit($limit);
        }
        return $selectQuery;
    }

    /**
     * Create Update statement.
     *
     * @param   string  $key    the address of the row that should be updated
     * @param   mixed   $value  value
     * @return  \Yana\Db\Queries\Update
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException          when a given argument is invalid
     * @throws  \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException  when the primary key is invalid or ambigious
     * @throws  \Yana\Db\Queries\Exceptions\ConstraintException         when a constraint violation is detected
     * @throws  \Yana\Db\Queries\Exceptions\InvalidResultTypeException  when trying to insert anything but a row.
     * @throws  \Yana\Core\Exceptions\NotWriteableException             when a target column or table is not writeable
     * @throws  \Yana\Core\Exceptions\NotFoundException                 when the column definition is invalid
     * @throws  \Yana\Core\Exceptions\NotImplementedException           when a column was encountered that has an unknown datatype
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException       when a given value is not valid
     * @throws  \Yana\Core\Exceptions\Forms\InvalidSyntaxException      when a value does not match a required pattern or syntax
     * @throws  \Yana\Core\Exceptions\Forms\MissingFieldException       when a not-nullable column is missing
     * @throws  \Yana\Core\Exceptions\Forms\FieldNotFoundException      when a value was provided but no corresponding column exists
     * @throws  \Yana\Core\Exceptions\Files\SizeException               when an uploaded file is too large
     */
    public function update($key, $value = array())
    {
        assert('is_string($key); // wrong argument type for argument 1, string expected');

        $updateQuery = new \Yana\Db\Queries\Update($this->_connection);
        $updateQuery->setKey($key);
        $updateQuery->setValues($value);
        return $updateQuery;
    }

    /**
     * Create Insert statement.
     *
     * @param   string  $key    the address of the row that should be inserted
     * @param   mixed   $value  value
     * @return  \Yana\Db\Queries\Insert
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException          if a given argument is invalid
     * @throws  \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException  when the primary key is invalid or ambigious
     * @throws  \Yana\Db\Queries\Exceptions\ConstraintException         when a constraint violation is detected
     * @throws  \Yana\Db\Queries\Exceptions\InvalidResultTypeException  when trying to insert anything but a row.
     */
    public function insert($key, $value = array())
    {
        assert('is_string($key); // wrong argument type for argument 1, string expected');

        $insertQuery = new \Yana\Db\Queries\Insert($this->_connection);
        $insertQuery->setKey($key);
        $insertQuery->setValues($value); // may throw exception
        return $insertQuery;
    }

    /**
     * Create Delete statement.
     *
     * The parameter $where follows this syntax:
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
     * @param   string  $key    the address of the row that should be removed
     * @param   array   $where  where clause
     * @param   int     $limit  maximum number of rows to remove
     * @return  \Yana\Db\Queries\Delete
     */
    public function remove($key, array $where = array(), $limit = 1)
    {
        assert('is_int($limit); // Wrong argument type $limit. Integer expected.');
        assert('$limit >= 0; // Invalid argument $limit. Value must be greater or equal 0.');
        assert('is_string($key); // Wrong argument type $key. String expected.');
        assert('!isset($deleteQuery); // Cannot redeclare var $deleteQuery');
        $deleteQuery = new \Yana\Db\Queries\Delete($this->_connection);
        $deleteQuery->setLimit((int) $limit);
        $deleteQuery->setKey($key);
        $deleteQuery->setWhere($where);
        return $deleteQuery;
    }

    /**
     * Create Select statement to count the number of entries inside a table.
     *
     * @param   string  $table  name of a table
     * @param   array   $where  optional where clause
     * @return  \Yana\Db\Queries\SelectCount
     * @throws  \Yana\Db\Exceptions\TableNotFoundException
     */
    public function length($table, array $where = array())
    {
        assert('is_string($table); // Wrong argument type $table. String expected.');

        $countQuery = new \Yana\Db\Queries\SelectCount($this->_connection);
        $countQuery->setTable($table); // throws Exception
        $countQuery->setWhere($where);

        return $countQuery;
    }

    /**
     * Create Select statement to check, wether a certain element exists.
     *
     * @param   string  $key    adress to check
     * @param   array   $where  optional where clause
     * @return  \Yana\Db\Queries\SelectExist
     */
    public function exists($key, array $where = array())
    {
        assert('is_string($key); // Wrong argument type for argument 1. String expected');

        // build query to check key
        $existQuery = new \Yana\Db\Queries\SelectExist($this->_connection);
        $existQuery->setKey(\mb_strtolower($key));
        if (!empty($where)) {
            $existQuery->setWhere($where);
        }

        return $existQuery;
    }

}

?>