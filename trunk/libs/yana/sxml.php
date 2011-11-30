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
 * Simple XML Files
 *
 * This is a wrapper-class that may be used to work with *.xml files.
 * However these files must be convertible to an array and vice-versa.
 *
 * Real-world example:
 * <pre>
 * &lt;calendar&gt;
 *   &lt;categories&gt;
 *     &lt;item id="white"&gt;#ffffff&lt;/item&gt;
 *     &lt;item id="vacation"&gt;#00aa00&lt;/item&gt;
 *   &lt;/categories&gt;
 *   &lt;timezone&gt;1&lt;/timezone&gt;
 *   &lt;dst&gt;0&lt;/dst&gt;
 * &lt;/calendar&gt;
 * </pre>
 *
 * <code>
 * $config = new SXML('calendar.xml');
 * $dst = $config->getVar('dst');
 * if ($dst == '1') {
 *     // do something
 * }
 * // loop through categories
 * foreach ($config->getVar('categories.item') as $category)
 * {
 *     print $category['@id'] . '=' . $category['#pcdata'];
 * }
 * // or ...
 * $array = $config->getVars();
 * foreach ($array['categories']['item'] as $category)
 * {
 *     print $category['@id'] . '=' . $category['#pcdata'];
 * }
 * </code>
 *
 * Note: If you prefer to work with objects instead of arrays, give SimpleXML a try instead.
 * See the PHP manual for details.
 *
 * @access      public
 * @package     yana
 * @subpackage  file_system
 * @since       3.1.0
 * @name        SXML
 */
class SXML extends SML
{
    /**
     * name of root element
     *
     * @access  protected
     * @var     string
     */
    protected $rootNode = "";

    /**
     * constructor
     *
     * Create a new instance of this class.
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
     * @param  string  $filename        filename
     * @param  int     $caseSensitive   one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     */
    public function __construct($filename, $caseSensitive = CASE_MIXED)
    {
        parent::__construct($filename, $caseSensitive);
        $this->decoder = $this;
    }

    /**
     * Read a file in XML syntax and return its contents
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
     * @access  public
     * @static
     * @name    SXML::getFile()
     * @param   array|string  $input          filename or file content
     * @param   int           $caseSensitive  CASE_UPPER|CASE_LOWER|CASE_MIXED
     * @param   string        &$rootNode      (optional)
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the input is not a filename or content-array
     *
     * @see     SXML::encode()
     */
    public static function getFile($input, $caseSensitive = CASE_MIXED, &$rootNode = "")
    {
        assert('is_array($input) || is_string($input); /* Wrong argument type for argument 1. '.
            'String or array expected. */');
        assert('$caseSensitive === CASE_MIXED || $caseSensitive === CASE_LOWER || $caseSensitive === CASE_UPPER;');

        $sxml = "";
        if (is_file("$input")) {
            try {

                $sxml = simplexml_load_file($input, '\Yana\XmlArray');

            } catch (\Exception $e) {
                trigger_error("XML ERROR in file '{$input}'.", E_USER_WARNING);
                return array();
            }
        } elseif (is_array($input)) {
            try {

                $sxml = simplexml_load_string(implode("", $input), '\Yana\XmlArray');

            } catch (\Exception $e) {
                trigger_error("XML ERROR in file.", E_USER_WARNING);
                return array();
            }
        } else {
            $message = "Argument 1 is expected to be a filename or an array " .
                "created with file().\n\t\tInstead found " . gettype($input) .
                " '" . print_r($input, true) . "'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        if (empty($sxml)) {
            trigger_error("XML ERROR in file '{$input}'.", E_USER_WARNING);
            return array();

        } else {
            $rootNode = $sxml->getName();
            $result = $sxml->toArray();
            if ($caseSensitive !== CASE_MIXED) {
                $result = \Yana\Util\Hashtable::changeCase($result, $caseSensitive);
            }
            return $result;
        }

    }

