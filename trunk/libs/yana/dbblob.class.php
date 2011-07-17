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
 * read binary large objects (blobs) from database
 *
 * Example of usage:
 * <code>
 * $db = Yana::connect('foo');
 * $id = $db->select('foo.1.foo_file');
 *
 * $file = new DbBlob($id);
 * $file->read();
 *
 * // output file to screen
 * print $file->getContent();
 * // copy file to some destination
 * $file->copy('foo/bar.dat');
 * </code>
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 * @since       2.9.2
 */
class DbBlob extends FileReadonly
{
    /**#@+
     * @access  private
     */

    /** @var int    */ private $_size = 0;
    /** @var string */ private $_type = 'application/unknown';

    /**#@-*/

    /**
     * @var     string
     * @access  protected
     * @static
     * @ignore
     */
    protected static $blobDir = null;

    /**
     * read file contents
     *
     * Tries to read the blob contents, decompresses it and caches the file attributes like
     * type, size and original filename.
     *
     * An message is issued
     *
     * @access  public
     * @throws  NotReadableException  if the blob is not valid
     * @throws  NotFoundException     if the blob does not exist
     */
    public function read()
    {
        $source = $this->getPath();

        if (!is_file($source)) {
            $message = "The file '{$source}' does not exist (database: blob not found).";
            throw new NotFoundException($message, E_USER_NOTICE);
        }
        $this->content = array();

        if (!preg_match('/\.gz$/', $source)) {
            $message = "The source '{$source}' is not a valid database blob.";
            throw new NotReadableException($message, E_USER_WARNING);
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
     * get mime-type of this file
     *
     * Returns bool(false) on error.
     *
     * @access  public
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
     * get size of this file
     *
     * Returns the size of the file in bytes (from cached value).
     * If an error occurs, bool(false) is returned.
     *
     * @access  public
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
     * @access  public
     * @param   int   $id        index in files list, of the file to get
     * @param   bool  $fullsize  show full size or thumb-nail (images only)
     * @static
     * @return  string
     * @throws  InvalidArgumentException  if file with index $id does not exist
     * @throws  FileNotFoundError         if the requested file no longer exists
     */
    public static function getFilenameFromSession($id, $fullsize = false)
    {
        assert('is_int($id); // Wrong type for argument 1. Integer expected');
        assert('is_bool($fullsize); // Wrong type for argument 2. Boolean expected');

        $id = (int) $id;

        /* check arguments */
        if (!isset($_SESSION[__CLASS__][$id])) {
            throw new InvalidArgumentException("Invalid argument. File '$id' is undefined.", E_USER_WARNING);
        }

        $file = $_SESSION[__CLASS__][$id];

        if (!$fullsize && !preg_match('/\.gz$/', $file)) {
            $id = self::getFileIdFromFilename($file);
            $file = self::getThumbnailFromFileId($id);
        }
        if (!is_file($file)) {
            throw new FileNotFoundError($file);
        }
        return $file;
    }

    /**
     * Store filename as session var and return an ID
     *
     * @access  public
     * @static
     * @param   string  $file
     * @return  string
     * @throws  FileNotFoundError  if the given $file does not exist
     */
    public static function storeFilenameInSession($file)
    {
        assert('is_string($file); // Wrong argument type argument 1. String expected');
        if (!is_file($file)) {
            throw new FileNotFoundError();
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
     * @access   public
     * @param    string  $destFile   destination to copy the file to
     * @param    bool    $overwrite  setting this to false will prevent
     *                               existing files from getting overwritten
     * @return   bool
     * @throws   InvalidArgumentException  on invalid filename
     */
    public function copy($destFile, $overwrite = true)
    {
        assert('is_string($destFile); // Wrong type for argument 1. String expected');
        assert('is_bool($overwrite); // Wrong type for argument 2. Boolean expected');

        /* validity checking */
        if (mb_strlen($destFile) > 512 || !preg_match('/^[\w\d-_\.][\w\d-_\/\.]*$/', $destFile)) {
            throw new InvalidArgumentException("Invalid filename '".$destFile."'.", E_USER_WARNING);
        }

        if (!$overwrite && file_exists($destFile)) {
            Log::report("Unable to copy to file '{$destFile}'. " .
                "Another file with the same name does already exist.");
            return false;
        } elseif ($overwrite && file_exists($destFile) && !is_writeable($destFile)) {
            Log::report("Unable to copy file to '{$destFile}'. Permission denied.");
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
     * create unique id to identify a file
     *
     * @access  public
     * @static
     * @param   DDLColumn  $column  column definition
     * @return  string
     * @ignore
     */
    public static function getNewFileId(DDLColumn $column)
    {
        $table = $column->getParent();
        $tableName = "";
        if ($table instanceof DDLTable) {
            $tableName = $table->getName();
        }
        $columnName = $column->getName();
        return md5(uniqid("$tableName.$columnName.", true));
    }

    /**
     * extract unique file-id from a database value
     *
     * @access  public
     * @static
     * @param   string  $filename filename
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
     * @access  public
     * @static
     * @param   string  $id    file id
     * @param   bool    $type  file type (image or file, leave blank for auto-detect)
     * @return  string
     */
    public static function getFilenameFromFileId($id, $type = '')
    {
        assert('is_string($id); // Invalid argument $id: string expected');
        assert('is_string($type); // Invalid argument $type: string expected');

        $file = self::getDbBlobDir() . $id;
        switch ($type)
        {
            case 'image':
                $file .= '.png';
            break;
            case 'file':
                $file .= '.gz';
            break;
            case 'file':
                $file .= '.*';
            break;
        }
        if (is_file($file)) {
            return $file;
        } else {
            foreach (glob(self::getDbBlobDir() . $id) as $filename)
            {
                return $filename;
            }
            return "";
        }
    }

    /**
     * get filename by id
     *
     * Searches for a matching filename for a given id.
     *
     * @param   string  $id  file id
     * @return  string
     */
    public static function getThumbnailFromFileId($id)
    {
        assert('is_string($id); // Wrong argument type argument 1. String expected');
        return self::getDbBlobDir() . "thumb.{$id}.png";
    }

    /**
     * remove a binary large object from database
     *
     * This removes a files (blob) from the database.
     * The type of the column must be "file" or "image".
     *
     * IMPORTANT NOTE: This is a low-level function that DOES NOT
     * disassociate the files with the datasets, that reference
     * them.
     *
     * Returns bool(true) on success and bool(false) if the file does not exist.
     *
     * @access  public
     * @param   string    $fileToDelete  filename which would be removed
     * @throws  NotFoundException        when the given file was not found
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
            throw new NotFoundException("File not found: $fileToDelete");
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
     * get datbase blob-directory
     *
     * Returns path to directory where files are stored.
     *
     * @access  protected
     * @static
     * @return  string
     * @ignore
     */
    public static function getDbBlobDir()
    {
        if (is_null(self::$blobDir)) {
            if (isset($GLOBALS['YANA'])) {
                $blobDir = $GLOBALS['YANA']->getVar('DBBLOB');
            } else {
                $blobDir = 'config/db/.blob/';
            }
            assert('is_dir($blobDir); // Blob-dir does not exist');
            self::$blobDir = realpath($blobDir) . DIRECTORY_SEPARATOR;
            if (empty(self::$blobDir)) {
                $message = "Configuration error: the yana system setting 'DBBLOB' = '$blobDir' " .
                    "does not refer to not a valid directory.";
                throw new Error($message);
            }
        }
        return self::$blobDir;
    }

    /**
     * sanitize file id
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
     * @access  private
     * @static
     * @param   array  $file  item taken from $_FILES array
     * @return  string
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
                    $alert = new FilesizeError("", $file['error']);
                    throw $alert->setFilename($filename)->setMaxSize($maxSize);
                break;

                case UPLOAD_ERR_FILE_TYPE:
                    $error = new UploadFailedError("", UPLOAD_ERR_FILE_TYPE);
                    throw $error->setFilename($filename);
                break;

                case UPLOAD_ERR_INVALID_TARGET:
                    Log::report("Unable to write uploaded file '{$filename}'.");
                    throw new NotWriteableError("", UPLOAD_ERR_INVALID_TARGET);
                break;
                case UPLOAD_ERR_OTHER:
                default:
                    Log::report("Unable to write uploaded file '{$filename}'.");
                    throw new NotWriteableError("", $file['error']);
                break;
            } // end switch (error code)
        }

        return $file['tmp_name'];
    }

    /**
     * Get original filename.
     *
     * @access  private
     * @static
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
     * $fileId = DbBlob::getNewFileId($column);
     * // and assign it to your row to update/insert
     * $row['my_file'] = $fileId;
     *
     * // upload the file
     * $filename = DbBlob::uploadFile($_FILES['my_file'], $fileId);
     * // and update/insert the row as usual
     * $db->insert("my_table", $row);
     * </code>
     *
     * For images use {@see DbBlob::uploadImage()}.
     *
     * @access  public
     * @static
     * @param   array      $file    item taken from array $_FILES
     * @param   string     $fileId  name of target file
     * @return  string
     * @ignore
     */
    public static function uploadFile(array $file, $fileId)
    {
        assert('is_string($fileId); // Wrong argument type for argument 3. String expected');

        assert('!isset($dir); // Cannot redeclare var $dir');
        $dir = self::getDbBlobDir();

        $fileTempName = self::_getTempName($file);
        $filename = self::_getOriginalName($file);

        /* handle errors */
        if (!is_file($fileTempName) || !is_readable($fileTempName)) {
            $error = new UploadFailedError("", E_USER_NOTICE);
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
     * $fileId = DbBlob::getNewFileId($column);
     * // and assign it to your row to update/insert
     * $row['my_file'] = $fileId;
     *
     * // get image/thumbnail settings
     * $settings = $column->getImageSettings();
     *
     * // upload the image
     * $filename = DbBlob::uploadImage($_FILES['my_file'], $fileId, $settings);
     * // and update/insert the row as usual
     * $db->insert("my_table", $row);
     * </code>
     *
     * For other types of files use {@see DbBlob::uploadFile()}.
     *
     * @access  public
     * @static
     * @param   array      $file      item taken from array $_FILES
     * @param   string     $fileId    name of target file
     * @param   array      $settings  int    width       image width in pixel,
     *                                int    height      image height in pixel,
     *                                bool   ratio       keep aspect ratio when resizing?,
     *                                string background  RGB color as hex-value (e.g. #ffffff)
     * @return  string
     * @ignore
     */
    public static function uploadImage(array $file, $fileId, array $settings)
    {
        $dir = self::getDbBlobDir();
        $fileTempName = self::_getTempName($file);
        $filename = self::_getOriginalName($file);

        // check mime-type of uploaded file
        if (!empty($file['type'])) {
            if (!preg_match('/^image\/(\w+)$/s', $file['type'], $mimetype)) {
                $error = new InvalidImageError("", UPLOAD_ERR_FILE_TYPE);
                throw $error->setFilename($filename);
            }
            $mimetype = $mimetype[1];
        } else {
            $mimetype = 'png';
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
            $error = new InvalidImageError("", UPLOAD_ERR_FILE_TYPE);
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