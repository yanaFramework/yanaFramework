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

/**
 * Menu information.
 *
 * This class configurates a menu entry
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 * @ignore
 */
class PluginMenuEntry
{

    /**
     * @access private
     * @var    string
     */
    private $_group = "";

    /**
     * @access private
     * @var    string
     */
    private $_title = "";

    /**
     * @access private
     * @var    string
     */
    private $_icon = "";

    /**
     * @access private
     * @var    bool
     */
    private $_safeMode = null;

    /**
     * Get name of menu group.
     *
     * Groups may have sub-groups, devided by '.'.
     *
     * @access  public
     * @return  int
     */
    public function getGroup()
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
     * @access  public
     * @param   string  $group  
     * @return  PluginMenuEntry
     */
    public function setGroup($group)
    {
        assert('is_string($group); // Invalid argument $group: string expected');
        $this->_group = (string) $group;
        return $this;
    }

    /**
     * Get title of the menu entry.
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set title of the menu entry.
     *
     * @access  public
     * @param   string  $title  a text or a translation token
     * @return  PluginMenuEntry
     */
    public function setTitle($title)
    {
        assert('is_string($title); // Invalid argument $title: string expected');
        $this->_title = (string) $title;
        return $this;
    }

    /**
     * Get icon path.
     *
     * @access  public
     * @return  string
     */
    public function getIcon()
    {
        return $this->_icon;
    }

    /**
     * Set icon path.
     *
     * @access  public
     * @param   string  $icon  path to image file
     * @return  PluginMenuEntry
     */
    public function setIcon($icon)
    {
        assert('is_string($icon); // Invalid argument $icon: string expected');
        $this->_icon = (string) $icon;
        return $this;
    }

    /**
     * get value of safemode setting.
     *
     * @access  public
     * @return  bool
     * @ignore
     */
    public function getSafeMode()
    {
        return $this->_safeMode;
    }

    /**
     * Set safemode setting.
     *
     * @access  public
     * @param   bool  $safeMode  true = requires safe-mode, false = disallows safe-mode, null = don't care
     * @return  PluginMenuEntry
     * @ignore
     */
    public function setSafeMode($safeMode = null)
    {
        assert('is_null($safeMode) || is_bool($safeMode); // Invalid argument $safeMode: bool expected');
        $this->_safeMode = $safeMode;
        return $this;
    }

}

?>