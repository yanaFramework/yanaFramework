<?php
/**
 * YANA library
 *
 * Primary controller class
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
declare(strict_types=1);

namespace Yana\Core\Sessions;

/**
 * <<interface>> Session wrapper.
 *
 * Meant to decouple applications from session data and allow injection of null-objects for unit testing.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsWrapper extends \Yana\Core\IsCountableArray
{

    /**
     * Returns the current session-id or an empty string if there is none.
     *
     * @return  string
     */
    public function getId();

    /**
     * Set new session-id.
     *
     * @param   string  $newId  new session-id
     * @return  \Yana\Core\Sessions\IsWrapper
     */
    public function setId($newId);

    /**
     * Resets all session-data and clears the session array.
     *
     * @return \Yana\Core\Sessions\IsWrapper
     */
    public function unsetAll();

    /**
     * Replace the session-id without destroying session-data.
     *
     * @param   bool  $deleteOldSession  Whether to delete the old associated session file or not.
     * @return  \Yana\Core\Sessions\IsWrapper
     */
    public function regenerateId($deleteOldSession = false);

    /**
     * Returns the name of the session-id variable.
     *
     * @return  string
     */
    public function getName();

    /**
     * Replaces the name of the session-id variable.
     *
     * @param  string  $name  new session name
     * @return \Yana\Core\Sessions\IsWrapper
     */
    public function setName($name);

    /**
     * Start or resumes the current session.
     *
     * Note: if session_autostart is active, calling this function isn't necessary.
     * Returns bool(true) on success and bool(true) on error. 
     *
     * @return  bool
     */
    public function start();

    /**
     * Writes all changes to the session and ends it.
     */
    public function stop();

    /**
     * Destroys all session-data.
     *
     * Note! This does not remove the session cookie or terminate the session.
     *
     * @return  bool
     */
    public function destroy();

    /**
     * Gets the session cookie parameters.
     *
     * Returns an associative array containing the following information:
     * <ul>
     * <li>int "lifetime" of the cookie in seconds</li>
     * <li>string "path" of the application it corresponds to</li>
     * <li>string "domain" that the cookie is valid for (your domain or your wouldn't be seeing this)</li>
     * <li>bool "secure" if TRUE, then the cookie contents are only sent via SSL
     *    (if this is TRUE then the request was made using SSL or you wouldn't be seeing the cookie)</li>
     * <li>"httponly" if TRUE, the cookie will not be sent using JavaScript
     *    (if this is TRUE, you have not been contacted using a script, or you wouldn't have gotten the cookie)</li>
     * </ul>
     *
     * @return  array
     */
    public function getCookieParameters();

    /**
     * Serialize the current session array to a string.
     *
     * @return  string
     */
    public function __toString();

    /**
     * Unserialize an array and use it as session values.
     *
     * Returns bool(true) on success and bool(true) on error. 
     *
     * @param   string  $serializedArray  serialized session data
     * @return  bool
     */
    public function fromString($serializedArray);

}

?>