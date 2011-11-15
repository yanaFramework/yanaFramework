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
class Captcha extends \Yana\Core\Object implements \Yana\Views\Helpers\IsFunction
{

    /**
     * <<smarty function>> Captcha.
     *
     * Inserts a captcha image into current template.
     * Note: you still need to check the value of this
     * in your script, otherwise it will have no effect.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $id = '';
        if (isset($params['id']) && is_string($params['id'])) {
            $id = ' id="' . htmlspecialchars($params['id'], ENT_COMPAT, 'UTF-8') . '"';
        }
        $index = rand(1, 9);

        global $YANA;
        $title = "";
        if (isset($YANA)) {
            $title = $YANA->getLanguage()->getVar('SECURITY_IMAGE.DESCRIPTION');
        }
        $formatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();

        return '<input type="hidden" name="security_image_index" value="' . $index . '"/>' .
            '<img alt="" hspace="5" src="' .
            $formatter("action=security_get_image&security_image_index={$index}", false, false) . '"/>' .
            '<input maxlength="5" size="5"' . $id . ' title="' . $title . '" type="text" name="security_image"/>';
    }

}

?>