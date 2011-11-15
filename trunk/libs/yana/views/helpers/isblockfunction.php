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

namespace Yana\Views\Helpers;

/**
 * <<smarty plugin>> Smarty-compatible blocks.
 * 
 * A block-function is something like {block}content{/block}.
 * It encloses some content, that it operates on.
 *
 * Block-functions are always called at least twice: once when reaching the opening tag,
 * and again for the closing tag.
 *
 * At the first call, $repeat is TRUE and the content is always empty. But this gives you the chance to decide,
 * whether or not you wish to execute the function at all. To abort, set $repeat to false.
 *
 * For the second and any subsequent call, $repeat is FALSE. Set this argument to TRUE and the loop will continue
 * another round.
 *
 * Example that loops through an array:
 * <code>
 *  public static function __invoke(array $params, $template, $smarty, &$repeat)
 *  {
 *      if ($repeat) {
 *          $this->elements = (array) $params['elements'];
 *      } else {
 *          $result = sprintf($content, current($this->elements));
 *          $repeat = (bool) next($this->elements) !== false;
 *          return $result;
 *      }
 *  }
 * </code>
 *
 * @package     yana
 * @subpackage  views
 */
interface IsBlockFunction
{

    /**
     * Function.
     *
     * @param   array                      $params   any list of arguments
     * @param   mixed                      $content  the looped content
     * @param   \Smarty_Internal_Template  $smarty   smarty object reference
     * @param   bool                       &$repeat  the loop will continue while this is TRUE
     * @return  scalar
     */
    public function __invoke(array $params, $content, \Smarty_Internal_Template $smarty, &$repeat);

}

?>