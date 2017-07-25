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
class SecurityLevelRule extends \Yana\Security\Rules\AbstractRule
{

    /**
     * @var  \Yana\Security\Sessions\IsWrapper
     */
    private $_session = null;

    /**
     * Initialize session wraper.
     *
     * @param  \Yana\Security\Sessions\IsWrapper  $session  a session wrapper
     */
    public function __construct(\Yana\Security\Sessions\IsWrapper $session)
    {
        $this->_session = $session;
    }

    /**
     * Returns a session wrapper.
     *
     * If none is set, a default wrapper is created.
     *
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    protected function _getSession()
    {
        if (!isset($this->_session)) {
            $this->_session = new \Yana\Security\Logins\StandardBehavior();
            new \Yana\Security\Logins\StandardBehavior();
        }
        return $this->_session;
    }

    /**
     * Check security level.
     *
     * @param   \Yana\Security\Rules\Requirements\IsRequirement  $required   list of required permissions
     * @param   string                                           $profileId  current application-profile id
     * @param   string                                           $action     name of the action the user tries to execute
     * @param   \Yana\Security\Data\Behaviors\IsBehavior         $user       user information to check
     * @return  bool
     */
    public function __invoke(\Yana\Security\Rules\Requirements\IsRequirement $required, $profileId, $action, \Yana\Security\Data\Behaviors\IsBehavior $user)
    {
        if ($required->getLevel() < 0) {
            return null;
        }
        if ($required->getLevel() === 0) {
            return true;
        }
        if (!$user->isLoggedIn()) {
            return false;
        }

        return $required->getLevel() <= (int) $user->getSecurityLevel($profileId);
    }

}

?>