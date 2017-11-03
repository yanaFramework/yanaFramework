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
abstract class AbstractHandler extends \Yana\Core\Object implements \Yana\Log\Errors\IsHandler
{

    /**
     * @var bool
     */
    private $_isActive = false;

    /**
     * @var int
     */
    private $_errorReportingLevel = null;

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
    abstract public function handleError($errorNumber, $description, $file, $lineNumber);

    /**
     * Handles failed assertions.
     *
     * Note: actually in PHP an assertion is treated as an "E_WARNING".
     * There is no such thing as an "E_ASSERT" error level.
     *
     * @param   string  $pathToFile   file
     * @param   int     $lineNumber   line number
     * @param   string  $code         assertion code (note: can be empty)
     * @param   string  $description  optional description
     *
     * @internal NOTE: this function is public for technical reasons. Don't call it yourself.
     */
    abstract public function handleAssertion($pathToFile, $lineNumber, $code, $description = "");

    /**
     * Handles uncaught exceptions.
     *
     * @param  \Throwable|\Exception  $e  some unhandled exception: PHP 7 implements Throwable, PHP 5 does not
     * @ignore
     *
     * @internal NOTE: this function is public for technical reasons. Don't call it yourself.
     */
    abstract public function handleException($e);

    /**
     * Activate or deactive error handler.
     *
     * @param   bool  $isActive  true = activate, false = deactivate
     * @return  $this
     * @codeCoverageIgnore
     */
    public function setActivate($isActive = true)
    {
        if ($this->_isActive != $isActive) {
            if ($isActive) {
                set_error_handler(array($this, 'handleError'));
                set_exception_handler(array($this, 'handleException'));
                assert_options(ASSERT_CALLBACK, array($this, 'handleAssertion'));
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

    /**
     * Local error reporting level.
     *
     * Defaults to native PHP setting returned by error_reporting().
     *
     * @return  int
     */
    public function getErrorReportingLevel()
    {
        if (!is_int($this->_errorReportingLevel)) {
            $errorReportingLevel = (int) error_reporting();
        } else {
            $errorReportingLevel = $this->_errorReportingLevel;
        }
        return $errorReportingLevel;
    }

    /**
     * Overwrite error reporting level.
     *
     * This falls back to PHP's native error_reporting() function.
     * So you don't _have_ to set this manually.
     * But you may - for example when writing unit-tests.
     *
     * @param   int  $newLevel  error reporting level corresponding to PHP error levels
     * @return  $this
     */
    public function setErrorReportingLevel($newLevel)
    {
        assert('is_int($newLevel); // Integer expected.');
        $this->_errorReportingLevel = (int) $newLevel;
        return $this;
    }

}

?>