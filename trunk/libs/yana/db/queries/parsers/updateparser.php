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
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException     if the query is invalid or could not be parsed
     * @throws  \Yana\Db\Queries\Exceptions\NotSupportedException  when the statement contains an unsupported feature
     */
    public function parseStatement(array $syntaxTree)
    {
        // security check: where clause must not be empty
        if (empty($syntaxTree['where_clause'])) {
            $message = "SQL security restriction. Cannot update a table (only rows and cells).";
            throw new \Yana\Db\Queries\Exceptions\NotSupportedException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        $table = $this->_mapTableName($syntaxTree); // currently we support only one table

        $query = new \Yana\Db\Queries\Update($this->_getDatabase());
        $query->setTable($table);

        $where = $syntaxTree['where_clause']; // array of left operand, operator, right operand
        $query->setWhere($this->_parseWhere($where));

        $set = $this->_mapSet($query, $syntaxTree); // array of value settings
        $query->setValues($set);
        return $query;
    }

    /**
     * Returns SET clause as a row-array.
     *
     * @param   \Yana\Db\Queries\Insert  $query       Query object to modify
     * @param   array                    $syntaxTree  abstract syntax tree as provided by SQL_Parser
     * @return  array
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when given column does not exist
     */
    protected function _mapSet(\Yana\Db\Queries\AbstractQuery $query, array $syntaxTree)
    {
        assert('!isset($keys); // Cannot redeclare variable $keys');
        $keys = array();
        assert('!isset($values); // Cannot redeclare variable $values');
        $values = array();

        if (isset($syntaxTree['sets']) && is_array($syntaxTree['sets'])) {

            assert('!isset($set); // Cannot redeclare variable $set');
            assert('!isset($value); // Cannot redeclare variable $value');
            foreach ($syntaxTree['sets'] as $set)
            {
                if (isset($set['name']) && isset($set['value']['args']) && is_array($set['value']['args'])) {

                    $keys[] = is_array($set['name']) && isset($set['name']['column']) ? (string) $set['name']['column'] : (string) $set['name'];
                    $value = current($set['value']['args']);
                    $values[] = is_array($value) ?  isset($value['value']) ? (string) $value['value'] : "" : (string) $value;
                }
            }
            unset($set, $value);
        }
        // combine arrays of keys and values
        return $this->_parseSet($query, $keys, $values); // combined array of $keys and $values
    }

}

?>