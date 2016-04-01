<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana\Http\Uploads;

/**
 * <<collection>> Of uploaded files.
 *
 * @package     yana
 * @subpackage  http
 */
class FileCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Store new value in collection.
     *
     * @param   scalar                     $offset  where to place the value (may also be empty)
     * @param   \Yana\Http\Uploads\IsFile  $value   new value to store
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not valid
     * @return  \Yana\Http\Uploads\FileCollection
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof \Yana\Http\Uploads\IsFile) {
            $message = "File expected. Found " . gettype($value) . "(" .
                ((is_object($value)) ? get_class($value) : $value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        return $this->_offsetSet($offset, $value);
    }

    /**
     * Set a list of items
     *
     * @param  array  $items  list of items to work on
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not valid
     */
    public function setItems(array $items = array())
    {
        parent::setItems();
        foreach ($items as $offset => $value)
        {
            $this->offsetSet($offset, $value);
        }
    }

}

?>