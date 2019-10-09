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
     * Return logger to pass formatted log entries to.
     *
     * @return  \Yana\Log\IsLogger
     */
    protected function _getLogger()
    {
        return $this->_logger;
    }

    /**
     * Return instance to format log entries.
     *
     * @return  \Yana\Log\Formatter\IsFormatter 
     */
    protected function _getFormatter()
    {
        return $this->_formatter;
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
    public function handleError(int $errorNumber, string $description, string $file, int $lineNumber)
    {
        $reportingLevel = $this->getErrorReportingLevel();
        if (($reportingLevel & ~$errorNumber) !== $reportingLevel) {
            $message = $this->_getFormatter()->format($errorNumber, $description, $file, $lineNumber);
            $this->_getLogger()->addLog($message, $errorNumber);

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
     * @param   scalar  $code         assertion code (note: can be boolean)
     * @param   string  $description  optional description
     *
     * @internal NOTE: this function is public for technical reasons. Don't call it yourself.
     */
    public function handleAssertion(string $pathToFile, int $lineNumber, $code, string $description = "")
    {
        // Can't put assertions here. This would risk an infinite loop.
        if (empty($description) && \is_scalar($code)) {
            $description = (string) $code;
        }

        $message = $this->_getFormatter()->format(\Yana\Log\TypeEnumeration::ASSERT, $description, $pathToFile, $lineNumber);
        $this->_getLogger()->addLog($message, \Yana\Log\TypeEnumeration::ASSERT);

        $this->_exit();
    }

    /**
     * Handles uncaught exceptions.
     *
     * @param  \Throwable  $e  some unhandled exception: PHP 7 implements Throwable, PHP 5 does not
     * @ignore
     *
     * @internal NOTE: this function is public for technical reasons. Don't call it yourself.
     */
    public function handleException(\Throwable $e)
    {
        // Can't throw exceptions here. This would risk an infinite loop.

        // @codeCoverageIgnoreStart
        if ($e instanceof \AssertionError) { // This exception is new as of PHP 7

            /* This should not be reachable, except for when there is no assertion callback function registered.
             * Usually the framework always registers a callback function, but we are not the only one with code around here.
             * So since we cannot know for sure, we leave this code here as a fallback.
             */
            return $this->handleAssertion($e->getFile(), $e->getLine(), (string) $e->getCode(), (string) $e->getMessage());
        }
        // @codeCoverageIgnoreEnd

        // move down to root exception
        $message = "";
        do
        {
            $message .= $this->_getFormatter()->format(
                \Yana\Log\TypeEnumeration::EXCEPTION, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace()
            );
            $e = $e->getPrevious();
        } while ($e);
        $this->_getLogger()->addLog($message, \Yana\Log\TypeEnumeration::EXCEPTION);

        $this->_exit();
    }

    /**
     * Exit the application on error.
     * @codeCoverageIgnore
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