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

namespace Yana\VDrive;

/**
 * <<interface>> Virtual Drive.
 *
 * Class to abstract from real filesystems by mapping
 * filenames to aliases (mountpoints).
 *
 * @package    yana
 * @subpackage vdrive
 */
interface IsVDrive extends \Yana\Files\IsResource, \Yana\Report\IsReportable
{

    /**
     * Make this the default drive.
     *
     * Each drive has it's own private settings. However: you can make
     * one drive a public "default drive". The settings of this drive
     * will become publicly visible to all other instances.
     *
     * These are standard directory settings, which are used to auto-replace references
     * found in VDrive-config-files.
     *
     * These settings are automatically initialized by the framework, so normally you
     * don't need to care for these settings.
     *
     * You can recall this function to change the global drive at any time.
     * This will not replace or remove the private settings of the prior "global" drive.
     *
     * But be adviced: you should always do this BEFORE creating the object,
     * or otherwise it will have no effect.
     *
     * @ignore
     */
    public function setAsGlobal();

    /**
     * Mount an unmounted virtual drive.
     *
     * Mount the mountpoint identified by $name and copies the contents
     * (if any) to the repository.
     *
     * This function returns bool(true) on success, or bool(false) on error.
     *
     * @param   string  $name  name of the drive to mount
     * @return  bool
     */
    public function mount($name);

    /**
     * Read the virtual drive.
     *
     * This loads the virtual drive and initializes it's contents.
     * It does nothing when called multiple times.
     *
     * If the file does not exist or is not readable, the function throws an exception.
     *
     * @throws  \Yana\Core\Exceptions\NotReadableException    when source file is not readable
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when the file could not be read or contains invalid syntax
     */
    public function read();

    /**
     * Returns name of virtual drive.
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\NotFoundException       when configuration file doesn't exist
     * @throws  \Yana\Core\Exceptions\NotReadableException    when configuration file is not readable
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when the file could not be read or contains invalid syntax
     */
    public function getDriveName();

    /**
     * Return file contents as string.
     *
     * @return  string
     */
    public function getContent();

    /**
     * Get a resource.
     *
     * Returns the file system resource specified by $key, or bool(false) if the resource
     * does not exist, or was unable to return any contents.
     *
     * @name    VDrive::getResource()
     * @param   string  $path  virtual file path
     * @return  \Yana\Files\AbstractResource
     * @throws  \Yana\Core\Exceptions\NotFoundException       when virtual file or directory does not exist.
     * @throws  \Yana\Core\Exceptions\NotReadableException    when source file is not readable
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when the file could not be read or contains invalid syntax
     */
    public function getResource($path);

    /**
     * Resolves the virtual path and returns the real path of the resource.
     *
     * @param   string  $virtualPath    virtual path that should be converted to real path
     * @return  string
     * @throws  \Yana\Core\Exceptions\NotFoundException       when virtual file or directory does not exist.
     * @throws  \Yana\Core\Exceptions\NotReadableException    when the vDrive is not readable
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when the vDrive could not be read or contains invalid syntax
     */
    public function getResourcePath($virtualPath);

    /**
     * Get list of mountpoints.
     *
     * Returns an array of {@see Mountpoint}s where the keys are the file paths and the values
     * are the mountpoint definitions.
     *
     * @return  array
     */
    public function getMountpoints();

}

?>