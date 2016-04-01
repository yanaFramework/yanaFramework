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

namespace Yana\Http\Requests;

/**
 * Request conainer to keep vars together.
 *
 * @package     yana
 * @subpackage  http
 */
class Container extends \Yana\Core\Object implements \Yana\Http\Requests\IsContainer
{

    /**
     * @var \Yana\Http\Requests\ValueWrapper
     */
    private $_request = null;

    /**
     * @var \Yana\Http\Requests\ValueWrapper
     */
    private $_get = null;

    /**
     * @var \Yana\Http\Requests\ValueWrapper
     */
    private $_post = null;

    /**
     * @var \Yana\Http\Requests\ValueWrapper
     */
    private $_cookie = null;

    /**
     * @var \Yana\Http\Requests\ValueWrapper
     */
    private $_arguments = null;

    /**
     * @var array
     */
    private $_files = array();

    /**
     * @var \Yana\Http\Requests\IsMethod
     */
    private $_method = null;

    /**
     * Get all request parameters combined in one array.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function getRequest()
    {
        if (!isset($this->_request)) {
            $this->_request = new \Yana\Http\Requests\ValueWrapper();
        }
        return $this->_request;
    }

    /**
     * Get parameters.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function getGet()
    {
        if (!isset($this->_get)) {
            $this->_get = new \Yana\Http\Requests\ValueWrapper();
        }
        return $this->_get;
    }

    /**
     * Get post data.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function getPost()
    {
        if (!isset($this->_post)) {
            $this->_post = new \Yana\Http\Requests\ValueWrapper();
        }
        return $this->_post;
    }

    /**
     * Get data from cookies.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function getCookie()
    {
        if (!isset($this->_cookie)) {
            $this->_cookie = new \Yana\Http\Requests\ValueWrapper();
        }
        return $this->_cookie;
    }

    /**
     * Get command line arguments.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function getArguments()
    {
        if (!isset($this->_arguments)) {
            $this->_arguments = new \Yana\Http\Requests\ValueWrapper();
        }
        return $this->_arguments;
    }

    /**
     * Get information about uploaded files.
     *
     * @return  array
     */
    public function getFiles()
    {
        return $this->_files;
    }

    /**
     * Return string identifying the transfer method as POST, GET, PUT or DELETE.
     *
     * @return  \Yana\Http\Requests\IsMethod
     */
    public function getMethod()
    {
        if (!isset($this->_method)) {
            $this->_method = new \Yana\Http\Requests\Method("");
        }
        return $this->_method;
    }

    /**
     * Set request data.
     * 
     * @param   \Yana\Http\Requests\ValueWrapper  $request  should be wrapping $_REQUEST array
     * @return  \Yana\Http\Requests\Container
     */
    public function setRequest(\Yana\Http\Requests\ValueWrapper $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Set get parameters.
     * 
     * @param   \Yana\Http\Requests\ValueWrapper  $get  should be wrapping $_GET array
     * @return  \Yana\Http\Requests\Container
     */
    public function setGet(\Yana\Http\Requests\ValueWrapper $get)
    {
        $this->_get = $get;
        return $this;
    }

    /**
     * Set post data.
     * 
     * @param   \Yana\Http\Requests\ValueWrapper  $post  should be wrapping $_POST array
     * @return  \Yana\Http\Requests\Container
     */
    public function setPost(\Yana\Http\Requests\ValueWrapper $post)
    {
        $this->_post = $post;
        return $this;
    }

    /**
     * Set cookie info.
     * 
     * @param   \Yana\Http\Requests\ValueWrapper  $cookie  should be wrapping $_COOKIE array
     * @return  \Yana\Http\Requests\Container
     */
    public function setCookie(\Yana\Http\Requests\ValueWrapper $cookie)
    {
        $this->_cookie = $cookie;
        return $this;
    }

    /**
     * Set command line arguments.
     * 
     * @param   \Yana\Http\Requests\ValueWrapper  $arguments  parsed contents of $_SERVER['argv']
     * @return  \Yana\Http\Requests\Container
     */
    public function setArguments(\Yana\Http\Requests\ValueWrapper $arguments)
    {
        $this->_arguments = $arguments;
        return $this;
    }

    /**
     * Set uploaded files info.
     *
     * @param   array  $files  should be like $_FILE array
     * @return  \Yana\Http\Requests\Container
     */
    public function setFiles(array $files)
    {
        $this->_files = $files;
        return $this;
    }

    /**
     * Set request method (POST, GET, etc).
     *
     * @param   \Yana\Http\Requests\IsMethod  $method  see also: \Yana\Http\Requests\MethodEnumeration
     * @return  \Yana\Http\Requests\Container
     */
    public function setMethod(\Yana\Http\Requests\IsMethod $method)
    {
        $this->_method = $method;
        return $this;
    }

}

?>