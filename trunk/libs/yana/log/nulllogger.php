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
 * For testing purposes.
 *
 * @package    yana
 * @subpackage log
 */
class NullLogger extends \Yana\Log\AbstactLogger implements \Yana\Log\IsLogger
{

    /**
     * @var array
     */
    private $_messages = array();

    /**
     * This implements the logging behavior.
     *
     * @param  string  $message  the message that should be reported
     * @param  int     $level    numeric level of severity
     * @param  mixed   $data     any kind of data that helps to understand context in which the message was created
     */
    public function addLog($message, $level = \Yana\Log\TypeEnumeration::INFO, $data = array())
    {
        assert(is_numeric($level), 'Invalid argument $level: numeric expected');

        if ($this->_isAcceptable((int) $level)) {
            $this->_messages[] = array($message, $level, $data);
        }
    }

    /**
     * For use in test-cases and for debugging purposes.
     *
     * @return  array
     */
    public function getLogs()
    {
        return $this->_messages;
    }

}

?>