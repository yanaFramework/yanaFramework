<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana\Core;

/**
 * <<Utility>> Request object
 *
 * This class is meant to handle and provide request header information.
 *
 * @package     yana
 * @subpackage  core
 */
final class Request extends \Yana\Core\AbstractUtility
{

    /**
     * Untainted request vars send via method post
     *
     * @var  array
     */
    private static $post = null;

    /**
     * Untainted request vars send via method get
     *
     * @var  array
     */
    private static $get = null;

    /**
     * Untainted request vars
     *
     * @var  array
     */
    private static $request = null;

    /**
     * Untainted cookie vars
     *
     * @var  array
     */
    private static $cookie = null;

    /**
     * Rebuild file vars send via POST
     *
     * @var  array
     */
    private static $files = null;

    /**
     * Request uri
     *
     * @var  array
     */
    private static $uri = null;

    /**
     * Get canonical request URI.
     *
     * Returns a canonical request URI that is identical, no matter if the data was sent via GET,
     * or POST. You may use this function to calculate IDs or identify the page.
     *
     * The request URI is the URI which was given in order to access this page; for instance,
     * '/index.php'.
     *
     * Remember some properties of $_SERVER['REQUEST_URI']:
     * <ol>
     *   <li> it is not available from CLI.  </li>
     *   <li> it is only 'complete' if $_SERVER['REQUEST_METHOD'] is 'get', because ...  </li>
     *   <li> it does not include POST-vars!  </li>
     *   <li> even when the request method is 'post', there might also have been GET-vars sent.  </li>
     * </ol>
     *
     * Also REQUEST_URI is not listed in the default CGI environment variables.
     * This means, servers are allowed to omit it!
     * See the complete list here: http://hoohoo.ncsa.illinois.edu/cgi/env.html
     *
     * @return  string
     */
    public static function getUri()
    {
        if (!isset(self::$uri)) {

            /*
             * Method: GET
             */
            if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
                self::$uri = str_replace('&', '&amp;', $_SERVER['REQUEST_URI']);

            /*
             * Method: POST
             *
             * arguments are NOT stored in request uri, but in $_POST array.
             */
            } else {
                // Got an uri for the current request? Then use it.
                if (isset($_SERVER['REQUEST_URI'])) {
                    self::$uri = $_SERVER['REQUEST_URI'];

                } elseif (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
                    self::$uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                } elseif (isset($_SERVER['SERVER_ADDR']) && isset($_SERVER['PHP_SELF'])) {
                    self::$uri = 'http://' . $_SERVER['SERVER_ADDR'] . $_SERVER['PHP_SELF'];

                } elseif (isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
                    assert('!isset($argv) && !isset($website_url);');
                    $argv = $_SERVER['argv'];
                    self::$uri = 'php ' . print_r(array_shift($argv), true);
                    while (count($argv) > 0)
                    {
                        self::$uri .= ' "' . print_r(array_shift($argv), true) . '"';
                    }
                    unset($argv);

                } elseif (isset($_SERVER['PHP_SELF'])) {
                    self::$uri = $_SERVER['PHP_SELF'];

                } else {
                    $message = 'Unable to resolve website URL. ' .
                        'This feature will not be available on this server.';
                    trigger_error($message, E_USER_NOTICE);
                    self::$uri = "";

                    return self::$uri;
                }

                // append delimiter
                if (mb_strpos('?', self::$uri) !== false) {
                    self::$uri .= '&amp;';
                } else {
                    self::$uri .= '?';
                }

                // append posted vars
                foreach ($_POST as $a => $b)
                {
                    if (is_scalar($b)) {
                        self::$uri .= urlencode($a).'='.urlencode($b).'&amp;';
                    }
                }
            }
        }

