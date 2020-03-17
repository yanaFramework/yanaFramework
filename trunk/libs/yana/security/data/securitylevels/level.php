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
declare(strict_types=1);

namespace Yana\Security\Data\SecurityLevels;

/**
 * Security level.
 *
 * Readonly information about the user's security level.
 *
 * @package     yana
 * @subpackage  security
 */
class Level extends \Yana\Security\Data\SecurityLevels\AbstractLevel
{

    /**
     * Default is -1, which is an invalid ID and will be mapped to NULL.
     *
     * @var int
     */
    private $_id = -1;

    /**
     * @var int
     */
    private $_securityLevel = 0;

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
     * @param  int   $level    integer between 0 and 100
     * @param  bool  $isProxy  is proxy for another user
     */
    public function __construct(int $level, bool $isProxy)
    {
        assert($level >= 0 && $level <= 100, 'Invalid argument $level. Must be between 0 and 100');
        $this->_securityLevel = (int) $level;
        $this->_userProxyActive = (bool) $isProxy;
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
     * Set the identifying value for this entity.
     *
     * @param   int  $id  numeric id
     * @return  $this
     */
    public function setId($id)
    {
        assert(is_numeric($id), 'Invalid argument type: $id. Integer expected');
        $this->_id = (int) $id;
        return $this;
    }

    /**
     * Get granted security level between 0 and 100.
     *
     * @return  int
     */
    public function getSecurityLevel(): int
    {
        return $this->_securityLevel;
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
    public function isUserProxyActive(): bool
    {
        return $this->_userProxyActive;
    }

    /**
     * Set associated application profile.
     *
     * @param   string  $profileName  application profile id
     * @return  $this
     */
    public function setProfile(string $profileName)
    {
        $this->_profile = (string) $profileName;
        return $this;
    }

    /**
     * Get associated application profile.
     *
     * @return  string
     */
    public function getProfile(): string
    {
        return $this->_profile;
    }

    /**
     * Get the id of the user this rule applies to.
     *
     * @return  string
     */
    public function getUserName(): string
    {
        return $this->_userName;
    }

    /**
     * Get the id of the user who created this rule.
     *
     * @return  string
     */
    public function getGrantedByUser(): string
    {
        return $this->_grantedByUser;
    }

    /**
     * Set the id of the user this rule applies to.
     *
     * @param   string  $userName  id referencing user table
     * @return  $this
     */
    public function setUserName(string $userName)
    {
        $this->_userName = (string) $userName;
        return $this;
    }

    /**
     * Set the id of the user who created this rule.
     *
     * @param   string  $createdByUser  id referencing user table
     * @return  $this
     */
    public function setGrantedByUser(string $createdByUser)
    {
        $this->_grantedByUser = (string) $createdByUser;
        return $this;
    }

    /**
     * Grant this permission to another user.
     *
     * @param   string  $userName  user id (will trigger database exception if not valid)
     * @return  \Yana\Security\Data\SecurityLevels\IsLevelEntity
     * @throws  \Yana\Core\Exceptions\User\NotGrantableException        when the permission has no grant option
     * @throws  \Yana\Core\Exceptions\User\LevelNotSavedException       when the new permission can't be saved
     * @throws  \Yana\Core\Exceptions\User\LevelAlreadyExistsException  when a similar entry already exists
     */
    public function grantTo(string $userName): \Yana\Security\Data\SecurityLevels\IsLevelEntity
    {
        if (!$this->isUserProxyActive()) {
            $message = "This permission cannot be granted to another user.";
            $code = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\User\NotGrantableException($message, $code);
        }
        $permission = new self($this->getSecurityLevel(), false);
        $permission
            ->setDataAdapter($this->_getDataAdapter())
            ->setUserName((string) $userName)
            ->setGrantedByUser($this->getUserName())
            ->setProfile($this->getProfile())
            ->saveEntity(); // may throw exception
        return $permission;
    }

}

?>