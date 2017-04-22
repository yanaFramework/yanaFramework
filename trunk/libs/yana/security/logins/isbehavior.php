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

namespace Yana\Security\Logins;

/**
 * <<interface>> Login manager.
 *
 * To handle logins and logouts of users by adjusting the session settings and cookies that go with them.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsBehavior
{

    /**
     * Check if user is logged in.
     *
     * Returns bool(true) if the user is currently
     * logged in and bool(false) otherwise.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  bool
     */
    public function isLoggedIn(\Yana\Security\Data\Users\IsEntity $user);

    /**
     * Handle user logins.
     *
     * Destroys any previous session (to prevent session fixation).
     * Creates new session id and updates the user's session information in the database.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  self
     * @throws  \Yana\Core\Exceptions\Security\InvalidLoginException  when access is denied
     */
    public function handleLogin(\Yana\Security\Data\Users\IsEntity $user);

    /**
     * Destroy the current session and clear all session data.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  self
     */
    public function handleLogout(\Yana\Security\Data\Users\IsEntity $user);

}

?>