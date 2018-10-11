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
 * This class is a query builder that can be used to build SQL statements to delete an existing
 * row in a database-table.
 *
 * @package     yana
 * @subpackage  db
 */
class Delete extends \Yana\Db\Queries\AbstractQuery
{

    /**
     * @var int
     * @ignore
     */
    protected $type = \Yana\Db\Queries\TypeEnumeration::DELETE;

    /**
     * Set column to sort the resultset by.
     *
     * @param   array  $orderBy  column name / list of column names
     * @param   array  $desc     sort descending (true=yes, false=no)
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException  when the base table does not exist
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException when the column does not exist
     * @return  \Yana\Db\Queries\Delete 
     */
    public function setOrderBy($orderBy, $desc = array())
    {
        settype($orderBy, 'array');
        settype($desc, 'array');
        parent::setOrderBy($orderBy, $desc); // throws exception
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
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException         when a referenced table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException        when a referenced column is not found
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the where-clause contains invalid values
     * @return  \Yana\Db\Queries\Delete 
     */
    public function setWhere(array $where = array())
    {
        parent::setWhere($where); // throws exception
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
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when limit is not positive
     * @return  \Yana\Db\Queries\Delete 
     */
    public function setLimit($limit)
    {
        parent::setLimit($limit);
        return $this;
    }

    /**
     * Will retrieve and return the row that is to be deleted.
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
     * @codeCoverageIgnore
     */
    public function sendQuery()
    {
        // logging: backup entry before deleting it
        $message = "Deleting entry '{$this->tableName}.{$this->row}'.";
        $level = \Yana\Log\TypeEnumeration::INFO;
        \Yana\Log\LogManager::getLogger()->addLog($message, $level, $this->getOldValues());

        // send query
        $result = parent::sendQuery();

        // delete old files and upload new
        $files = $this->currentTable()->getFileColumns();
        if (!empty($files)) {
            $this->deleteFiles($files);
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
    public function toString($stmt = "DELETE FROM %TABLE% %WHERE% %ORDERBY%")
    {
        return parent::toString($stmt);
    }

}

?>