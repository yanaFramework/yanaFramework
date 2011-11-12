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

namespace Yana\Templates\Helpers\Html\MenuLayouts;

/**
 * <<enumeration>> Helps to select correct values for the $key parameter.
 *
 * @package     yana
 * @subpackage  templates
 * @see         \Yana\Templates\Helpers\Html\MenuLayouts\IsLayout
 */
class KeyEnumeration extends \Yana\Core\AbstractEnumeration
{

    /**
     * Don't convert array keys to hyperlinks.
     */
    const DONT_CONVERT_HREF = 0;

    /**
     * Convert URLs in array keys to hyperlinks.
     */
    const CONVERT_HREF = 1;

    /**
     * Don't output keys at all.
     */
    const DONT_PRINT_KEYS = 2;

}

?>