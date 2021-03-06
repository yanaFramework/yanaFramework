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
declare(strict_types=1);

namespace Yana\Security\Data\Users;

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
class Entity extends \Yana\Data\Adapters\AbstractEntity implements \Yana\Security\Data\Users\IsEntity
{

    /** @var string */ private $_id = null;
    /** @var string */ private $_language = null;
    /** @var string */ private $_password = null;
    /** @var string */ private $_mail = null;
    /** @var bool   */ private $_isActive = false;
    /** @var int    */ private $_failureCount = 0;
    /** @var int    */ private $_failureTime = 0;
    /** @var int    */ private $_loginCount = 0;
    /** @var int    */ private $_loginTime = null;
    /** @var bool   */ private $_isExpert = false;
    /** @var string */ private $_passwordRecoveryId = null;
    /** @var int    */ private $_passwordRecoveryTime = 0;
    /** @var int    */ private $_passwordChangedTime = 0;
    /** @var array  */ private $_recentPasswords = array();
    /** @var int    */ private $_timeCreated = null;
    /** @var string */ private $_sessionCheckSum = null;

    /**
     * Creates an user by name.
     *
     * @param  string  $userName  current user name
     */
    public function __construct(string $userName)
    {
        $this->setId((string) $userName);
    }

    /**
     * Get the name of the user as a string.
     *
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set the user's name.
     *
     * @param  string  $userName  current user name
     * @return  \Yana\Data\Adapters\IsEntity
     */
    public function setId($userName)
    {
        assert(is_string($userName), 'Wrong type for argument 1. String expected');
        $this->_id = (string) $userName;
        return $this;
    }

    /**
     * Password failure count.
     *
     * Set number of times user entered the password incorrectly.
     *
     * @param   int  $failureCount  must be positive
     * @return  $this
     */
    public function setFailureCount(int $failureCount)
    {
        assert($failureCount >= 0, 'Integer must be positive');
        $this->_failureCount = $failureCount;
        return $this;
    }

    /**
     * Password failure time.
     *
     * Set the last time when the user entered a password incorrectly.
     *
     * @param   int  $failureTime  valid timestamp
     * @return  $this
     */
    public function setFailureTime(int $failureTime)
    {
        $this->_failureTime = $failureTime;
        return $this;
    }

    /**
     * Number of successful logins.
     *
     * Set the number of times the user successfully logged in.
     *
     * @param   int  $loginCount  must be positive
     * @return  $this
     */
    public function setLoginCount(int $loginCount)
    {
        assert($loginCount >= 0, 'Integer must be positive');
        $this->_loginCount = $loginCount;
        return $this;
    }

    /**
     * Last login time.
     *
     * Set the time the user last successfully logged in.
     *
     * @param   int  $loginTime  a valid timestamp
     * @return  $this
     */
    public function setLoginTime(int $loginTime)
    {
        $this->_loginTime = $loginTime;
        return $this;
    }

    /**
     * Set a hash-id for password recovery.
     *
     * The user must enter this id in order to reset the password.
     *
     * @param   string  $passwordRecoveryId  some identifier (preferably a hash value)
     * @return  $this
     */
    public function setPasswordRecoveryId(string $passwordRecoveryId)
    {
        $this->_passwordRecoveryId = $passwordRecoveryId;
        return $this;
    }

    /**
     * Set time when the last password recovery request was made.
     *
     * @param   int $passwordRecoveryTime  a valid timestamp
     * @return  $this
     */
    public function setPasswordRecoveryTime(int $passwordRecoveryTime)
    {
        $this->_passwordRecoveryTime = $passwordRecoveryTime;
        return $this;
    }

    /**
     * Set time when the user last changed his password.
     *
     * @param   int  $passwordChangedTime  a valid timestamp
     * @return  $this
     */
    public function setPasswordChangedTime(int $passwordChangedTime)
    {
        $this->_passwordChangedTime = $passwordChangedTime;
        return $this;
    }

    /**
     * Set a list of recent passwords.
     *
     * When a new password is set it must not be one of those.
     *
     * @param   array  $recentPasswords  list of password hashes
     * @return  $this
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
     * @return  $this
     */
    public function setTimeCreated(int $timeCreated)
    {
        $this->_timeCreated = $timeCreated;
        return $this;
    }

