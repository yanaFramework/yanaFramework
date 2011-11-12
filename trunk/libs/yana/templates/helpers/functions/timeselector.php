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

namespace Yana\Templates\Helpers\Functions;

/**
 * Smarty-compatible function.
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  templates
 */
class TimeSelector extends \Yana\Core\Object implements \Yana\Templates\Helpers\IsFunction
{

    /**
     * <<smarty function>> select time
     *
     * <pre>
     * This function takes the following arguments:
     *
     * string  $name  (mandatory) name attribute of select items
     * string  $attr  (optional)  list of attributes for select element
     * string  $id    (optional)  id attribute of select element
     * int     $time  (optional)  selected timestamp
     * </pre>
     *
     * Returns "[select hour]:[select minute]".
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $item = new \Yana\Forms\Fields\HtmlBuilder();
        if (empty($params['name'])) {
            return "";
        } else {
            $item->setName((string) $params['name']);
        }
        if (!empty($params['id'])) {
            $item->setId((string) $params['id']);
        }
        if (!empty($params['attr'])) {
            $item->setAttr((string) $params['attr']);
        }

        return $item->buildTimeSelector($params);
    }

}

?>