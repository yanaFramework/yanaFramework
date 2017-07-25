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
     * @return  self
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
     * @param   \Yana\Security\Rules\IsRule  $rule  to be validated
     * @return  self
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
     * @param   string  $profileId  profile id
     * @param   string  $action     action
     * @param   string  $userName   user name
     * @return  bool
     */
    public function checkRules($profileId = null, $action = null, $userName = "");

    /**
     * Check requirements against given rules.
     *
     * @param   \Yana\Security\Rules\Requirements\IsRequirement  $requirement  that will be checked
     * @param   string                                           $profileId    profile id
     * @param   string                                           $action       action name
     * @param   string                                           $userName     user name
     * @return  bool
     */
    public function checkByRequirement(\Yana\Security\Rules\Requirements\IsRequirement $requirement, $profileId, $action, $userName);

    /**
     * Get user groups.
     *
     * Returns an array of group names, where the keys are the group ids and the values are the human-readable group names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function loadListOfGroups();

    /**
     * Get user roles.
     *
     * Returns an array of role names, where the keys are the group ids and the values are the human-readable role names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function loadListOfRoles();

    /**
     * Returns a list of all users.
     *
     * Returned array will have the user id and names as keys and values respectively.
     *
     * @return  array
     */
    public function loadListOfUsers();

    /**
     * Build a user object from database and return it.
     *
     * @param   string  $userName  identifies user, retrieves user name from session if left empty
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found in the database
     */
    public function loadUser($userName = "");

    /**
     * Loads and returns an user account from the database.
     *
     * @param   string  $mail  unique mail address
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\MailNotFoundException  when no such user exists
     */
    public function findUserByMail($mail);

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
    public function createUser($userName, $mail);

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
    public function removeUser($userName, $allowUserToDeleteSelf = false);

    /**
     * Check if user exists.
     *
     * Returns bool(true) if a user named $userName can be found in the current database.
     * Returns bool(false) otherwise.
     *
     * @param   string  $userName   user name
     * @return  bool
     */
    public function isExistingUserName($userName);

}

?>