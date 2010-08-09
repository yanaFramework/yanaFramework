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
 * @access     public
 * @package    yana
 * @subpackage vdrive
 */
class Registry extends VDrive
{
    /**
     * An index of references to improve performance when accessing values
     *
     * @access  private
     * @var     array
     */
    private $cache = array();

    /**
     * This is a place-holder for the singleton's instance
     *
     * @access  private
     * @static
     * @var     Registry
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
     * @access  public
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
     * @access  public
     * @static
     * @return  Registry
     */
    public static function &getGlobalInstance()
    {
        return self::$instance;
    }

    /**
     * retrieves var from registry
     *
     * This returns the var identified by $key.
     * Returns bool(false) on error.
     *
     * @access  public
     * @param   string  $key  (optional)
     * @return  mixed
     */
    public function getVar($key = "*")
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        assert('is_array($this->vars); // Unexpected type for instance property "vars". Array expected');
        /*
         * 1) return whole registry
         */
        if (empty($key) || $key === "*") {
            return $this->vars;

        /*
         * 2) return value from index
         */
        } elseif (isset($this->vars[$key])) {
            return $this->vars[$key];

        } elseif (isset($this->cache[$key])) {
            if (is_null($this->cache[$key])) {
                return false;
            } else {
                return $this->cache[$key];
            }

        /*
         * 3) return value specified by key
         */
        } else {
            
            $this->cache[$key] =& Hashtable::get($this->vars, $key);
            if (is_null($this->cache[$key])) {
                return false;
            } else {
                return $this->cache[$key];
            }
        } /* end if */

    } /* end getVar */

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
     * @access  public
     * @param   string  $key  (optional)
     * @return  mixed
     * @since   2.9.5
     */
    public function &getVarByReference($key = "*")
    {
        /*
         * 1) return all
         */
        if (empty($key) || $key === "*") {
            return $this->vars;

        /*
         * 2) return value from index
         */
        } elseif (isset($this->cache[$key])) {
            return $this->cache[$key];

        /*
         * 3) return value by key
         */
        } else {
            /* returns NULL on error */
            $this->cache[$key] =& Hashtable::get($this->vars, $key);
            return $this->cache[$key];

        }
    }

    /**
     * sets var on registry by Reference
     *
     * Sets the element identified by $key
     * to $value by passing it's reference.
     * If the value does not exist it gets inserted.
     * If a previous value existed the value gets updated.
     *
     * This function returns bool(false) if $key = '*'
     * and $value is not an array - which is: trying
     * overwrite the complete registry with a non-array value.
     * It returns bool(true) otherwise.
     *
     * @access  public
     * @param   string  $key        key of updated element
     * @param   mixed   &$value     new value
     * @return  bool
     * @since   2.8.5
     */
    public function setVarByReference($key, &$value)
    {
        assert('is_string($key); // wrong argument type for argument 1, string expected');
        /* settype to STRING */
        $key = (string) $key;

        if (isset($this->vars[$key])) {
            $this->vars[$key] =& $value;
            return true;
        }

        if (Hashtable::setByReference($this->vars, $key, $value)) {
            $this->cache[$key] =& $value;
            return true;
        } else {
            return false;
        }

    }

    /**
     * sets var on registry
     *
     * Sets the element identified by $key  to $value.
     * If the value does not exist it gets inserted.
     * If a previous value existed the value gets updated.
     *
     * This function returns bool(false) if $key = '*'
     * and $value is not an array - which is: trying to
     * overwrite the complete registry with a non-array value.
     * It returns bool(true) otherwise.
     *
     * @access  public
     * @param   string  $key        key of updated element
     * @param   mixed   $value      new value
     * @return  bool
     */
    public function setVar($key, $value)
    {
        assert('is_string($key); // Wrong argument type for argument 1. String expected');
        return $this->setVarByReference($key, $value);
    }

    /**
     * merges the value at adresse $key with the provided array data
     *
     * Merges the element identified by $key with the array $value.
     *
     * Returns bool(false) on error.
     *
     * If $overwrite is set to false, then the values of keys that already exist are ignored.
     * Otherwise these values get updated to the new ones.
     *
     * @access  public
     * @param   string  $key        key of updated element
     * @param   array   $array      new value
     * @param   bool    $overwrite  true = update, false = ignore
     * @return  bool
     */
    public function mergeVars($key, array $array, $overwrite = true)
    {
        assert('is_string($key);  // Wrong argument type for argument 1, string expected');
        assert('is_bool($overwrite); // Wrong argument type for argument 3, boolean expected');

        if ($key == "" || $key == "*") {
            $this->vars = Hashtable::merge($this->vars, $array);
            $this->cache[$key] = array();
            return true;

        } elseif ($overwrite) {
            $vars =& Hashtable::get($this->vars, $key);
            if (is_null($vars)) {
                return Hashtable::set($this->vars, $key, $array);
            } else {
                $vars = Hashtable::merge($vars, $array);
                return true;
            }

        } else {
            $vars =& Hashtable::get($this->vars, $key);
            if (is_null($vars)) {
                return Hashtable::set($this->vars, $key, $array);
            } else {
                $array = array_diff_key($array, $vars);
                $vars = Hashtable::merge($vars, $array);
                return true;
            }

        } /* end if */
    }

    /**
     * removes var from registry
     *
     * Unsets the element identified by $key in the
     * registry. Returns bool(false) if the element
     * does not exist or the key is invalid.
     * Returns bool(true) otherwise.
     *
     * @access  public
     * @param   string  $key    key of element for delete
     * @return  bool
     */
    public function unsetVar($key)
    {
        assert('is_string($key);   // wrong argument type for argument 1, string expected');
        $key = (string) $key;

        if ($key == "" || $key == "*") {
            $this->vars = array();
            return true;
        } else {
            if (isset($this->cache[$key])) {
                unset($this->cache[$key]);
            }
            return Hashtable::remove($this->vars, $key);
        }

    }

    /**
     * sets the type of a var on registry
     *
     * Set the data type of the element identified by $key
     * to $type.
     *
     * Returns bool(false) if the element is NULL or does not exist,
     * or the $type parameter is invalid. Returns bool(true) otherwise.
     *
     * @access  public
     * @param   string  $key    key
     * @param   string  $type   type
     * @return  bool
     */
    public function setType($key, $type)
    {
        assert('is_string($key);   // wrong argument type for argument 1, string expected');
        assert('is_string($type);  // wrong argument type for argument 2, string expected');
        return Hashtable::setType($this->vars, "$key", "$type");
    }

}

?>