        return self::$uri;
    }

    /**#@+
     * The input data is untainted automatically.
     *
     * "Untainted" means, the result is either a string with at most 50000 characters,
     * or an array of such strings, or the constant NULL if the key does not exist.
     *
     * If an input string contains a template token, or a '$' character, they are
     * automatically escaped to HTML entities. This is done to reduce the risk of code
     * injection.
     *
     * Note that this version is case-insensitive. This means "var", "Var" and "VAR"
     * all refer to the same input.
     *
     * If you call this function without the $key parameter or you use the wildcard '*'
     * the whole "request" array is returned.
     *
     * Important note: as a feature this function always returns unquoted
     * input, regardless if "magic quotes" is turned on or off. However this does leave
     * it to you to ensure for yourself that input is quoted where needed.
     * If you do not want this feature to be used, this behaviour can be turned off by
     * setting the constant YANA_AUTODEQUOTE to false.
     */

    /**
     * Get a value from the get vars.
     *
     * Returns the untainted value identified by $key or NULL if the value does not exist.
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     * @see     Request::getVars()
     */
    public static function getGet($key = '*')
    {
        if (is_null(self::$get)) {
            self::$get = self::_untaintRequest($_GET, YANA_AUTODEQUOTE && get_magic_quotes_gpc());
        }
        return \Yana\Util\Hashtable::get(self::$get, $key);
    }

    /**
     * Get a value from the post vars.
     *
     * Returns the untainted value identified by $key or NULL if the value does not exist.
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     * @see     Request::getVars()
     */
    public static function getPost($key = '*')
    {
        if (is_null(self::$post)) {
            self::$post = self::_untaintRequest($_POST, YANA_AUTODEQUOTE && get_magic_quotes_gpc());
        }
        return \Yana\Util\Hashtable::get(self::$post, $key);
    }

    /**
     * Get a value from the cookie vars.
     *
     * Returns the untainted value identified by $key or NULL if the value does not exist.
     * Note: that cookies may have been disabled on the client machine.
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     * @see     Request::getVars()
     */
    public static function getCookie($key = '*')
    {
        if (is_null(self::$cookie)) {
            self::$cookie = self::_untaintRequest($_COOKIE, YANA_AUTODEQUOTE && get_magic_quotes_gpc());
        }
        return \Yana\Util\Hashtable::get(self::$cookie, $key);
    }


    /**
     * Get a value from the request vars.
     *
     * Returns the value identified by $key from an untainted, merged copy of the $_POST and $_GET arrays.
     *
     * NOTE IN COMPATIBILITY:
     * PHP's policy on which vars to import in what order to the super-global $_REQUEST may have been changed
     * in php.ini. Thus it might contain other values than expected.
     * This function thus does not rely on $_REQUEST, but builds it itself. This ensures you always get the
     * expected behavior, regardless of settings in php.ini.
     * 
     * If called via a command line interface, this will return the input arguments instead.
     * In this case you should write your arguments as follows:
     * <code>
     * php index.php arg1=value "arg2=value with spaces"
     * </code>
     *
     * To provide elements of an array on the command line, use:
     * <code>
     * php index.php arg.0=value1 arg.1=value2 arg.foo=bar
     * </code>
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     * @name    Request::getVars()
     */
    public static function getVars($key = '*')
    {
        if (is_null(self::$request)) {
            self::$request = array();

            // for calls via web - interface
            if (!empty($_REQUEST)) {
                $request = \Yana\Util\Hashtable::merge($_GET, $_POST);
                self::$request = self::_untaintRequest($request, YANA_AUTODEQUOTE && get_magic_quotes_gpc());

            // for calls via command line - interface
            } elseif (!empty($_SERVER['argv'])) {
                foreach ($_SERVER['argv'] as $argument)
                {
                    if (preg_match('/^([\w\d-_\.]*)=(.*)$/', "$argument", $m)) {
                        \Yana\Util\Hashtable::set(self::$request, mb_strtolower($m[1]), $m[2]);
                    }
                }
            }
        }
        return \Yana\Util\Hashtable::get(self::$request, $key);
    }

    /**#@-*/

    /**
     * Build file array from $_FILES input-
     *
     * Why you need this function?
     * Well, it's use comes when you use nested forms and
     * send the input as arrays.
     *
     * If so, PHP provides the following input:
     * <code>
     * array(
     *   'Outer' => array(
     *     'name' => array(
     *       'Inner' => array(
     *          'column1' => 'filename1',
     *          'column2' => 'filename2'
     *       )
     *     ),
     *     'type' => array(
     *       'Inner' => array(
     *          'column1' => 'type1',
     *          'column2' => 'type2'
     *       )
     *     ),
     *     'tmp_name' => array(
     *       'Inner' => array(
     *          'column1' => 'temp_name1',
     *          'column2' => 'temp_name2'
     *       )
     *     )
     *     // ...
     *   )
     * );
     * </code>
     *
     * Obviously this is not what you would expect.
     * With an input like this, it's not that easy to iterate over all input files.
     *
     * So this function transforms this to:
     * <code>
     * array(
     *   'outer' => array(
     *     'inner' => array(
     *       'column1' => array(
     *          'name' => 'filename1',
     *          'type' => 'type1',
     *          'tmp_name' => 'temp_name1'
     *          // ...
     *       ),
     *       'column2' => array(
     *          'name' => 'filename2',
     *          'type' => 'type2',
     *          'tmp_name' => 'temp_name2'
     *          // ...
     *       )
     *     )
     *   )
     * );
     * </code>
     *
     * Now you may just call:
     * <code>
     * foreach (Request::getFiles("outer.inner") as $columName => $fileData)
     * {
     *     print "Uploaded file: " . $fileData['name'] . "\n";
     *     print "Using MIME-type: " . $fileData['type'] . "\n";
     *     move_uploaded_file($fileData['tmp_name'], $dir . $columnName . ".dat");
     * }
     * </code>
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  array
     */
    public static function getFiles($key = '*')
    {
        if (is_null(self::$files)) {
            self::$files = array();
            if (!empty($_FILES)) {
                $files = array_change_key_case($_FILES, CASE_LOWER);
                foreach ($files as $name => $file)
                {
                    $checkedItem = array();
                    foreach ($file as $property => $item)
                    {
                        if (is_array($item)) {
                            $item = self::_buildFileArray($item, $property);
                            $checkedItem = \Yana\Util\Hashtable::merge($checkedItem, $item);
                        }
                    }
                    unset($item, $property);
                    self::$files[$name] = $checkedItem;
                }
                unset($file);
                self::$files = self::_removeEmptyFiles(self::$files);
            }
        }
        return \Yana\Util\Hashtable::get(self::$files, $key);
    }

    /**
     * Build file array from $_FILES input.
     *
     * Converts:
     * <code>
     * $property = 'name';
     * $files = array(
     *   'ddldefaultinsertiterator' => array(
     *     'column1' => 'filename1',
     *     'column2' => 'filename2'
     *   )
     * );
     * </code>
     *
     * To this:
     * <code>
     * array(
     *   'ddldefaultinsertiterator' => array(
     *     'column1' => array(
     *       'name' => 'filename1'
     *     ),
     *     'column2' => array(
     *       'name' => 'filename2'
     *     ),
     *   )
     * );
     * </code>
     *
     * @param   mixed   $files     file array or scalar property
     * @param   string  $property  one of: name, type, size, tmp_name, error
     * @return  array
     */
    private static function _buildFileArray($files, $property)
    {
        if (is_array($files)) {
            $result = array();
            $files = array_change_key_case($files, CASE_LOWER);
            foreach ($files as $key => $item)
            {
                $result[$key] = self::_buildFileArray($item, $property);
            }
            return $result;
        } else {
            return array($property => $files);
        }
    }

    /**
     * Remove empty (phantom) entries.
     *
     * PHP adds entries to $_FILES array even when no file was uploaded at all, adding a phantom error
     * UPLOAD_ERR_NO_FILE.
     *
     * To avoid bogus error messages and unexpected behavior, we remove these phantom files from the upload list.
     *
     * @param   array  $files  list of files
     * @return  array
     */
    private static function _removeEmptyFiles(array $files)
    {
        foreach($files as $key => &$item)
        {
            if (is_array($item)) {
                $item = self::_removeEmptyFiles($item);
                if (empty($item)) {
                    unset($files[$key]);
                }
            } elseif (isset($files['error']) && $files['error'] === UPLOAD_ERR_NO_FILE) {
                return null;
            } else {
                break;
            }
        }
        return $files;
    }

    /**
     * Untaint request vars-
     *
     * @param   array   $value    request vars
     * @param   bool    $unquote  true: strip slashes, false: leave slashes alone
     * @return  array
     */
    private static function _untaintRequest(array $value, $unquote = false)
    {
        $value = array_change_key_case($value, CASE_LOWER);
        $sanitizer = new \Yana\Data\StringValidator();
        $sanitizer->setMaxLength(50000)
            ->addOption(\Yana\Data\StringValidator::TOKEN);
        foreach ($value as $i => $item)
        {
            if (is_array($item)) {
                $value[$i] = self::_untaintRequest($value[$i], $unquote);
            } else {
                if ($unquote === true) {
                    $item = stripcslashes($item);
                }
                $value[$i] = $sanitizer($item);
            }
        }
        return $value;
    }

}

?>