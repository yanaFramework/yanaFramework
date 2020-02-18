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
declare(strict_types=1);

namespace Yana\Files;

/**
 * Simple Markup Language (SML) Files
 *
 * This is a wrapper-class that may be used to work with *.config and *.sml files.
 *
 * SML files provide the same semantics and functionality as JSON encoded files,
 * are as easy to read and understand, but they stick with XML-style markup,
 * which is widely used and understood by most people.
 *
 * @package     yana
 * @subpackage  files
 * @since       2.8.5
 * @name        SML
 */
abstract class AbstractVarContainer extends \Yana\Files\File implements \Yana\Core\IsVarContainer
{

    /**
     * is file already loaded
     *
     * @var  bool
     */
    private $_isReady = false;

    /**
     * treat keys as
     *
     * CASE_MIXED = leave keys alone
     * CASE_UPPER = convert to upper case
     * CASE_LOWER = convert to lower case
     *
     * @var  int
     * @ignore
     */
    protected $caseSensitive = CASE_MIXED;

    /**
     * @var  \Yana\Files\Decoders\IsDecoder
     * @ignore
     */
    protected $decoder = null;

    /**
     * Return contents of resource.
     *
     * Note: The type returned depends on the resource.
     * The default is a string, containing the file's contents as a text.
     *
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\NotReadableException  if the file is not readable
     */
    public function getContent()
    {
        /* auto-load */
        try {
            $this->read();
        } catch (\Yana\Core\Exceptions\NotFoundException $e) { // file does not exist
            return "";
        }

        return $this->decoder->encode($this->content, null, $this->caseSensitive);
    }

    /**
     * Create a new instance of this class.
     *
     * This extends the super class constructor.
     *
     * Note the additional parameter $caseSensitive.
     * This parameter decides on how to return key names.
     *
     * It's value can be one of the following constants:
     * <ul>
     *     <li>  CASE_UPPER  upper-case all keys       </li>
     *     <li>  CASE_LOWER  lower-case all keys       </li>
     *     <li>  CASE_MIXED  leave keys in mixed case  </li>
     * </ul>
     *
     * @param   string  $filename         filename
     * @param   int     $caseSensitive    one of: CASE_MIXED, CASE_LOWER, CASE_UPPER
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when argument $caseSensitive is invalid
     */
    public function __construct($filename, $caseSensitive = CASE_MIXED)
    {
        assert(is_string($filename), 'Wrong argument type for argument 1. String expected');
        assert(is_int($caseSensitive), 'Wrong type for argument 2. Integer expected');

        parent::__construct($filename);

        switch ($caseSensitive)
        {
            case CASE_MIXED:
            case CASE_UPPER:
            case CASE_LOWER:
                $this->caseSensitive = $caseSensitive;
            break;
            default:
                $message = "Invalid argument 2. Expected one of CASE_MIXED, CASE_UPPER, CASE_LOWER, found '" .
                    print_r($caseSensitive, true) . "' instead.";
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
            break;
        }

        $this->decoder = static::_getDecoder();
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
        return !is_null($this->getVarByReference($key));
    }

    /**
     * Get a value from the file.
     *
     * Returns the value at the position specified by $key.
     *
     * Example: use "foo1.foo2" to get the contents of the "foo2"
     * child tag inside the "foo1" root tag.
     * Or, more technical speaking, to get the "foo2" element
     * of the "foo1" array.
     *
     * @param   string  $key  address of the var to get
     * @return  mixed
     * @name    SML::getVar()
     * @since   2.9.4
     */
    public function getVar($key)
    {
        return $this->getVarByReference($key);
    }

    /**
     * Returns all contained vars.
     *
     * @return  array
     */
    public function getVars()
    {
        return $this->getVarsByReference();
    }

    /**
     * Returns the value at the position specified by $key.
     *
     * The value is returned by reference.
     *
     * @param   string  $key  address of the var to get (use wildcard '*' to get all)
     * @return  mixed
     * @name    SML::getVarByReference()
     * @since   2.9.5
     */
    public function &getVarByReference($key = "*")
    {
        assert(is_string($key), 'Wrong argument type for argument 1. String expected.');

        $key = $this->_convertKey($key);

        $content =& $this->getVarsByReference();

        /* return result */
        if ($key === "*") {
            return $content;
        } else {
            $result =& \Yana\Util\Hashtable::get($content, $key);
            return $result;
        }
    }

