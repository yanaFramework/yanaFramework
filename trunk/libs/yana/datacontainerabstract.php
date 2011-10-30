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
 * <<abstract>> <<entity>> data container
 *
 * The AbstractDataContainer class is an <<observer>>.
 * It keeps track of the state of a data object by intercepting any changes applied to it.
 *
 * Example:
 * <code>
 * try {
 *   $container = AbstractDataContainer::getInstance("foo");
 * } catch (\Yana\Core\Exceptions\NotFoundException $e) {
 *   $container = AbstractDataContainer::createInstance("foo");
 * }
 * // do something
 * </code>
 *
 * @access      public
 * @abstract
 * @package     yana
 * @subpackage  core
 */
abstract class DataContainerAbstract extends \Yana\Core\Object
{
    /**
     * instance id
     *
     * @access  public
     * @var     string
     */
    public $id = "";

    /**
     * instance has been read since loading
     *
     * @access  private
     * @var     bool
     */
    private $_isRead = false;

    /**
     * instance has been modified since loading
     *
     * (needs to be updated)
     *
     * @access  private
     * @var     bool
     */
    private $_isModified = false;

    /**
     * instance has just been created
     *
     * (needs to be inserted)
     *
     * @access  private
     * @var     bool
     */
    private $_isNew = false;

    /**
     * instance has been dropped
     *
     * @access  private
     * @var     bool
     */
    private $_isDropped = false;

    /**
     * data adapter
     *
     * The DataAdapter is an interface. We use it to inject a dependency into the AbstractDataContainer.
     * The AbstractDataContainer uses the DataAdapter to read and write data from and to an arbitrary data source.
     *
     * @access  protected
     * @static
     * @var     IsDataAdapter
     * @ignore
     */
    protected static $_dataAdapter = null;

    /**
     * instances
     *
     * List of instances by id.
     * It is used to ensure every call to getInstance returns the same instance, so changes are not lost.
     *
     * @access  protected
     * @static
     * @var     array
     * @ignore
     */
    protected static $_instances = array();

    /**
     * hidden constructor
     *
     * @access  private
     * @final
     */
    private final function __construct()
    {
        // intentionally left blank
    }

    /**
     * get property
     *
     * Gets the property and marks the instance as read.
     *
     * @access  public
     * @param   string  $name   property name
     * @return  mixed
     */
    public function __get($name)
    {
        $this->_isRead = true;
        return $this->$name;
    }

    /**
     * set property
     *
     * Sets the property and marks the instance as modified.
     *
     * @access  public
     * @param   string  $name   property name
     * @param   mixed   $value
     */
    public function __set($name,  $value)
    {
        $this->$name = $value;
        $this->_isModified = true;
    }

    /**
     * get data adapter
     *
     * @access  protected
     * @static
     * @return  IsDataAdapter
     * @throws  \Yana\Core\Exceptions\NotImplementedException  if no data adapter has been registered
     */
    protected static function getDataAdapter()
    {
        if (!isset(self::$_dataAdapter)) {
            throw new \Yana\Core\Exceptions\NotImplementedException("No data adapter registered.");
        }
        return self::$_dataAdapter;
    }

    /**
     * register a new data adapter
     *
     * The DataAdapter is an interface.
     * We use it to inject a dependency into the AbstractDataContainer.
     * The AbstractDataContainer uses the DataAdapter to read and write data
     * from and to an arbitrary data source.
     *
     * @access  public
     * @static
     * @param   IsDataAdapter  $adapter  data adapter implementation
     */
    public static function registerDataAdapter(IsDataAdapter $adapter)
    {
        self::$_dataAdapter = $adapter;
    }

    /**
     * create instance
     *
     * This factory method calls the datasource to retrieve and initialize the instance.
     *
     * @param   string  $id  instance id
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException  if the instance already exists
     * @return  DataContainerAbstract
     */
    public static function createInstance($id)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');
        if (!isset(self::$_instances[$id])) {
            self::$_instances[$id] = new self();
            self::$_instances[$id]->id = $id;
            self::$_instances[$id]->new = true;
            self::$_instances[$id]->modified = false;
        }
        return self::$_instances[$id];
    }

    /**
     * get instance
     *
     * This factory method calls the datasource to retrieve and initialize the instance.
     *
     * @param   string  $id  instance id
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the instance does not exist
     * @return  DataContainerAbstract
     */
    public static function getInstance($id)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');
        if (!isset(self::$_instances[$id])) {
            self::$_instances[$id] = new self();
            self::$_instances[$id]->id = $id;
            self::$_instances[$id]->modified = false;
        }
        return self::$_instances[$id];
    }

    /**
     * Check if instance exists
     *
     * Returns bool(true) if there is an instance with the given id and bool(false) otherwise.
     *
     * @access   public
     * @abstract
     * @param    string  $id  instance id
     * @return   bool
     */
    public static function isValid($id)
    {
        return isset(self::$_instances[$id]) || self::getDataAdapter()->isValid($id);
    }

    /**
     * persistent object destructor
     *
     * @access  protected
     * @ignore
     */
    public function __destruct()
    {
        try {
            if ($this->_isModified) {
                self::getDataAdapter()->updateInstance($this);
            }
        } catch (\Exception $e) {
            // A destructor may not throw an exception, since there is nobody who could catch it.
            \Yana\Log\LogManager::getLogger()->addLog($e->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * mark the instance as dropped
     *
     * @access  public
     */
    public function dropInstance()
    {
        $this->_isDropped = true;
    }

    /**
     * Check if instance has been modifed
     *
     * @access  public
     * @return  bool
     */
    public function isModified()
    {
        return $this->_isModified;
    }

    /**
     * Check if instance has been read
     *
     * @access  public
     * @return  bool
     */
    public function isRead()
    {
        return $this->_isRead;
    }

    /**
     * Check if instance has just been created
     *
     * @access  public
     * @return  bool
     */
    public function isNew()
    {
        return $this->_isNew;
    }

    /**
     * Check if instance has been dropped
     *
     * @access  public
     * @return  bool
     */
    public function isDropped()
    {
        return $this->_isDropped;
    }

}

?>