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
 */

namespace Yana;

/**
 * Plugin
 *
 * This is an interface that all plugins must implement.
 * Each subclass should implement at least the default event handler.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsPlugin
{
    /**
     * default event handler
     *
     * This is a dummy-implementation. Overwrite this in your
     * sub-classes as you see fit.
     *
     * @param    string  $event  identifier of the occured event
     * @param    array   $ARGS   list of arguments
     * @return   bool
     */
    public function catchAll($event, array $ARGS);
}

?>
