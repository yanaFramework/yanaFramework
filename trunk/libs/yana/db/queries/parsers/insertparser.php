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
class InsertParser extends \Yana\Db\Queries\Parsers\AbstractParser implements \Yana\Db\Queries\Parsers\IsParser
{

    /**
     * Parse SQL query into query object.
     *
     * @param   array  $syntaxTree  SQL statement
     * @return  \Yana\Db\Queries\Insert
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException      if the query is invalid or could not be parsed
     * @throws  \Yana\Db\Queries\Exceptions\InvalidSyntaxException  when the statement contains illegal values
     */
    public function parseStatement(array $syntaxTree)
    {
        $table = current($syntaxTree['tables']); // array of table names
        $keys = $syntaxTree['columns']; // array of column names
        $values = $syntaxTree['values']; // array of value settings
        $set = array(); // combined array of $keys and $values

        $query = new \Yana\Db\Queries\Insert($this->_getDatabase());
        $query->setTable($table);

        // combine arrays of keys and values
        $set = $this->_parseSet($query, $keys, $values);
        if (empty($set)) {
            $message = 'SQL syntax error. The statement contains illegal values.';
            throw new \Yana\Db\Queries\Exceptions\InvalidSyntaxException($message);
        }
        unset($keys, $values);

        // set values
        $query->setValues($set);

        // check security constraint
        if ($query->getExpectedResult() !== \Yana\Db\ResultEnumeration::ROW) {
            if (!$query->table->getColumn($query->table->getPrimaryKey())->isAutoFill()) {
                $message = "SQL security restriction. Cannot insert a table (only rows).";
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
            }
        }
        return $query;
    }

    /**
     * combine a list of keys and values
     *
     * Returns the row-array on success.
     * On failure an empty array is returned.
     *
     * @param   \Yana\Db\Queries\Insert $query   Query object to modify
     * @param   array                   $keys    keys
     * @param   array                   $values  values
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given column does not exist
     * @ignore
     */
    protected function _parseSet(\Yana\Db\Queries\AbstractQuery $query, array $keys, array $values)
    {
        assert('count($keys) == count($values);');
        // prepare values
        assert('!isset($value); // Cannot redeclare var $value');
        assert('!isset($i); // Cannot redeclare var $i');
        foreach ($values as $i => $value)
        {
            if (array_key_exists('value', $value)) {
                $values[$i] = $value['value'];
            }
        }
        unset($i, $value);
        // combine keys and values
        $set = array();
        $table = $this->_getDatabase()->getSchema()->getTable($query->getTable());
        assert('!isset($column); // Cannot redeclare var $column');
        assert('!isset($i); // Cannot redeclare var $i');
        for ($i = 0; $i < count($keys); $i++)
        {
            $column = $table->getColumn($keys[$i]);
            if (! $column instanceof \Yana\Db\Ddl\Column) {
                throw new \Yana\Core\Exceptions\InvalidArgumentException("Column '" . $keys[$i] . "' does not exist " .
                    "in table '" . $query->getTable() . "'.", E_USER_WARNING);
            }
            if ($column->getType() === 'array') {
                $set[mb_strtoupper($keys[$i])] = json_decode($values[$i]);
            } else {
                $set[mb_strtoupper($keys[$i])] = $values[$i];
            }
        } // end foreach
        unset($i, $column);

        assert('is_array($set);');
        return $set;
    }

}

?>