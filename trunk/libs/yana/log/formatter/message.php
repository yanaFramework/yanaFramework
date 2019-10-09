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

namespace Yana\Log\Formatter;

/**
 * Data Interchange class that holds information about an error.
 *
 * @package    yana
 * @subpackage log
 */
class Message extends \Yana\Core\StdObject
{

    /**
     * @var int
     */
    private $_level = 0;

    /**
     * @var string
     */
    private $_description = "";

    /**
     * @var string
     */
    private $_filename = "";

    /**
     * @var int
     */
    private $_lineNumber = 0;

    /**
     * @var bool
     */
    private $_hasMore = false;

    /**
     * Returns the level of severity.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->_level;
    }

    /**
     * The level of severity.
     *
     * @param  int  $level  error level, must be > 0
     * @return Message 
     */
    public function setLevel($level)
    {
        assert(is_int($level), 'Invalid argument $level: int expected');

        $this->_level = (int) $level;
        return $this;
    }

    /**
     * Get a brief error description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set a brief error description.
     *
     * @param  string  $description  verbose error description
     * @return Message 
     */
    public function setDescription($description)
    {
        assert(is_string($description), 'Invalid argument $description: string expected');

        $this->_description = (string) $description;
        return $this;
    }

    /**
     * Get file where the error occured / was thrown.
     *
     * Note: this setting may be empty for general errors, that are not related to code.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * Set an absolute path to a file.
     *
     * @param  string  $filename  path to file where the error occured
     * @return Message 
     */
    public function setFilename($filename)
    {
        assert(is_string($filename), 'Invalid argument $filename: string expected');

        $this->_filename = (string) $filename;
        return $this;
    }

    /**
     * Returns the line number.
     *
     * Note: 0 means "no line number" and is used for general errors.
     *
     * @return int
     */
    public function getLineNumber()
    {
        return $this->_lineNumber;
    }

    /**
     * Sets the line number where the error occured / was thrown.
     *
     * @param  string  $lineNumber  line of file where the error occured
     * @return Message 
     */
    public function setLineNumber($lineNumber)
    {
        assert(is_int($lineNumber), 'Invalid argument $lineNumber: int expected');

        $this->_lineNumber = (int) $lineNumber;
        return $this;
    }

    /**
     * Returns true, if this error has occured before.
     *
     * @return bool
     */
    public function hasMore()
    {
        return $this->_hasMore;
    }

    /**
     * Flags, that this error has occured before.
     *
     * @return Message 
     */
    public function setHasMore()
    {

        $this->_hasMore = true;
        return $this;
    }

}

?>