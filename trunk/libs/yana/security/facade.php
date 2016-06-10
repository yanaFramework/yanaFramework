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

namespace Yana\Security;

/**
 * <<facade>> Simplifies dealing with user information.
 *
 * This facade implements a standard behavior to be used for handling user logins, creating and updating users aso.
 * and hides the complexity of creating the necessary classes and handing over the correct parameters required.
 *
 * The standard behavior is based on an "educated guess" about, what might fit most common situation.
 *
 * You are encouraged to use this facade for any of your day-to-day interactions with the security sub-system and
 * only re-implement your own behavior, in situations where you need to diverge from the standard.
 *
 * @package     yana
 * @subpackage  security
 */
class Facade extends \Yana\Core\Object
{

    /**
     * Count boundary.
     *
     * Maximum number of times a user may enter
     * a wrong password before its account
     * is suspended for $maxFailureTime seconds.
     *
     * @var  int
     */
    private $_maxFailureCount = 3;

    /**
     * Time boundary.
     *
     * Maximum time in seconds a user's login
     * is blocked after entering a wrong password
     * $maxFailureCount times.
     *
     * E.g. 300 sec. = 5 minutes.
     *
     * @var  int
     */
    private $_maxFailureTime = 300;

    /**
     * @var  \Yana\Security\Rules\CacheableChecker
     */
    private $_rulesChecker = null;

    /**
     * @var  \Yana\Security\Sessions\IsWrapper
     */
    private $_session = null;

    /**
     * Creates a session wrapper on demand and returns it.
     *
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    protected function _getSession()
    {
        if (!isset($this->_session)) {
            $this->_session = new \Yana\Security\Sessions\Wrapper();
        }
        return $this->_session;
    }

    /**
     * @return  \Yana\Security\Users\Logins\Manager
     */
    private function _createLoginManager()
    {
        return new \Yana\Security\Users\Logins\Manager($this->_getSession());
    }

    /**
     * @return  \Yana\Security\Passwords\Checks\IsCheck
     */
    private function _createPasswordCheck()
    {
        return new \Yana\Security\Passwords\Checks\StandardCheck($this->_createPasswordAlgorithm());
    }

    /**
     * @return  \Yana\Security\Passwords\IsAlgorithm
     */
    private function _createPasswordAlgorithm()
    {
        $builder = new \Yana\Security\Passwords\Builders\Builder();
        return $builder
            ->add(\Yana\Security\Passwords\Builders\Enumeration::BASIC)
            ->add(\Yana\Security\Passwords\Builders\Enumeration::BLOWFISH)
            ->add(\Yana\Security\Passwords\Builders\Enumeration::SHA256)
            ->add(\Yana\Security\Passwords\Builders\Enumeration::SHA512)
            ->add(\Yana\Security\Passwords\Builders\Enumeration::BCRYPT)
            ->__invoke();
    }

    /**
     * @return  \Yana\Security\Passwords\Generators\IsAlgorithm
     */
    private function _createPasswordGenerator()
    {
        return new \Yana\Security\Passwords\Generators\StandardAlgorithm();
    }

    /**
     * @return  \Yana\Security\Passwords\IsAlgorithm
     */
    private function _createPasswordBehavior()
    {
        return new \Yana\Security\Passwords\Behaviors\StandardBehavior(
            $this->_createPasswordAlgorithm(), $this->_createPasswordCheck(), $this->_createPasswordGenerator()
        );
    }

    /**
     * Builds and returns a rule-checker object.
     *
     * @return  \Yana\Security\Rules\CacheableChecker
     */
    protected function _getRulesChecker()
    {
        if (!isset($this->_rulesChecker)) {
            $default = \Yana\Application::getDefault('event.user');
            if (!is_array($default)) {
                $default = array();
            }
            $this->_rulesChecker = new \Yana\Security\Rules\CacheableChecker(new \Yana\Security\Rules\Requirements\DefaultableDataReader($default));
        }
        return $this->_rulesChecker;
    }

    /**
     * @return \Yana\Security\Rules\Requirements\DataReader
     */
    protected function _createDataReader()
    {
        return new \Yana\Security\Rules\Requirements\DataReader($this->_getDataSource());
    }

    /**
     * @return \Yana\Security\Users\UserAdapter
     */
    protected function _createUserAdapter()
    {
        return new \Yana\Security\Users\UserAdapter($this->_getDataSource());
    }

    /**
     * @return \Yana\Security\Users\UserBuilder
     */
    protected function _createUserBuilder()
    {
        return new \Yana\Security\Users\UserBuilder($this->_createUserAdapter());
    }

