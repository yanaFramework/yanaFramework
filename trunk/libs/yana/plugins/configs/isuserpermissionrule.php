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

namespace Yana\Plugins\Configs;

/**
 * <<interface>> User permission management information.
 *
 * Access management comes in 3-layers, each of which is optional.
 * <ul>
 *  <li> User groups: like "Sales", "Human_Ressources" </li>
 *  <li> User role: like "Project_Manager" </li>
 *  <li> Security level: an integer of 0 through 100 </li>
 * </ul>
 *
 * @package     yana
 * @subpackage  plugins
 */
interface IsUserPermissionRule extends \Yana\Security\Rules\Requirements\IsRequirement
{
    // intentionally left blank
}

?>