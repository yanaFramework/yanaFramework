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
 * @access      public
 * @name        ErrorUtility
 * @package     yana
 * @subpackage  error_reporting
 */
class ErrorUtility extends Utility
{

    /**
     * custom error handler
     *
     * This function creates colorfull error messages that should provide
     * better readability.
     *
     * @access  public
     * @param   int     $error_nr       error number
     * @param   string  $description    description
     * @param   string  $file           file
     * @param   int     $line_nr        line number
     * @static
     * @ignore
     */
    public static function printError($error_nr, $description, $file, $line_nr)
    {
        /* NOTE: to trigger an user error inside an user error handler could cause an infinite loop
         * (and by the way it does'nt make any sense at all).
         * So errors need to be printed out directly.
         */

        /* to check error reporting levels fall back to PHP's default settings */
        switch ($error_nr)
        {
            case E_USER_ASSERT:
                $error_level = E_USER_ERROR;
            break;
            case E_UNKNOWN_ERROR:
                $error_level = E_NOTICE;
            break;
            default:
                $error_level = $error_nr;
            break;
        }

        $error_reporting = error_reporting();
        /* print an error only if the current error reporting level is high enough */
        if (($error_reporting & ~$error_level) !== $error_reporting) {
            print ErrorUtility::_formatError($error_nr, $description, $file, $line_nr, true);
        }

        /* exit on error */
        switch ($error_level)
        {
            case E_ERROR:
            case E_USER_ERROR:
            case E_COMPILE_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_ERROR:
                if (ob_get_length() !== false) {
                    ob_end_flush();
                }
                exit(1);
            break;
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
     * @access  public
     * @param   int     $error_nr       error number
     * @param   string  $description    description
     * @param   string  $file           file
     * @param   int     $line_nr        line number
     * @static
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
                "(). Integer expected, found '".gettype($error_nr)."' instead.";
            print $message;
            exit(1);
        }
        if (!is_string($description)) {
            $message = "Error: Illegal argument type for argument 2 in " . __METHOD__ .
                "(). String expected, found '".gettype($description)."' instead.";
            print $message;
            exit(1);
        }
        if (!is_string($file)) {
            $message = "Error: Illegal argument type for argument 3 in " . __METHOD__ .
                "(). String expected, found '".gettype($file)."' instead.";
            print $message;
            exit(1);
        }
        if (!is_int($line_nr)) {
            $message = "Error: Illegal argument type for argument 4 in " . __METHOD__ .
                "(). Integer expected, found '".gettype($line_nr)."' instead.";
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
            $error_log = ErrorUtility::_formatError($error_nr, $description, $file, $line_nr, false);
            $filename = 'cache/error.log';
            $log =  Log::getLogFromMessage($error_log);
            unset($error_log);

            $error_message = "";

            /* the result is already some message or status */
            if (is_scalar($log)) {

                $error_message = (string) $log;

            /*
             * The result is an array, possibly containing a message
             * and additional information describing the circumstances
             * in which the error occured.
             */
            } elseif (is_array($log)) {

                $log['TIME'] = date('r');
                foreach ($log as $label => $value)
                {
                    $error_message .= $label;
                    for ($i = mb_strlen($label); $i < 15; $i++)
                    {
                        $error_message .= ' ';
                    }
                    $error_message .= $value."\n";
                }

            /*
             * If the result is a String object, just unbox it.
             */
            } elseif (is_object($log) && is_a($log, 'String')) {

                $error_message = $log->toString();

            /* If the result is something unexpected output a default message. */
            } else {

                $error_message = "The program encountered an unknown error.";

            }

            /* output the error message to a log file */
            if (error_log($error_message."\n", 3, $filename) === false) {
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
     * @access  public
     * @param   string  $file         file
     * @param   int     $line_nr      line number
     * @param   string  $description  description
     * @static
     * @ignore
     */
    public static function logAssertion($file, $line_nr, $description)
    {
        if (!is_string($file)) {
            $message = "Error: Illegal argument type for argument 1 in " . __METHOD__ .
                "(). String expected, found '".gettype($file)."' instead.";
            print $message;
            exit(1);
        }
        if (!is_int($line_nr)) {
            $message = "Error: Illegal argument type for argument 2 in " . __METHOD__ .
                "(). Integer expected, found '".gettype($line_nr)."' instead.";
            print $message;
            exit(1);
        }
        if (!is_string($description)) {
            $message = "Error: Illegal argument type for argument 3 in " . __METHOD__ .
                "(). String expected, found '".gettype($description)."' instead.";
            print $message;
            exit(1);
        }
        ErrorUtility::logError(E_USER_ASSERT, $description, $file, $line_nr);
    }

    /**
     * print failed assertions
     *
     * @access  public
     * @param   string  $file         file
     * @param   int     $line_nr      line number
     * @param   string  $description  description
     * @static
     * @ignore
     */
    public static function printAssertion($file, $line_nr, $description)
    {
        print ErrorUtility::_formatError(E_USER_ASSERT, $description, $file, $line_nr, true);
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
     * @access  public
     * @static
     * @name    ErrorUtility::setErrorReporting()
     * @param   integer|string    $errorLevel    examples: YANA_ERROR_OFF, YANA_ERROR_LOG,
     *                                           YANA_ERROR_ON or E_ALL, E_ALL & ~E_NOTICE
     * @uses    ErrorUtility::setErrorReporting(YANA_ERROR_OFF)
     */
    public static function setErrorReporting($errorLevel)
    {
        /* Note: to make PHP produce clickable PHP error messages,
         * the ini values docref_root and docref_ext need to be set.
         * These are not set by default.
         * In case they have not been set previously, the following
         * passage will set them to point to the online version
         * of the PHP manual.
         */

        $docref_root = ini_get('docref_root');
        $docref_ext  = ini_get('docref_ext');
        if (empty($docref_root)) {
            ini_set('docref_root', 'http://www.php.net/manual/en/');
        }
        if (empty($docref_ext)) {
            ini_set('docref_ext', '.php');
        }

        /* Note: method overloading is not available in PHP. (like Perl, but unlike C++ or Java)
         * The following work-around is a common way to simulate that feature.
         */

        /* Integer */
        if (is_int($errorLevel)) {
            ErrorUtility::_setLevelOfErrorHandling($errorLevel);

        /* String */
        } elseif (is_string($errorLevel)) {
            ErrorUtility::_setTypeOfErrorHandling($errorLevel);

        /* Other input */
        } else {
            $message = "Illegal argument type. Integer or string expected, found '".gettype($errorLevel)."' instead.";
            trigger_error($message, E_USER_ERROR);
        }
    }

    /**
     * Set error reporting level via PHP's error_reporting() function
     *
     * returns previous error level or bool(false) on error
     *
     * @access  private
     * @static
     * @name    ErrorUtility::_setLevelOfErrorHandling()
     * @param   int    $errorLevel    examples: E_ALL, E_ALL & ~E_NOTICE
     * @return  int|bool(false)
     *
     * @ignore
     */
    private static function _setLevelOfErrorHandling($errorLevel = null)
    {
        if (is_null($errorLevel)) {
            return error_reporting();
        } elseif (!is_int($errorLevel)) {
            $message = "Illegal argument type. Integer expected, found '".gettype($errorLevel)."' instead.";
            trigger_error($message, E_USER_ERROR);
            return false;
        } else {
            return error_reporting($errorLevel);
        }
    }

    /**
     * Set error reporting to on, off, or log
     *
     * @access  private
     * @static
     * @name    ErrorUtility::_setTypeOfErrorHandling()
     * @param   string    $errorLevel    YANA_ERROR_OFF|YANA_ERROR_LOG|YANA_ERROR_ON
     * @return  string|bool(false)
     *
     * @ignore
     */
    private static function _setTypeOfErrorHandling($errorLevel)
    {
        if (defined('YANA_ERROR_REPORTING') && $errorLevel !== YANA_ERROR_REPORTING) {
            $message = 'Error reporting level has already been set and can not be redefined.';
            trigger_error($message, E_USER_WARNING);
            return false;
        } elseif (!is_string($errorLevel)) {
            $message = "Illegal argument type. String expected, found '".gettype($errorLevel)."' instead.";
            trigger_error($message, E_USER_ERROR);
            return false;
        } else {
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
                    set_error_handler(array(__CLASS__,'logError'));
                    assert_options(ASSERT_ACTIVE,     1);
                    assert_options(ASSERT_CALLBACK,   array(__CLASS__,'logAssertion'));
                    assert_options(ASSERT_BAIL,       0);
                    assert_options(ASSERT_WARNING,    0);
                    assert_options(ASSERT_QUIET_EVAL, 0);
                break;
                case YANA_ERROR_ON:
                    /* For debugging only:
                     * show all errors, warnings and notices and evaluate assertions.
                     */
                    if (defined('E_STRICT')) {
                        error_reporting(E_ALL & ~E_STRICT);
                    } else {
                        error_reporting(E_ALL);
                    }
                    set_error_handler(array(__CLASS__,'printError'));
                    assert_options(ASSERT_ACTIVE,     1);
                    assert_options(ASSERT_CALLBACK,   array(__CLASS__,'printAssertion'));
                    assert_options(ASSERT_BAIL,       0);
                    assert_options(ASSERT_WARNING,    0);
                    assert_options(ASSERT_QUIET_EVAL, 0);
                break;
                case YANA_ERROR_OFF: default:
                    /* Prevent PHP from showing error messages to avoid information leak to hackers.
                     * Do no evaluate assertions for better performance.
                     */
                    error_reporting(0);
                    assert_options(ASSERT_ACTIVE,    0);
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

    /**
     * format error messages
     *
     * @access  private
     * @static
     * @param   int     $error_nr       error number
     * @param   string  $description    description
     * @param   string  $file           file
     * @param   int     $line_nr        line number
     * @param   bool    $as_html        as_html (set true if the message should be writen in html)
     * @return  string
     * @ignore
     */
    private static function _formatError($error_nr, $description, $file, $line_nr, $as_html)
    {
        $base_style = 'font-size: 13px; font-weight: normal; padding: 5px; border: 1px solid #888; text-align: left;';
        $show_details = true;
        /* for readability do not report errors twice */
        $registry = \Yana\VDrive\Registry::getGlobalInstance();
        if ($registry instanceof \Yana\VDrive\Registry) {
            $laseErrNr = $registry->getVar('lasterror.nr');
            $laseErrFile = $registry->getVar('lasterror.file');
            $laseErrLine = $registry->getVar('lasterror.line');
            if ($laseErrNr === $error_nr && $laseErrFile === $file && $laseErrLine === $line_nr) {
                if ($registry->getVar('lasterror.description') === $description) {
                    if ($registry->getVar('lasterror.hasmore') === true) {
                        $error_message = '';
                    } else {
                        $registry->setVar('lasterror.hasmore', true);
                        if ($as_html === true) {
                            $error_message = '<div style="'.$base_style.'"><pre>'."\t".'... the previous'.
                                ' error was reported multiple times.</pre></div>';
                        } else {
                            $error_message = "\t... the previous error was reported multiple times.";
                        }
                    }
                    return $error_message;
                } else {
                    $show_details = false;
                }
            } else {
                $registry->setVar('lasterror.hasmore',     false);
                $registry->setVar('lasterror.nr',          $error_nr);
                $registry->setVar('lasterror.description', $description);
                $registry->setVar('lasterror.file',        $file);
                $registry->setVar('lasterror.line',        $line_nr);
            }
        }

        $colors    = array (
            'critical' => array('color' => '#f00', 'background' => '#fee'),
            'compiler' => array('color' => '#000', 'background' => '#fff'),
            'error'    => array('color' => '#a00', 'background' => '#fff'),
            'warning'  => array('color' => '#d60', 'background' => '#fff'),
            'notice'   => array('color' => '#038', 'background' => '#fff'),
            'assert'   => array('color' => '#000', 'background' => '#ff8')
        );
        if (!defined('E_DEPRECATED')) {
            /**
             * introduced in PHP 5.3
             * @ignore
             */
            define('E_DEPRECATED', -1);
        }
        if (!defined('E_RECOVERABLE_ERROR')) {
            /**
             * introduced in PHP 5.2
             * @ignore
             */
            define('E_RECOVERABLE_ERROR', -1);
        }
        $errortype = array (
            E_CORE_ERROR        => array('Core Error',             $colors['critical']),
            E_CORE_WARNING      => array('Core Warning',           $colors['critical']),
            E_COMPILE_ERROR     => array('Compile Error',          $colors['compiler']),
            E_COMPILE_WARNING   => array('Compile Warning',        $colors['compiler']),
            E_PARSE             => array('Parsing Error',          $colors['compiler']),
            E_ERROR             => array('Error',                  $colors['error']),
            E_USER_ERROR        => array('Yana Error',             $colors['error']),
            E_WARNING           => array('Warning',                $colors['warning']),
            E_USER_WARNING      => array('Yana Warning',           $colors['warning']),
            E_RECOVERABLE_ERROR => array('Catchable Fatal Error',  $colors['error']),
            E_NOTICE            => array('Notice',                 $colors['notice']),
            E_USER_NOTICE       => array('Yana Notice',            $colors['notice']),
            E_USER_ASSERT       => array('Assertion failed',       $colors['assert']),
            E_DEPRECATED        => array('Deprecated',             $colors['notice']),
            E_STRICT            => array("Runtime Notice",         $colors['notice']),
            E_UNKNOWN_ERROR     => array('Unknown Error',          $colors['notice'])
        );

        if (!isset($errortype[$error_nr])) {
            $error_nr = E_UNKNOWN_ERROR;
        }
        assert('isset($errortype[$error_nr]);');

        /* Note: for readability assertions can have a description in form of a comment.
         * Example: assert('some_test; // comment');
         *
         * Where a comment is provided, this function will show the comment rather than
         * the assert code.
         *
         * Example of usage:
         * assert('$input >= 3 and $input <= 15; // argument '$input' is out of range [3..15]');
         */
        if ($error_nr === E_USER_ASSERT) {
            $description = preg_replace('/^.*;\s*(?:\/\/|\/\*|#)\s*(\S+.*)\s*(?:\*\/)?\s*$/Us', '$1', $description);
            if ($as_html !== true) {
                $description = "Assertion $description failed";
            }
        }
        assert('!isset($shortenFilepath); /* Cannot redeclare variable $backtrace */');
        $shortenFilepath = '/^'.preg_quote(getcwd(), '/').'/';

        /* Backtracing */
        /* @var $isTraceableError bool */
        assert('!isset($isTraceableError); // Cannot redeclare var $isTraceableError');
        $isTraceableError = ($error_nr === E_USER_ASSERT || $error_nr === E_USER_ERROR ||
            $error_nr === E_ERROR || $error_nr === E_USER_WARNING || $error_nr === E_WARNING ||
            $error_nr === E_RECOVERABLE_ERROR);
        if ($show_details === true && function_exists('debug_backtrace') && $isTraceableError) {
            /* Note: debug_backtrace became available in PHP 4.3 */
            assert('!isset($temp); /* Cannot redeclare variable $temp */');
            assert('!isset($backtrace); /* Cannot redeclare variable $backtrace */');
            $temp = debug_backtrace();
            /* Format backtrace */
            $backtrace = array();
            assert('!isset($i); /* Cannot redeclare variable $i */');
            for ($i = 0; $i < (count($temp) -3); $i++)
            {
                if (isset($temp[$i]['class']) && (strcasecmp($temp[$i]['class'], __CLASS__) === 0)) {
                    continue;
                } elseif ($i === count($temp) -3) {
                    $strcasecmp = strcasecmp($temp[$i]['class'], 'PluginManager');
                    if ($strcasecmp === 0 && strcasecmp($temp[$i]['function'], 'handle') === 0) {
                        continue;
                    }
                } elseif ($i === count($temp) -2) {
                    $strcasecmp = strcasecmp($temp[$i]['class'], 'Yana');
                    if ($strcasecmp === 0 && strcasecmp($temp[$i]['function'], 'handle') === 0) {
                        continue;
                    }
                } elseif ($i === count($temp) -1) {
                    $strcasecmp = strcasecmp($temp[$i]['class'], 'Index');
                    if ($strcasecmp === 0 && strcasecmp($temp[$i]['function'], 'main') === 0) {
                        continue;
                    }
                } elseif ($error_nr === E_USER_ASSERT && $temp[$i]['function'] === 'assert') {
                    continue;
                }

                /* initialize vars */
                $i1 = count($backtrace);
                $backtrace[$i1] = "";

                /* shorten file path for readability */
                if (isset($temp[$i]['file'])) {
                    assert('!isset($temp_file); /* Cannot redeclare variable $temp_file */');
                    assert('isset($shortenFilepath);');
                    $temp_file = preg_replace($shortenFilepath, '.', $temp[$i]['file']);
                }

                if (isset($temp[$i]['class'])) {
                    $backtrace[$i1] .= $temp[$i]['class'];
                    if (isset($temp[$i]['type'])) {
                        $backtrace[$i1] .= $temp[$i]['type'];
                    }
                }
                if (isset($temp[$i]['function'])) {
                    if (preg_match('/^(?:trigger_error|user_error)$/i', $temp[$i]['function'])) {
                        if (isset($temp_file)) {
                            $backtrace[$i1] = "Error was raised in file '".$temp_file."'";
                            if (isset($temp[$i]['line'])) {
                                $backtrace[$i1] .= " on line ".$temp[$i]['line'];
                            }
                            unset($temp_file);
                        }
                        continue;
                    } else {
                        $backtrace[$i1] .= $temp[$i]['function'];
                        $backtrace[$i1] .= '(';
                        if (isset($temp[$i]['args']) && is_array($temp[$i]['args'])) {
                            assert('!isset($j); /* Cannot redeclare variable $j */');
                            for ($j = 0; $j < count($temp[$i]['args']); $j++)
                            {
                                if ($j > 0) {
                                    $backtrace[$i1] .= ', ';
                                }
                                $backtrace[$i1] .= gettype($temp[$i]['args'][$j]);
                            }
                            unset($j);
                        }
                        $backtrace[$i1] .= ')';
                    }
                }
                unset($temp_file);
            } // end for
            unset($i);
            unset($temp);
        } else {
            $backtrace = null;
        }
        unset($isTraceableError);

        /* shorten file path for readability */
        assert('isset($shortenFilepath);');
        $file = preg_replace($shortenFilepath, '.', $file);

        if ($as_html === true) {
            $style = 'overflow: auto; color: '.$errortype[$error_nr][1]['color'].'; background: '.
                $errortype[$error_nr][1]['background'].';';
            $error_message  = '<div style="'.$base_style.$style.'"><pre>';
            /**
             * encode description depending on type of error
             */
            switch ($error_nr)
            {
                case E_USER_ERROR:
                case E_USER_WARNING:
                case E_USER_NOTICE:
                case E_USER_ASSERT:
                case E_UNKNOWN_ERROR:
                    $description = htmlspecialchars($description, ENT_NOQUOTES, 'UTF-8');
                    $description = preg_replace('/\'[^\'\"\s]+\'/', '<span style="color:#f00">$0</span>', $description);
                break;
                default:
                    /* intentionally left blank */
                break;
            }
            /**
             * create output
             */
            if ($show_details) {
                $error_message .= '<span style="font-weight: bold; text-decoration: underline;">'.
                    $errortype[$error_nr][0]."</span>\n";
                $error_message .= "  description:\t$description\n";
                $error_message .= "  file:\t\t$file\n";
                $error_message .= "  line:\t\t$line_nr</pre>";
                if (!is_null($backtrace)) {
                    $error_message .= '<div><pre><span style="font-weight: bold; font-style: italic;">Backtrace</span>';
                    $error_message .= "\n\t" . implode("\n\t", $backtrace);
                    $error_message .= '</pre></div>';
                }
            } else {
                $error_message .= ((mb_strpos($description, "\t\t")) ? "\t\t" : "") . $description;
            }
            $error_message .= "</div>";
        } else {
            $error_message = $errortype[$error_nr][0].': '.$description." in file '$file' on line $line_nr.";
        }

        return $error_message;
    }

    /**
     * debug breakpoint
     *
     * This is a debugging function that allows to use "de facto" breakpoints without the need of an extra debugger.
     * It creates and outputs a debug backtrace and dumps any vars you pass to it.
     *
     * Note: calling this function exists the program.
     *
     * Example of usage:
     * <code>
     * // ... some code here
     * ErrorUtility::breakpoint($foo, $bar);
     * // more code here ...
     * </code>
     *
     * @access  public
     * @static
     */
    public static function breakpoint()
    {
        print "<h1>BREAKPOINT</h1>\n";

        /* var dumps */
        if (func_num_args() > 0) {
            print "<h2>Var-Dumps</h2>\n<ol>\n";
            assert('!isset($element); // Cannot redeclare var $element');
            foreach (func_get_args() as $element)
            {
                print "<li><pre>";
                var_dump($element);
                print "</pre></li>\n";
            } /* end foreach */
            unset($element); /* clean up garbage */
            print "</ol>\n";
        }

        /* debug backtrace */
        print "<h2>Backtrace</h2>\n";
        print "<ol>\n";

        $smarty = null;
        assert('!isset($element); /* Cannot redeclare variable $element */');
        foreach (debug_backtrace() as $element)
        {
            // ignore class ErrorUtility
            if (isset($element['class']) && (strcasecmp($element['class'], __CLASS__) === 0)) {
                continue;
            }

            // include line and file name
            if (isset($element['line']) && isset($element['file'])) {
                $element['file'] .= ", on line " . $element['line'];
                unset($element['line']);
            }

            // compose method name
            if (isset($element['class']) && isset($element['type'])) {
                $element['function'] = $element['class'] . $element['type'] . $element['function'];
                /* function arguments */
                $element['function'] .= '( ';
                if (!empty($element['args'])) {
                    assert('!isset($arg); // Cannot redeclare var $arg');
                    foreach ($element['args'] as $arg)
                    {
                        $element['function'] .= gettype($arg) . ' ';
                    } /* end foreach */
                    unset($arg); /* clean up garbage */
                } else {
                    $element['function'] .= 'void ';
                }
                $element['function'] .= ')';
                unset($element['class'], $element['type']);
            }
            print "<li><pre>";
            // add params
            $params = array('value' => &$element);
            print SmartUtility::printArray($params, $smarty);
            print "</pre></li>";

        } /* end foreach */
        unset($element);

        print "</ol>\n";
        exit(0);
    }

}

?>