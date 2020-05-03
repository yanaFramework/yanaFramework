<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana\Http\Uploads;

/**
 * <<interface>> Uploaded file.
 *
 * @package     yana
 * @subpackage  http
 */
interface IsFile
{

    /**
     * Filename provided by the user.
     *
     * ALWAYS CHECK THIS FILENAME GIVEN BY THE CLIENT!
     * Might contain '.' and/or '/' characters!
     *
     * @return  string
     */
    public function getName(): string;

    /**
     * File type provided by the user.
     *
     * DO NOT TRUST THIS MIME-TYPE GIVEN BY THE CLIENT!
     * Always check the type yourself, regardless of what the client tells you!
     *
     * @return  string
     */
    public function getMimeType(): string;

    /**
     * Temporary path and filename on the server.
     *
     * May be empty if file-upload was unsuccessful.
     *
     * @return  string
     */
    public function getTemporaryPath(): string;

    /**
     * File size in bytes.
     *
     * May be empty if file-upload was unsuccessful.
     *
     * @return  int
     */
    public function getSizeInBytes(): int;

    /**
     * Returns the designated target column.
     *
     * This defaults to NULL.
     *
     * @return \Yana\Db\Ddl\Column|NULL
     */
    public function getTargetColumn(): ?\Yana\Db\Ddl\Column;

    /**
     * Set designated target column.
     *
     * This is where the value is supposed to be stored in the database (if any).
     *
     * @param   \Yana\Db\Ddl\Column  $targetColumn  designated target column
     * @return  $this
     */
    public function setTargetColumn(?\Yana\Db\Ddl\Column $targetColumn = null);

    /**
     * File size in bytes.
     *
     * May be empty if file-upload was unsuccessful.
     *
     * @return  int
     */
    public function getErrorCode(): int;

    /**
     * Returns bool(true) if upload was successful.
     *
     * This checks the error code for UPLOAD_ERR_OK.
     *
     * @return  bool
     */
    public function isOkay(): bool;

    /**
     * Returns bool(true) if the file is too big.
     *
     * This checks the error code for UPLOAD_ERR_INI_SIZE.
     *
     * @return  bool
     */
    public function isFileTooBigByIni(): bool;

    /**
     * Returns bool(true) if the file is too big.
     *
     * This checks the error code for UPLOAD_ERR_FORM_SIZE.
     *
     * @return  bool
     */
    public function isFileTooBigByForm(): bool;

    /**
     * Returns bool(true) if the uploaded was interrupted.
     *
     * This checks the error code for UPLOAD_ERR_PARTIAL.
     *
     * @return  bool
     */
    public function isIncompleteUpload(): bool;

    /**
     * Returns bool(true) if the file was not provided by the client.
     *
     * This checks the error code for UPLOAD_ERR_NO_FILE.
     *
     * @return  bool
     */
    public function isNotUploaded(): bool;

    /**
     * Returns bool(true) if no temp-directory is set.
     *
     * This checks the error code for UPLOAD_NO_TMP_DIR.
     * This usually means you got a configuration error in your php.ini.
     *
     * @return  bool
     */
    public function isMissingTemporaryDirectory(): bool;

    /**
     * Returns bool(true) if the file can't be written to the temp-directory.
     *
     * This checks the error code for UPLOAD_CANT_WRITE.
     * This usually means you got a configuration error in your php.ini.
     *
     * @return  bool
     */
    public function isUnableToWriteFile(): bool;

    /**
     * Returns bool(true) if a PHP extension stopped the upload.
     *
     * This checks the error code for UPLOAD_ERR_EXTENSION.
     *
     * @return  bool
     */
    public function isExtensionError(): bool;
}

?>