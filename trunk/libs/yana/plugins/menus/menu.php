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
 * Provides menu information.
 *
 * @name        PluginMenu
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class Menu extends \Yana\Plugins\Menus\AbstractMenu
{

    /**
     * @var  \Yana\Plugins\Menus\IsTextMenuBuilder
     */
    private $_textMenuBuilder = null;

    /**
     * @param  \Yana\Plugins\Menus\IsTextMenuBuilder  $builder  inject builder class here
     */
    public function __construct(\Yana\Plugins\Menus\IsTextMenuBuilder $builder)
    {
        $this->_textMenuBuilder = $builder;
    }

    /**
     * Returns the injected text-builder-instance.
     *
     * @return  \Yana\Plugins\Menus\IsTextMenuBuilder
     */
    protected function _getTextMenuBuilder()
    {
        return $this->_textMenuBuilder;
    }

    /**
     * Get menu name.
     *
     * Returns the label of the given menu as a string.
     * If the menu is unknown the id is returned instead.
     *
     * @param   string  $menuId  menu id to look up
     * @return  string
     */
    public function getMenuName($menuId)
    {
        return $this->_getTextMenuBuilder()->translateMenuName(parent::getMenuName($menuId));
    }

    /**
     * return menu as associative array
     *
     * Extracts all menus and items.
     * Array Keys are menu names.
     * Value Keys are URLs, values are text labels.
     *
     * @return  array
     */
    public function getTextMenu()
    {
        return $this->_getTextMenuBuilder()->getTextMenu($this);
    }

}

?>