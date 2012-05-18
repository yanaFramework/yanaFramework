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
 * <<interface>> En-/Decoder.
 *
 * @package     yana
 * @subpackage  files
 */
interface IsDecoder
{

    /**
     * Read a file and return its contents.
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
    public function getFile($input, $caseSensitive = CASE_MIXED);

    /**
     * Create a string from a scalar variable, an object, or an array of data.
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
    public function encode($data, $name = null, $caseSensitive = CASE_MIXED);

    /**
     * Read variables from an encoded string.
     *
     * This function is pretty much the same as getFile() except
     * for the fact that it is working on strings rather than files.
     *
     * Returns NULL on error.
     *
     * The argument $input has to be a string, that has been encoded using encode().
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
     * $encoded = $decoder->encode($input_bool, 'MY_VAR');
     * $decoded = $decoder->decode($encoded);
     * // the following returns true
     * $input_bool === $decoded['MY_VAR'];
     * </code>
     *
     * ... or shorter:
     * <code>
     * $input_bool = true
     * // the following returns true
     * $input_bool === array_pop($decoder->decode($decoder->encode($input_bool)));
     * </code>
     *
     * Handling string values and nummerics:
     * <code>
     * $input_string = 'foo';
     * // the following returns true
     * $input_string === array_pop($decoder->decode($decoder->encode($input_string)));
     *
     * $input_int = 123;
     * // the following returns true
     * $input_int == array_pop($decoder->decode($decoder->encode($input_int)));
     * </code>
     *
     * Handling the 'NULL' value:
     * <code>
     * $input_null = null;
     * // the following returns true
     * is_null( array_pop($decoder->decode($decoder->encode($input_string))) );
     * </code>
     *
     * Arrays (were key case does matter):
     * <code>
     * $input_array = array(1,2,3,array(4,5),'a'=>6,'B'=>7);
     * $output_array = $decoder->decode($decoder->encode($input_array));
     * // the following returns true
     * $input_array == $output_array;
     * </code>
     *
     * When dealing with nummeric arrays, or associative arrays where all keys should be uppercase,
     * or if you just don't care, you may set the $caseSensitive parameter to CASE_UPPER.
     *
     * <code>
     * $input_array = array(1,2,3,array(4,5),'A'=>6,'B'=>7);
     * $output_array = $decoder->decode($decoder->encode($input_array,null,CASE_UPPER),CASE_UPPER);
     * // the following returns true
     * $input_array == $output_array;
     * </code>
     *
     * The obvious advantage of doing so is: you can rely on the writing of keys with no need to care
     * for case-sensitivity.
     *
     * @param   string  $input          input
     * @param   int     $caseSensitive  caseSensitive
     * @return  array
     */
    public function decode($input, $caseSensitive = CASE_MIXED);

}

?>