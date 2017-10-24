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

namespace Yana\Db\Binaries;

/**
 * Read binary large objects (blobs) from database.
 *
 * Example of usage:
 * <code>
 * $db = \Yana\Application::connect('foo');
 * $id = $db->select('foo.1.foo_file');
 *
 * $file = new \Yana\Db\Blob($id);
 * $file->read();
 *
 * // output file to screen
 * print $file->getContent();
 * // copy file to some destination
 * $file->copy('foo/bar.dat');
 * </code>
 *
 * @package     yana
 * @subpackage  db
 * @since       2.9.2
 */
class File extends \Yana\Files\Readonly
{

    /**
     * @var int
     */
    private $_fileSize = 0;
    /**
     * @var string
     */
    private $_type = 'application/unknown';
    /**
     * @var string
     */
    private $_path = '';

    /**
     * Get path to the resource.
     *
     * Returns the name of the extracted file.
     * If the file has not been extracted, returns the name and path of the archive instead.
     *
     * @return  string
     */
    public function getPath()
    {
        $path = (string) $this->_path;
        if ($path === "") {
            $path = parent::getPath();
        }
        return $path;
    }

    /**
     * Read file contents.
     *
     * Tries to read the blob contents, decompresses it and caches the file attributes like
     * type, size and original filename.
     *
     * @throws  \Yana\Core\Exceptions\NotReadableException  if the blob is not valid
     * @throws  \Yana\Core\Exceptions\NotFoundException     if the blob does not exist
     * @return  self
     */
    public function read()
    {
        $source = $this->getPath();

        if (!is_file($source)) {
            $message = "The file '{$source}' does not exist (database: blob not found).";
            throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::INFO);
        }
        $this->content = array();

        if (!preg_match('/\.gz$/', $source)) {
            $message = "The source '{$source}' is not a valid database blob.";
            throw new \Yana\Core\Exceptions\NotReadableException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
        $i = 0;
        $gz = gzopen($source, 'r');
        while (!gzeof($gz))
        {
            $buffer = gzgets($gz, 4096);
            switch ($i)
            {
                case 0:
                    $buffer = trim($buffer);
                    if (preg_match('/^[\w\.\d\-\_]+$/s', $buffer)) {
                        $this->_path = (string) $buffer;
                    } else {
                        \Yana\Log\LogManager::getLogger()
                            ->addLog("Invalid file path: '{$buffer}'.", \Yana\Log\TypeEnumeration::INFO);
                    }
                break;

                case 1:
                    $buffer = trim($buffer);
                    if (is_numeric($buffer)) {
                        $this->_fileSize = (int) $buffer;
                    } else {
                        \Yana\Log\LogManager::getLogger()
                            ->addLog("Invalid filesize: '{$buffer}'.", \Yana\Log\TypeEnumeration::INFO);
                    }
                break;

                case 2:
                    $buffer = trim($buffer);
                    if (preg_match('/^\w+\/[\w-]+$/s', $buffer)) {
                        $this->_type = (string) $buffer;
                    } else {
                        \Yana\Log\LogManager::getLogger()
                            ->addLog("Invalid MIME-Type: '{$buffer}'.", \Yana\Log\TypeEnumeration::INFO);
                    }
                break;

                default:
                    $this->content[] = $buffer;
                break;

            }
            $i++;
        }
        gzclose($gz);
        return $this;
    }

    /**
     * Get mime-type of this file.
     *
     * Returns bool(false) on error.
     *
     * @return  string|bool(false)
     */
    public function getMimeType()
    {
        return (string) $this->_type;
    }

    /**
     * Get size of this file in bytes.
     *
     * Note: this function may return a cached value.
     *
     * @return  int
     */
    public function _getFilesize()
    {
        return (int) $this->_fileSize;
    }

