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
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException          when the query is invalid or could not be parsed
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException       when the statement contains illegal values
     * @throws  \Yana\Db\Queries\Exceptions\NotFoundException           when a table or column wasn't found
     * @throws  \Yana\Db\Queries\Exceptions\NotSupportedException       when we encounter an unsupported feature in the given SQL
     * @throws  \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException  when trying to insert a row without a primary key
     */
    public function parseStatement(array $syntaxTree)
    {
        $table = $this->_mapTableName($syntaxTree);
        $query = new \Yana\Db\Queries\Insert($this->_getDatabase());
        $query->setTable($table); // may throw \Yana\Db\Exceptions\TableNotFoundException

        $keys = $this->_mapColumnNames($syntaxTree); // array of column names
        if (empty($keys)) {
            $keys = $this->_getDatabase()->getSchema()->getTable($table)->getColumnNames();
        }

        // @todo Currently this is limited to the "current" row of values. For multiple inserts we need to go through all of them in a loop.
        if (isset($syntaxTree['values']) && count($syntaxTree['values']) > 1) {
            $message = "Inserting more than one row in a single statement is not yet supported.";
            throw new \Yana\Db\Queries\Exceptions\NotSupportedException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
        $values = $this->_mapValues(current($syntaxTree['values']));

        // combine arrays of keys and values
        $set = $this->_parseSet($query, $keys, $values); // combined array of $keys and $values
        assert('!empty($set); // Cannot be empty - the parser must not allow ');
        $query->setValues($set); // may throw \Yana\Core\Exceptions\InvalidArgumentException or \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException
        unset($keys, $values);

        return $query;
    }

    /**
     * Prepare value-arrays by unpacking 'value'.
     *
     * @param   array  $values  array of value settings
     * @return  array
     */
    protected function _mapValues(array $values)
    {
        assert('!isset($value); // Cannot redeclare var $value');
        assert('!isset($i); // Cannot redeclare var $i');
        foreach ($values as $i => $value)
        {
            if (array_key_exists('value', $value)) {
                $values[$i] = $value['value'];
            }
        }
        unset($i, $value);
        return $values;
    }

    /**
     * combine a list of keys and values
     *
     * Returns the row-array on success.
     * On failure an empty array is returned.
     *
     * @param   \Yana\Db\Queries\Insert  $query   Query object to modify
     * @param   array                    $keys    keys
     * @param   array                    $values  values
     * @return  array
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when given column does not exist
     * @ignore
     */
    protected function _parseSet(\Yana\Db\Queries\AbstractQuery $query, array $keys, array $values)
    {
        assert('count($keys) >= count($values);');

        // combine keys and values
        $set = array();
        $table = $this->_getDatabase()->getSchema()->getTable($query->getTable());
        assert('!isset($column); // Cannot redeclare var $column');
        assert('!isset($i); // Cannot redeclare var $i');
        for ($i = 0; $i < count($keys); $i++)
        {
            if (!array_key_exists($i, $values)) {
                break;
            }
            $column = $table->getColumn($keys[$i]);
            if (! $column instanceof \Yana\Db\Ddl\Column) {
                $message = "Column '" . $keys[$i] . "' does not exist in table '" . $query->getTable() . "'.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Db\Queries\Exceptions\ColumnNotFoundException($message, $level);
            }
            if ($column->getType() === 'array') {
                $set[mb_strtoupper((string) $keys[$i])] = json_decode($values[$i]);
            } else {
                $set[mb_strtoupper((string) $keys[$i])] = $values[$i];
            }
        } // end foreach
        unset($i, $column);

        assert('is_array($set);');
        return $set;
    }

}

?>