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

namespace Yana\Security\Rules;

/**
 * <<interface>> Rule checking class.
 *
 * Allows collection and checking of security rules.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsChecker
{

    /**
     * Add security rule.
     *
     * This method adds a user-definded implementation to a list of custom security checks.
     *
     * To execute these rules call checkPermission().
     * The rules are executed in the order in which they were added.
     *
     * @param   \Yana\Security\Rules\IsRule  $rule  must be a valid callback
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException when the function is not callable
     * @return  \Yana\Security\Rules\Checker
     */
    public function addSecurityRule(\Yana\Security\Rules\IsRule $rule);

    /**
     * Check permission.
     *
     * Check if user has permission to apply changes to the profile identified
     * by the argument $profileId.
     *
     * Returns bool(true) if the user's permission level is high enough to
     * execute the changes and bool(false) otherwise.
     *
     * @param   string  $profileId  profile id
     * @param   string  $action     action
     * @param   string  $userName   user name
     * @return  bool
     * @ignore
     */
    public function checkPermission($profileId = null, $action = null, $userName = null);

}

?>