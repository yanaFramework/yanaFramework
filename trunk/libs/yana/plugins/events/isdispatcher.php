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

namespace Yana\Plugins\Events;

/**
 * <<interface>> For event dispatching strategies.
 *
 * @package     yana
 * @subpackage  plugins
 */
interface IsDispatcher
{

    /**
     * Notify subscribing plugins of event.
     *
     * Here, to "send an event" actually means "calling a function that serves as an event handler".
     * The function will keep track of events called and the generated results.
     *
     * Note that subscribing plugins may throw exceptions of their own.
     *
     * @param   \Yana\Plugins\Collection                     $subscribers  list of plugins that should be called
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration  $event        identifier of the occured event
     * @return  mixed
     * @throws  \Exception  plugins may throw arbitrary exceptions on failure
     */
    public function sendEvent(\Yana\Plugins\Collection $subscribers, \Yana\Plugins\Configs\IsMethodConfiguration $event);

    /**
     * Get result of last event handler.
     *
     * Returns the result of the last successfully handled event.
     * Returns bool(false) if there was an error.
     * Returns NULL if no event was handled yet.
     *
     * @return  mixed
     */
    public function getLastResult();

    /**
     * Get the previously handled event.
     *
     * Returns the name of the current or previously handled event.
     * If there has been no previous event, the function will return an empty string.
     *
     * @return  string
     */
    public function getLastEvent(): string;

    /**
     * Get the initially handled event.
     *
     * Returns the name of the currently handled event.
     * If there has been no previous event, the function will return an empty string.
     *
     * @return  string
     */
    public function getFirstEvent(): string;

}

?>