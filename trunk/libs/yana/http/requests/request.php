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
 * Request object.
 *
 * @package     yana
 * @subpackage  http
 */
class Request extends \Yana\Core\Object implements \Yana\Http\Requests\IsRequest
{

    /**
     * @var  \Yana\Http\Requests\IsContainer
     */
    protected $_container = null;

    /**
     * Create a new instance using the given request vars.
     *
     * @param  \Yana\Http\Requests\IsContainer  $container  contains POST, GET and request vars
     */
    public function __construct(\Yana\Http\Requests\IsContainer $container)
    {
        $this->_container = $container;
    }

    /**
     * Get all values.
     *
     * This is as a combination of "get" and "post" vars, where "post" vars take precedence over "get" vars.
     * It does NOT include "cookie" vars.
     *
     * For command-line calls the function will return the "argv" array, presented as key-value pairs.
     * In order for this to work you have to write your arguments as follows:
     * <code>
     * php index.php arg1=value "arg2=value with spaces"
     * </code>
     *
     * To provide elements of an array on the command line, use:
     * <code>
     * php index.php arg.0=value1 arg.1=value2 arg.foo=bar
     * </code>
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function all()
    {
        if ($this->method()->isCommandLine()) {
            $wrappedValues = $this->_container->getArguments();
        } else {
            $allValues = \Yana\Util\Hashtable::merge($this->_container->getGet()->asUnsafeArray(), $this->_container->getPost()->asUnsafeArray());
            $wrappedValues = new \Yana\Http\Requests\ValueWrapper($allValues);
        }
        return $wrappedValues;
    }

    /**
     * Get ARGV values.
     *
     * Arguments should be given in the following format:
     * <code>
     * php index.php arg1=value "arg2=value with spaces"
     * </code>
     *
     * To provide elements of an array on the command line, use:
     * <code>
     * php index.php arg.0=value1 arg.1=value2 arg.foo=bar
     * </code>
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function args()
    {
        return $this->_container->getArguments();
    }

    /**
     * Get GET values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function get()
    {
        return $this->_container->getGet();
    }

    /**
     * Get POST values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function post()
    {
        return $this->_container->getPost();
    }

    /**
     * Get COOKIE values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function cookie()
    {
        return $this->_container->getCookie();
    }

    /**
     * Get REQUEST values.
     *
     * WARNING ON PORTABILITY:
     * Since PHP 5.3 the policy on which vars are imported in what order to the super-global $_REQUEST array
     * may be changed in php.ini using the "request_order" directive.
     *
     * Thus you MUST NOT rely on the contents of the $_REQUEST array to be portable across systems, unless
     * your code takes this behavior into account and you have very good reason to be sure you know what
     * the contents of $_REQUEST will be.
     *
     * As an alternative you may use the function "all()" instead, which provides a portable version that
     * ignores the "request_order" directive and always builds the array the same way.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function request()
    {
        return $this->_container->getRequest();
    }

    /**
     * Get request method.
     *
     * @return  \Yana\Http\Requests\IsMethod
     */
    public function method()
    {
        return $this->_container->getMethod();
    }

    /**
     * Returns bool(true) if the request was sent using AJAX.
     *
     * This relies on the "is_ajax_request" parameter being present, which is automaticall added,
     * if an AJAX request is sent using the API provided with the framework.
     *
     * @return  bool
     */
    public function isAjaxRequest()
    {
        return $this->get()->value("is_ajax_request", "")->asBool();
    }

}

?>