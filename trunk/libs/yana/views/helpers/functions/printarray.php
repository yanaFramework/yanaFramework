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
class PrintArray extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsFunction
{

    /**
     * <<smarty function>> Output array content as HTML.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        if (!isset($params['value'])) {
            return "";
        }
        $array = $params['value'];
        if (is_string($array)) {
            $array = \Yana\Files\SML::decode($array);
        }
        $lDelim = $smarty->smarty->left_delimiter;
        $rDelim = $smarty->smarty->right_delimiter;
        if (is_array($array)) {
            $array = htmlspecialchars(\Yana\Files\SML::encode($array), ENT_COMPAT, 'UTF-8');
            $replacement = '<span style="color: #35a;">$1</span>$2<span style="color: #35a;">$3</span>';

            $array = preg_replace('/(&lt;\w[^&]*&gt;)(.*?)(&lt;\/[^&]*&gt;)$/m', $replacement, $array);
            $replacement = '<span style="color: #607; font-weight: bold;">$0</span>';
            $array = preg_replace('/&lt;[^&]+&gt;\s*$/m', $replacement, $array);

            $pattern = '/' . preg_quote($lDelim, '/') . '\$[\w\.\-_]+' . preg_quote($rDelim, '/') . '/m';
            $array = preg_replace($pattern, '<span style="color: #080;">$0</span>', $array);

            $array = preg_replace('/\[\/?[\w\=]+\]/m', '<span style="color: #800;">$0</span>', $array);
            $array = preg_replace('/&amp;\w+;/m', '<span style="color: #880;">$0</span>', $array);
            $array = "<pre>{$array}</pre>";
        }
        return $array;
    }

}

?>