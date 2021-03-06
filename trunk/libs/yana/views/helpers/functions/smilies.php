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
declare(strict_types=1);

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
     * <<smarty function>> guiSmilies.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $table = '<table summary="smilies" class="gui_generator_smilies"><tr>';
        $width = isset($params['width']) ? (int) $params['width'] : 1;
        if ($width < 1) {
            $width = 1;
        }
        $configuration = $this->_getDependencyContainer()->getTemplateConfiguration();
        $lDelim = $configuration['leftdelimiter'];
        assert(!empty($lDelim));
        $rDelim = $configuration['rightdelimiter'];
        assert(!empty($rDelim));

        $count = 0;
        foreach ($this->_buildListOfIcons() as $icon)
        {
            /* @var $icon \Yana\Views\Icons\IsFile */
            $text = \Yana\Util\Strings::htmlSpecialChars((string) $icon->getId());
            if ($count % $width == 0 && $count > 0) {
                $table .= '</tr><tr>';
            }
            $table .= '<td title="' . $lDelim . "lang id='TITLE_SMILIES'" . $rDelim . '" ' .
                'style="cursor: pointer"><img alt="' . $text . '" src="' . $icon->getPath() .
                '" onmousedown="yanaAddIcon(\':' . $text . ':\',event)"/></td>' . "\n";
            $count++;
        }

        return $table . "</tr></table>";
    }

    /**
     * Returns a list of available icon files.
     *
     * The list is build from the profile configuration on demand.
     *
     * @return  \Yana\Views\Icons\Collection
     * @codeCoverageIgnore
     */
    protected function _buildListOfIcons()
    {
        $iconLoader = new \Yana\Views\Icons\Loader();
        return $iconLoader->getIcons();
    }

}

?>