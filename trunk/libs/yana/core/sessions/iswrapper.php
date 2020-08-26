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
    public function getId(): string;

    /**
     * Set new session-id.
     *
     * @param   string  $newId  new session-id
     * @return  $this
     */
    public function setId(string $newId);

    /**
     * Resets all session-data and clears the session array.
     *
     * @return  $this
     */
    public function unsetAll();

    /**
     * Replace the session-id without destroying session-data.
     *
     * @param   bool  $deleteOldSession  Whether to delete the old associated session file or not.
     * @return  $this
     */
    public function regenerateId(bool $deleteOldSession = false);

    /**
     * Returns the name of the session-id variable.
     *
     * @return  string
     */
    public function getName(): string;

    /**
     * Replaces the name of the session-id variable.
     *
     * @param  string  $name  new session name
     * @return $this
     */
    public function setName(string $name);

    /**
     * Start or resumes the current session.
     *
     * Note: if session_autostart is active, calling this function isn't necessary.
     * Returns bool(true) on success and bool(true) on error. 
     *
     * @return  bool
     */
    public function start(): bool;

    /**
     * Writes all changes to the session and ends it.
     */
    public function stop();

    /**
     * If there is an active session, destroys all session-data.
     *
     * Note! This does not remove the session cookie or terminate the session.
     *
     * @return  bool
     */
    public function destroy(): bool;

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
    public function fromString(string $serializedArray): bool;

}

?>