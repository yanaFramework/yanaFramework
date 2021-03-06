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

namespace Yana\Util;

// @codeCoverageIgnoreStart
if (!defined('CASE_MIXED')) {
    /**
     * @ignore
     */
    define('CASE_MIXED', -1);
}
// @codeCoverageIgnoreEnd

/**
 * <<utility>> Hashtable
 *
 * This is a static utility class to implement certain
 * operations on hashtables (associative arrays).
 *
 * Example for usage of the $key parameter:
 * <pre>
 * Array {
 *     ID1 => Array {
 *         ID2 => 'value'
 *     }
 * }
 * </pre>
 * To get the string 'value' from the hashtable above
 * use $key = 'ID1.ID2'
 *
 * The wildcard '*' may be used to refer to the hashtable
 * as a whole.
 *
 * @package     yana
 * @subpackage  util
 */
class Hashtable extends \Yana\Core\AbstractUtility
{

    /**
     * @var  string
     */
    private static $_inputEncoding = null;

    /**
     * @var  string
     */
    private static $_outputEncoding = null;

    /**
     * Retrieve a value.
     *
     * Finds the value identified by $key and returns it.
     * If the value is not found NULL is returned.
     *
     * @param   array   &$hash   associative array
     * @param   string  $key     address
     * @return  mixed
     */
    public static function &get(array &$hash, $key)
    {
        if ($key == '*') {
            return $hash;
        } else {
            $listOfKeys = explode(".", $key);
            assert(is_array($listOfKeys) && count($listOfKeys) > 0, 'is_array($listOfKeys) && count($listOfKeys) > 0');
            $a =& self::_get($hash, $listOfKeys);
            return $a;
        }
    }

    /**
     * Magic getter.
     *
     * Recursively resolves key address.
     *
     * @param   array  &$hash        associative array
     * @param   array  &$listOfKeys  list of array keys
     * @return  mixed
     * @ignore
     */
    private static function &_get(&$hash, array &$listOfKeys)
    {
        $key_name = array_shift($listOfKeys);
        if (is_array($hash) && isset($hash[$key_name]) && !is_null($hash[$key_name])) {
            $result = &$hash[$key_name];
            if (count($listOfKeys) == 0) {
                return $result;
            } else {
                $a =& self::_get($result, $listOfKeys);
                return $a;
            }
        } else {
            $result = null;
            return $result;
        }
    }

    /**
     * Set an element by reference.
     *
     * Sets the element identified by $key to $value by passing it's reference.
     * If the value does not exist it gets inserted.
     * If a previous value existed the value gets updated.
     *
     * @name    Hashtable::setByReference()
     * @param   array   &$hash   associative array
     * @param   string  $key     address
     * @param   mixed   &$value  some new value
     * @see     Hashtable::set()
     * @since   2.8.5
     */
    public static function setByReference(array &$hash, $key, &$value)
    {
        assert(is_string($key), 'wrong argument type for argument 2, string expected');
        if ($key === '' || $key === '*') {
            assert(is_array($value), 'Only values of type array may be assigned to a Hashtable.');
            $value = (array) $value;
            foreach ($value as $id => &$var)
            {
                $hash[$id] =& $var;
            }
        } else {
            $list_of_keys = explode(".", $key);
            assert(is_array($list_of_keys) && count($list_of_keys) > 0, 'is_array($list_of_keys) && count($list_of_keys) > 0');
            $result = &$hash;
            for ($i = 0; $i < (count($list_of_keys) -1); $i++)
            {
                if (!isset($result[$list_of_keys[$i]]) || is_scalar($result[$list_of_keys[$i]])) {
                    $result[$list_of_keys[$i]] = array();
                }
                $result = &$result[$list_of_keys[$i]];
            }
            $result[$list_of_keys[count($list_of_keys)-1]] = &$value;
        }
    }

    /**
     * Set an element to a value.
     *
     * Sets the element identified by $key to $value.
     * If the value does not exist it gets inserted.
     * If a previous value existed the value gets updated.
     *
     * @name    Hashtable::set()
     * @param   array   &$hash  associative array
     * @param   string  $key    address
     * @param   mixed   $value  some new value
     * @see     Hashtable::setByReference()
     */
    public static function set(array &$hash, $key, $value)
    {
        assert(is_string($key), 'wrong argument type for argument 2, string expected');
        self::setByReference($hash, $key, $value);
    }

