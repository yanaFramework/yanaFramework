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

namespace Yana\Data\Adapters;

/**
 * <<adapter>> data adapter
 *
 * Null adapter, that basically does nothing at all.
 * Use this for UnitTests!
 *
 * @package     yana
 * @subpackage  data
 */
class ArrayAdapter extends \Yana\Core\AbstractCountableArray implements \Yana\Data\Adapters\IsDataAdapter
{

    /**
     * Adds the item if it is missing.
     *
     * Same as:
     * <code>
     * $array[] = $subject;
     * </code>
     *
     * @param  \Yana\Data\Adapters\IsEntity  $entity  what you want to add
     */
    public function saveEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        if (!\in_array($entity, $this->_getItems())) {
            $this[] = $entity;
        }
    }

    /**
     * Return array of ids in use.
     *
     * @return  array
     */
    public function getIds()
    {
        return \array_keys($this->_getItems());
    }

}

?>