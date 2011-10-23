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
 * <<Utility>> Stores loggers and allows to call them all at once.
 *
 * @package    yana
 * @subpackage log
 */
class LogManager extends \Yana\Core\AbstractUtility implements IsLogableClass
{

    /**
     * @var \Yana\Log\LoggerCollection
     */
    private static $_loggers = null;

    /**
     * Adds a logger to the class.
     *
     * @param  \Yana\Log\IsLogger  $logger  instance that will handle the logging
     */
    public static function attachLogger(\Yana\Log\IsLogger $logger)
    {
        $loggers = self::getLogger();
        $loggers[] = $logger;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public static function getLogger()
    {
        if (!isset(self::$_loggers)) {
            self::$_loggers = new LoggerCollection();
        }
        return self::$_loggers;
    }

}

?>