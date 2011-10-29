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

namespace Yana\Log\Formatter;

/**
 * This does not format anything - use it for your test cases.
 *
 * @package    yana
 * @subpackage log
 */
class NullFormatter extends \Yana\Log\Formatter\AbstractFormatter
{

    /**
     * Format error messages.
     *
     * @param   int     $level        error level
     * @param   string  $description  description
     * @param   string  $filename     file
     * @param   int     $lineNumber   line number
     * @param   array   $trace        the error backtrace as returned by debug_backtrace()
     * @return  string
     */
    public function format($level, $description, $filename = "", $lineNumber = 0, array $trace = array())
    {
        // intentionally left blank
    }

}

?>