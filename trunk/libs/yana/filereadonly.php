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
 * checked reading of file sources
 *
 * This class adds functionality to compute the checksum
 * of a resource and failsafe reading of resources.
 *
 * Note: This class does not implement methods for writing
 * on files.
 *
 * @access      public
 * @package     yana
 * @subpackage  file_system
 */
class FileReadonly extends \Yana\File\AbstractResource implements \Yana\File\IsReadable
{

    /**
     * MD5 checksum cache
     *
     * @access  private
     * @var string
     * @ignore
     */
    private $checkSum = null;

    /**
     * file content
     *
     * @access  protected
     * @var array
     * @ignore
     */
    protected $content = array();

    /**
     * read file contents
     *
     * Tries to read the file contents and throws an exception on error.
     *
     * @access  public
     * @throws  NotReadableException  if the file is not readable
     * @throws  NotFoundException     if the file does not exist
     */
    public function read()
    {
        if (!$this->exists()) {
            throw new NotFoundException("No such file: '{$this->getPath()}'.", E_USER_NOTICE);
        }
        $content = file_get_contents($this->getPath());
        /**
         * check for success
         *
         * This may fail, if the file was locked by another application.
         * Note that checking for $content === false is not enough, since
         * file_get_contents() does not  report this failure.
         */
        if ($content === false || $this->getFilesize() !== strlen($content)) {
            $message = "File '{$this->getPath()}' is currently not readable.";
            throw new NotReadableException($message, E_USER_NOTICE);
        }
        $this->content = explode("\n", $content);
    }

    /**
     * read file contents
     *
     * Automatically restarts reading if the file-resource
     * is temporarily not available after waiting for 0.5 seconds.
     *
     * The process is aborted if it fails 3 times.
     *
     * @access  public
     * @throws  NotReadableException  if the file is not readable
     * @throws  NotFoundException     if the file does not exist
     */
    public function failSafeRead()
    {
        for ($i = 0; $i < 3; $i++)
        {
            try {
                $this->read();
                return;
            } catch (NotReadableException $e) { // file may be locked temporarily
                sleep(1); // sleep for n seconds (must be an integer >= 1)
            }
        }
        $message = "File '{$this->getPath()}' is currently not readable.";
        throw new NotReadableException($message, E_USER_NOTICE);
    }

    /**
     * get size of this file
     *
     * Returns the size of the file in bytes (from cached value).
     * If an error occurs, bool(false) is returned.
     *
     * @access  public
     * @return  int
     * @since   2.8.5
     */
    public function getFilesize()
    {
        return parent::getFilesize();
    }

    /**
     * return contents of resource
     *
     * Note: The type returned depends on the resource.
     * The default is a string, containing the file's contents as a text.
     *
     * @access  public
     * @return  mixed
     */
    public function getContent()
    {
        return implode("\n", $this->content);
    }

    /**
     * alias of get()
     *
     * @access  public
     * @return  string
     */
    public function __toString()
    {
        if (!$this->exists()) {
            return "File ".$this->getPath()." does not exist\n";
        } elseif ($this->isEmpty()) {
            return "File ".$this->getPath()." is wether empty or not loaded\n";
        } else {
            return $this->getContent();
        }
    }

    /**
     * returns bool(true) if the source is empty or not loaded
     *
     * @access  public
     * @return  bool
     */
    public function isEmpty()
    {
        if (empty($this->content)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * return crc32 checksum for this file
     *
     * The filename parameter became available
     * in version 2.8.5
     *
     * This function has two synopsis. You may decide
     * either to call it statically or based on a current instance.
     * <ul>
     *  <li> $md5 = FileReadonly::getMd5(string $filename) </li>
     *  <li> $md5 = $file->getMd5(); </li>
     * </ul>
     * Both will return a MD5 hash. The first one for the file with the
     * name you provided, the second for the file currently represented
     * by the object $file (where $file is an instance of FileReadonly
     * or a derived sub-class).
     *
     * Note: This function has been renamed in version 2.9 RC3 from
     * "FileReadonly::checksum", to better comply with the
     * framework's naming convention
     *
     * If you prefer a hash value over a checksum, you may want to have
     * a look at {@link FileReadonly::getMd5()} instead.
     *
     * @access  public
     * @param   string  $filename   filename
     * @return  int
     * @name    FileReadonly::getCrc32()
     * @see     FileReadonly::getMd5()
     */
    public function getCrc32($filename = "")
    {
        assert('is_string($filename); // Wrong type for argument 1. String expected');

        if (!empty($filename)) {
            if (!is_file("$filename")) {
                trigger_error("Unable to calculate checksum. The file '{$filename}' does not exist.", E_USER_NOTICE);
                return false;
            }
        } else {
            $filename = $this->getPath();
            assert('is_file($filename); // Expected $filename to be a file, but it does not exist.');
        }

        $source = file_get_contents($filename);

        if (!is_string($source)) {
            return 0;
        } else {
            return crc32($source);
        }
    }

    /**
     * return md5 hash for this file
     *
     * This function calculates and returns the MD5
     * hash-string for a file.
     *
     * Usefull to check whether or not a
     * file has been changed since last access.
     *
     * This function has two synopsis. You may decide
     * either to call it statically or based on a current instance.
     * <ul>
     *  <li> $md5 = FileReadonly::getMd5(string $filename) </li>
     *  <li> $md5 = $file->getMd5(); </li>
     * </ul>
     * Both will return a MD5 hash. The first one for the file with the
     * name you provided, the second for the file currently represented
     * by the object $file (where $file is an instance of
     * FileReadonly or a derived sub-class).
     *
     * Note: results are cached for the current file.
     *
     * Note: This function has been renamed in version 2.9 RC3 from
     * "FileReadonly::md5Checksum", as the MD5 algorithm does
     * not really return a checksum, but a hash value.
     *
     * If you prefer a checksum over a hash value, you may want to have
     * a look at {@link FileReadonly::getCrc32()} instead.
     *
     * @access  public
     * @param   string  $filename  name of file (using current file if left blank)
     * @return  string
     * @since   2.8.5
     * @name    FileReadonly::getMd5()
     * @see     FileReadonly::getCrc32()
     */
    public function getMd5($filename = "")
    {
        assert('is_string($filename); // Wrong type for argument 1. String expected');

        // for static calls
        if (!empty($filename)) {
            if (!is_file("$filename")) {
                $message = "Unable to calculate MD5 hash. The file '{$filename}' does not exist.";
                trigger_error($message, E_USER_NOTICE);
                return false;
            }
            return md5_file($filename);
        }

        if (!isset($this->checkSum)) {
            if (!$this->exists()) {
                return false;
            }
            $this->checkSum = md5_file($this->getPath());
        }

        assert('is_string($this->checkSum); // Unexpected member type for "checkSum". String expected.');
        return $this->checkSum;
    }

    /**
     * reset file statistics
     *
     * Reset file stats, e.g. after creating a file that did not exist.
     *
     * @access  protected
     * @ignore
     */
    protected function resetStats()
    {
        parent::resetStats();
        $this->checkSum = null;
    }

}

?>