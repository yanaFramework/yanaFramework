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

namespace Yana\Views\Helpers\Functions;

/**
 * Smarty-compatible function.
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  views
 */
class UnorderedList extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsFunction
{

    /**
     * <<smarty function>> Print an array using a tree menu.
     *
     * This function takes the following arguments:
     * <ul>
     *  <li> array  $value         list contents (possibly multi-dimensional) </li>
     *  <li> bool   $allow_html    allow HTML values (defaults to false) </li>
     *  <li> bool   $keys_as_href  convert array keys to links (defaults to false) </li>
     *  <li> int    $layout        you may choose between layout 1 through 3 (defaults to 1) </li>
     * </ul>
     *
     * Example for $value:
     * <code>
     * $A = array(
     *     '1.html' => 'Link',
     *     'Menu 1' => array(
     *         '2_1.html' => '1) Entry',
     *         '2_2.html' => '2) Entry',
     *         'Menü 2' => array(
     *             '2_3_1.html' => '1) Entry',
     *             '2_3_2.html' => '2) Entry'
     *         ),
     *     ),
     *     'Menu 3' => array(
     *         '3_1.html' => '1) Entry',
     *         '3_2.html' => '2) Entry',
     *         '3_2.html' => '3) Entry'
     *     ),
     * );
     * </code>
     *
     * If you set the argument 'keys_as_href' to true, the tree menu will create links with the array
     * values as link text and the array keys as hrefs.
     * Links are only created if the value is scalar, which means you may create sub-menues by using
     * another array as the value.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  string
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        if (!isset($params['value']) || !is_array($params['value'])) {
            return ""; // Parameter is mandatory
        } else {
            $array = $params['value'];
        }

        $layout = 1;
        if (!empty($params['layout'])) {
            $layout = (int) $params['layout'];
        }
        /* @var $menuHelper \Yana\Views\Helpers\Html\MenuHelper */
        $menuHelper = \Yana\Views\Helpers\Html\MenuHelper::factory($layout);

        // set arguments and call function
        return $menuHelper
            ->setUseKeys((int) !empty($params['keys_as_href']))
            ->setAllowHtml(!empty($params['allow_html']))
            ->__invoke($array);
    }

}

?>