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
class Rule extends \Yana\Core\Object implements \Yana\Security\Data\SecurityRules\IsRule
{

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
     * Initalize properties.
     *
     * @param  string  $group    id
     * @param  string  $role     id
     * @param  bool    $isProxy  is proxy for another user
     */
    public function __construct($group, $role, $isProxy)
    {
        assert('is_string($group); // Wrong type for argument $group. String expected');
        assert('is_string($role); // Wrong type for argument $role. String expected');
        assert('is_bool($isProxy); // Wrong type for argument $isProxy. Boolean expected');
        $this->_group = (string) $group;
        $this->_role = (string) $role;
        $this->_userProxyActive = (bool) $isProxy;
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

}

?>