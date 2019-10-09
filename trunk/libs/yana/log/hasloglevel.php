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
 * <<trait>> Adds log-level member and access functions to class.
 *
 * @package    yana
 * @subpackage log
 */
trait HasLogLevel
{

    /**
     * @var int
     */
    private $_logLevel = 0;

    /**
     * Get current logging level.
     *
     * @return  int
     */
    public function getLogLevel()
    {
        return $this->_logLevel;
    }

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
     * @param   int  $level  logging level, following the PHP error bitmask
     * @return  $this
     */
    public function setLogLevel($level)
    {
        assert(is_int($level), 'Invalid argument $level: int expected');
        $this->_logLevel = (int) $level;
        return $this;
    }

    /**
     * Returns TRUE if the logger should react on the given error level.
     *
     * @param   int  $level  logging level, following the PHP error bitmask
     * @return  bool
     */
    protected function _isAcceptable($level)
    {
        assert(is_int($level), 'Invalid argument $level: int expected');

        $reportingLevel = $this->getLogLevel();
        return $reportingLevel <= 0 || (($reportingLevel & ~$level) !== $reportingLevel);
    }

}

?>