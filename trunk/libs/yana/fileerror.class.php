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
 * File error.
 *
 * This is a base class for all errors that have to do with files.
 *
 * @access      public
 * @package     yana
 * @subpackage  error_reporting
 */
class FileError extends Error
{

    /**
     * Set file name.
     *
     * @access  public
     * @param   scalar  $file  path to some file or filename
     * @return  UploadFailedError
     */
    public function setFilename($file)
    {
        $this->data['FILE'] = (string) $file;
        return $this;
    }

}

?>