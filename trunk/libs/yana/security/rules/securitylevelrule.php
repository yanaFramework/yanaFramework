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

namespace Yana\Security\Rules;

/**
 * Security rule
 *
 * @package    yana
 * @subpackage plugins
 */
class SecurityLevelRule extends \Yana\Security\Rules\AbstractRule
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
    public function __construct(string $defaultProfileId)
    {
        $this->_defaultProfileId = $defaultProfileId;
    }

    /**
     * Returns the name of a profile to be used as default.
     *
     * @return  string
     */
    protected function _getDefaultProfileId(): string
    {
        return $this->_defaultProfileId;
    }

   /**
     * Check security level.
     *
     * @param   \Yana\Security\Rules\Requirements\IsRequirement  $required   list of required permissions
     * @param   string                                           $profileId  current application-profile id
     * @param   string                                           $action     name of the action the user tries to execute
     * @param   \Yana\Security\Data\Behaviors\IsBehavior         $user       user information to check
     * @return  bool|NULL
     */
    public function __invoke(\Yana\Security\Rules\Requirements\IsRequirement $required, string $profileId, string $action, \Yana\Security\Data\Behaviors\IsBehavior $user): ?bool
    {
        if ($required->getLevel() <= 0) {
            return null;
        }
        if (!$user->isLoggedIn()) {
            return false;
        }

        return $required->getLevel() <= (int) $user->getSecurityLevel($profileId, $this->_getDefaultProfileId());
    }

}

?>