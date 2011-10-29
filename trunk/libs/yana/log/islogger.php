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

namespace Yana\Log;

/**
 * <<Interface>> Classes that are able to maintain logs.
 *
 * @package    yana
 * @subpackage log
 */
interface IsLogger extends IsLogHandler
{
    /**
     * Suggested level for debugging messages.
     */
    const DEBUG = E_STRICT;
    /**
     * Suggested level for noticed messages.
     */
    const INFO = E_USER_NOTICE;
    /**
     * Debugging
     */
    const WARNING = E_USER_WARNING;
    /**
     * Debugging
     */
    const ERROR = E_USER_ERROR;
    /**
     * Failed assertion
     */
    const ASSERT = E_ALL;
    /**
     * Any mesage
     */
    const ALL = E_ALL;

    /**
     * Set new logging level.
     *
     * The logger will only react if the given log-level matches.
     * The log-level is a bitmask, that follows the PHP-rules for error-messages.
     * Example:
     * <code>
     * // React on anything but 
     * $logger->setLogLevel(IsLogger::ALL & ~IsLogger::DEBUG)
     * </code>
     *
     * @param  int  $level  
     */
    public function setLogLevel($level);

    /**
     * Get current logging level.
     *
     * @return  int
     */
    public function getLogLevel();

}

?>