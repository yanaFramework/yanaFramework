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
     * Default is -1, which is an invalid ID and will be mapped to NULL.
     *
     * @var int
     */
    private $_id = -1;

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
     * @var string
     */
    private $_userName = "";

    /**
     * @var string
     */
    private $_grantedByUser = "";

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
     * Note: the default is -1.
     * An ID of -1 translates to "unknown".
     *
     * It doesn't mean that there is a database entry with the ID -1.
     * And it doesn't mean that there this entry is not in the database either.
     * It simply means precisely what it says: the real Id is unknown.
     *
     * This may be the case if the case (for example) if the entry is new,
     * and either has not been assigned an ID, or if the entry was saved using auto-increment
     * and we simply don't know what ID has been assigned to it.
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

    /**
     * Get the id of the user this rule applies to.
     *
     * @return  string
     */
    public function getUserName()
    {
        return $this->_userName;
    }

    /**
     * Get the id of the user who created this rule.
     *
     * @return  string
     */
    public function getGrantedByUser()
    {
        return $this->_grantedByUser;
    }

    /**
     * Set associated application profile.
     *
     * @param   string  $profileName  application profile id
     * @return  self
     */
    public function setProfile($profileName)
    {
        assert('is_string($profileName); // Invalid argument $profileName: string expected');
        $this->_profile = (string) $profileName;
        return $this;
    }

    /**
     * Set the id of the user this rule applies to.
     *
     * @param   string  $userName  id referencing user table
     * @return  self
     */
    public function setUserName($userName)
    {
        assert('is_string($userName); // Invalid argument $userName: string expected');

        $this->_userName = (string) $userName;
        return $this;
    }

    /**
     * Set the id of the user who created this rule.
     *
     * @param   string  $createdByUser  id referencing user table
     * @return  self
     */
    public function setGrantedByUser($createdByUser)
    {
        assert('is_string($createdByUser); // Invalid argument $createdByUser: string expected');

        $this->_grantedByUser = (string) $createdByUser;
        return $this;
    }

    /**
     * Grant this permission to another user.
     *
     * @param   string  $userName  user id (will trigger database exception if not valid)
     * @return  self
     * @throws  \Yana\Core\Exceptions\User\NotGrantableException                when the permission has no grant option
     * @throws  \Yana\Core\Exceptions\User\RuleAlreadyExistsExceptionException  when the new permission can't be saved
     * @throws  \Yana\Core\Exceptions\User\RuleNotSavedException                when a similar entry already exists
     */
    public function grantTo($userName)
    {
        assert('is_string($userName); // Invalid argument $userName: string expected');
        if (!$this->isUserProxyActive()) {
            $message = "This permission cannot be granted to another user.";
            $code = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\User\NotGrantableException($message, $code);
        }
        $permission = new self($this->getGroup(), $this->getRole(), false);
        $permission
            ->setDataAdapter($this->_getDataAdapter())
            ->setUserName((string) $userName)
            ->setGrantedByUser($this->getUserName())
            ->setProfile($this->getProfile())
            ->saveEntity(); // may throw exception
        return $this;
    }

}

?>