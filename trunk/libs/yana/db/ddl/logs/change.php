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

namespace Yana\Db\Ddl\Logs;

/**
 * database change-log sql statement
 *
 * This wrapper class represents the structure of a database
 *
 * @package     yana
 * @subpackage  db
 */
class Change extends \Yana\Db\Ddl\Logs\AbstractLog
{

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $xddlTag = "change";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlAttributes = array(
        'version'     => array('version',     'string'),
        'ignoreError' => array('ignoreError', 'bool'),
        'dbms'        => array('dbms',        'string'),
        'type'        => array('type',        'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlTags = array(
        'description' => array('description', 'string'),
        'logparam'    => array('parameters',  'array', null, 'name'),
    );

    /**
     * @var  string
     * @ignore
     */
    protected $dbms = null;

    /**
     * @var  string
     * @ignore
     */
    protected $type = null;

    /**
     * @var  array
     * @ignore
     */
    protected $parameters = array();

    /**
     * list of functions to apply changes to the database structure
     *
     * Note: the implementation, number and type of arguments depend on the
     * type of changes that have to be carried out.
     *
     * @var array
     * @ignore
     */
    protected static $handlers = array();

    /**
     * Initialize instance.
     *
     * @param  \Yana\Db\Ddl\ChangeLog  $parent  parent database
     */
    public function __construct(?\Yana\Db\Ddl\ChangeLog $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Get target DBMS.
     *
     * Returns the name of the target DBMS for this definition as a lower-cased string.
     * The default is "generic".
     *
     * @return  string|NULL
     */
    public function getDBMS(): ?string
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
     * @param   string  $dbms   target DBMS, defaults to "generic"
     * @return  $this
     */
    public function setDBMS(string $dbms = \Yana\Db\DriverEnumeration::GENERIC)
    {
        if ($dbms === "") {
            $this->dbms = null;
        } else {
            $this->dbms = strtolower($dbms);
        }
        return $this;
    }

    /**
     * Get sql statement.
     *
     * Returns the type of this operation.
     * This also sets which handler to use, as the handler is associated with a certain type.
     *
     * @return  string|NULL
     */
    public function getType(): ?string
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
     * @param   string  $type   type of this operation
     * @return  $this
     */
    public function setType(string $type = "default")
    {
        if ($type === "") {
            $this->type = null;
        } else {
            $this->type = $type;
        }
        return $this;
    }

    /**
     * Get list of parameters.
     *
     * Returns an associative array of parameters for this change.
     * These parameters are passed to the handler function.
     *
     * @return  array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Adds a new parameter to the parameter list.
     *
     * These parameters are passed to the handler function.
     *
     * @param   string       $value  parameter value
     * @param   string|NULL  $name   parameter name
     * @return  $this
     */
    public function addParameter(string $value, ?string $name = null)
    {
        assert(is_array($this->parameters), 'Member "parameters" is expected to be an array.');
        if (!is_string($name)) {
            $this->parameters[] = $value;
        } else {
            $this->parameters[$name] = $value;
        }
        return $this;
    }

    /**
     * Drops and resets the current list of parameters.
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
     * @param   callable  $functionName  name of the function which is called
     * @param   string    $functionType  function type
     */
    public static function setHandler(callable $functionName, string $functionType = "default")
    {
        self::$handlers[$functionType] = $functionName;
    }

    /**
     * Reset handler function.
     *
     * @param  string  $functionType  function type
     */
    public static function dropHandler(string $functionType = "default")
    {
        unset(self::$handlers[$functionType]);
    }

    /**
     * Reset all handler functions.
     */
    public static function dropHandlers()
    {
        self::$handlers = array();
    }

    /**
     * Calls the provided handler function.
     *
     * Provided arguments are the object's parameter list.
     * Returns bool(true) on success and bool(false) on error.
     *
     * @return  bool
     */
    public function commitUpdate(): bool
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
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\LogSql
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $ddl = new self($parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>
