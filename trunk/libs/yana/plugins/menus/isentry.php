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

namespace Yana\Plugins\Menus;

/**
 * <<interface>> Menu information.
 *
 * This class configurates a menu entry
 *
 * @package     yana
 * @subpackage  plugins
 * @ignore
 */
interface IsEntry
{

    /**
     * Get name of menu group.
     *
     * Groups may have sub-groups, devided by '.'.
     *
     * @return  int
     */
    public function getGroup();

    /**
     * Set name of menu group.
     *
     * Groups may have sub-groups, devided by '.'.
     *
     * Example: foo.bar
     * Meaning, foo is the top-menu, with bar as the second-level entry.
     *
     * @param   string  $group  
     * @return  \Yana\Plugins\Menus\IsEntry
     */
    public function setGroup($group);

    /**
     * Get title of the menu entry.
     *
     * @return  string
     */
    public function getTitle();

    /**
     * Set title of the menu entry.
     *
     * @param   string  $title  a text or a translation token
     * @return  \Yana\Plugins\Menus\IsEntry
     */
    public function setTitle($title);

    /**
     * Get icon path.
     *
     * @return  string
     */
    public function getIcon();

    /**
     * Set icon path.
     *
     * @param   string  $icon  path to image file
     * @return  \Yana\Plugins\Menus\IsEntry
     */
    public function setIcon($icon);

    /**
     * get value of safemode setting.
     *
     * @return  bool
     * @ignore
     */
    public function getSafeMode();

    /**
     * Set safemode setting.
     *
     * @param   bool  $safeMode  true = requires safe-mode, false = disallows safe-mode, null = don't care
     * @return  \Yana\Plugins\Menus\IsEntry
     * @ignore
     */
    public function setSafeMode($safeMode = null);

}

?>