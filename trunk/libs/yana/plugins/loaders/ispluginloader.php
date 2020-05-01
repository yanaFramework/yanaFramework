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
 * <<interface>> Loads plugin instances.
 *
 * @package     yana
 * @subpackage  plugins
 */
interface IsPluginLoader
{

    /**
     * Check if plugin is currently loaded.
     *
     * @param   string  $pluginName  identifier of the plugin to check
     * @return  bool
     */
    public function isLoaded(string $pluginName): bool;

    /**
     * Check if a specific plugin is installed.
     *
     * This returns bool(true) if a plugin with the name
     * $pluginName exists and has currently been installed.
     * Otherwise it returns bool(false).
     *
     * @param   string  $pluginName  identifier for the plugin
     * @return  bool
     */
    public function isInstalled(string $pluginName): bool;

    /**
     * Loads plugins from a list of names.
     *
     * If no list is provided, all known plugins are loaded.
     *
     * @param   array  $plugins  list of plugin names
     * @throws  \Yana\Core\Exceptions\NotReadableException  when an existing VDrive definition is not readable
     * @return  \Yana\Plugins\Collection
     */
    public function loadPlugins(array $plugins): \Yana\Plugins\Collection;

    /**
     * Load a plugin.
     *
     * Loaded instances are cached. Therefore calling this function twice returns the same instance.
     *
     * @param   string  $name  Must be valid identifier. Consists of chars, numbers and underscores.
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no plugin with that name exists
     * @return  \Yana\IsPlugin
     */
    public function loadPlugin(string $name): \Yana\IsPlugin;

}

?>