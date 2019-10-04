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
 * <<abstract>> Menu information
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
abstract class AbstractMenu extends \Yana\Core\StdObject implements \Yana\Plugins\Menus\IsMenu
{

    /**
     * @var  array
     */
    private $_names = array();

    /**
     * @var  array
     */
    private $_entries = array();

    /**
     * Adds an entry to a menu of your choice.
     *
     * If no menu-group is specified, the entry is added to the root-level.
     *
     * @param   string                   $action     name of action
     * @param   \Yana\Plugins\Menus\IsEntry  $menuEntry  configuration object
     * @return  $this
     */
    public function setMenuEntry($action, \Yana\Plugins\Menus\IsEntry $menuEntry)
    {
        assert('is_string($action); // Invalid argument $action: string expected');
        \Yana\Util\Hashtable::set($this->_entries, $menuEntry->getGroup() . ".$action", $menuEntry);
        return $this;
    }

    /**
     * remove menu entry
     *
     * Returns bool(true) on success and bool(false) if the entry does not exist.
     *
     * @param   string  $action    name of action
     * @param   string  $menuName  set this entry is inside a menu
     * @return  bool
     */
    public function unsetMenuEntry($action, $menuName = "")
    {
        assert('is_string($action); // Invalid argument $action: string expected');
        assert('is_string($menuName); // Invalid argument $menuName: string expected');
        return \Yana\Util\Hashtable::remove($this->_entries, $menuName . "." . $action);
    }

    /**
     * Set a name for a menu of your choice.
     *
     * @param   string  $menu  menu where entry should be added, blank means root-level
     * @param   string  $name  name of your choice
     * @return  $this
     */
    public function setMenuName($menu, $name = "")
    {
        assert('is_string($menu); // Invalid argument $menu: string expected');
        assert('is_string($name); // Invalid argument $name: string expected');
        $this->_names[$menu] = $name;
        return $this;
    }

    /**
     * get menu entries
     *
     * Returns an array of all entries of a specific menu,
     * or all menus if none is specified, as a associative
     * array.
     *
     * @param   string  $menuName  filter by menu name
     * @return  array
     */
    public function getMenuEntries($menuName = null)
    {
        assert('is_null($menuName) || is_string($menuName); // Wrong type for argument 1. String expected');
        if (empty($menuName)) {
            return $this->_entries;
        } else {
            $result = \Yana\Util\Hashtable::get($this->_entries, $menuName);
            if (is_array($result)) {
                return $result;
            } else {
                return array();
            }
        }
    }

    /**
     * Get menu name.
     *
     * Returns the label of the given menu as a string.
     *
     * @param   string  $menuId  menu id to look up
     * @return  string
     */
    public function getMenuName($menuId)
    {
        assert('is_string($menuId); // Wrong type for argument 1. String expected');
        if (isset($this->_names[$menuId])) {
            return $this->_names[$menuId];
        } else {
            return $menuId;
        }
    }

}

?>