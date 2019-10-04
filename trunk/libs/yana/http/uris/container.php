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
 * Holds URL components.
 *
 * This class allows to inject certain URLs by manually setting server vars.
 * Use this for test-purposes.
 *
 * @package     yana
 * @subpackage  http
 * @ignore
 */
class Container extends \Yana\Core\StdObject implements \Yana\Http\Uris\IsContainer
{

    /**
     * @var  string
     */
    private $_requestUri = "";

    /**
     * @var  string
     */
    private $_requestMethod = "";

    /**
     * @var  string
     */
    private $_httpHost = "";

    /**
     * @var  string
     */
    private $_serverAddress = "";

    /**
     * @var  string
     */
    private $_commandLineArguments = array();

    /**
     * @var  string
     */
    private $_phpSelf = "";

    /**
     * @var  bool
     */
    private $_isHttps = false;

    /**
     * @var  array
     */
    private $_postVars = array();

    /**
     * Should be equal to $_SERVER['REQUEST_URI'].
     *
     * @return  string
     */
    public function getRequestUri()
    {
        return $this->_requestUri;
    }

    /**
     * Should be equal to $_SERVER['REQUEST_METHOD'].
     *
     * @return  string
     */
    public function getRequestMethod()
    {
        return $this->_requestMethod;
    }

    /**
     * Should be equal to $_SERVER['HTTP_HOST'].
     *
     * @return  string
     */
    public function getHttpHost()
    {
        return $this->_httpHost;
    }

    /**
     * Should be equal to $_SERVER['SERVER_ADDR'].
     *
     * @return  string
     */
    public function getServerAddress()
    {
        return $this->_serverAddress;
    }

    /**
     * Should be equal to $_SERVER['argv'].
     *
     * @return  array
     */
    public function getCommandLineArguments()
    {
        return $this->_commandLineArguments;
    }

    /**
     * Should be equal to $_SERVER['PHP_SELF'].
     *
     * @return  string
     */
    public function getPhpSelf()
    {
        return $this->_phpSelf;
    }

    /**
     * Returns bool(true) if protocol is HTTPS instead of HTTP.
     *
     * @return  array
     */
    public function isHttps()
    {
        return $this->_isHttps;
    }

    /**
     * Should be equal to $_POST.
     *
     * @return  array
     */
    public function getPostVars()
    {
        return $this->_postVars;
    }

    /**
     * Set request URI server setting.
     *
     * @param  string  $requestUri  should be equal to $_SERVER['REQUEST_URI']
     * @return \Yana\Http\Uris\Container
     */
    public function setRequestUri($requestUri)
    {
        $this->_requestUri = (string) $requestUri;
        return $this;
    }

    /**
     * Set request method server setting.
     *
     * @param  string  $requestMethod  should be equal to $_SERVER['REQUEST_METHOD']
     * @return \Yana\Http\Uris\Container
     */
    public function setRequestMethod($requestMethod)
    {
        $this->_requestMethod = (string) $requestMethod;
        return $this;
    }

    /**
     * Set HTTP host server setting.
     *
     * @param  string  $httpHost  should be equal to $_SERVER['HTTP_HOST']
     * @return \Yana\Http\Uris\Container
     */
    public function setHttpHost($httpHost)
    {
        $this->_httpHost = (string) $httpHost;
        return $this;
    }

    /**
     * Set server address server setting.
     *
     * @param  string  $serverAddress  should be equal to $_SERVER['SERVER_ADDR']
     * @return \Yana\Http\Uris\Container
     */
    public function setServerAddress($serverAddress)
    {
        $this->_serverAddress = (string) $serverAddress;
        return $this;
    }

    /**
     * Set argv server setting.
     *
     * @param  array  $commandLineArguments  should be equal to $_SERVER['argv']
     * @return \Yana\Http\Uris\Container
     */
    public function setCommandLineArguments(array $commandLineArguments)
    {
        $this->_commandLineArguments = $commandLineArguments;
        return $this;
    }

    /**
     * Set PHP self setting.
     *
     * @param  string  $phpSelf  should be equal to $_SERVER['PHP_SELF']
     * @return \Yana\Http\Uris\Container
     */
    public function setPhpSelf($phpSelf)
    {
        $this->_phpSelf = (string) $phpSelf;
        return $this;
    }

    /**
     * Set HTTPS setting.
     *
     * @param  string  $isHttps  should be set to bool(true) if $_SERVER['HTTPS'] is not empty
     * @return \Yana\Http\Uris\Container
     */
    public function setIsHttps($isHttps)
    {
        $this->_isHttps = (bool) $isHttps;
        return $this;
    }

    /**
     * Set POST variables.
     *
     * @param  array  $postVars  should be equal to $_POST
     * @return \Yana\Http\Uris\Container
     */
    public function setPostVars(array $postVars)
    {
        $this->_postVars = $postVars;
        return $this;
    }

}

?>