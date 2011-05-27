<?php
/**
 * Common tools
 *
 * This file contains a variety of tools that might be usefull to all
 * applications, no matter wether the use the rest of the framework or not.
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
 * @package     yana
 * @subpackage  utilities
 * @license     http://www.gnu.org/licenses/gpl.txt
 */

/**#@+
 * CONSTANTS
 *
 * @ignore
 */

/* yana framework only */
if (!defined('UPLOAD_ERR_SIZE')) {
    define('UPLOAD_ERR_SIZE', -1);
}
if (!defined('UPLOAD_ERR_FILE_TYPE')) {
    define('UPLOAD_ERR_FILE_TYPE', -2);
}
if (!defined('UPLOAD_ERR_INVALID_TARGET')) {
    define('UPLOAD_ERR_INVALID_TARGET', -4);
}
if (!defined('UPLOAD_ERR_OTHER')) {
    define('UPLOAD_ERR_OTHER', -5);
}

if (!function_exists('sys_get_temp_dir')) {
    /**
     * This function is new to PHP 5.
     * Currently no version information available.
     *
     * Will try to return the system's temporary directory,
     * or bool(false) on failure.
     *
     * Note: in some previous releases of PHP this function
     * was named 'php_get_temp_dir()'.
     *
     * This function will try several known settings starting
     * with the system's environment vars, through to PHP's
     * ini settings.
     *
     * Important note: This function MAY NOT return the real
     * temporary directory of the system. It returns SOME
     * temporary directory.
     *
     * Seems to work fine with Windows. Not tested with POSIX.
     *
     * @since   2.9.4
     * @return  string
     */
    function sys_get_temp_dir()
    {
        /* This will work for Server2Go */
        if (isset($_ENV['S2G_TEMP_FOLDER'])) {
            return $_ENV['S2G_TEMP_FOLDER'];

        /* previous name of the same function in an early PHP-release */
        } elseif (function_exists('php_get_temp_dir')) {
            return php_get_temp_dir();

        /* environment vars */
        } elseif (isset($_ENV['TEMP'])) {
            return $_ENV['TEMP'];
        } elseif (isset($_ENV['TMP'])) {
            return $_ENV['TMP'];

        /* ini and config vars */
        } else {
            $tmp = ini_get('temp_dir');
            if (!empty($tmp)) {
                return $tmp;
            }
            $tmp = ini_get('upload_tmp_dir');
            if (!empty($tmp)) {
                return $tmp;
            }
            $tmp = ini_get('session.save_path');
            if (!empty($tmp)) {
                return $tmp;
            }
            $tmp = get_cfg_var('upload_tmp_dir');
            if (!empty($tmp)) {
                return $tmp;
            }
            $tmp = get_cfg_var('session.save_path');
            if (!empty($tmp)) {
                return $tmp;
            }

            /**
             * No value found.
             *
             * Note: that this function does not return
             * a bogus "tmp" directory (which possibly
             * might not even exist) in this case.
             */
            return false;
        }
    }
}

/**
 * list contents of a directory
 *
 * The argument $filter may contain multiple file extension,
 * use a pipe '|' sign to seperate them.
 * Example: "*.xml|*.html" will find all xml- and html-files
 *
 * The argument $switch may be used to get only subdirectories (YANA_GET_DIRS),
 * or only files (YANA_GET_FILES), or all contents (YANA_GET_ALL), which is the default.
 *
 * @param   string  $dir     directory name
 * @param   string  $filter  filter
 * @param   int     $switch  possible values YANA_GET_ALL, YANA_GET_DIRS, YANA_GET_FILES
 * @return  array
 * @name    function_dirlist()
 */
