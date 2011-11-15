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
class Portlet extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsFunction
{

    /**
     * <<smarty function>> Portlet.
     *
     * This function includes a portlet at the chosen point.
     *
     * Expected arguments:
     * <ul>
     * <li> string $action = the action that should be executed </li>
     * <li> string $title = (optional) title of frame </li>
     * <li> string $id = (optional) id of target tag </li>
     * <li> string $args = (optional) list of additional arguments </li>
     * </ul>
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  string
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        if (isset($params['action'])) {
            $url = self::url("action={$params['action']}");
        } else {
            trigger_error("Missing argument 'action' in function " . __FUNCTION__ . "()", E_USER_WARNING);
            return "";
        }
        if (isset($params['title'])) {
            $title = (string) $params['title'];
        } else {
            $title = '';
        }
        if (isset($params['id'])) {
            $id = (string) $params['id'];
        } else {
            $id = uniqid('_' . __FUNCTION__ . '_');
        }
        if (isset($params['args'])) {
            $args = (string) $params['args'];
        } else {
            $args = '';
        }
        return "<script type=\"text/javascript\">yanaPortlet('$url', '$id', '$args', '$title')</script>" .
            "<noscript><iframe class=\"yana_portlet\" src=\"{$url}&amp;$args\"></iframe></noscript>";
    }

}

?>