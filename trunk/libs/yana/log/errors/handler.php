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

namespace Yana\Log\Errors;

/**
 * Class for formatting errors and sending them to a logger.
 *
 * @package     yana
 * @subpackage  log
 */
class Handler extends \Yana\Log\Errors\AbstractHandler
{

    /**
     * @var \Yana\Log\IsLogger
     */
    private $_logger = null;

    /**
     * @var \Yana\Log\Formatter\IsFormatter 
     */
    private $_formatter = null;

    /**
     * Initialize instances.
     *
     * @param  \Yana\Log\Formatter\IsFormatter  $formatter  formats the error messages for output
     * @param  \Yana\Log\IsLogger               $logger     Logs the formatted errors
     */
    public function __construct(\Yana\Log\Formatter\IsFormatter $formatter, \Yana\Log\IsLogger $logger)
    {
        $this->_logger = $logger;
        $this->_formatter = $formatter;
    }

    /**
     * This custom error handler implements logging of errors.
     *
     * Be aware that error logs can get very large very soon, if not reset or deleted frequently.
     * Don't use the logging feature in a productive environment.
     *
     * @param   int     $errorNumber  error number
     * @param   string  $description  description
     * @param   string  $file         path to file
     * @param   int     $lineNumber   line number
     * @ignore
     *
     * @internal NOTE: to trigger an user error inside an user error handler could cause an infinite loop
     *           (and by the way it does'nt make any sense at all). So errors need to be printed out directly.
     *
     * @internal NOTE: this function is public for technical reasons. Don't call it yourself.
     */
    public function handleError($errorNumber, $description, $file, $lineNumber)
    {
        $reportingLevel = $this->getErrorReportingLevel();
        if (($reportingLevel & ~$errorNumber) !== $reportingLevel) {
            $message = $this->_formatter->format($errorNumber, $description, $file, $lineNumber);
            $this->_logger->addLog($message, $errorNumber);

            $this->_exit();
        }
    }

    /**
     * Handles failed assertions.
     *
     * Note: actually in PHP an assertion is treated as an "E_WARNING".
     * There is no such thing as an "E_ASSERT" error level.
     *
     * @param   string  $pathToFile   file
     * @param   int     $lineNumber   line number
     * @param   string  $code         assertion code (note: can be boolean)
     * @param   string  $description  optional description
     *
     * @internal NOTE: this function is public for technical reasons. Don't call it yourself.
     */
    public function handleAssertion($pathToFile, $lineNumber, $code, $description = "")
    {
        // Can't put assertions here. This would risk an infinite loop.
        if (empty($description)) {
            $description = (string) $code;
        }

        $message = $this->_formatter->format(\Yana\Log\TypeEnumeration::ASSERT, $description, $pathToFile, $lineNumber);
        $this->_logger->addLog($message, \Yana\Log\TypeEnumeration::ASSERT);

        $this->_exit();
    }

    /**
     * Handles uncaught exceptions.
     *
     * @param  \Throwable  $e  some unhandled exception
     * @ignore
     *
     * @internal NOTE: this function is public for technical reasons. Don't call it yourself.
     */
    public function handleException(\Throwable $e)
    {
        // Can't throw exceptions here. This would risk an infinite loop.

        // move down to root exception
        $message = "";
        do
        {
            $message .= $this->_formatter->format(
                \Yana\Log\TypeEnumeration::EXCEPTION, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace()
            );
            $e = $e->getPrevious();
        } while ($e);
        $this->_logger->addLog($message, \Yana\Log\TypeEnumeration::EXCEPTION);

        $this->_exit();
    }

    /**
     * Exit the application on error.
     */
    protected function _exit()
    {
        if (ob_get_length() !== false) {
            ob_end_flush();
        }
        exit(1);
    }

}

?>