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
 *
 * @ignore
 */

namespace Yana\Io\Adapters;

/**
 * <<Interface>> Data Adapter
 *
 * The DataAdapter is used to inject a dependency into the {@see AbstractDataContainer}.
 *
 * @package     yana
 * @subpackage  io
 */
interface IsDataBaseAdapter extends \Yana\Io\Adapters\IsDataAdapter
{

    /**
     * Removes the given entity from the database.
     *
     * @param   \Yana\Io\Adapters\IsEntity  $entity  compose the where clause based on this object
     */
    public function delete(\Yana\Io\Adapters\IsEntity $entity);

    /**
     * DELETE all entries WHERE "column" = 'value'.
     *
     * @param   string  $columnName  name of column to search in
     * @param   scalar  $value       used for where clause
     * @return  int
     */
    public function deleteByColumn($columnName, $value);

    /**
     * SELECTs all entries WHERE "column" = 'value'.
     *
     * @param   string  $columnName  name of column to search in
     * @param   scalar  $value       used for where clause
     * @return  \Yana\Io\Adapters\IsEntity[]
     */
    public function findItemsByColumn($columnName, $value);

    /**
     * Analyzes the given entity and returns all items with similar properties.
     *
     * What is considered "similar" depends on the implementation.
     *
     * @param   \Yana\Io\Adapters\IsEntity  $entity  compose the where clause based on this object
     * @return  \Yana\Io\Adapters\IsEntity[]
     */
    public function findSimilarItems(\Yana\Io\Adapters\IsEntity $entity);

    /**
     * SELECTs all entries WHERE "column" = 'value'.
     *
     * @return  \Yana\Io\Adapters\IsEntity[]
     */
    public function getAllItems();

}

?>