    /**
     * @param   string  $userName  identifies user
     * @return  \Yana\Security\Users\IsUser
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found in the database
     */
    protected function _loadUser($userName = "")
    {
        assert('is_string($userName); // Invalid argument $userName: string expected');
        $builder = $this->_createUserBuilder();
        if ($userName > "") {
            $user = $builder->buildFromUserName($userName);
        } else {
            $user = $builder->buildFromSession($this->_getSession());
        }
        return $user;
    }

    /**
     * Replace the cache adapter.
     *
     * This class uses an ArrayAdapter by default.
     * Overwrite only for unit-tests, or if you are absolutely sure you need to
     * and know what you are doing.
     * Replacing this by the wrong adapter might introduce a security risk,
     * unless you are in a very specific usage scenario.
     *
     * Note that this may also replace the cache contents.
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $cache  new cache adapter
     * @return  \Yana\Data\Adapters\IsCacheable
     * @ignore
     */
    public function setCache(\Yana\Data\Adapters\IsDataAdapter $cache)
    {
        $this->_cache = $cache;
        return $this;
    }

    /**
     * Get cache-adapter
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     * @ignore
     */
    protected function _getCache()
    {
        if (!isset($this->_cache)) {
            $this->_cache = new \Yana\Data\Adapters\ArrayAdapter();
        }
        return $this->_cache;
    }

    /**
     * Handle user logins.
     *
     * This is handling the interaction between various classes of the security sub-system, in order
     * to implement the standard behavior for checking password and handling logins.
     *
     * It destroys any previous session (to prevent session fixation).
     * Creates new session id and updates the user's session information in the database.
     *
     * @param   string  $userName  may contain only A-Z, 0-9, '-' and '_'
     * @param   string  $password  user password
     * @throws  \Yana\Core\Exceptions\Security\PermissionDeniedException  when the user is temporarily blocked
     * @throws  \Yana\Core\Exceptions\Security\InvalidLoginException      when the credentials are invalid
     * @throws  \Yana\Core\Exceptions\NotFoundException                   when the user name is unknown
     */
    public function login($userName, $password)
    {
        assert('is_string($userName); // Invalid argument $userName: string expected');
        assert('is_string($password); // Invalid argument $password: string expected');

        assert('!isset($userEntity); // Cannot redeclare var $userEntity');
        $userEntity = $this->_loadUser($userName); // throws \Yana\Core\Exceptions\NotFoundException

        /* 1. reset failure count if failure time has expired */
        if ($this->getMaxFailureTime() > 0 && $userEntity->getFailureTime() < time() - $this->getMaxFailureTime()) {
            $userEntity->resetFailureCount();
        }
        /* 2. exit if the user has 3 times tried to login with a wrong password in last 5 minutes */
        if ($this->getMaxFailureCount() > 0 && $userEntity->getFailureCount() >= $this->getMaxFailureCount()) {
            throw new \Yana\Core\Exceptions\Security\PermissionDeniedException();
        }
        /* 3. error - login has failed */
        if (!$this->_createPasswordCheck()->__invoke($userEntity, $userName, $password)) {

            throw new \Yana\Core\Exceptions\Security\InvalidLoginException();
        }
        $this->_createLoginManager()->handleLogin($userEntity); // creates new session
    }

    /**
     * Destroy the current session and clear all session data.
     */
    public function logout()
    {
        $this->_createLoginManager()->handleLogout($this->_loadUser());
    }

    /**
     * Set count boundary.
     *
     * Maximum number of times a user may enter a wrong password before its account is suspended for x seconds.
     *
     * @param   int  $maxFailureCount  1 = block on first invalid password, 0 = never block user
     * @return  \Yana\Security\Facade
     */
    public function setMaxFailureCount($maxFailureCount = 3)
    {
        assert('is_int($maxFailureCount); // Invalid argument $maxFailureCount: integer expected');
        assert('$maxFailureCount >= 0; // Invalid argument $maxFailureCount: must not be negative');
        $this->_maxFailureCount = (int) $maxFailureCount;
        return $this;
    }

    /**
     * Set time boundary.
     *
     * Maximum time in seconds a user's login is blocked after entering a wrong password x times.
     *
     * @param   int  $maxFailureTime  in seconds (0 = keep blocked forever)
     * @return  \Yana\Security\Facade
     */
    public function setMaxFailureTime($maxFailureTime = 300)
    {
        assert('is_int($maxFailureTime); // Invalid argument $maxFailureTime: integer expected');
        assert('$maxFailureTime >= 0; // Invalid argument $maxFailureTime: must not be negative');
        $this->_maxFailureTime = (int) $maxFailureTime;
        return $this;
    }

