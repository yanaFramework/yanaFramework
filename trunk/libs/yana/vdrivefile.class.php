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
 * @subpackage vdrive
 * @license    http://www.gnu.org/licenses/gpl.txt
 */

/**
 * Virtual Drive File
 *
 * class representing virtual files
 *
 * @access     public
 * @package    yana
 * @subpackage vdrive
 *
 * @ignore
 */
class VDriveFile extends VDriveMountpoint
{
    /**
     * constructor
     *
     * @access  public
     * @param   string  $path   path
     * @param   string  $type   type  (default : FileReadonly)
     */
    public function __construct($path, $type = null)
    {
        assert('is_string($path); // Wrong type for argument 1. String expected');
        assert('is_string($type); // Wrong type for argument 2. String expected');

        $this->path = $path;

        /* fall back to default value */
        if (empty($type)) {
            $this->type = "FileReadonly";

        /* error: class does not exist */
        } elseif (!class_exists($type)) {
            trigger_error("Invalid resource-type argument supplied for Mountpoint '" . print_r($path, true) . "'.\n\t" .
            "No such file wrapper: '$type'.", E_USER_WARNING);
            return;

        /* all fine - proceed */
        } else {
            $this->type = $type;
            assert('class_exists($this->type); // The value $this->type is expected to be a valid class name.');
            $this->mountpoint = new $this->type($this->path);
        }
    }

    /**
     * get mounted resource
     *
     * Returns the mounted file resource or bool(false) if none is present.
     *
     * @access  public
     * @return  Object
     */
    public function &getMountpoint()
    {
        $error = false;
        if (isset($this->mountpoint) && is_object($this->mountpoint)) {
            return $this->mountpoint;
        } else {
            return $error;
        }
    }

}

?>