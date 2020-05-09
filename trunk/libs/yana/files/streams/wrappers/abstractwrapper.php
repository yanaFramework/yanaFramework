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

namespace Yana\Files\Streams\Wrappers;

/**
 * <<abstract>> Base class for stream wrappers.
 *
 * Will always throw an exception.
 * This mimics PHP's default behavior that allows you to NOT implement one or more methods,
 * but also throws an error if a method is called that has not been implemented.
 *
 * @package     yana
 * @subpackage  files
 */
abstract class AbstractWrapper extends \Yana\Core\StdObject implements \Yana\Files\Streams\Wrappers\IsStreamWrapper
{

    /**
     * @var  resource
     */
    private $_resource = false;

    /**
     * <<magic>> Maps the method calls.
     *
     * @param   string  $name       method name
     * @param   array   $arguments  method arguments
     * @return  mixed
     */
    public function __call($name, array $arguments)
    {
        $mapper = new \Yana\Files\Streams\Wrappers\NameMapper();
        $mappings = $mapper->getMethodMappings();
        $lowerCasedName = strtolower($name);
        $methodName = (isset($mappings[$lowerCasedName])) ? (string) $mappings[$lowerCasedName] : $name;
        if (\method_exists($this, $methodName)) {
            return \call_user_func_array(array($this, $methodName), $arguments);
        }
        return parent::__call($methodName, $arguments);
    }

    /**
     * <<magic>> Maps the property.
     *
     * @param   string  $name  property name
     * @return  \Yana\Core\StdObject
     */
    public function __get($name)
    {
        $lowerCasedName = strtolower($name);
        if ($lowerCasedName == 'context') {
            return $this->getResource(\STREAM_CAST_AS_STREAM);
        }
        return parent::__get($name);
    }

    /**
     * <<magic>> Maps the property.
     *
     * @param   string  $name   property name
     * @param   mixed   $value  new value
     * @return  mixed
     */
    public function __set($name, $value)
    {
        $lowerCasedName = strtolower($name);
        if ($lowerCasedName == 'context') {
            $this->setResource($value);
            return $value;
        }
        return parent::__set($name, $value);
    }

    /**
     * Renames a file or directory
     *
     * @param   string  $pathFrom
     * @param   string  $pathTo
     * @return  bool
     */
    public function renameFileOrDirectory($pathFrom, $pathTo)
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Set the underlaying resource
     *
     * @param  resource  $resource
     */
    public function setResource($resource)
    {
        $this->_resource = $resource;
    }

    /**
     * Retrieve the underlaying resource
     *
     * @param   int  $castAs
     * @return  resource
     */
    public function getResource($castAs)
    {
        return $this->_resource;
    }

    /**
     * Close an resource
     */
    public function closeFile()
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Tests for end-of-file on a file pointer
     *
     * @return  bool
     */
    public function isEndOfFile()
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Flushes the output
     *
     * @return  bool
     */
    public function flushFileContents()
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Advisory file locking
     *
     * @param   int  $operation
     * @return  bool
     */
    public function lockFile($operation)
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
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
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
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
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Read from stream
     *
     * @param   int  $count
     * @return  string
     */
    public function readFile($count)
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Seeks to specific location in a stream
     *
     * @param   int  $offset
     * @param   int  $whence
     * @return  bool
     */
    public function seekInFile($offset, $whence = SEEK_SET)
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
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
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Retrieve information about a file resource
     *
     * @return  array
     */
    public function getFileStats()
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Retrieve the current position of a stream
     *
     * @return  int
     */
    public function getPositionInFile()
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Truncate stream
     *
     * @param   int  $newSize
     * @return  bool
     */
    public function truncateFile($newSize)
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Write to stream
     *
     * @param   string  $data
     * @return  int
     */
    public function writeFile($data)
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Delete a file
     *
     * @param   string  $path
     * @return  bool
     */
    public function removeFile($path)
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
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
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Close directory handle
     *
     * @return bool
     */
    public function closeDirectory()
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
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
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Read entry from directory handle
     *
     * @return string
     */
    public function readDirectory()
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

    /**
     * Rewind directory handle
     *
     * @return bool
     */
    public function rewindDirectory()
    {
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
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
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
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
        throw new \Yana\Core\Exceptions\NotImplementedException(__METHOD__);
    }

}

?>