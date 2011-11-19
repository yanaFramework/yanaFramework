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
class UpdateParser extends \Yana\Db\Queries\Parsers\InsertParser implements \Yana\Db\Queries\Parsers\IsParser
{

    /**
     * Parse SQL query into query object.
     *
     * @param   array  $syntaxTree  SQL statement
     * @return  \Yana\Db\Queries\Update
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException      if the query is invalid or could not be parsed
     * @throws  \Yana\Db\Queries\Exceptions\InvalidSyntaxException  when the statement contains illegal values
     */
    public function parseStatement(array $syntaxTree)
    {
        // security check: where clause must not be empty
        if (empty($syntaxTree['where_clause'])) {
            $message = "SQL security restriction. Cannot update a table (only rows and cells).";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }

        $table = current($syntaxTree['tables']); // array of table names
        $keys = $syntaxTree['columns']; // array of column names
        $values = $syntaxTree['values']; // array of value settings
        $where = $syntaxTree['where_clause']; // array of left operand, operator, right operand
        $set = array(); // combined array of $keys and $values

        $query = new \Yana\Db\Queries\Update($this->_getDatabase());
        $query->setTable($table);

        // combine arrays of keys and values
        $set = $this->_parseSet($query, $keys, $values);
        if (empty($set)) {
            $message = 'SQL syntax error. The statement contains illegal values.';
            throw new \Yana\Db\Queries\Exceptions\InvalidSyntaxException($message);
        }
        unset($keys, $values);

        $query->setWhere($this->_parseWhere($where));
        $expectedResult = $query->getExpectedResult();
        $query->setValues($set);

        // check security constraint
        if ($expectedResult !== \Yana\Db\ResultEnumeration::ROW && $expectedResult !== \Yana\Db\ResultEnumeration::CELL) {
            $message = "SQL security restriction. Cannot update a table (only rows and cells).";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
        return $query;
    }

}

?>