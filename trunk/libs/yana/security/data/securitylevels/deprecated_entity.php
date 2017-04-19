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

namespace Yana\Security\Data\SecurityLevels;

/**
 * <<entity>> Security level.
 *
 * Holds user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Deprecated_Entity extends \Yana\Data\Adapters\AbstractEntity
{

    /** @var int    */ private $_id = null;
    /** @var string */ private $_userId = null;
    /** @var int    */ private $_securityLevel = 0;
    /** @var string */ private $_profile = null;
    /** @var string */ private $_userCreated = null;
    /** @var bool   */ private $_userProxyActive = true;

    /**
     * Get row id.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get id of user this security level applies to.
     *
     * @return  string
     */
    public function getUserId()
    {
        return $this->_userId;
    }

    /**
     * Get granted security level between 0 and 100.
     *
     * @return  int
     */
    public function getSecurityLevel()
    {
        return $this->_securityLevel;
    }

    /**
     * Get id of profile this applies to.
     *
     * @return  string
     */
    public function getProfile()
    {
        return $this->_profile;
    }

    /**
     * Get id of user who created this rule.
     *
     * Note: the user may no longer exist. This is for documentation purposes only.
     *
     * @return  string
     */
    public function getUserCreated()
    {
        return $this->_userCreated;
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
    public function getUserProxyActive()
    {
        return $this->_userProxyActive;
    }

    /**
     * Add or change primary key.
     *
     * @param   int  $id  primary key
     * @return  self
     */
    public function setId($id)
    {
        assert('is_int($id); // Wrong type for argument $id. Integer expected');
        $this->_id = (int) $id;
        return $this;
    }

    /**
     * Set Id of user this refers to.
     *
     * @param   string  $userId  current user name
     * @return  self
     */
    public function setUserId($userId)
    {
        assert('is_string($userName); // Wrong type for argument $userName. String expected');
        assert('$userName > ""; // Invalid argument $userName. Cannot be empty');
        $this->_userId = (string) $userId;
        return $this;
    }

    /**
     * Set granted security level between 0 and 100.
     *
     * @param   int  $securityLevel  between 0 (none) and 100 (all)
     * @return  self
     */
    public function setSecurityLevel($securityLevel)
    {
        assert('is_int($securityLevel); // Wrong type for argument $securityLevel. Integer expected');
        assert('$securityLevel >= 0 && $securityLevel <= 100; // Invalid argument $securityLevel. Must be between 0 and 100');

        $this->_securityLevel = (int) $securityLevel;
        return $this;
    }

    /**
     * Set id of profile this applies to.
     *
     * @param   string  $profile  id
     * @return  self
     */
    public function setProfile($profile)
    {
        assert('is_string($profile); // Wrong type for argument $profile. String expected');
        $this->_profile = $profile;
        return $this;
    }

    /**
     * Set id of user who created this rule.
     *
     * Note: the user may no longer exist. This is for documentation purposes only.
     *
     * @param   string  $userCreated  user id
     * @return  self
     */
    public function setUserCreated($userCreated)
    {
        assert('is_string($userCreated); // Wrong type for argument $userCreated. String expected');
        assert('$userCreated > ""; // Invalid argument $userCreated. Cannot be empty');
        $this->_userCreated = $userCreated;
        return $this;
    }

    /**
     * Set proxy option.
     *
     * Set to bool(true) if this user should be allowed to forward this security setting
     * to another user named to act as a temporary proxy. Set to bool(false) otherwise.
     *
     * Note: this is just a setting. The actual proxy implementation needs to be done by plugins.
     *
     * @param   bool    $userProxyActive  is allowed to be used as proxy
     * @return  self
     */
    public function setUserProxyActive($userProxyActive)
    {
        assert('is_bool($userProxyActive); // Wrong type for argument $userProxyActive. Boolean expected');
        $this->_userProxyActive = (bool) $userProxyActive;
        return $this;
    }

}

?>