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

namespace Yana\Log\Formatter;

/**
 * Formatting error messages for plain text output.
 *
 * @package    yana
 * @subpackage log
 */
abstract class AbstractFormatter extends \Yana\Core\StdObject implements \Yana\Log\Formatter\IsFormatter
{

    /**
     * @var \Yana\Log\Formatter\Message
     */
    private $_message = null;

    /**
     * Configuration setting.
     *
     * @var array 
     */
    private static $_colors = array(
        'critical' => array('color' => '#f00', 'background' => '#fee'),
        'compiler' => array('color' => '#000', 'background' => '#fff'),
        'error' => array('color' => '#a00', 'background' => '#fff'),
        'warning' => array('color' => '#d60', 'background' => '#fff'),
        'notice' => array('color' => '#038', 'background' => '#fff'),
        'assert' => array('color' => '#000', 'background' => '#ff8')
    );

    /**
     * Configuration setting.
     *
     * @var array 
     */
    private static $_errortypeToColor = array(
        E_CORE_ERROR => array('Core Error', 'critical'),
        E_CORE_WARNING => array('Core Warning', 'critical'),
        E_COMPILE_ERROR => array('Compile Error', 'compiler'),
        E_COMPILE_WARNING => array('Compile Warning', 'compiler'),
        E_PARSE => array('Parsing Error', 'compiler'),
        E_ERROR => array('Error', 'error'),
        E_USER_ERROR => array('Yana Error', 'error'),
        E_WARNING => array('Warning', 'warning'),
        E_USER_WARNING => array('Yana Warning', 'warning'),
        E_RECOVERABLE_ERROR => array('Catchable Fatal Error', 'error'),
        E_NOTICE => array('Notice', 'notice'),
        E_USER_NOTICE => array('Yana Notice', 'notice'),
        \Yana\Log\TypeEnumeration::ASSERT => array('Assertion failed', 'assert'),
        E_DEPRECATED => array('Deprecated', 'notice'),
        E_STRICT => array("Runtime Notice", 'notice'),
        \Yana\Log\TypeEnumeration::UNKNOWN => array('Unknown Error', 'notice')
    );

    /**
     * Returns cached message.
     *
     * @ignore
     */
    protected function _getMessage()
    {
        if (!isset($this->_message)) {
            $this->_message = new \Yana\Log\Formatter\Message();
        }
        return $this->_message;
    }

    /**
     * Renew the cache.
     *
     * @param  \Yana\Log\Formatter\Message  $message  new cache instance
     * @ignore
     */
    protected function _setMessage(\Yana\Log\Formatter\Message $message)
    {
        $this->_message = $message;
    }

