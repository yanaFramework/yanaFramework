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
 * Holds URL components.
 *
 * This class allows to inject certain URLs by manually setting server vars.
 * Use this for test-purposes.
 *
 * @package     yana
 * @subpackage  http
 */
class File extends \Yana\Core\StdObject implements \Yana\Http\Uploads\IsFile
{

    /**
     * @var  string
     */
    private $_name = "";

    /**
     * @var  string
     */
    private $_mimeType = "";

    /**
     * @var  string
     */
    private $_temporaryPath = "";

    /**
     * @var  int
     */
    private $_sizeInBytes = 0;

    /**
     * @var  int
     */
    private $_errorCode = 0;

    /**
     * @var  \Yana\Db\Ddl\Column
     */
    private $_targetColumn = null;

    /**
     * Initialize file.
     *
     * @param  string  $name           file name provided by client
     * @param  string  $mimeType       file type provided by client
     * @param  string  $temporaryPath  path to file on server
     * @param  int     $sizeInBytes    size of file in Bytes
     * @param  int     $errorCode      error code (0 = no error)
     */
    public function __construct(string $name, string $mimeType, string $temporaryPath, int $sizeInBytes, int $errorCode)
    {
        $this->_name = $name;
        $this->_mimeType = $mimeType;
        $this->_temporaryPath = $temporaryPath;
        $this->_sizeInBytes = $sizeInBytes;
        $this->_errorCode = $errorCode;
    }

    /**
     * File name provided by the user.
     *
     * @return  string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * File type provided by the user.
     *
     * @return  string
     */
    public function getMimeType(): string
    {
        return $this->_mimeType;
    }

    /**
     * Temporary path and filename on the server.
     *
     * May be empty if file-upload was unsuccessful.
     *
     * @return  string
     */
    public function getTemporaryPath(): string
    {
        return $this->_temporaryPath;
    }

    /**
     * File size in bytes.
     *
     * May be empty if file-upload was unsuccessful.
     *
     * @return  int
     */
    public function getSizeInBytes(): int
    {
        return $this->_sizeInBytes;
    }

    /**
     * Returns the designated target column.
     *
     * This defaults to NULL.
     *
     * @return \Yana\Db\Ddl\Column|NULL
     */
    public function getTargetColumn(): ?\Yana\Db\Ddl\Column
    {
        return $this->_targetColumn;
    }

    /**
     * Set designated target column.
     *
     * This is where the value is supposed to be stored in the database (if any).
     *
     * @param   \Yana\Db\Ddl\Column  $targetColumn  designated target column
     * @return  $this
     */
    public function setTargetColumn(?\Yana\Db\Ddl\Column $targetColumn = null)
    {
        $this->_targetColumn = $targetColumn;
        return $this;
    }

    /**
     * File size in bytes.
     *
     * May be empty if file-upload was unsuccessful.
     *
     * @return  int
     */
    public function getErrorCode(): int
    {
        return $this->_errorCode;
    }

    /**
     * Returns bool(true) if upload was successful.
     *
     * This checks the error code for UPLOAD_ERR_OK.
     *
     * @return  bool
     */
    public function isOkay(): bool
    {
        return $this->getErrorCode() === \Yana\Http\Uploads\ErrorEnumeration::OK;
    }

    /**
     * Returns bool(true) if the file is too big.
     *
     * This checks the error code for UPLOAD_ERR_INI_SIZE.
     *
     * @return  bool
     */
    public function isFileTooBigByIni(): bool
    {
        return $this->getErrorCode() === \Yana\Http\Uploads\ErrorEnumeration::INI_SIZE;
    }

    /**
     * Returns bool(true) if the file is too big.
     *
     * This checks the error code for UPLOAD_ERR_FORM_SIZE.
     *
     * @return  bool
     */
    public function isFileTooBigByForm(): bool
    {
        return $this->getErrorCode() === \Yana\Http\Uploads\ErrorEnumeration::FORM_SIZE;
    }

    /**
     * Returns bool(true) if the uploaded was interrupted.
     *
     * This checks the error code for UPLOAD_ERR_PARTIAL.
     *
     * @return  bool
     */
    public function isIncompleteUpload(): bool
    {
        return $this->getErrorCode() === \Yana\Http\Uploads\ErrorEnumeration::PARTIAL;
    }

    /**
     * Returns bool(true) if the file was not provided by the client.
     *
     * This checks the error code for UPLOAD_ERR_NO_FILE.
     *
     * @return  bool
     */
    public function isNotUploaded(): bool
    {
        return $this->getErrorCode() === \Yana\Http\Uploads\ErrorEnumeration::NO_FILE;
    }

    /**
     * Returns bool(true) if no temp-directory is set.
     *
     * This checks the error code for UPLOAD_NO_TMP_DIR.
     * This usually means you got a configuration error in your php.ini.
     *
     * @return  bool
     */
    public function isMissingTemporaryDirectory(): bool
    {
        return $this->getErrorCode() === \Yana\Http\Uploads\ErrorEnumeration::NO_TMP_DIR;
    }

    /**
     * Returns bool(true) if the file can't be written to the temp-directory.
     *
     * This checks the error code for UPLOAD_CANT_WRITE.
     * This usually means you got a configuration error in your php.ini.
     *
     * @return  bool
     */
    public function isUnableToWriteFile(): bool
    {
        return $this->getErrorCode() === \Yana\Http\Uploads\ErrorEnumeration::CANT_WRITE;
    }

    /**
     * Returns bool(true) if a PHP extension stopped the upload.
     *
     * This checks the error code for UPLOAD_ERR_EXTENSION.
     *
     * @return  bool
     */
    public function isExtensionError(): bool
    {
        return $this->getErrorCode() === \Yana\Http\Uploads\ErrorEnumeration::EXTENSION;
    }

}

?>