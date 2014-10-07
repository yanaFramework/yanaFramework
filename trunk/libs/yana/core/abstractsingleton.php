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
 * <<abstract>> Singleton.
 *
 * To create a Singleton class, simply add "extends Singleton" to your class definition.
 *
 * The PHP manual (like others) explains the singleton pattern as follows:
 *
 * Citation: "The Singleton pattern applies to situations in which there needs to be a
 * single instance of a class. The most common example of this is a database
 * connection. Implementing this pattern allows a programmer to make this single
 * instance easily accessible by many other objects."
 *
 * Note! This class comes with a __wakeup() method that is required to reinitialize
 * self::$_instance if you serialized and then unserialized a singleton.
 * As a result, your sub-classes may not implement the Serializable interface.
 *
 * Also note: if you implement your own __wakeup() method, you MUST call parent::__wakeup() as well.
 * 
 * @package     yana
 * @subpackage  core
 */
abstract class AbstractSingleton extends \Yana\Core\Object implements \Yana\Core\IsSingleton
{

    /**
     * This is a place-holder for the singleton's instance
     *
     * @var  \Yana\Core\IsSingleton[]
     */
    private static $_instances = array();

    /**
     * Private constructor.
     *
     * To prevent the constructor from being called directly.
     */
    protected function __construct()
    {
        /* intentionally left blank */
    }

    /**
     * Creates an instance if there is none.
     *
     * Then it returns a reference to this (single) instance.
     *
     * Note: There is a weakness in this pattern, if you wish
     * to call it that.
     * Since this function must be able to call the constructor
     * but takes no arguments, the constructor must also not
     * take any input arguments, unless they are static.
     *
     * @return  \Yana\Core\AbstractSingleton
     */
    public static function getInstance()
    {
        $callerClassName = static::_getClassName();
        if (!isset(self::$_instances[$callerClassName])) {
            self::$_instances[$callerClassName] = static::_createNewInstance();
        }
        return self::$_instances[$callerClassName];
    }

    /**
     * Hook-method that allows you to create the instance yourself.
     *
     * Use this if you have to prepare the class prior to use.
     * Or if you wish to custom-load the instance from cache.
     * Note! This event does not fire, if you unserialize from cache, to prevent infinite loops.
     *
     * Please overwrite this in your sub-classes where needed.
     *
     * @return \Yana\Core\IsSingleton
     */
    protected static function _createNewInstance()
    {
        return new static();
    }

    /**
     * Reinitialize instance.
     */
    public function __wakeup()
    {
        self::$_instances[$this->getClass()] = $this;
    }

}

?>