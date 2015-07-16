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
 * JSON Files.
 *
 * This is a wrapper-class that may be used to work with *.json files.
 *
 * @package     yana
 * @subpackage  files
 */
class Json extends \Yana\Core\Object implements IsDecoder
{

    /**
     * Read a file in JSON syntax and return its contents.
     *
     * The argument $input can be a filename or a numeric array of strings
     * created by file($filename).
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
    public function getFile($input, $caseSensitive = CASE_MIXED)
    {
        assert('is_array($input) || is_string($input); /* Wrong argument type for argument 1. '.
            'String or array expected. */');
        assert('$caseSensitive === CASE_MIXED || $caseSensitive === CASE_LOWER || $caseSensitive === CASE_UPPER;');

        if (is_string($input) && is_file($input)) {
            $result = json_decode(file_get_contents($input), true);
        } elseif (is_array($input)) {
            $result = json_decode(implode("", $input), true);
        } else {
            $message = "Argument 1 is expected to be a filename or an array " .
                "created with file().\n\t\tInstead found " . gettype($input) .
                " '" . print_r($input, true) . "'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        if ($caseSensitive != CASE_MIXED) {
            $result = \Yana\Util\Hashtable::changeCase($result, $caseSensitive);
        }

        return $result;
    }

    /**
     * Create a JSON-string from an array of data.
     *
     * The argument $name can be used to specify the name of the root node.
     * If $name is omitted, no root node is created.
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
     * @param   array   $data           data to encode
     * @param   string  $name           name of root-tag
     * @param   int     $caseSensitive  one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @param   int     $indent         internal value (ignore)
     * @return  string
     */
    public function encode($data, $name = null, $caseSensitive = CASE_MIXED, $indent = 0)
    {
        assert('is_array($data) || is_scalar($data); // Wrong argument type for argument 1. Array expected.');
        assert('is_null($name) || is_string($name); // Wrong argument type for argument 2. String expected.');
        assert('$caseSensitive === CASE_MIXED || $caseSensitive === CASE_LOWER || $caseSensitive === CASE_UPPER; /* '.
            'Invalid argument 3. Expected one of the following constants: CASE_MIXED, CASE_LOWER, CASE_UPPER. */');
        assert('is_int($indent); // Wrong argument type for argument 4. Integer expected.');

        if (!is_null($name)) {
            $data = array($name => $data);
        }
        if ($caseSensitive != CASE_MIXED && is_array($data)) {
            $data = \Yana\Util\Hashtable::changeCase($data, $caseSensitive);
        }
        return json_encode($data);
    }

    /**
     * Read variables from an encoded string.
     *
     * @param   string    $input            input
     * @param   int       $caseSensitive    one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @return  array
     */
    public function decode($input, $caseSensitive = CASE_MIXED)
    {
        assert('is_string($input); // Wrong argument type $input. String expected.');
        $input = explode("\n", "$input");
        return $this->getFile($input, $caseSensitive);
    }

}

?>