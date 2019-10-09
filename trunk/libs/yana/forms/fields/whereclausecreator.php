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

namespace Yana\Forms\Fields;

/**
 * <<helper>> This helper class 'helps' to create where clauses for search forms based on given values.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
class WhereClauseCreator extends \Yana\Forms\Fields\AbstractWhereClauseCreator
{

    /**
     * <<constructor>> Create new instance.
     *
     * @param  \Yana\Db\Ddl\Column  $column     base column definition
     * @param  string               $tableName  base table name
     */
    public function __construct(\Yana\Db\Ddl\Column $column, $tableName)
    {
        assert(is_string($tableName), 'Invalid argument $tableName: string expected');
        $this->_setColumn($column);
        $this->_setTableName($tableName);
    }

    /**
     * Build left operand of where clause.
     *
     * Contains tabe name and column name.
     *
     * @return  array
     */
    private function _buildLeftOperand()
    {
        return array($this->getTableName(), $this->getColumn()->getName());
    }

    /**
     * Build where clause for boolean value.
     *
     * @return  array
     */
    protected function _buildBoolClause()
    {
        switch ($this->getValue())
        {
            case 'true':
                $rightOperand = true;
            break;
            case 'false':
                $rightOperand = false;
            break;
            default:
                return null;
        }
        return array($this->_buildLeftOperand(), '=', $rightOperand);
    }

    /**
     * Build where clause for enum/set values.
     *
     * @return  array
     */
    protected function _buildListClause()
    {
        $value = $this->getValue();
        if (!is_array($value) || empty($value)) {
            return null;
        }
        $validItems = $this->getColumn()->getEnumerationItemNames();
        // prevent use of invalid items (possible injection)
        $rightOperand = array_intersect($value, $validItems);
        if (empty($rightOperand)) {
            return null;
        }
        assert(is_array($rightOperand), 'is_array($rightOperand)');
        return array($this->_buildLeftOperand(), 'IN', $rightOperand);
    }

    /**
     * Build where clause for time/date ranges.
     *
     * The range is created ONLY if the client has provided the argument "ACTIVE" with the string value "true".
     *
     * @return  array
     */
    protected function _buildTimeRangeClause()
    {
        $value = $this->getValue();
        $min = $this->getMinValue();
        $max = $this->getMaxValue();
        switch (true)
        {
            case !is_array($value):
            case !isset($value['ACTIVE']): // this is a parameter provided by the FRONTEND. Be aware of this dependency!
            case $value['ACTIVE'] !== 'true':
            case !is_array($min):
            case !isset($min['MONTH']):
            case !isset($min['DAY']):
            case !isset($min['YEAR']):
            case !is_array($max):
            case !isset($max['MONTH']):
            case !isset($max['DAY']):
            case !isset($max['YEAR']):
            return null;
        }

        $minTime = mktime(0, 0, 0, $min['MONTH'], $min['DAY'], $min['YEAR']);
        $maxTime = mktime(23, 59, 59, $max['MONTH'], $max['DAY'], $max['YEAR']);

        $leftOperand = $this->_buildLeftOperand();
        return array(array($leftOperand, '>=', $minTime), 'AND', array($leftOperand, '<=', $maxTime));
    }

    /**
     * Build where clause for int/float ranges.
     *
     * @return  array
     */
    protected function _buildNumberRangeClause()
    {
        $leftOperand = $this->_buildLeftOperand();
        $min = $this->getMinValue();
        $max = $this->getMaxValue();
        if (\is_numeric($min) && \is_numeric($max) && $min <= $max) {
            $minOperand = (float) $min;
            $maxOperand = (float) $max;

            if ($minOperand === $maxOperand) {
                $operator = '=';
                $rightOperand = (float) $min;

            } else {
                $operator = 'AND';
                $rightOperand = array($leftOperand, '<=', $maxOperand);
                $leftOperand = array($leftOperand, '>=', $minOperand);

            }
        } elseif (\is_numeric($min)) {
            $operator = '>=';
            $rightOperand = (float) $min;

        } elseif (\is_numeric($max)) {
            $operator = '<=';
            $rightOperand = (float) $max;

        } else {
            return null;
        }
        return array($leftOperand, $operator, $rightOperand);
    }

    /**
     * Build where clause for strings (default).
     *
     * @return  array
     */
    protected function _buildStringClause()
    {
        $value = $this->getValue();
        if (is_null($value) || $value === '') {
            return null;
        }
        $string = strtr((string) $value, '*?', '%_'); // translate wildcards
        return array($this->_buildLeftOperand(), 'LIKE', \Yana\Util\Strings::htmlSpecialChars($string));
    }

    /**
     * Get value as where clause.
     *
     * This function returns an array of (leftOperand, operator, rightOperand),
     * which may be used to set a where clause on a database query object.
     *
     * If the value is empty, the function returns NULL instead.
     *
     * @return  array
     */
    public function __invoke()
    {
        /**
         * Switch by column's type
         */
        switch ($this->getColumn()->getType())
        {
            case \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL:
                return $this->_buildBoolClause();

            case \Yana\Db\Ddl\ColumnTypeEnumeration::ENUM:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::SET:
                return $this->_buildListClause();

            case 'time':
            case 'timestamp':
            case 'date':
                return $this->_buildTimeRangeClause();

            case \Yana\Db\Ddl\ColumnTypeEnumeration::INT:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::RANGE:
                return $this->_buildNumberRangeClause();
        }
        return $this->_buildStringClause();
    }

}

?>