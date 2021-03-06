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
 * <<abstract>> filesystem resource
 *
 * This is an abstract super-class for all filesystem wrappers.
 *
 * For examples: resources may be files of any type, or directories.
 *
 * All subclasses should implement the abstract functions read() and isEmpty().
 * You are also encouraged to re-implement the function __toString().
 *
 * @package     yana
 * @subpackage  files
 */
abstract class AbstractResource extends \Yana\Core\StdObject implements \Yana\Files\IsResource
{

    /**
     * @var  string
     */
    private $_path = "";

    /**
     * @var  int
     */
    private $_fileSize = null;

    /**
     * @var  int
     * @ignore
     */
    private $_lastModified = null;

    /**
     * Create a new instance of this class.
     *
     * @param  string  $filename    filename
     */
    public function __construct($filename)
    {
        assert(is_string($filename), 'Wrong argument type for argument 1. String expected.');
        $this->_setPath((string) $filename);
        $this->_resetStats();
    }

    /**
     * provide string representation
     *
     * You are encouraged to re-implement this in each derived sub-classes to
     * better represent the type of resource.
     *
     * @return  string
     * @ignore
     */
    public function __toString()
    {
        return $this->getPath();
    }

    /**
     * Get path to the resource.
     *
     * Returns a string with the path and name of the currently open resource
     * (directory or file).
     *
     * {@internal
     *
     * Note: this function has been renamed as of  version 2.9 RC3 from
     * "InputStream::getFilename()" for reasons of increased plausibility, as
     * input streams may be files AND directories, even though working on files
     * is more common here.
     * The name "getPath()" was choosen to comply with the attribute of the same
     * name in PHP's directory directory class, which is dir::$path.
     *
     * }}
     *
     * @return  string
     */
    public function getPath(): string
    {
        return $this->_path;
    }

    /**
     * Set path to the resource.
     *
     * @param   string  $path  to resource
     * @return  self
     * @ignore
     */
    protected function _setPath(string $path)
    {
        assert($path > "", 'Argument 1 must not be empty.');
        $this->_path = (string) $path;
        return $this;
    }

    /**
     * check existence
     *
     * Returns true, if the resource exists.
     *
     * @return  bool
     */
    public function exists(): bool
    {
        return file_exists($this->getPath());
    }

    /**
     * check if is writeable
     *
     * Returns true, if the resource is writeable.
     *
     * A resource is "writeable" if:
     * <ul>
     *  <li> it exists </li>
     *  <li> it is accessible </li>
     *  <li> the script has permission to write data to it </li>
     * </ul>
     *
     * Note: this is a security setting for permission handling only.
     * Just because a file is set to be writeable doesn't mean it really is
     * writeable right now.
     *
     * There are some reasons that might prevent you from accessing the file.
     * For example: the file may currently be locked by another program, due to
     * safe mode limitations you might not have permission to access it, or due
     * to limitations to your user group permission to access the file may be
     * denied.
     *
     * Remember that the results of this function are cached.
     * Use the function resetStats() to clear the cache and reload all
     * statistics when needed.
     *
     * @return  bool
     */
    public function isWriteable(): bool
    {
        return is_writeable($this->getPath());
    }

    /**
     * check if is readable
     *
     * Returns true, if the resource is readable.
     *
     * A resource is "readable" if:
     * <ul>
     *  <li> it exists </li>
     *  <li> it is accessible </li>
     *  <li> the script has permission to read data from it </li>
     * </ul>
     *
     * @return  bool
     */
    public function isReadable(): bool
    {
        return $this->exists() && is_readable($this->getPath());
    }

    /**
     * check if is executable
     *
     * Returns true, if the resource is executable.
     *
     * A resource is "executable" if:
     * <ul>
     *  <li> it exists </li>
     *  <li> it is accessible </li>
     *  <li> the "executable" flag is set </li>
     * </ul>
     *
     * Note: just that a resource is set to be "executable", doesn't mean it is
     * a program. It just means that if it were a program, you might use it as
     * an executable resource. This is a security setting for permission
     * handling, esp. on Linux/Unix system.
     * However: the interpretation is system dependent, as e.g. Windows doesn't
     * support this setting. So if a resource is not write-protected on Windows
     * systems, it's always also "executable".
     *
     * @return  bool
     */
    public function isExecutable(): bool
    {
        return $this->exists() && is_executable($this->getPath());
    }

    /**
     * Reset statistics.
     *
     * Reset file stats, e.g. after creating a file that did not exist.
     *
     * @ignore
     */
    protected function _resetStats()
    {
        $this->_fileSize = null;
        $this->_lastModified = null;
        clearstatcache();
    }

    /**
     * Get time when file was last modified.
     *
     * Returns the file MTIME value (from cached value).
     * The result is an UNIX timestamp (UTC). If the file does not exist, this returns 0.
     *
     * @return  int
     */
    public function getLastModified(): int
    {
        if (!isset($this->_lastModified)) {
            if ($this->exists()) {
                $this->_lastModified = filemtime($this->getPath());
            } else {
                $this->_lastModified = 0;
            }
        }
        return $this->_lastModified;
    }

    /**
     * Get cached file-size.
     *
     * Returns the FILESIZE (cached) value.
     * Note! This is the value of the original file WITHOUT the changes you possibly made to
     * the file since then.
     *
     * If the file is new, this returns 0.
     *
     * @return  int
     * @ignore
     */
    protected function _getFilesize(): int
    {
        if (!isset($this->_fileSize)) {
            $this->_fileSize = 0;
            if ($this->exists()) {
                $this->_fileSize = filesize($this->getPath());
            }
        }
        return $this->_fileSize;
    }

    /**
     * Compare with another object.
     *
     * Returns bool(true) if this object and $anotherObject are equal and
     * bool(false) otherwise.
     *
     * Two instances are considered equal if and only if they are both objects
     * of the same class and they both refer to the same filesystem resource.
     *
     * @param   \Yana\Core\IsObject  $anotherObject  another object too compare
     * @return  bool
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        if ($anotherObject instanceof $this) {
            return $this->getPath() === $anotherObject->getPath();
        } else {
            return false;
        }
    }

}

?>