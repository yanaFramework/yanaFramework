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
class Handler extends \Yana\Core\Object implements \Yana\Log\Errors\IsHandler
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
     * @var bool
     */
    private $_isActive = false;

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
    public function _handleError($errorNumber, $description, $file, $lineNumber)
    {
        $reportingLevel = error_reporting();
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
     * @param   string  $description  description
     *
     * @internal NOTE: this function is public for technical reasons. Don't call it yourself.
     *
     * @ignore
     */
    public function _handleAssertion($pathToFile, $lineNumber, $description)
    {
        $message = $this->_formatter->format(\Yana\Log\TypeEnumeration::ASSERT, $description, $pathToFile, $lineNumber);
        $this->_logger->addLog($message, \Yana\Log\TypeEnumeration::ASSERT);

        $this->_exit();
    }

    /**
     * Handles uncaught exceptions.
     *
     * @param  \Exception  $e  some unhandled exception
     * @ignore
     *
     * @internal NOTE: this function is public for technical reasons. Don't call it yourself.
     */
    public function _handleException(\Exception $e)
    {
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

    /**
     * Activate or deactive error handler.
     *
     * @param   bool  $isActive  true = activate, false = deactivate
     * @return  Handler 
     */
    public function setActivate($isActive = true)
    {
        if ($this->_isActive != $isActive) {
            if ($isActive) {
                set_error_handler(array($this, '_handleError'));
                set_exception_handler(array($this, '_handleException'));
                assert_options(ASSERT_CALLBACK, array($this, '_handleAssertion'));
                assert_options(ASSERT_ACTIVE, 1); // activates assertions
                assert_options(ASSERT_QUIET_EVAL, 0); // surpresses warnings for errors in assertion code
            } else {
                assert_options(ASSERT_ACTIVE, 0); // deactivates assertions
                restore_error_handler();
                restore_exception_handler();
            }
            $this->_isActive = (bool) $isActive;
        }
        return $this;
    }

}

?>