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

namespace Yana\Security\Data\Behaviors;

/**
 * <<facade>> User behavior.
 *
 * Holds user data and functions to execute logins and change passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Standard extends \Yana\Security\Data\Behaviors\AbstractBehavior
{

    /**
     * Initializes and returns password behavior facade.
     *
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    protected function _getPasswordBehavior()
    {
        return $this->_getDependencies()->getPasswordBehavior()->setUser($this->_getEntity());
    }

    /**
     * Saves all changes to the user.
     *
     * @return  self
     */
    public function saveChanges()
    {
        $this->_getEntity()->saveEntity();
        return $this;
    }

    /**
     * Get the name of the user as a string.
     *
     * @return  string
     */
    public function getId()
    {
        return (string) $this->_getEntity()->getId();
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
        return (string) $this->_getEntity()->getSessionCheckSum();
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
        assert('is_string($language); // Wrong type for argument: $language. String expected');
        $this->_getEntity()->setLanguage((string) $language);
        return $this;
    }

    /**
     * Get selected language.
     *
     * @return  string
     */
    public function getLanguage()
    {
        return (string) $this->_getEntity()->getLanguage();
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
        return (string) $this->_getEntity()->getMail();
    }

    /**
     * Update expert setting.
     *
     * Set to bool(true) if the user prefers to see expert applications settings and bool(false)
     * if a simpler GUI is prefered.
     *
     * @param   bool  $isExpert  use expert settings (yes/no)
     * @return  self
     */
    public function setExpert($isExpert)
    {
        assert('is_bool($isExpert); // Wrong type for argument: $isExpert. Boolean expected');
        $this->_getEntity()->setExpert((bool) $isExpert);
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
        return (bool) $this->_getEntity()->isExpert();
    }

    /**
     * Update expert setting.
     *
     * Set to bool(true) if the user should be able to log-in or to bool(false) if the user
     * should be deactivated (suspended) without permanently deleting the user settings.
     *
     * @param   bool  $isActive  use expert settings (yes/no)
     * @return  self
     */
    public function setActive($isActive)
    {
        assert('is_bool($isActive); // Wrong type for argument: $isActive. Boolean expected');
        $this->_getEntity()->setActive((bool) $isActive);
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
        return (array) $this->_getEntity()->getRecentPasswords();
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
        $passwordRecoveryId = (string) $this->_getPasswordBehavior()->generatePasswordRecoveryId();
        return $passwordRecoveryId;
    }

    /**
     * Update login password.
     *
     * @param   string  $password  user password
     * @return  self
     */
    public function changePassword($password)
    {
        assert('is_string($password); // Wrong type for argument: $password. String expected');
        $this->_getPasswordBehavior()->changePassword((string) $password);
        return $this;
    }

    /**
     * Update mail.
     *
     * Sets the user's mail address. This information is required to send the user a password.
     *
     * @param   string  $mail  e-mail address
     * @return  self
     * @throws  \Yana\Core\Exceptions\Mails\InvalidMailException  when given e-mail address isn't valid
     */
    public function setMail($mail)
    {
        assert('is_string($mail); // Wrong type for argument: $mail. String expected');
        if (\Yana\Data\MailValidator::validate($mail) === false) {
            throw new \Yana\Core\Exceptions\Mails\InvalidMailException('Given e-mail address is not valid');
        }
        $this->_getEntity()->setMail((string) $mail);
        return $this;
    }

    /**
     * Compare password with password of current user.
     *
     * Returns bool(true) if the password is correct an bool(false) otherwise.
     *
     * @param   string  $plainText  user password
     * @return  bool
     */
    public function checkPassword($plainText)
    {
        assert('is_string($plainText); // Wrong type for argument: $plainText. String expected');
        return (bool) $this->_getPasswordBehavior()->checkPassword((string) $plainText);
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
        assert('is_string($recoveryId); // Wrong type for argument: $recoveryId. String expected');
        return (bool) $this->_getPasswordBehavior()->checkRecoveryId((string) $recoveryId);
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
        $randomPassword = $this->_getPasswordBehavior()->generateRandomPassword();
        assert('is_string($randomPassword); // Invalid return type: String expected');
        return (string) $randomPassword;
    }

    /**
     * Get valid combination of user groups and roles.
     *
     * Result is empty if there are no entries.
     *
     * @param   string  $profileId  profile id
     * @return  \Yana\Security\Data\SecurityRules\IsCollection
     */
    public function getSecurityGroupsAndRoles($profileId)
    {
        try {
            $entities = $this->_getDependencies()->getRulesAdapter()
                ->findEntitiesOwnedByUser($this->getId(), $profileId);

        } catch (\Yana\Core\Exceptions\User\NotFoundException $e) {

            $entities = new \Yana\Security\Data\SecurityRules\Collection();
            unset($e);
        }
        return $entities;
    }

    /**
     * Get all combinations of user groups and roles.
     *
     * Result is empty if there are no entries.
     *
     * @return  \Yana\Security\Data\SecurityRules\IsCollection
     */
    public function getAllSecurityGroupsAndRoles()
    {
        try {
            $entities = $this->_getDependencies()->getRulesAdapter()
                ->findEntitiesOwnedByUser($this->getId());

        } catch (\Yana\Core\Exceptions\User\NotFoundException $e) {

            $entities = new \Yana\Security\Data\SecurityRules\Collection();
            unset($e);
        }
        return $entities;
    }

    /**
     * Find all security rules given to other users.
     *
     * This finds and returns all groups and roles this user owns and
     * has granted to other users.
     *
     * Meaning, all security permissions created by the current user,
     * where the owner of the permission is somebody else.
     *
     * @return  \Yana\Security\Data\SecurityRules\IsCollection
     */
    public function getAllSecurityGroupsAndRolesGrantedToOthers()
    {
        try {
            $entities = $this->_getDependencies()->getRulesAdapter()
                ->findEntitiesGrantedByUser($this->getId());

        } catch (\Yana\Core\Exceptions\User\NotFoundException $e) {

            $entities = new \Yana\Security\Data\SecurityRules\Collection();
            unset($e);
        }
        return $entities;
    }

    /**
     * Check and delete the given rule.
     *
     * Warning! This doesn't check if the given entity is actually current.
     *
     * @param   \Yana\Security\Data\SecurityRules\IsRuleEntity  $rule  the entity that should be deleted
     * @return  self
     * @throws  \Yana\Core\Exceptions\User\RuleNotRevokedException  when there is some logical problem with this rule
     * @throws  \Yana\Core\Exceptions\User\RuleNotDeletedException  when there was some problem with the database
     */
    public function revokePreviouslyGrantedSecurityGroupOrRole(\Yana\Security\Data\SecurityRules\IsRuleEntity $rule)
    {
        if (\Yana\Util\Strings::compareToIgnoreCase($rule->getGrantedByUser(), $this->getId()) !== 0) {
            throw new \Yana\Core\Exceptions\User\RuleNotRevokedException("Can't revoke a rule that the user didn't grant.");
        }
        if (\Yana\Util\Strings::compareToIgnoreCase($rule->getUserName(), $this->getId()) === 0) {
            throw new \Yana\Core\Exceptions\User\RuleNotRevokedException("Can't revoke a rule the user owns.");
        }
        try {
            $this->_getDependencies()->getRulesAdapter()->delete($rule); // may throw exception
        } catch (\Exception $e) {
            throw new \Yana\Core\Exceptions\User\RuleNotDeletedException();
        }
        return $this;
    }

    /**
     * Check and delete the given level.
     *
     * Warning! This doesn't check if the given entity is actually current.
     *
     * @param   \Yana\Security\Data\SecurityRules\IsRuleEntity  $level  the entity that should be deleted
     * @return  self
     * @throws  \Yana\Core\Exceptions\User\LevelNotRevokedException  when there is some logical problem with this level
     * @throws  \Yana\Core\Exceptions\User\LevelNotDeletedException  when there was some problem with the database
     */
    public function revokePreviouslyGrantedSecurityLevel(\Yana\Security\Data\SecurityRules\IsLevelEntity $level)
    {
        if (\Yana\Util\Strings::compareToIgnoreCase($level->getGrantedByUser(), $this->getId()) !== 0) {
            throw new \Yana\Core\Exceptions\User\LevelNotRevokedException("Can't revoke a level that the user didn't grant.");
        }
        if (\Yana\Util\Strings::compareToIgnoreCase($level->getUserName(), $this->getId()) === 0) {
            throw new \Yana\Core\Exceptions\User\LevelNotRevokedException("Can't revoke a level the user owns.");
        }
        try {
            $this->_getDependencies()->getLevelsAdapter()->delete($level); // may throw exception
        } catch (\Exception $e) {
            throw new \Yana\Core\Exceptions\User\LevelNotDeletedException();
        }
        return $this;
    }

    /**
     * Get security level.
     *
     * Returns the user's security level as an integer value.
     * The default is 0.
     *
     * @param   string  $profileId  profile id
     * @return  int
     */
    public function getSecurityLevel($profileId)
    {
        assert('is_string($profileId); // Invalid argument $profileId: string expected');

        try {
            $securityLevelEntity = $this->_getDependencies()->getLevelsAdapter()
                ->findEntityOwnedByUser($this->getId(), $profileId);

        } catch (\Yana\Core\Exceptions\User\NotFoundException $e) {

            $securityLevelEntity = new \Yana\Security\Data\SecurityLevels\Level(0, true); // 0 is default
            unset($e);
        }
        return (int) $securityLevelEntity->getSecurityLevel();
    }

    /**
     * Get security levels.
     *
     * Returns a collection of all security levels associated with this user.
     *
     * @return  \Yana\Security\Data\SecurityLevels\IsCollection
     */
    public function getAllSecurityLevels()
    {
        try {
            $securityLevelEntities = $this->_getDependencies()->getLevelsAdapter()
                ->findEntitiesOwnedByUser($this->getId());

        } catch (\Yana\Core\Exceptions\User\NotFoundException $e) {

            $securityLevelEntities = new \Yana\Security\Data\SecurityLevels\Collection();
            $securityLevelEntities[] = new \Yana\Security\Data\SecurityLevels\Level(0, true); // 0 is default
            unset($e);
        }
        return $securityLevelEntities;
    }

    /**
     * Find all security levels given to other users.
     *
     * This finds and returns all security permissions this user
     * has granted to other users.
     *
     * Meaning, all security permissions created by the current user,
     * where the owner of the permission is somebody else.
     *
     * @return  \Yana\Security\Data\SecurityLevels\IsCollection
     */
    public function getAllSecurityLevelsGrantedToOthers()
    {
        try {
            $securityLevelEntities = $this->_getDependencies()->getLevelsAdapter()
                ->findEntitiesGrantedByUser($this->getId());
        } catch (\Yana\Core\Exceptions\User\NotFoundException $e) {
            $securityLevelEntities = new \Yana\Security\Data\SecurityLevels\Collection();
            $securityLevelEntities[] = new \Yana\Security\Data\SecurityLevels\Level(0, true); // 0 is default
            unset($e);
        }
        return $securityLevelEntities;
    }

    /**
     * Handle user logins.
     *
     * Checks the password.
     * Destroys any previous session (to prevent session fixation).
     * Creates new session id and updates the user's session information in the database.
     *
     * @param   string  $password  user password
     * @return  self
     * @throws  \Yana\Core\Exceptions\Security\PermissionDeniedException  when the user is temporarily blocked
     * @throws  \Yana\Core\Exceptions\Security\InvalidLoginException      when the credentials are invalid
     */
    public function login($password)
    {
        assert('is_string($password); // Invalid argument $password: string expected');

        assert('!isset($user); // Cannot redeclare var $userEntity');
        $user = $this->_getEntity();

        /* 1. reset failure count if failure time has expired */
        if ($this->_getMaxFailureTime() > 0 && $user->getFailureTime() < time() - $this->_getMaxFailureTime()) {
            $user->setFailureCount(0)->setFailureTime(0);
        }
        /* 2. exit if the user has 3 times tried to login with a wrong password in last 5 minutes */
        if ($this->_getMaxFailureCount() > 0 && $user->getFailureCount() >= $this->_getMaxFailureCount()) {
            throw new \Yana\Core\Exceptions\Security\PermissionDeniedException();
        }
        /* 3. error - login has failed */
        if (!$this->_getPasswordBehavior()->checkPassword($password)) {

            throw new \Yana\Core\Exceptions\Security\InvalidLoginException();
        }

        $this->_getDependencies()->getLoginBehavior()->handleLogin($user); // creates new session
        return $this;
    }

    /**
     * Destroy the current session and clear all session data.
     *
     * @return  self
     */
    public function logout()
    {
        $this->_getDependencies()->getLoginBehavior()->handleLogout($this->_getEntity());
        return $this;
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
        return $this->_getDependencies()->getLoginBehavior()->isLoggedIn($this->_getEntity());
    }

}

?>