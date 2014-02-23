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

namespace Yana\Views\Helpers\PreFilters;

/**
 * Smarty-compatible HTML-processors
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  views
 */
class AjaxBridgeFilter extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsPreFilter
{

    /**
     * <<smarty processor>> htmlPreProcessor
     *
     * Create initialization for JavaScript/Ajax bridge
     *
     * @param   string                    $source         HTML source
     * @param   Smarty_Internal_Template  $templateClass  template class
     * @return  string
     */
    public function __invoke($source, \Smarty_Internal_Template $templateClass)
    {
        assert('is_string($source); // Wrong type for argument 1. String expected');

        if (mb_strpos($source, '<head') > -1) {
            assert('!isset($script); // Cannot redeclare var $script');
            $script = "\n        " . '<script type="text/javascript" language="javascript"><!--' . "\n" .
                '        window.yanaProfileId="' . \Yana::getId() . '";' . "\n" .
                '        window.yanaSessionName="{$SESSION_NAME}";' . "\n" .
                '        window.yanaSessionId="{$SESSION_ID}";' . "\n" .
                '        window.yanaLanguage="' . \Yana::getInstance()->getLanguage()->getLocale() . '";' . "\n" .
                '        var src="";' . "\n" .
                '        var php_self="' . $templateClass->getTemplateVars('PHP_SELF') . '";' . "\n" .
                '        //--></script>';
            $source = preg_replace('/<head(>| [^\/>]*>)/', '${0}' . $script, $source);
            unset($script);
        }
        return $source;
    }

}

?>