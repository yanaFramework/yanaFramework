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
 * <<interface>> Simplifies dealing with user information.
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
interface IsFacade
{

    /**
     * Rescan plugin list and refresh the action security settings.
     *
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if new entries could not be inserted
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  if existing entries could not be deleted
     * @return  $this
     */
    public function refreshPluginSecurityRules();

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
    public function addSecurityRule(\Yana\Security\Rules\IsRule $rule);

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
    public function checkRules(?string $profileId = null, ?string $action = null, string $userName = ""): bool;

    /**
     * Check requirements against given rules.
     *
     * @param   \Yana\Security\Rules\Requirements\IsRequirement  $requirement  that will be checked
     * @param   string                                           $profileId    profile id
     * @param   string                                           $action       action name
     * @param   string                                           $userName     user name
     * @return  bool
     */
    public function checkByRequirement(\Yana\Security\Rules\Requirements\IsRequirement $requirement, string $profileId, string $action, string $userName = ""): bool;

    /**
     * Get user groups.
     *
     * Returns an array of group names, where the keys are the group ids and the values are the human-readable group names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function loadListOfGroups(): array;

    /**
     * Get user roles.
     *
     * Returns an array of role names, where the keys are the group ids and the values are the human-readable role names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function loadListOfRoles(): array;

    /**
     * Returns a list of all users.
     *
     * Returned array will have the user id and names as keys and values respectively.
     *
     * @return  array
     */
    public function loadListOfUsers(): array;

    /**
     * Build a user object from database and return it.
     *
     * @param   string  $userName  identifies user, retrieves user name from session if left empty
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found in the database
     */
    public function loadUser(string $userName = ""): \Yana\Security\Data\Behaviors\IsBehavior;

    /**
     * Loads and returns an user account from the database.
     *
     * @param   string  $mail  unique mail address
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\MailNotFoundException  when no such user exists
     */
    public function findUserByMail(string $mail): \Yana\Security\Data\Behaviors\IsBehavior;

    /**
     * Finds and returns an user account from the database.
     *
     * Use this function if, for example, you need to recover a user based on and id, during the password recovery process.
     *
     * @param   string  $recoveryId  unique identifier provided by user input
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no such user exists
     */
    public function findUserByRecoveryId(string $recoveryId): \Yana\Security\Data\Behaviors\IsBehavior;

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
    public function createUser(string $userName, string $mail): \Yana\Security\Data\Behaviors\IsBehavior;

    /**
     * Create a new user using form data.
     *
     * @param   array  $formData  needs to match the form described in the user database
     * @param   array  $adapter   optional user adapter to inject
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\MissingNameException    when no user name is given
     * @throws  \Yana\Core\Exceptions\User\MissingMailException    when no mail address is given
     * @throws  \Yana\Core\Exceptions\User\AlreadyExistsException  if another user with the same name already exists
     */
    public function createUserByFormData(array $formData, ?\Yana\Security\Data\Users\IsDataAdapter $adapter = null): \Yana\Security\Data\Behaviors\IsBehavior;

    /**
     * Remove the chosen user from the database.
     *
     * @param   string  $userName               user name
     * @param   bool    $allowUserToDeleteSelf  overwrites self-check, use with caution!
     * @return  $this
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException   when no valid user name given
     * @throws  \Yana\Core\Exceptions\User\DeleteAdminException  when trying to delete the "ADMINISTRATOR" default account
     * @throws  \Yana\Core\Exceptions\NotFoundException          when the given user does not exist
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  when the user may not be deleted for other reasons
     */
    public function removeUser(string $userName, bool $allowUserToDeleteSelf = false);

    /**
     * Check if user exists.
     *
     * Returns bool(true) if a user named $userName can be found in the current database.
     * Returns bool(false) otherwise.
     *
     * @param   string  $userName   user name
     * @return  bool
     */
    public function isExistingUserName(string $userName): bool;

}

?>
