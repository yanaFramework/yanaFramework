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
 * @package    yana
 * @license    http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\VDrive;

/**
 * Virtual Drive File.
 *
 * @package    yana
 * @subpackage vdrive
 *
 * @ignore
 */
class File extends AbstractMountpoint
{

    /**
     * <<constructor>> Initialize wrapped object.
     *
     * @param   string  $path       path to existing file
     * @param   string  $className  class name including namespace, defaults to \Yana\Files\Readonly
     * @throws  \Yana\Core\Exceptions\ClassNotFoundException  when the given class does not exist
     */
    public function __construct($path, $className = '')
    {
        assert('is_string($path); // Wrong type for argument 1. String expected');
        assert('$path > ""; // Argument 1 must not be empty');
        assert('is_string($className); // Wrong type for argument 2. String expected');

        $this->path = (string) $path;

        /* error: class does not exist */
        if ($className !== "" && !class_exists($className)) {
            $message = "Invalid resource-type argument supplied for Mountpoint '" . print_r($path, true) . "'.\n\t" .
                "No such file wrapper: '" . $className . "'.";
            throw new \Yana\VDrive\ClassNotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        /* all fine - proceed */
        if ($className === "") {
            $this->mountpoint = new \Yana\Files\Readonly($path);
        } else {
            $this->mountpoint = new $className($path);
        }
        $this->type = '\\' . \get_class($this->mountpoint);
    }

}

?>