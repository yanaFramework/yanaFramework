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
 * <<utility>> Smarty abstract resource.
 *
 * This is a resource wrapper class for use with the smarty template engine.
 *
 * @static
 * @access      public
 * @package     yana
 * @subpackage  core
 * @ignore
 */
abstract class SmartAbstractResource extends \Yana\Core\AbstractUtility implements IsSmartResource
{

    /**
     * Resource is secure
     *
     * Return TRUE or FALSE, depending on whether the requested resource is secure or not.
     * This function is used only for template resources but should still be defined.
     *
     * @access  public
     * @static
     * @param   string  $string   the template (as a string)
     * @param   string  &$output  the same template (as output)
     * @return  bool
     */
    public static function isSecure()
    {
        return true;
    }

    /**
     * Resource is trusted
     *
     * Return TRUE or FALSE, depending on whether the requested resource is trusted or not.
     * This function is used for only for PHP script components requested by  {include_php} tag or
     * {insert}  tag with the src attribute.
     * However, it should still be defined even for template resources.
     *
     * @access  public
     * @static
     * @param   string  $string   the template (as a string)
     * @param   string  &$output  the same template (as output)
     * @return  bool
     */
    public static function isTrusted()
    {
        return false;
    }

}

?>