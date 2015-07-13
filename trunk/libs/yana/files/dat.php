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

namespace Yana\Files;

/**
 * This class is an alternative to csv files.
 *
 * Each line represents a row of data.
 * Tags are used to mark up the fields.
 *
 * The use of tag delimiters inside field values is not allowed.
 * If a tag is found in a value it automatically gets removed.
 *
 * The structure is not as strict as you might know from CSV files,
 * which means the number and names of fields inside a row may
 * vary from one row to another.
 *
 * This provides you with more flexibility. For example:
 * You may decide not to use a field for future entries
 * or just add a new one and you don't have to change your previous
 * entries inside the file.
 *
 * This is also a good way around if you just don't know the
 * structure of future entries yet.
 *
 * @package     yana
 * @subpackage  files
 * @ignore
 * @deprecated  since version 4.0
 */
class Dat extends \Yana\Files\Text
{

    /**
     * Retrieve a line of data from the file.
     *
     * This returns the dataset in line $lineNr as an associative array, or
     * bool(false) on error.
     *
     * Note that the keys are returned in capital letters.
     *
     * @param   int  $lineNr  line to retrieve
     * @return  array
     */
    public function getLine($lineNr)
    {
        assert('is_int($lineNr)', ' Invalid argument type argument 1. Integer expected.');
        if (isset($this->content[$lineNr])) {
            return self::_parseLine($this->content[$lineNr]);
        } else {
            trigger_error("There is no line '$lineNr' in file '" . $this->getPath() . "'.", E_USER_NOTICE);
            return array();
        }
    }

    /**
     * Retrieve all data from the file.
     *
     * This returns the datasets, or bool(false) on error.
     *
     * Note that the keys are returned in capital letters.
     *
     * @return  array
     */
    public function getLines()
    {
        $array = array();
        if (!empty($this->content)) {
            foreach ($this->content as $line)
            {
                $array[] = self::_parseLine($line);
            }
        }
        return $array;
    }

    /**
     * Parse a line of text and return content.
     *
     * This function parses a single line of text from a DatFile
     * and returns the contents as an array.
     *
     * @param   string  $line  line of text to parse
     * @return  array
     */
    private static function _parseLine($line)
    {
        assert('is_string($line)', ' Wrong type for argument 1. String expected');
        $array = array();
        $matches = array();
        preg_match_all("/<(.*)>(.*)<\/.*>/Ui", $line, $matches);
        for ($i = 0; $i < count($matches[0]); $i++)
        {
            $matches[2][$i] = preg_replace("/(\S{80})(\S*)/i", "\\1<wbr>\\2", $matches[2][$i]);
            $array[mb_strtoupper($matches[1][$i])] = $matches[2][$i];
        }
        assert('is_array($array)', ' Returned type must be an array');
        return $array;
    }

    /**
     * Insert (append) an entry to the file.
     *
     * This appends the new entry to the file.
     * Depending on the setting for argument $append, the entry is appended to
     * the end, or inserted on top of the file.
     *
     * The input array must not be multi-dimensional.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   array  $content  associative array containing the new entry
     * @param   bool   $append   true = append entry on end of file, false = insert entry on top of file
     */
    public function appendLine($content, $append = false)
    {
        assert('is_array($content)', ' Wrong type for argument 1. Array expected');
        assert('is_bool($append)', ' Wrong type for argument 2. Boolean expected');

        $txt = self::_encodeEntry($content);
        if (!$this->isEmpty()) {
            if ($append) {
                $this->content[] = $txt;
            } else {
                array_unshift($this->content, $txt);
            }
        } else {
            $this->content[0] = $txt;
        }
    }

    /**
     * Update an entry of the file.
     *
     * Replaces an existing entry.
     * The input array must not be multi-dimensional.
     *
     * If the line does not exist, an OutOfBoundsException is thrown.
     *
     * @param   int    $lineNr    line number to update
     * @param   array  $newEntry  associative array containing the new entry
     * @since   2.9.6
     */
    public function setLine($lineNr, $newEntry)
    {
        assert('is_int($lineNr)', ' Wrong type for argument 1. Integer expected');
        assert('is_array($newEntry)', ' Wrong type for argument 1. Integer expected');

        $txt = self::_encodeEntry($newEntry);
        parent::setLine($lineNr, $txt);
    }

    /**
     * Encode an entry.
     *
     * @param   array   $entry  entry that should be encode
     * @return  string
     */
    private static function _encodeEntry(array $entry)
    {
        $txt = "";
        foreach ($entry as $key => $element)
        {
            $key = mb_strtoupper($key);
            $element = strip_tags($element);
            $txt .= "<$key>$element</$key>";
        }
        $txt = nl2br($txt);
        $txt = preg_replace("/\s/", " ", $txt);
        $txt .= "\n";

        return $txt;
    }

}

?>