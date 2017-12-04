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

namespace Yana\Files;

/**
 * <<Interface>> writable file system resource
 *
 * This class identifies writable resources.
 *
 * @package     yana
 * @subpackage  files
 */
interface IsWritable extends \Yana\Files\IsReadable
{

    /**
     * Create the current file if it does not exist.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException  when target does already exist
     * @throws  \Yana\Core\Exceptions\NotWriteableException   when unable to create file
     */
    public function create();

    /**
     * Write file to system.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\Files\NotWriteableException  when file does not exist or is not writeable
     * @throws  \Yana\Core\Exceptions\Files\UncleanWriteException  when the file was changed by a third party
     */
    public function write();

    /**
     * Fail-safe writing of data.
     *
     * Automatically restarts writing if the file-resource
     * is temporarily not available after waiting for 0.5 seconds.
     *
     * The process is aborted if it failed 3 times.
     *
     * @return  bool
     */
    public function failSafeWrite();

    /**
     * Delete this file.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\Files\NotWriteableException  when the file is not writeable
     */
    public function delete();

    /**
     * Copy the file to some destination.
     *
     * This will create a copy of this file on the filesystem.
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param    string   $destFile     destination to copy the file to
     * @return   bool
     */
    public function copy($destFile);

}

?>