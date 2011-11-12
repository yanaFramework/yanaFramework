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
 * <<interface, strategy>> For menu layout helpers.
 *
 * @package     yana
 * @subpackage  templates
 */
interface IsLayout
{

    /**
     * <<smarty function>> print unordered list
     *
     * Print an array using a tree menu.
     *
     * Example for $value:
     * <code>
     * $A = array(
     *     '1.html' => 'Link',
     *     'Menu 1' => array(
     *         '2_1.html' => '1) Entry',
     *         '2_2.html' => '2) Entry',
     *         'Menü 2' => array(
     *             '2_3_1.html' => '1) Entry',
     *             '2_3_2.html' => '2) Entry'
     *         ),
     *     ),
     *     'Menu 3' => array(
     *         '3_1.html' => '1) Entry',
     *         '3_2.html' => '2) Entry',
     *         '3_2.html' => '3) Entry'
     *     ),
     * );
     * </code>
     *
     * @param   array   $array      list contents (see example above)
     * @param   int     $keys       convert keys to href: 0 = no, 1 = yes, 2 = don't print keys at all
     * @param   bool    $allowHtml  allow HTML values
     * @return  string
     * @see     \Yana\Templates\Helpers\Html\MenuLayouts\KeyEnumeration
     */
    public function __invoke(array $array, $keys = KeyEnumeration::DONT_CONVERT_HREF, $allowHtml = false);

}

?>