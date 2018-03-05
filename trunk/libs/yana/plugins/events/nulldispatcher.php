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
 * Null object for unit tests.
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class NullDispatcher extends \Yana\Plugins\Events\Dispatcher
{

    /**
     * Always returns bool(true).
     *
     * @param   \Yana\IsPlugin                               $subscriber  implements event handler
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration  $event       describes the call interface of the event
     * @return  bool
     */
    protected function _sendEvent(\Yana\IsPlugin $subscriber, \Yana\Plugins\Configs\IsMethodConfiguration $event)
    {
        return true;
    }

}

?>