    /**
     * Get a reference to all entries of the file.
     *
     * @return  array
     */
    public function &getVarsByReference()
    {
        /* auto-load */
        try {
            $this->read();
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            // ignored
        }
        return $this->content;
    }

    /**
     * Insert an array into the file.
     *
     * This function sets a new value at the address provided in $key to $value.
     * If the key already exists, it's value gets updated.
     *
     * @param   string  $key    adress of old data
     * @param   mixed   $value  new data
     * @return  \Yana\Files\SML
     * @name    SML::setVar()
     */
    public function setVar($key, $value)
    {
        $this->setVarByReference($key, $value);
        return $this;
    }

    /**
     * Replace key / value pair by reference.
     *
     * @param   string  $key     adress of old data
     * @param   mixed   &$value  new data
     * @return  \Yana\Files\SML
     * @see     SML::insert()
     * @since   2.9.5
     */
    public function setVarByReference($key, &$value)
    {
        assert(is_scalar($key), 'Wrong argument type for argument 1. String expected.');
        $key = $this->_convertKey($key);
        $this->_isReady = true;
        if (is_array($value)) {
            $this->_setKeyCase($value);
        }

        if (isset($this->content[$key])) {
            $this->content[$key] =& $value; // shortcut to improve performance
        } else {
            \Yana\Util\Hashtable::setByReference($this->content, $key, $value);
        }
        return $this;
    }

    /**
     * Replaces all content of the file with the provided array.
     *
     * @param   array  $array  new file content
     * @return  \Yana\Files\SML
     *
     * @name    SML::setVars()
     */
    public function setVars(array $array)
    {
        $this->setVarsByReference($array);
        return $this;
    }


    /**
     * Replaces all content of the file with the provided array.
     *
     * @param   array  &$array  new file content
     * @return  \Yana\Files\SML
     *
     * @name    SML::setVarsByReference()
     */
    public function setVarsByReference(array &$array)
    {
        $this->_setKeyCase($array);
        $this->content =& $array;
        $this->_isReady = true;
        return $this;
    }

    /**
     * Reset the file contents.
     *
     * Changes to the file will not be safed unless you
     * explicitely call $configFile->write().
     * So if you want or need to revert your changes just call
     * $configFile->reset() and all will be fine.
     *
     * @name    SML::reset()
     */
    public function reset()
    {
        $this->content = $this->decoder->getFile($this->getPath(), $this->caseSensitive);
    }

    /**
     * get a string representation
     *
     * If the file is empty, does not exist or is not readable, an empty string is returned.
     *
     * @return  string
     */
    public function __toString()
    {
        try {
            return $this->getContent();
        } catch (\Exception $e) {
            return ""; // must not throw an exception
        }
    }

    /**
     * Initialize file contents.
     *
     * You should always call this before anything else.
     * Returns the file content on success and bool(false) on error.
     *
     * @throws  \Yana\Core\Exceptions\NotReadableException  if the file is not readable
     * @throws  \Yana\Core\Exceptions\NotFoundException     if the file does not exist
     * @return  self
     *
     * @name    SML::read()
     */
    public function read()
    {
        if (!$this->_isReady) {
            parent::read();
            assert(is_array($this->content), 'unexpected result: $this->content');

            $this->content = $this->decoder->getFile($this->content, $this->caseSensitive);
            assert(is_array($this->content), 'unexpected result: $this->content');

            $this->_isReady = true; // setting $this->ready state
        }
        return $this;
    }

    /**
     * Get the number of elements.
     *
     * This returns how many elements can
     * be found inside the array at position
     * $key. If $key points to a non-existing value,
     * or an empty array, the function returns 0.
     *
     * @param   string  $key  (optional)
     * @return  int
     * @throws  \Yana\Core\Exceptions\NotReadableException  if the file is not readable
     *
     * @name    SML::length()
     */
    public function length($key = "*"): int
    {
        assert(is_scalar($key), 'Wrong argument type for argument 1. String expected.');
        $key = $this->_convertKey($key);

        /* auto-load */
        try {
            $this->read();
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            return 0; // file does not exist
        }

        // content is empty
        if (empty($this->content)) {
            return 0;
        }
        // return whole content count
        if ($key === "*") {
            return count($this->content);
        }
        // count specific key
        $result =& \Yana\Util\Hashtable::get($this->content, $key);
        if (is_null($result)) {
            return 0;
        } else {
            assert(is_array($result), 'is_array($result)');
            return count($result);
        }
    }

