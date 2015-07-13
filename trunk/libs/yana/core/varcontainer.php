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

namespace Yana\Core;

/**
 * Container for managing variables.
 *
 * This class' primary use is for implementing unit-tests.
 *
 * @package     yana
 * @subpackage  core
 * @ingore
 */
class VarContainer extends \Yana\Core\Object implements \Yana\Core\IsVarContainer
{

    /**
     * @var array
     */
    private $_contents = array();

    /**
     * Convert a key name to an offset.
     *
     * @param   sclar  $key  some valid identifier, either a number or a non-empty text
     * @return  string
     */
    protected function _toArrayOffset($key)
    {
        assert('is_scalar($key)', ' Invalid argument $key: string expected');
        return (string) $key;
    }

    /**
     * Alias of getVar().
     *
     * @param   string  $id  some valid identifier
     * @return  mixed
     */
    public function __get($id)
    {
        assert('is_string($id)', ' Invalid argument $id: string expected');
        return $this->getVar($id);
    }

    /**
     * Alias of setVar().
     *
     * Return the value that is passed to the function (for assignment chaining).
     *
     * @param   string  $id     some valid identifier
     * @param   mixed   $value  any acceptable value
     * @return  mixed
     */
    public function __set($id, $value)
    {
        assert('is_string($id)', ' Invalid argument $id: string expected');
        $this->setVar($id, $value);
        return $value;
    }

    /**
     * Returns the var identified by $key or bool(false) on error.
     *
     * Note: this function may return false but also other values that evaluate to false.
     * To check for an error use: is_null($result).
     * To check for bool(false) use: $result === false.
     *
     * @param   string  $key  the var to retrieve
     * @return  mixed
     */
    public function getVar($key)
    {
        assert('!isset($offset)', ' Cannot redeclare var $offset');
        $offset = $this->_toArrayOffset($key);
        return (isset($this->_contents[$offset])) ? $this->_contents[$offset] : null;
    }

    /**
     * Returns all contained vars.
     *
     * @return  array
     */
    public function getVars()
    {
        return $this->_contents;
    }

    /**
     * Check if a var exists.
     *
     * Returns bool(true) if the key is known and bool(false) otherwise.
     *
     * @param   string  $key  some key (case insensitive)
     * @return  bool
     */
    public function isVar($key)
    {
        assert('!isset($offset)', ' Cannot redeclare var $offset');
        $offset = $this->_toArrayOffset($key);
        return (isset($this->_contents[$offset]));
    }

    /**
     * Sets the element identified by $key to $value by passing it's reference.
     *
     * If the value does not exist it gets inserted.
     * If a previous value existed the value gets updated.
     *
     * @param   string  $key        key of updated element
     * @param   mixed   &$value     new value
     * @return  \Yana\Core\VarContainer
     */
    public function setVarByReference($key, &$value)
    {
        assert('!isset($offset)', ' Cannot redeclare var $offset');
        $offset = $this->_toArrayOffset($key);
        $this->_contents[$offset] =& $value;
        return $this;
    }

    /**
     * Replaces all elements of the container by reference.
     *
     * @param   array  &$value  set of new values
     * @return  \Yana\Core\VarContainer
     */
    public function setVarsByReference(array &$value)
    {
        $this->_contents =& $value;
        return $this;
    }

    /**
     * Sets the element identified by $key  to $value.
     *
     * If the value does not exist it gets inserted.
     * If a previous value existed the value gets updated.
     *
     * @param   string  $key        key of updated element
     * @param   mixed   $value      new value
     * @return  \Yana\Core\VarContainer
     */
    public function setVar($key, $value)
    {
        return $this->setVarByReference($key, $value);
    }

    /**
     * Replaces all elements of the container.
     *
     * @param   array  $value  set of new values
     * @return  \Yana\Core\VarContainer
     */
    public function setVars(array $value)
    {
        return $this->setVarsByReference($value);
    }

}

?>