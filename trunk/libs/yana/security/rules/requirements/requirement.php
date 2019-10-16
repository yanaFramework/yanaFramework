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

namespace Yana\Security\Rules\Requirements;

/**
 * Security requirement.
 *
 * The security rules are checked against these.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Requirement extends \Yana\Core\StdObject implements \Yana\Security\Rules\Requirements\IsRequirement
{

    const DEFAULT_LEVEL = -1;

    /**
     * user group
     *
     * @var  string
     */
    private $_group = "";

    /**
     * user role
     *
     * @var  string
     */
    private $_role = "";

    /**
     * user level
     *
     * @var  int
     */
    private $_level = SELF::DEFAULT_LEVEL;

    /**
     * Create a new requirement.
     *
     * @param  string  $group  has to be part of this user group, "" = does not apply
     * @param  string  $role   has to have this user role, "" = does not apply
     * @param  int     $level  has to have this or greater security level, -1 = does not apply, 0 = public
     */
    public function __construct($group, $role, $level = SELF::DEFAULT_LEVEL)
    {
        assert(is_string($group), 'Invalid argument $group: String expected');
        assert(is_string($role), 'Invalid argument $role: String expected');
        assert(is_int($level), 'Invalid argument $level: Integer expected');
        assert($level >= -1, 'Security level cannot be smaller than -1');
        assert($level <= 100, 'Security level cannot be greater than 100');
        $this->_group = (string) $group;
        $this->_role = (string) $role;
        $this->_level = (int) $level;
    }

    /**
     * Returns the required group the user must be a member of.
     *
     * Returns an empty string if the group does not apply to this rule and should be ignored.
     *
     * @return  string
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * Returns the required role the user must have.
     *
     * Returns an empty string if the role does not apply to this rule and should be ignored.
     *
     * @return  string
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * Returns the required minimum security level the user must have.
     *
     * Default is: -1 (if the level does not matter for this rule and should be ignored).
     *
     * @return  int
     */
    public function getLevel()
    {
        return $this->_level;
    }

}

?>