    /**
     * Remove an entry from the file.
     *
     * When no argument is given the function removes all entries.
     *
     * You may however use a key address like "foo", if the content is an
     * associative array. In that case the index will (of course) not shift.
     * For multi-dimensional arrays this key address may be multi-part, like
     * "foo.bar".
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $key  (optional)
     * @return  bool
     * @throws  \Yana\Core\Exceptions\NotReadableException  if the file is not readable
     *
     * @name    SML::remove()
     */
    public function remove(?string $key = null): bool
    {
        if (is_null($key)) {
            $this->content = array();
            return true;
        }

        assert(is_string($key), 'Wrong argument type for argument 1. String expected.');
        $key = $this->_convertKey($key);

        /* auto-load */
        try {
            $this->read();
        } catch (\Yana\Core\Exceptions\NotFoundException $e) { // file does not exist
            return false;
        }

        /* compute request */
        return \Yana\Util\Hashtable::remove($this->content, $key);

    }

    /**
     * Test if a certain value exists.
     *
     * This function has two synopsis:
     *
     * 1st: if parameter $key is provided.
     *
     * <ul>
     *   <li>  returns bool(true) if the file exists, is loaded and the variable identified by $key is set  </li>
     *   <li>  returns bool(false) otherwise.  </li>
     * </ul>
     *
     * 2nd: if parameter $key is missing, == '' or the wildcard '*'.
     *
     * <ul>
     *   <li>  returns bool(true) if the file exists and is loaded  </li>
     *   <li>  returns bool(false) otherwise.  </li>
     * </ul>
     *
     * @param   string $key (optional)
     * @return  bool
     *
     * @name    SML::exists()
     */
    public function exists(string $key = '*'): bool
    {
        $key = $this->_convertKey($key);

        /* return result */
        if ($key === '*') {
            return parent::exists();
        } else {
            return \Yana\Util\Hashtable::exists($this->content, $key);
        }

    }

    /**
     * Returns some decoder to load and store file contents.
     *
     * @return  \Yana\Files\Decoders\IsDecoder
     */
    protected static function _getDecoder(): \Yana\Files\Decoders\IsDecoder
    {
        return new \Yana\Files\Decoders\NullDecoder();
    }

    /**
     * Read a file in SML syntax and return its contents.
     *
     * The argument $input can wether be a filename or a numeric array
     * of strings created by file($filename).
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
     * @param   array|string  $input          filename or file content
     * @param   int           $caseSensitive  CASE_UPPER|CASE_LOWER|CASE_MIXED
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the input is not a filename or content-array
     */
    public static function getFile($input, $caseSensitive = CASE_MIXED)
    {
        return static::_getDecoder()->getFile($input, $caseSensitive);
    }

    /**
     * Create a SML string from a scalar variable, an object, or an array of data.
     *
     * The argument $name can be used to specify the name of the root node.
     * If $name is omitted, no root node is created.
     *
     * Note that this function will issue an E_USER_NOTICE if $name is omitted
     * and $data is a scalar value. In this case the scalar variable will
     * be named '0' by default.
     *
     * The argument $caseSensitive can be used to decide how keys should be treated.
     *
     * Note that any tags from string inputs will be stripped.
     * You should convert tags to entities, before submiting the input.
     *
     * Valid values for $caseSensitive are:
     * <ul>
     *     <li>  CASE_UPPER  upper-case all keys       </li>
     *     <li>  CASE_LOWER  lower-case all keys       </li>
     *     <li>  CASE_MIXED  leave keys in mixed case  </li>
     * </ul>
     *
     * @param   scalar|array|object  $data           data to encode
     * @param   string               $name           name of root-tag
     * @param   int                  $caseSensitive  one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @return  string
     */
    public static function encode($data, $name = null, $caseSensitive = CASE_MIXED)
    {
        return static::_getDecoder()->encode($data, $name, $caseSensitive);
    }