    /**
     * read the current file id from the session vars
     *
     * Returns the path of a file as stored in the session.
     * Throws an exception if the id is invalid or the file is not found.
     *
     * @param   int   $id        index in files list, of the file to get
     * @param   bool  $fullsize  show full size or thumb-nail (images only)
     * @return  string
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if file with index $id does not exist
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException   if the requested file no longer exists
     */
    public static function getFilenameFromSession($id, $fullsize = false)
    {
        assert('is_int($id); // Wrong type for argument 1. Integer expected');
        assert('is_bool($fullsize); // Wrong type for argument 2. Boolean expected');

        $id = (int) $id;

        /* check arguments */
        if (!isset($_SESSION[__CLASS__][$id])) {
            $message = "Invalid argument. File '$id' is undefined.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        $file = $_SESSION[__CLASS__][$id];

        if (!$fullsize && !preg_match('/\.gz$/', $file)) {
            $mapper = new \Yana\Db\Binaries\FileMapper();
            $id = $mapper->toFileId($id);
            $file = $mapper->toFileName($id, \Yana\Db\Binaries\FileTypeEnumeration::THUMB); // may throw NotFoundException
        }
        if (!is_file($file)) {
            $message = "Database entry exists, but the corresponding file was not found '{$file}'.";
            $code = \Yana\Log\TypeEnumeration::ERROR;
            $error = new \Yana\Core\Exceptions\Files\NotFoundException($message, $code);
            $error->setFilename($file);
            throw $error;
        }
        return $file;
    }

    /**
     * Store filename as session var and return an ID.
     *
     * @param   string  $file
     * @return  string
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  if the given $file does not exist
     */
    public static function storeFilenameInSession($file)
    {
        assert('is_string($file); // Wrong argument type argument 1. String expected');
        if (!is_file($file)) {
            $message = "File was not found '{$file}'.";
            $code = \Yana\Log\TypeEnumeration::ERROR;
            $error = new \Yana\Core\Exceptions\Files\NotFoundException($message, $code);
            $error->setFilename($file);
            throw $error;
        }
        if (!isset($_SESSION[__CLASS__]) || !is_array($_SESSION[__CLASS__])) {
            $_SESSION[__CLASS__] = array();
            $id = false;
        } else {
            $id = array_search($file, $_SESSION[__CLASS__]);
        }
        if ($id === false) {
            $id = array_push($_SESSION[__CLASS__], $file) - 1;
        }
        return $id;
    }

    /**
     * copy the file to some destination
     *
     * @param    string  $destFile   destination to copy the file to
     * @param    bool    $overwrite  setting this to false will prevent existing files from getting overwritten
     * @return   bool
     * @throws   \Yana\Core\Exceptions\InvalidArgumentException  on invalid filename
     */
    public function copy($destFile, $overwrite = true)
    {
        assert('is_string($destFile); // Wrong type for argument 1. String expected');
        assert('is_bool($overwrite); // Wrong type for argument 2. Boolean expected');

        /* validity checking */
        if (mb_strlen($destFile) > 512 || !preg_match('/^[\w\d-_\.][\w\d-_\/\.]*$/', $destFile)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Invalid filename '".$destFile."'.", \Yana\Log\TypeEnumeration::WARNING);
        }

        if (!$overwrite && file_exists($destFile)) {
            \Yana\Log\LogManager::getLogger()->addLog("Unable to copy to file '{$destFile}'. " .
                "Another file with the same name does already exist.");
            return false;
        } elseif ($overwrite && file_exists($destFile) && !is_writeable($destFile)) {
            \Yana\Log\LogManager::getLogger()->addLog("Unable to copy file to '{$destFile}'. Permission denied.");
            return false;
        } else {
            $handle = fopen($destFile, "w+");
            if ($handle === false) {
                return false;
            }
            flock($handle, LOCK_EX);
            if (fwrite($handle, $this->getFileContent()) == false) {
                return false;
            }
            flock($handle, LOCK_UN);
            if (fclose($handle) == false) {
                return false;
            }
            chmod($destFile, 0777);
            return true;
        }
    }

    /**
     * Remove a binary large object from the database.
     *
     * The type of the column must be "file" or "image".
     *
     * IMPORTANT NOTE: This is a low-level function that DOES NOT
     * disassociate the files with the datasets, that reference
     * them.
     *
     * Returns bool(true) on success and bool(false) if the file does not exist.
     *
     * @param   string    $fileToDelete  filename which would be removed
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the given file was not found
     * @since   3.1.0
     * @ignore
     */
    public static function removeFile($fileToDelete)
    {
        assert('is_string($fileToDelete); // Wrong type for argument 1. String expected.');

        if (empty($fileToDelete)) {
            return;
        }

        $mapper = new \Yana\Db\Binaries\FileMapper();
        $id = $mapper->toFileId($fileToDelete);
        $thumbFile = $mapper->toFileName($id, \Yana\Db\Binaries\FileTypeEnumeration::THUMB);

        // error - file does not exist
        if (!is_file($fileToDelete)) {
            throw new \Yana\Core\Exceptions\NotFoundException("File not found: $fileToDelete");
        }

        // delete file
        unlink($fileToDelete);
        assert('!is_file($fileToDelete); // file was not deleted');

        // applies to images only:
        if (is_file($thumbFile)) {
            unlink($thumbFile);
            assert('!is_file($thumbFile); // file was not deleted');
            /* Note: we intentionally (and silently) ignore the case,
             * that an image file exists, but no thumbnail is found.
             */
        }
    }

}

?>