    /**
     * Get count boundary.
     *
     * Maximum number of times a user may enter a wrong password before its account is suspended for x seconds.
     *
     * @return  int
     */
    public function getMaxFailureCount()
    {
        return (int) $this->_maxFailureCount;
    }

    /**
     * Get time boundary.
     *
     * Maximum time in seconds a user's login is blocked after entering a wrong password x times.
     *
     * @return  int
     */
    public function getMaxFailureTime()
    {
        return (int) $this->_maxFailureTime;
    }

    /**
     * Rescan plugin list and refresh the action security settings.
     *
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if new entries could not be inserted
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  if existing entries could not be deleted
     */
    public function refreshPluginSecurityRules()
    {
        $refreshRequirements = new \Yana\Security\Rules\Requirements\DataWriter($this->_getDatasource());
        $refreshRequirements(\Yana\Plugins\Manager::getInstance()->getEventConfigurations());
    }

    /**
     * Add security rule.
     *
     * This method adds a reference to an user-definded implementation to a list of custom security checks.
     *
     * By default the list is empty.
     *
     * The checks added here will be executed when checkPermission() is called, in the order in which they were added.
     *
     * The called rules must return bool(true) if the user ist granted permission to proceed
     * with the requested action and bool(false) otherwise.
     * They may not throw exceptions or raise errors.
     *
     * Example implementation for a custom rule:
     * <code>
     * class MyCheck implements IsRule
     * {
     *   public function __invoke(\Yana\Security\Rules\Requirements\IsRequirement $required, $profileId, $action, $userName)
     *   {
     *     $manager = SessionManager::getInstance();
     *     $level = $manager->getSecurityLevel($userName, $profileId);
     *     return $required[PluginAnnotationEnumeration::LEVEL] <= $level;
     *   }
     * }
     * </code>
     * The code above returns true, if the user's security level is higher or equal the required
     * level. The check is added when the plugin is created.
     *
     * @param  \Yana\Security\Rules\IsRule  $rule  to be validated
     */
    public function addSecurityRule(\Yana\Security\Rules\IsRule $rule)
    {
        $this->_getRulesChecker()->addSecurityRule($rule);
    }

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
     */
    public function checkRules($profileId = null, $action = null, $userName = "")
    {
        assert('is_null($profileId) || is_string($profileId); // Wrong type for argument $profileId. String expected');
        assert('is_null($action) || is_string($action); // Wrong type for argument $action. String expected');
        assert('is_string($userName); // Wrong type for argument $userName. String expected');

        /* Argument 1 */
        if (empty($profileId)) {
            $profileId = \Yana\Application::getId();
        }
        assert('is_string($profileId);');
        assert('!isset($uppderCaseProfileId); // Cannot redeclare $uppderCaseProfileId');
        $uppderCaseProfileId = \Yana\Util\String::toUpperCase((string) $profileId);

        /* Argument 2 */
        if (empty($action)) {
            $action = \Yana\Plugins\Manager::getLastEvent();
            // security restriction on undefined event
            if (empty($action)) {
                return false;
            }
        }
        assert('is_string($action);');
        assert('!isset($lowerCaseAction); // Cannot redeclare $lowerCaseAction');
        $lowerCaseAction = \Yana\Util\String::toLowerCase((string) $action);

        /* Argument 3 */
        /**
         * {@internal
         * The user id is resolved by the "user" plugin and stored in a session var, so other plugins can look it up.
         * }}
         */
        if (empty($userName)) {
            $userName = $this->_getSession()->getCurrentUserName();
        }

        assert('!isset($user); // Cannot redeclare $user');
        $user = empty($userName) ? new \Yana\Security\Users\GuestUser() : $this->_loadUser((string) $userName);

        assert('!isset($e); // Cannot redeclare $e');
        try {

            assert('!isset($result); // Cannot redeclare $result');
            $result = $this->_getRulesChecker()->checkRules($uppderCaseProfileId, $lowerCaseAction, $user);

        } catch (\Yana\Security\Rules\Requirements\NotFoundException $e) {
            \Yana\Log\LogManager::getLogger()->addLog($e->getMessage());
            $result = false;
            unset($e);
        }

        assert('is_bool($result);');
        return $result;
    }

