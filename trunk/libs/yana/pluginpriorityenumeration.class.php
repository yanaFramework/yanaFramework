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

/**
 * <<Enumeration>> PluginPriority
 *
 * Values for plugin priority
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 * @ignore
 */
class PluginPriorityEnumeration
{

    const LOWEST = 0;
    const LOW = 1;
    const NORMAL = 2;
    const HIGH = 3;
    const HIGHEST = 4;

    /**
     * get enumeration item from string representation
     *
     * Every enumeration item has an equivalent string representation that can be used within
     * annotation inside a PHP doc block.
     * This function associates them with their responsive integer values.
     *
     * @access  public
     * @static
     * @param   string  $string  text representation to convert
     * @return  int
     */
    public static function fromString($string)
    {
        switch (mb_strtolower((string) $string))
        {
            case 'lowest':
            case '0':
                return self::LOWEST;
            break;
            case 'low':
            case '1':
                return self::LOW;
            break;
            case 'normal':
            case '2':
                return self::NORMAL;
            break;
            case 'high':
            case '3':
                return self::HIGH;
            break;
            case 'highest':
            case '4':
                return self::HIGHEST;
            break;
            default:
                return self::NORMAL;
            break;
        }
    }

}

?>