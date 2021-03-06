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
 * Base class for handling text files
 *
 * Adds functions to read and write to line-based text files.
 *
 * @package     yana
 * @subpackage  files
 */
class Text extends \Yana\Files\File implements \Yana\Files\IsTextFile
{

    /**
     * Set file content.
     *
     * Replace the content of the file with the value of argument $content.
     *
     * @param   string  $content  content
     */
    public function setContent(string $content)
    {
        assert(is_array($this->content), 'is_array($this->content)');
        $content = explode("\n", (string) $content);
        $this->content = $content;
    }

    /**
     * Get line from file.
     *
     * The content of the given line is returned.
     * If the line does not exist, the function returns false.
     *
     * @param   int  $lineNr  line number
     * @return  string
     * @throws  \Yana\Core\Exceptions\OutOfBoundsException  when the line is not found
     */
    public function getLine(int $lineNr)
    {
        if (!isset($this->content[$lineNr])) {
            $message = "There is no line '$lineNr' in file '" . $this->getPath() . "'.";
            throw new \Yana\Core\Exceptions\OutOfBoundsException($message, \Yana\Log\TypeEnumeration::INFO);
        }
        return $this->content[$lineNr];
    }

    /**
     * Appends the value as a new line to the end of the file.
     *
     * @param   scalar  $content    content
     */
    public function appendLine($content)
    {
        assert(is_scalar($content), 'Wrong argument type $content. Scalar value expected.');
        assert(is_array($this->content), 'is_array($this->content)');
        array_push($this->content, "$content");
    }

    /**
     * Sets the text of the given line to the given content.
     *
     * @param   int     $lineNr     line number
     * @param   string  $content    content
     * @thros   \Yana\Core\Exceptions\OutOfBoundsException  if the line does not exist
     */
    public function setLine(int $lineNr, $content)
    {
        assert(is_int($lineNr), 'Invalid argument type argument 1. Integer expected.');
        assert(is_string($content), 'Invalid argument type argument 2. String expected.');
        if (isset($this->content[$lineNr])) {
            assert(is_array($this->content), 'is_array($this->content)');
            $this->content[$lineNr] = (string) $content;
        } else {
            $message = "Line '$lineNr' does not exist in file '{$this->getPath()}'.";
            throw new \Yana\Core\Exceptions\OutOfBoundsException($message, E_USER_WARNING);
        }
    }

    /**
     * Remove an entry from the file.
     *
     * If no argument is given the function removes all entries.
     * Else the function removes the entry at line of the given number, if the
     * key is numeric. Note: The array index shifts after you remove a line, so
     * line numbers may change.
     *
     * @param   int  $lineNr  line to remove
     * @throws   \Yana\Core\Exceptions\OutOfBoundsException  if the line does not exist
     */
    public function removeLine(?int $lineNr = null)
    {
        if (is_null($lineNr)) {
            $this->content = array();
        } elseif (isset($this->content[$lineNr])) {
            array_splice($this->content, $lineNr, 1);
        } else {
            $message = "Line '$lineNr' does not exist in file '{$this->getPath()}'.";
            throw new \Yana\Core\Exceptions\OutOfBoundsException($message, E_USER_WARNING);
        }
    }

    /**
     * Returns the number of lines in the currently opened file.
     *
     * Note that the number raises if you append new lines.
     * If the file is empty, doesn't exist or is not loaded, the function will
     * return 0.
     *
     * @return  int
     */
    public function length(): int
    {
        $count = 0;
        if (isset($this->content)) {
            assert(is_array($this->content), 'is_array($this->content)');
            $count = count($this->content);
        }
        return $count;
    }

}

?>