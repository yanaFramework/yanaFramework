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
 * Checked reading and writing of file sources.
 *
 * This class adds functionality to write changes
 * on the object to the corresponding file.
 *
 * @package     yana
 * @subpackage  files
 */
class File extends \Yana\Files\Readonly implements \Yana\Files\IsWritable
{

    /**
     * Read file contents.
     *
     * Tries to read the file contents.
     * Additionaly reads and caches the file attributes.
     *
     * @throws  \Yana\Core\Exceptions\NotReadableException  if the file is not readable
     * @throws  \Yana\Core\Exceptions\NotFoundException     if the file does not exist
     * @return  $this
     */
    public function read()
    {
        parent::read();
        // init cache so we are able to check later, if the file was modified
        $this->getLastModified();
        return $this;
    }

    /**
     * Write file to system.
     *
     * This function will return bool(true) on success.
     * It issues an E_USER_NOTICE and returns bool(false) on error.
     *
     * @throws  \Yana\Core\Exceptions\Files\NotWriteableException  when file does not exist or is not writeable
     * @throws  \Yana\Core\Exceptions\Files\UncleanWriteException  when the file was changed by a third party
     */
    public function write()
    {
        assert(is_array($this->content), 'Member "content" has illegal type. Array expected.');
        $this->content = (array) $this->content;

        if (!$this->isWriteable()) {
            $message = "Unable to write to file '" . $this->getPath() . "'.";
            $e = new \Yana\Core\Exceptions\Files\NotWriteableException($message, \Yana\Log\TypeEnumeration::INFO);
            $e->setFilename($this->getPath());
            throw $e;
        }
        clearstatcache(); // clear cache - otherwise we won't recognize if file was modified
        if ($this->getLastModified() != filemtime($this->getPath())) {
            $message = "Unable to write to file '" . $this->getPath() . "'.\n\t\t".
                "The file has been changed by some third party recently. " .
                "Your cached copy is out of date.";
            $e = new \Yana\Core\Exceptions\Files\UncleanWriteException($message, \Yana\Log\TypeEnumeration::INFO);
            $e->setFilename($this->getPath());
            throw $e;
        }
        $handle = fopen($this->getPath(), "w+");
        flock($handle, LOCK_EX);
        fwrite($handle, $this->getContent());
        flock($handle, LOCK_UN);
        fclose($handle);
        $this->_resetStats();
    }

    /**
     * Delete this file.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\Files\NotWriteableException  when the file is not writeable
     */
    public function delete()
    {
        if (!$this->exists()) {
            return true; // By definition, a non-existing file is deleted.
        }
        if (!$this->isWriteable() || !@unlink($this->getPath())) {
            $message = "Unable to delete file '" . $this->getPath() . "'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $e = new \Yana\Core\Exceptions\Files\NotWriteableException($message, $level);
            $e->setFilename($this->getPath());
            throw $e;
        }
        return true;
    }

