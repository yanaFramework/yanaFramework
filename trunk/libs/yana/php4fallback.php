<?php
/**
 * Fall back functions
 *
 * This file is to collect fall-backs for older versions of PHP, in order to
 * enhance backwards compatibility.
 *
 * These will apply automatically.
 * If the original function does not exist in the installed version of PHP,
 * the fall-back function listed here will fill the gap so your code will no
 * be broken.
 *
 * Of course these implementations won't reach the same performance as the
 * original functions - but they will work.
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
 * @ignore
 */

/**#@+
 * CONSTANTS
 *
 * @ignore
 */

/* Missing constants in 5.1, originally appeared in 4.0. */
if (!defined("M_SQRTPI")) {
    define("M_SQRTPI", 1.7724538509055);
}
if (!defined("M_LNPI")) {
    define("M_LNPI", 1.1447298858494);
}
if (!defined("M_EULER")) {
    define("M_EULER", 0.57721566490153);
}
if (!defined("M_SQRT3")) {
    define("M_SQRT3", 1.7320508075689);
}

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

/* removed in PHP5.3.0 */
if (!defined("MHASH_CRC32")) {
    define("MHASH_CRC32", 0);
}
if (!defined("MHASH_MD5")) {
    define("MHASH_MD5", 1); /* RFC1321 */
}
if (!defined("MHASH_SHA1")) {
    define("MHASH_SHA1", 2); /* RFC3174 */
}
if (!defined("MHASH_TIGER")) {
    define("MHASH_TIGER", 7);
}
if (!defined("MHASH_MD4")) {
    define("MHASH_MD4", 16); /* RFC1320 */
}
if (!defined("MHASH_SHA256")) {
    define("MHASH_SHA256", 17);
}
if (!defined("MHASH_ADLER32")) {
    define("MHASH_ADLER32", 18);
}

/**#@-*/

/* FUNCTIONS */

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
?>