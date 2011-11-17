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
class Import extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsFunction
{

    /**
     * <<smarty function>> import templates.
     *
     * Import another template.
     * This replaces Smarty's default import function 'include'.
     *
     * In opposite to 'include' this function allows the file parameter
     * to use a relative path and does not force the template designer
     * to work with absolute paths.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  string
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $filename = '';
        if (isset($params['file'])) {

            assert('is_string($params["file"]); // Wrong argument type: file. String expected');
            $filename = $params['file'];
            if (!\Yana\Util\String::startsWith($filename, 'file:')) {
                $filename = 'file:' . $filename;
            }
            unset($params['file']);

        } elseif (isset($params['id'])) {

            assert('is_string($params["id"]); // Wrong argument type: id. String expected');
            $filename = $params['id'];
            if (!\Yana\Util\String::startsWith($filename, 'id:')) {
                $filename = 'id:' . $filename;
            }
            unset($params['id']);

        } else {
            trigger_error("Missing argument. You need to provide either the argument 'file' or 'id'.", E_USER_WARNING);
            return "";

        }

        $document = $this->_getViewManager()->createContentTemplate($filename);
        if (count($params) > 0) {
            $document->setVarsByReference($params);
        }
        $document->setVar('FILE_IS_INCLUDE', true);

        return $document->fetch();
    }

}

?>