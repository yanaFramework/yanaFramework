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
 * <<builder>> Request object.
 *
 * @package     yana
 * @subpackage  http
 */
class Builder extends \Yana\Http\Requests\Container
{

    /**
     * Create a request instance using custom settings and return it.
     *
     * @return  \Yana\Http\Requests\Request
     */
    public function __invoke()
    {
        return new \Yana\Http\Requests\Request($this);
    }

    /**
     * Create a request instance using super-global request vars and return it.
     *
     * Note that you should always use the "get", "post" and "cookie" values accordingly, if you expect a value to be transmitted a certain way.
     *
     * @return  \Yana\Http\Requests\IsRequest
     */
    public static function buildFromSuperGlobals()
    {
        assert('!isset($methodName); // Cannot redeclare var $methodName.');
        $methodName = (string) (isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : \Yana\Http\Requests\MethodEnumeration::CLI);
        $builder = new self();
        $builder
            ->setRequest(new \Yana\Http\Requests\ValueWrapper($_REQUEST))
            ->setGet(new \Yana\Http\Requests\ValueWrapper($_GET))
            ->setPost(new \Yana\Http\Requests\ValueWrapper($_POST))
            ->setCookie(new \Yana\Http\Requests\ValueWrapper($_COOKIE))
            ->setArguments(new \Yana\Http\Requests\ValueWrapper(self::_createArgumentsFromSuperGlobals()))
            ->setMethod(new \Yana\Http\Requests\Method($methodName));

        return $builder();
    }

    /**
     * Check if call was made using HTTP or command line and return parameters.
     *
     * @return  array
     */
    private static function _createArgumentsFromSuperGlobals()
    {
        assert('!isset($arguments); // Cannot redeclare var $arguments');
        $arguments = array();
        if (!empty($_SERVER['argv'])) { // for calls via command line - interface
            $arguments = array();
            assert('!isset($argument); // Cannot redeclare var $argument');
            foreach ($_SERVER['argv'] as $argument)
            {
                assert('!isset($m); // Cannot redeclare var $m');
                if (preg_match('/^([\w\d-_\.]*)=(.*)$/', "$argument", $m)) {
                    \Yana\Util\Hashtable::set($arguments, mb_strtolower($m[1]), $m[2]);
                }
                unset($m);
            }
            unset($argument);
        }
        assert('is_array($arguments); // Invalid post-condition: array expected for var $arguments');
        return $arguments;
    }

}

?>