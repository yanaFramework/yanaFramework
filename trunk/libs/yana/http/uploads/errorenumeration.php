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

namespace Yana\Http\Uploads;

/**
 * Listing the common error codes.
 *
 * This is basically meant to keep you from having to look that stuff up on the PHP manual.
 *
 * @package     yana
 * @subpackage  http
 */
class ErrorEnumeration extends \Yana\Core\AbstractEnumeration
{

    /**
     * File upload was success.
     */
    const OK = \UPLOAD_ERR_OK;
    /**
     * File exceeds upload_max_filesize directive in php.ini.
     */
    const INI_SIZE = \UPLOAD_ERR_INI_SIZE;
    /**
     * File exceeds MAX_FILE_SIZE directive in HTML form.
     *
     * This is rarely seen in the wild.
     */
    const FORM_SIZE = \UPLOAD_ERR_FORM_SIZE;
    /**
     * File upload interrupted.
     */
    const PARTIAL = \UPLOAD_ERR_PARTIAL;
    /**
     * No file uploaded.
     *
     * Again, a rare specimen.
     */
    const NO_FILE = \UPLOAD_ERR_NO_FILE;
    /**
     * Missing a setting for the temporary folder.
     */
    const NO_TMP_DIR = \UPLOAD_ERR_NO_TMP_DIR;
    /**
     * Can't write to temporary directory.
     *
     * Either because it doesn't exist, or because we are missing the required privileges.
     */
    const CANT_WRITE = \UPLOAD_ERR_CANT_WRITE;
    /**
     * PHP extension stopped upload.
     *
     * Might happen with some extensions that check uploaded files for things like viruses .
     */
    const EXTENSION = \UPLOAD_ERR_EXTENSION;
}

?>