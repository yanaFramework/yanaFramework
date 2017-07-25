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
 * Security rule
 *
 * @package    yana
 * @subpackage plugins
 */
class SecurityGroupRule extends \Yana\Security\Rules\AbstractRule
{

    /**
     * @var  string
     */
    private $_defaultProfileId = "";

    /**
     * Initialize dependencies
     *
     * @param  string  $defaultProfileId  used as fallback
     */
    public function __construct($defaultProfileId)
    {
        assert('is_string($defaultProfileId); // Wrong type for argument: $defaultProfileId. String expected');
        $this->_defaultProfileId = (string) $defaultProfileId;
    }

    /**
     * Returns the name of a profile to be used as default.
     *
     * @return  string
     */
    protected function _getDefaultProfileId()
    {
        return $this->_defaultProfileId;
    }

    /**
     * Check security rule.
     *
     * The settings of the default profile are supposed to be inherited by all other profiles.
     * Meaning, if somebody is an administrator for the default profile,
     * it is not necessary to redeclare this for every other profile.
     * Note that this means that it is impossible to remove permissions that are already granted
     * by the default profile.
     *
     * @param   \Yana\Security\Rules\Requirements\IsRequirement  $required   list of required permissions
     * @param   string                                           $profileId  current application-profile id
     * @param   string                                           $action     name of the action the user tries to execute
     * @param   \Yana\Security\Data\Behaviors\IsBehavior         $user       user information to check
     * @return  bool
     */
    public function __invoke(\Yana\Security\Rules\Requirements\IsRequirement $required, $profileId, $action, \Yana\Security\Data\Behaviors\IsBehavior $user)
    {
        // rule does not apply
        if ($required->getGroup() === "" && $required->getRole() === "") {
            return null;
        }

        $requiredRole = $required->getRole();
        $requiredGroup = $required->getGroup();
        $collection = $user->getSecurityGroupsAndRoles($profileId);
        switch (true)
        {
            case $required->getGroup() === "" && $collection->hasRole($requiredRole):
            case $required->getRole() === "" && $collection->hasGroup($requiredGroup):
            case $collection->hasGroupAndRole($action, $profileId):
                return true;
        }
        unset($collection);

        $defaultCollection = $user->getSecurityGroupsAndRoles($this->_getDefaultProfileId());
        switch (true)
        {
            case $required->getGroup() === "" && $defaultCollection->hasRole($requiredRole):
            case $required->getRole() === "" && $defaultCollection->hasGroup($requiredGroup):
            case $defaultCollection->hasGroupAndRole($action, $profileId):
                return true;
        }
        unset($defaultCollection);

        return false;
    }

}

?>