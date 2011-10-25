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
 * Formatting error messages for HTML output
 *
 * @package    yana
 * @subpackage log
 */
class HtmlFormatter extends \Yana\Core\Object implements \Yana\Log\Formatter\IsFormatter
{

    /**
     * @var Message
     * @ignore
     */
    protected $_message = null;

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
        E_USER_ASSERT => array('Assertion failed', 'assert'),
        E_DEPRECATED => array('Deprecated', 'notice'),
        E_STRICT => array("Runtime Notice", 'notice'),
        E_UNKNOWN_ERROR => array('Unknown Error', 'notice')
    );

    /**
     * @ignore
     */
    public function __construct()
    {
        $this->_message = new Message();
    }

    /**
     * format error messages
     *
     * @param   int     $level        error level
     * @param   string  $description  description
     * @param   string  $filename     file
     * @param   int     $lineNumber   line number
     * @param   bool    $asHtml       as_html (set true if the message should be writen in html)
     * @return  string
     */
    public function __invoke($level, $description, $filename = "", $lineNumber = 0, $asHtml = true)
    {
        $baseStyle = 'font-size: 13px; font-weight: normal; padding: 5px; border: 1px solid #888; text-align: left;';
        $showDetails = true;
        /* for readability do not report errors twice */
        $laseErrNr = $this->_message->getLevel();
        $laseErrFile = $this->_message->getFilename();
        $laseErrLine = $this->_message->getLineNumber();
        if ($laseErrNr === $level && $laseErrFile === $filename && $laseErrLine === $lineNumber) {
            if ($this->_message->getDescription() === $description) {
                if ($this->_message->hasMore() === true) {
                    $errorMessage = '';
                } else {
                    $this->_message->setHasMore();
                    if ($asHtml === true) {
                        $errorMessage = '<div style="' . $baseStyle . '"><pre>' . "\t" . '... the previous' .
                            ' error was reported multiple times.</pre></div>';
                    } else {
                        $errorMessage = "\t... the previous error was reported multiple times.";
                    }
                }
                return $errorMessage;
            } else {
                $showDetails = false;
            }
        } else {
            $this->_message = new Message();
            $this->_message->setLevel($level)
                ->setDescription($description)
                ->setFilename($filename)
                ->setLineNumber($lineNumber);
        }

        if (!isset(self::$_errortypeToColor[$level])) {
            $level = E_UNKNOWN_ERROR;
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
        if ($level === E_USER_ASSERT) {
            $description = preg_replace('/^.*;\s*(?:\/\/|\/\*|#)\s*(\S+.*)\s*(?:\*\/)?\s*$/Us', '$1', $description);
            if ($asHtml !== true) {
                $description = "Assertion $description failed";
            }
        }
        assert('!isset($shortenFilepath); /* Cannot redeclare variable $backtrace */');
        $shortenFilepath = '/^' . preg_quote(getcwd(), '/') . '/';

        /* Backtracing */
        /* @var $isTraceableError bool */
        assert('!isset($isTraceableError); // Cannot redeclare var $isTraceableError');
        $isTraceableError = ($level === E_USER_ASSERT || $level === E_USER_ERROR ||
            $level === E_ERROR || $level === E_USER_WARNING || $level === E_WARNING ||
            $level === E_RECOVERABLE_ERROR);
        if ($showDetails === true && function_exists('debug_backtrace') && $isTraceableError) {
            assert('!isset($temp); /* Cannot redeclare variable $temp */');
            assert('!isset($backtrace); /* Cannot redeclare variable $backtrace */');
            $temp = debug_backtrace();
            /* Format backtrace */
            $backtrace = array();
            assert('!isset($i); /* Cannot redeclare variable $i */');
            for ($i = 0; $i < (count($temp) - 3); $i++)
            {
                if (isset($temp[$i]['class']) && (strcasecmp($temp[$i]['class'], __CLASS__) === 0)) {
                    continue;
                } elseif ($i === count($temp) - 3) {
                    $strcasecmp = strcasecmp($temp[$i]['class'], 'PluginManager');
                    if ($strcasecmp === 0 && strcasecmp($temp[$i]['function'], 'handle') === 0) {
                        continue;
                    }
                } elseif ($i === count($temp) - 2) {
                    $strcasecmp = strcasecmp($temp[$i]['class'], 'Yana');
                    if ($strcasecmp === 0 && strcasecmp($temp[$i]['function'], 'handle') === 0) {
                        continue;
                    }
                } elseif ($i === count($temp) - 1) {
                    $strcasecmp = strcasecmp($temp[$i]['class'], 'Index');
                    if ($strcasecmp === 0 && strcasecmp($temp[$i]['function'], 'main') === 0) {
                        continue;
                    }
                } elseif ($level === E_USER_ASSERT && $temp[$i]['function'] === 'assert') {
                    continue;
                }

                /* initialize vars */
                $i1 = count($backtrace);
                $backtrace[$i1] = "";

                /* shorten file path for readability */
                if (isset($temp[$i]['file'])) {
                    assert('!isset($temp_file); /* Cannot redeclare variable $temp_file */');
                    assert('isset($shortenFilepath);');
                    $tempFile = preg_replace($shortenFilepath, '.', $temp[$i]['file']);
                }

                if (isset($temp[$i]['class'])) {
                    $backtrace[$i1] .= $temp[$i]['class'];
                    if (isset($temp[$i]['type'])) {
                        $backtrace[$i1] .= $temp[$i]['type'];
                    }
                }
                if (isset($temp[$i]['function'])) {
                    if (preg_match('/^(?:trigger_error|user_error)$/i', $temp[$i]['function'])) {
                        if (isset($tempFile)) {
                            $backtrace[$i1] = "Error was raised in file '" . $tempFile . "'";
                            if (isset($temp[$i]['line'])) {
                                $backtrace[$i1] .= " on line " . $temp[$i]['line'];
                            }
                            unset($tempFile);
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
                unset($tempFile);
            } // end for
            unset($i);
            unset($temp);
        } else {
            $backtrace = null;
        }
        unset($isTraceableError);

        /* shorten file path for readability */
        assert('isset($shortenFilepath);');
        $filename = preg_replace($shortenFilepath, '.', $filename);

        if ($asHtml === true) {
            $style = 'overflow: auto; color: ' . self::$_colors[self::$_errortypeToColor[$level][1]]['color'] . '; background: ' .
                self::$_colors[self::$_errortypeToColor[$level][1]]['background'] . ';';
            $errorMessage = '<div style="' . $baseStyle . $style . '"><pre>';
            /**
             * encode description depending on type of error
             */
            switch ($level)
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
            if ($showDetails) {
                $errorMessage .= '<span style="font-weight: bold; text-decoration: underline;">' .
                    self::$_errortypeToColor[$level][0] . "</span>\n";
                $errorMessage .= "  description:\t$description\n";
                $errorMessage .= "  file:\t\t$filename\n";
                $errorMessage .= "  line:\t\t$lineNumber</pre>";
                if (!is_null($backtrace)) {
                    $errorMessage .= '<div><pre><span style="font-weight: bold; font-style: italic;">Backtrace</span>';
                    $errorMessage .= "\n\t" . implode("\n\t", $backtrace);
                    $errorMessage .= '</pre></div>';
                }
            } else {
                $errorMessage .= ((mb_strpos($description, "\t\t")) ? "\t\t" : "") . $description;
            }
            $errorMessage .= "</div>";
        } else {
            $errorMessage = self::$_errortypeToColor[$level][0] . ': ' . $description . " in file '$filename' on line $lineNumber.";
        }

        return $errorMessage;
    }

}

?>