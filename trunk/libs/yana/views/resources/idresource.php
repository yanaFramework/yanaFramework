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

namespace Yana\Views\Resources;

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
 * @package     yana
 * @subpackage  core
 */
class IdResource extends \Yana\Views\Resources\FileResource
{

    /**
     * Retrieve the resource.
     *
     * @param string  $id       template name
     * @param string  &$source  template source
     * @param integer &$mtime   template modification timestamp (epoch)
     */
    protected function fetch($id, &$output, &$mtime)
    {
        assert('is_string($id); // Wrong argument type argument 1. String expected');
        $filename = \Yana::getInstance()->getSkin()->getFile($id);
        $smarty->assign('BASEDIR', dirname($filename));
        parent::fetch($filename, $output, $mtime);
    }

    /**
     * Retrieve last modification time of the requested resource
     *
     * @param   string  $id       the template id
     * @return  int
     */
    protected function fetchTimestamp($id)
    {
        assert('is_string($id); // Wrong argument type argument 1. String expected');
        $filename = \Yana::getInstance()->getSkin()->getFile($id);
        return parent::fetchTimestamp($filename);
    }

}

?>