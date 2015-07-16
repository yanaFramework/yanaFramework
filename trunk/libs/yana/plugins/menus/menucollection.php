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

namespace Yana\Plugins\Menus;

/**
 * <<collection>> Provides menu information.
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class MenuCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Add a new Menu to the collection.
     *
     * @param   scalar  $offset  mapper id
     * @param   \Yana\Plugins\Menus\IsMenu  $value  menu that shoud be added
     * @return  \Yana\Plugins\Menus\IsMenu
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not a mapper
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof \Yana\Plugins\Menus\IsMenu) {
            $message = "Instance of \Yana\Plugins\Menus\IsMenu expected. " .
                "Found " . gettype($value) . "(" . get_class($value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        return $this->_offsetSet($offset, $value);
    }

}

?>