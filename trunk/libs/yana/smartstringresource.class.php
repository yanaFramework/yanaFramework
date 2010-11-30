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

/**
 * <<utility>> Smarty string-resource.
 *
 * This is a resource wrapper class for use with the smarty template engine.
 *
 * To register use this code:
 * <code>
 * $smarty->register_resource("string",
 *   array("SmartStringResource::getTemplate",
 *     "SmartStringResource::getTimestamp",
 *     "SmartStringResource::isSecure",
 *     "SmartStringResource::isTrusted"
 *   )
 * );
 * </code>
 *
 * To use the ressource wrapper, call Smarty as follows:
 * <code>
 * $template = file_get_contents('foo.tpl');
 * $smarty->display("string:$template");
 * </code>
 *
 * May even be used in templates:
 * <code>
 * {import file="string:$template"}
 * </code>
 *
 * @static
 * @access      public
 * @package     yana
 * @subpackage  core
 * @ignore
 */
class SmartStringResource extends SmartAbstractResource
{

    /**
     * Retrieve the resource
     *
     * Second parameter is a variable passed by reference where the result should be stored.
     * The function is supposed to return TRUE if it was able to successfully retrieve the
     * resource and FALSE otherwise.
     *
     * @access  public
     * @static
     * @param   string  $string   the template (as a string)
     * @param   string  &$output  the same template (as output)
     * @param   Smarty  $smarty   Smarty template engine
     * @return  bool
     */
    public static function getTemplate($string, &$output, Smarty $smarty)
    {
        assert('is_string($string); // Wrong argument type argument 1. String expected');
        $output = (string) $string;
        return true;
    }

    /**
     * Retrieve last modification time of the requested resource
     *
     * Second parameter is a variable passed by reference where the timestamp should be stored.
     * The function is supposed to return TRUE if the timestamp could be succesfully determined,
     * or FALSE otherwise.
     *
     * @access  public
     * @static
     * @param   string  $string   the template (as a string)
     * @param   string  &$output  the same template (as output)
     * @param   Smarty  $smarty   Smarty template engine
     * @return  bool
     */
    public static function getTimestamp($string, &$output, Smarty $smarty)
    {
        assert('is_string($string); // Wrong argument type argument 1. String expected');
        $output = time();
        return true;
    }

}

?>