    /**
     * Set the data type of an element.
     *
     * Set the data type of the element identified by $key
     * to $type.
     *
     * Returns bool(false) if the element is NULL or does not exist,
     * or the $type parameter is invalid. Returns bool(true) otherwise.
     *
     * @param   array   &$hash  associative array
     * @param   string  $key    address
     * @param   string  $type   data type
     * @return  bool
     */
    public static function setType(array &$hash, $key, $type)
    {
        assert(is_string($type)&& !empty($type), 'Wrong type for argument 1. String expected');
        $field =& self::get($hash, $key);
        return !is_null($field) && settype($field, $type);
    }

    /**
     * Check if an element exists.
     *
     * Returns bool(false) if the element identified by $key
     * can not be found in the given hashtable or it is NULL.
     * Returns bool(true) otherwise.
     *
     * @param   array   &$hash  associative array
     * @param   string  $key    address
     * @return  bool
     */
    public static function exists(array &$hash, $key)
    {
        return !is_null(self::get($hash, $key));
    }

    /**
     * Remove an element.
     *
     * Unsets the element identified by $key in the
     * hashtable. Returns bool(false) if the element
     * does not exist or the key is invalid.
     * Returns bool(true) otherwise.
     *
     * @param   array   &$hash  associative array
     * @param   string  $key    address
     * @return  bool
     */
    public static function remove(array &$hash, $key)
    {
        if ($key == '*') {
            unset($hash);
            return true;
        }

        $listOfKeys = explode(".", $key);
        assert(is_array($listOfKeys) && count($listOfKeys) > 0, 'is_array($listOfKeys) && count($listOfKeys) > 0');
        $stack = array();
        $stack[0] = &$hash;
        $result = &$hash;
        for ($i = 0; $i < (count($listOfKeys) -1); $i++)
        {
            if (!isset($result[$listOfKeys[$i]])) {
                return false;
            } else {
                $result = &$result[$listOfKeys[$i]];
                $stack[count($stack)] = &$result;
            }
        }
        $j = array_pop($listOfKeys);
        if (!isset($stack[count($stack)-1][$j])) {
            return false;
        }

        unset($stack[count($stack)-1][$j]);
        array_pop($stack);
        for ($i = (count($stack) -1); $i > -1; $i--)
        {
            $j = array_pop($listOfKeys);
            if (is_array($stack[$i][$j]) && count($stack[$i][$j])==0) {
                unset($stack[$i][$j]);
            } else {
                break;
            }
        }
        return true;
    }

    /**
     * Lowercase or uppercase all keys of an associative array.
     *
     * This is a recursive implementation of the PHP function array_change_key_case().
     * It takes the same arguments: an array $input to work on and an optional
     * argument $case. The argument $case can be one of two constants: CASE_LOWER and
     * CASE_UPPER, where CASE_LOWER is the default.
     *
     * @param   array  $input  input array
     * @param   int    $case   CASE_UPPER or CASE_LOWER
     * @return  array
     */
    public static function changeCase(array $input, $case = CASE_LOWER)
    {
        assert($case === CASE_UPPER || $case === CASE_LOWER, 'Wrong argument type for $case. Expected CASE_UPPER or CASE_LOWER.');

        /* Map boolean input to constant */
        if ($case !== CASE_UPPER) {
            $case = CASE_LOWER;
        }

        /* input handling */
        $input = array_change_key_case($input, $case);
        foreach ($input as $k => $e)
        {
            if (is_array($e)) {
                $input[$k] = self::changeCase($e, $case);
            }
        } // end foreach
        assert(is_array($input), 'Unexpected result: $input. Array expected.');
        return $input;
    }

