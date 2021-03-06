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
class SelectCount extends \Yana\Db\Queries\SelectExist implements \Yana\Db\Queries\IsCountQuery
{

    /**
     * @var int
     * @ignore
     */
    protected $type = \Yana\Db\Queries\TypeEnumeration::COUNT;

    /**
     * set source column
     *
     * Checks if the column exists and sets the source column
     * of the query to the given value.
     *
     * @param   string  $column  column name
     * @param   string  $alias   optional column alias
     * @throws  \Yana\Db\Queries\Exceptions\InvalidSyntaxException   if table has not been initialized
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the given column is not found in the table
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       if a given argument is invalid
     * @return  $this
     */
    public function setColumn(string $column = '*', string $alias = "")
    {
        parent::setColumnWithAlias($column, $alias);
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
     * Returns lower-cased names of the selected columns as a numeric array of strings.
     *
     * If none has been selected, an empty array is returned.
     *
     * @return  array
     */
    public function getColumns(): array
    {
        return parent::getColumns();
    }

    /**
     * Build a SQL-query.
     *
     * @return  string
     */
    protected function toString(): string
    {
        $serializer = new \Yana\Db\Queries\QuerySerializer();
        return $serializer->fromCountQuery($this);
    }

    /**
     * Returns bool(true) if the result set is not empty.
     *
     * This sends the query statement to the database and returns bool(true)
     * if the requested database object exists and bool(false) otherwise.
     *
     * @return  bool
     */
    public function doesExist(): bool
    {
        return $this->countResults() > 0;
    }

    /**
     * Get the number of entries.
     *
     * This sends the query statement to the database and returns the results.
     * The return type depends on the query settings, see {@see DbQuery::getExpectedResult()}.
     *
     * @return  int
     */
    public function countResults(): int
    {
        try {
            
            $result = $this->sendQuery();

        } catch (\Yana\Db\Queries\Exceptions\QueryException $e) {

            $message = "Statement '$this' on database failed: " . \get_class($e) . ' ' . $e->getMessage();
            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING, $e);
            return 0;
        }

        $rowCount = $result->fetchOne();
        assert(is_null($rowCount) || is_numeric($rowCount));

        return (int) $rowCount;
    }

}

?>