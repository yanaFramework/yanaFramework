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
 * <<mapper>> This class provides mapping between internal and external API names.
 *
 * @package     yana
 * @subpackage  files
 */
class NameMapper extends \StdClass implements \Yana\Files\Streams\Wrappers\IsStreamBase
{

    /**
     * @var  array
     */
    private static $_mappings = array();

    /**
     * Returns the name mappings of method names between internal and external API.
     *
     * An array is returned where the external name is given as a key and the internal name as value.
     *
     * @return  array
     */
    public static function getMethodMappings()
    {
        if (empty(self::$_mappings)) {
            $object = new self();
            $reflection = new \ReflectionObject($object);
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method)
            {
                /* @var $method \ReflectionMethod */
                $internalName = $method->getName();
                if ($internalName !== __FUNCTION__) {
                    $externalName = $method->invoke($object);
                    self::$_mappings[$externalName] = $internalName;
                }
            }
        }
        return self::$_mappings;
    }

    /**
     * Retrieve the underlaying resource
     *
     * @param   int  $castAs  dummy
     * @return  string
     */
    public function getResource($castAs = null)
    {
        return 'stream_cast';
    }

    /**
     * Renames a file or directory
     *
     * @param   string  $pathFrom  dummy
     * @param   string  $pathTo    dummy
     * @return  string
     */
    public function renameFileOrDirectory($pathFrom = null, $pathTo = null)
    {
        return 'rename';
    }

    /**
     * Close an resource
     *
     * @return  string
     */
    public function closeFile()
    {
        return 'stream_close';
    }

    /**
     * Tests for end-of-file on a file pointer
     *
     * @return  string
     */
    public function isEndOfFile()
    {
        return 'stream_eof';
    }

    /**
     * Flushes the output
     *
     * @return  string
     */
    public function flushFileContents()
    {
        return 'stream_flush';
    }

    /**
     * Advisory file locking
     *
     * @param   int  $operation  dummy
     * @return  string
     */
    public function lockFile($operation = null)
    {
        return 'stream_lock';
    }

    /**
     * Change stream options
     *
     * @param   string  $path     dummy
     * @param   int     $options  dummy
     * @param   int     $var      dummy
     * @return  string
     */
    public function setMetaData($path = null, $options = null, $var = null)
    {
        return 'stream_metadata';
    }

    /**
     * Opens file or URL
     *
     * @param   string  $path          dummy
     * @param   string  $mode          dummy
     * @param   int     $options       dummy
     * @param   string  &$opened_path  dummy
     * @return  string
     */
    public function openFile($path = null, $mode = null, $options = null, &$openedPath = null)
    {
        return 'stream_open';
    }

    /**
     * Read from stream
     *
     * @param   int  $count  dummy
     * @return  string
     */
    public function readFile($count = null)
    {
        return 'stream_read';
    }

    /**
     * Seeks to specific location in a stream
     *
     * @param   int  $offset  dummy
     * @param   int  $whence  dummy
     * @return  string
     */
    public function seekInFile($offset = null, $whence = null)
    {
        return 'stream_seek';
    }

    /**
     * Change stream options
     *
     * @param   int  $option  dummy
     * @param   int  $arg1    dummy
     * @param   int  $arg2    dummy
     * @return  string
     */
    public function setOption($option = null, $arg1 = null, $arg2 = null)
    {
        return 'stream_set_option';
    }

    /**
     * Retrieve information about a file resource
     *
     * @return  string
     */
    public function getFileStats()
    {
        return 'stream_stat';
    }

    /**
     * Retrieve the current position of a stream
     *
     * @return  string
     */
    public function getPositionInFile()
    {
        return 'stream_tell';
    }

    /**
     * Truncate stream
     *
     * @param   int  $newSize  dummy
     * @return  string
     */
    public function truncateFile($newSize = null)
    {
        return 'stream_truncate';
    }

    /**
     * Write to stream
     *
     * @param   string  $data  dummy
     * @return  string
     */
    public function writeFile($data = null)
    {
        return 'stream_write';
    }

    /**
     * Delete a file
     *
     * @param   string  $path  dummy
     * @return  string
     */
    public function removeFile($path = null)
    {
        return 'unlink';
    }

    /**
     * Retrieve information about a file
     *
     * @param   string  $path   dummy
     * @param   int     $flags  dummy
     * @return  string
     */
    public function getUrlStats($path = null, $flags = null)
    {
        return 'url_stat';
    }

    /**
     * Close directory handle
     *
     * @return  string
     */
    public function closeDirectory()
    {
        return 'dir_closedir';
    }

    /**
     * Open directory handle
     *
     * @param   string  $path     dummy
     * @param   int     $options  dummy
     * @return  string
     */
    public function openDirectory($path = null, $options = null)
    {
        return 'dir_opendir';
    }

    /**
     * Read entry from directory handle
     *
     * @return string
     */
    public function readDirectory()
    {
        return 'dir_readdir';
    }

    /**
     * Rewind directory handle
     *
     * @return  string
     */
    public function rewindDirectory()
    {
        return 'dir_rewinddir';
    }

    /**
     * Create a directory
     *
     * @param   string  $path     dummy
     * @param   int     $mode     dummy
     * @param   int     $options  dummy
     * @return  string
     */
    public function makeDirectory($path = null, $mode = null, $options = null)
    {
        return 'mkdir';
    }

    /**
     * Removes a directory
     *
     * @param   string  $path     dummy
     * @param   int     $options  dummy
     * @return  string
     */
    public function removeDirectory($path = null, $options = null)
    {
        return 'rmdir';
    }

}

?>