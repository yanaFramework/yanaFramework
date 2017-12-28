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

namespace Yana\Plugins\Subscriptions;

/**
 * Standard priority queue.
 *
 * Sorted by priority attribute of subscribers.
 *
 * @package     yana
 * @subpackage  plugins
 */
interface IsQueue
{

    /**
     * Get list of IDs for plugins sorted by priority.
     *
     * @return  array
     */
    public function getSubscribers();

    /**
     * Subscribe the given class to this event.
     * 
     * @param   \Yana\Plugins\Configs\IsClassConfiguration  $class  implements the given event as a method
     * @return  $this
     */
    public function subscribe(\Yana\Plugins\Configs\IsClassConfiguration $class);

    /**
     * Unregister an subscribing class.
     * 
     * @param   \Yana\Plugins\Configs\IsClassConfiguration $class  identifies the plugin
     * @return  $this
     */
    public function unsubscribe(\Yana\Plugins\Configs\IsClassConfiguration $class);

}

?>