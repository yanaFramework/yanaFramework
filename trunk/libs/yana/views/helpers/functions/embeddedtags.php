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
     * Name of color picker template
     *
     * @var string
     */
    private $_templateName = "id:GUI_EMBEDDED_TAGS";

    /**
     * Returns name of color picker template.
     *
     * Default is "id:GUI_EMBEDDED_TAGS".
     *
     * @return  string
     * @ignore
     */
    public function getTemplateName(): string
    {
        return $this->_templateName;
    }

    /**
     * Sets name of color picker template.
     *
     * Default is "id:GUI_EMBEDDED_TAGS".
     *
     * @param   string  $templateName  must be valid template name or file path
     * @return  $this
     * @ignore
     */
    public function setTemplateName(string $templateName)
    {
        $this->_templateName = $templateName;
        return $this;
    }

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
        assert(!isset($params["show"]) || is_string($params["show"]), 'Invalid argument "show": string expected');
        assert(!isset($params["hide"]) || is_string($params["hide"]), 'Invalid argument "hide": string expected');
        $show = array();
        $hide = array();

        /* Argument 'show' */
        if (isset($params['show']) && (!is_string($params['show']) || !preg_match('/^(\w+|\||-)(,(\w+|\||-))*$/is', $params['show']))) {
            $message = "Argument 'show' is not a valid comma-separated list in function " . __FUNCTION__ . "().";
            $this->_getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
            return "";
        } elseif (!isset($params['show'])) {
            $show =& $listOfTags;

        } else {
            $show = explode(',', mb_strtolower($params['show']));

        }

        /* Argument 'hide' */
        if (!empty($params['hide']) && (!is_string($params['hide']) || !preg_match('/^[\w,]+$/is', $params['hide']))) {
            $message = "Argument 'hide' is not a valid comma-separated list in function " . __FUNCTION__ . "().";
            $this->_getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
            return "";
        } elseif (empty($params['hide'])) {
            $hide = array();

        } else {
            $hide = explode(',', mb_strtolower($params['hide']));

        }

        assert(is_array($show), 'is_array($show)');
        assert(is_array($hide), 'is_array($hide)');

        $tags = array_diff($show, $hide);

        /* create document */
        $document = $this->_getViewManager()->createContentTemplate($this->getTemplateName());
        $document->setVar('TAGS', $tags);
        $document->setVar('USER_DEFINED', $this->_getRegistry()->getVar('PROFILE.EMBTAG'));
        $document->setVar('LANGUAGE', $this->_getLanguage()->getVars());

        return $document->fetch();
    }

}

?>