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
 * For unit tests only.
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class NullMenuBuilder extends \Yana\Core\StdObject implements \Yana\Plugins\Menus\IsTextMenuBuilder
{

    /**
     * Return menu as associative array.
     *
     * Extracts all menus and items.
     * Array Keys are menu names.
     * Value Keys are URLs, values are text labels.
     *
     * @param   \Yana\Plugins\Menus\IsMenu  $menu  from which to take the entries
     * @return  array
     */
    public function getTextMenu(\Yana\Plugins\Menus\IsMenu $menu): array
    {
        return array();
    }

    /**
     * Translate menu name in selected system locale.
     *
     * @param   string  $menuNameToken  menu name language token
     * @return  string
     */
    public function translateMenuName(string $menuNameToken): string
    {
        return $menuNameToken;
    }

}

?>