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
abstract class AbstractWhereClauseCreator extends \Yana\Core\StdObject implements \Yana\Forms\Fields\IsWhereClauseCreator
{

    /**
     * @var  \Yana\Db\Ddl\Column
     */
    private $_column = null;

    /**
     * @var  string
     */
    private $_tableName = "";

    /**
     * @var  mixed
     */
    private $_value = null;

    /**
     * @var  mixed
     */
    private $_minValue = array('MONTH' => 1, 'DAY' => 1, 'YEAR' => 1970);

    /**
     * @var  mixed
     */
    private $_maxValue = array('MONTH' => 31, 'DAY' => 12, 'YEAR' => 2040);

    /**
     * Set column definition.
     *
     * @param   \Yana\Db\Ddl\Column  $column  base column definition
     * @return  $this
     */
    protected function _setColumn(\Yana\Db\Ddl\Column $column)
    {
        $this->_column = $column;
        return $this;
    }

    /**
     * Get column definition.
     *
     * @return  \Yana\Db\Ddl\Column
     */
    public function getColumn()
    {
        return $this->_column;
    }

    /**
     * Set table definition.
     *
     * The table name is changed to lower case.
     *
     * @param   string  $tableName  base table name
     * @return  $this
     */
    protected function _setTableName($tableName)
    {
        assert('is_string($tableName); // Invalid argument $tableName: string expected');
        $this->_tableName = \Yana\Util\Strings::toLowerCase((string) $tableName);
        return $this;
    }

    /**
     * Get table definition.
     *
     * @return  string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * Get column value.
     *
     * @return  mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set column value.
     *
     * @param   mixed  $value  of column
     * @return  $this
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * Get min value for range.
     *
     * @return  array
     */
    public function getMinValue()
    {
        return $this->_minValue;
    }

    /**
     * Get max value for range.
     *
     * @return  mixed
     */
    public function getMaxValue()
    {
        return $this->_maxValue;
    }

    /**
     * Set start value for range.
     *
     * @param   mixed  $minValue  of range
     * @return  $this
     */
    public function setMinValue($minValue)
    {
        $this->_minValue = $minValue;
        return $this;
    }

    /**
     * Set end value for range.
     *
     * @param   mixed  $maxValue  of range
     * @return  $this
     */
    public function setMaxValue($maxValue)
    {
        $this->_maxValue = $maxValue;
        return $this;
    }

}

?>