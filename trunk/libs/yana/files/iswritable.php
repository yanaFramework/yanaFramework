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
 * @access      public
 * @package     yana
 * @subpackage  file
 */
interface IsWritable extends IsReadable
{

    /**
     * create the current file if it does not exist
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @return  bool
     */
    public function create();

    /**
     * write file to system
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @return  bool
     */
    public function write();

    /**
     * delete this file
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @return  bool
     */
    public function delete();

    /**
     * copy the file to some destination
     *
     * This will create a copy of this file on the filesystem.
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access   public
     * @param    string   $destFile     destination to copy the file to
     * @return   bool
     */
    public function copy($destFile);
}

?>
