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
 * <<utility>> Smarty id-resource.
 *
 * This is a resource wrapper class for use with the smarty template engine.
 *
 * To register use this code:
 * <code>
 * $smarty->register_resource("id",
 *   array("SmartIdResource::getTemplate",
 *     "SmartIdResource::getTimestamp",
 *     "SmartIdResource::isSecure",
 *     "SmartIdResource::isTrusted"
 *   )
 * );
 * </code>
 *
 * To use the ressource wrapper, call Smarty as follows:
 * <code>
 * $smarty->display("id:template_id");
 * </code>
 *
 * May even be used in templates:
 * <code>
 * {import file="id:template_id"}
 * </code>
 *
 * @static
 * @access      public
 * @package     yana
 * @subpackage  core
 * @ignore
 */
class SmartIdResource extends SmartFileResource
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
     * @param   string  $id       the template id
     * @param   string  &$output  the template source
     * @param   Smarty  $smarty   Smarty template engine
     * @return  bool
     */
    public static function getTemplate($id, &$output, Smarty $smarty)
    {
        assert('is_string($id); // Wrong argument type argument 1. String expected');
        $filename = Skin::getInstance()->getFile($id);
        if (parent::getTemplate($filename, &$output, $smarty)) {
            return true;
        } else {
            $smarty->trigger_error("Invalid template '$id': File '$filename' not found.");
            return false;
        }
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
     * @param   string  $id       the template id
     * @param   string  &$output  the same template (as output)
     * @param   Smarty  $smarty   Smarty template engine
     * @return  bool
     */
    public static function getTimestamp($id, &$output, Smarty $smarty)
    {
        assert('is_string($id); // Wrong argument type argument 1. String expected');
        $filename = Skin::getInstance()->getFile($id);
        return parent::getTimestamp($filename, &$output, $smarty);
    }

}

?>