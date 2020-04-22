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
class ColorPicker extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsFunction
{

    /**
     * Name of color picker template
     *
     * @var string
     */
    private $_templateName = "id:colorpicker";

    /**
     * Returns name of color picker template.
     *
     * Default is "id:colorpicker".
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
     * Default is "id:colorpicker".
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
     * <<smarty function>> Creates Javascript color picker.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $template = $this->_getViewManager()->createContentTemplate($this->getTemplateName());
        if (isset($params['id']) && \is_string($params['id'])) {
            $template->setVar('target', $params['id']);
        }
        return $template->fetch();
    }

}

?>