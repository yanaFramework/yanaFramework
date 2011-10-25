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

/**
 * <<utility>> static class for error handling and debugging
 *
 * This class provides static methods to set the current error
 * reporting level, set the way how errors are reported -
 * wether as text messages on the screen or (hidden from visitors)
 * in log files - and to format and output reported errors.
 *
 * The center of interest for a common audience should be
 * how to enable or disable error reporting for debug purposes.
 *
 * @name        ErrorUtility
 * @package     yana
 * @subpackage  error_reporting
 */
class ErrorUtility extends \Yana\Core\AbstractUtility
{

    /**
     * Temporary helper function until functionality is transfered to a logger class.
     *
     * @return \Yana\Log\Formatter\HtmlFormatter 
     */
    private static function _getFormatter()
    {
        return new \Yana\Log\Formatter\HtmlFormatter();
    }

    /**
     * custom error handler
     *
     * This function creates colorfull error messages that should provide
     * better readability.
     *
     * @param   int     $errorNumber   error number
     * @param   string  $description   description
     * @param   string  $file          file
     * @param   int     $lineNumber    line number
     * @ignore
     */
    public static function printError($errorNumber, $description, $file, $lineNumber)
    {
        /* NOTE: to trigger an user error inside an user error handler could cause an infinite loop
         * (and by the way it does'nt make any sense at all).
         * So errors need to be printed out directly.
         */

        /* to check error reporting levels fall back to PHP's default settings */
        switch ($errorNumber)
        {
            case E_USER_ASSERT:
                $errorLevel = E_USER_ERROR;
                break;
            case E_UNKNOWN_ERROR:
                $errorLevel = E_NOTICE;
                break;
            default:
                $errorLevel = $errorNumber;
                break;
        }

        $errorReporting = error_reporting();
        /* print an error only if the current error reporting level is high enough */
        if (($errorReporting & ~$errorLevel) !== $errorReporting) {
            $formatter = self::_getFormatter();
            print $formatter($errorNumber, $description, $file, $lineNumber, true);

            /* exit on error */
            if (ob_get_length() !== false) {
                ob_end_flush();
            }
            exit(1);
        }
    }

    /**
     * custom error handler
     *
     * this implements logging of errors
     *
     * Be aware that error logs can get very large very soon, if not reset or deleted frequently.
     * Don't use the logging feature in a productive environment.
     *
     * As of PHP 5 there is an additional error reporting level called E_STRICT to document PHP 4
     * features that have been marked deprecated in PHP 5. But some of those 'deprecated features'
     * are still needed for backwards compatibility reasons. So using 'E_STRICT' will posssibly
     * flood your error logs or even crash the script.
     *
     * @param   int     $error_nr       error number
     * @param   string  $description    description
     * @param   string  $file           file
     * @param   int     $line_nr        line number
     * @ignore
     */
    public static function logError($error_nr, $description, $file, $line_nr)
    {
        /* NOTE: to trigger an user error inside an user error handler could cause an infinite loop
         * (and by the way it does'nt make any sense at all).
         * So errors need to be printed out directly.
         */
        if (!is_int($error_nr)) {
            $message = "Error: Illegal argument type for argument 1 in " . __METHOD__ .
                "(). Integer expected, found '" . gettype($error_nr) . "' instead.";
            print $message;
            exit(1);
        }
        if (!is_string($description)) {
            $message = "Error: Illegal argument type for argument 2 in " . __METHOD__ .
                "(). String expected, found '" . gettype($description) . "' instead.";
            print $message;
            exit(1);
        }
        if (!is_string($file)) {
            $message = "Error: Illegal argument type for argument 3 in " . __METHOD__ .
                "(). String expected, found '" . gettype($file) . "' instead.";
            print $message;
            exit(1);
        }
        if (!is_int($line_nr)) {
            $message = "Error: Illegal argument type for argument 4 in " . __METHOD__ .
                "(). Integer expected, found '" . gettype($line_nr) . "' instead.";
            print $message;
            exit(1);
        }

        /* to check error reporting levels fall back to PHP's default settings */
        if ($error_nr === E_USER_ASSERT) {
            $error_level = E_USER_WARNING;
        } elseif ($error_nr === E_UNKNOWN_ERROR) {
            $error_level = E_NOTICE;
        } else {
            $error_level = $error_nr;
        }

        $error_reporting = error_reporting();

        /* log an error only if the current error reporting level is high enough */
        if (($error_reporting & ~$error_level) !== $error_reporting) {

            assert('!isset($error_log); // Cannot redeclare var $error_log');
            $formatter = self::_getFormatter();
            $error_log = $formatter($error_nr, $description, $file, $line_nr, false);
            $filename = 'cache/error.log';
            $log = Log::getLogFromMessage($error_log);
            unset($error_log);

            $errorMessage = "";

            /* the result is already some message or status */
            if (is_scalar($log)) {

                $errorMessage = (string) $log;

                /*
                 * The result is an array, possibly containing a message
                 * and additional information describing the circumstances
                 * in which the error occured.
                 */
            } elseif (is_array($log)) {

                $log['TIME'] = date('r');
                foreach ($log as $label => $value)
                {
                    $errorMessage .= $label;
                    for ($i = mb_strlen($label); $i < 15; $i++)
                    {
                        $errorMessage .= ' ';
                    }
                    $errorMessage .= $value . "\n";
                }

                /* If the result is something unexpected output a default message. */
            } else {

                $errorMessage = "The program encountered an unknown error.";
            }

            /* output the error message to a log file */
            if (error_log($errorMessage . "\n", 3, $filename) === false) {
                print "Cannot write to file '$filename'.";
                exit(1);
            }
        }
    }

