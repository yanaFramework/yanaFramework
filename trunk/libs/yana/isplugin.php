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

namespace Yana;

/**
 * Plugin
 *
 * This is an interface that all plugins must implement.
 * Each subclass should implement at least the default event handler.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsPlugin
{

    /**
     * <<construct>> For whatever needs to run whenever the plugin is loaded.
     *
     * This is only part of the interface so that derived classes get a warning when they overwrite this and introduce new mandatory parameters.
     * Because doing so would cause trouble, since the plugin factory doesn't expect the constructor to take any arguments.
     */
    public function __construct();
}

?>
