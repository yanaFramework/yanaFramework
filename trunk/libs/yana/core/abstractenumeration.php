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

namespace Yana\Core;

/**
 * <<Enumeration>> Abstract base enumeration class.
 *
 * @package     yana
 * @subpackage  core
 */
abstract class AbstractEnumeration extends \StdClass
{

    /**
     * Prevent instantiation.
     *
     * @final
     */
    final private function __construct()
    {
        // Cannot create an instance of a static 'enumeration' class
    }

    /**
     * Returns an associative array of valid enumeration items.
     *
     * The keys are the names of the class constants as strings.
     * The values are the values of those constants.
     *
     * @return array
     */
    public static function getValidItems()
    {
        $reflection = new \ReflectionClass(new static());
        return $reflection->getConstants();
    }

    /**
     * Check wether the item is a valid member of this enumeration.
     *
     * Returns bool(true), if the value equals the value of a constant of the enumeration class.
     * Returns bool(false) otherwise.
     *
     * @param  string  $item  value of enumeration constant to check
     * @return bool
     */
    public static function isValidItem($item)
    {
        $validItems = static::getValidItems();
        return in_array($item, $validItems);
    }

}

?>