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

namespace Yana\Files\Streams\Wrappers;

/**
 * <<nullobject>> This is a dummy wrapper for unit tests.
 *
 * @package     yana
 * @subpackage  files
 */
class NullWrapper extends \Yana\Files\Streams\Wrappers\AbstractWrapper
{

    /**
     * @var  string
     */
    private $_content = "";

    /**
     * @var  int
     */
    private $_position = 0;

    /**
     * Renames a file or directory
     *
     * @param   string  $pathFrom
     * @param   string  $pathTo
     * @return  bool
     */
    public function renameFileOrDirectory($pathFrom, $pathTo)
    {
        return true;
    }

    /**
     * Close an resource
     */
    public function closeFile()
    {
        // do nothing
    }

    /**
     * Tests for end-of-file on a file pointer
     *
     * @return  bool
     */
    public function isEndOfFile()
    {
        return true;
    }

    /**
     * Flushes the output
     *
     * @return  bool
     */
    public function flushFileContents()
    {
        return true;
    }

    /**
     * Advisory file locking
     *
     * @param   int  $operation
     * @return  bool
     */
    public function lockFile($operation)
    {
        return true;
    }

    /**
     * Change stream options
     *
     * @param   string  $path
     * @param   int     $options
     * @param   int     $var
     * @return  bool
     */
    public function setMetaData($path, $options, $var)
    {
        return true;
    }

    /**
     * Opens file or URL
     *
     * @param   string  $path
     * @param   string  $mode
     * @param   int     $options
     * @param   string  &$opened_path
     * @return  bool
     */
    public function openFile($path, $mode, $options, &$openedPath)
    {
        return true;
    }

    /**
     * Read from stream
     *
     * @param   int  $count  content length to read
     * @return  string
     */
    public function readFile($count)
    {
        $content = \substr($this->_content, $this->_position, $count);
        $this->_position += $count;
        return $content;
    }

    /**
     * Seeks to specific location in a stream
     *
     * @param   int  $offset  new position to move to
     * @param   int  $whence
     * @return  bool
     */
    public function seekInFile($offset, $whence = SEEK_SET)
    {
        return true;
    }

    /**
     * Change stream options
     *
     * @param   int  $option
     * @param   int  $arg1
     * @param   int  $arg2
     * @return  bool
     */
    public function setOption($option, $arg1, $arg2)
    {
        return true;
    }

    /**
     * Retrieve information about a file resource
     *
     * @return  array
     */
    public function getFileStats()
    {
        return array();
    }

    /**
     * Retrieve the current position of a stream
     *
     * @return  int
     */
    public function getPositionInFile()
    {
        return $this->_position;
    }

    /**
     * Truncate stream
     *
     * @param   int  $newSize  truncate to this file size
     * @return  bool
     */
    public function truncateFile($newSize)
    {
        $this->_position = 0;
        $this->_content = $this->readFile($newSize);
        return true;
    }

    /**
     * Write to stream
     *
     * @param   string  $data
     * @return  int
     */
    public function writeFile($data)
    {
        $this->_position = 0;
        $this->_content = $data;
        return strlen($data);
    }

    /**
     * Delete a file
     *
     * @param   string  $path
     * @return  bool
     */
    public function removeFile($path)
    {
        $this->writeFile("");
        return true;
    }

    /**
     * Retrieve information about a file
     *
     * @param   string  $path
     * @param   int     $flags
     * @return  array
     */
    public function getUrlStats($path, $flags)
    {
        return array();
    }

    /**
     * Close directory handle
     *
     * @return bool
     */
    public function closeDirectory()
    {
        return true;
    }

    /**
     * Open directory handle
     *
     * @param string $path
     * @param int $options
     * @return bool
     */
    public function openDirectory($path , $options)
    {
        return true;
    }

    /**
     * Read entry from directory handle
     *
     * @return string
     */
    public function readDirectory()
    {
        return false;
    }

    /**
     * Rewind directory handle
     *
     * @return bool
     */
    public function rewindDirectory()
    {
        return true;
    }

    /**
     * Create a directory
     *
     * @param string $path
     * @param int $mode
     * @param int $options
     * @return bool
     */
    public function makeDirectory($path , $mode , $options)
    {
        return true;
    }

    /**
     * Removes a directory
     *
     * @param string $path
     * @param int    $options
     * @return bool
     */
    public function removeDirectory($path , $options)
    {
        return true;
    }

}

?>