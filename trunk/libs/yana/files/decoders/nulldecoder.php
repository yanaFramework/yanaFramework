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

namespace Yana\Files\Decoders;

/**
 * Mock class.
 *
 * @package     yana
 * @subpackage  files
 * @ignore
 */
class NullDecoder extends \Yana\Core\StdObject implements \Yana\Files\Decoders\IsDecoder
{

    /**
     * Read an encoded file and return its contents.
     *
     * {@inheritdoc}
     *
     * @param   array|string  $input          filename as string, or file content as array of lines
     * @param   int           $caseSensitive  CASE_UPPER|CASE_LOWER|CASE_MIXED
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the input is not a filename or content-array
     */
    public function getFile($input, $caseSensitive = CASE_MIXED)
    {
        if (is_string($input) && is_file($input)) {
            $result = (array) unserialize(file_get_contents($input));
        } elseif (is_array($input)) {
            $result = (array) unserialize(implode("", $input));
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
     * Create a string from a scalar variable, an object, or an array of data.
     *
     * {@inheritdoc}
     *
     * @param   scalar|array|object  $data           data to encode
     * @param   string               $name           name of root-tag
     * @param   int                  $caseSensitive  one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @param   int                  $indent         internal value (ignore)
     * @return  string
     */
    public function encode($data, $name = null, $caseSensitive = CASE_MIXED, $indent = 0)
    {
        if (!is_null($name)) {
            $data = array($name => $data);
        }
        if ($caseSensitive != CASE_MIXED && is_array($data)) {
            $data = \Yana\Util\Hashtable::changeCase($data, $caseSensitive);
        }
        return \serialize($data);
    }

    /**
     * Read variables from an encoded string.
     *
     * {@inheritdoc}
     *
     * @param   string  $input          input
     * @param   int     $caseSensitive  caseSensitive
     * @return  array
     */
    public function decode($input, $caseSensitive = CASE_MIXED)
    {
        return $this->getFile(explode("\n", "$input"), $caseSensitive);
    }

}

?>