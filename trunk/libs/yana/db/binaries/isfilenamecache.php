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
 * <<interface>> Stores and retrieves filenames in cache.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsFileNameCache
{

    /**
     * Read the current file id from cache.
     *
     * Returns the path of a file as stored in the session.
     * Throws an exception if the id is invalid or the file is not found.
     *
     * @param   int   $id        index in files list, of the file to get
     * @param   bool  $fullsize  show full size or thumb-nail (images only)
     * @return  string
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if file with index $id does not exist
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException   if the requested file no longer exists
     */
    public function getFilename($id, $fullsize = false);

    /**
     * Store filename in cache and return an ID.
     *
     * @param   string  $file
     * @return  string
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  if the given $file does not exist
     */
    public function storeFilename($file);
}

?>