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

namespace Yana\Db\Queries\Parsers;

/**
 * Internal Query-Parser.
 *
 * @package     yana
 * @subpackage  db
 */
class DeleteParser extends \Yana\Db\Queries\Parsers\AbstractParser implements \Yana\Db\Queries\Parsers\IsParser
{

    /**
     * Parse SQL query into query object.
     *
     * @param   array  $syntaxTree  SQL statement
     * @return  \Yana\Db\Queries\Delete
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       if the query is invalid or could not be parsed
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   when the table does not exist
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when one of the columns does not exist
     */
    public function parseStatement(array $syntaxTree)
    {
        $table = current($syntaxTree['tables']); // array of table names
        $where = @$syntaxTree['where_clause']; // array of left operand, operator, right operand
        $orderBy = @$syntaxTree['sort_order']; // list of columns (keys) and asc/desc (value)

        /*
         * 1) set table
         */
        $query = new \Yana\Db\Queries\Delete($this->_getDatabase());
        $query->setTable($table);

        /*
         * 2) set order by + direction
         */
        if (!empty($orderBy)) {
            assert('!isset($columnName)', ' Cannot redeclare variable $columnName');
            assert('!isset($direction)', ' Cannot redeclare variable $direction');
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
            $query->setWhere($this->_parseWhere($where));
        }
        return $query;
    }

}

?>