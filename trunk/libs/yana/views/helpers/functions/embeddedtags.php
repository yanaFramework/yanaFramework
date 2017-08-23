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
class EmbeddedTags extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsFunction
{

    /**
     * <<smarty function>> Output a HTML bar to select elements for emb-tags markup.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $listOfTags = array('b','i','u','h','emp','c','small','big','hide',
                            'code','img','url','mail','color','mark','smilies');
        assert('!isset($params["show"]) || is_string($params["show"]); // Invalid argument "show": string expected');
        assert('!isset($params["hide"]) || is_string($params["hide"]); // Invalid argument "hide": string expected');
        $show = array();
        $hide = array();

        /* Argument 'show' */
        if (isset($params['show']) && !preg_match('/^(\w+|\||-)(,(\w+|\||-))*$/is', $params['show'])) {
            $message = "Argument 'show' contains illegal characters in function " . __FUNCTION__ . "().";
            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
            return "";
        } elseif (!isset($params['show'])) {
            $show =& $listOfTags;

        } else {
            $show = explode(',', mb_strtolower($params['show']));

        }

        /* Argument 'hide' */
        if (!empty($params['hide']) && !preg_match('/^[\w,]+$/is', $params['hide'])) {
            $message = "Argument 'hide' contains illegal characters for function " . __FUNCTION__ . "().";
            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
            return "";
        } elseif (empty($params['hide'])) {
            $hide = array();

        } else {
            $hide = explode(',', mb_strtolower($params['hide']));

        }

        assert('is_array($show);');
        assert('is_array($hide);');

        $tags = array_diff($show, $hide);

        /* create document */
        assert('!isset($builder); // Cannot redeclare var $builder');
        assert('!isset($application); // Cannot redeclare var $application');
        $builder = new \Yana\ApplicationBuilder();
        $application = $builder->buildApplication();
        unset($builder);
        $document = $smarty->smarty->createTemplate("id:GUI_EMBEDDED_TAGS", null, null, $smarty);
        $document->assign('TAGS', $tags);
        $document->assign('USER_DEFINED', $application->getVar('PROFILE.EMBTAG'));
        $document->assign('LANGUAGE', $application->getLanguage()->getVars());

        return $document->fetch();
    }

}

?>