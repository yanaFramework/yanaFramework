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

namespace Yana\Plugins\Data;

/**
 * <<interface>> Plugin data-adapter.
 *
 * @package     yana
 * @subpackage  plugins
 */
interface IsAdapter extends \Yana\Data\Adapters\IsDataBaseAdapter
{

    /**
     * Returns collection of all plugin status stored in database.
     *
     * @return  \Yana\Plugins\Data\Collection
     */
    public function getAll();


    /**
     * Return only those plugins that are active.
     *
     * @param   array  $plugins  list of identifiers
     * @return  array
     */
    public function filterActivePlugins(array $plugins);

    /**
     * Check if plugin is active.
     *
     * Returns bool(true) if the plugin identified by $pluginName exists
     * and is active and bool(false) otherwise.
     *
     * @param   string  $pluginName  identifier for the plugin
     * @return  bool
     */
    public function isActive($pluginName);

}

?>