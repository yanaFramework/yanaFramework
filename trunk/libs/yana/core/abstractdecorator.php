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
 */

namespace Yana\Core;

/**
 * <<abstract, decorator>> Transparent decorator class.
 *
 * Use this as a base-class whenever you need to decorate an object.
 *
 * To "decorate" means: you take an object and wrap a class around it.
 * The decorating class implements the same (relevant) interface.
 * All calls to the decorating class are relayed to the wrapped object.
 * BUT: the decorating class has the chance to change any input and output before it is sent.
 * It may also actively hide non-relevant methods and properties.
 *
 * The "Decorator" pattern is used in cases, when you just don't wish to use inheritence.
 * The magic __call, __set and __get methods also make this very easy to implement in PHP.
 *
 * {@internal
 * Note that you may document magic functions and wrapped APIs using the @method and @property annotations.
 * }}
 *
 * Example:
 * <code>
 * class MyFormatter extends \Yana\Core\AbstractDecorator implements IsDateFormatter
 * {
 *     public function __construct(MyDate $date)
 *     {
 *         $this->_setDecoratedObject($object);
 *     }
 *
 *     public function getDate()
 *     {
 *         $object = $this->_getDecoratedObject();
 *         return date('r', $object->getDate());
 *     }
 * }
 * </code>
 *
 * NOTE: If you need a decorator, that decorates multiple objects,
 * you may want to take a look at the "Facade" pattern instead.
 *
 * @package     yana
 * @subpackage  core
 */
abstract class AbstractDecorator extends \Yana\Core\Object
{

    /**
     * list of items to work on
     *
     * @var  object
     */
    private $_object = null;

    /**
     * Returns the instance that all calls will be relayed to.
     *
     * @return  object
     */
    protected function _getDecoratedObject()
    {
        return $this->_object;
    }

    /**
     * Set a new object that will be decorated.
     *
     * @param   object  $object the new decorated object
     * @return  \Yana\Core\AbstractDecorator
     */
    protected function _setDecoratedObject($object)
    {
        assert('is_object($object); // $object expected to be an object');
        $this->_object = $object;
        return $this;
    }

    /**
     * <<magic>> Calls the wrapped object.
     *
     * @param  string  $name       method name
     * @param  array   $arguments  method arguments
     * @ignore
     */
    public function __call($name, array $arguments)
    {
        return call_user_func_array(array($this->_getDecoratedObject(), $name), $arguments);
    }

    /**
     * <<magic>> Retrieves properties from the decorated object.
     *
     * @param   string  $name  property name
     * @return  \Yana\Core\Object
     * @ignore
     */
    public function __get($name)
    {
        assert('is_string($name); // $name expected to be String');
        return $this->_object->$name;
    }

    /**
     * <<magic>> Writes changes to properties of the decorated object.
     *
     * @param   string  $name   property name
     * @param   string  $value  new value
     * @return  mixed
     * @ignore
     */
    public function __set($name, $value)
    {
        assert('is_string($name); // $name expected to be String');
        return $this->_object->$name = $value;
    }

}

?>