function dirlist($dir, $filter = "", $switch = YANA_GET_ALL)
{
    assert('is_string($dir); /* Wrong argument type for argument 1. String expected. */');
    assert('is_string($filter); /* Wrong argument type for argument 2. String expected. */');
    assert('$switch === YANA_GET_ALL || $switch === YANA_GET_DIRS  || $switch === YANA_GET_FILES; /* '.
        'Invalid value for argument 3. */');
    /* settype to STRING */
    $dir = (string) $dir;
    $filter = (string) $filter;

    /* Input handling */
    if ($filter == "") {
        $filter = false;
    } elseif (strpos($filter, '|') !== false) {
        $filter = preg_replace("/[^\.\-\_\w\d\|]/", "", $filter);
        assert('!isset($tok); /* cannot redeclare variable $tok */');
        $tok = strtok($filter, "|");
        $filter = "";
        while ($tok !== false)
        {
            $filter .= preg_quote($tok, '/');
            $tok = strtok("|");
            if ($tok !== false) {
                $filter .= "|";
            }
        } /* end while */
        unset($tok);
    } else {
        $filter = preg_replace("/[^\.\-\_\w\d]/", "", $filter);
        $filter = preg_quote($filter, '/');
    }

    /* read contents from directory */
    if (is_dir($dir)) {
        assert('!isset($dirlist); /* cannot redeclare variable $dirlist */');
        assert('!isset($d);       /* cannot redeclare variable $d       */');
        assert('!isset($entry);   /* cannot redeclare variable $entry   */');
        $dirlist = array();
        $d = dir($dir);
        while($entry = $d->read())
        {
            if (!preg_match('/^\.{1,2}/', $entry) && ($filter === false || preg_match("/(?:{$filter})$/i", $entry))) {
                assert('is_array($dirlist); /* Invariant condition failed: $dirlist is not an array. */');
                if ($switch === YANA_GET_ALL) {
                    $dirlist[] = $entry;
                } elseif ($switch === YANA_GET_DIRS && is_dir($dir.$entry)) {
                    $dirlist[] = $entry;
                } elseif ($switch === YANA_GET_FILES && is_file($dir.$entry)) {
                    $dirlist[] = $entry;
                } else {
                    continue;
                }
            }
        } /* end while */
        unset($entry);
        $d->close();
        sort($dirlist);
        assert('is_array($dirlist); /* Unexpected result: $dirlist is not an array. */');
        return $dirlist;
    } else {
        trigger_error("The directory '{$dir}' does not exist.", E_USER_NOTICE);
        return array();
    }

}

/**
 * Untaint user input taken from a web form
 *
 * This function scrubbs your user input data shiny and clean.
 *
 * It ensures: the data has a given type, maximum length, and syntax.
 * E.g. if the data comes out of an input-field use this function with
 * the argument $escape set to YANA_ESCAPE_LINEBREAK, to enforce the
 * input does not have any unexpected line breaks.
 *
 * Valid values for parameter $type:
 *
 * <ul>
 * <li>  int, integer  </li>
 * <li>  float  </li>
 * <li>  boolean, bool  </li>
 * <li>  array, set  </li>
 * <li>  string  </li>
 * <li>  object  </li>
 * <li>  time     = the input is an unix time code  </li>
 * <li>  mail     = the input is a mail adress  </li>
 * <li>  inet, ip = the input is an IP adress  </li>
 * <li>  url      = the input is an URL  </li>
 * <li>  select   = the input is taken from a select field (treated as "string")  </li>
 * <li>  text     = the input is taken from a textarea field (treated as "string")  </li>
 * </ul>
 *
 * Valid values for parameter $escape:
 *
 * <ul>
 * <li>  YANA_ESCAPE_NONE      = leave special chars alone (default)  </li>
 * <li>  YANA_ESCAPE_SLASHED   = apply addslashes()  </li>
 * <li>  YANA_ESCAPE_TOKEN     = replace template delimiters with html-entities  </li>
 * <li>  YANA_ESCAPE_CODED     = convert all characters to html-entities  </li>
 * <li>  YANA_ESCAPE_LINEBREAK = revert all white-space to spaces<br />(for security reasons you should ALWAYS use
 *                               this setting if you<br />expect data from any other field than textarea)  </li>
 * <li>  YANA_ESCAPE_USERTEXT  = treat full-text message from an textarea element,<br />prevents flooding by removing
 *                               doubled elements  </li>
 * </ul>
 *
 * These constants can be combined! Examples of usage:
 * <ul>
 * <li>  YANA_ESCAPE_SLASHED = just slashes</li>
 * <li>  YANA_ESCAPE_SLASHED | YANA_ESCAPE_TOKEN = slashes and token  </li>
 * <li>  YANA_ESCAPE_ALL & ~YANA_ESCAPE_USERTEXT = all but usertext </li>
 * </ul>
 *
 * Interpretation of the $length parameter depends on the $type argument given.
 *
 * <ul>
 * <li>  no type = interpreted as maximum length of characters (implicit string conversion)  </li>
 * <li>  string  = maximum length of characters  </li>
 * <li>  integer = maximum number of digits  </li>
 * <li>  float   = maximum number of digits (without fraction) - this may be combined with argument $precision  </li>
 * </ul>
 *
 * For type float and integer, if the number of digits exceeds the maximum,
 * the maximum number allowed will be returned instead.
 *
 * For type integer see the following examples:
 * <pre>
 * $value=-3,     $length=1 : return -3
 * $value=3.2,    $length=1 : return 3
 * $value=3.4,    $length=1 : return 3
 * $value=3.5,    $length=1 : return 4
 * $value=3.6,    $length=1 : return 4
 * $value=9.9,    $length=1 : return 9
 * $value=11.11,  $length=2 : return 11
 * $value=111.11, $length=2 : return 99
 * $value=10,     $length=1 : return 9
 * </pre>
 *
 * The argument $precision is the maximum number of digits for the decimal fraction of a number.
 * This argument applies only to types float and double.
 *
 * For type float see the following examples:
 * <pre>
 * $value=-3.1,   $length=1, $precision 0: return -3
 * $value=3.4,    $length=1, $precision 0: return 3
 * $value=3.5,    $length=1, $precision 0: return 4
 * $value=3.21,   $length=1, $precision 1: return 3.2
 * $value=13.5,   $length=1, $precision 1: return 9.9
 * $value=11.11,  $length=2, $precision 1: return 11.1
 * $value=111.11, $length=2, $precision 1: return 99.9
 * $value=0.115,  $length=0, $precision 2: return .12
 * $value=5.115,  $length=1, $precision 2: return 5.12
 * </pre>
 *
 * Note on compatibility:
 * The argument $precision was introduced in version 2.9.7. This changes the interpretation
 * of the argument $length. Versions BEFORE 2.9.7 interpreted float values as numeric strings.
 * Thus $length was understood as the maximum length in characters (including fraction).
 * This has changed in version 2.9.7, where float values are treated as numbers - as shown
 * in the examples above. The argument $length is now interpreted as the maximum length of the
 * full decimal number (excluding fraction).
 *
 * Note: type "image" is only treated as string here.
 * There is a specific function for this job in {@see DbBlob}.
 *
 * For type "text" see the following example:
 * <code>
 * // this example will untaint text taken from a HTML form
 *
 * // input taken from field 'message'
 * $unsaveInput = $_GET['message'];
 * // type of data
 * $type = 'text';
 * // max. number of characters
 * $length = 1000;
 * // escape input
 * $escape = YANA_ESCAPE_USERTEXT;
 * // untaint input
 * $saveInput = untaintInput($unsaveInput, $type, $length, $escape);
 * </code>
 *
 * @param   mixed   $value         the input data
 * @param   string  $type          desired type, note that this should always be a scalar type
 * @param   int     $length        maximum length
 * @param   int     $escape        choose how special characters should be treated
 * @param   bool    $doubleEncode  when false, existing codes will not be encoded again
 *                                 (currently only with $escape=YANA_ESCAPE_USERTEXT)
 * @param   int     $precision     types float and double only - number of post
 * @return  mixed
 * @name    function_untaintInput()
 */
