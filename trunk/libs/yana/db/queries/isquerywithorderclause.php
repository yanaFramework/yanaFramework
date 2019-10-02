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
 * <<interface>> For queries that can be order by columns.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsQueryWithOrderClause extends \Yana\Db\Queries\IsQuery
{

    /**
     * set column to sort the resultset by
     *
     * @param   array  $orderBy  list of column names
     * @param   array  $desc     sort descending (true=yes, false=no)
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException  when the base table does not exist
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException when the column does not exist
     * @return  $this
     */
    public function setOrderBy(array $orderBy, array $desc = array());

    /**
     * get the list of columns the resultset is ordered by
     *
     * Returns a lower-cased list of column names.
     * If none has been set yet, then the list is empty.
     *
     * @return  array
     */
    public function getOrderBy(): array;

    /**
     * Returns an array of boolean values: true = descending, false = ascending.
     *
     * @return  array
     */
    public function getDescending(): array;

}

?>