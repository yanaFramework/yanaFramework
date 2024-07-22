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
class Facade extends \Yana\Security\AbstractFacade implements \Yana\Security\IsFacade
{

    /**
     * Rescan plugin list and refresh the action security settings.
     *
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if new entries could not be inserted
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  if existing entries could not be deleted
     * @return  self
     */
    public function refreshPluginSecurityRules()
    {
        $refreshRequirements = $this->_getContainer()->getDataWriter();
        $refreshRequirements($this->_getContainer()->getEventConfigurationsForPlugins());
        return $this;
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
     * class MyCheck implements \Yana\Security\Rules\IsRule
     * {
     *   public function __invoke(IsRequirement $required, $profileId, $action, IsBehavior $user)
     *   {
     *     return $required->getLevel() &lgt;= $user->getSecurityLevel();
     *   }
     * }
     * </code>
     * The code above returns true, if the user's security level is higher or equal the required
     * level. The check is added when the plugin is created.
     *
     * @param   \Yana\Security\Rules\IsRule  $rule  to be validated
     * @return  $this
     */
    public function addSecurityRule(\Yana\Security\Rules\IsRule $rule)
    {
        $this->_getContainer()->getRulesChecker()->addSecurityRule($rule);
        return $this;
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
     * @param   string|NULL  $profileId  profile id
     * @param   string|NULL  $action     action
     * @param   string       $userName   user name
     * @return  bool
     */
    public function checkRules(?string $profileId = null, ?string $action = null, string $userName = ""): bool
    {
        /* Argument 1 */
        if (empty($profileId)) {
            $profileId = $this->_getContainer()->getProfileId();
        }
        assert(is_string($profileId), 'is_string($profileId)');
        assert(!isset($uppderCaseProfileId), 'Cannot redeclare $uppderCaseProfileId');
        $upperCaseProfileId = \Yana\Util\Strings::toUpperCase((string) $profileId);

        /* Argument 2 */
        if (is_null($action) || $action === "") {
            $action = $this->_getContainer()->getLastPluginAction();
            // security restriction on undefined event
            if (!($action > "")) {
                return false;
            }
        }
        assert(is_string($action), 'is_string($action)');
        assert(!isset($lowerCaseAction), 'Cannot redeclare $lowerCaseAction');
        $lowerCaseAction = \Yana\Util\Strings::toLowerCase((string) $action);

        /* Argument 3 */
        /**
         * {@internal
         * The user id is resolved by the "user" plugin and stored in a session var, so other plugins can look it up.
         * }}
         */
        if (empty($userName)) {
            $userName = $this->_getContainer()->getSession()->getCurrentUserName();
        }

        assert(!isset($user), 'Cannot redeclare $user');
        $user = $this->_buildUserEntity((string) $userName);

        assert(!isset($e), 'Cannot redeclare $e');
        try {

            assert(!isset($result), 'Cannot redeclare $result');
            $result = $this->_getContainer()->getRulesChecker()->checkRules($upperCaseProfileId, $lowerCaseAction, $user);

        } catch (\Yana\Security\Rules\Requirements\NotFoundException $e) {
            // @codeCoverageIgnoreStart
            // This is only thrown if the rules table is entirely empty.
            $this->_getContainer()->getLogger()->addLog($e->getMessage());
            $result = false;
            unset($e);
            // @codeCoverageIgnoreEnd
        }

        assert(is_bool($result), 'is_bool($result)');
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
    public function checkByRequirement(\Yana\Security\Rules\Requirements\IsRequirement $requirement, string $profileId, string $action, string $userName = ""): bool
    {
        return (bool) $this->_getContainer()->getRulesChecker()->checkByRequirement($requirement, $profileId, $action, $this->_buildUserEntity($userName));
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
    public function loadListOfGroups(): array
    {
        return $this->_getContainer()->getDataReader()->loadListOfGroups();
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
    public function loadListOfRoles(): array
    {
        return $this->_getContainer()->getDataReader()->loadListOfRoles();
    }

    /**
     * Build a user object from database and return it.
     *
     * @param   string  $userName  identifies user, retrieves user name from session if left empty
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found in the database
     */
    public function loadUser(string $userName = ""): \Yana\Security\Data\Behaviors\IsBehavior
    {
        return $this->_buildUserEntity($userName);
    }

    /**
     * Returns a list of all users.
     *
     * Returned array will have the user id and names as keys and values respectively.
     *
     * @return  array
     */
    public function loadListOfUsers(): array
    {
        return $this->_getContainer()->getUserAdapter()->getIds();
    }

    /**
     * Finds and returns an user account from the database.
     *
     * Use this function if, for example, you need to send a user an e-mail during the password recovery process.
     *
     * @param   string  $mail  unique mail address
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\MailNotFoundException  when no such user exists
     */
    public function findUserByMail(string $mail): \Yana\Security\Data\Behaviors\IsBehavior
    {
        return $this->_createUserBuilder()->buildFromUserMail($mail);
    }

    /**
     * Finds and returns an user account from the database.
     *
     * Use this function if, for example, you need to recover a user based on and id, during the password recovery process.
     *
     * @param   string  $recoveryId  unique identifier provided by user input
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no such user exists
     */
    public function findUserByRecoveryId(string $recoveryId): \Yana\Security\Data\Behaviors\IsBehavior
    {
        return $this->_createUserBuilder()->buildFromRecoveryId($recoveryId);
    }

    /**
     * Create a new user.
     *
     * @param   string  $userName  user name
     * @param   string  $mail      e-mail address
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\MissingNameException    when no user name is given
     * @throws  \Yana\Core\Exceptions\User\MissingMailException    when no mail address is given
     * @throws  \Yana\Core\Exceptions\User\AlreadyExistsException  if another user with the same name already exists
     * @throws  \Yana\Db\CommitFailedException                     when the database entry could not be created
     */
    public function createUser(string $userName, string $mail): \Yana\Security\Data\Behaviors\IsBehavior
    {
        if (empty($userName)) {
            throw new \Yana\Core\Exceptions\User\MissingNameException("No user name given.", \Yana\Log\TypeEnumeration::WARNING);
        }

        if (empty($mail)) {
            throw new \Yana\Core\Exceptions\User\MissingMailException("No mail address given.", \Yana\Log\TypeEnumeration::WARNING);
        }

        assert(!isset($builder), '$builder already declared');
        $builder = $this->_createUserBuilder();

        if ($builder->isExistingUserName($userName)) {
            throw new \Yana\Core\Exceptions\User\AlreadyExistsException(
                "A user with the name '$userName' already exists.", \Yana\Log\TypeEnumeration::WARNING
            );
        }

        $user = $builder->buildNewUser($userName, $mail);
        assert($user instanceof \Yana\Security\Data\Behaviors\IsBehavior);

        try {
            $user->saveChanges(); // may throw exception

        } catch (\Exception $e) {
            $message = "Unable to commit changes to the database server while trying to update settings for user '{$userName}'.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Db\CommitFailedException($message, $level, $e);
        }
        return $user;
    }

    /**
     * Create a new user using form data.
     *
     * Note: This does NOT save the user.
     *
     * @param   array  $formData  needs to match the form described in the user database
     * @param   array  $adapter   optional user adapter to inject
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\MissingNameException    when no user name is given
     * @throws  \Yana\Core\Exceptions\User\MissingMailException    when no mail address is given
     * @throws  \Yana\Core\Exceptions\User\AlreadyExistsException  if another user with the same name already exists
     */
    public function createUserByFormData(array $formData, ?\Yana\Security\Data\Users\IsDataAdapter $adapter = null): \Yana\Security\Data\Behaviors\IsBehavior
    {
        assert(!isset($entity), '$entity already declared');
        $entity = $this->_getContainer()->getUserAdapter()->toEntity($formData); // May throw MissingNameException
        if (is_null($adapter)) {
            $adapter = $this->_getContainer()->getUserAdapter();
        }
        $entity->setDataAdapter($adapter);
        assert(!isset($builder), '$builder already declared');
        $builder = $this->_createUserBuilder();
        assert(!isset($user), '$user already declared');
        $user = $builder->__invoke($entity);

        if ($user->getMail() === "") {
            throw new \Yana\Core\Exceptions\User\MissingMailException("No mail address given.", \Yana\Log\TypeEnumeration::WARNING);
        }

        if ($builder->isExistingUserName($user->getId())) {
            throw new \Yana\Core\Exceptions\User\AlreadyExistsException(
                "A user with the name '{$user->getId()}' already exists.", \Yana\Log\TypeEnumeration::WARNING
            );
        }
        return $user;
    }

    /**
     * Remove the chosen user from the database.
     *
     * @param   string  $userName               user name
     * @param   bool    $allowUserToDeleteSelf  overwrites self-check, use with caution!
     * @return  \Yana\Security\Facade
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException   when no valid user name given
     * @throws  \Yana\Core\Exceptions\User\DeleteAdminException  when trying to delete the "ADMINISTRATOR" default account
     * @throws  \Yana\Core\Exceptions\NotFoundException          when the given user does not exist
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  when the user may not be deleted for other reasons
     */
    public function removeUser(string $userName, bool $allowUserToDeleteSelf = false)
    {
        $upperCaseUserName = \Yana\Util\Strings::toUpperCase($userName);
        $user = $this->_buildUserEntity($upperCaseUserName); // throws NotFoundException
        // user should not delete himself
        switch (true)
        {
            case $upperCaseUserName === 'ADMINISTRATOR':
                $message = 'Administrator account must not be deleted. This might cause the application to become inaccessible.';
                throw new \Yana\Core\Exceptions\User\DeleteAdminException($message, \Yana\Log\TypeEnumeration::WARNING);

            case !$allowUserToDeleteSelf && $user->isLoggedIn():
                $message = 'Current settings don\'t allow you to delete your own account.';
                throw new \Yana\Core\Exceptions\User\DeleteSelfException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        $this->_getContainer()->getUserAdapter()->offsetUnset($upperCaseUserName); // throws NotDeletedException
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
    public function isExistingUserName(string $userName): bool
    {
        return $this->_createUserBuilder()->isExistingUserName($userName);
    }

}

?>
