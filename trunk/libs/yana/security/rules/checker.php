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
     * @param   string                       $profileId  profile id in upper-case
     * @param   string                       $action     action parameter in lower-case
     * @param   \Yana\Security\Users\IsUser  $user       user information to check
     * @return  bool
     * @throws  \Yana\Security\Rules\Requirements\NotFoundException  when no requirements are found
     */
    public function checkRules($profileId, $action, \Yana\Security\Users\IsUser $user)
    {
        assert('is_string($profileId); // Invalid argument type: $profileId. String expected');
        assert('is_string($action); // Invalid argument type: $action. String expected');

        assert('!isset($adapter); // Cannot redeclare var $adapter');
        $adapter = $this->_getRequirementsAdapter();

        // find out what the required permission level is to perform the current action
        assert('!isset($requiredLevels); // Cannot redeclare var $requiredLevels');
        $requiredLevels = $adapter->loadRequirementsByAssociatedAction($action); // may throw exception

        // if nothing else is defined, then the current event is public ...
        if ($requiredLevels->count() === 0) {
            return true;
        }

        // ... else check user permissions
        assert('!isset($result); // Cannot redeclare var $result');
        $result = false;
        assert('!isset($requirement); // cannot redeclare $requirement');
        foreach ($requiredLevels as $requirement)
        {
            if ($this->checkByRequirement($requirement, $profileId, $action, $user)) {
                $result = true;
                break;
            }
        }
        unset($requirement);

        assert('is_bool($result); // return type should be boolean');
        return $result;
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
     * @param   \Yana\Security\Users\IsUser                      $user         user information to check
     * @return  bool
     */
    public function checkByRequirement(\Yana\Security\Rules\Requirements\IsRequirement $requirement, $profileId, $action, \Yana\Security\Users\IsUser $user)
    {
        assert('is_string($profileId); // Invalid argument type: $profileId. String expected');
        assert('is_string($action); // Invalid argument type: $action. String expected');

        return (bool) $this->_getRules()->checkRules($requirement, $profileId, $action, $user);
    }

}

?>