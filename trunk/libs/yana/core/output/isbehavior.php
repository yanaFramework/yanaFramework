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
declare(strict_types=1);

namespace Yana\Core\Output;

/**
 * <<interface>> Helps the application class to handle output behavior.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsBehavior
{

    /**
     * Provides GUI from current data.
     *
     * Returns the name of the action to call next (if any).
     * Returns NULL if there is no such action.
     *
     * @return  string
     */
    public function outputResults() : ?string;

    /**
     * Output relocation request.
     *
     * This will flush error messages and warnings to the screen
     * and tell the client (i.e. a browser) to relocate, so that the given action can be executed.
     *
     * You may use the special event 'null' to prevent the framework from handling an event.
     *
     * @param  string  $action  relocate here
     * @param   array  $args    with these arguments
     */
    public function relocateTo(string $action, array $args);

}

?>