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

/**
 * Object
 *
 * This is a base class for all entities
 * used in the framework. It provides
 * same basic functionality that is common
 * for all subclasses.
 *
 * @abstract
 * @access      public
 * @package     yana
 * @subpackage  core
 */
class Object extends StdClass implements IsObject, IsCloneable
{

    /**
     * get a string representation of this object
     *
     * This function is intended to be called when the object
     * is used in a string context.
     *
     * You are encouraged to implement this for each derived subclass,
     * to reflect your implementation and purpose of your class.
     *
     * @access  public
     * @return  string
     */
    public function toString()
    {
        return "Instance of '".$this->getClass()."'.\n";
    }

    /**
     * magic function
     *
     * This is automatically used to "unbox" the object when used in
     * a string context. The function itself became available in PHP 5.
     * In versions prior to that, you had to call the function manually.
     *
     * Wide support for this became available as of PHP 5.2.
     *
     * @access  public
     * @return  string
     *
     * @since   2.8.5
     * @ignore
     */
    public function __toString()
    {
        try {
            return $this->toString();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * magic function
     *
     * Issues a warning when trying to access undefined property.
     *
     * @access  public
     * @param   string  $name   name
     * @return  Object
     * @throws  Error
     * @ignore
     */
    public function __get($name)
    {
        throw new Error("Trying to access undefined property '$name'.");
    }

    /**
     * magic function
     *
     * Issues a warning when trying to access undefined property.
     *
     * @access  public
     * @param   string  $name   name
     * @param   string  $value  value
     * @return  Object
     * @throws  Error
     * @ignore
     */
    public function __set($name, $value)
    {
        throw new Error("Trying to access undefined property '$name'.");
    }

    /**
     * magic function
     *
     * This is automatically used to create copies of the object when
     * using the "clone" keyword.
     *
     * Note that this function will be trying to create deep copies, in case
     * the attribute to clone is an object.
     * Deep copies are created by relying upon a '__clone()' method within the
     * object, that is expected to return a deep copy of it.
     *
     * @access  public
     * @since   2.8.5
     * @ignore
     */
    public function __clone()
    {
        foreach (get_class_vars(get_class($this)) as $attribute => $defaultValue)
        {
            if (is_object($this->$attribute)) {
                $this->$attribute = clone $this->$attribute;
            } elseif (is_array($this->$attribute)) {
                $this->$attribute = Hashtable::cloneArray($this->$attribute);
            } else {
                // nothing to do
            }
        }
    }

    /**
     * get the class name of the instance
     *
     * This function returns the name of the class of this object as a string.
     *
     * @access public
     * @return string
     * @since  2.8.5
     */
    public function getClass()
    {
        return get_class($this);
    }

    /**
     * compare with another object
     *
     * Returns bool(true) if this object and $anotherObject
     * are the same and bool(false) otherwise.
     *
     * You are encouraged to overwrite this function in subclasses
     * to reflect your implementation.
     *
     * @access public
     * @param  object $anotherObject another object to compare
     * @return bool
     * @since  2.8.5
     */
    public function equals(object $anotherObject)
    {
        if ($this == $anotherObject) {
            return true;
        } else {
            return false;
        }
    }

}

?>