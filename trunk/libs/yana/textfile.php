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
 * Base class for handling text files
 *
 * Adds functions to read and write to line-based text files.
 *
 * @access      public
 * @package     yana
 * @subpackage  file_system
 */
class TextFile extends File implements \Yana\File\IsTextFile
{

    /**
     * set file content
     *
     * Replace the content of the file with the value of argument $content.
     *
     * @access  public
     * @param   string  $content    content
     */
    public function setContent($content)
    {
        assert('is_string($content); // Invalid argument type argument 1. String expected.');
        assert('is_array($this->content);');
        $content = explode("\n", (string) $content);
        $this->content = $content;
    }

    /**
     * get line from file
     *
     * The content of the given line is returned.
     * If the line does not exist, the function returns false.
     *
     * @access  public
     * @param   int  $lineNr    line number
     * @return  string
     */
    public function getLine($lineNr)
    {
        if (isset($this->content[$lineNr])) {
            return $this->content[$lineNr];
        } else {
            return false;
        }
    }

    /**
     * insert new content
     *
     * This appends the scalar value $content as a new line to the end of the
     * file.
     *
     * @access  public
     * @param   scalar  $content    content
     */
    public function appendLine($content)
    {
        assert('is_scalar($content); // Wrong argument type $content. Scalar value expected.');
        if (!isset($this->content)) {
            $this->content = array();
        }
        assert('is_array($this->content);');
        array_push($this->content, "$content");
    }

    /**
     * set new content
     *
     * This sets the text of the given line to the given content.
     * If the line does not exist, an OutOfBoundsException is thrown.
     *
     * @access  public
     * @param   int     $lineNr     line number
     * @param   string  $content    content
     */
    public function setLine($lineNr, $content)
    {
        assert('is_int($lineNr); // Invalid argument type argument 1. Integer expected.');
        assert('is_string($content); // Invalid argument type argument 2. String expected.');
        if (isset($this->content[$lineNr])) {
            assert('is_array($this->content);');
            $this->content[$lineNr] = (string) $content;
        } else {
            $message = "Line '$lineNr' does not exist in file '{$this->getPath()}'.";
            throw new OutOfBoundsException($message, E_USER_WARNING);
        }
    }

    /**
     * remove an entry from the file
     *
     * If no argument is given the function removes all entries.
     * Else the function removes the entry at line of the given number, if the
     * key is numeric. Note: The array index shifts after you remove a line, so
     * line numbers may change.
     *
     * If the line does not exist, an OutOfBoundsException is thrown.
     *
     * @access  public
     * @param   int  $lineNr  line to remove
     */
    public function removeLine($lineNr = null)
    {
        assert('is_int($lineNr) || is_null($lineNr); // Wrong type for argument 1. Integer expected');

        if (is_null($lineNr)) {
            $this->content = array();
        } elseif (isset($this->content[$lineNr])) {
            array_splice($this->content, $lineNr, 1);
        } else {
            $message = "Line '$lineNr' does not exist in file '{$this->getPath()}'.";
            throw new OutOfBoundsException($message, E_USER_WARNING);
        }
    }

    /**
     * get the number of lines in the file
     *
     * Returns the number of lines in the currently opened file.
     * Note that the number raises if you append new lines.
     * If the file is empty, doesn't exist or is not loaded, the function will
     * return 0.
     *
     * @access  public
     * @return  int
     */
    public function length()
    {
        if (!isset($this->content)) {
            return 0;
        } else {
            assert('is_array($this->content);');
            return count($this->content);
        }
    }

}

?>