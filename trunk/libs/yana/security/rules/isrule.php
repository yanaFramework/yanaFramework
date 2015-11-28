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
 * <<interface>> Security rule.
 *
 * @package     yana
 * @subpackage  security
 */
interface IsRule
{

    /**
     * Rule implementation.
     *
     * When called, rules must return bool(true) if the user ist granted permission
     * to proceed with the requested action and bool(false) if not.
     *
     * If the function returns anything else than a boolean value, it will be considered invalid and ignored.
     *
     * Rules may not throw any exception or error.
     *
     * The list of $required permissions is an array containing the following information (in the given order):
     * <ul>
     *   <li> \Yana\Plugins\Annotations\Enumeration::GROUP  required user group </li>
     *   <li> \Yana\Plugins\Annotations\Enumeration::ROLE   required user role </li>
     *   <li> \Yana\Plugins\Annotations\Enumeration::LEVEL  required security level </li>
     * </ul>
     *
     * Note: Rules do NOT implement password checks.
     * Those are implemented elsewhere ({@see \Yana\Security\Passwords\Checks\IsCheck}).
     *
     * @param   \Yana\Db\IsConnection  $database   open connection to user database
     * @param   array                  $required   list of required permissions
     * @param   string                 $profileId  current application-profile id
     * @param   string                 $action     name of the action the user tries to execute
     * @param   string                 $userName   username (may be empty if not logged in)
     * @return  bool
     */
    public function __invoke(\Yana\Db\IsConnection $database, array $required, $profileId, $action, $userName);

}

?>