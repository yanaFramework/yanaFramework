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
 * <<Enumeration>> Contains supported log types.
 *
 * @package    yana
 * @subpackage log
 */
class TypeEnumeration extends \Yana\Core\AbstractEnumeration
{
    /**
     * Not classifyable
     */
    const UNKNOWN = -1;
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
    const EXCEPTION = E_RECOVERABLE_ERROR;
    /**
     * Failed assertion
     */
    const ASSERT = 10;
    /**
     * Any mesage
     */
    const ALL = E_ALL;

}

?>