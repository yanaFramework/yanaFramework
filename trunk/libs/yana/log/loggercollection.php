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
 * <<collection>> Stores loggers and allows to call them all at once.
 *
 * @package    yana
 * @subpackage log
 */
class LoggerCollection extends \Yana\Core\AbstractCollection implements IsLogHandler
{

    /**
     * Store new value in database.
     *
     * @param   scalar              $offset  where to place the value (may also be empty)
     * @param   \Yana\Log\IsLogger  $value   new value to store
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not valid
     * @return  \Yana\Log\IsLogger
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof \Yana\Log\IsLogger) {
            $message = "Instance of IsLogger expected. Found " . gettype($value) . "(" .
                ((is_object($value)) ? get_class($value) : $value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        return $this->_offsetSet($offset, $value);
    }

    /**
     * This implements the logging behavior.
     *
     * @param  string  $message  the message that should be reported
     * @param  int     $level    numeric level of severity
     * @param  mixed   $data     any kind of data that might help to understand context in which the message was created
     */
    public function addLog($message, $level = \Yana\Log\IsLogger::INFO, $data = array())
    {
        foreach ($this as $logger)
        {
            /* @var $logger \Yana\Log\IsLogger */
            $logger->addLog($message, $level, $data);
        }
    }

}