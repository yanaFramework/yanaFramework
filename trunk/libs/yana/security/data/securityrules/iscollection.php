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

namespace Yana\Security\Data\SecurityRules;

/**
 * Collection of security rules.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsCollection extends \Yana\Core\IsCollection
{

    /**
     * Check for group+role combination.
     *
     * Returns bool(true) if the collection contains a rule that has a combination
     * of this group and role.
     *
     * Returns bool(false) otherwise.
     *
     * @param   string  $group  user group
     * @param   string  $role   user role
     * @return  bool
     */
    public function hasGroupAndRole($group, $role);

    /**
     * Check for role.
     *
     * Returns bool(true) if the collection contains a rule that has the role.
     *
     * Returns bool(false) otherwise.
     *
     * @param   string  $role  user role
     * @return  bool
     */
    public function hasRole($role);

    /**
     * Check for group.
     *
     * Returns bool(true) if the collection contains a rule that has the group.
     *
     * Returns bool(false) otherwise.
     *
     * @param   string  $group  user group
     * @return  bool
     */
    public function hasGroup($group);
            
}

?>