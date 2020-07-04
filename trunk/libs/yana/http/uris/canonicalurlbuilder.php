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

namespace Yana\Http\Uris;

/**
 * Build canonical request URIs.
 *
 * While this is similar to using $_SERVER['REQUEST_URI'], it is more reliable.
 *
 * Here is why you can't just trust $_SERVER['REQUEST_URI'] for this purpose:
 * <ol>
 *   <li> $_SERVER['REQUEST_URI'] is not available from CLI.  </li>
 *   <li> it is only 'complete' if $_SERVER['REQUEST_METHOD'] is 'GET', because ...  </li>
 *   <li> it does not include POST-vars!  </li>
 *   <li> even when the request method is 'post', there might also have been GET-vars sent.  </li>
 *   <li> REQUEST_URI is not listed in the default CGI environment variables.
 *        This means, servers are allowed to omit it!
 *        See the complete list here: http://hoohoo.ncsa.illinois.edu/cgi/env.html  </li>
 * </ol>
 *
 * Thus you must not rely on $_SERVER['REQUEST_URI'] and should use this class instead.
 *
 * Note that despite all efforts the result might still come up empty under rare circumstances,
 * where no server variables are available that would hint the URL used.
 * Be sure to handle empty output accordingly.
 *
 * @package     yana
 * @subpackage  http
 */
class CanonicalUrlBuilder extends \Yana\Http\Uris\Container implements \Yana\Http\Uris\IsUrlBuilder
{

    /**
     * Get canonical request URI.
     *
     * Returns a canonical request URI that is identical, no matter if the data was sent via GET,
     * or POST. You may use this function to calculate IDs or identify the page.
     *
     * The request URI is the URI which was given in order to access this page; like '/index.php'.
     *
     * @return  string
     */
    public function __invoke()
    {

        $uri = $this->_buildRequestAddress();
        /*
         * Method: GET
         */
        if ($this->getRequestMethod() === \Yana\Http\Requests\MethodEnumeration::GET) {
            $uri = str_replace('&', '&amp;', $uri);

            /*
             * Method: POST
             *
             * arguments are NOT stored in request uri, but in $_POST array.
             */
        } elseif ($this->getRequestMethod() === \Yana\Http\Requests\MethodEnumeration::POST) {
            $uri = str_replace('&', '&amp;', $uri);

            // append delimiter
            if (mb_strpos('?', $uri) !== false) {
                $uri .= '&amp;';
            } else {
                $uri .= '?';
            }

            // append posted vars
            foreach ($this->getPostVars() as $a => $b)
            {
                if (is_scalar($b)) {
                    $uri .= urlencode($a) . '=' . urlencode($b) . '&amp;';
                }
            }
            unset($a, $b);
        }

        return $uri;
    }

    /**
     * Build URL from known settings.
     *
     * @return  string
     */
    private function _buildRequestAddress()
    {

        if ($this->getRequestUri() > "") {
            $uri = $this->getRequestUri(); // Got an uri for the current request? Then use it.
            if ($this->getHttpHost() > "") {
                $uri = ($this->isHttps() ? 'https' : 'http') . '://' . $this->getHttpHost() . $uri;
            }

        } elseif ($this->getServerAddress() > "" && $this->getPhpSelf() > "") {
            $uri = ($this->isHttps() ? 'https' : 'http') . '://' . $this->getServerAddress() . $this->getPhpSelf();

        } elseif (count($this->getCommandLineArguments()) > 0) {
            assert(!isset($argv) && !isset($website_url), '!isset($argv) && !isset($website_url)');
            $argv = $this->getCommandLineArguments();
            $uri = 'php ' . print_r(array_shift($argv), true);
            while (count($argv) > 0)
            {
                $uri .= ' "' . print_r(array_shift($argv), true) . '"';
            }
            unset($argv);

        } elseif ($this->getPhpSelf() > "") {
            $uri = $this->getPhpSelf();

        } else {
            $uri = "";
        }

        return $uri;
    }

    /**
     * Build a canonical URL using $_SERVER super-globals.
     *
     * This function is more reliable than using $_SERVER['REQUEST_URI'].
     * Note however that it may still come up empty.
     * Be sure to handle empty results accordingly.
     *
     * @return  \Yana\Http\Uris\CanonicalUrlBuilder
     */
    public static function buildFromSuperGlobals()
    {
        $builder = new self();
        $builder->setIsHttps(!empty($_SERVER['HTTPS']));

        if (isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
            $builder->setCommandLineArguments($_SERVER['argv']);
            $builder->setRequestMethod(\Yana\Http\Requests\MethodEnumeration::CLI);

        } elseif (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD'])) {
            $builder->setRequestMethod($_SERVER['REQUEST_METHOD']);
        }

        if (isset($_SERVER['HTTP_HOST']) && is_string($_SERVER['HTTP_HOST'])) {
            $builder->setHttpHost($_SERVER['HTTP_HOST']);
        }

        if (isset($_SERVER['SCRIPT_NAME']) && is_string($_SERVER['SCRIPT_NAME'])) {
            $builder->setPhpSelf($_SERVER['SCRIPT_NAME']);
        }

        if (!empty($_POST) && is_array($_POST)) {
            $builder->setPostVars($_POST);
        }

        if (isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])) {
            $builder->setRequestUri($_SERVER['REQUEST_URI']);
        }

        if (isset($_SERVER['SERVER_ADDR']) && is_string($_SERVER['SERVER_ADDR'])) {
            $builder->setServerAddress($_SERVER['SERVER_ADDR']);
        }

        return $builder;
    }

}

?>