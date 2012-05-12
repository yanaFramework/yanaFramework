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
 * <<Interface>> writable file system resource
 *
 * This class identifies writable resources.
 *
 * @package     yana
 * @subpackage  files
 */
interface IsTextFile extends \Yana\Files\IsWritable
{

    /**
     * Replace file content.
     *
     * Replace the content of the file with the value of argument $content.
     *
     * @param   string  $content  any file data
     */
    public function setContent($content);

    /**
     * Get line from file.
     *
     * The content of the given line is returned.
     * If the line does not exist, the function returns false.
     *
     * @param   int  $lineNr  starting with 0 for first line
     * @return  string
     */
    public function getLine($lineNr);

    /**
     * Append new content.
     *
     * This appends the scalar value $content as a new line to the end of the
     * file.
     *
     * @param   scalar  $content  content
     */
    public function appendLine($content);

    /**
     * Replace one line.
     *
     * This sets the text of the given line to the given content.
     * If the line does not exist, an OutOfBoundsException is thrown.
     *
     * @param   int     $lineNr   starting with 0 for first line
     * @param   string  $content  any file data, must not contain line-break
     */
    public function setLine($lineNr, $content);

    /**
     * Remove an entry from the file.
     *
     * If no argument is given the function removes all entries.
     * Else the function removes the entry at line of the given number, if the
     * key is numeric. Note: The array index shifts after you remove a line, so
     * line numbers may change.
     *
     * If the line does not exist, an OutOfBoundsException is thrown.
     *
     * @param   int  $lineNr  line to remove
     */
    public function removeLine($lineNr = null);

    /**
     * Get the number of lines in the file.
     *
     * Returns the number of lines in the currently opened file.
     * Note that the number raises if you append new lines.
     * If the file is empty, doesn't exist or is not loaded, the function will
     * return 0.
     *
     * @return  int
     */
    public function length();

}

?>