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
    private static $_content = "";

    /**
     * @var  int
     */
    private static $_position = 0;

    /**
     * @var  array
     */
    private static $_directories = array();

    /**
     * Directory that is currently open for iteration.
     *
     * @var  string
     */
    private static $_directoryName = "";

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
        self::$_content = "";
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
     * @param   string  &$openedPath
     * @return  bool
     */
    public function openFile($path, $mode, $options, &$openedPath)
    {
        $dirname = \dirname($path);
        $filename = \basename($path);
        $this->makeDirectory($dirname, $mode, $options);
        if (!\in_array($filename, self::$_directories[$dirname])) {
            self::$_directories[$dirname][] = $filename;
        }
        $openedPath = $path;
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
        $content = \substr(self::$_content, self::$_position, $count);
        self::$_position += $count;
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
        return self::$_position;
    }

    /**
     * Truncate stream
     *
     * @param   int  $newSize  truncate to this file size
     * @return  bool
     */
    public function truncateFile($newSize)
    {
        self::$_position = 0;
        self::$_content = $this->readFile($newSize);
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
        self::$_position = 0;
        self::$_content = $data;
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
        $path = preg_replace('/\/$/', '', $path);
        $isDirectory = isset(self::$_directories[$path]);
        $mode = ($isDirectory) ? 0040000 : 0100000;
        $mode = $mode | 0000400 | 0000200 | 0000100;
        $size = ($isDirectory) ? count(self::$_directories[$path]) : \strlen(self::$_content);
        $time = time();
        return array(
            'dev' => 1,
            'ino' => 0,
            'mode' => $mode,
            'nlink' => 0,
            'uid' => 0,
            'gid' => 0,
            'rdev' => 0,
            'size' => $size,
            'atime' => $time,
            'mtime' => $time,
            'ctime' => $time,
            'blksize' => -1,
            'blocks' => -1
        );
    }

    /**
     * Close directory handle
     *
     * @return bool
     */
    public function closeDirectory()
    {
        self::$_directoryName = "";
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
        $path = preg_replace('/\/$/', '', $path);
        $isDirectory = isset(self::$_directories[$path]);
        if ($isDirectory) {
            self::$_directoryName = $path;
            reset(self::$_directories[$path]);
        }
        return $isDirectory;
    }

    /**
     * Read entry from directory handle
     *
     * @return string
     */
    public function readDirectory()
    {
        $currentItem = false;
        if (isset(self::$_directories[self::$_directoryName])) {
            $currentItem = current(self::$_directories[self::$_directoryName]);
            next(self::$_directories[self::$_directoryName]);
        }
        return $currentItem;
    }

    /**
     * Rewind directory handle
     *
     * @return bool
     */
    public function rewindDirectory()
    {
        $isDirectory = isset(self::$_directories[self::$_directoryName]);
        if ($isDirectory) {
            reset(self::$_directories[self::$_directoryName]);
        }
        return $isDirectory;
    }

    /**
     * Create a directory
     *
     * @param string $path
     * @param int $mode
     * @param int $options
     * @return bool
     */
    public function makeDirectory($path, $mode, $options)
    {
        $path = preg_replace('/\/$/', '', $path);
        $isNoDirectory = !isset(self::$_directories[$path]);
        if ($isNoDirectory) {
            self::$_directories[$path] = array();
            \clearstatcache(false, $path); // required or otherwise is_dir() will still return FALSE.
        }
        return $isNoDirectory;
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
        $path = preg_replace('/\/$/', '', $path);
        $isDirectory = isset(self::$_directories[$path]);
        if ($isDirectory) {
            unset(self::$_directories[$path]);
            \clearstatcache(false, $path); // required or otherwise is_dir() will still return TRUE.
        }
        return $isDirectory;
    }

}

?>