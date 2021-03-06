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
 */

namespace Yana\Plugins\Configs;

/**
 * User permission management information.
 *
 * Rights management comes in 3-layer, each of which is optional in this document.
 * <ul>
 *  <li> User groups: like Sales, Human Ressources </li>
 *  <li> User role: like Project Manager </li>
 *  <li> Security level: an integer of 0 through 100 </li>
 * </ul>
 *
 * @package     yana
 * @subpackage  plugins
 * @ignore
 */
class UserPermissionRule extends \Yana\Core\StdObject implements \Yana\Plugins\Configs\IsUserPermissionRule
{

    /**
     * @var  string
     */
    private $_role = "";

    /**
     * @var  string
     */
    private $_group = "";

    /**
     * @var  int
     */
    private $_level = 0;

    /**
     * get user role
     *
     * The role a user plays inside a user group.
     * This may be any string value.
     *
     * @return  string
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * set role
     *
     * The role a user plays inside a user group.
     * This may be any string value.
     *
     * Note that it is not checked wether the role is in use ore not.
     *
     * @param   string  $role  new value of this property, allowed characters: 0-9, a-z, -, _
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when parameter is not alpha-numeric
     * @return  self
     */
    public function setRole($role)
    {
        assert(is_string($role), 'Wrong type for argument 1. String expected');
        if (!preg_match('/^[\d\w\-_]*$/si', $role)) {
            $message = "Invalid characters in role '$role'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $this->_role = (string) $role;
        return $this;
    }

    /**
     * get user group
     *
     * The group a user belongs. Each group defines it's own default security level (which may be
     * overwritten though).
     *
     * You may additionally define security levels to check.
     *
     * @return  string
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * set user group
     *
     * The group a user belongs. Each group defines it's own default security level (which may be
     * overwritten though).
     *
     * You may additionally define security levels to check.
     *
     * Note that it is not checked wether the group is in use ore not.
     *
     * @param   string  $group  new value of this property, allowed characters: 0-9, a-z, -, _
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when parameter is not alpha-numeric
     * @return  self
     */
    public function setGroup($group)
    {
        assert(is_string($group), 'Invalid argument $group: string expected');
        if (!preg_match('/^[\d\w\-_]*$/si', $group)) {
            $message = "Invalid characters in group '$group'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $this->_group = (string) $group;
        return $this;
    }

    /**
     * get security level
     *
     * The security level may be any integer number of 0 through 100.
     * You may translate this to 0-100 percent, where 0 is the lowest level of access and 100 is the
     * highest.
     *
     * @return  int
     */
    public function getLevel()
    {
        return $this->_level;
    }

    /**
     * set security level
     *
     * The security level may be any integer number of 0 through 100.
     * You may translate this to 0-100 percent, where 0 is the lowest level of access and 100 is the
     * highest.
     *
     * @param   int  $level  new value of this property
     * @return  self
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given security level is outside range [0,100]
     */
    public function setLevel($level)
    {
        assert(is_int($level), 'Wrong type for argument 1. Integer expected');
        if ($level < 0 || $level > 100) {
            $message = "Security level '$level' outside range [0,100].";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $this->_level = (int) $level;
        return $this;
    }

}

?>