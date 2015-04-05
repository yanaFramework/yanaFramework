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

namespace Yana\Security\Users;

/**
 * <<interface>> User.
 *
 * Holds user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsUser extends \Yana\Data\Adapters\IsEntity
{

    /**
     * Password failure count.
     *
     * Set number of times user entered the password incorrectly.
     *
     * @param   int  $failureCount  must be positive
     * @return  \Yana\Security\Users\IsUser
     */
    public function setFailureCount($failureCount);

    /**
     * Password failure time.
     *
     * Set the last time when the user entered a password incorrectly.
     *
     * @param   int  $failureTime  valid timestamp
     * @return  \Yana\Security\Users\IsUser
     */
    public function setFailureTime($failureTime);

    /**
     * Number of successful logins.
     *
     * Set the number of times the user successfully logged in.
     *
     * @param   int  $loginCount  must be positive
     * @return  \Yana\Security\Users\IsUser
     */
    public function setLoginCount($loginCount);

    /**
     * Last login time.
     *
     * Set the time the user last successfully logged in.
     *
     * @param   int  $loginTime  a valid timestamp
     * @return  \Yana\Security\Users\IsUser
     */
    public function setLoginTime($loginTime);

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

    /**
     * Get password hash.
     *
     * @return  string
     */
    public function getPassword();

    /**
     * Update login password.
     *
     * @param   string  $password  user password
     * @return  \Yana\Security\Users\IsUser
     */
    public function changePassword($password);

    /**
     * Update language.
     *
     * Sets prefered language of the user, that is used to provide translates GUI elements.
     *
     * @param   string  $language  language or locale string
     * @return  \Yana\Security\Users\IsUser
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
     * Reset failure count.
     *
     * Resets the number of times the user entered an invalid password back to 0.
     * Use this, when the maximum failure time has expired.
     *
     * @return  \Yana\Security\Users\IsUser
     */
    public function resetFailureCount();

    /**
     * Reset password recovery id.
     *
     * @return  \Yana\Security\Users\IsUser
     */
    public function resetPasswordRecoveryId();

    /**
     * Add successful login.
     *
     * Call this if the user successfully logged in.
     *
     * @return  \Yana\Security\Users\IsUser
     */
    public function addLoginSuccess();

    /**
     * Add failed login.
     *
     * Call this if the user successfully logged in.
     *
     * @return  \Yana\Security\Users\IsUser
     */
    public function addLoginFailure();

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
     * Get login count.
     *
     * Returns the number of times the user sucessfully logged-in.
     *
     * The default is 0.
     * @return  int
     */
    public function getLoginCount();

    /**
     * Update mail.
     *
     * Sets the user's mail address. This information is required to send the user a password.
     *
     * @param   string  $mail  e-mail address
     * @return  \Yana\Security\Users\IsUser
     */
    public function setMail($mail);

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
     * @return  \Yana\Security\Users\IsUser
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
     * @return  \Yana\Security\Users\IsUser
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
    public function createPasswordRecoveryId();

    /**
     * Set session-checksum.
     *
     * This is a secondary security feature meant as a fallback in case primary security is compromised.
     *
     * Yes, we actually are a little paranoid, aren't we?
     *
     * So what it does is:
     * The session-id is converted to a checksum and saved to the user-database and the user's session array simultaneously.
     *
     * Why it does that is:
     * So that we can compare the user's session data with the database record and determine, if the data he requested is
     * actually his.
     *
     * What is supposed to happen is:
     * If a user makes a request, claiming he is person X and the database states that he is not, then this user's session
     * is invalidated. Thus preventing the attacker from impersonating another user.
     *
     * This is purely hypothetical of course. As far as we know, there is no way a user could impersonate another user
     * and we sure as hell hope there never will be. But if you are being paranoid: at least do it properly.
     *
     * Consequences:
     * As a side-effect a user can't log-in on two different browsers or systems simultaneously.
     * If he does, his earlier session will be invalidated.
     * We think this is acceptable since it also makes the user less vulnerable to session-riding-attacks.
     *
     * @param   string  $checkSum  MD5-checksum of session-id
     * @return  \Yana\Security\Users\IsUser
     */
    public function setSessionCheckSum($checkSum);

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
     * Set a hash-id for password recovery.
     *
     * The user must enter this id in order to reset the password.
     *
     * @param   string  $passwordRecoveryId  some identifier (preferably a hash value)
     * @return  \Yana\Security\Users\IsUser
     */
    public function setPasswordRecoveryId($passwordRecoveryId);

    /**
     * Set time when the last password recovery request was made.
     *
     * @param   int $passwordRecoveryTime  a valid timestamp
     * @return  \Yana\Security\Users\IsUser
     */
    public function setPasswordRecoveryTime($passwordRecoveryTime);

    /**
     * Set time when the user last changed his password.
     *
     * @param   int  $passwordTime  a valid timestamp
     * @return  \Yana\Security\Users\IsUser
     */
    public function setPasswordTime($passwordTime);

    /**
     * Set a list of recent passwords.
     *
     * When a new password is set it must not be one of those.
     *
     * @param   array  $recentPasswords  list of password hashes
     * @return  \Yana\Security\Users\IsUser
     */
    public function setRecentPasswords(array $recentPasswords);

    /**
     * Set the time when the user account was created.
     *
     * Should not be changed manually.
     *
     * @param   int  $timeCreated  valid timestamp
     * @return  \Yana\Security\Users\IsUser
     */
    public function setTimeCreated($timeCreated);

    /**
     * Set user groups.
     *
     * An array of group names, where the keys are the group ids and the values are
     * the human-readable group names.
     *
     * @param   array  $groups  list of names
     * @return  \Yana\Security\Users\IsUser
     */
    public function setGroups(array $groups);

    /**
     * Set user roles.
     *
     * An array of role names, where the keys are the group ids and the values are
     * the human-readable role names.
     *
     * @param   array  $roles  list of names
     * @return  \Yana\Security\Users\IsUser
     */
    public function setRoles(array $roles);

    /**
     * Set login password to $password.
     *
     * @param   string  $password user password
     * @return  \Yana\Security\Users\User
     */
    public function setPassword($password);

}

?>