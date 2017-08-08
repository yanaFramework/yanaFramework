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
 *
 * @ignore
 */

namespace Yana\Views\Helpers\Formatters;

/**
 * <<formatter>> This class encapsulates an extension for HTML creation.
 *
 * @package     yana
 * @subpackage  views
 */
class UrlFormatter extends \Yana\Core\Object implements \Yana\Views\Helpers\IsFormatter
{

    /**
     * @var string
     */
    private static $_baseUrl = '';

    /**
     * Create a new instance.
     *
     * This also loads the base URL configuration.
     * Adds the script name, current session id and current profile id to base URL.
     */
    public function __construct()
    {
        $YANA = \Yana\Application::getInstance();
        if (isset($YANA)) {
            self::$_baseUrl = $YANA->getVar('PHP_SELF')  . "?id=" . \Yana\Application::getInstance()->getProfileId();
        } else {
            self::$_baseUrl = $_SERVER['PHP_SELF'] . "?";
        }
        if (empty($_COOKIE) && @session_id() != "") {
            self::$_baseUrl .= "&" . session_name() . "=" . session_id();
        }
    }

    /**
     * Sets the prefix for the generated URL.
     *
     * @param  string  $url  prefix, possibly including protocol part and server address
     */
    public static function setBaseUrl($url)
    {
        self::$_baseUrl = $url;
    }

    /**
     * Returns the prefix for the generated URL.
     *
     * @return string
     */
    protected function _getBaseUrl()
    {
        return self::$_baseUrl;
    }

    /**
     * Check for HTTPS vs HTTP.
     *
     * Returns bool(true) if $_SERVER settings suggest that the current request was made using HTTPS.
     * Returns bool(false) otherwise.
     *
     * @internal  $_SERVER['HTTPS'] is "on" when PHP is installed as an Apache module.
     *            Since this setting is webserver-specific $_SERVER['HTTPS'] may also be "1" or "0".
     *            $_SERVER['HTTP_X_FORWARDED_PROTO'] is 'https' when PHP is installed as CGI for Apache.
     *
     * @return    bool
     */
    private function _isHttps()
    {
        return (!empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], "off") !== 0) ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], "https") === 0);
    }

    /**
     * Encode URL.
     *
     * Encodes the values in a search-string of a given URL so it can be safely output to HTML.
     *
     * @param   string  $parameterList  url parameter list
     * @return  string
     */
    private function _encodeParameters($parameterList)
    {
        assert('is_string($parameterList); // Invalid argument $parameterList: string expected');

        assert('!isset($m); // Cannot redeclare var $m');
        $m = array();
        preg_match_all("/(&|^)(.*)=(.*)(&|$)/U", $parameterList, $m);
        assert('!isset($i); // Cannot redeclare var $i');
        assert('!isset($replace); // Cannot redeclare var $replace');
        for ($i = 0; $i < count($m[0]); $i++)
        {
            $replace = $m[1][$i] . urlencode($m[2][$i]) . "=" . urlencode($m[3][$i]) . $m[4][$i];
            $parameterList = str_replace($m[0][$i], $replace, $parameterList);
        }
        unset($replace, $i);

        return $parameterList;
    }

    /**
     * Creates an URL to the script itself from a search-string fragment.
     *
     * @param   string   $string           url parameter list
     * @param   bool     $asString         decide wether entities in string should be encoded or not
     * @param   bool     $asAbsolutePath   decide wether function should return relative or absolut path
     * @return  string
     */
    public function __invoke($string, $asString = false, $asAbsolutePath = true)
    {
        assert('is_string($string); // Wrong type for argument "string". String expected');
        assert('is_bool($asString); // Invalid argument $asString: bool expected');
        assert('is_bool($asAbsolutePath); // Invalid argument $asAbsolutePath: bool expected');

        assert('!isset($url); // Cannot redeclare var $url');
        $url = "";

        /**
         * create absolute path on demand
         *
         * This includes the current protocol, domain name and script path
         */
        if ($asAbsolutePath === true) {
            if ($this->_isHttps() === true) {
                $url .= "https://";
            } else {
                $url .= "http://";
            }
            if (isset($_SERVER['HTTP_HOST'])) {
                $url .= $_SERVER['HTTP_HOST'];
            } else {
                $url .= $_SERVER['SERVER_NAME'];
            }
            assert('!isset($dirname); // Cannot redeclare var $dirname');
            $dirname = dirname($_SERVER['PHP_SELF']);
            if ($dirname !== DIRECTORY_SEPARATOR) {
                $url .= $dirname . '/';
            } else {
                $url .= '/';
            }
            unset($dirname);
        }

        /**
         * add custom search-string fragment
         *
         * This encodes special characters found in the fragment,
         * depending on the $asString argument.
         */
        assert('!isset($urlPath); // Cannot redeclare var $urlPath');
        $urlPath = $this->_getBaseUrl() . '&' . $this->_encodeParameters($string);
        if ($asString === false) {
            $urlPath = \Yana\Util\Strings::htmlSpecialChars($urlPath);
        }
        $url .= $urlPath;

        return $url;
    }

}

?>