    /**
     * Check requirements against given rules.
     *
     * @param   \Yana\Security\Rules\Requirements\IsRequirement  $requirement  that will be checked
     * @param   string                                           $profileId    profile id
     * @param   string                                           $action       action name
     * @param   string                                           $userName     user name
     * @return  bool
     */
    public function checkByRequirement(\Yana\Security\Rules\Requirements\IsRequirement $requirement, $profileId, $action, $userName)
    {
        assert('is_string($profileId); // Wrong type for argument $profileId. String expected');
        assert('is_string($action); // Wrong type for argument $action. String expected');
        assert('is_string($userName); // Wrong type for argument $userName. String expected');

        return (bool) $this->_getRulesChecker()->checkByRequirement($requirement, $profileId, $action, $this->_loadUser($userName));
    }

    /**
     * Change password.
     *
     * Set login password to $password for current user.
     *
     * @param   \Yana\Security\Users\IsUser  $user      entity
     * @param   string                       $password  non-empty alpha-numeric text with optional special characters
     * @return  self
     * @throws  \Yana\Core\Exceptions\User\UserException  when there was a problem with the database
     */
    public function changePassword(\Yana\Security\Users\IsUser $user, $password)
    {
        assert('is_string($password); // Wrong type for argument $password. String expected');

        $this->_createPasswordBehavior()->setUser($user)->changePassword($password);
        $user->saveEntity(); // may throw exception
        return $this;
    }

    /**
     * Reset password with random string.
     *
     * A new random password is auto-generated, applied to the user and then returned.
     *
     * @param   \Yana\Security\Users\IsUser  $user  entity
     * @return  string
     * @throws  \Yana\Core\Exceptions\User\UserException  when there was a problem with the database
     */
    public function resetPassword(\Yana\Security\Users\IsUser $user)
    {
        $password = $this->_createPasswordBehavior()->setUser($user)->generateRandomPassword();
        $user->saveEntity(); // may throw exception
        return $password;
    }

    /**
     * Get user groups.
     *
     * Returns an array of group names, where the keys are the group ids and the values are the human-readable group names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function loadListOfGroups()
    {
        return $this->_createDataReader()->loadListOfGroups();
    }

    /**
     * Get user roles.
     *
     * Returns an array of role names, where the keys are the group ids and the values are the human-readable role names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function loadListOfRoles()
    {
        return $this->_createDataReader()->loadListOfRoles();
    }

    /**
     * Create a new user.
     *
     * @param   string  $userName  user name
     * @param   string  $mail      e-mail address
     * @throws  \Yana\Core\Exceptions\User\MissingNameException    when no user name is given
     * @throws  \Yana\Core\Exceptions\User\AlreadyExistsException  if another user with the same name already exists
     * @throws  \Yana\Db\CommitFailedException                     when the database entry could not be created
     */
    public function createUser($userName, $mail)
    {
        assert('is_string($userName); // Wrong type for argument $userName. String expected');
        assert('is_string($mail); // Wrong type for argument $mail. String expected');

        if (empty($userName)) {
            throw new \Yana\Core\Exceptions\User\MissingNameException("No user name given.", \Yana\Log\TypeEnumeration::WARNING);
        }

        try {
            $this->_createUserBuilder()->buildNewUser($userName, $mail)->saveEntity(); // may throw exception

        } catch (\Exception $e) {
            $message = "Unable to commit changes to the database server while trying to update settings for user '{$userName}'.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Db\CommitFailedException($message, $level, $e);
        }
    }

    /**
     * Remove the chosen user from the database.
     *
     * @param   string  $userName  user name
     * @return  bool
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException   when no valid user name given
     * @throws  \Yana\Core\Exceptions\NotFoundException          when the given user does not exist
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  when the user may not be deleted for other reasons
     */
    public function removeUser($userName)
    {
        assert('is_string($userName); // Wrong type for argument $userName. String expected');
        $upperCaseUserName = \Yana\Util\String::toUpperCase($userName);
        // user should not delete himself
        if ($this->_createLoginManager()->isLoggedIn($this->_loadUser($upperCaseUserName))) {
            throw new \Yana\Core\Exceptions\User\DeleteSelfException();
        }

        $this->_createUserAdapter()->offsetUnset($upperCaseUserName); // may throw NotFoundException or NotDeletedException
    }

    /**
     * Check if user exists.
     *
     * Returns bool(true) if a user named $userName can be found in the current database.
     * Returns bool(false) otherwise.
     *
     * @param   string  $userName   user name
     * @return  bool
     */
    public function isExistingUserName($userName)
    {
        assert('is_string($userName); // Wrong type for argument 1. String expected');

        return $this->_createUserBuilder()->isExistingUserName($userName);
    }

}

?>