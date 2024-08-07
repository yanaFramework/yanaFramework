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
declare(strict_types=1);

namespace Yana\Data\Adapters;

/**
 * <<Interface>> Data Adapter
 *
 * The DataAdapter is used to inject a dependency into the {@see AbstractDataContainer}.
 *
 * @package     yana
 * @subpackage  data
 */
interface IsDataAdapter extends \Yana\Core\IsCountableArray
{

    /**
     * Return an array of all valid identifiers.
     *
     * @return  array
     */
    public function getIds();

    /**
     * Persists the given entity.
     *
     * @param  \Yana\Data\Adapters\IsEntity  $entity  object to persist
     */
    public function saveEntity(\Yana\Data\Adapters\IsEntity $entity);

}

?>
