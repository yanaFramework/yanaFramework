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

namespace Yana\Security\Users;

/**
 * <<interface>> Session id generator.
 *
 * Used to create random session-ids.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsSessionIdGenerator
{

    /**
     * Application instance id.
     *
     * The instance-id identifies the current instance of the installation,
     * where multiple instances of the framework are available on the same server.
     *
     * @return  string
     * @ignore
     */
    public function createApplicationUserId();

    /**
     * Create a random session id for user BEFORE login.
     *
     * @return  string
     */
    public function createUnauthenticatedSessionId();

    /**
     * Create a random session id for authenticated users AFTER successful login.
     *
     * Uses SHA1 where available and MD5 as fallback.
     *
     * @return  string
     */
    public function createAuthenticatedSessionId();

}

?>