    /**
     * handle failed assertions
     *
     * Note: actually in PHP an assertion is treated as an "E_WARNING".
     * There is no such thing like an "E_ASSERT" error level.
     *
     * @param   string  $file         file
     * @param   int     $line_nr      line number
     * @param   string  $description  description
     * @ignore
     */
    public static function logAssertion($file, $line_nr, $description)
    {
        if (!is_string($file)) {
            $message = "Error: Illegal argument type for argument 1 in " . __METHOD__ .
                "(). String expected, found '" . gettype($file) . "' instead.";
            print $message;
            exit(1);
        }
        if (!is_int($line_nr)) {
            $message = "Error: Illegal argument type for argument 2 in " . __METHOD__ .
                "(). Integer expected, found '" . gettype($line_nr) . "' instead.";
            print $message;
            exit(1);
        }
        if (!is_string($description)) {
            $message = "Error: Illegal argument type for argument 3 in " . __METHOD__ .
                "(). String expected, found '" . gettype($description) . "' instead.";
            print $message;
            exit(1);
        }
        ErrorUtility::logError(E_USER_ASSERT, $description, $file, $line_nr);
    }

    /**
     * print failed assertions
     *
     * @param   string  $file         file
     * @param   int     $line_nr      line number
     * @param   string  $description  description
     * @ignore
     */
    public static function printAssertion($file, $line_nr, $description)
    {
        $formatter = self::_getFormatter();
        print $formatter(E_USER_ASSERT, $description, $file, $line_nr, true);
    }

    /**
     * Set error reporting level
     *
     * You may use the following constants:
     * <ol>
     * <li>  YANA_ERROR_ON = catch all errors and print to screen  </li>
     * <li>  YANA_ERROR_OFF = do not report any errors or messages  </li>
     * <li>  YANA_ERROR_LOG = write errors and messages to a log file  </li>
     * </ol>
     *
     * @name    ErrorUtility::setErrorReporting()
     * @param   string    $errorLevel    examples: YANA_ERROR_OFF, YANA_ERROR_LOG,
     *                                   YANA_ERROR_ON or E_ALL, E_ALL & ~E_NOTICE
     */
    public static function setErrorReporting($errorLevel)
    {
        /* Note: method overloading is not available in PHP. (like Perl, but unlike C++ or Java)
         * The following work-around is a common way to simulate that feature.
         */

        if (!is_string($errorLevel)) {
            $message = "Illegal argument type. String expected, found '" . gettype($errorLevel) . "' instead.";
            throw new \ErrorException($message, E_USER_ERROR);
        } elseif (defined('YANA_ERROR_REPORTING') && $errorLevel !== YANA_ERROR_REPORTING) {
            $message = 'Error reporting level has already been set and can not be redefined.';
            throw new \ErrorException($message, E_USER_WARNING);
        }

        switch ($errorLevel)
        {
            case YANA_ERROR_LOG:
                /*
                 * Write errors to a log file, rather than passing them to a browser.
                 */
                if (defined('E_STRICT')) {
                    error_reporting(E_ALL & ~E_STRICT);
                } else {
                    error_reporting(E_ALL);
                }
                set_error_handler(array(__CLASS__, 'logError'));
                assert_options(ASSERT_ACTIVE, 1);
                assert_options(ASSERT_CALLBACK, array(__CLASS__, 'logAssertion'));
                assert_options(ASSERT_BAIL, 0);
                assert_options(ASSERT_WARNING, 0);
                assert_options(ASSERT_QUIET_EVAL, 0);

                break;

            case YANA_ERROR_ON:
                /* For debugging only:
                 * show all errors, warnings and notices and evaluate assertions.
                 */
                error_reporting(E_ALL);
                set_error_handler(array(__CLASS__, 'printError'));
                assert_options(ASSERT_ACTIVE, 1);
                assert_options(ASSERT_CALLBACK, array(__CLASS__, 'printAssertion'));
                assert_options(ASSERT_BAIL, 0);
                assert_options(ASSERT_WARNING, 0);
                assert_options(ASSERT_QUIET_EVAL, 0);

                break;

            case YANA_ERROR_OFF:
            default:
                /* Prevent PHP from showing error messages to avoid information leak to hackers.
                 * Do no evaluate assertions for better performance.
                 */
                error_reporting(0);
                assert_options(ASSERT_ACTIVE, 0);
                $errorLevel = YANA_ERROR_OFF;

                break;
        }
        if (!defined('YANA_ERROR_REPORTING')) {
            /**
             * what to do with errors and system messages
             *
             * This constant reflects the way how errors and other messages are treated.
             * It can be one of the following: 'on', 'off' or 'log'.
             *
             * Meaning:
             * <ol>
             * <li>  on = catch all errors and print to screen  </li>
             * <li>  off = do not report any errors or messages  </li>
             * <li>  log = write errors and messages to a log file  </li>
             * </ol>
             */
            define('YANA_ERROR_REPORTING', $errorLevel);
        }
        return YANA_ERROR_REPORTING;
    }

}

?>