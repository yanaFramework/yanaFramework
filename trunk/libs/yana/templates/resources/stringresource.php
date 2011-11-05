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

namespace Yana\Templates\Resources;

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
 * @package     yana
 * @subpackage  core
 */
class StringResource extends \Yana\Templates\Resources\AbstractResource
{

    /**
     * Fetch template and its modification time from data source.
     *
     * @param  string  $name     template name
     * @param  string  &$source  template source
     * @param  int     &$mtime   template modification timestamp
     */
    protected function fetch($name, &$source, &$mtime)
    {
        assert('is_string($string); // Wrong argument type argument 1. String expected');
        $mtime = time();
        $source = $string;
    }

}

?>