    /**
     * Recursively merge two arrays to one.
     *
     * This function is pretty much the same as the php function "array_merge_recursive"
     * except for the way how duplicate keys are treated. Dupplicate keys get replaced
     * in this implementation rather than being appended.
     *
     * You may provide two arrays to merge.
     *
     * Example:
     * <code>
     * $foo = Hashtable::merge(array(0 => 'a', 'a' => 'a'), array(0 => 'b', 1 => 'c'));
     * </code>
     * Result:
     * <pre>
     * array (
     * 0 => 'b',
     * 'a' => 'a',
     * 1 => 'c'
     * )
     * </pre>
     *
     * Recursive example:
     * <pre>
     * $a1 = array (
     * 0 => 1,
     * 1 => array(
     *   0 => 1,
     *   'a' => 'b'
     *   )
     * );
     * $b = array (
     * 1 => array(
     *   0 => 'c',
     *   1 => 2,
     *   )
     * );
     * 2 => 3,
     * </pre>
     * Compute this via:
     * <code>
     * $foobar = Hashtable::merge($a1, $a2);
     * </code>
     * Result:
     * <pre>
     * array (
     * 0 => 1,
     * 1 => array(
     *   0 => 'c',
     *   1 => 2,
     *   'a' => 'b'
     *   ),
     * 2 => 3
     * )
     * </pre>
     *
     * @param   array  $a  base array
     * @param   array  $b  merge with this array
     * @return  array
     */
    public static function merge(array $a, array $b)
    {
        $a += $b; // Add non-existing keys
        // Overwrite existing keys
        foreach (array_intersect_key($b, $a) as $k => $e)
        {
            if (is_array($e) && is_array($a[$k])) {
                $a[$k] = self::merge($a[$k], $e); // merge recursive
            } else {
                $a[$k] = $e; // overwrite
            }
        } // end foreach
        assert(is_array($a), 'is_array($a)');
        return $a;
    }

    /**
     * Create a XML string from a scalar variable, an object, or an array of data.
     *
     * The argument $name can be used to specify the id of the root node.
     * If $name is omitted, the id will be "root".
     *
     * Note that any tags from string inputs will be stripped.
     * You should convert tags to entities, before submiting the input.
     *
     * The argument $caseSensitive can be used to decide how keys should be treated.
     *
     * Valid values for $caseSensitive are:
     * <ul>
     *     <li>  CASE_UPPER  upper-case all keys       </li>
     *     <li>  CASE_LOWER  lower-case all keys       </li>
     *     <li>  CASE_MIXED  leave keys in mixed case  </li>
     * </ul>
     *
     * An XML-header is created automatically.
     * Encoding of input and output will run on auto-detect.
     *
     * @param   mixed   $data           input data
     * @param   string  $name           name of root element
     * @param   int     $caseSensitive  CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @param   int     $indent         number of tabs to indent
     */
    public static function toXML($data, $name = "root", $caseSensitive = CASE_MIXED, $indent = 0)
    {
        assert(is_null($data) || is_scalar($data) || is_array($data) || is_object($data),
            'Wrong argument type for argument 1. Array or scalar value expected.');
        assert(is_scalar($name), 'Wrong argument type for argument 2. String expected.');
        assert($caseSensitive === CASE_MIXED || $caseSensitive === CASE_LOWER || $caseSensitive === CASE_UPPER,
            'Invalid argument 3. Expected one of the following constants: CASE_MIXED, CASE_LOWER, CASE_UPPER.');
        assert(is_int($indent), 'Wrong argument type for argument 4. Integer expected.');

        /*
         * settype to STRING
         *            INTEGER
         *            INTEGER
         */
        $name = (string) $name;
        $indent = (int) $indent;
        $caseSensitive = (int) $caseSensitive;

        /* string */ $txt = "";

        if (is_null($data)) {
            return "";
        }

        $tab = "";
        $head = "";

        /*
         * detect encoding
         */
        if (empty(self::$_inputEncoding)) {
            self::$_inputEncoding = function_exists('iconv_get_encoding') ? iconv_get_encoding("internal_encoding") : "UTF-8";
        }
        if (empty(self::$_outputEncoding)) {
            self::$_outputEncoding = function_exists('iconv_get_encoding') ? iconv_get_encoding("output_encoding") : "UTF-8";
        }

        /*
         * create xml header
         */
        if ($indent === 0) {
            $head = '<?xml version="1.0" encoding="' . self::$_outputEncoding . '"?>' . "\n";

        /*
         * indent tag
         */
        } else {
            for ($i = 0; $i < $indent; $i++)
            {
                $tab .= "\t";
            }
            unset($i);
        }

        $tagName = gettype($data);
        $attId = 'id';
        $attClass = 'class';
        $xml = ''; /* containts output */
        /*
         * convert case of keys
         */
        switch ($caseSensitive)
        {
            case CASE_UPPER:
                $name = \Yana\Util\Strings::toUpperCase($name);
            break;
            case CASE_LOWER:
                $name = \Yana\Util\Strings::toLowerCase($name);
            break;
        }

        /*
         * switch typeOf($data)
         */
        switch (true)
        {
            case is_bool($data):
                $xml = $tab . ('<' . $tagName . ' ' . $attId . '="' . $name . '">' .
                    ( ($data) ? "true" : "false" ) . '</' . $tagName . ">\n");
            break;
            case is_array($data):
                $xml = $tab . '<' . $tagName . ' ' . $attId . '="' . $name . '">' . "\n";
                $indent++;
                foreach ($data as $key => $element)
                {
                    if ($element !== $data) {
                        $xml .= self::toXML($element, $key, $caseSensitive, $indent);
                    } else {
                        /* ignore recursion */
                    }
                } // end foreach
                $xml .= $tab . '</' . $tagName . ">\n";
            break;
            case is_object($data):
                $xml = $tab.'<' . $tagName . ' ' . $attId . '="' . $name . '" ' . $attClass . '="' .
                    get_class($data) . '">'."\n";
                foreach (get_object_vars($data) as $key => $element)
                {
                    $xml .= self::toXML($element, $key, $caseSensitive, $indent + 1);
                }
                $xml .= $tab . '</' . $tagName . ">\n";
            break;
            default:
                $xml = $tab . ('<' . $tagName . ' ' . $attId . '="' . $name . '">' .
                \Yana\Util\Strings::htmlSpecialChars((string) $data, ENT_NOQUOTES, self::$_inputEncoding) . '</' . $tagName . ">\n");
            break;
        } /* end switch */

        if (self::$_inputEncoding !== self::$_outputEncoding && function_exists('iconv')) {
            return iconv(self::$_inputEncoding, self::$_outputEncoding, $head . $xml);
        } else {
            return $head . $xml;
        }
    }

