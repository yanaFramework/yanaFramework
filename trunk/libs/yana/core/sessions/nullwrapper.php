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
class NullWrapper extends \Yana\Core\StdObject implements \Yana\Core\Sessions\IsWrapper
{

    /**
     * @var  array
     */
    private $_data = array();

    /**
     * @var  string
     */
    private $_id = "";

    /**
     * @var  string
     */
    private $_name = "";

    /**
     * @var array
     */
    private $_cookieParameters = array("lifetime" => "0", "path" => "", "domain" => "", "secure" => "0", "httponly" => "1");

    /**
     * Create a new instance
     *
     * @param  array  $sessionData  your fake session data
     */
    public function __construct(array $sessionData = array())
    {
        $this->_data = $sessionData;
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
        return isset($this->_data[$offset]);
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
            $value = $this->_data[$offset];
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
            $this->_data[$offset] = $value;
        } else {
            $this->_data[] = $value;
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

        unset($this->_data[$offset]);
    }

    /**
     * Returns the number of items in the session-array.
     *
     * @return  int
     */
    public function count(): int
    {
        return count($this->_data);
    }

    /**
     * Returns the current session-id or an empty string if there is none.
     *
     * @return  string
     */
    public function getId(): string
    {
        return $this->_id;
    }

    /**
     * Set new session-id.
     *
     * @param   string  $newId  new session-id
     * @return  $this
     */
    public function setId(string $newId)
    {
        $this->_id = $newId;
        return $this;
    }

    /**
     * Resets all session-data and clears the session array.
     *
     * @return  $this
     */
    public function unsetAll()
    {
        $this->_data = array();
        return $this;
    }

    /**
     * Replace the session-id without destroying session-data.
     *
     * @param   bool  $deleteOldSession  Whether to delete the old associated session file or not.
     * @return  $this
     */
    public function regenerateId(bool $deleteOldSession = false)
    {
        $this->_id = "";
        $this->unsetAll();
        return $this;
    }

    /**
     * Returns the name of the session-id variable.
     *
     * @return  string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * Replaces the name of the session-id variable.
     *
     * @param  string  $name  new session name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->_name = $name;
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
    public function start(): bool
    {
        return true;
    }

    /**
     * Writes all changes to the session and ends it.
     */
    public function stop()
    {
        // nothing to do.
    }

    /**
     * Destroys all session-data.
     *
     * Note! This does not remove the session cookie or terminate the session.
     *
     * @return  bool
     */
    public function destroy(): bool
    {
        return true;
    }

    /**
     * Serialize the current session array to a string.
     *
     * @return  string
     */
    public function __toString()
    {
        return serialize($this->_data);
    }

    /**
     * Unserialize an array and use it as session values.
     *
     * Returns bool(true) on success and bool(true) on error. 
     *
     * @param   string  $serializedArray  serialized session data
     * @return  bool
     */
    public function fromString(string $serializedArray): bool
    {
        $this->_data = \unserialize($serializedArray);
        return true;
    }

}

?>