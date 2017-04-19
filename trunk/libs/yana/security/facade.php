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
     * @var  \Yana\Security\Dependencies\Container
     */
    private $_container = null;

    /**
     * Creates dependency container on demand and returns it.
     *
     * @return  \Yana\Security\Dependencies\Container
     */
    protected function _getContainer()
    {
        if (!isset($this->_container)) {
            $this->_container = new \Yana\Security\Dependencies\Container();
        }
        return $this->_container;
    }

    /**
     * Creates a session wrapper on demand and returns it.
     *
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    protected function _getSession()
    {
        return $this->_getContainer()->getSession();
    }

    /**
     * Builds and returns a rule-checker object.
     *
     * @return  \Yana\Security\Rules\CacheableChecker
     */
    protected function _getRulesChecker()
    {
        return $this->_getContainer()->getRulesChecker();
    }

    /**
     * @return \Yana\Security\Rules\Requirements\DataReader
     */
    private function _createDataReader()
    {
        $default = \Yana\Application::getDefault('event.user');
        if (!is_array($default)) {
            $default = array();
        }
        return new \Yana\Security\Rules\Requirements\DefaultableDataReader($this->_getDataSource(), $default);
    }

    /**
     * @return \Yana\Security\Data\Users\Adapter
     */
    protected function _createUserAdapter()
    {
        return new \Yana\Security\Data\Users\Adapter($this->_getDataSource());
    }

    /**
     * @return \Yana\Security\Data\UserBuilder
     */
    protected function _createUserBuilder()
    {
        return new \Yana\Security\Data\UserBuilder($this->_createUserAdapter());
    }

    /**
     * @param   string  $userName  identifies user
     * @return  \Yana\Security\Data\IsUser
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found in the database
     */
    protected function _buildUserEntity($userName = "")
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
        $user = empty($userName) ? new \Yana\Security\Data\Users\Guest() : $this->_buildUserEntity((string) $userName);

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

        return (bool) $this->_getRulesChecker()->checkByRequirement($requirement, $profileId, $action, $this->_buildUserEntity($userName));
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
     * 
     * @param   string  $userName  identifies user
     * @return  \Yana\Security\Data\IsUser
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found in the database
     */
    public function loadUser($userName)
    {
        $entity = $this->_buildUserEntity($userName);
        
        $passwords = new \Yana\Security\Passwords\Behaviors\StandardBehavior(
            //$this->_createPasswordAlgorithm(), $this->_createPasswordCheck(), $this->_createPasswordGenerator()
        );
        $behavior = new \Yana\Security\Data\Behaviors\Standard($passwords, $logins);
        return $behavior;
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
     * @return  \Yana\Security\Facade
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException   when no valid user name given
     * @throws  \Yana\Core\Exceptions\NotFoundException          when the given user does not exist
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  when the user may not be deleted for other reasons
     */
    public function removeUser($userName)
    {
        assert('is_string($userName); // Wrong type for argument $userName. String expected');
        $upperCaseUserName = \Yana\Util\String::toUpperCase($userName);
        // user should not delete himself
        if ($this->_buildUserEntity($upperCaseUserName)->isLoggedIn()) { // throws NotFoundException
            throw new \Yana\Core\Exceptions\User\DeleteSelfException();
        }

        $this->_createUserAdapter()->offsetUnset($upperCaseUserName); // throws NotDeletedException
        return $this;
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