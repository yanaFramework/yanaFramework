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

namespace Yana\Security\Data\SecurityRules;

/**
 * Security rule.
 *
 * Readonly information representing a security rule.
 *
 * @package     yana
 * @subpackage  security
 */
class Rule extends \Yana\Security\Data\SecurityRules\AbstractRule
{

    /**
     * @var int
     */
    private $_id = 0;

    /**
     * @var string
     */
    private $_group = "";

    /**
     * @var string
     */
    private $_role = "";

    /**
     * @var bool
     */
    private $_userProxyActive = true;

    /**
     * @var string
     */
    private $_profile = "";

    /**
     * Initalize properties.
     *
     * @param  int     $id       database id
     * @param  string  $group    id
     * @param  string  $role     id
     * @param  bool    $isProxy  is proxy for another user
     * @param  string  $profile  id
     */
    public function __construct($id, $group, $role, $isProxy, $profile = "")
    {
        assert('is_int($id); // Invalid argument $id: int expected');
        assert('is_string($group); // Wrong type for argument $group. String expected');
        assert('is_string($role); // Wrong type for argument $role. String expected');
        assert('is_bool($isProxy); // Wrong type for argument $isProxy. Boolean expected');
        assert('is_string($profile); // Invalid argument $profile: string expected');

        $this->_id = (int) $id;
        $this->_group = (string) $group;
        $this->_role = (string) $role;
        $this->_userProxyActive = (bool) $isProxy;
        $this->_profile = (string) $profile;
    }

    /**
     * Set the identifying value for this entity.
     *
     * @param   int  $id  numeric id
     * @return  self
     */
    public function setId($id)
    {
        assert('is_numeric($id); // Invalid argument type: $id. Integer expected');
        $this->_id = (int) $id;
        return $this;
    }

    /**
     * Get database id for this entry.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get associated user group.
     *
     * @return  string
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * Get associated user role.
     *
     * @return  string
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * Check proxy settings.
     *
     * Returns bool(true) if this user should be allowed to forward this security setting
     * to another user named to act as a temporary proxy and bool(false) otherwise.
     *
     * Note: this is just a setting. The actual proxy implementation needs to be done by plugins.
     *
     * @return  bool
     */
    public function isUserProxyActive()
    {
        return $this->_userProxyActive;
    }

    /**
     * Get associated application profile.
     *
     * @return  string
     */
    public function getProfile()
    {
        return $this->_profile;
    }

}

?>