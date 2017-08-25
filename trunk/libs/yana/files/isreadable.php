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
 * <<interface>> readable file system resource
 *
 * This class identifies readable resources.
 *
 * @package     yana
 * @subpackage  file
 */
interface IsReadable extends \Yana\Files\IsResource
{

    /**
     * Read file contents.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @throws  \Yana\Core\Exceptions\NotFoundException     when the file is not found
     * @throws  \Yana\Core\Exceptions\NotReadableException  when the file is not readable
     * @return  self
     */
    public function read();

    /**
     * Returns the contents.
     *
     * @return  string|array
     */
    public function getContent();

}

?>