    /**
     * Create a XML-string from an array of data
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
     * @access  public
     * @static
     * @name    SXML::encode()
     * @param   array   $data           data to encode
     * @param   string  $name           name of root-tag
     * @param   int     $caseSensitive  one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @param   int     $indent         internal value (ignore)
     * @return  string
     *
     * @see     SXML::getFile()
     */
    public static function encode($data, $name = null, $caseSensitive = CASE_MIXED, $indent = 0)
    {
        assert('is_array($data) || is_scalar($data); /* Wrong argument type for argument 1. Array expected. */');
        assert('is_null($name) || is_string($name); /* Wrong argument type for argument 2. String expected. */');
        assert('$caseSensitive === CASE_MIXED || $caseSensitive === CASE_LOWER || $caseSensitive === CASE_UPPER; /* '.
            'Invalid argument 3. Expected one of the following constants: CASE_MIXED, CASE_LOWER, CASE_UPPER. */');
        assert('is_int($indent); /* Wrong argument type for argument 4. Integer expected. */');

        if (is_null($data)) {
            return "";
        }

        // use given tag name
        if (is_array($data) && isset($data['#tag'])) {
            $name = $data['#tag'];
            unset($data['#tag']);

        // no tag name found and no fall-back given: use default
        } elseif (!is_string($name)) {
            $name = "root";

        // keep current node name
        } else {
            $name = "$name";
        }

        if ($caseSensitive === CASE_UPPER) {
            $name = mb_strtoupper($name);
        } elseif ($caseSensitive === CASE_LOWER) {
            $name = mb_strtolower($name);
        }

        // indent tag
        $tab = str_pad("", $indent, "\t");

        // handle tag data
        if (is_array($data)) {
            $result = "";
            $attributes = "";
            $omitTags = true;
            $shortTags = false;
            foreach ($data as $currentName => $currentData)
            {
                if (is_string($currentName)) {
                    if ($currentName[0] === '@') {
                        $attributes .= " " . mb_substr($currentName, 1) . "=\"$currentData\"";
                    } elseif ($currentName === '#pcdata') {
                        $result .= $currentData;
                        $shortTags = true;
                    } else {
                        $result .= self::encode($currentData, $currentName, $caseSensitive, $indent +1);
                    }
                    $omitTags = false;
                } else {
                    $result .= self::encode($currentData, $name, $caseSensitive, $indent);
                    $omitTags = true && $omitTags;
                }
            }
            if (!$omitTags) {
                if ($result === "") {
                    return "$tab<$name$attributes/>\n";
                } elseif ($shortTags) {
                    return "$tab<$name$attributes>$result</$name>\n";
                } else {
                    return "$tab<$name$attributes>\n$result$tab</$name>\n";
                }
            } else {
                return $result;
            }
        } else {
            return "$tab<$name>$data</$name>\n";
        }
    }

    /**
     * Read variables from an encoded string
     *
     * This function is pretty much the same as SXML::getFile() except
     * for the fact that it is working on strings rather than files.
     *
     * @access  public
     * @static
     * @name    SXML::decode()
     * @param   string    $input            input
     * @param   int       $caseSensitive    one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @return  array
     */
    public static function decode($input, $caseSensitive = CASE_MIXED)
    {
        assert('is_string($input); /* Wrong argument type for argument 1. String expected. */');
        $input = explode("\n", "$input");
        return self::getFile($input, $caseSensitive);
    }

    /**
     * Proxy for static function _decode
     *
     * This function should be overwritten by any subclass.
     * It is meant to simulate the behavior of virtual static references.
     *
     * @access  protected
     * @param   string    $input            input
     * @param   int       $caseSensitive    one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @return  array
     * @ignore
     */
    protected function _decode($input, $caseSensitive = CASE_MIXED)
    {
        return self::decode($input, $caseSensitive);
    }

    /**
     * Proxy for static function _encode
     *
     * This function should be overwritten by any subclass.
     * It is meant to simulate the behavior of virtual static
     * references.
     *
     * @access  protected
     * @param   scalar|array|object  $data           data to encode
     * @param   string               $name           name of root-tag
     * @param   int                  $caseSensitive  one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @return  string
     * @ignore
     */
    protected function _encode($data, $name = null, $caseSensitive = CASE_MIXED)
    {
        if (is_null($name)) {
            $name = $this->rootNode;
        }
        return self::encode($data, $name, $caseSensitive);
    }

    /**
     * Proxy for static function _getFile
     *
     * This function should be overwritten by any subclass.
     * It is meant to simulate the behavior of virtual static
     * references.
     *
     * @access  protected
     * @param   array|string  $input          filename or file content
     * @param   int           $caseSensitive  CASE_UPPER|CASE_LOWER|CASE_MIXED
     * @return  array
     * @ignore
     */
    protected function _getFile($input, $caseSensitive = CASE_MIXED)
    {
        return self::getFile($input, $caseSensitive, $this->rootNode);
    }

}

?>