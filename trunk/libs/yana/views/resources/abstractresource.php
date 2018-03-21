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
 * <<utility>> Smarty abstract resource.
 *
 * This is a resource wrapper class for use with the smarty template engine.
 *
 * @package     yana
 * @subpackage  views
 */
abstract class AbstractResource extends \Smarty_Resource_Custom
{

    use \Yana\Views\HasViewDependency;

    /**
     * Fetch template's modification timestamp from data source.
     *
     * Returns the timestamp when the template was modified, or false if not found.
     *
     * @param   string  $name  template name
     * @return  int
     */
    protected function fetchTimestamp($name)
    {
        return null;
    }

}

?>