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
 * <<interface>> Used for implementing streams.
 *
 * @package     yana
 * @subpackage  files
 * @link        http://www.php.net/manual/en/class.streamwrapper.php
 */
interface IsStreamBase
{

    /**
     * Retrieve the underlaying resource
     *
     * @param   int  $castAs
     * @return  resource
     */
    public function getResource($castAs);

    /**
     * Renames a file or directory
     *
     * @param   string  $pathFrom
     * @param   string  $pathTo
     * @return  bool
     * @name
     */
    public function renameFileOrDirectory($pathFrom, $pathTo);

    /**
     * Close an resource
     */
    public function closeFile();

    /**
     * Tests for end-of-file on a file pointer
     *
     * @return  bool
     */
    public function isEndOfFile();

    /**
     * Flushes the output
     *
     * @return  bool
     */
    public function flushFileContents();

    /**
     * Advisory file locking
     *
     * @param   int  $operation
     * @return  bool
     */
    public function lockFile($operation);

    /**
     * Change stream options
     *
     * @param   string  $path
     * @param   int     $options
     * @param   int     $var
     * @return  bool
     */
    public function setMetaData($path, $options, $var);

    /**
     * Opens file or URL
     *
     * @param   string  $path
     * @param   string  $mode
     * @param   int     $options
     * @param   string  &$opened_path
     * @return  bool
     */
    public function openFile($path, $mode, $options, &$openedPath);

    /**
     * Read from stream
     *
     * @param   int  $count
     * @return  string
     */
    public function readFile($count);

    /**
     * Seeks to specific location in a stream
     *
     * @param   int  $offset
     * @param   int  $whence
     * @return  bool
     */
    public function seekInFile($offset, $whence = SEEK_SET);

    /**
     * Change stream options
     *
     * @param   int  $option
     * @param   int  $arg1
     * @param   int  $arg2
     * @return  bool
     */
    public function setOption($option, $arg1, $arg2);

    /**
     * Retrieve information about a file resource
     *
     * @return  array
     */
    public function getFileStats();

    /**
     * Retrieve the current position of a stream
     *
     * @return  int
     */
    public function getPositionInFile();

    /**
     * Truncate stream
     *
     * @param   int  $newSize
     * @return  bool
     */
    public function truncateFile($newSize);

    /**
     * Write to stream
     *
     * @param   string  $data
     * @return  int
     */
    public function writeFile($data);

    /**
     * Delete a file
     *
     * @param   string  $path
     * @return  bool
     */
    public function removeFile($path);

    /**
     * Retrieve information about a file
     *
     * @param   string  $path
     * @param   int     $flags
     * @return  array
     */
    public function getUrlStats($path, $flags);

    /**
     * Close directory handle
     *
     * @return  bool
     */
    public function closeDirectory();

    /**
     * Open directory handle
     *
     * @param   string  $path
     * @param   int     $options
     * @return  bool
     */
    public function openDirectory($path, $options);

    /**
     * Read entry from directory handle
     *
     * @return  string
     */
    public function readDirectory();

    /**
     * Rewind directory handle
     *
     * @return  bool
     */
    public function rewindDirectory();

    /**
     * Create a directory
     *
     * @param   string  $path
     * @param   int     $mode
     * @param   int     $options
     * @return  bool
     */
    public function makeDirectory($path, $mode, $options);

    /**
     * Removes a directory
     *
     * @param   string  $path
     * @param   int     $options
     * @return  bool
     */
    public function removeDirectory($path, $options);

}

?>