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
 * Session id generator.
 *
 * Used to create random session-ids.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class IdGenerator extends \Yana\Core\Object implements \Yana\Security\Sessions\IsIdGenerator
{

    /**
     * Get IP from SERVER array.
     *
     * Returns the 'REMOTE_ADDR' setting. If no such setting exists, returns 127.0.0.1 instead.
     *
     * @return  string
     */
    protected function _getRemoteAddress()
    {
        assert('!isset($remoteAddress); // Cannot redeclare var $remoteAddress');
        $remoteAddress = '127.0.0.1';
        if (isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR'])) {
            $remoteAddress = $_SERVER['REMOTE_ADDR'];
        }
        return $remoteAddress;
    }

    /**
     * Application instance id.
     *
     * The instance-id identifies the current instance of the installation,
     * where multiple instances of the framework are available on the same server.
     *
     * @return  string
     * @ignore
     */
    public function createApplicationUserId()
    {
        return $this->_getRemoteAddress() . '@' . dirname(__FILE__);
    }

    /**
     * Create a random session id for user BEFORE login.
     *
     * @return  string
     */
    public function createUnauthenticatedSessionId()
    {
        return (string) md5(uniqid());
    }

    /**
     * Create a random session id for authenticated users AFTER successful login.
     *
     * Uses SHA1 where available and MD5 as fallback.
     *
     * @return  string
     */
    public function createAuthenticatedSessionId()
    {
        assert('!isset($sessionId); // Cannot redeclare var $sessionId');
        $sessionId = uniqid($this->createApplicationUserId(), true);
        assert('!isset($encryptedId); // Cannot redeclare var $encryptedId');
        // @codeCoverageIgnoreStart
        if (function_exists('sha1')) {
            $encryptedId = sha1($sessionId);
        } else {
            /* if sha1 is not supported, fall back to default encryption method */
            $encryptedId = md5($sessionId);
        }
        // @codeCoverageIgnoreEnd
        return (string) $encryptedId;
    }

}

?>