    /**
     * Fail-safe writing of data.
     *
     * Automatically restarts writing if the file-resource
     * is temporarily not available after waiting for 0.5 seconds.
     *
     * The process is aborted if it failed 3 times.
     *
     * @return  bool
     */
    public function failSafeWrite()
    {
        for ($i = 0; $i < 3; $i++)
        {
            try {
                $this->write();
                return true;
                // @codeCoverageIgnoreStart

            } catch (\Exception $e) {
                sleep(0.7);
                unset($e);
            }
        }
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Create the current file if it does not exist.
     *
     * @throws  \Yana\Core\Exceptions\Files\AlreadyExistsException  when target does already exist
     * @throws  \Yana\Core\Exceptions\Files\NotWriteableException   when unable to create file
     */
    public function create()
    {
        assert(!isset($path), 'cannot redeclare variable $path');
        $path = $this->getPath();
        if ($this->exists()) {
            $message = "Unable to create file '{$path}'. " .
                "Another file with the same name already exists.";
            $exception = new \Yana\Core\Exceptions\Files\AlreadyExistsException($message, \Yana\Log\TypeEnumeration::INFO);
            $exception->setFilename($path);
            throw $exception;
        }
        if (!@touch($path)) { // touch() issues an E_WARNING that would prevent unit tests from reaching the following block
            $message = "Unable to create file '{$path}'. Target not writeable.";
            $exception = new \Yana\Core\Exceptions\Files\NotWriteableException($message, \Yana\Log\TypeEnumeration::WARNING);
            $exception->setFilename($path);
            throw $exception;
        }
        chmod($path, 0777);
        $this->_resetStats();
    }

    /**
     * Reload file contents.
     *
     * You accidently did something wrong with the file?
     * Calling this will reload the file from disk while
     * reseting its current state.
     *
     * @throws  \Yana\Core\Exceptions\NotReadableException  if the file is not readable
     */
    public function reset()
    {
        try {
            $this->read();
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            // ignore if file does not exist
        }
        // does not catch NotReadableException
    }

    /**
     * Copy the file to some destination.
     *
     * This will create a copy of this file on the filesystem.
     * Bool(true) will be returned on success and bool(false)
     * on error.
     *
     * Possible errors are:
     * <ul>
     *  <li> the destination directory does not exist (and $isRecursive is not
     *        provided, or set to false)  </li>
     *  <li> another file with the same name does already exist (and $overwrite
     *       is set to false)  </li>
     *  <li> the destination file is not accessible for some other reason  </li>
     * </ul>
     *
     * If the directory the desination file would be place in
     * does not exist, and $isRecursive is set to true,
     * it will automatically, recursively create the missing directories.
     * Newly created directories will be set to access restriction 0766.
     * Note that this is unlike the default behaviour, where there is no
     * restriction at all.
     *
     * Note: instead of a file name in parameter $destFile, you may also provide
     * the name of a directory.
     * In this case the file the current name of the file will be used.
     * The destination directory needs to be terminated by a forward-slash '/'.
     *
     * The $destFile parameter must not be any longer than 512 characters.
     * It must not contain any but alphanumeric characters.
     *
     * If you copy a PHP file, note that for security reasons the $mode
     * parameter defaults to 0766, which means the file will not be executable
     * in a UNIX environment (while it may be executable on windows platforms)
     * until you set the $mode parameter to 0777.
     *
     * @param    string   $destFile     destination to copy the file to
     * @param    bool     $overwrite    setting this to false will prevent existing files from getting overwritten
     * @param    bool     $isRecursive  setting this to true will automatically, recursively create directories
     *                                  in the $destFile string, if required
     * @param    int      $mode         the access restriction that applies to the copied file, defaults to 0766
     * @throws   \Yana\Core\Exceptions\InvalidArgumentException      when one input argument is invalid
     * @throws   \Yana\Core\Exceptions\Files\AlreadyExistsException  if the target file already exists
     * @throws   \Yana\Core\Exceptions\Files\NotWriteableException   if the target location is not writeable
     * @throws   \Yana\Core\Exceptions\Files\NotFoundException       if the target directory does not exist
     */
    public function copy(string $destFile, bool $overwrite = true, bool $isRecursive = false, int $mode = 0766)
    {
        if ($mode > 0777 || $mode < 1) {
            $message = "Argument mode must be an octal number in range: [1,0777].";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        /* validity checking */
        if (empty($destFile) || strlen($destFile) > 512) {
            $message = "Invalid filename '$destFile'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }

        $destFileIsDirectory = preg_match('/[\/\\\\]$/s', $destFile);
        $destDir  = $destFileIsDirectory ? $destFile : dirname($destFile) . '/';
        $destFile = $destFileIsDirectory ? basename($this->getPath()) : basename($destFile);

        /* check if file already exists */
        if (file_exists($destDir . $destFile)) {
            if ($overwrite === false) {
                $message = "Unable to copy file '{$destDir}{$destFile}'. " .
                    "Another file with the same name does already exist.";
                $exception = new \Yana\Core\Exceptions\Files\AlreadyExistsException($message, \Yana\Log\TypeEnumeration::INFO);
                $exception->setFilename($destDir . $destFile);
                throw $exception;
            } elseif ($overwrite === true && is_writeable($destDir . $destFile) === false) {
                $message = "Unable to copy to file '{$destDir}{$destFile}'. ".
                    "The file does already exist and is not writeable.";
                $exception = new \Yana\Core\Exceptions\Files\NotWriteableException($message, \Yana\Log\TypeEnumeration::INFO);
                $exception->setFilename($destDir . $destFile);
                throw $exception;
            }
        }

        assert(is_string($destDir), 'Unexpected result: $destDir. String expected.');
        assert(is_string($destFile), 'Unexpected result: $destFile. String expected.');

        /* recursively create directories */
        if (!empty($destDir) && !is_dir($destDir)) {
            if (!$isRecursive) {
                $message = "Unable to copy file '{$destFile}'. The directory '{$destDir}' does not exist.";
                $exception = new \Yana\Core\Exceptions\Files\NotFoundException($message, \Yana\Log\TypeEnumeration::INFO);
                $exception->setFilename($destDir . $destFile);
                throw $exception;
            }
            assert(!isset($currentDir), 'cannot redeclare variable $currentDir');
            $currentDir = '';
            assert(!isset($current), 'cannot redeclare variable $current');
            assert(!isset($dir), 'cannot redeclare variable $dir');
            foreach (explode('/', $destDir) as $dir)
            {
                if (!is_dir($currentDir . $dir)) {
                    $current = new \Yana\Files\Dir($currentDir . $dir);
                    $current->create($mode);
                }
                $currentDir .= $dir.'/';
            } /* end foreach */
            unset($dir,$current);
        }

        if (!copy($this->getPath(), $destDir . $destFile)) {
            $message = "Unable to copy file. The target '{$destDir}{$destFile}' is not writeable.";
            $exception = new \Yana\Core\Exceptions\Files\NotWriteableException($message, \Yana\Log\TypeEnumeration::WARNING);
            $exception->setFilename($destDir . $destFile);
            throw $exception;
        }
        if (chmod($destDir . $destFile, $mode) === false) {
            // @codeCoverageIgnoreStart
            \Yana\Log\LogManager::getLogger()->addLog("Unable to set mode (access level) for file '{$destDir}{$destFile}'.");
            // @codeCoverageIgnoreEnd
        }
    }

}

?>