function untaintInput($value, $type = "", $length = 0, $escape = 0, $doubleEncode = false, $precision = -1)
{
    assert('is_string($type);   /* Wrong argument type for argument 2. String expected. */');
    assert('is_int($length);    /* Wrong argument type for argument 3. Integer expected. */');
    assert('is_int($escape);    /* Wrong argument type for argument 4. Integer expected. */');
    assert('is_int($precision); /* Wrong argument type for argument 6. Integer expected. */');
    /* settype to INTEGER */
    $length = (int) $length;
    $escape = (int) $escape;
    $precision = (int) $precision;

    switch (mb_strtolower("$type"))
    {
        case "inet":
            if (filter_var($value, FILTER_VALIDATE_IP) === false) {
                $value = null;
            }
            return $value;

        case "url":
            $value = filter_var($value, FILTER_SANITIZE_URL);
            if (!preg_match('/^\w+\:\/\//', $value)) {
                $value = 'http://' . $value;
            }
            if ($length > 0) {
                $value = substr($value, 0, $length);
            }
            if (filter_var($value, FILTER_VALIDATE_URL) === false) {
                $value = "";
            }
            return $value;

        /* this maps aliases to values, returned by gettype according to php manual*/
        case "int":
        case "integer":
        case "timestamp":
            $value = (int) round(floatval($value));

            /**
             * This case handles an INTEGER OVERFLOW
             * by converting the input to the maximum
             * positive or negative number.
             *
             * Note: this is the only case where an
             * integer value may exceed the $length
             * provided.
             */
            if ($length > 0 && mb_strlen((string) abs($value)) > $length) {
                if ($value < 0) {
                    return intval("-" . str_pad("", $length, "9"));
                } else {
                    return intval(str_pad("", $length, "9"));
                }
            }
            return $value;

        case "float":
        case "double":
            if (is_string($value)) {
                $value = str_replace(',', '.', $value);
            }
            $value = floatval($value);

            /*
             * round to precision
             */
            if ($length > 0 && abs($value) >= pow(10, $length - $precision)) {

                assert('!isset($_value); // Cannot redeclare var $_value');
                $_value = str_pad('', $length - $precision, '9') . '.' . str_pad('', $precision, '9');

                if ($value < 0) {
                    $_value = '-' . $_value;
                }
                $value = (float) $_value;

            /*
             * round to precision
             */
            } elseif ($precision >= 0) {
                $value = (float) round($value, $precision);

            }
            return $value;

        case "boolean":
        case "bool":
            return !empty($value);

        case "object":
            if (!is_object($value)) {
                $value = null;
            }
            return $value;

        case "mail":
            $value = (string) filter_var($value, FILTER_SANITIZE_EMAIL);
            if ($length > 0) {
                $value = substr($value, 0, $length);
            }
            if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                return "";
            }
        break;

        case "text":
            // strip NULL-chars et al.
            $value = preg_replace('/[\x00-\x08\x0b]*|[\x0e-\x1f]*/', '', "$value");
        break;

        case "select":
        case "file":
        case "image":
        case "profile":
        case "string":
            // strip tags, new-lines and chars < 32
            $value = filter_var((string) $value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
        break;

        case "array":
        case "set":
            $value = SML::encode((array) $value, null, CASE_UPPER);
        break;

    }

    if ($length > 0) {
        if (is_scalar($value)) {
            if (mb_strlen("$value") > $length) {
                $value = mb_substr($value, 0, $length);
            }
        } elseif (is_array($value)) {
            if (count($value) > $length) {
                $value = array_slice($value, 0, $length, true);
            }
        }
    }

    /*
     * Apply filter
     *
     * escape special characters / untaint content
     */
    if (is_string($value)) {
        /*
         * (1) filter SLASHED
         */
        if ($escape & YANA_ESCAPE_SLASHED) {
            $value = addslashes($value);
        }
        /*
         * (2) filter TOKEN
         */
        if ($escape & YANA_ESCAPE_TOKEN) {
            $value = str_replace(YANA_LEFT_DELIMITER, '&#'.ord(YANA_LEFT_DELIMITER).';', $value);
            $value = str_replace(YANA_RIGHT_DELIMITER, '&#'.ord(YANA_RIGHT_DELIMITER).';', $value);
            $value = str_replace('$', '&#'.ord('$').';', $value);
        }
        /*
         * (3) filter LINEBREAK
         */
        if ($escape & YANA_ESCAPE_LINEBREAK) {
            $value = trim(preg_replace("/\s/", " ", $value));
        }
        /*
         * (4) filter LINEBREAK
         */
        if ($escape & YANA_ESCAPE_USERTEXT) {
            /* Note: it is important to ensure that an already checked string
             * that undergoes the same procedure again remains unchanged,
             * to avoid double conversion.
             */
            $value = nl2br($value);
            /* white space */
            $value = preg_replace('/[\x00-\x1f]/', '', $value);
            /* length */
            if (is_int($length) && $length > 0) {
                $value = preg_replace("/^(.{" . $length . "}\S*).*/", '$1', $value);
                $value = preg_replace("/^(.{" . ($length + 100) . "}).*/", '$1', $value);
            }
            /* white space before start- and after end- tag */
            $value = preg_replace("/(\[\/\S+\])(\S)/U", '$1 $2', $value);
            $value = preg_replace("/(\S)(\[!(?:wbr|br|\/[^\s\]]+)\])/U", '$1 $2', $value);
            /* white space around emoticons */
            $value = preg_replace("/(\S)(\:\S+\:)(\S)/U", '$1 $2 $3', $value);
            /* line break */
            $value = preg_replace("/([^\b\s\[\]]{80})(\B)/", '$1[wbr]$2', $value);
            /* clean up */
            $value = preg_replace("/\[wbr\]\[wbr\]/", "[wbr]", $value);
            $value = preg_replace("/\<br\s*\/?>/", "[br]", $value);
            $value = preg_replace("/\s/", " ", $value);
            /* trim spaces */
            $value = trim($value);
            if ($doubleEncode) {
                $value = str_replace('&#'.ord(YANA_LEFT_DELIMITER).';', YANA_LEFT_DELIMITER, $value);
                $value = str_replace('&#'.ord(YANA_RIGHT_DELIMITER).';', YANA_RIGHT_DELIMITER, $value);
                $value = str_replace('&#36;', '$', $value);
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } else {
                $value = String::htmlSpecialChars($value, ENT_QUOTES, 'UTF-8', false);
            }
            /*
             * Escape Token-delimiters, to prevent possible code injection.
             * Note: YANA_UNI_DELIMITER has also to be a part of YANA_LEFT_DELIMITER and
             * YANA_RIGHT_DELIMITER, so these will also be escaped.
             */
            $value = str_replace(YANA_LEFT_DELIMITER, '&#'.ord(YANA_LEFT_DELIMITER).';', $value);
            $value = str_replace(YANA_RIGHT_DELIMITER, '&#'.ord(YANA_RIGHT_DELIMITER).';', $value);
            /*
             * Escape '$' character, to prevent possible code injection.
             */
            $value = str_replace('$', '&#36;', $value);
        }
    }
    /*
     * (5) special filter CODED
     *
     * complete conversion
     */
    if ($escape & YANA_ESCAPE_CODED) {
        $value = (string) ($value);
        $value = String::htmlEntities($value);
    }

    return $value;

}

?>