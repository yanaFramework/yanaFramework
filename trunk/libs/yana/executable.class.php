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
 * handle php files
 *
 * This is a wrapper class for executable PHP files.
 * You may use this to auto-load source files via a {@see VDrive} configuration file.
 *
 * @access      public
 * @package     yana
 * @subpackage  file_system
 */
class Executable extends FileSystemResource implements IsReadable
{

    /**
     * require / include a php file
     *
     * Tries to interpret the given file as php code using "require_once".
     * This implements dynamic loading of php source code.
     *
     * @access  public
     * @return  bool
     */
    public function read()
    {
        if ($this->exists()) {
            include_once $this->getPath();
            return true;
        } else {
            return false;
        }
    }

    /**
     * alias of toString
     *
     * @ignore
     */
    public function getContent()
    {
        return $this->toString();
    }

    /**
     * get the filename
     *
     * @uses $executable->toString()
     *
     * @access  public
     * @return  string
     *
     * @ignore
     */
    public function toString()
    {
        $phpFile = "PHP-File '".$this->getPath()."'";
        if (!$this->exists()) {
            return "$phpFile does not exist\n";
        } else {
            return "$phpFile\n";
        }
    }

    /**
     * is empty
     *
     * Alias of exists()
     *
     * @access  public
     * @return  bool
     *
     * @ignore
     */
    public function isEmpty()
    {
        return ! $this->exists();
    }

}

?>