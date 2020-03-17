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
declare(strict_types=1);

namespace Yana\Files;

/**
 * Null object for unit tests only.
 *
 * @package     yana
 * @subpackage  files
 */
class NullFile extends \Yana\Files\Text
{

    /**
     * Does nothing.
     */
    public function write()
    {
        // intentionally left blank
    }

    /**
     * Does nothing.
     *
     * @return  bool
     */
    public function delete()
    {
        return true;
    }

    /**
     * Does nothing.
     */
    public function create()
    {
        // intentionally left blank
    }

    /**
     * Does nothing.
     *
     * @param    string   $destFile     destination to copy the file to
     * @param    bool     $overwrite    setting this to false will prevent existing files from getting overwritten
     * @param    bool     $isRecursive  setting this to true will automatically, recursively create directories
     *                                  in the $destFile string, if required
     * @param    int      $mode         the access restriction that applies to the copied file, defaults to 0766
     */
    public function copy($destFile, $overwrite = true, $isRecursive = false, $mode = 0766)
    {
        // intentionally left blank
    }

}

?>