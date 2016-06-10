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

namespace Yana\Security\Users\Behaviors;

/**
 * <<abstract>> User behavior facade.
 *
 * Holds user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractBehavior extends \Yana\Core\Object implements \Yana\Security\Users\Behaviors\IsBehavior
{

    /**
     * Handles the changing of passwords.
     *
     * @var  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    private $_passwordBehavior = null;

    /**
     * Creates an user by name.
     *
     * @param  \Yana\Security\Passwords\Behaviors\IsBehavior  $behavior  password behavior, wrapping user
     */
    public function __construct(\Yana\Security\Passwords\Behaviors\IsBehavior $behavior)
    {
        $this->_passwordBehavior = $behavior;
    }

    /**
     * Returns User entity.
     *
     * @return  \Yana\Security\Users\IsUser
     */
    protected function _getEntity()
    {
        return $this->_getPasswordBehavior()->getUser();
    }

    /**
     * Returns password behavior.
     *
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    protected function _getPasswordBehavior()
    {
        return $this->_passwordBehavior;
    }

}

?>