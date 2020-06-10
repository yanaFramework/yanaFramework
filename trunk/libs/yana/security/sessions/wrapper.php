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

namespace Yana\Security\Sessions;

/**
 * Adds functionality to set and retrieve the user name.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Wrapper extends \Yana\Core\Sessions\Wrapper implements \Yana\Security\Sessions\IsWrapper
{

    /**
     * @var  string
     */
    private $_userNameKey = "user_name";

    /**
     * @var  string
     */
    private $_applicationUserId = "prog_id";

    /**
     * @var  string
     */
    private $_sessionUserId = "user_session";

    /**
     * @var  string
     */
    private $_languageKey = "language";

    /**
     * Retrieve the currently logged-in user's name.
     *
     * Note that this function does not check if the user is actually logged in!
     *
     * @return  string
     */
    public function getCurrentUserName(): string
    {
        return $this->offsetExists($this->_userNameKey) && $this->offsetGet($this->_userNameKey) ? (string) $this->offsetGet($this->_userNameKey) : "";
    }

    /**
     * Overwrite the currently selected user name.
     *
     * Note that this function does not check if the user is actually logged in!
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  $this
     */
    public function setCurrentUserName(\Yana\Security\Data\Users\IsEntity $user)
    {
        $this->offsetSet($this->_userNameKey, (string) $user->getId());
        return $this;
    }

    /**
     * Retrieve the current user's application id.
     *
     * @return  string
     */
    public function getApplicationUserId(): string
    {
        return $this->offsetExists($this->_applicationUserId) && $this->offsetGet($this->_applicationUserId) ? (string) $this->offsetGet($this->_applicationUserId) : "";
    }

    /**
     * Set current user's application id.
     *
     * @param   string  $applicationUserId  some string
     * @return  $this
     */
    public function setApplicationUserId(string $applicationUserId)
    {
        $this->offsetSet($this->_applicationUserId, $applicationUserId);
        return $this;
    }

    /**
     * Retrieve the current user's session id.
     *
     * @return  string
     */
    public function getSessionUserId(): string
    {
        return $this->offsetExists($this->_sessionUserId) && $this->offsetGet($this->_sessionUserId) ? (string) $this->offsetGet($this->_sessionUserId) : "";
    }

    /**
     * Set current user's session id.
     *
     * @param   string  $sessionUserId  some string
     * @return  $this
     */
    public function setSessionUserId(string $sessionUserId)
    {
        $this->offsetSet($this->_sessionUserId, $sessionUserId);
        return $this;
    }

    /**
     * Retrieve the current user's selected language id.
     *
     * @return  string
     */
    public function getCurrentLanguage(): string
    {
        return $this->offsetExists($this->_languageKey) && $this->offsetGet($this->_languageKey) ? $this->offsetGet($this->_languageKey) : "";
    }

    /**
     * Set current user's selected language id.
     *
     * @param   string  $language  some string
     * @return  $this
     */
    public function setCurrentLanguage(string $language)
    {
        $this->offsetSet($this->_languageKey, $language);
        return $this;
    }

}

?>