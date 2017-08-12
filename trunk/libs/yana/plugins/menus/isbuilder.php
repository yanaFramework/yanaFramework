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
 * <<interface>> API to create the main application menu.
 *
 * @name        PluginMenu
 * @package     yana
 * @subpackage  plugins
 */
interface IsBuilder
{

    /**
     * Returns the current menu id depending on the current locale settings.
     *
     * Defaults to the selected locale of the application if none was selected.
     *
     * @return  string
     */
    public function getLocale();

    /**
     * Select the locale that should be used to translate the menu entries.
     *
     * The "locale" functions as the "name" of the menu.
     *
     * YES: we wrote earlier that there is only 1 "main application menu",
     * and this is still true.
     * BUT: the items of this "main application menu" may have X translations.
     * Thus the "locale" is required to identify the language that should be used to create it.
     *
     * @param   string  $locale  LOCALE consisting of Language and (optional) country code
     * @return  self
     */
    public function setLocale($locale);

    /**
     * Build main application menu.
     *
     * Note: there is by definition only 1 main application menu.
     * Which makes it a de-facto singleton.
     * Thus calling this builder twice (for the same locale) will give you the same instance.
     *
     * @return  \Yana\Plugins\Menus\IsMenu
     */
    public function buildMenu();

}

?>