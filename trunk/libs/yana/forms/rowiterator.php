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

namespace Yana\Forms;

/**
 * <<iterator>> for table rows.
 *
 * An outer iterator, that allows to iterate over the rows of a table layout
 *
 * @package     yana
 * @subpackage  form
 */
class RowIterator extends \Yana\Core\AbstractCollection
{

    /**
     * Insert or replace a row.
     *
     * @param   string  $offset  index of item to replace
     * @param   array   $value   the row you wish to add
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not an array
     * @return  array
     */
    public function offsetSet($offset, $value)
    {
        if (is_array($value)) {
            return $this->_offsetSet($offset, $value);
        } else {
            $message = "Array expected. Found " . gettype($value) . " instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
    }

}

?>