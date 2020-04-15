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

namespace Yana\Plugins\Menus;

/**
 * <<interface>> Menu information
 *
 * @name        PluginMenu
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
interface IsMenu
{

    /**
     * Adds an entry to a menu of your choice.
     *
     * If no menu-group is specified, the entry is added to the root-level.
     *
     * @param   string                       $action     name of action
     * @param   \Yana\Plugins\Menus\IsEntry  $menuEntry  configuration object
     * @return  $this
     */
    public function setMenuEntry(string $action, \Yana\Plugins\Menus\IsEntry $menuEntry);

    /**
     * remove menu entry
     *
     * Returns bool(true) on success and bool(false) if the entry does not exist.
     *
     * @param   string  $action    name of action
     * @param   string  $menuName  set this entry is inside a menu
     * @return  bool
     */
    public function unsetMenuEntry(string $action, string $menuName = ""): bool;

    /**
     * Set a name for a menu of your choice.
     *
     * @param   string  $menu  menu where entry should be added, blank means root-level
     * @param   string  $name  name of your choice
     * @return  $this
     */
    public function setMenuName(string $menu, string $name = "");

    /**
     * get menu entries
     *
     * Returns an array of all entries of a specific menu,
     * or all menus if none is specified, as a associative
     * array.
     *
     * @param   string  $menuName  filter by menu name
     * @return  \Yana\Plugins\Menus\IsEntry[]
     */
    public function getMenuEntries(?string $menuName = null): array;

    /**
     * get menu name
     *
     * Returns the label of the given menu as a string.
     * If the menu is unknown the id is returned instead.
     *
     * @param   string  $menuId  menu id to look up
     * @return  string
     */
    public function getMenuName(string $menuId): string;

    /**
     * return menu as associative array
     *
     * Extracts all menus and items.
     * Array Keys are menu names.
     * Value Keys are URLs, values are text labels.
     *
     * @return  array
     */
    public function getTextMenu(): array;

}

?>