    /**
     * Format error messages.
     *
     * @param   int     $level        error level
     * @param   string  $description  description
     * @param   string  $filename     file
     * @param   int     $lineNumber   line number
     * @param   array   $trace        the error backtrace as returned by debug_backtrace()
     * @param   bool    $asHtml       as_html (set true if the message should be writen in html)
     * @return  string
     */
    protected function _format($level, $description, $filename, $lineNumber, array $trace = array(), $asHtml = true)
    {
        $baseStyle = 'font-size: 13px; font-weight: normal; padding: 5px; border: 1px solid #888; text-align: left;';

        $message = $this->_getMessage();
        $isTraceableError = !($message->getLevel() === $level && $message->getFilename() === $filename && $message->getLineNumber() === $lineNumber);

        if ($isTraceableError) {
            $message = new \Yana\Log\Formatter\Message();
            $message->setLevel($level)
                ->setDescription($description)
                ->setFilename($filename)
                ->setLineNumber($lineNumber);
            $this->_setMessage($message);

        } elseif (!$isTraceableError && $message->getDescription() === $description) {
            /* for readability do not report errors twice */
            if ($message->hasMore() === true) {
                $errorMessage = '';
            } else {
                $message->setHasMore();
                if ($asHtml === true) {
                    $errorMessage = '<div style="' . $baseStyle . '"><pre>' . "\t" . '... the previous' .
                        ' error was reported multiple times.</pre></div>';
                } else {
                    $errorMessage = "\t... the previous error was reported multiple times.";
                }
            }
            return $errorMessage;
        }
        unset($message);

        if (!isset(self::$_errortypeToColor[$level])) {
            $level = \Yana\Log\TypeEnumeration::UNKNOWN;
        }

        /* Note: for readability assertions can have a description in form of a comment.
         * Example: assert('some_test; // comment');
         *
         * Where a comment is provided, this function will show the comment rather than
         * the assert code.
         *
         * Example of usage:
         * assert('$input >= 3 and $input <= 15; // argument '$input' is out of range [3..15]');
         */
        if ($level === \Yana\Log\TypeEnumeration::ASSERT) {
            $description = preg_replace('/^.*;\s*(?:\/\/|\/\*|#)\s*(\S+.*)\s*(?:\*\/)?\s*$/Us', '$1', $description);
            if ($asHtml !== true) {
                $description = "Assertion $description failed";
            }
        }

        // shorten file path for readability
        $shortenFilepath = '/^' . preg_quote(getcwd(), '/') . '/';
        assert('isset($shortenFilepath);');
        $filename = preg_replace($shortenFilepath, '.', $filename);

        if ($asHtml === true) {
            $style = 'overflow: auto; color: ' . self::$_colors[self::$_errortypeToColor[$level][1]]['color'] . '; background: ' .
                self::$_colors[self::$_errortypeToColor[$level][1]]['background'] . ';';
            $errorMessage = '<div style="' . $baseStyle . $style . '">';
            // encode description depending on type of error
            switch ($level)
            {
                case \Yana\Log\TypeEnumeration::ERROR:
                case \Yana\Log\TypeEnumeration::WARNING:
                case \Yana\Log\TypeEnumeration::INFO:
                case \Yana\Log\TypeEnumeration::ASSERT:
                case \Yana\Log\TypeEnumeration::UNKNOWN:
                    $description = \Yana\Util\Strings::htmlSpecialChars((string) $description, ENT_NOQUOTES);
                    $description = preg_replace('/\'[^\'\"\s]+\'/', '<span style="color:#f00">$0</span>', $description);
            }
            // create output
            if ($isTraceableError === true) {
                $errorMessage .= '<pre><span style="font-weight: bold; text-decoration: underline;">' .
                    self::$_errortypeToColor[$level][0] . "</span>\n";
                $errorMessage .= "  description:\t$description\n";
                $errorMessage .= "  file:\t\t$filename\n";
                $errorMessage .= "  line:\t\t$lineNumber</pre>";
                /* Backtracing */
                $errorMessage .= '<div><pre><span style="font-weight: bold; font-style: italic;">Backtrace</span>';
                $errorMessage .= "\n\t" . implode("\n\t", $this->_formatBacktrace($trace));
                $errorMessage .= '</pre></div>';
            } else {
                // This will be executed if the error occured in the same file and on the same line as a previous one,
                // but with a different message
                $errorMessage .= '<pre>' . ((mb_strpos($description, "\t\t")) ? "\t\t" : "") . $description . '</pre>';
            }
            $errorMessage .= "</div>";
        } else {
            $errorMessage = self::$_errortypeToColor[$level][0] . ': ' . $description . " in file '$filename' on line $lineNumber.";
        }

        return $errorMessage;
    }

    /**
     * Formats and returns debug backgtrace as array of strings.
     *
     * Each returned string is one hop.
     * The hops are ordered the same way as they are given.
     *
     * @param   array  $trace  output of debug_backtrace()
     * @return  array
     */
    protected function _formatBacktrace(array $trace = array())
    {
        if (empty($trace)) {
            $trace = debug_backtrace();
        }

        $backtrace = array();
        $shortenFilepath = '/^' . preg_quote(getcwd(), '/') . '/';
        /* Format backtrace */
        foreach ($trace as $temp)
        {
            /* initialize vars */
            $i = count($backtrace);
            $backtrace[$i] = "";

            /* shorten file path for readability */
            if (isset($temp['file'])) {
                $tempFile = preg_replace($shortenFilepath, '.', $temp['file']);
            }

            if (isset($temp['class'])) {
                $backtrace[$i] .= $temp['class'];
                if (isset($temp['type'])) {
                    $backtrace[$i] .= $temp['type'];
                }
            }
            if (isset($temp['function'])) {
                if (preg_match('/^(?:trigger_error|user_error)$/i', $temp['function'])) {
                    if (isset($tempFile)) {
                        $backtrace[$i] = "Error was raised in file '" . $tempFile . "'";
                        if (isset($temp['line'])) {
                            $backtrace[$i] .= " on line " . $temp['line'];
                        }
                        unset($tempFile);
                    }
                    continue;
                } else {
                    $backtrace[$i] .= $temp['function'];
                    $backtrace[$i] .= '(';
                    if (isset($temp['args']) && is_array($temp['args'])) {
                        $j = 0;
                        foreach ($temp['args'] as $tempArg)
                        {
                            if ($j > 0) {
                                $backtrace[$i] .= ', ';
                            }
                            $backtrace[$i] .= gettype($tempArg);
                            $j++;
                        }
                        unset($j);
                    }
                    $backtrace[$i] .= ')';
                }
            }
            unset($tempFile, $i);
        } // end foreach
        unset($temp);

        return $backtrace;
    }

}

?>