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
 * <<entity>> User.
 *
 * Holds user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class User extends \Yana\Core\Object implements IsUser
{

    /** @var string */ private $_name = null;
    /** @var string */ private $_language = null;
    /** @var string */ private $_password = null;
    /** @var string */ private $_mail = null;
    /** @var bool   */ private $_isActive = false;
    /** @var int    */ private $_failureCount = 0;
    /** @var int    */ private $_failureTime = 0;
    /** @var int    */ private $_loginCount = 0;
    /** @var int    */ private $_loginTime = 0;
    /** @var bool   */ private $_isExpert = false;
    /** @var string */ private $_passwordRecoveryId = null;
    /** @var int    */ private $_passwordRecoveryTime = null;
    /** @var int    */ private $_passwordTime = null;
    /** @var array  */ private $_recentPasswords = array();
    /** @var int    */ private $_timeCreated = null;
    /** @var array  */ private $_groups = array();
    /** @var array  */ private $_roles = array();

    /**
     * data adapter used to load and save the entity's contents.
     *
     * @var  \Yana\Data\Adapters\IsDataAdapter
     */
    private $_dataAdapter = null;

    /**
     * Creates an user by name.
     *
     * @param  string  $userName  current user name
     */
    public function __construct($userName)
    {
        assert('is_string($userName); // Wrong type for argument 1. String expected');

        $this->_name = (string) $userName;
    }

    /**
     * Get the name of the user as a string.
     *
     * @return  string
     */
    public function getId()
    {
        return $this->_name;
    }

    /**
     * Set the user's name.
     *
     * @param  string  $userName  current user name
     * @return  \Yana\Data\Adapters\IsEntity
     */
    public function setId($userName)
    {
        assert('is_string($userName); // Wrong type for argument 1. String expected');
        $this->_name = (string) $userName;
        return $this;
    }

    /**
     * This sets the data adapter used to persist the entity
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $adapter  object that should be used
     * @return  \Yana\Security\Users\User
     */
    public function setDataAdapter(\Yana\Data\Adapters\IsDataAdapter $adapter)
    {
        $this->_dataAdapter = $adapter;
        return $this;
    }

    /**
     * Returns a data adapter.
     *
     * If there is none, the function returns NULL instead.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    protected function _getDataAdapter()
    {
        return $this->_dataAdapter;
    }

    /**
     * Returns bool(true) if the instance has a valid data adapter.
     *
     * Returns bool(false) otherwise.
     *
     * @return  bool
     */
    protected function _hasDataAdapter()
    {
        return $this->_getDataAdapter() instanceof \Yana\Data\Adapters\IsDataAdapter;
    }

    /**
     * Calls the assigned data adapter to persist the entity.
     */
    public function saveEntity()
    {
        if ($this->_hasDataAdapter()) {
            $adapter = $this->_getDataAdapter();
            $adapter->saveEntity($this);
        }
    }

    /**
     * Password failure count.
     *
     * Set number of times user entered the password incorrectly.
     *
     * @param   int  $failureCount  must be positive
     * @return  \Yana\Security\Users\User
     */
    public function setFailureCount($failureCount)
    {
        assert('is_int($failureCount); // Wrong type for argument 1. Integer expected');
        assert('$failureCount >= 0; // Integer must be positive');
        $this->_failureCount = (int) $failureCount;
        return $this;
    }

    /**
     * Password failure time.
     *
     * Set the last time when the user entered a password incorrectly.
     *
     * @param   int  $failureTime  valid timestamp
     * @return  \Yana\Security\Users\User
     */
    public function setFailureTime($failureTime)
    {
        assert('is_int($failureTime); // Wrong type for argument 1. Integer expected');
        $this->_failureTime = (int) $failureTime;
        return $this;
    }

    /**
     * Number of successful logins.
     *
     * Set the number of times the user successfully logged in.
     *
     * @param   int  $loginCount  must be positive
     * @return  \Yana\Security\Users\User
     */
    public function setLoginCount($loginCount)
    {
        assert('is_int($loginCount); // Wrong type for argument 1. Integer expected');
        assert('$loginCount >= 0; // Integer must be positive');
        $this->_loginCount = (int) $loginCount;
        return $this;
    }

    /**
     * Last login time.
     *
     * Set the time the user last successfully logged in.
     *
     * @param   int  $loginTime  a valid timestamp
     * @return  \Yana\Security\Users\User
     */
    public function setLoginTime($loginTime)
    {
        assert('is_int($loginTime); // Wrong type for argument 1. Integer expected');
        $this->_loginTime = (int) $loginTime;
        return $this;
    }

    /**
     * Set a hash-id for password recovery.
     *
     * The user must enter this id in order to reset the password.
     *
     * @param   string  $passwordRecoveryId  some identifier (preferably a hash value)
     * @return  \Yana\Security\Users\User
     */
    public function setPasswordRecoveryId($passwordRecoveryId)
    {
        assert('is_string($passwordRecoveryId); // Wrong type for argument 1. String expected');
        $this->_passwordRecoveryId = (string) $passwordRecoveryId;
        return $this;
    }

    /**
     * Set time when the last password recovery request was made.
     *
     * @param   int $passwordRecoveryTime  a valid timestamp
     * @return  \Yana\Security\Users\User
     */
    public function setPasswordRecoveryTime($passwordRecoveryTime)
    {
        assert('is_int($passwordRecoveryTime); // Wrong type for argument 1. Integer expected');
        $this->_passwordRecoveryTime = (int) $passwordRecoveryTime;
        return $this;
    }

    /**
     * Set time when the user last changed his password.
     *
     * @param   int  $passwordTime  a valid timestamp
     * @return  \Yana\Security\Users\User
     */
    public function setPasswordTime($passwordTime)
    {
        assert('is_int($passwordTime); // Wrong type for argument 1. Integer expected');
        $this->_passwordTime = (int) $passwordTime;
        return $this;
    }

    /**
     * Set a list of recent passwords.
     *
     * When a new password is set it must not be one of those.
     *
     * @param   array  $recentPasswords  list of password hashes
     * @return  \Yana\Security\Users\User
     */
    public function setRecentPasswords(array $recentPasswords)
    {
        $this->_recentPasswords = $recentPasswords;
        return $this;
    }

    /**
     * Set the time when the user account was created.
     *
     * Should not be changed manually.
     *
     * @param   int  $timeCreated  valid timestamp
     * @return  \Yana\Security\Users\User
     */
    public function setTimeCreated($timeCreated)
    {
        assert('is_int($timeCreated); // Wrong type for argument 1. Integer expected');
        $this->_timeCreated = (int) $timeCreated;
        return $this;
    }

    /**
     * Set user groups.
     *
     * An array of group names, where the keys are the group ids and the values are
     * the human-readable group names.
     *
     * @param   array  $groups  list of names
     * @return  \Yana\Security\Users\User
     */
    public function setGroups(array $groups)
    {
        $this->_groups = $groups;
        return $this;
    }

    /**
     * Set user roles.
     *
     * An array of role names, where the keys are the group ids and the values are
     * the human-readable role names.
     *
     * @param   array  $roles  list of names
     * @return  \Yana\Security\Users\User
     */
    public function setRoles(array $roles)
    {
        $this->_roles = $roles;
        return $this;
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
     */
    public function getGroups()
    {
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
     */
    public function getRoles()
    {
        return $this->_roles;
    }

    /**
     * Get password hash.
     *
     * @return  string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Set login password to $password.
     *
     * @param   string  $password user password
     * @return  \Yana\Security\Users\User
     */
    public function setPassword($password)
    {
        assert('is_string($password); // Wrong type for argument 1. String expected');

        $this->_password = "$password";
        return $this;
    }

    /**
     * Update language.
     *
     * Sets prefered language of the user, that is used to provide translates GUI elements.
     *
     * @param   string  $language  language or locale string
     */
    public function setLanguage($language)
    {
        assert('is_string($language); // Wrong type for argument 1. String expected');

        $this->_language = "$language";
    }

    /**
     * Get selected language.
     *
     * @return  string
     */
    public function getLanguage()
    {
        return $this->_language;
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
        return (int) $this->_failureCount;
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
        return (int) $this->_failureTime;
    }

    /**
     * Reset failure count.
     *
     * Resets the number of times the user entered an invalid password back to 0.
     * Use this, when the maximum failure time has expired.
     *
     * @return  \Yana\Security\Users\User
     */
    public function resetFailureCount()
    {
        $this->_failureCount = 0;
        $this->_failureTime = 0;
        return $this;
    }

    /**
     * Reset password recovery id.
     *
     * @return  \Yana\Security\Users\User
     */
    public function resetPasswordRecoveryId()
    {
        $this->_passwordRecoveryId = "";
        $this->_passwordRecoveryTime = 0;
        return this;
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
        return (int) $this->_loginCount;
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
        return (int) $this->_loginTime;
    }

    /**
     * Update mail.
     *
     * Sets the user's mail address. This information is required to send the user a password.
     *
     * @param   string  $mail  e-mail address
     * @return  \Yana\Security\Users\User
     */
    public function setMail($mail)
    {
        assert('is_string($mail); // Wrong type for argument 1. String expected');

        $this->_mail = "$mail";
        return $this;
    }

    /**
     * Get mail address.
     *
     * @return  string
     */
    public function getMail()
    {
        return $this->_mail;
    }

    /**
     * Update expert setting.
     *
     * Set to bool(true) if the user prefers to see expert applications settings and bool(false)
     * if a simpler GUI is prefered.
     *
     * @param   bool  $isExpert  use expert settings (yes/no)
     * @return  \Yana\Security\Users\User
     */
    public function setExpert($isExpert)
    {
        assert('is_bool($isExpert); // Wrong type for argument 1. Boolean expected');

        $this->_isExpert = !empty($isExpert);
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
        return !empty($this->_isExpert);
    }

    /**
     * Update expert setting.
     *
     * Set to bool(true) if the user should be able to log-in or to bool(false) if the user
     * should be deactivated (suspended) without permanently deleting the user settings.
     *
     * @param   bool  $isActive  use expert settings (yes/no)
     * @return  \Yana\Security\Users\User
     */
    public function setActive($isActive)
    {
        assert('is_bool($isActive); // Wrong type for argument 1. Boolean expected');

        $this->_isActive = (bool) $isActive;
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
        return (bool) $this->_isActive;
    }

    /**
     * Get the time when the user was created.
     *
     * @return  int
     */
    public function getTimeCreated()
    {
        return (int) $this->_timeCreated;
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
        return (int) $this->_passwordTime;
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
        return $this->_recentPasswords;
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
        return $this->_passwordRecoveryId;
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
        return (int) $this->_passwordRecoveryTime;
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
    public function createPasswordRecoveryId()
    {
        $this->setPasswordRecoveryId(uniqid(substr(md5($this->getMail()), 0, 3)));
        $this->setPasswordRecoveryTime(time());
        return $this->_passwordRecoveryId;
    }

    /**
     * Add successful login.
     *
     * Call this if the user successfully logged in.
     *
     * @return  \Yana\Security\Users\User
     */
    public function addLoginSuccess()
    {
        $this->_loginCount++;
        $this->_loginTime = time();
        return $this;
    }

    /**
     * Add failed login.
     *
     * Call this if the user successfully logged in.
     *
     * @return  \Yana\Security\Users\IsUser
     */
    public function addLoginFailure()
    {
        $this->_failureCount++;
        $this->_failureTime = time();
        return $this;
    }

    /**
     * Update login password.
     *
     * @param   string  $password  user password
     * @return  \Yana\Security\Users\User
     */
    public function changePassword($password)
    {
        $this->_recentPasswords[] = $this->getPassword();
        $this->setPassword($password);
        $this->_passwordTime = time();
        return $this;
    }

}

?>