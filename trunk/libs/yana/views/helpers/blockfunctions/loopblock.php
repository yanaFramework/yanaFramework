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

namespace Yana\Views\Helpers\Blockfunctions;

/**
 * Smarty-compatible block-function.
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  views
 */
class LoopBlock extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsBlockFunction
{

    /**
     * <<smarty function>> loop through elements of an array
     *
     * This is pretty much the same as Smarty's "foreach" block-function,
     * excepts for the fact that it flattens multidimensional arrays
     * to one-dimension by including the '.' seperator to create compound
     * keys.
     *
     * Example:
     * <code>
     * $array = array('a' = 1, 'b' => array('foo', 'bar'), 'c' = 2);
     *
     * {foreach from=$array item="key" key="element"}
     * {$key} = {$element}
     * {/foreach}
     *
     * the code above will output:
     * a = 1
     * b = Array
     * c = 2
     *
     * Note that 'foreach' converts $element to a string,
     * even if it is was an array.
     *
     * {loop from=$array item="key" key="element"}
     * $key = $element
     * {/loop}
     *
     * the code above will output:
     * a = 1
     * b.0 = foo
     * b.1 = bar
     * c = 2
     *
     * Note that 'loop' does not convert $element to a string
     * if it is an array, but provides a list of it's items instead.
     *
     * You should also note the difference in the writing of the token.
     * While 'foreach' writes {$key} the function 'loop' uses just $key.
     * </code>
     *
     * @param   array                      $params   any list of arguments
     * @param   mixed                      $content  the looped content
     * @param   \Smarty_Internal_Template  $smarty   smarty object reference
     * @param   bool                       &$repeat  the loop will continue while this is TRUE
     * @return  scalar
     */
    public function __invoke(array $params, $content, \Smarty_Internal_Template $smarty, &$repeat)
    {
        if (!is_array($params['from'])) {
            return "";
        }

        if (!$repeat) {
            return self::_loop(@$params['key'], @$params['item'], $params['from'], $content);
        }
    }

    /**
     * Called by main method.
     *
     * @param   string  $key         array key string
     * @param   string  $item        array item string
     * @param   array   &$array      source array
     * @param   string  &$template   template HTML content
     * @return  string
     */
    private function _loop($key, $item, array &$array, &$template)
    {
        $list = '';
        assert('!isset($id); // Cannot redeclare $id');
        assert('!isset($element); // Cannot redeclare $element');
        foreach ($array as $id => $element)
        {
            if (is_array($element)) {
                $list .= self::_loop($key, $item, $element, $template, $id . '.');
            } elseif (is_scalar($element)) {
                $li = $template;
                $li = str_replace('$' . $key, htmlspecialchars($id, ENT_COMPAT, 'UTF-8'), $li);
                if ($element === true) {
                    $li = str_replace('$' . $item, 'true', $li);
                } elseif ($element === false) {
                    $li = str_replace('$' . $item, 'false', $li);
                } else {
                    $li = str_replace('$' . $item, htmlspecialchars($element, ENT_COMPAT, 'UTF-8'), $li);
                }
                $list .= $li;
            }

        } // end foreach
        unset($id, $element);
        return $list;
    }

}

?>