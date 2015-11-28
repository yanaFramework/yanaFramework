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
 * Object base class for all entities used in the framework.
 * 
 * It protects against common pitfalls, by throwing an exception
 * whenever you try to access an undefiend method or property.
 *
 * @package     yana
 * @subpackage  core
 */
class Object extends \StdClass implements \Yana\Core\IsObject, \Yana\Core\IsCloneable
{

    /**
     * <<magic>> String conversion.
     *
     * This is automatically used to "unbox" the object when used in a string context.
     *
     * You are encouraged to implement this for each derived subclass,
     * to reflect your implementation and purpose of your class.
     *
     * @return  string
     */
    public function __toString()
    {
        return "Instance of '" . $this->getClass() . "'.";
    }

    /**
     * <<magic>> Issues a warning when trying to call an undefined method.
     *
     * @param string $name       method name
     * @param array  $arguments  method arguments
     * @throws  \Yana\Core\Exceptions\UndefinedMethodException  always!
     */
    public function __call($name, array $arguments)
    {
        assert('is_string($name); // $name expected to be String');
        throw new \Yana\Core\Exceptions\UndefinedMethodException($name . " in " . $this->getClass());
    }

    /**
     * <<magic>> Issues a warning when trying to access undefined property.
     *
     * @param   string  $name  property name
     * @return  \Yana\Core\Object
     * @throws  \Yana\Core\Exceptions\UndefinedPropertyException  always!
     * @ignore
     */
    public function __get($name)
    {
        assert('is_string($name); // $name expected to be String');
        throw new \Yana\Core\Exceptions\UndefinedPropertyException($name);
    }

    /**
     * <<magic>> Issues a warning when trying to access undefined property.
     *
     * @param   string  $name   property name
     * @param   mixed   $value  new value
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\UndefinedPropertyException  always!
     * @ignore
     */
    public function __set($name, $value)
    {
        assert('is_string($name); // $name expected to be String');
        throw new \Yana\Core\Exceptions\UndefinedPropertyException($name);
    }

    /**
     * <<magic>> Deep copy.
     *
     * This is automatically used to create copies of the object when
     * using the "clone" keyword.
     *
     * Note that this function will be trying to create deep copies, in case
     * the attribute to clone is an object.
     * Deep copies are created by relying upon a '__clone()' method within the
     * object, that is expected to return a deep copy of it.
     *
     * @ignore
     */
    public function __clone()
    {
        foreach (get_class_vars(get_class($this)) as $attribute => $defaultValue)
        {
            if (is_object($this->$attribute)) {
                $this->$attribute = clone $this->$attribute;
            } elseif (is_array($this->$attribute)) {
                $this->$attribute = \Yana\Util\Hashtable::cloneArray($this->$attribute);
            } else {
                // nothing to do
            }
        }
    }

    /**
     * Returns the name of this object's class as a string.
     *
     * @return string
     */
    public function getClass()
    {
        return get_class($this);
    }

    /**
     * Returns TRUE if the objects are equal and FALSE otherwise.
     *
     * You are encouraged to overwrite this function in subclasses
     * to reflect your implementation.
     *
     * @param  \Yana\Core\IsObject $anotherObject another object to compare
     * @return bool
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        return $this == $anotherObject;
    }

}

?>