    /**
     * Recursive deep-copy on arrays.
     *
     * This function creates a deep-copy of
     * the input $array and returns it.
     *
     * "Deep-copy" means, it tries to clone
     * objects registered in the array by calling
     * the function "copy()", if the object has any,
     * or by using the keyword "clone" as of PHP 5.
     *
     * Note that this will not work correctly, if the object has
     * neither the one nor the other.
     *
     * @since   2.8.5
     * @param   array  $array  input array that should be cloned
     * @return  array
     */
    public static function cloneArray(array $array)
    {
        $result = array();
        foreach ($array as $a => $b)
        {
            if (is_object($b)) {
                $result[$a] = clone $b;
            } elseif (is_array($b)) {
                $result[$a] = self::cloneArray($b);
            } else {
                $result[$a] = $b;
            }
        }
        return $result;
    }

    /**
     * Search for a value in a sorted list.
     *
     * If the array contains $needle, the key of $needle is
     * returned. Otherwise this functions returns bool(false).
     *
     * This function does something similar to PHP's in_array(),
     * except, that it expects the input to be a numeric, unique,
     * sorted array.
     *
     * If the input is sorted, searching for a value will be faster
     * using this function than the original, especially for large arrays.
     *
     * To be more technical: qSearchArray() performs a search
     * on a sorted array in O(log(n)) running time, which is the
     * same as searching for a key in a red-black tree.
     * While a "normal", linear scan of the array takes O(n) running
     * time.
     *
     * Example:
     * <code>
     * // Search through a large, sorted, numeric array of strings.
     * // E.g. a file where each line is representing a value.
     * $list = file('large_file.txt');
     * if (is_array($list)) {
     *     $i = Hashtable::quickSearch($list, 'foo');
     *     if ($i === false) {
     *         print "Value 'foo' not found!\n";
     *     } else {
     *         print "Found 'foo' in line $i.\n";
     *     }
     * }
     * </code>
     *
     * @param   array   &$array     array
     * @param   scalar  $needle     needle
     * @return  int|bool(false)
     * @name    function_qSearchArray()
     */
    public static function quickSearch(array &$array, $needle)
    {
        assert(is_scalar($needle), 'Wrong type for argument 2. Scalar expected');

        /* Input handling */
        /* settype to STRING */
        $needle = (string) $needle;
        $max = count($array) - 1;
        $min = 0;
        $n = floor($max / 2);
        $previousN = array(-1, -1);

        if ($max === $min) {
            if ($array[$max] === $needle) {
                return $max;
            } else {
                return false;
            }
        }

        while ($max > $min)
        {
            $temp = strcmp(trim($array[$n]), $needle);
            if ($temp > 0) {
                $max = $n;
            } elseif ($temp < 0) {
                $min = $n;
            } else {
                return (int) $n;
            }

            array_shift($previousN);
            $previousN[] = $n;

            if ($min != $n) {
                $n = $min + floor(($max - $min) / 2);
            } else {
                $n = $min + ceil(($max - $min) / 2);
            }

            if ($previousN[0] == $n || $previousN[1] == $n) {
                return false;
            }
        } /* end while */
        return false;
    }

}

?>