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

namespace Yana\Security\Users\Behaviors;

/**
 * <<interface>> User behavior facade.
 *
 * Holds user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsBehavior
{

    /**
     * Get the name of the user as a string.
     *
     * @return  string
     */
    public function getId();

    /**
     * Get session-checksum.
     *
     * Will return NULL if the user never did a login before.
     * Otherwise it will return the checksum of the least recently used session.
     * 
     * @return  string
     */
    public function getSessionCheckSum();

    /**
     * Update language.
     *
     * Sets prefered language of the user, that is used to provide translates GUI elements.
     *
     * @param   string  $language  language or locale string
     * @return  self
     */
    public function setLanguage($language);

    /**
     * Get selected language.
     *
     * @return  string
     */
    public function getLanguage();

    /**
     * Get failure count.
     *
     * Returns the number of times the user entered an invalid password recently.
     * Note: This number is reset, when the user inserts a valid password.
     *
     * The default is 0.
     *
     * @return  int
     */
    public function getFailureCount();

    /**
     * Get failure time.
     *
     * Returns the timestamp when user last entered an invalid password.
     * Note: This number is reset, when the user inserts a valid password.
     *
     * The default is 0.
     *
     * @return  int
     */
    public function getFailureTime();

    /**
     * Get login count.
     *
     * Returns the number of times the user sucessfully logged-in.
     *
     * The default is 0.
     * @return  int
     */
    public function getLoginCount();

    /**
     * Get the timestamp when user last sucessfully logged-in.
     *
     * Note: This number is not reset on log-out.
     * Thus you cannot use this settings to check if a user is currently logged-in.
     *
     * The default is 0.
     *
     * @return  int
     */
    public function getLoginTime();

    /**
     * Get mail address.
     *
     * @return  string
     */
    public function getMail();

    /**
     * Update expert setting.
     *
     * Set to bool(true) if the user prefers to see expert applications settings and bool(false)
     * if a simpler GUI is prefered.
     *
     * @param   bool  $isExpert  use expert settings (yes/no)
     * @return  \Yana\Security\Users\Facade
     */
    public function setExpert($isExpert);

    /**
     * User prefers expert settings.
     *
     * Returns bool(true) if the user prefers to see expert applications settings and bool(false)
     * if a simpler GUI is prefered.
     *
     * @return  string
     */
    public function isExpert();

    /**
     * Update expert setting.
     *
     * Set to bool(true) if the user should be able to log-in or to bool(false) if the user
     * should be deactivated (suspended) without permanently deleting the user settings.
     *
     * @param   bool  $isActive  use expert settings (yes/no)
     * @return  \Yana\Security\Users\Facade
     */
    public function setActive($isActive);

    /**
     * User is active.
     *
     * Returns bool(true) if the user is activated and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isActive();

    /**
     * Get the time when the user was created.
     *
     * @return  int
     */
    public function getTimeCreated();

    /**
     * Get time when password was last changed.
     *
     * This returns the timestamp for when the password was last updated.
     * You may use this to determine if the password hasn't changed within a long time and prompt
     * the user to enter a new one.
     *
     * The default is 0.
     *
     * @return  int
     */
    public function getPasswordChangedTime();

    /**
     * Get list of 10 recent passwords.
     *
     * This returns a list of MD5-encoded password strings that the user used recently.
     * The list does NOT include the current password.
     *
     * Use this to enforce that the user does not reuse a password multiple times.
     *
     * If there are have been no other passwords then the current, this returns an empty list.
     *
     * @return  array
     */
    public function getRecentPasswords();

    /**
     * get password recovery id
     *
     * When the user requests a new password, a recovery id is created and sent to his mail address.
     * This is to ensure that the user is a allowed to reset the password.
     *
     * @return  string
     */
    public function getPasswordRecoveryId();

    /**
     * Get password recovery time.
     *
     * When the user requests a new password, the time is stored.
     * This is meant to check, wether the password recovery request has expired.
     *
     * The default is 0.
     *
     * @return  int
     */
    public function getPasswordRecoveryTime();

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
     * Update login password.
     *
     * @param   string  $password  user password
     * @return  \Yana\Security\Users\Facade
     */
    public function changePassword($password);

    /**
     * Update mail.
     *
     * Sets the user's mail address. This information is required to send the user a password.
     *
     * @param   string  $mail  e-mail address
     * @return  \Yana\Security\Users\Facade
     */
    public function setMail($mail);

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
     * Compare recovery id with recovery id of current user.
     *
     * Returns bool(true) if the id is correct an bool(false) otherwise.
     *
     * @param   string  $recoveryId  user password recovery id
     * @return  bool
     */
    public function checkRecoveryId($recoveryId);

    /**
     * Change password.
     *
     * Set login password to $password for current user.
     *
     * @param   string  $password  non-empty alpha-numeric text with optional special characters
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function setPassword($password);

    /**
     * Reset to new random password and return it.
     *
     * A new random password is auto-generated, applied to the user and then returned.
     *
     * @return  string
     */
    public function generateRandomPassword();

    /**
     * Get user groups.
     *
     * Returns an array of group names, where the keys are the group ids and the values are
     * the human-readable group names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function getGroups();

    /**
     * Get user roles.
     *
     * Returns an array of role names, where the keys are the group ids and the values are
     * the human-readable role names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function getRoles();

}

?>