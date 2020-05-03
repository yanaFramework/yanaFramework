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

namespace Yana\Db\Binaries\Uploads;

/**
 * Handle file uploads.
 *
 * @package     yana
 * @subpackage  db
 * @ignore
 */
class FileUploader extends \Yana\Db\Binaries\Uploads\AbstractUploader
{

    /**
     * The purpose of this method is to store (text) files in a file pool.
     *
     * If the file already exists, it will get replaced.
     *
     * Returns the path to the uploaded file.
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
     * $column = $db->getSchema()->getTable('my_table')->getColumn('my_file');
     * $idGenerator = new \Yana\Db\Helpers\IdGenerator();
     * $fileId = $idGenerator($column);
     * // and assign it to your row to update/insert
     * $row['my_file'] = $fileId;
     *
     * // upload the file
     * $helper = new \Yana\Db\Binaries\Uploads\FileUploader();
     * $file = new \Yana\Http\Uploads\File(
     *     $_FILES['my_file']['name'],
     *     $_FILES['my_file']['type'],
     *     $_FILES['my_file']['tmp_name'],
     *     $_FILES['my_file']['size'],
     *     $_FILES['my_file']['error']
     * );
     * $filename = $helper->upload($file, $fileId);
     * // and update/insert the row as usual
     * $db->insert("my_table", $row);
     * </code>
     *
     * @param   \Yana\Http\Uploads\IsFile $file    representing one entry in global $_FILES array
     * @param   string                    $fileId  name of target file
     * @return  string
     */
    public function upload(\Yana\Http\Uploads\IsFile $file, string $fileId)
    {
        assert(!isset($dir), 'Cannot redeclare var $dir');
        $dir = $this->_getConfiguration()->getDirectory();

        $fileTempName = $this->_getTempName($file);
        $filename = $file->getName();

        /* handle errors */
        if (!is_file($fileTempName) || !is_readable($fileTempName)) {
            $message = "Uploaded file is not readable.";
            $code = \Yana\Log\TypeEnumeration::INFO;
            $error = new \Yana\Core\Exceptions\Files\UploadFailedException($message, $code);
            throw $error->setFilename($filename);

        } /* end error handling */

        /* name of output file */
        $path = "{$dir}/{$fileId}.gz";

        /*
         * mime-type
         *
         * The Mime-type is saved, so it may be sent to the client on download.
         */
        assert(!isset($mimetype), 'Cannot redeclare var $mimetype');
        $mimetype = "";
        if ($file->getMimeType() > "") {
            $mimetype = preg_replace('/\s/', ' ', $file->getMimeType());
        }

        $this->_createCompressedFile($path, $filename, $fileTempName, $mimetype);
        return $path;
    }

    /**
     * Copy and compress contents of uploaded source file to target G-Zip file.
     *
     * @param  string  $path          where to store file and its name
     * @param  string  $filename      original file name
     * @param  string  $fileTempName  path to source file
     * @param  string  $mimetype      MIME-type of source
     * @codeCoverageIgnore
     */
    protected function _createCompressedFile(string $path, string $filename, string $fileTempName, string $mimetype)
    {
        assert(!isset($gz), 'Cannot redeclare var $gz');
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
        gzwrite($gz, filesize($fileTempName) . "\n");
        gzwrite($gz, "$mimetype\n");
        /*
         * save file contents
         */
        gzwrite($gz, file_get_contents($fileTempName));
        gzclose($gz);
    }

}

?>