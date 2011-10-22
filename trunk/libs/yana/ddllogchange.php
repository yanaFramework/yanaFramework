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
 * database change-log sql statement
 *
 * This wrapper class represents the structure of a database
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLLogChange extends DDLLog
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "change";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'version'     => array('version',     'string'),
        'ignoreError' => array('ignoreError', 'bool'),
        'dbms'        => array('dbms',        'string'),
        'type'        => array('type',        'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string'),
        'logparam'    => array('parameters',  'array', null, 'name'),
    );

    /** @var string */ protected $dbms = null;
    /** @var string */ protected $type = null;
    /** @var array  */ protected $parameters = array();

    /**#@-*/

    /**
     * list of functions to apply changes to the database structure
     *
     * Note: the implementation, number and type of arguments depend on the
     * type of changes that have to be carried out.
     *
     * @access  protected
     * @static
     * @var array
     * @ignore
     */
    protected static $handlers = array();

    /**
     * Initialize instance.
     *
     * @param  DDLChangeLog  $parent  parent database
     */
    public function __construct(DDLChangeLog $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Get target DBMS.
     *
     * Returns the name of the target DBMS for this definition as a lower-cased string.
     * The default is "generic".
     *
     * @access  public
     * @return  string
     */
    public function getDBMS()
    {
        if (is_string($this->dbms)) {
            return $this->dbms;
        } else {
            return null;
        }
    }

    /**
     * Set target DBMS.
     *
     * While you may settle for any target DBMS you want and provide it in any kind of writing you
     * choose, you should remind, that not every DBMS is supported by the database API provided
     * here.
     *
     * The special "generic" DBMS-value means that the constraint is suitable for any DBMS.
     * Any DBMS other than "generic" will limit the setting to that DBMS only.
     *
     * @access  public
     * @param   string  $dbms   target DBMS, defaults to "generic"
     * @return  DDLLogChange
     */
    public function setDBMS($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('empty($dbms) || in_array($dbms, DDLDatabase::getSupportedDBMS()); // Unsupported DBMS');
        if (empty($dbms)) {
            $this->dbms = null;
        } else {
            $this->dbms = "$dbms";
        }
        return $this;
    }

    /**
     * Get sql statement.
     *
     * Returns the type of this operation.
     * This also sets which handler to use, as the handler is associated with a certain type.
     *
     * @access  public
     * @return  string
     */
    public function getType()
    {
        if (is_string($this->type)) {
            return $this->type;
        } else {
            return null;
        }
    }

    /**
     * Set type of change.
     *
     * Set the type the type of this operation.
     * This also sets which handler to use, as the handler is associated with a certain type.
     *
     * @access  public
     * @param   string  $type   type of this operation
     * @return  DDLLogChange
     */
    public function setType($type = "default")
    {
        assert('is_string($type); // Wrong type for argument 1. String expected');
        if (empty($type)) {
            $this->type = null;
        } else {
            $this->type = "$type";
        }
        return $this;
    }

    /**
     * Get list of parameters.
     *
     * Returns an associative array of parameters for this change.
     * These parameters are passed to the handler function.
     *
     * @access  public
     * @return  array
     */
    public function getParameters()
    {
        assert('is_array($this->parameters); // Member "parameters" is expected to be an array.');
        return $this->parameters;
    }

    /**
     * Adds a new parameter to the parameter list.
     *
     * These parameters are passed to the handler function.
     *
     * @access  public
     * @param   string  $value  parameter value
     * @param   string  $name   parameter name
     */
    public function addParameter($value, $name = null)
    {
        assert('is_string($value); // Wrong type for argument 1. String expected');
        assert('is_null($name) || is_string($name); // Wrong type for argument 2. String expected');
        assert('is_array($this->parameters); // Member "parameters" is expected to be an array.');
        if (is_null($name)) {
            $this->parameters[] = "$value";
        } else {
            $this->parameters["$name"] = "$value";
        }
    }

    /**
     * Drops and resets the current list of parameters.
     *
     * @access  public
     */
    public function dropParameters()
    {
        $this->parameters = array();
    }

    /**
     * Set function to handle updates.
     *
     * Provided arguments for handler are the object's parameter list.
     *
     * @access  public
     * @param   string|array  $functionName     name of the function which is called
     * @param   string        $functionType     function type
     * @throws  \Yana\Core\InvalidArgumentException  when the given function is not callable
     */
    public static function setHandler($functionName, $functionType = "default")
    {
        assert('is_string($functionType); // Wrong argument type for argument 2. String expected');
        if (is_callable($functionName)) {
            self::$handlers["$functionType"] = $functionName;
        } else {
            throw new \Yana\Core\InvalidArgumentException("The function name '$functionName' is not callable.", E_USER_WARNING);
        }
    }

    /**
     * Calls the provided handler function.
     *
     * Provided arguments are the object's parameter list.
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @return  bool
     */
    public function commitUpdate()
    {
        $type = $this->getType();
        if (is_null($type)) {
            $type = "default";
        }
        if (isset(self::$handlers[$type])) {
            return call_user_func(self::$handlers[$type], $this->getParameters());
        } else {
            return false;
        }
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  DDLLogSql
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $ddl = new self($parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>