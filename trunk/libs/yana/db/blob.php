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

namespace Yana\Db;

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
class Blob extends \Yana\Files\Readonly
{

    /**
     * @var int
     */
    private $_size = 0;
    /**
     * @var string
     */
    private $_type = 'application/unknown';

    /**
     * @var  string
     * @ignore
     */
    protected static $blobDir = 'config/db/.blob/';

    /**
     * Read file contents.
     *
     * Tries to read the blob contents, decompresses it and caches the file attributes like
     * type, size and original filename.
     *
     * @throws  \Yana\Core\Exceptions\NotReadableException  if the blob is not valid
     * @throws  \Yana\Core\Exceptions\NotFoundException     if the blob does not exist
     */
    public function read()
    {
        $source = $this->getPath();

        if (!is_file($source)) {
            $message = "The file '{$source}' does not exist (database: blob not found).";
            throw new \Yana\Core\Exceptions\NotFoundException($message, E_USER_NOTICE);
        }
        $this->content = array();

        if (!preg_match('/\.gz$/', $source)) {
            $message = "The source '{$source}' is not a valid database blob.";
            throw new \Yana\Core\Exceptions\NotReadableException($message, E_USER_WARNING);
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
                        $this->path = $buffer;
                    } else {
                        trigger_error("Invalid file path: '{$buffer}'.", E_USER_NOTICE);
                    }
                break;

                case 1:
                    $buffer = trim($buffer);
                    if (is_numeric($buffer)) {
                        $this->_size = $buffer;
                    } else {
                        trigger_error("Invalid filesize: '{$buffer}'.", E_USER_NOTICE);
                    }
                break;

                case 2:
                    $buffer = trim($buffer);
                    if (preg_match('/^\w+\/[\w-]+$/s', $buffer)) {
                        $this->_type = $buffer;
                    } else {
                        trigger_error("Invalid MIME-Type: '{$buffer}'.", E_USER_NOTICE);
                    }
                break;

                default:
                    $this->content[] = $buffer;
                break;

            }
            $i++;
        }
        gzclose($gz);
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
        if (isset($this->_type)) {
            return $this->_type;
        } else {
            return false;
        }
    }

    /**
     * Get size of this file in bytes.
     *
     * Note: this function may return a cached value.
     * If an error occurs, bool(false) is returned.
     *
     * @return  int
     */
    public function getFilesize()
    {
        if (isset($this->_size)) {
            return $this->_size;
        } else {
            return false;
        }
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
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException        if the requested file no longer exists
     */
    public static function getFilenameFromSession($id, $fullsize = false)
    {
        assert('is_int($id); // Wrong type for argument 1. Integer expected');
        assert('is_bool($fullsize); // Wrong type for argument 2. Boolean expected');

        $id = (int) $id;

        /* check arguments */
        if (!isset($_SESSION[__CLASS__][$id])) {
            $message = "Invalid argument. File '$id' is undefined.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }

        $file = $_SESSION[__CLASS__][$id];

        if (!$fullsize && !preg_match('/\.gz$/', $file)) {
            $id = self::getFileIdFromFilename($file);
            $file = self::getThumbnailFromFileId($id);
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
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Invalid filename '".$destFile."'.", E_USER_WARNING);
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
     * Create unique id to identify a file.
     *
     * @param   \Yana\Db\Ddl\Column  $column  column definition
     * @return  string
     * @ignore
     */
    public static function getNewFileId(\Yana\Db\Ddl\Column $column)
    {
        $table = $column->getParent();
        $tableName = "";
        if ($table instanceof \Yana\Db\Ddl\Table) {
            $tableName = $table->getName();
        }
        $columnName = $column->getName();
        return md5(uniqid("$tableName.$columnName.", true));
    }

    /**
     * Extract unique file-id from a database value.
     *
     * For any given path like "path/file.extension" this returns "file".
     * 
     * @internal Note: for "path/file.ext1.ext2" this returns "ext1". (Remember this for "file.tar.gz")
     *
     * @param   string  $filename  expected to be path/file.extension
     * @return  string
     * @ignore
     */
    public static function getFileIdFromFilename($filename)
    {
        assert('is_string($filename); // Wrong argument type for argument 1. String expected');
        return preg_replace('/^.*?([\w-_]+)\.\w+$/', '$1', $filename);
    }

    /**
     * Get matching filename for a given id.
     *
     * @param   string  $id    file id
     * @param   bool    $type  file type (image or file, leave blank for auto-detect)
     * @return  string
     */
    public static function getFilenameFromFileId($id, $type = '')
    {
        assert('is_string($id); // Invalid argument $id: string expected');
        assert('is_string($type); // Invalid argument $type: string expected');

        $file = self::getDirectory() . $id;
        switch ($type)
        {
            case 'image':
                $file .= '.png';
            break;
            case 'file':
                $file .= '.gz';
            break;
        }
        if (is_file($file)) {
            return $file;
        } else {
            foreach (glob(self::getDirectory() . $id . '.*') as $filename)
            {
                return $filename;
            }
            \Yana\Log\LogManager::getLogger()->addLog('Invalid database entry. File not found: ' . $id);
            return "";
        }
    }

    /**
     * Returns the path to the file with the given id.
     *
     * Note: this does not check if the file exists.
     *
     * @param   string  $id  file id
     * @return  string
     */
    public static function getThumbnailFromFileId($id)
    {
        assert('is_string($id); // Wrong argument type argument 1. String expected');
        return self::getDirectory() . "thumb.{$id}.png";
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
        assert('is_string($fileToDelete);  // Wrong type for argument 1. String expected.');

        if (empty($fileToDelete)) {
            return;
        }

        $id = self::getFileIdFromFilename($fileToDelete);
        $thumbFile = self::getThumbnailFromFileId($id);

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

    /**
     * Returns path to directory where blob-files are stored.
     *
     * @return  string
     */
    public static function getDirectory()
    {
        assert('is_dir(self::$blobDir); // Blob-dir does not exist');
        return self::$blobDir;
    }

    /**
     * Set path to directory where blob-files are stored.
     * 
     * @param  string  $directory
     */
    public static function setDirectory($directory)
    {
        assert('is_dir($directory); // Directory does not exist');
        self::$blobDir = realpath($directory) . DIRECTORY_SEPARATOR;
    }

    /**
     * Sanitize file id.
     *
     * For file upload error handling see the following example:
     * <code>
     * try {
     *     $filename = self::_sanitizeFile($_FILES['my_file']);
     *     move_uploaded_file($filename, "upload/foo.bar"));
     * } catch (\Exception $e) {
     *     switch ($e->getCode())
     *     {
     *        case UPLOAD_ERR_INI_SIZE:
     *            exit('File exceeds upload_max_filesize in php.ini');
     *        break;
     *        case UPLOAD_ERR_FORM_SIZE:
     *            exit('File bigger than 1000000 bytes');
     *        break;
     *        case UPLOAD_ERR_PARTIAL:
     *            exit('File was only partially uploaded');
     *        break;
     *        case UPLOAD_ERR_NO_FILE:
     *            exit('No file was uploaded');
     *        break;
     *        case UPLOAD_ERR_NO_TMP_DIR:
     *            exit('Missing a temporary folder');
     *        break;
     *        case UPLOAD_ERR_CANT_WRITE:
     *            exit('Failed to write file to disk');
     *        break;
     *        case UPLOAD_ERR_EXTENSION:
     *            exit('File upload stopped by extension');
     *        break;
     *        case UPLOAD_ERR_FILE_TYPE:
     *            exit('uploaded file is not a recognized image');
     *        break;
     *        case UPLOAD_ERR_OTHER:
     *            exit('misc. (unexpected) errors');
     *        break;
     *     }
     * }
     * </code>
     *
     * See the PHP manual for more details on these codes.
     *
     * @param   array  $file  item taken from $_FILES array
     * @return  string
     * @throws  \Yana\Core\Exceptions\Files\NotWriteableException on UPLOAD_ERR_INVALID_TARGET and UPLOAD_ERR_OTHER
     * @throws  \Yana\Core\Exceptions\Files\UploadFailedException on UPLOAD_ERR_FILE_TYPE
     * @throws  \Yana\Core\Exceptions\Files\SizeException         on UPLOAD_ERR_FORM_SIZE, UPLOAD_ERR_SIZE, UPLOAD_ERR_FORM_SIZE
     */
    private static function _getTempName(array $file)
    {
        // get original filename (for reporting purposes only)
        $filename = self::_getOriginalName($file);
        // check error state
        if (!empty($file['error'])) {
            // check type of error
            switch ($file['error'])
            {
                case UPLOAD_ERR_OK:
                    // all fine - proceed!
                break;
                case UPLOAD_ERR_SIZE:
                case UPLOAD_ERR_INI_SIZE:
                    $maxSize = ini_get("upload_max_filesize");
                case UPLOAD_ERR_FORM_SIZE:
                    if (!isset($maxSize)) {
                        $maxSize = (int) $_POST['MAX_FILE_SIZE'];
                    }
                    $message = "Uploaded file exceeds maximum size.";
                    $alert = new \Yana\Core\Exceptions\Files\SizeException($message, $file['error']);
                    throw $alert->setFilename($filename)->setMaxSize($maxSize);
                break;

                case UPLOAD_ERR_FILE_TYPE:
                    $message = "Uploaded file has a file type that is either not recognized or not permitted.";
                    $error = new \Yana\Core\Exceptions\Files\UploadFailedException($message, UPLOAD_ERR_FILE_TYPE);
                    throw $error->setFilename($filename);
                break;

                case UPLOAD_ERR_INVALID_TARGET:
                    $message = "Unable to write uploaded file '{$filename}'.";
                    \Yana\Log\LogManager::getLogger()->addLog($message);
                    $error = new \Yana\Core\Exceptions\Files\NotWriteableException($message, UPLOAD_ERR_INVALID_TARGET);
                    throw $error->setFilename($filename);
                break;
                case UPLOAD_ERR_OTHER:
                default:
                    $message = "Unable to write uploaded file '{$filename}'.";
                    \Yana\Log\LogManager::getLogger()->addLog($message);
                    $error = new \Yana\Core\Exceptions\Files\NotWriteableException($message, $file['error']);
                    throw $error->setFilename($filename);
                break;
            } // end switch (error code)
        }

        return $file['tmp_name'];
    }

    /**
     * Get original filename.
     *
     * @param   array  $file  item taken from $_FILES array
     * @return  string
     */
    private static function _getOriginalName(array $file)
    {
        return $file['name'];
    }

    /**
     * handle file uploads
     *
     * The purpose of this method is to handle an uploaded file.
     * If the file already exists, it will get replaced.
     *
     * Example of usage:
     *
     * HTML-Code:
     * <code>
     * <form method="POST" action="{$PHP_SELF}" enctype="multipart/form-data">
     * ...
     * Upload file: <input type="file" name="my_file" />
     * ...
     * </form>
     * </code>
     *
     * PHP-Code:
     * <code>
     * // get a random filename
     * $column = $db->schema->{'my_table'}->{'my_file'};
     * $fileId = \Yana\Db\Blob::getNewFileId($column);
     * // and assign it to your row to update/insert
     * $row['my_file'] = $fileId;
     *
     * // upload the file
     * $filename = \Yana\Db\Blob::uploadFile($_FILES['my_file'], $fileId);
     * // and update/insert the row as usual
     * $db->insert("my_table", $row);
     * </code>
     *
     * For images use {@see \Yana\Db\Blob::uploadImage()}.
     *
     * @param   array   $file    item taken from array $_FILES
     * @param   string  $fileId  name of target file
     * @return  string
     * @ignore
     */
    public static function uploadFile(array $file, $fileId)
    {
        assert('is_string($fileId); // Wrong argument type for argument 3. String expected');

        assert('!isset($dir); // Cannot redeclare var $dir');
        $dir = self::getDirectory();

        $fileTempName = self::_getTempName($file);
        $filename = self::_getOriginalName($file);

        /* handle errors */
        if (!is_file($fileTempName) || !is_readable($fileTempName)) {
            $message = "Uploaded file is not readable.";
            $code = \Yana\Log\TypeEnumeration::INFO;
            $error = new \Yana\Core\Exceptions\Files\UploadFailedException($message, $code);
            throw $error->setFilename($filename);

        } /* end error handling */

        /* name of output file */
        $path = "{$dir}/{$fileId}.gz";
        assert('is_string($path); // Wrong argument type for argument 2. String expected');

        /*
         * mime-type
         *
         * The Mime-type is saved, so it may be sent to the client on download.
         */
        assert('!isset($mimetype); // Cannot redeclare var $mimetype');
        if (!empty($file['type'])) {
            $mimetype = $file['type'];
            $mimetype = preg_replace('/\s/', ' ', $mimetype);
        }

        /*
         * create zip file
         */
        assert('!isset($gz); // Cannot redeclare var $gz');
        $gz = gzopen($path, 'w9');
        /*
         * insert Header information
         *
         * 1) original filename
         * 2) original size
         * 3) original mime-type
         *
         * Note: line break "\n" is used as a delimiter
         */
        gzwrite($gz, "$filename\n");
        gzwrite($gz, filesize($fileTempName)."\n");
        gzwrite($gz, "$mimetype\n");
        /*
         * save file contents
         */
        gzwrite($gz, file_get_contents($fileTempName));
        gzclose($gz);
        return $filename;
    }

    /**
     * handle image uploads
     *
     * The purpose of this method is to handle an uploaded images.
     * If the file already exists, it will get replaced.
     *
     * PHP-Code:
     * <code>
     * // get a random filename
     * $column = $db->schema->{'my_table'}->{'my_file'};
     * $fileId = \Yana\Db\Blob::getNewFileId($column);
     * // and assign it to your row to update/insert
     * $row['my_file'] = $fileId;
     *
     * // get image/thumbnail settings
     * $settings = $column->getImageSettings();
     *
     * // upload the image
     * $filename = \Yana\Db\Blob::uploadImage($_FILES['my_file'], $fileId, $settings);
     * // and update/insert the row as usual
     * $db->insert("my_table", $row);
     * </code>
     *
     * For other types of files use {@see \Yana\Db\Blob::uploadFile()}.
     *
     * @param   array   $file      item taken from array $_FILES
     * @param   string  $fileId    name of target file
     * @param   array   $settings  int    width       image width in pixel,
     *                             int    height      image height in pixel,
     *                             bool   ratio       keep aspect ratio when resizing?,
     *                             string background  RGB color as hex-value (e.g. #ffffff)
     * @return  string
     * @ignore
     * @throws  \Yana\Core\Exceptions\Files\InvalidImageException  when the uploaded file was no valid image
     */
    public static function uploadImage(array $file, $fileId, array $settings)
    {
        $dir = self::getDirectory();
        $fileTempName = self::_getTempName($file);
        $filename = self::_getOriginalName($file);

        // check mime-type of uploaded file
        $mimetype = 'png';
        if (!empty($file['type'])) {
            if (!preg_match('/^image\/(\w+)$/s', $file['type'], $mimetype)) {
                $message = "The uploaded file has an invalid MIME-type.";
                $level = UPLOAD_ERR_FILE_TYPE;
                $error = new \Yana\Core\Exceptions\Files\InvalidImageException($message, $level);
                throw $error->setFilename($filename);
            }
            $mimetype = $mimetype[1];
        }

        /* name of output files */
        $path = "{$dir}/{$fileId}";
        $thumbnailPath = "{$dir}/thumb.{$fileId}";
        assert('is_string($path); // Wrong argument type for argument 2. String expected');
        assert('is_string($thumbnailPath); // Wrong argument type for argument 3. String expected');

        $fileTempName = $file['tmp_name'];

        /*
         * process image
         *
         * Note: this also checks the file content and will
         * set a "broken" flag if the image is not valid.
         */
        $image = new \Yana\Media\Image($fileTempName, $mimetype);
        /*
         * check if the "broken" flag has been set
         */
        if ($image->isBroken()) {
            $message = "The uploaded file was not a valid image.";
            $level = UPLOAD_ERR_FILE_TYPE;
            $error = new \Yana\Core\Exceptions\Files\InvalidImageException($message, $level);
            throw $error->setFilename($filename);
        }
        $width = $height = $background = null; // init image settings
        if (!empty($settings['width'])) {
            $width = (int) $settings['width'];
        }
        if (!empty($settings['width'])) {
            $height = $settings['height'];
        }
        $ratio = !empty($settings['ratio']);
        if (!empty($settings['background'])) {
            $background = $settings['background'];
            if (preg_match('/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', "$background", $rgb)) {
                $background = array(
                    hexdec($rgb[1]),
                    hexdec($rgb[2]),
                    hexdec($rgb[3])
                );
            }
        }
        // resize (if at least a width or height is given)
        if ($width || $height) {
            $image->createThumbnail($width, $height, $ratio, $background);
            $image->outputToFile($path, $mimetype);
        } else {
            move_uploaded_file($fileTempName, "$path.$mimetype");
        }
        // create thumbnail
        $image->createThumbnail(100, 100, true, $background);
        $image->outputToFile($thumbnailPath, 'png');
        return $path;
    }

}

?>