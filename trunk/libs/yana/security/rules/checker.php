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

namespace Yana\Security\Rules;

/**
 * Rule checking class.
 *
 * Allows collection and checking of security rules.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Checker extends \Yana\Security\Rules\AbstractChecker
{

    /**
     * Add security rule.
     *
     * This method adds a user-definded implementation to a list of custom security checks.
     *
     * To execute these rules call checkPermission().
     * The rules are executed in the order in which they were added.
     *
     * @param   \Yana\Security\Rules\IsRule  $rule  that will be added
     * @return  \Yana\Security\Rules\Checker
     */
    public function addSecurityRule(\Yana\Security\Rules\IsRule $rule)
    {
        $rules = $this->_getRules();
        $rules[] = $rule;
        return $this;
    }

    /**
     * Check requirements.
     *
     * Check if user meets on of the applicable rules to apply changes to the profile identified by the argument $profileId.
     *
     * Returns bool(true) if the user's permission level is high enough to
     * execute the changes and bool(false) otherwise.
     *
     * @param   string                                    $profileId  profile id in upper-case
     * @param   string                                    $action     action parameter in lower-case
     * @param   \Yana\Security\Data\Behaviors\IsBehavior  $user       user information to check
     * @return  bool
     * @throws  \Yana\Security\Rules\Requirements\NotFoundException  when no requirements are found
     */
    public function checkRules($profileId, $action, \Yana\Security\Data\Behaviors\IsBehavior $user)
    {
        assert('is_string($profileId); // Invalid argument type: $profileId. String expected');
        assert('is_string($action); // Invalid argument type: $action. String expected');

        assert('!isset($adapter); // Cannot redeclare var $adapter');
        $adapter = $this->_getRequirementsAdapter();

        // find out what the required permission level is to perform the current action
        assert('!isset($requiredLevels); // Cannot redeclare var $requiredLevels');
        $requiredLevels = $adapter->loadRequirementsByAssociatedAction($action); // May throw a NotFoundException if table is empty.
        /**
         * NotFoundException: Because For security reasons we consider an empty requirements table an illegal state
         * and will not allow ANY operations rather than allowing ALL operations.
         */

        // if nothing else is defined, then the current action is public ...
        if ($requiredLevels->count() === 0) {
            return true;
        }

        //  ... else check user permissions
        assert('!isset($requirement); // cannot redeclare $requirement');
        foreach ($requiredLevels as $requirement)
        {
            switch ($this->_checkByRequirement($requirement, $profileId, $action, $user))
            {
                // requirement statements are connected via "OR", so we return TRUE if any of them is TRUE.
                case 1:
                    return true;
                // default: we stick with FALSE
            }
        }
        unset($requirement);


        /**
         * Deny all access by default.
         *
         * This means that even if an attacker somehow managed to deactivate all security rules,
         * the attacker will from now on ALWAYS be denied access to ANY action that lists any security requirements at all.
         *
         * However, this also means that when setting up security requirements, the developer must ensure that there
         * is always at least one active security rule that actually checks at least one of the given requirements.
         *
         * For example, if the developer defines an action using as the only requirement "@user group: ADMIN" and
         * there is no security rule to check the user group, then the action will by default be denied no matter what
         * the user group may be.
         *
         * In case the developer explicitly doesn't want this behavior, the developer is free to introduce a security
         * rule that always returns bool(true) and thus changes the default value, BUT this must be done explicitly
         * and intentionally by the developer and cannot be the default behavior.
         */
        return false;
    }

    /**
     * Check requirement.
     *
     * Check if user does meet the given requirement to apply changes to the profile identified by the argument $profileId.
     *
     * Returns bool(true) if the user may execute the changes and bool(false) otherwise.
     *
     * @param   \Yana\Security\Rules\Requirements\IsRequirement  $requirement  to check for
     * @param   string                                           $profileId    profile id in upper-case
     * @param   string                                           $action       action parameter in lower-case
     * @param   \Yana\Security\Data\Behaviors\IsBehavior         $user         user information to check
     * @return  bool
     */
    public function checkByRequirement(\Yana\Security\Rules\Requirements\IsRequirement $requirement, $profileId, $action, \Yana\Security\Data\Behaviors\IsBehavior $user)
    {
        assert('is_string($profileId); // Invalid argument type: $profileId. String expected');
        assert('is_string($action); // Invalid argument type: $action. String expected');

        /* Note: Altough we should always return bool(true) for an empty set of requirements,
         * we don't check for an empty set of requirements here; because this function is explicitly given a requirement,
         * and thus, by definition, the set cannot be empty.
         */
        return 1 === $this->_checkByRequirement($requirement, $profileId, $action, $user);
    }

    /**
     * Check requirement.
     *
     * Returns 1 if the user may proceed, 2 if the user may not, and 0 if no given rule applies.
     *
     * @param   \Yana\Security\Rules\Requirements\IsRequirement  $requirement  to check for
     * @param   string                                           $profileId    profile id in upper-case
     * @param   string                                           $action       action parameter in lower-case
     * @param   \Yana\Security\Data\Behaviors\IsBehavior         $user         user information to check
     * @return  int
     */
    private function _checkByRequirement(\Yana\Security\Rules\Requirements\IsRequirement $requirement, $profileId, $action, \Yana\Security\Data\Behaviors\IsBehavior $user)
    {
        assert('is_string($profileId); // Invalid argument type: $profileId. String expected');
        assert('is_string($action); // Invalid argument type: $action. String expected');

        assert('!isset($result); // cannot redeclare $result');
        $result = 0; // By default no rules do apply, so that this works for empty ruleset as well

        // loop through rules
        assert('!isset($rule); // cannot redeclare $rule');
        foreach ($this->_getRules()->toArray() as $rule)
        {
            switch ($rule($requirement, $profileId, $action, $user)) // Can return TRUE, FALSE, or NULL.
            {
                case false:
                    return -1; // access denied
                case true:
                    $result = 1; // access granted
                // else: rule does not apply
            }
        }
        unset($rule);

        assert('is_int($result); // return type should be integer');
        assert('$result === -1 || $result === 0 || $result === 1; // returned value must be either 0, 1, or -1');
        return $result;
    }

}

?>