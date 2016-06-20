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
 * <<facade>> User behavior.
 *
 * Holds user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Standard extends \Yana\Security\Users\Behaviors\AbstractBehavior
{

    /**
     * Get the name of the user as a string.
     *
     * @return  string
     */
    public function getId()
    {
        return $this->_getEntity()->getId();
    }

    /**
     * Get session-checksum.
     *
     * Will return NULL if the user never did a login before.
     * Otherwise it will return the checksum of the least recently used session.
     * 
     * @return  string
     */
    public function getSessionCheckSum()
    {
        return $this->_getEntity()->getSessionCheckSum();
    }

    /**
     * Update language.
     *
     * Sets prefered language of the user, that is used to provide translates GUI elements.
     *
     * @param   string  $language  language or locale string
     * @return  self
     */
    public function setLanguage($language)
    {
        $this->_getEntity()->setPassword($language);
        return $this;
    }

    /**
     * Get selected language.
     *
     * @return  string
     */
    public function getLanguage()
    {
        return $this->_getEntity()->getLanguage();
    }

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
    public function getFailureCount()
    {
        return (int) $this->_getEntity()->getFailureCount();
    }

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
    public function getFailureTime()
    {
        return (int) $this->_getEntity()->getFailureTime();
    }

    /**
     * Get login count.
     *
     * Returns the number of times the user sucessfully logged-in.
     *
     * The default is 0.
     * @return  int
     */
    public function getLoginCount()
    {
        return (int) $this->_getEntity()->getLoginCount();
    }

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
    public function getLoginTime()
    {
        return (int) $this->_getEntity()->getLoginTime();
    }

    /**
     * Get mail address.
     *
     * @return  string
     */
    public function getMail()
    {
        return $this->_getEntity()->getMail();
    }

    /**
     * Update expert setting.
     *
     * Set to bool(true) if the user prefers to see expert applications settings and bool(false)
     * if a simpler GUI is prefered.
     *
     * @param   bool  $isExpert  use expert settings (yes/no)
     * @return  \Yana\Security\Users\Facade
     */
    public function setExpert($isExpert)
    {
        $this->_getEntity()->setExpert($isExpert);
        return $this;
    }

    /**
     * User prefers expert settings.
     *
     * Returns bool(true) if the user prefers to see expert applications settings and bool(false)
     * if a simpler GUI is prefered.
     *
     * @return  string
     */
    public function isExpert()
    {
        return $this->_getEntity()->isExpert();
    }

    /**
     * Update expert setting.
     *
     * Set to bool(true) if the user should be able to log-in or to bool(false) if the user
     * should be deactivated (suspended) without permanently deleting the user settings.
     *
     * @param   bool  $isActive  use expert settings (yes/no)
     * @return  \Yana\Security\Users\Facade
     */
    public function setActive($isActive)
    {
        $this->_getEntity()->setActive($isActive);
        return $this;
    }

    /**
     * User is active.
     *
     * Returns bool(true) if the user is activated and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isActive()
    {
        return (bool) $this->_getEntity()->isActive();
    }

    /**
     * Get the time when the user was created.
     *
     * @return  int
     */
    public function getTimeCreated()
    {
        return (int) $this->_getEntity()->getTimeCreated();
    }

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
    public function getPasswordChangedTime()
    {
        return (int) $this->_getEntity()->getPasswordChangedTime();
    }

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
    public function getRecentPasswords()
    {
        return $this->_getEntity()->getRecentPasswords();
    }

    /**
     * get password recovery id
     *
     * When the user requests a new password, a recovery id is created and sent to his mail address.
     * This is to ensure that the user is a allowed to reset the password.
     *
     * @return  string
     */
    public function getPasswordRecoveryId()
    {
        return $this->_getEntity()->getPasswordRecoveryId();
    }

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
    public function getPasswordRecoveryTime()
    {
        return (int) $this->_getEntity()->getPasswordRecoveryTime();
    }

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
    public function generatePasswordRecoveryId()
    {
        return $this->_getPasswordBehavior()->generatePasswordRecoveryId();
    }

    /**
     * Update login password.
     *
     * @param   string  $password  user password
     * @return  \Yana\Security\Users\Facade
     */
    public function changePassword($password)
    {
        $this->_getPasswordBehavior()->changePassword($password);
        return $this;
    }

    /**
     * Update mail.
     *
     * Sets the user's mail address. This information is required to send the user a password.
     *
     * @param   string  $mail  e-mail address
     * @return  \Yana\Security\Users\Facade
     */
    public function setMail($mail)
    {
        $this->_getEntity()->setMail($mail);
        return $this;
    }

    /**
     * Compare password with password of current user.
     *
     * Returns bool(true) if the password is correct an bool(false) otherwise.
     *
     * @param   string  $userPwd  user password
     * @return  bool
     */
    public function checkPassword($userPwd)
    {
        return $this->_getPasswordBehavior()->checkPassword($userPwd);
    }

    /**
     * Compare recovery id with recovery id of current user.
     *
     * Returns bool(true) if the id is correct an bool(false) otherwise.
     *
     * @param   string  $recoveryId  user password recovery id
     * @return  bool
     */
    public function checkRecoveryId($recoveryId)
    {
        return $this->_getPasswordBehavior()->checkRecoveryId($recoveryId);
    }

    /**
     * Reset to new random password and return it.
     *
     * A new random password is auto-generated, applied to the user and then returned.
     *
     * @return  string
     */
    public function generateRandomPassword()
    {
        return $this->_getPasswordBehavior()->generateRandomPassword();
    }

    /**
     * Get user groups.
     *
     * Returns an array of group names, where the keys are the group ids and the values are
     * the human-readable group names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     * @todo    implement me!
     */
    public function getGroups()
    {
        if (count($this->_groups) === 0 && $this->_hasDataAdapter()) {
            $this->_groups = $this->_getDataAdapter()->getGroups($this->getId());
        }
        return $this->_groups;
    }

    /**
     * Get user roles.
     *
     * Returns an array of role names, where the keys are the group ids and the values are
     * the human-readable role names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     * @todo    implement me!
     */
    public function getRoles()
    {
        if (count($this->_roles) === 0 && $this->_hasDataAdapter()) {
            $this->_roles = $this->_getDataAdapter()->getRoles($this->getId());
        }
        return $this->_roles;
    }

    /**
     * Handle user logins.
     *
     * Checks the password.
     * Destroys any previous session (to prevent session fixation).
     * Creates new session id and updates the user's session information in the database.
     *
     * @param   string  $password  user password
     * @throws  \Yana\Core\Exceptions\Security\PermissionDeniedException  when the user is temporarily blocked
     * @throws  \Yana\Core\Exceptions\Security\InvalidLoginException      when the credentials are invalid
     */
    public function login($password)
    {
        assert('is_string($userName); // Invalid argument $userName: string expected');
        assert('is_string($password); // Invalid argument $password: string expected');

        assert('!isset($user); // Cannot redeclare var $userEntity');
        $user = $this->_getEntity();

        /* 1. reset failure count if failure time has expired */
        if ($this->_getMaxFailureTime() > 0 && $user->getFailureTime() < time() - $this->_getMaxFailureTime()) {
            $user->resetFailureCount();
        }
        /* 2. exit if the user has 3 times tried to login with a wrong password in last 5 minutes */
        if ($this->_getMaxFailureCount() > 0 && $user->getFailureCount() >= $this->_getMaxFailureCount()) {
            throw new \Yana\Core\Exceptions\Security\PermissionDeniedException();
        }
        /* 3. error - login has failed */
        if (!$this->_getPasswordBehavior()->checkPassword($password)) {

            throw new \Yana\Core\Exceptions\Security\InvalidLoginException();
        }
        $this->_getLoginBehavior()->handleLogin($user); // creates new session
    }

    /**
     * Destroy the current session and clear all session data.
     */
    public function logout()
    {
        $this->_getLoginBehavior()->handleLogout($this->_getEntity());
    }

    /**
     * Check if user is logged in.
     *
     * Returns bool(true) if the user is currently
     * logged in and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isLoggedIn()
    {
        $this->_getLoginBehavior()->isLoggedIn($this->_getEntity());
    }

}

?>