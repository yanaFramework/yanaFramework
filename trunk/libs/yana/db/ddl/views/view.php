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

namespace Yana\Db\Ddl\Views;

/**
 * database view structure
 *
 * Basically "views" are stored select-statement.
 * Thus they may span multiple tables, aggregate data or hide information.
 * To the user they may act like real tables. Views are widely used for forms and user interfaces.
 *
 * However, you should note that there are some restrictions.
 * If you want to change data in a view, the view has to be updatable.
 * The support for this features depends on the chosen DBMS and some vendors even limit this to
 * certain (very simple) scenarios. Basically spoken, an "updateable view" must know the primary
 * key and source table for each and every column in the view.
 * The where-clause of the statement specifies some sort of constraint and a view may demand, that
 * every updated or inserted column is still part of the view and thus justifying this constraint.
 *
 * @access      public
 * @package     yana
 * @subpackage  db
 */
class View extends \Yana\Db\Ddl\AbstractNamedObject implements \Yana\Db\Ddl\IsIncludableDDL
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "view";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'        => array('name',         'nmtoken'),
        'readonly'    => array('readonly',     'bool'),
        'tables'      => array('tables',       'array'),
        'where'       => array('where',        'string'),
        'orderby'     => array('orderBy',      'array'),
        'sorting'     => array('_sorting',     'string'),
        'checkoption' => array('_checkOption', 'string'),
        'title'       => array('title',        'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string'),
        'grant'       => array('grants',      'array', 'Yana\Db\Ddl\Grant'),
        'field'       => array('fields',      'array', 'Yana\Db\Ddl\Views\Field', 'column'),
        'select'      => array('queries',     'array', null,           'dbms')
    );

    /** @var string         */ protected $description = null;
    /** @var string         */ protected $title = null;
    /** @var bool           */ protected $readonly = null;
    /** @var int            */ protected $checkOption = null;
    /** @var array          */ protected $tables = array();
    /** @var array          */ protected $orderBy = array();
    /** @var \Yana\Db\Ddl\Views\Field[] */ protected $fields = array();
    /** @var array          */ protected $queries = array();
    /** @var string         */ protected $where = null;
    /** @var bool           */ protected $descendingOrder = null;
    /** @var \Yana\Db\Ddl\Grant[]     */ protected $grants = array();
    /** @var \Yana\Db\Ddl\Database    */ protected $parent = null;

    /**#@-*/
    /**#@+
     * properties for persistance mapping: object <-> XDDL
     *
     * @ignore
     * @access  protected
     */

    /** @var string  */ protected $_checkOption = null;
    /** @var string  */ protected $_sorting = null;
    /**#@-*/

    /**
     * Initialize instance.
     *
     * @param  string       $name    foreign key name
     * @param  \Yana\Db\Ddl\Database  $parent  parent database
     */
    public function __construct($name, \Yana\Db\Ddl\Database $parent = null)
    {
        parent::__construct($name);
        $this->parent = $parent;
    }

    /**
     * Get parent database.
     *
     * @return  \Yana\Db\Ddl\Database
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get title.
     *
     * The title is a label text that should be displayed in the UI when viewing this object.
     *
     * It is optional. If it is not set, the function returns NULL instead.
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        if (is_string($this->title)) {
            return $this->title;
        } else {
            return null;
        }
    }

    /**
     * Set title.
     *
     * Sets the title used to display the object in the UI.
     * To reset the property, leave the parameter empty.
     *
     * @access  public
     * @param   string  $title  some text
     * @return  \Yana\Db\Ddl\Views\View
     */
    public function setTitle($title = "")
    {
        assert('is_string($title); // Wrong type for argument 1. String expected');
        if (empty($title)) {
            $this->title = null;
        } else {
            $this->title = "$title";
        }
        return $this;
    }

    /**
     * Get the description.
     *
     * The description serves two purposes:
     * 1st as offline-documentation 2nd as online-documentation.
     *
     * The form-generator may use the description to provide context-sensitive
     * help or additional information (depending on it's implementation) on a
     * auto-generated database application.
     *
     * The description is optional. If there is none, the function will return
     * NULL instead. Note that the description may also contain an identifier
     * for automatic translation.
     *
     * @access  public
     * @return  string
     */
    public function getDescription()
    {
        if (is_string($this->description)) {
            return $this->description;
        } else {
            return null;
        }
    }

    /**
     * Set the description property.
     *
     * The description serves two purposes:
     * 1st as offline-documentation 2nd as online-documentation.
     *
     * Note that the description may also contain an identifier for automatic
     * translation.
     *
     * To reset the property, leave the parameter $description empty.
     *
     * @access  public
     * @param   string  $description  new value of this property
     * @return  \Yana\Db\Ddl\Views\View
     */
    public function setDescription($description)
    {
        assert('is_string($description); // Wrong type for argument 1. String expected');
        if (empty($description)) {
            $this->description = null;
        } else {
            $this->description = "$description";
        }
        return $this;
    }

    /**
     * Check whether the dbo has read-only access.
     *
     * Returns bool(true) if the view is read-only and bool(false) otherwise.
     *
     * The default is bool(false).
     *
     * @access  public
     * @return  bool
     */
    public function isReadonly()
    {
        return !empty($this->readonly);
    }

    /**
     * Set read-only access.
     *
     * You may set the view to be read-only to prevent any changes to it by setting this to
     * bool(true).
     * A view that is "read-only" is not updatable.
     *
     * @access  public
     * @param   bool  $isReadonly   new value of this property
     * @return  \Yana\Db\Ddl\Views\View
     */
    public function setReadonly($isReadonly)
    {
        assert('is_bool($isReadonly); // Wrong type for argument 1. Boolean expected');
        $this->readonly = (bool) $isReadonly;
        return $this;
    }

    /**
     * Check whether or not to use check-option.
     *
     * If true, the where-clause of the select-statement will be interpreted as
     * check-constraint. All input will be validated against the where-clause
     * and rejected, if it doesn't satisfy the constraint.
     *
     * @access  public
     * @return  bool
     * @see     \Yana\Db\Ddl\Views\View::getCheckOption()
     */
    public function hasCheckOption()
    {
        return !empty($this->checkOption);
    }

    /**
     * Get type of check option.
     *
     * Get the behavior of the check option.
     * Returns one of the following constants:
     * <ul>
     *   <li> \Yana\Db\Ddl\Views\View::NONE - no check option </li>
     *   <li> \Yana\Db\Ddl\Views\View::CASCADED - recursive checks </li>
     *   <li> \Yana\Db\Ddl\Views\View::LOCAL - local checks only </li>
     * </ul>
     *
     * The difference between "local" and "cascaded" applies only to situations,
     * where a view is built recursively upon another view and the parent view
     * declares a check-option itself.
     * If this is the case, the setting "local" will prevent the DBS from
     * recursively evaluating the ceck option(s) of the parent view(s).
     * Note that this is not supported by all DBMS. E.g. MySQL and PostgreSQL
     * both support this feature, while MSSQL does not.
     *
     * \Yana\Db\Ddl\Views\ConstraintEnumeration::NONE evaluates to bool(false),
     * while both \Yana\Db\Ddl\Views\ConstraintEnumeration::CASCADED and
     * \Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL evaluate to bool(true).
     *
     * @access  public
     * @return  int
     * @name    \Yana\Db\Ddl\Views\View::getCheckOption()
     */
    public function getCheckOption()
    {
        return (int) $this->checkOption;
    }

    /**
     * set read-only access
     *
     * The parameter $checkOption may be one of the following constants:
     * <ul>
     *   <li> \Yana\Db\Ddl\Views\ConstraintEnumeration::NONE - no check option </li>
     *   <li> \Yana\Db\Ddl\Views\ConstraintEnumeration::CASCADED - recursive checks </li>
     *   <li> \Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL - local checks only </li>
     * </ul>
     *
     * @access  public
     * @param   int  $checkOption   new value of this property
     * @see     \Yana\Db\Ddl\Views\View::getCheckOption()
     * @return  \Yana\Db\Ddl\Views\View 
     */
    public function setCheckOption($checkOption)
    {
        assert('is_numeric($checkOption); // Wrong type for argument 1. Integer expected');
        switch($checkOption) {
            case \Yana\Db\Ddl\Views\ConstraintEnumeration::NONE:
            case \Yana\Db\Ddl\Views\ConstraintEnumeration::CASCADED:
            case \Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL:
                $this->checkOption = $checkOption;
            break;
            default:
                $this->checkOption = \Yana\Db\Ddl\Views\ConstraintEnumeration::NONE;
            break;
        }
        return $this;
    }

    /**
     * Get field by name.
     *
     * Returns the \Yana\Db\Ddl\Views\Field item with the given name from the current view.
     * If no such item can be found, an exception will be thrown.
     *
     * @access  public
     * @param   string  $name   field name
     * @return  \Yana\Db\Ddl\Views\Field
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the given field does not exist
     */
    public function getField($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        } else {
            throw new \Yana\Core\Exceptions\NotFoundException("Field with the name '$name' is not defined");
        }
    }

    /**
     * Get list of fields.
     *
     * Returns an associative array of all \Yana\Db\Ddl\Views\Field items in this view.
     * The keys are the unique names of the fields.
     * If no field has been defined, the returned array will be empty.
     *
     * @access  public
     * @return  array
     */
    public function getFields()
    {
        assert('is_array($this->fields); // Wrong type for argument 1. array expected');
        if (count($this->fields) != 0) {
            return $this->fields;
        } else {
            return array();
        }
    }

    /**
     * Add field by name.
     *
     * Adds a field element by the given name and returns it.
     * Throws an exception if a field with the given name already exists.
     *
     * @access  public
     * @param   string  $name   field name
     * @return  \Yana\Db\Ddl\Views\Field
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException  when another field with the same name already exists
     */
    public function addField($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (!isset($this->fields[$name])) {
            $this->fields[$name] = new \Yana\Db\Ddl\Views\Field($name);
            return $this->fields[$name];
        } else {
            $message = "Another field with the name '$name' is already defined.";
            $level = E_USER_WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($name);
            throw $exception;
        }
    }

    /**
     * Removes a field element by the given name if it exists.
     *
     * @access  public
     * @param   string  $name  name of the droped field
     */
    public function dropField($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (isset($this->fields[$name])) {
            unset($this->fields[$name]);
        }
    }

    /**
     * Get SQL-query by dbms.
     *
     * Returns the source code of the SQL query as a string or NULL if none has been defined.
     *
     * @access  public
     * @param   string  $dbms  target DBMS, defaults to "generic"
     * @return  string
     */
    public function getQuery($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('in_array($dbms, \Yana\Db\Ddl\Database::getSupportedDBMS()); // Unsupported DBMS');
        if (isset($this->queries[$dbms])) {
            return $this->queries[$dbms];
        } else {
            return null;
        }
    }

    /**
     * Get list of SQL-queries.
     *
     * Returns a list of source codes of the SQL queries as an array.
     * The syntax depends on the chosen DBMS.
     *
     * If there are none defined, the function returns an empty array.
     *
     * @access  public
     * @return  array
     */
    public function getQueries()
    {
        if (!empty($this->queries)) {
            return $this->queries;
        } else {
            return array();
        }

    }

    /**
     * Set SQL-query.
     *
     * Sets the source code of the SQL query. The syntax depends on the chosen DBMS.
     *
     * @access  public
     * @param   string  $query  sql query
     * @param   string  $dbms   target DBMS, defaults to "generic"
     */
    public function setQuery($query, $dbms = "generic")
    {
        assert('is_string($query); // Wrong type for argument 1. String expected');
        assert('is_string($dbms); // Wrong type for argument 2. String expected');
        $dbms = strtolower($dbms);
        assert('in_array($dbms, \Yana\Db\Ddl\Database::getSupportedDBMS()); // Unsupported DBMS');
        if (empty($query)) {
            unset($this->queries["$dbms"]);
        } else {
            $this->queries["$dbms"] = "$query";
        }
        return $this->queries;
    }

    /**
     * Drops the SQL-query for the chosen DBMS if there is any.
     *
     * @access  public
     * @param   string  $dbms  target DBMS, defaults to "generic"
     */
    public function dropQuery($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('in_array($dbms, \Yana\Db\Ddl\Database::getSupportedDBMS()); // Unsupported DBMS');
        if (isset($this->queries["$dbms"])) {
            unset($this->queries["$dbms"]);
        }
    }

    /**
     * Get list of tables.
     *
     * This is a generic information.
     *
     * Returns the list of tables used in the current view.
     * If the array contains more than one table, the first is the base table
     * and all other tables are joined to this.
     *
     * For joined table, don't forget to define a where-clause.
     *
     * @access  public
     * @return  array
     */
    public function getTables()
    {
        assert('is_array($this->tables); // Wrong type for argument 1. array expected');
        return $this->tables;
    }

    /**
     * Set list of tables.
     *
     * This is a generic information.
     *
     * Sets the list of tables used in the current view.
     * If the array contains more than one table, the first is the base table
     * and all other tables are joined to this.
     *
     * For joined table, don't forget to define a where-clause.
     *
     * Note the array may not be empty. All given tables must be defined in the
     * current database structure definition.
     *
     * @access  public
     * @param   array  $tables  list of tables
     * @throws  \Yana\Core\Exceptions\NotFoundException         when a table does not exist
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the list of tables is empty
     * @return  \Yana\Db\Ddl\Views\View 
     */
    public function setTables(array $tables)
    {
        assert('is_array($tables); // Wrong type for argument 1. array expected');
        if (isset($this->parent)) {
            foreach ($tables as $table)
            {
                if (!$this->parent->isTable($table)) {
                    throw new \Yana\Core\Exceptions\NotFoundException("No such table '$table'.");
                }
            }
        }
        if (!empty($tables)) {
            $this->tables = $tables;
        } else {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Parameter with the name '$tables' can not be empty.");
        }
        return $this;
    }

    /**
     * Get where-clause.
     *
     * This is a generic information.
     *
     * Returns the where-clause of the view if there is one, or NULL if not
     * defined.
     *
     * @access  public
     * @return  string
     */
    public function getWhere()
    {
        if (is_string($this->where)) {
            return $this->where;
        } else {
            return null;
        }
    }

    /**
     * Set where-clause.
     *
     * This is a generic information.
     *
     * Sets the where-clause of the view.
     * The parameter $where has the following syntax:
     * {[TABLE].}[COLUMN][=|<|>|LIKE][{[TABLE].}COLUMN|VALUE]
     * { [AND|OR] [{[TABLE].}COLUMN][=|<|>|LIKE][{[TABLE].}COLUMN|VALUE]}
     *
     * Example: "t1.id=t2.id AND value > 1000"
     *
     * Note! This will NOT work: "time < now()" as the function "now()" is not
     * portable between DBMS. However, you may define explicit SQL-statements
     * for any target-DBMS of your choice.
     * Also you may not use sub-expressions, for this is not (yet) supported by
     * the generic interpreter.
     *
     * @access  public
     * @param   string  $where  where clausel
     */
    public function setWhere($where)
    {
        assert('is_string($where); // Wrong type for argument 1. String expected');
        if (empty($where)) {
            $this->where = null;
        } else {
            $this->where = "$where";
        }
    }

    /**
     * Get list of sorting-columns.
     *
     * Returns a list of columns for sorting the output.
     * If no order-by-clause has been defined, the returned array is empty.
     *
     * @access  public
     * @return  array
     */
    public function getOrderBy()
    {
        assert('is_array($this->orderBy); // Wrong type for argument 1. Array expected');
        return (!empty($this->orderBy)) ? $this->orderBy : array();
    }

    /**
     * Set list of sorting-columns.
     *
     * Set a list of columns for sorting the output.
     * You may provide an empty array for $orderBy, to reset the property.
     * By default the table-output is ordered by it's primary-key.
     *
     * To reverse the sorting direction, you may set the second parameter
     * $isDesc to bool(true).
     *
     * Throws an exception if one or more columns don't exist.
     *
     * @access  public
     * @param   array  $orderBy  list of column names
     * @param   bool   $isDesc   sorting order (false = ascending, true = descending)
     * @return  \Yana\Db\Ddl\Views\View
     */
    public function setOrderBy(array $orderBy, $isDesc = false)
    {
        assert('is_array($orderBy); // Wrong type for argument 1. Array expected');
        assert('is_bool($isDesc); // Wrong type for argument 2. Boolean expected');

        $this->orderBy = $orderBy;
        $this->descendingOrder = (bool) $isDesc;
        return $this;
    }

    /**
     * Get sorting order.
     *
     * Returns bool(false) for ascending, and bool(true) for descending order.
     * The default is bool(false).
     *
     * @access  public
     * @return  bool
     */
    public function isDescendingOrder()
    {
        return !empty($this->descendingOrder);
    }

    /**
     * Get rights management settings.
     *
     * Returns an array of \Yana\Db\Ddl\Grant objects.
     *
     * Note! If no grant is defined, the form is considered to be public and the
     * resulting array will be empty.
     *
     * If at least one grant is set, any user that does not match the given
     * restrictions is not permitted to access the form.
     *
     * @access  public
     * @return  array
     */
    public function getGrants()
    {
        assert('is_array($this->grants); // Member "grants" is expected to be an array.');
        return $this->grants;
    }

    /**
     * Drop rights management settings.
     *
     * {@link \Yana\Db\Ddl\Grant}s control the access permissions granted to the user.
     *
     * Note! If no grant is defined, the form is considered to be public.
     *
     * If at least one grant is set, any user that does not match the given
     * restrictions is not permitted to access the form.
     *
     * @access  public
     */
    public function dropGrants()
    {
        $this->grants = array();
    }

    /**
     * Set rights management setting.
     *
     * {@link \Yana\Db\Ddl\Grant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the configuration.
     *
     * @access  public
     * @param   \Yana\Db\Ddl\Grant  $grant    new grant object (rights management)
     * @return  \Yana\Db\Ddl\Views\View
     */
    public function setGrant(\Yana\Db\Ddl\Grant $grant)
    {
        $this->grants[] = $grant;
        return $this;
    }

    /**
     * Add rights management setting.
     *
     * {@link \Yana\Db\Ddl\Grant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the configuration by using the given
     * options and returns it as an \Yana\Db\Ddl\Grant object.
     *
     * @access  public
     * @param   string  $user   user group
     * @param   string  $role   user role
     * @param   int     $level  security level
     * @return  \Yana\Db\Ddl\Grant
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when $level is out of range [0,100]
     */
    public function addGrant($user = null, $role = null, $level = null)
    {
        assert('is_null($user) || is_string($user); // Wrong type for argument 1. String expected');
        assert('is_null($role) || is_string($role); // Wrong type for argument 2. String expected');
        assert('is_null($level) || is_int($level);  // Wrong type for argument 3. Integer expected');
        $grant = new \Yana\Db\Ddl\Grant();
        if (!empty($user)) {
            $grant->setUser($user);
        }
        if (!empty($role)) {
            $grant->setRole($role);
        }
        // may throw an \Yana\Core\Exceptions\InvalidArgumentException
        if (!is_null($level)) {
            $grant->setLevel($level);
        }
        $this->grants[] = $grant;
        return $grant;
    }

    /**
     * <<magic>> Returns a ViewField, with the given attribute name.
     *
     * @access  public
     * @param   string $name  name
     */
    public function __get($name)
    {
        return $this->getField($name);
    }

    /**
     * Serialize this object to XDDL.
     *
     * Returns the serialized object as a string in XML-DDL format.
     *
     * @access  public
     * @param   \SimpleXMLElement $parentNode  parent node
     * @return  \SimpleXMLElement
     */
    public function serializeToXDDL(\SimpleXMLElement $parentNode = null)
    {
        if ($this->descendingOrder) {
            $this->_sorting = 'descending';
        } else {
            $this->_sorting = 'ascending';
        }
        switch ($this->checkOption)
        {
            case \Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL:
                $this->_checkOption = 'local';
            break;
            case \Yana\Db\Ddl\Views\ConstraintEnumeration::CASCADED:
                $this->_checkOption = 'cascaded';
            break;
            default:
                $this->_checkOption = 'none';
            break;
        }
        return parent::serializeToXDDL($parentNode);
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\Views\View
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the name attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
        }
        $ddl = new self((string) $attributes['name'], $parent);
        $ddl->descendingOrder = ($ddl->_sorting !== 'ascending');
        switch ($ddl->_checkOption)
        {
            case 'local':
                $ddl->checkOption = \Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL;
            break;
            case 'cascaded':
                $ddl->checkOption = \Yana\Db\Ddl\Views\ConstraintEnumeration::CASCADED;
            break;
            default:
                $ddl->checkOption = \Yana\Db\Ddl\Views\ConstraintEnumeration::NONE;
            break;
        }
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>