    /**
     * Read variables from an encoded string.
     *
     * This function is pretty much the same as SML::getFile() except
     * for the fact that it is working on strings rather than files.
     *
     * Returns NULL on error.
     *
     * The argument $input has to be a string, that has been encoded using
     * SML::encode().
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
     * Note: to reaccess an encoded value look at the following examples.
     *
     * Handling boolean values:
     * <code>
     * $input_bool = true;
     * $encoded = SML::encode($input_bool, 'MY_VAR');
     * $decoded = SML::decode($encoded);
     * // the following returns true
     * $input_bool === $decoded['MY_VAR'];
     * </code>
     *
     * ... or shorter:
     * <code>
     * $input_bool = true
     * // the following returns true
     * $input_bool === array_pop(SML::decode(SML::encode($input_bool)));
     * </code>
     *
     * Handling string values and nummerics:
     * <code>
     * $input_string = 'foo';
     * // the following returns true
     * $input_string === array_pop(SML::decode(SML::encode($input_string)));
     *
     * $input_int = 123;
     * // the following returns true
     * $input_int == array_pop(SML::decode(SML::encode($input_int)));
     * </code>
     *
     * Handling the 'NULL' value:
     * <code>
     * $input_null = null;
     * // the following returns true
     * is_null( array_pop(SML::decode(SML::encode($input_string))) );
     * </code>
     *
     * Arrays (were key case does matter):
     * <code>
     * $input_array = array(1,2,3,array(4,5),'a'=>6,'B'=>7);
     * $output_array = SML::decode(SML::encode($input_array));
     * // the following returns true
     * $input_array == $output_array;
     * </code>
     *
     * When dealing with nummeric arrays, or associative arrays where all keys should be uppercase,
     * or if you just don't care, you may set the $caseSensitive parameter to CASE_UPPER.
     *
     * <code>
     * $input_array = array(1,2,3,array(4,5),'A'=>6,'B'=>7);
     * $output_array = SML::decode(SML::encode($input_array,null,CASE_UPPER),CASE_UPPER);
     * // the following returns true
     * $input_array == $output_array;
     * </code>
     *
     * The obvious advantage of doing so is: you can rely on the writing of keys with no need to care
     * for case-sensitivity.
     *
     * @param   string    $input            input
     * @param   int       $caseSensitive    caseSensitive
     * @return  array
     */
    public static function decode($input, $caseSensitive = CASE_MIXED)
    {
        return static::_getDecoder()->decode($input, $caseSensitive);
    }

    /**
     * Convert $key parameter.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $key  the input argument to be checked
     * @return  bool
     */
    private function _convertKey($key)
    {
        assert(is_string($key), 'Wrong argument type for argument 1. String expected.');

        /* Convert empty strings to wildcard.
         * This is to evade problems when testing for bool(false)
         * E.g. "" == false; will compute to true.
         */
        if ($key == '' || $key === '*') {
            return '*';
        }

        /* create and return output */
        switch ($this->caseSensitive)
        {
            case CASE_UPPER:
                return mb_strtoupper("$key");
            break;
            case CASE_LOWER:
                return mb_strtolower("$key");
            break;
            default:
                return "$key";
            break;
        }
    }

    /**
     * Set case of $array's keys according to object settings.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param  array  &$array  the input argument to be checked
     */
    private function _setKeyCase(array &$array)
    {
        /* create and return output */
        switch ($this->caseSensitive)
        {
            case CASE_UPPER:
                $array = \Yana\Util\Hashtable::changeCase($array, CASE_UPPER);
            break;
            case CASE_LOWER:
                $array = \Yana\Util\Hashtable::changeCase($array, CASE_LOWER);
            break;
            default:
                /* intentionally left blank */
            break;
        } /* end switch */
    }

    /**
     * Return file contents as string.
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\NotReadableException  if the file is not readable
     */
    public function getFileContent()
    {
        /* auto-load */
        try {
            $this->read();
        } catch (\Yana\Core\Exceptions\NotFoundException $e) { // file does not exist
            return "";
        }

        if (is_array($this->content)) {
            return $this->decoder->encode($this->content, null, $this->caseSensitive);
        } elseif (is_scalar($this->content)) {
            return (string) $this->content;
        } else {
            return "";
        }
    }

}

?>