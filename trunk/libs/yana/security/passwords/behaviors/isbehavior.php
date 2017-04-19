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

namespace Yana\Security\Passwords\Behaviors;

/**
 * <<interface>> Implements password behavior.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsBehavior
{

    /**
     * Compare recovery id with recovery id of current user.
     *
     * Returns bool(true) if the id is correct an bool(false) otherwise.
     *
     * @param   string  $recoveryId  user password recovery id
     * @return  bool
     */
    public function checkRecoveryId($recoveryId);

    /**
     * Compare password with password of current user.
     *
     * Returns bool(true) if the password is correct an bool(false) otherwise.
     *
     * @param   string  $userPwd  user password
     * @return  bool
     */
    public function checkPassword($userPwd);

    /**
     * Change password.
     *
     * Set login password to $password for current user.
     *
     * @param   string  $password  non-empty alpha-numeric text with optional special characters
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function changePassword($password);

    /**
     * Change password.
     *
     * A new random password is auto-generated, applied to the user and then returned.
     *
     * @return  string
     */
    public function generateRandomPassword();

    /**
     * Create new password recovery id.
     *
     * When the user requests a new password, a recovery id is created and the time is stored.
     * This is to ensure that the user is a allowed to reset the password and determine, when the
     * request has expired.
     *
     * Returns the new recovery id.
     *
     * @return  string
     */
    public function generatePasswordRecoveryId();

    /**
     * Get wrapped user.
     *
     * @return  \Yana\Security\Data\IsUser
     */
    public function getUser();

    /**
     * Replaces currently wrapped user.
     *
     * @param   \Yana\Security\Data\IsUser  $user  entity to wrap
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function setUser(\Yana\Security\Data\IsUser $user);

}

?>