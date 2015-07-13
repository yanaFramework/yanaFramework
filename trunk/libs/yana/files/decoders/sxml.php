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

namespace Yana\Files\Decoders;

/**
 * Simple XML Files.
 *
 * @package     yana
 * @subpackage  files
 */
class SXML extends \Yana\Core\Object implements \Yana\Files\Decoders\IsDecoder
{

    /**
     * Name of root element.
     *
     * @var  string
     */
    private $_rootNode = "";

    /**
     * Read a file in XML syntax and return its contents.
     *
     * {@inheritdoc}
     *
     * @param   array|string  $input          filename or file content
     * @param   int           $caseSensitive  CASE_UPPER|CASE_LOWER|CASE_MIXED
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the input is not a filename or content-array
     */
    public function getFile($input, $caseSensitive = CASE_MIXED)
    {
        assert('is_array($input) || is_string($input); /* Wrong argument type for argument 1. '.
            'String or array expected. */');
        assert('$caseSensitive === CASE_MIXED || $caseSensitive === CASE_LOWER || $caseSensitive === CASE_UPPER;');

        $sxml = "";
        if (is_file("$input")) {
            try {

                $sxml = simplexml_load_file($input, '\Yana\Util\XmlArray');

            } catch (\Exception $e) {
                trigger_error("XML ERROR in file '{$input}'.", E_USER_WARNING);
                return array();
            }
        } elseif (is_array($input)) {
            try {

                $sxml = simplexml_load_string(implode("", $input), '\Yana\Util\XmlArray');

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
            $this->_rootNode = $sxml->getName();
            $result = $sxml->toArray();
            if ($caseSensitive !== CASE_MIXED) {
                $result = \Yana\Util\Hashtable::changeCase($result, $caseSensitive);
            }
            return $result;
        }

    }

    /**
     * Create a XML-string from an array of data.
     *
     * {@inheritdoc}
     *
     * @param   array   $data           data to encode
     * @param   string  $name           name of root-tag
     * @param   int     $caseSensitive  one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @param   int     $indent         internal value (ignore)
     * @return  string
     */
    public function encode($data, $name = null, $caseSensitive = CASE_MIXED, $indent = 0)
    {
        assert('is_array($data) || is_scalar($data)', 'Wrong argument type for argument 1. Array expected.');
        assert('is_null($name) || is_string($name)', 'Wrong argument type for argument 2. String expected.');
        assert('$caseSensitive === CASE_MIXED || $caseSensitive === CASE_LOWER || $caseSensitive === CASE_UPPER; /* '.
            'Invalid argument 3. Expected one of the following constants: CASE_MIXED, CASE_LOWER, CASE_UPPER. */');
        assert('is_int($indent)', 'Wrong argument type for argument 4. Integer expected.');

        if (is_null($data)) {
            return "";
        }

        if (is_null($name)) {
            $name = $this->_rootNode;
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
            $name = (string) $name;
        }

        switch ($caseSensitive)
        {
            case CASE_UPPER:
                $name = \mb_strtoupper($name);
                break;
            case CASE_LOWER:
                $name = \mb_strtolower($name);
                break;
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
     * Read variables from an encoded string.
     *
     * {@inheritdoc}
     *
     * @param   string    $input            input
     * @param   int       $caseSensitive    one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @return  array
     */
    public function decode($input, $caseSensitive = CASE_MIXED)
    {
        assert('is_string($input)', 'Wrong argument type for argument 1. String expected.');
        $input = explode("\n", "$input");
        return self::getFile($input, $caseSensitive);
    }

}

?>