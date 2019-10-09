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

namespace Yana\Files\Decoders;

/**
 * En-/Decoder for Simple Markup Language (SML).
 *
 * @package     yana
 * @subpackage  files
 */
class SML extends \Yana\Core\StdObject implements \Yana\Files\Decoders\IsDecoder
{

    /**
     * Read a file in SML syntax and return its contents.
     *
     * {@inheritdoc}
     *
     * @param   array|string  $input          filename or file content
     * @param   int           $caseSensitive  CASE_UPPER|CASE_LOWER|CASE_MIXED
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the input is not a filename or content-array
     */
    public function getFile($input, $caseSensitive = CASE_MIXED)
    {
        assert(is_array($input) || is_string($input), 'Wrong argument type: $input. String or array expected.');
        assert($caseSensitive === CASE_MIXED || $caseSensitive === CASE_LOWER || $caseSensitive === CASE_UPPER, '$caseSensitive === CASE_MIXED || $caseSensitive === CASE_LOWER || $caseSensitive === CASE_UPPER');

        $result = array();
        $stack = array(&$result);
        $stackLength = 0;
        $match1 = array();
        $match2 = array();
        $handle = null;
        $buffer = "";
        $isFile = false;
        // the following vars are for debugging purposes only:
        $i = 0;
        $translatedKey = array();  // holds the debugging backtrace
        $isValid = true;

        if (is_string($input) && is_file($input)) {
            $handle  = fopen("$input", "r");
            $isFile = true;
        } elseif (is_array($input)) {
            $isFile = false;
        } else {
            $message = "Argument 1 is expected to be a filename or an array " .
                "created with file().\n\t\tInstead found " . gettype($input) .
                " '" . print_r($input, true) . "'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        while (true)
        {
            if ($isFile === true) {
                if (feof($handle)) {
                    break;
                }
                $buffer = fgets($handle);
            } else {
                if (!isset($input[$i])) {
                    break;
                }
                $buffer = &$input[$i];
            }
            if (preg_match("/^\s*<(\w[^>]*)>/", $buffer, $match1)) {
                /* START TAG */
                if ($caseSensitive === CASE_UPPER) {
                    $match1[1] = mb_strtoupper($match1[1]);
                } elseif ($caseSensitive === CASE_LOWER) {
                    $match1[1] = mb_strtolower($match1[1]);
                } else {
                    /* intentionally left blank */
                }
                $stack[$stackLength +1] =& $stack[$stackLength++][$match1[1]];

                $pattern = '/^'.str_replace('/', '\/', preg_quote($match1[0])).'(.*)<\/'
                    .str_replace('/', '\/', preg_quote($match1[1])).'>/Ui';
                if (preg_match($pattern, $buffer, $match2)) {
                    /* CDATA */
                    if ($match2[1] == "true") {
                        $stack[$stackLength] = true;
                    } elseif ($match2[1] == "false") {
                        $stack[$stackLength] = false;
                    } elseif ($match2[1]!="") {
                        $stack[$stackLength] = $match2[1];
                    }
                    array_pop($stack);
                    $stackLength--;
                } else {
                    /* debugging backtrace */
                    if (defined('YANA_ERROR_REPORTING') && YANA_ERROR_REPORTING !== YANA_ERROR_OFF) {
                        array_push($translatedKey, $match1[1]);
                    }
                }

            } elseif (preg_match("/<\/\w[^>]*>/U", $buffer)) {
                /* END TAG */
                array_pop($stack);
                $stackLength--;
                // debugging backtrace
                // @codeCoverageIgnoreStart
                if (defined('YANA_ERROR_REPORTING') && YANA_ERROR_REPORTING !== YANA_ERROR_OFF) {
                    $openTag = array_pop($translatedKey);
                    /* hide follow up errors */
                    if ($isValid === true) {
                        assert(!isset($m), '!isset($m)');
                        $m = array();
                        preg_match("/<\/([^>]*)>/U", $buffer, $m);
                        $closedTag = $m[1];
                        unset($m);
                        if (strcasecmp($openTag, $closedTag) !== 0) {
                            $message = "Unclosed tag '" . implode('.', $translatedKey) . '.' . $openTag . "'.";
                            if ($isFile) {
                                $message = "SML ERROR in file '" . $input . "' on line " . $i . ": " . $message;
                            } else {
                                $message = "SML ERROR on line " . $i . ": " . $message;
                            }
                            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
                            $isValid = false;
                        }
                    }
                }
                // @codeCoverageIgnoreEnd
            } else {
                /* COMMENT */
            }
            if ($isFile === false || (defined('YANA_ERROR_REPORTING') && YANA_ERROR_REPORTING !== YANA_ERROR_OFF)) {
                $i++;
            }
        } /* end while */

        if ($isFile) {
            fclose($handle);
        }

        // debugging backtrace
        // @codeCoverageIgnoreStart
        if (defined('YANA_ERROR_REPORTING') && YANA_ERROR_REPORTING !== YANA_ERROR_OFF && $stackLength !== 0) {
            $message = "The tag '" . implode('.', $translatedKey) . "' has never been closed.";
            if ($isFile) {
                $message = "SML ERROR in file '" . $input . "': " . $message;
            } else {
                $message = "SML ERROR: " . $message;
            }
            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
        }
        // @codeCoverageIgnoreEnd

        return $result;

    }

    /**
     * Create a SML string from a scalar variable, an object, or an array of data.
     *
     * {@inheritdoc}
     *
     * @param   scalar|array|object  $data           data to encode
     * @param   string               $name           name of root-tag
     * @param   int                  $caseSensitive  one of: CASE_UPPER, CASE_LOWER, CASE_MIXED
     * @param   int                  $indent         internal value (ignore)
     * @return  string
     */
    public function encode($data, $name = null, $caseSensitive = CASE_MIXED, $indent = 0)
    {
        assert(is_null($data) || is_scalar($data) || is_array($data) || is_object($data),
            'Wrong argument type for argument 1. Array or scalar value expected.');
        assert(is_null($name) || is_scalar($name), 'Wrong argument type for argument 2. String expected.');
        assert($caseSensitive === CASE_MIXED || $caseSensitive === CASE_LOWER || $caseSensitive === CASE_UPPER,
            'Invalid argument 3. Expected one of the following constants: CASE_MIXED, CASE_LOWER, CASE_UPPER.');
        assert(is_int($indent), 'Wrong argument type for argument 4. Integer expected.');

        /* settype to STRING
         *            INTEGER
         *            INTEGER
         */
        $name          = (string) $name;
        $indent        = (int)    $indent;
        $caseSensitive = (int)    $caseSensitive;

        /* string */ $txt = "";

        if (is_null($data)) {
            return "";
        } elseif (is_scalar($data) && (is_null($name) || $name === "")) {
            $message = "Your untitled scalar value (arg. 1) in ".__METHOD__."() was renamed to '0'.\n\t\t".
                "You are encouraged to use the \$name argument (arg. 2) to set the variable name ".
                "to anything you prefer.";
            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::INFO);
            $name = '0';
        } elseif ($caseSensitive === CASE_UPPER) {
            $name = mb_strtoupper($name);
        } elseif ($caseSensitive === CASE_LOWER) {
            $name = mb_strtolower($name);
        }

        $ignoreName = (bool) ((is_null($name) || $name === "") && is_array($data));

        /* indent tag */
        $tab = "";
        for ($i = 0; $i < $indent; $i++)
        {
            $tab .= "\t";
        }

        /* switch typeOf($data) */
        switch (true)
        {
            case is_bool($data):
                return $tab."<$name>".( ($data) ? "true" : "false" )."</$name>"."\n";

            case is_string($data):
                return $tab."<$name>".preg_replace("/\s/", " ", strip_tags($data))."</$name>"."\n";

            case is_integer($data): case is_float($data):
                return $tab."<$name>".$data."</$name>"."\n";

            case is_object($data):
                $data = get_object_vars($data);
            // fall through
            case is_array($data):
                $ignoreName || $txt = $tab."<$name>"."\n";
                $ignoreName || $indent++;
                if ($indent < 50) { // recursion protection: maximal nesting level of 50
                    foreach ($data as $key => $element)
                    {
                        if ($element !== $data) {
                            $txt .= $this->encode($element, $key, $caseSensitive, $indent);
                        } else {
                            /* ignore recursion */
                        }
                    } /* end foreach */
                }
                $ignoreName || $txt .= $tab."</$name>"."\n";
                return $txt;

            default:
                return $tab."<$name>".$data."</$name>"."\n";

        } /* end switch */
    }

    /**
     * Read variables from an encoded string.
     *
     * {@inheritdoc}
     *
     * @param   string  $input          input
     * @param   int     $caseSensitive  caseSensitive
     * @return  array
     */
    public function decode($input, $caseSensitive = CASE_MIXED)
    {
        assert(is_string($input), 'Wrong argument type for argument 1. String expected.');
        $input = explode("\n", "$input");
        return $this->getFile($input, $caseSensitive);
    }

}

?>