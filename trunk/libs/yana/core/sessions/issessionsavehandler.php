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

namespace Yana\Core\Sessions;

/**
 * <<Interface>> Session handler interface.
 *
 * This class mimics the PHP 5.4.0 SessionHandlerInterface for PHP 5.3.x.
 *
 * @package     yana
 * @subpackage  core
 * @link        http://www.php.net/manual/en/class.sessionhandlerinterface.php
 */
interface IsSessionSaveHandler
{

    /**
     * Re-initialize existing session, or create a new one.
     *
     * Called on session_start(). 
     *
     * @param   string  $savePath   The path where to store/retrieve the session
     * @param   string  $sessionId  A unique identifier
     * @return  bool
     * @link    http://www.php.net/manual/en/sessionhandlerinterface.open.php
     */
    public function open($savePath, $sessionId);

    /**
     * Closes the current session.
     *
     * Automaticaly executed when closing the session, or explicitly via session_write_close().
     *
     * @return  bool
     * @link    http://www.php.net/manual/en/sessionhandlerinterface.close.php
     */
    public function close();

    /**
     * Read session data from session storage and return it.
     *
     * This is usually called after {@see \Yana\Core\Sessions\IsSessionHandler::open()}
     *
     * @param   string  $id  A unique identifier
     * @return  string
     * @link    http://www.php.net/manual/en/sessionhandlerinterface.read.php
     */
    public function read($id);

    /**
     * Write session data.
     * 
     * @param   string  $id    A unique identifier
     * @param   string  $data  the encoded session data
     * @return  bool
     * @link    http://www.php.net/manual/en/sessionhandlerinterface.write.php
     */
    public function write($id, $data);

    /**
     * Destroy a session.
     * 
     * @param   string  $id    A unique identifier
     * @return  bool
     * @link    http://www.php.net/manual/en/sessionhandlerinterface.destroy.php
     */
    public function destroy($id);

    /**
     * Execute Garbage-Collector.
     * 
     * @param   int  $maxlifetime  Sessions not updated for maxlifetime seconds will be removed
     * @return  bool
     * @link    http://www.php.net/manual/en/sessionhandlerinterface.gc.php
     */
    public function gc($maxlifetime);

}

?>
