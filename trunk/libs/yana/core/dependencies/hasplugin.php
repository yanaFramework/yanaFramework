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

namespace Yana\Core\Dependencies;

/**
 * <<trait>> Plugin sub-system dependencies.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
trait HasPlugin
{

    /**
     * @var \Yana\Plugins\Events\IsDispatcher
     */
    private $_dispatcher = null;

    /**
     * Get instance of event dispatcher.
     *
     * @return  \Yana\Plugins\Events\IsDispatcher
     */
    public function getDispatcher()
    {
        if (!isset($this->_dispatcher)) {
            $this->_dispatcher = new \Yana\Plugins\Events\Dispatcher();
        }
        return $this->_dispatcher;
    }

    /**
     * Set event dispatcher.
     *
     * @param   \Yana\Plugins\Events\IsDispatcher  $dispatcher  will distribute events to subscribing plugins
     * @return  $this
     */
    public function setDispatcher(\Yana\Plugins\Events\IsDispatcher $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }

}

?>