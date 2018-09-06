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
class SelectCountParser extends \Yana\Db\Queries\Parsers\AbstractParser implements \Yana\Db\Queries\Parsers\IsParser
{

    /**
     * Parse SQL query into query object.
     *
     * @param   array  $syntaxTree  SQL statement
     * @return  \Yana\Db\Queries\SelectCount
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     */
    public function parseStatement(array $syntaxTree)
    {
        $query = new \Yana\Db\Queries\SelectCount($this->_getDatabase());

        // retrieve table
        $tables = $this->_mapTableList($syntaxTree);
        if (empty($tables)) {
            $message = "SQL-statement has no table names.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        } elseif (count($tables) > 1) {
            $message = "Row-Counts are not supported on joined tables.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        $table = current($tables);
        if (is_array($table) && isset($table["table"])) {
            $table = $table["table"];
        }
        $query->setTable((string) $table);

        // retrieve column
        $columns = $this->_mapColumnList($syntaxTree); // array of column names
        if (empty($columns)) {
            $message = "SQL-statement has no column names.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);

        } elseif (count($columns) > 1) {
            $message = "Row-Counts are not supported on more than one column.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);

        } elseif (!isset($columns[0]) || !isset($columns[0]['args']) || !isset($columns[0]['args'][0]) || !isset($columns[0]['args'][0]['name'])) {
            $message = "Function name expected.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);

        } elseif (!isset($columns[0]['args'][0]['arg'])) {
            $message = "Column name expected for function count.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);

        } elseif ($columns[0]['args'][0]['name'] !== 'count') {
            $message = "Function 'count' expected for 'Select count(foo) ...'-statement. " .
                "Found '{$columns[0]['args'][0]['name']}' instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
        $column = current($columns[0]['args'][0]['arg']);
        if ($column != '*') {
            $query->setColumn($column);
        }

        // retrieve where clause
        if (!empty($syntaxTree['where_clause'])) {
            // array of left operand, operator, right operand
            $query->setWhere($this->_parseWhere($syntaxTree['where_clause']));
        }

        return $query;
    }

}

?>