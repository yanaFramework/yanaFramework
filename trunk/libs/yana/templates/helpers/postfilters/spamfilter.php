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
 *
 * @ignore
 */

namespace Yana\Templates\Helpers\PostFilters;


/**
 * Smarty-compatible HTML-processors
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  templates
 */
class SpamFilter extends \Yana\Core\Object
{

    /**
     * <<smarty processor>> htmlPostProcessor
     *
     * Adds an invisible dummy-field (honey-pot) to forms for spam protection.
     * If it's filled, it's a bot.
     *
     * @param   string  $source  HTML with PHP source code
     * @return  string
     */
    public function __invoke($source)
    {
        assert('is_string($source); // Wrong type for argument 1. String expected');

        if (!\YanaUser::isLoggedIn()) {
            $replace = "<span class=\"yana_button\"><input type=\"text\" name=\"yana_url\"/></span>\n</form>";
            $source = str_replace("</form>", $replace, $source);
        }

        return $source;
    }

}

?>