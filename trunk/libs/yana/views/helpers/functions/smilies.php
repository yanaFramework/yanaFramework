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
 * @subpackage  templates
 */
class Smilies extends \Yana\Views\Helpers\Formatters\IconFormatter implements \Yana\Views\Helpers\IsFunction
{

    /**
     * Create a new instance.
     *
     * This also loads the configuration.
     */
    public function __construct()
    {
        parent::__construct();
        
        global $YANA;
    }

    /**
     * <<smarty function>> guiSmilies.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $table = '<table summary="smilies" class="gui_generator_smilies"><tr>';
        $width = (int) $params['width'];
        if ($width < 1) {
            $width = 1;
        }
        $lDelim = $smarty->smarty->left_delimiter;
        $rDelim = $smarty->smarty->right_delimiter;

        foreach (array_keys(self::$_icons) as $count => $icon)
        {
            $text = htmlspecialchars($icon, ENT_COMPAT, 'UTF-8');
            $url = urlencode($icon);
            if ($count % $width == 0 && $count > 0) {
                $table .= '</tr><tr>';
            }
            $table .= '<td><a title="' . $lDelim . "lang id='TITLE_SMILIES'" . $rDelim . '" ' .
                'href="javascript://:' . $url . ':"><img alt="' . $text . '" src="' . self::$_dir . $text .
                '.gif" onmousedown="yanaAddIcon(\':' . $text . ':\',event)"/></a></td>' . "\n";
        }

        return $table . "</tr></table>";
    }

}

?>