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

namespace Yana\Plugins\Loaders;

/**
 * <<interface>> Loads registry definitions.
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
interface IsRegistryLoader
{

    /**
     * Access the drive of a plugin by using it's name.
     *
     * @param   string  $name  name of plugin
     * @return  \Yana\Files\IsReadable
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no such file is defined
     */
    public function getFileObjectFromRegistry(string $name): \Yana\Files\IsReadable;

    /**
     * Loads registry definitions from a list of names.
     *
     * @param   array  $registries  list of registry identifiers
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     * @return  \Yana\VDrive\RegistryCollection
     */
    public function loadRegistries(array $registries): \Yana\VDrive\RegistryCollection;

    /**
     * Load a registry definition.
     *
     * @param   string  $name  Must be valid identifier. Consists of chars, numbers and underscores.
     * @throws  \Yana\Core\Exceptions\NotFoundException     when a VDrive definition does not exist
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     * @return  \Yana\VDrive\IsRegistry
     */
    public function loadRegistry(string $name): \Yana\VDrive\IsRegistry;

}

?>