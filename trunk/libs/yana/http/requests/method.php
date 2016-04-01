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
 * Value-object to handle request methods.
 *
 * @package     yana
 * @subpackage  http
 */
class Method extends \Yana\Core\Object implements \Yana\Http\Requests\IsMethod
{

    /**
     * @var  string
     */
    private $_method = '';

    /**
     * Create a new instance using the given request vars.
     *
     * @param  string  $methodName  POST, GET aso
     */
    public function __construct($methodName)
    {
        assert('is_string($methodName); // Invalid argument type: $methodName. String expected.');
        assert($methodName === "" || \Yana\Http\Requests\MethodEnumeration::isValidItem($methodName));
        $this->_method = (string) $methodName;
    }

    /**
     * Returns method name.
     *
     * @return  string
     */
    protected function _getMethod()
    {
        return $this->_method;
    }

    /**
     * Returns bool(true) if the request method is POST.
     *
     * @return  bool
     */
    public function isPost()
    {
        return $this->_getMethod() === \Yana\Http\Requests\MethodEnumeration::POST;
    }

    /**
     * Returns bool(true) if the request method is GET.
     *
     * @return  bool
     */
    public function isGet()
    {
        return $this->_getMethod() === \Yana\Http\Requests\MethodEnumeration::GET;
    }

    /**
     * Returns bool(true) if the request method is PUT.
     *
     * @return  bool
     */
    public function isPut()
    {
        return $this->_getMethod() === \Yana\Http\Requests\MethodEnumeration::PUT;
    }

    /**
     * Returns bool(true) if the request method is DELETE.
     *
     * @return  bool
     */
    public function isDelete()
    {
        return $this->_getMethod() === \Yana\Http\Requests\MethodEnumeration::DELETE;
    }

    /**
     * Returns bool(true) if the request method is likely to have been made from CLI.
     *
     * @return  bool
     */
    public function isCommandLine()
    {
        return $this->_getMethod() === \Yana\Http\Requests\MethodEnumeration::CLI;
    }

}

?>