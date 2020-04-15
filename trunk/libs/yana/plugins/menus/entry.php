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
 * Menu information.
 *
 * This class configurates a menu entry
 *
 * @package     yana
 * @subpackage  plugins
 * @ignore
 */
class Entry extends \Yana\Core\StdObject implements \Yana\Plugins\Menus\IsEntry
{

    /**
     * @var  string
     */
    private $_group = "";

    /**
     * @var  string
     */
    private $_title = "";

    /**
     * @var  string
     */
    private $_icon = "";

    /**
     * @var  bool
     */
    private $_safeMode = null;

    /**
     * Get name of menu group.
     *
     * Groups may have sub-groups, devided by '.'.
     *
     * @return  string
     */
    public function getGroup(): string
    {
        return $this->_group;
    }

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
    public function setGroup(string $group)
    {
        $this->_group = $group;
        return $this;
    }

    /**
     * Get title of the menu entry.
     *
     * @return  string
     */
    public function getTitle(): string
    {
        return $this->_title;
    }

    /**
     * Set title of the menu entry.
     *
     * @param   string  $title  a text or a translation token
     * @return  $this
     */
    public function setTitle(string $title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * Get icon path.
     *
     * @return  string
     */
    public function getIcon(): string
    {
        return $this->_icon;
    }

    /**
     * Set icon path.
     *
     * @param   string  $icon  path to image file
     * @return  $this
     */
    public function setIcon(string $icon)
    {
        $this->_icon = $icon;
        return $this;
    }

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
    public function getSafeMode(): ?bool
    {
        return $this->_safeMode;
    }

    /**
     * Set safemode setting.
     *
     * @param   bool|NULL  $safeMode  true = requires safe-mode, false = disallows safe-mode, null = don't care
     * @return  $this
     * @ignore
     */
    public function setSafeMode(?bool $safeMode = null)
    {
        $this->_safeMode = $safeMode;
        return $this;
    }

}

?>