    /**
     * Set session-checksum.
     *
     * The checksum should be a hexadecimal number represented as a string no longer than 32 chars.
     *
     * @param   string  $checkSum  MD5-checksum of session-id
     * @return  $this
     */
    public function setSessionCheckSum(string $checkSum)
    {
        $this->_sessionCheckSum = $checkSum;
        return $this;
    }

    /**
     * Get session-checksum.
     *
     * Will return NULL if the user never did a login before.
     * Otherwise it will return the checksum of the least recently used session.
     * 
     * @return  string
     */
    public function getSessionCheckSum(): ?string
    {
        return $this->_sessionCheckSum;
    }

    /**
     * Get password hash.
     *
     * @return  string|null
     */
    public function getPassword(): ?string
    {
        return $this->_password;
    }

    /**
     * Set login password to $password.
     *
     * @param   string  $password  user password
     * @return  $this
     */
    public function setPassword(string $password)
    {
        $this->_password = $password;
        return $this;
    }

    /**
     * Update language.
     *
     * Sets prefered language of the user, that is used to provide translates GUI elements.
     *
     * @param   string  $language  language or locale string
     * @return  $this
     */
    public function setLanguage(string $language)
    {
        $this->_language = $language;
        return $this;
    }

    /**
     * Get selected language.
     *
     * @return  string|NULL
     */
    public function getLanguage(): ?string
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
    public function getFailureCount(): int
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
    public function getFailureTime(): int
    {
        return $this->_failureTime;
    }

    /**
     * Get login count.
     *
     * Returns the number of times the user sucessfully logged-in.
     *
     * The default is 0.
     *
     * @return  int
     */
    public function getLoginCount(): int
    {
        return $this->_loginCount;
    }

    /**
     * Get the timestamp when user last sucessfully logged-in.
     *
     * Note: This number is not reset on log-out.
     * Thus you cannot use this settings to check if a user is currently logged-in.
     *
     * The default is 0.
     *
     * @return  int|NULL
     */
    public function getLoginTime(): ?int
    {
        return $this->_loginTime;
    }

    /**
     * Update mail.
     *
     * Sets the user's mail address. This information is required to send the user a password.
     *
     * @param   string  $mail  e-mail address
     * @return  $this
     */
    public function setMail(string $mail)
    {
        $this->_mail = $mail;
        return $this;
    }

    /**
     * Get mail address.
     *
     * @return  string|null
     */
    public function getMail(): ?string
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
     * @return  $this
     */
    public function setExpert(bool $isExpert)
    {
        $this->_isExpert = $isExpert;
        return $this;
    }

    /**
     * User prefers expert settings.
     *
     * Returns bool(true) if the user prefers to see expert applications settings and bool(false)
     * if a simpler GUI is prefered.
     *
     * @return  bool
     */
    public function isExpert(): bool
    {
        return $this->_isExpert;
    }

    /**
     * Activate/Deactivate user.
     *
     * Set to bool(true) if the user should be able to log-in or to bool(false) if the user
     * should be deactivated (suspended) without permanently deleting the user settings.
     *
     * @param   bool  $isActive  use expert settings (yes/no)
     * @return  $this
     */
    public function setActive(bool $isActive)
    {
        $this->_isActive = $isActive;
        return $this;
    }

    /**
     * User is active.
     *
     * Returns bool(true) if the user is activated and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isActive(): bool
    {
        return $this->_isActive;
    }

    /**
     * Get the time when the user was created.
     *
     * @return  int|null
     */
    public function getTimeCreated(): ?int
    {
        return $this->_timeCreated;
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
    public function getPasswordChangedTime(): int
    {
        return $this->_passwordChangedTime;
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
    public function getRecentPasswords(): array
    {
        return $this->_recentPasswords;
    }

    /**
     * Get password recovery id.
     *
     * When the user requests a new password, a recovery id is created and sent to his mail address.
     * This is to ensure that the user is a allowed to reset the password.
     *
     * @return  string|null
     */
    public function getPasswordRecoveryId(): ?string
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
    public function getPasswordRecoveryTime(): int
    {
        return $this->_passwordRecoveryTime;
    }

}

?>