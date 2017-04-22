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
class Smilies extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsFunction
{

    /**
     * @var \Yana\Views\Helpers\Formatters\IconFormatter
     */
    private $_formatter = null;

    /**
     * Lazy loading for formatter class.
     *
     * @return \Yana\Views\Helpers\Formatters\IconFormatter 
     */
    protected function _getFormatter()
    {
        if (!isset($this->_formatter)) {
            $this->_formatter = new \Yana\Views\Helpers\Formatters\IconFormatter();
        }
        return $this->_formatter;
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

        $iconLoader = new \Yana\Views\Helpers\IconLoader();
        $count = 0;
        foreach ($iconLoader->getIcons() as $text => $icon)
        {
            $text = \Yana\Util\Strings::htmlSpecialChars($text);
            if ($count % $width == 0 && $count > 0) {
                $table .= '</tr><tr>';
            }
            $table .= '<td title="' . $lDelim . "lang id='TITLE_SMILIES'" . $rDelim . '" ' .
                'style="cursor: pointer"><img alt="' . $text . '" src="' . $icon .
                '" onmousedown="yanaAddIcon(\':' . $text . ':\',event)"/></td>' . "\n";
            $count++;
        }

        return $table . "</tr></table>";
    }

}

?>