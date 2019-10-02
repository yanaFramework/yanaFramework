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
interface IsQueryWithColumn extends \Yana\Db\Queries\IsQuery
{

    /**
     * set source column
     *
     * Checks if the column exists and sets the source column
     * of the query to the given value.
     *
     * @param   string  $column  column name
     * @throws  \Yana\Db\Queries\Exceptions\InvalidSyntaxException   if table has not been initialized
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  if the given column is not found in the table
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException       if a given argument is invalid
     * @return  $this
     */
    public function setColumn(string $column = '*');

    /**
     * Returns the lower-cased name of the currently selected column.
     *
     * If none has been selected, '*' is returned.
     *
     * Version info: the argument $i became available in 2.9.6.
     * When multiple columns are selected, use this argument to
     * choose the index of the column you want. Where 0 is the
     * the first column, 1 is the second aso.
     * If the argument $i is not provided, the function returns
     * the first column.
     *
     * @param   scalar  $i  index of column to get
     * @return  string
     */
    public function getColumn(): string;

}

?>