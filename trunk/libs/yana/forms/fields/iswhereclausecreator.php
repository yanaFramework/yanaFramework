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
 * <<interface>> This helper class 'helps' to create where clauses for search forms based on given values.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
interface IsWhereClauseCreator
{

    /**
     * Get column definition.
     *
     * @return  \Yana\Db\Ddl\Column
     */
    public function getColumn();

    /**
     * Get table definition.
     *
     * @return  string
     */
    public function getTableName();

    /**
     * Get column value.
     *
     * @return  mixed
     */
    public function getValue();

    /**
     * Set column value.
     *
     * @param   mixed  $value  of column
     * @return  $this
     */
    public function setValue($value);

    /**
     * Get min value for range.
     *
     * @return  array
     */
    public function getMinValue();

    /**
     * Get max value for range.
     *
     * @return  mixed
     */
    public function getMaxValue();

    /**
     * Set start value for range.
     *
     * @param   mixed  $minValue  of range
     * @return  $this
     */
    public function setMinValue($minValue);

    /**
     * Set end value for range.
     *
     * @param   mixed  $maxValue  of range
     * @return  $this
     */
    public function setMaxValue($maxValue);

    /**
     * Get value as where clause.
     *
     * This function returns an array of (leftOperand, operator, rightOperand),
     * which may be used to set a where clause on a database query object.
     *
     * If the value is empty, the function returns NULL instead.
     *
     * @return  array|null
     */
    public function __invoke();

}

?>