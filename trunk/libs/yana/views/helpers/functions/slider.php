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
class Slider extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsFunction
{

    /**
     * <<smarty function>> slider
     *
     * This function includes a portlet at the chosen point.
     *
     * Expected arguments:
     * <ul>
     * <li> string          $inputName       =  name of inut element </li>
     * <li> string          $id              =  A unique ID of the Element. </li>
     * <li> integer         $width           =  The value length of the element. </li>
     * <li> integer|float   $min             =  The expected lower bound for the elementÃ¢â‚¬â„¢s value. </li>
     * <li> integer|float   $max             =  The expected upper bound for the elementÃ¢â‚¬â„¢s value. </li>
     * <li> integer|float   $step            =  Specifies the value granularity of the elementÃ¢â‚¬â„¢s value. </li>
     * <li> integer|float   $value           =  Default value for set the start point of the element. </li>
     * <li> string          $backgroundColor =  background-color of the slider
     *                                          (if no one choosen default will be use) </li>
     * </ul>
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        assert('is_string($params["inputName"]); // Invalid argument $params["inputName"]: string expected');

        $htmlResult = "";

        /* create document */
        if (isset($params['inputName'])) {
            $document = $this->_getViewManager()->createContentTemplate("id:gui_slider");
            $sliderId = uniqid(__FUNCTION__ . '_');
            $document->setVar('sliderId', $sliderId);
            // check if the width is set, otherwise the min width will be set to default
            if (isset($params['width'])) {
                $width = (int) $params['width'];
            } else {
                $width = 0;
            }
            $document->setVar('width', $width);
            // if the minimum value does not set, 0 will be choosen
            if (isset($params['min'])) {
                $min = (float) $params['min'];
            } else {
                $min = 0;
            }
            $document->setVar('min', $min);
            // if the maximum value does not set, 1 will be choosen
            if (isset($params['max'])) {
                $max = (float) $params['max'];
            } else {
                $max = 1;
            }
            $document->setVar('max', $max);
            if (isset($params['step'])) {
                $step = (float) $params['step'];
            } else {
                $step = 1;
            }
            $document->setVar('step', $step);
            if (isset($params['backgroundColor'])) {
                $backgroundColor = (string) $params['backgroundColor'];
            } else {
                $backgroundColor = '';
            }
            $document->setVar('background', $backgroundColor);
            if (isset($params['value'])) {
                $value = (float) $params['value'];
            } else {
                $value = $min;
            }
            $document->setVar('value', $value);
            $inputName = (string) $params['inputName'];
            $document->setVar('inputName', $inputName);
            $htmlResult = (string) $document;
        }
        return $htmlResult;
    }

}

?>