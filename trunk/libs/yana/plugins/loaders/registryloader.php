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

namespace Yana\Plugins\Loaders;

/**
 * Transient drive cache-wrapper.
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class RegistryLoader extends \Yana\Plugins\Loaders\AbstractRegistryLoader
{

    /**
     * Load a registry definition.
     *
     * Loaded instances are cached. Therefore calling this function twice returns the same instance.
     *
     * @param   string  $name  Must be valid identifier. Consists of chars, numbers and underscores.
     * @throws  \Yana\Core\Exceptions\NotFoundException     when a VDrive definition does not exist
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     * @return  \Yana\VDrive\IsRegistry
     */
    public function loadRegistry($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');

        // load virtual drive, if it exists
        assert('!isset($driveFile); // Cannot redeclare var $driveFile');
        $driveFile = \Yana\Plugins\PluginNameMapper::toVDriveFilenameWithDirectory($name, $this->_getPluginDirectory());

        if (!is_file($driveFile)) {
            throw new \Yana\Core\Exceptions\NotFoundException("Resource not found: " . $driveFile, \Yana\Log\TypeEnumeration::INFO);
        }
        $vDrive = new \Yana\VDrive\Registry($driveFile, $this->_getPluginDirectory()->getPath() . $name . "/");
        $vDrive->read(); // Throws \Yana\Core\Exceptions\NotReadableException
        return $vDrive;
    }

}

?>