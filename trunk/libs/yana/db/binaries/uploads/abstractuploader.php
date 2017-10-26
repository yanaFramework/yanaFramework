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
 * <<abstract>> Base class to handle file uploads.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractUploader extends \Yana\Core\Object
{

    /**
     * @var \Yana\Db\Binaries\IsConfiguration
     */
    private $_configuration = null;

    /**
     * <<constructor>> To inject a custom configuration if needed.
     *
     * @param  \Yana\Db\Binaries\IsConfiguration  $configuration  inject your own configuration
     */
    public function __construct(\Yana\Db\Binaries\IsConfiguration $configuration = null)
    {
        $this->_configuration = $configuration;
    }

    /**
     * Returns a file source configuration.
     *
     * @return  \Yana\Db\Binaries\IsConfiguration
     */
    protected function _getConfiguration()
    {
        if (!isset($this->_configuration)) {
            $this->_configuration = \Yana\Db\Binaries\ConfigurationSingleton::getInstance();
        }
        return $this->_configuration;
    }

    /**
     * Sanitize file id.
     *
     * For file upload error handling see the following example:
     * <code>
     * try {
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
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException    when the given array contains no filename
     */
    protected function _getTempName(array $file)
    {
        // get original filename (for reporting purposes only)
        $filename = $this->_getOriginalName($file);
        // check error state
        if (isset($file['error'])) {
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
                    // @codeCoverageIgnoreStart
                    if (!isset($maxSize)) {
                        $maxSize = (int) $_POST['MAX_FILE_SIZE'];
                    }
                    // @codeCoverageIgnoreEnd

                    $message = "Uploaded file exceeds maximum size.";
                    $alert = new \Yana\Core\Exceptions\Files\SizeException($message, $file['error']);
                    throw $alert->setFilename($filename)->setMaxSize($maxSize);

                case UPLOAD_ERR_FILE_TYPE:
                    $message = "Uploaded file has a file type that is either not recognized or not permitted.";
                    $error = new \Yana\Core\Exceptions\Files\UploadFailedException($message, UPLOAD_ERR_FILE_TYPE);
                    throw $error->setFilename($filename);

                case UPLOAD_ERR_INVALID_TARGET:
                case UPLOAD_ERR_OTHER:
                default:
                    $message = "Unable to write uploaded file '{$filename}'.";
                    $error = new \Yana\Core\Exceptions\Files\NotWriteableException($message, $file['error']);
                    throw $error->setFilename($filename);

            } // end switch (error code)
        }

        if (!isset($file['tmp_name'])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException('Input array contains no file temp-name.', \Yana\Log\TypeEnumeration::ERROR);
        }
        return $file['tmp_name'];
    }

    /**
     * Get original filename.
     *
     * Returns 'name' index of given array.
     *
     * @param   array  $file  item taken from $_FILES array
     * @return  string
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given array contains no filename
     */
    protected function _getOriginalName(array $file)
    {
        if (!isset($file['name'])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException('Input array contains no filename.', \Yana\Log\TypeEnumeration::ERROR);
        }
        return (string) $file['name'];
    }

}

?>