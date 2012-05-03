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

namespace Yana\Core\Sessions;

/**
 * Session manager.
 *
 * For classes that manage sessions.
 *
 * @package     yana
 * @subpackage  core
 */
class Manager extends \Yana\Core\Object implements \Yana\Core\Sessions\IsManager
{

    /**
     * @var \Yana\Core\Sessions\IsSessionSaveHandler
     */
    private static $_handler = null;

    /**
     * Returns the registered custom save handler or NULL if there is none.
     * 
     * @return  \Yana\Core\Sessions\IsSessionSaveHandler
     */
    protected static function _getSaveHandler()
    {
        return $this->_handler;
    }

    /**
     * Registers a new session save handler.
     *
     * @param  \Yana\Core\Sessions\IsSessionSaveHandler  $handler   new session safe handler
     * @param  bool                                  $autoSave  additionally registers session_write_close() as shutdown function
     */
    public function setSaveHandler(\Yana\Core\Sessions\IsSessionSaveHandler $handler, $autoSave = false)
    {
        // Register a custom session save handler
        session_set_save_handler(array($handler, 'open'), array($handler, 'close'), array($handler, 'read'),
            array($handler, 'write'), array($handler, 'destroy'), array($handler, 'gc'));
        self::$_handler = $handler;

        /* Register session_write_close() as a shutdown function
         *
         * Note: this may not be necessary as PHP by default does it itself, however it can be deactivated in the php.ini,
         *       thus making this option very usefull to enforce the intended behavior.
         */
        if ($autoSave) {
            \register_shutdown_function('session_write_close');
        }

        return $this;
    }

}

?>
