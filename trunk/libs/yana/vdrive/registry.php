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
 */

namespace Yana\VDrive;

/**
 * Registry
 *
 * This class implements a registry.
 * You can operate on it via keys, identifying
 * scalar values or arrays inside the registry.
 *
 * Example for usage of the $key parameter:
 * <pre>
 * Array {
 *     ID1 => Array {
 *         ID2 => 'value'
 *     }
 * }
 * </pre>
 * To get the string 'value' from the array above
 * use $key = 'ID1.ID2'
 *
 * The wildcard '*' may be used to refer to the array
 * as a whole.
 *
 * @package    yana
 * @subpackage vdrive
 */
class Registry extends \Yana\VDrive\VDrive implements \Yana\VDrive\IsRegistry
{

    /**
     * An index of references to improve performance when accessing values
     *
     * @var  array
     */
    private $cache = array();

    /**
     * This is a place-holder for the singleton's instance
     *
     * @var  \Yana\VDrive\IsRegistry
     */
    private static $instance = null;

    /**
     * make this the default drive
     *
     * Each drive has it's own private settings. However: you can make
     * one drive make a public "default drive". The settings of this drive
     * will become publicly visible to all other instances.
     *
     * These are standard directory settings, which are used to auto-replace references
     * found in VDrive-config-files.
     *
     * These settings are automatically initialized by the framework, so normally you
     * don't need to care for these settings.
     *
     * You can recall this function to change the global drive at any time.
     * This will not replace or remove the private settings of the prior "global" drive.
     *
     * But be adviced: you should always do this BEFORE creating the object,
     * or otherwise it will have no effect.
     *
     * @ignore
     */
    public function setAsGlobal()
    {
        parent::setAsGlobal();
        self::$instance =& $this;
    }

    /**
     * get instance of this class
     *
     * Returns the global instance if there is one.
     * If none has been defined, the function returns NULL.
     *
     * You need to have created an instance of this class
     * and set it to be the global instance.
     * If you have done this you may call this function
     * at any time to retrieve it.
     *
     * Example:
     * <code>
     * $registry = new Registry( ... );
     * $registry->setAsGlobal();
     * // ...
     * $myRegistry = Registry::getGlobalInstance();
     * </code>
     *
     * @return  self
     */
    public static function &getGlobalInstance()
    {
        return self::$instance;
    }

    /**
     * Retrieve a var from registry.
     *
     * This returns the var identified by $key.
     * Returns bool(false) on error.
     *
     * @param   string  $key  (optional)
     * @return  mixed
     */
    public function getVar($key)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        assert('is_array($this->vars); // Unexpected type for instance property "vars". Array expected');

        if (!$this->isVar($key)) {
            $value = false;

        } elseif (isset($this->vars[$key])) {
            $value = $this->vars[$key];

        } else {
            assert('isset($this->cache[$key]); // Expected key to exist, but it was not loaded');
            $value = $this->cache[$key];
        }
        return $value;
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
        assert('is_string($key); // Wrong type for argument 1. String expected');

