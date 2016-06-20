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
 * <<interface>> Adds functionality to set and retrieve the user name.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsWrapper extends \Yana\Core\Sessions\IsWrapper
{

    /**
     * Retrieve the currently logged-in user's name.
     *
     * Note that this function does not check if the user is actually logged in!
     *
     * @return  string
     */
    public function getCurrentUserName();

    /**
     * Overwrite the currently selected user name.
     *
     * Note that this function does not check if the user is actually logged in!
     *
     * @param   \Yana\Security\Users\IsUser $user
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function setCurrentUserName(\Yana\Security\Users\IsUser $user);

    /**
     * Retrieve the current user's application id.
     *
     * @return  string
     */
    public function getApplicationUserId();

    /**
     * Set current user's application id.
     *
     * @param   string  $applicationUserId  some string
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function setApplicationUserId($applicationUserId);

    /**
     * Retrieve the current user's session id.
     *
     * @return  string
     */
    public function getSessionUserId();

    /**
     * Set current user's session id.
     *
     * @param   string  $sessionUserId  some string
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function setSessionUserId($sessionUserId);

    /**
     * Retrieve the current user's selected language id.
     *
     * @return  string
     */
    public function getCurrentLanguage();

    /**
     * Set current user's selected language id.
     *
     * @param   string  $language  some string
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function setCurrentLanguage($language);

}

?>