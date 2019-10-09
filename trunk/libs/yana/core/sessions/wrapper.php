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
 * <<wrapper>> Session wrapper.
 *
 * This class is an OO-wrapper around php's functions.
 * It is meant to decouple applications from session data and allow injection of null-objects for unit testing.
 *
 * @package     yana
 * @subpackage  core
 */
class Wrapper extends \Yana\Core\StdObject implements \Yana\Core\Sessions\IsWrapper
{

    /**
     * <<constructor>> Initialize session vars.
     */
    public function __construct()
    {
        if (!isset($_SESSION)) {
            $_SESSION = array();
        }
    }

    /**
     * Returns bool(true) if the session has a value at the given offset.
     *
     * Returns bool(false) otherwise.
     *
     * @param   scalar  $offset  some array index
     * @return  bool
     */
    public function offsetExists($offset)
    {
        assert(is_scalar($offset), 'Invalid argument $offset: scalar expected');
        return isset($_SESSION[$offset]);
    }

    /**
     * Returns the session-value at the given offset.
     *
     * Returns NULL if no value exists to that offset.
     *
     * @param   scalar  $offset  some array index
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        assert(is_scalar($offset), 'Invalid argument $offset: scalar expected');
        $value = null;
        if ($this->offsetExists($offset)) {
            $value = $_SESSION[$offset];
        }
        return $value;
    }

    /**
     * Replaces the session-var at the given offset and returns it.
     *
     * @param   scalar  $offset  some array index
     * @param   mixed   $value   new session-var value
     * @return  mixed
     */
    public function offsetSet($offset, $value)
    {
        if (!\is_null($offset)) {
            assert(is_scalar($offset), 'Invalid argument $offset: scalar expected');
            $_SESSION[$offset] = $value;
        } else {
            $_SESSION[] = $value;
        }
        return $value;
    }

    /**
     * Unsets an item in the session-array.
     *
     * @param  scalar  $offset  some array index
     */
    public function offsetUnset($offset)
    {
        assert(is_scalar($offset), 'Invalid argument $offset: scalar expected');

        unset($_SESSION[$offset]);
    }

    /**
     * Returns the number of items in the session-array.
     *
     * @return  int
     */
    public function count()
    {
        return count($_SESSION);
    }

    /**
     * Returns the current session-id or an empty string if there is none.
     *
     * @return  string
     */
    public function getId()
    {
        return \session_id();
    }

    /**
     * Set new session-id.
     *
     * @param   string  $newId  new session-id
     * @return  \Yana\Core\Sessions\IsWrapper
     */
    public function setId($newId)
    {
        assert(is_string($newId), 'Invalid argument $newId: string expected');

        \session_id($newId);
        return $this;
    }

    /**
     * Resets all session-data and clears the session array.
     *
     * @return  \Yana\Core\Sessions\IsWrapper
     */
    public function unsetAll()
    {
        \session_unset();
        $_SESSION = array();
        return $this;
    }

    /**
     * Replace the session-id without destroying session-data.
     *
     * @param   bool  $deleteOldSession  Whether to delete the old associated session file or not.
     * @return  \Yana\Core\Sessions\IsWrapper
     */
    public function regenerateId($deleteOldSession = false)
    {
        assert(is_bool($deleteOldSession), 'Invalid argument $deleteOldSession: bool expected');

        /* regenerate_id() will issue a warning if it is called while the session is not in an active state.
         * To avoid that, we will auto-activate the session if none is there.
         *
         * While in theory "session-autostart" should do that for us, it is best not to rely on that setting.
         */
        $this->start();
        \session_regenerate_id($deleteOldSession);
        return $this;
    }

    /**
     * Returns the name of the session-id variable.
     *
     * @return  string
     */
    public function getName()
    {
        return \session_name();
    }

    /**
     * Replaces the name of the session-id variable.
     *
     * @param   string  $name  new session name
     * @return  \Yana\Core\Sessions\IsWrapper
     */
    public function setName($name)
    {
        assert(is_string($name), 'Invalid argument $name: string expected');

        \session_name((string) $name);
        return $this;
    }

    /**
     * Start or resumes the current session.
     *
     * Note: if session_autostart is active, calling this function isn't necessary.
     * Returns bool(true) on success and bool(true) on error. 
     *
     * @return  bool
     */
    public function start()
    {
        $result = false;
        /* Check the state of the session and only call start() if the session is not active (yet).
         * Just to avoid the warning "session already started" if start is called on an active session.
         */
        switch (\session_status())
        {
            case \PHP_SESSION_ACTIVE:
                $result = true;
            break;
            case \PHP_SESSION_NONE:
                $result = \session_start();
                assert(\session_status() === \PHP_SESSION_ACTIVE);
            break;
            // Session-handling may also be disabled (PHP_SESSION_DISABLED).
            // In which case we always return bool(false).
        }
        return $result;
    }

    /**
     * Writes all changes to the session and ends it.
     */
    public function stop()
    {
        \session_write_close();
    }

    /**
     * Destroys all session-data.
     *
     * Note! This does not remove the session cookie or terminate the session.
     *
     * @return  bool
     */
    public function destroy()
    {
        return \session_destroy();
    }

    /**
     * Gets the session cookie parameters.
     *
     * Returns an associative array containing the following information:
     * <ul>
     * <li>int "lifetime" of the cookie in seconds, default is 0</li>
     * <li>string "path" of the application it corresponds to, default is "/".</li>
     * <li>string "domain" that the cookie is valid for, default is "" (if this isn't your domain, something is seriously wrong)</li>
     * <li>bool "secure" if TRUE, then the cookie contents are only sent via SSL, default is FALSE.
     *    (if this is TRUE then the request was made using SSL or you wouldn't be seeing the cookie)</li>
     * <li>"httponly" if TRUE, the cookie will not be sent using JavaScript, default is FALSE.
     *    (if this is TRUE, you have not been contacted using a script, or you wouldn't have gotten the cookie)</li>
     * </ul>
     *
     * @return  array
     */
    public function getCookieParameters()
    {
        return \session_get_cookie_params();
    }

    /**
     * Serialize the current session array to a string.
     *
     * @return  string
     */
    public function __toString()
    {
        return \session_encode();
    }

    /**
     * Unserialize an array and use it as session values.
     *
     * Returns bool(true) on success and bool(true) on error. 
     *
     * @param   string  $serializedArray  serialized session data
     * @return  bool
     */
    public function fromString($serializedArray)
    {
        assert(is_string($serializedArray), 'Invalid argument $serializedArray: string expected');
        return \session_decode($serializedArray);
    }

}

?>