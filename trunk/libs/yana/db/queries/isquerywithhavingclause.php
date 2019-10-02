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
 * <<interface>> Queries that have a where clause.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsQueryWithHavingClause extends \Yana\Db\Queries\IsQuery
{

    /**
     * add having clause (filter)
     *
     * The syntax is as follows:
     * array(0=>column,1=>value,2=>operator)
     * Where "operator" can be one of the following:
     * '=', 'REGEXP', 'LIKE', '<', '>', '!=', '<=', '>='
     *
     * @param   array  $having       having clause
     * @param   bool   $isMandatory  switch between operators (true='AND', false='OR')
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   when a referenced table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when a referenced column is not found
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       when the having-clause contains invalid values
     * @return  $this
     */
    public function addHaving(array $having, bool $isMandatory = true);

    /**
     * set having clause (filter)
     *
     * The syntax is as follows:
     * <ol>
     * <li> left operand </li>
     * <li> operator </li>
     * <li> right operand </li>
     * </ol>
     *
     * List of supported operators:
     * <ul>
     * <li> and, or (indicates that both operands are sub-clauses) </li>
     * <li> =, !=, <, <=, >, >=, like, regexp </li>
     * </ul>
     *
     * Note that not all DBMS support the operator "regexp".
     * Also note that this simulation uses the Perl-compatible regular
     * expressions syntax (PCRE).
     *
     * Example:
     * <code>
     * array(
     *     array('col1', '=', 'val1'),
     *     'and',
     *     array(
     *         array('col2', '<', 1),
     *         'or',
     *         array('col2', '>', 3)
     *     )
     * )
     * </code>
     *
     * The example above translates to: col1 = 'val1' and (col2 < 1 or col2 > 3).
     *
     * To unset the having clause, call this function without
     * providing a parameter.
     *
     * The having clause uses the same syntax as the where clause.
     * The difference between 'where' and 'having' is that 'where' is executed during the execution
     * of the statement, while 'having' is executed on the result set.
     *
     * Thus 'having' may not access any columns which are not present in the result set, while
     * the 'where' clause may not access any column that is only present in the result set but not
     * in the table.
     *
     * If you have a choice you should always prefer 'where' over 'having', as using a where clause
     * usually is faster and consums less memory than using the same having clause.
     *
     * @param   array  $having  having clause
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException   when a referenced table is not found
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when a referenced column is not found
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       when the having-clause contains invalid values
     * @return  $this
     */
    public function setHaving(array $having = array());

    /**
     * Returns the current having clause.
     *
     * @return  array
     */
    public function getHaving(): array;

}

?>