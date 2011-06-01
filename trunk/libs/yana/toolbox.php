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
        }
        unset($tok);
    } else {
        $filter = preg_replace("/[^\.\-\_\w\d]/", "", $filter);
        $filter = preg_quote($filter, '/');
    }

    /* read contents from directory */
    $dirlist = array();
    if (is_dir($dir)) {
        $dirHandle = dir($dir);
        while($entry = $dirHandle->read())
        {
            if ($entry[0] !== '.' && ($filter === false || preg_match("/(?:{$filter})$/i", $entry))) {
                assert('is_array($dirlist); /* Invariant condition failed: $dirlist is not an array. */');
                switch ($switch)
                {
                    case YANA_GET_ALL:
                        $dirlist[] = $entry;
                    break;
                    case YANA_GET_DIRS:
                        if (is_dir($dir.$entry)) {
                            $dirlist[] = $entry;
                        }
                    break;
                    case YANA_GET_FILES:
                        if (is_file($dir.$entry)) {
                            $dirlist[] = $entry;
                        }
                    break;
                }
            }
        } // end while
        unset($entry);
        $dirHandle->close();
        sort($dirlist);
        assert('is_array($dirlist); /* Unexpected result: $dirlist is not an array. */');
    } else {
        trigger_error("The directory '{$dir}' does not exist.", E_USER_NOTICE);
    }
    return $dirlist;

}

?>