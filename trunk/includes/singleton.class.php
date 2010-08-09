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
 * <<abstract>> Singleton
 *
 * To create a Singleton class, simply add "extends Singleton" to your class definition
 * copy the pattern as defined here.
 *
 * Note: this class is abstract, because it extends an abstract super-class without
 * implementing all abstract functions - NOT because it declares any abstract members
 * itself.
 *
 * The PHP manual (like others) explains the singleton pattern as follows:
 *
 * (Citation) The Singleton pattern applies to situations in which there needs to be a
 * single instance of a class. The most common example of this is a database
 * connection. Implementing this pattern allows a programmer to make this single
 * instance easily accessible by many other objects.
 *
 * Note that the full implementation of this pattern was not possible in PHP versions
 * prior PHP5.
 *
 * Here is an example implementation, that you may copy for your singleton-class.
 * <code>
 * class Foo extends Singleton
 * {
 *     private static $instance = null;
 *     public static function &getInstance()
 *     {
 *         if (!isset(self::$instance)) {
 *             self::$instance = new Foo();
 *         }
 *         return self::$instance;
 *     }
 * }
 * </code>
 *
 * Note! If you wish to serialize a singleton, be aware that you MUST
 * set the self::$instance var when you unserialize the object.
 * To do so you should implement the interface IsSerializable like this.
 * <code>
 * class Foo extends Singleton implements IsSerializable
 * {
 *    public function serialize()
 *    {
 *        return serialize($this);
 *    }
 *    public static function unserialize($string)
 *    {
 *        if (!isset(self::$instance)) {
 *            self::$instance = unserialize($string);
 *            return self::$instance;
 *        } else {
 *            return self::$instance;
 *        }
 *    }
 * }
 * </code>
 *
 * Also remember that you MUST NOT unserialize a string using any other
 * function or you will breach the singleton pattern.
 * 
 * @abstract
 * @access      public
 * @package     yana
 * @subpackage  core
 */
abstract class Singleton extends Object implements IsSingleton
{
    /**
     * This is a place-holder for the singleton's instance
     *
     * @access  private
     * @static
     */
    private static $instance = null;

    /**
     * constructor
     *
     * To prevent the constructor from being called directly
     *
     * @access private
     */
    private function __construct()
    {
        /* intentionally left blank */
    }

    /**
     * get instance of this class
     *
     * Creates an instance if there is none.
     * Then it returns a reference to this (single) instance.
     *
     * Note: There is a weakness in this pattern, if you wish
     * to call it that.
     * Since this function must be able to call the constructor
     * but takes no arguments, the constructor must also not
     * take any input arguments, unless they are static.
     *
     * Also the function needs to be copied to each sub-class.
     * This is due to the fact that the function cannot access
     * a static attribute of a sub-class. This works for the
     * current and it's parent classes only.
     *
     * @access public
     * @static
     */
    public static function &getInstance()
    {
        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class();
        }
        return self::$instance;
    }

}

?>