        $isVar = isset($this->vars[$key]) || (isset($this->cache[$key]) && !is_null($this->cache[$key]));
        if (!$isVar) {
            $this->cache[$key] =& \Yana\Util\Hashtable::get($this->vars, $key);
            $isVar = !is_null($this->cache[$key]);
        }
        return $isVar;
    }

    /**
     * Retrieves all vars from registry.
     *
     * @return  array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * retrieves var from registry and returns it by reference
     *
     * This returns the var identified by $key.
     * Returns null (not bool(false)) on error.
     *
     * Note: this function may return false but also
     * other values that evaluates to false.
     * To check for an error use: is_null($result).
     * To check for bool(false) use: $result === false.
     *
     * @param   string  $key  (optional)
     * @return  mixed
     * @since   2.9.5
     */
    public function &getVarByReference($key)
    {
        // return value from index
        if (!isset($this->cache[$key])) {
            $this->cache[$key] =& \Yana\Util\Hashtable::get($this->vars, $key); // returns NULL on error
        }
        return $this->cache[$key];
    }

    /**
     * Retrieves all vars from registry and returns them by reference.
     *
     * @return  array
     */
    public function &getVarsByReference()
    {
        return $this->vars;
    }

    /**
     * sets var on registry by Reference
     *
     * Sets the element identified by $key
     * to $value by passing it's reference.
     * If the value does not exist it gets inserted.
     * If a previous value existed the value gets updated.
     *
     * @param   string  $key        key of updated element
     * @param   mixed   &$value     new value
     * @return  self
     */
    public function setVarByReference($key, &$value)
    {
        assert('is_string($key); // wrong argument type for argument 1, string expected');
        /* settype to STRING */
        $keyString = (string) $key;

        if (isset($this->vars[$keyString])) {
            $this->vars[$keyString] =& $value;
            return $this;
        }

        \Yana\Util\Hashtable::setByReference($this->vars, $keyString, $value);
        $this->cache[$keyString] =& $value;
        return $this;
    }

    /**
     * Replace all vars on registry.
     *
     * @param   array  &$value  new set of values
     * @return  self
     */
    public function setVarsByReference(array &$value)
    {
        $this->vars =& $value;
        return $this;
    }

    /**
     * Sets var on registry.
     *
     * Sets the element identified by $key  to $value.
     * If the value does not exist it gets inserted.
     * If a previous value existed the value gets updated.
     *
     * @param   string  $key        key of updated element
     * @param   mixed   $value      new value
     * @return  self
     */
    public function setVar($key, $value)
    {
        assert('is_string($key); // Wrong argument type for argument 1. String expected');
        $this->setVarByReference($key, $value);
        return $this;
    }

    /**
     * Replace all vars in the registry.
     *
     * @param   array  $value  new set of values
     * @return  self
     */
    public function setVars(array $value)
    {
        $this->setVarsByReference($value);
        return $this;
    }

    /**
     * Merges the value at adress $key with the provided array data.
     *
     * If $overwrite is set to false, then the values of keys that already exist are ignored.
     * Otherwise these values get updated to the new ones.
     *
     * @param   string  $key        key of updated element
     * @param   array   $array      new value
     * @param   bool    $overwrite  true = update, false = ignore
     * @return  self
     */
    public function mergeVars($key, array $array, $overwrite = true)
    {
        assert('is_string($key); // Wrong argument type for argument 1, string expected');
        assert('is_bool($overwrite); // Wrong argument type for argument 3, boolean expected');

        if ($key == "" || $key == "*") {
            $this->vars = \Yana\Util\Hashtable::merge($this->vars, $array);
            $this->cache[$key] = array();

        } elseif ($overwrite) {
            $vars =& \Yana\Util\Hashtable::get($this->vars, $key);
            if (is_null($vars)) {
                \Yana\Util\Hashtable::set($this->vars, $key, $array);
            } else {
                $vars = \Yana\Util\Hashtable::merge($vars, $array);
            }

        } else {
            $vars =& \Yana\Util\Hashtable::get($this->vars, $key);
            if (is_null($vars)) {
                \Yana\Util\Hashtable::set($this->vars, $key, $array);
            } else {
                $array = array_diff_key($array, $vars);
                $vars = \Yana\Util\Hashtable::merge($vars, $array);
            }

        } // end if
        return  $this;
    }

    /**
     * Removes all vars from registry.
     *
     * @return  self
     */
    public function unsetVars()
    {
        $this->vars = array();
        return $this;
    }

    /**
     * Removes var from registry.
     *
     * Unsets the element identified by $key in the
     * registry. Returns bool(false) if the element
     * does not exist or the key is invalid.
     * Returns bool(true) otherwise.
     *
     * @param   string  $key  key of element for delete
     * @return  self
     */
    public function unsetVar($key)
    {
        assert('is_string($key); // wrong argument type for argument 1, string expected');
        $key = (string) $key;

        if (isset($this->cache[$key])) {
            unset($this->cache[$key]);
        }
        \Yana\Util\Hashtable::remove($this->vars, $key);
        return $this;
    }

    /**
     * Set the data type of the element identified by $key to $type.
     *
     * Returns bool(false) if the element is NULL or does not exist,
     * or the $type parameter is invalid. Returns bool(true) otherwise.
     *
     * @param   string  $key   target index
     * @param   string  $type  name of scalar data type
     * @return  self
     */
    public function setType($key, $type)
    {
        assert('is_string($key); // wrong argument type for argument 1, string expected');
        assert('is_string($type); // wrong argument type for argument 2, string expected');
        \Yana\Util\Hashtable::setType($this->vars, "$key", "$type");
        return $this;
    }

}

?>