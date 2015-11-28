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
 * Password check.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractCheck extends \Yana\Core\Object implements \Yana\Security\Passwords\Checks\IsCheck
{

    /**
     * Check username.
     *
     * Returns bool(true) if the given username and the user id of the entity
     * are the same (ignoring case).
     * Returns bool(false) otherwise.
     *
     * @param   \Yana\Security\Users\IsUser  $user      entity
     * @param   string                       $userName  user name
     * @return  bool
     */
    protected function _isValidUserName(\Yana\Security\Users\IsUser $user, $userName)
    {
        return \Yana\Util\String::compareToIgnoreCase($user->getId(), $userName) === 0;
    }

}

?>