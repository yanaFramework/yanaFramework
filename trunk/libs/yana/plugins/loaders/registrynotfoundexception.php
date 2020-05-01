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
 * <<exception>> Thrown when no registry of a given name awas found.
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class RegistryNotFoundException extends \Yana\Plugins\Loaders\LoaderException
{

    /**
     * Set registry name.
     *
     * @param   string  $registryName  of object that was not found, usually identical to associated plugin name
     * @return  $this
     */
    public function setRegistryName(string $registryName)
    {
        $this->data['NAME'] = $registryName;
        return $this;
    }

}

?>