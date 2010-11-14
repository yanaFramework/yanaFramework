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
 * <<Enumeration>> PluginActivity
 *
 * Values for plugin activity status
 *
 * @access      public
 * @name        PluginActivity
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
class PluginActivityEnumeration
{
    /**
     * plugin is not active
     */
    const INACTIVE = 0;

    /**
     * plugin is active
     */
    const ACTIVE = 1;

    /**
     * plugin is active by default
     */
    const DEFAULT_ACTIVE = 2;

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
    public static function getActiveState($string)
    {
        assert('is_string($string); // Wrong type for argument 1. String expected');

        switch (mb_strtolower($string))
        {
            case 'active':
            case '1':
                return PluginActivityEnumeration::ACTIVE;
            break;
            case 'inactive':
            case '0':
                return PluginActivityEnumeration::INACTIVE;
            break;
            case 'always':
            case 'always_active':
            case 'always active':
            case 'default_active':
            case 'default active':
            case '2':
                return PluginActivityEnumeration::DEFAULT_ACTIVE;
            break;
            default:
                return PluginActivityEnumeration::INACTIVE;
            break;
        }
    }
}

?>
