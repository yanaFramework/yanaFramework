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
 * <<interface>> Holds URL components.
 *
 * Meant to inject certain URLs by manually setting server vars.
 * Use this for test-purposes.
 *
 * @package     yana
 * @subpackage  http
 * @ignore
 */
interface IsContainer
{

    /**
     * Should be equal to $_SERVER['REQUEST_URI'].
     *
     * @return  string
     */
    public function getRequestUri();

    /**
     * Should be equal to $_SERVER['REQUEST_METHOD'].
     *
     * @return  string
     */
    public function getRequestMethod();

    /**
     * Should be equal to $_SERVER['HTTP_HOST'].
     *
     * @return  string
     */
    public function getHttpHost();

    /**
     * Should be equal to $_SERVER['SERVER_ADDR'].
     *
     * @return  string
     */
    public function getServerAddress();

    /**
     * Should be equal to $_SERVER['argv'].
     *
     * @return  string
     */
    public function getCommandLineArguments();

    /**
     * Should be equal to $_SERVER['PHP_SELF'].
     *
     * @return  string
     */
    public function getPhpSelf();

    /**
     * Returns bool(true) if protocol is HTTPS instead of HTTP.
     *
     * @return  array
     */
    public function isHttps();

    /**
     * Should be equal to $_POST.
     *
     * @return  array
     */
    public function getPostVars();

    /**
     * Set request URI server setting.
     *
     * @param  string  $requestUri  should be equal to $_SERVER['REQUEST_URI']
     * @return \Yana\Http\Uris\IsContainer
     */
    public function setRequestUri($requestUri);

    /**
     * Set request method server setting.
     *
     * @param  string  $requestMethod  should be equal to $_SERVER['REQUEST_METHOD']
     * @return \Yana\Http\Uris\IsContainer
     */
    public function setRequestMethod($requestMethod);

    /**
     * Set HTTP host server setting.
     *
     * @param  string  $httpHost  should be equal to $_SERVER['HTTP_HOST']
     * @return \Yana\Http\Uris\IsContainer
     */
    public function setHttpHost($httpHost);

    /**
     * Set server address server setting.
     *
     * @param  string  $serverAddress  should be equal to $_SERVER['SERVER_ADDR']
     * @return \Yana\Http\Uris\IsContainer
     */
    public function setServerAddress($serverAddress);

    /**
     * Set argv server setting.
     *
     * @param  array  $commandLineArguments  should be equal to $_SERVER['argv']
     * @return \Yana\Http\Uris\IsContainer
     */
    public function setCommandLineArguments(array $commandLineArguments);

    /**
     * Set PHP self setting.
     *
     * @param  string  $phpSelf  should be equal to $_SERVER['PHP_SELF']
     * @return \Yana\Http\Uris\IsContainer
     */
    public function setPhpSelf($phpSelf);

    /**
     * Set POST variables.
     *
     * @param  array  $postVars  should be equal to $_POST
     * @return \Yana\Http\Uris\IsContainer
     */
    public function setPostVars(array $postVars);

}

?>