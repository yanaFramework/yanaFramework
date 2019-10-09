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
 * Loads plugin instances.
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class PluginLoader extends \Yana\Plugins\Loaders\AbstractPluginLoader
{

    /**
     * Load a plugin.
     *
     * @param   string  $name  Must be valid identifier. Consists of chars, numbers and underscores.
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no plugin with that name exists
     * @return  \Yana\IsPlugin
     */
    public function loadPlugin($name)
    {
        assert(is_string($name), 'Invalid argument $name: string expected');
        return \Yana\Plugins\AbstractPlugin::loadPlugin($name, $this->_getPluginDirectory(), $this->_getContainer());
    }

}

?>