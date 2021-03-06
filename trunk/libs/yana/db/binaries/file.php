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
    public function getPath(): string
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
     * @return  $this
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
                    if (preg_match('/^\w+\/[\w\-]+$/s', $buffer)) {
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
     * Default is 'application/unknown'.
     *
     * @return  string
     */
    public function getMimeType(): string
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
    public function getFilesize(): int
    {
        return (int) $this->_fileSize;
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
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when the given file was not found
     * @since   3.1.0
     */
    public static function removeFile(string $fileToDelete)
    {
        assert($fileToDelete !== "", 'Invalid argument 1. Filename cannot be empty.');

        $mapper = static::_createMapper();
        $id = $mapper->toFileId($fileToDelete);
        $thumbFile = $mapper->toFileName($id, \Yana\Db\Binaries\FileTypeEnumeration::THUMB);

        // error - file does not exist
        if (!is_file($fileToDelete)) {
            $error = new \Yana\Core\Exceptions\Files\NotFoundException("File not found", \Yana\Log\TypeEnumeration::WARNING);
            $error->setFilename($fileToDelete);
            throw $error;
        }

        // delete file
        unlink($fileToDelete);
        assert(!is_file($fileToDelete), 'file was not deleted');

        // applies to images only:
        if (is_file($thumbFile)) {
            unlink($thumbFile);
            assert(!is_file($thumbFile), 'file was not deleted');
            /* Note: we intentionally (and silently) ignore the case,
             * that an image file exists, but no thumbnail is found.
             */
        }
    }

    /**
     * Creates and returns an instance of FileMapper.
     *
     * @return  \Yana\Db\Binaries\IsFileMapper
     */
    protected static function _createMapper(): \Yana\Db\Binaries\IsFileMapper
    {
        return new \Yana\Db\Binaries\FileMapper();
    }

}

?>