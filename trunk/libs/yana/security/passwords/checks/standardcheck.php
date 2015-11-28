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

namespace Yana\Security\Passwords\Checks;

/**
 * Standard-check used by the framework if nothing else is configured.
 *
 * Allows the invalid hash "UNITITIALIZED" to skip tests (used during setup) and
 * otherwise checks for matching username and password.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class StandardCheck extends \Yana\Security\Passwords\Checks\PasswordCheck
{

    /**
     * Check if password is 'uninitialized'.
     *
     * @param   \Yana\Security\Users\IsUser  $user  entity
     * @return  bool
     */
    protected function _isUninitializedPassword(\Yana\Security\Users\IsUser $user)
    {
        return \Yana\Util\String::compareToIgnoreCase($user->getPassword(), 'UNINITIALIZED') === 0;
    }

    /**
     * Check password.
     *
     * Returns bool(true) if the given password and the password of the entity
     * are the same (case-sensitive). Returns bool(false) otherwise.
     *
     * @param   \Yana\Security\Users\IsUser  $user      entity
     * @param   string                       $userName  user name
     * @param   string                       $password  password (clear text)
     * @return  bool
     */
    public function __invoke(\Yana\Security\Users\IsUser $user, $userName, $password)
    {
        assert('is_string($userName); // Invalid argument $userName: string expected');
        assert('is_string($password); // Invalid argument $password: string expected');
        return $this->_isValidUserName($user, $userName) &&
            ($this->_isUninitializedPassword($user) || $this->_isValidPassword($user, $userName, $password));
    }

}

?>