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
     * @return  string
     */
    public function getGroup(): string;

    /**
     * Set name of menu group.
     *
     * Groups may have sub-groups, devided by '.'.
     *
     * Example: foo.bar
     * Meaning, foo is the top-menu, with bar as the second-level entry.
     *
     * @param   string  $group  
     * @return  $this
     */
    public function setGroup(string $group);

    /**
     * Get title of the menu entry.
     *
     * @return  string
     */
    public function getTitle(): string;

    /**
     * Set title of the menu entry.
     *
     * @param   string  $title  a text or a translation token
     * @return  $this
     */
    public function setTitle(string $title);

    /**
     * Get icon path.
     *
     * @return  string
     */
    public function getIcon(): string;

    /**
     * Set icon path.
     *
     * @param   string  $icon  path to image file
     * @return  $this
     */
    public function setIcon(string $icon);

    /**
     * Get value of safemode setting.
     *
     * Returned values may be:
     *
     * - bool(true) = requires safe-mode
     * - bool(false) = disallows safe-mode
     * - null = don't care
     *
     * @return  bool|NULL
     * @ignore
     */
    public function getSafeMode(): ?bool;

    /**
     * Set safemode setting.
     *
     * @param   bool|NULL  $safeMode  true = requires safe-mode, false = disallows safe-mode, null = don't care
     * @return  $this
     * @ignore
     */
    public function setSafeMode(?bool $safeMode = null);

}

?>