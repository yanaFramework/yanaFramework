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

namespace Yana\Db\Ddl;

/**
 * database form structure
 *
 * This wrapper class represents the structure of a database
 *
 * Forms are named objects, that are used as input for a form or GUI generator. They must have at
 * least a base table and a template.
 * The base table may of course also be an (updatable) view.
 *
 * @package     yana
 * @subpackage  db
 */
class Form extends \Yana\Db\Ddl\AbstractNamedObject implements \Yana\Db\Ddl\IsIncludableDDL
{
    /**#@+
     * @ignore
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "form";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'     => array('name',     'nmtoken'),
        'table'    => array('table',    'nmtoken'),
        'template' => array('template', 'nmtoken'),
        'key'      => array('key',      'nmtoken'),
        'title'    => array('title',    'string'),
        'allinput' => array('allinput', 'bool')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string'),
        'grant'       => array('grants',      'array', 'Yana\Db\Ddl\Grant'),
        'form'        => array('forms',       'array', 'Yana\Db\Ddl\Form'),
        'input'       => array('fields',      'array', 'Yana\Db\Ddl\Field'),
        'event'       => array('events',      'array', 'Yana\Db\Ddl\Event')
    );

    /**
     * @var string        
     */
    protected $description = null;

    /**
     * @var string        
     */
    protected $title = null;

    /**
     * @var string        
     */
    protected $table = null;

    /**
     * @var string        
     */
    protected $template = null;

    /**
     * @var string        
     */
    protected $key = null;

    /**
     * @var \Yana\Db\Ddl\Event[]    
     */
    protected $events = array();

    /**
     * @var \Yana\Db\Ddl\Grant[]    
     */
    protected $grants = array();

    /**
     * @var \Yana\Db\Ddl\Form[]     
     */
    protected $forms = array();

    /**
     * @var \Yana\Db\Ddl\Field[]    
     */
    protected $fields = array();

    /**
     * @var \Yana\Db\Ddl\Database   
     */
    protected $parent = null;

    /**
     * @var bool          
     */
    protected $isInitialized = false;

    /**
     * @var bool          
     */
    protected $allinput = null;

    /**#@-*/

    /**
     * cached value
     *
     * @access  private
     * @var     bool
     * @ignore
     */
    private $_selectable = null;

    /**
     * cached value
     *
     * @access  private
     * @var     bool
     * @ignore
     */
    private $_insertable = null;

    /**
     * cached value
     *
     * @access  private
     * @var     bool
     * @ignore
     */
    private $_updatable = null;

    /**
     * cached value
     *
     * @access  private
     * @var     bool
     * @ignore
     */
    private $_deletable = null;

    /**
     * cached value
     *
     * @access  private
     * @var     bool
     * @ignore
     */
    private $_grantable = null;

    /**
     * Initialize instance.
     *
     * @access  public
     * @param   string  $name    form name
     * @param   \Yana\Db\Ddl\DDL     $parent  parent form or parent database
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given parent is not valid
     */
    public function __construct($name, \Yana\Db\Ddl\DDL $parent = null)
    {
        parent::__construct($name);
        if (is_null($parent) || $parent instanceof \Yana\Db\Ddl\Database || $parent instanceof \Yana\Db\Ddl\Form) {
            $this->parent = $parent;
        } else {
            $message = "Wrong argument type argument 1. \Yana\Db\Ddl\Database or \Yana\Db\Ddl\Form expected";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_ERROR);
        }
    }

    /**
     * Get parent object.
     *
     * The result is wether an instance of {@see \Yana\Db\Ddl\Database} or {@see \Yana\Db\Ddl\Form}.
     * This depends on wether this is a sub-form of a parent-form, or not.
     * The result may also be null, if there is no parent at all.
     *
     * Thus you should check the result object by using instanceof.
     *
     * @access  public
     * @return  \Yana\Db\Ddl\DDL
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get database.
     *
     * @access  public
     * @return  \Yana\Db\Ddl\Database
     */
    public function getDatabase()
    {
        if ($this->parent instanceof \Yana\Db\Ddl\Form) {
            return $this->parent->getDatabase();
        } else {
            return $this->parent;
        }
    }

    /**
     * Get the database name associated with a table.
     *
     * Returns NULL if the schema name is unknown or an empty string if the schema name is
     * undefined.
     *
     * @access  public
     * @return  string
     * @ignore
     */
    public function getSchemaName()
    {
        $database = $this->getDatabase();
        if ($database instanceof \Yana\Db\Ddl\Database) {
            return $database->getName();
        } else {
            assert('is_null($database)', ' Expecting return value to be instance of \Yana\Db\Ddl\Database or NULL.');
            return null;
        }
    }

    /**
     * Get table name.
     *
     * Returns the name of the source table or view.
     * If none has been defined the function returns NULL instead.
     *
     * @access  public
     * @return  string
     */
    public function getTable()
    {
        if (is_string($this->table) && !empty($this->table)) {
            return $this->table;
        } else {
            return null;
        }
    }

    /**
     * Set table name.
     *
     * Sets the name of the source table or view.
     * Note that for views the view should be updatable or otherwise you won't be able to use an
     * edit form.
     *
     * This setting is mandatory.
     *
     * @access  public
     * @param   string  $table  table name
     * @return  \Yana\Db\Ddl\Form
     */
    public function setTable($table)
    {
        assert('is_string($table)', ' Wrong type for argument 1. String expected');
        $this->table = "$table";
        return $this;
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
     * @return  \Yana\Db\Ddl\Form
     */
    public function setTitle($title = "")
    {
        assert('is_string($title)', ' Wrong type for argument 1. String expected');
        if (empty($title)) {
            $this->title = null;
        } else {
            $this->title = "$title";
        }
        return $this;
    }

    /**
     * Get the user description.
     *
     * The description serves two purposes:
     * 1st is offline-documentation 2nd is online-documentation.
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
     * 1st is offline-documentation 2nd is online-documentation.
     *
     * Note that the description may also contain an identifier for automatic
     * translation.
     *
     * To reset the property, leave the parameter $description empty.
     *
     * @access  public
     * @param   string  $description  new value of this property
     * @return  \Yana\Db\Ddl\Form
     */
    public function setDescription($description)
    {
        assert('is_string($description)', ' Wrong type for argument 1. String expected');
        if (empty($description)) {
            $this->description = null;
        } else {
            $this->description = "$description";
        }
        return $this;
    }

    /**
     * Get form template.
     *
     * The template may be any value supported by a form-generator class.
     * It informs the generator how present the contents of the form.
     *
     * @access  public
     * @return  string
     */
    public function getTemplate()
    {
        if (is_string($this->template)) {
            return $this->template;
        } else {
            return null;
        }
    }

    /**
     * Set form template.
     *
     * The template may be any value supported by a form-generator class.
     * It informs the generator how present the contents of the form.
     *
     * @access  public
     * @param   string  $template  name or id of template to use
     * @return  \Yana\Db\Ddl\Form
     */
    public function setTemplate($template)
    {
        assert('is_string($template)', ' Wrong type for argument 1. String expected');
        if (empty($template)) {
            $this->template = null;
        } else {
            $this->template = "$template";
        }
        return $this;
    }

    /**
     * Get foreign key.
     *
     * If the form is associated with the parent form via a foreign key,
     * this function will return it. If there is none, it will return NULL instead.
     *
     * @access  public
     * @return  string
     */
    public function getKey()
    {
        if (is_string($this->key)) {
            return $this->key;
        } else {
            return null;
        }
    }

    /**
     * Set form template.
     *
     * The form may be associated with the parent form via a foreign key, if the
     * form and the parent form inherit from different base tables.
     * If so, a foreign key must exist linking both tables.
     *
     * @access  public
     * @param   string  $key  name of foreign key column
     * @return  \Yana\Db\Ddl\Form
     */
    public function setKey($key)
    {
        assert('is_string($key)', ' Wrong type for argument 1. String expected');
        if (empty($key)) {
            $this->key = null;
        } else {
            $this->key = "$key";
        }
        return $this;
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
        assert('is_array($this->grants)', ' Member "grants" is expected to be an array.');
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
     * @param   \Yana\Db\Ddl\Grant  $grant  grant object expected (rights managment)
     * @return  \Yana\Db\Ddl\Form
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
     * This function adds a new grant to the form settings by using the given
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
        assert('is_null($user) || is_string($user)', ' Wrong type for argument 1. String expected');
        assert('is_null($role) || is_string($role)', ' Wrong type for argument 2. String expected');
        assert('is_null($level) || is_int($level)', ' Wrong type for argument 3. Integer expected');
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
     * Check if sub-form exists.
     *
     * Returns bool(true) if a form with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $name   new value of this property
     * @return  bool
     */
    public function isForm($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        return isset($this->forms[$name]);
    }

    /**
     * Get form by name.
     *
     * Returns the \Yana\Db\Ddl\Form sub-form with the given name from the current form.
     * If no such item can be found, an exception will be thrown.
     *
     * @access  public
     * @param   string  $name  form name
     * @return  \Yana\Db\Ddl\Form
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when form does not exist
     */
    public function getForm($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->forms[$name])) {
            return $this->forms[$name];
        } else {
            $message = "No such sub-form '$name' in form '{$this->getName()}'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
    }

    /**
     * Add sub-form by name.
     *
     * Adds a form element by the given name and returns it.
     *
     * @access  public
     * @param   string  $name  form name
     * @return  \Yana\Db\Ddl\Form
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException  when a sub-form with the same name already exists
     */
    public function addForm($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->forms[$name])) {
            $message = "Another form with the name '$name' already exists in form '{$this->getName()}'.";
            $level = E_USER_WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($name);
            throw $exception;
        }
        $this->forms[$name] = new self($name, $this);
        return $this->forms[$name];
    }

    /**
     * Returns an array of sub-forms as \Yana\Db\Ddl\Form elements.
     *
     * @access  public
     * @return  array
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * Get list of form names as an array of strings.
     *
     * @access  public
     * @return  array
     */
    public function getFormNames()
    {
        return array_keys($this->forms);
    }

    /**
     * Drop the sub-form with the specified name.
     *
     * @access  public
     * @param   string  $name  form name
     */
    public function dropForm($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->forms[$name])) {
            unset($this->forms[$name]);
        }
    }

    /**
     * Check if field exists.
     *
     * Returns bool(true) if a field with the given name is already defined.
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @param   string  $name  name of a field
     * @return  bool
     */
    public function isField($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        return isset($this->fields[$name]);
    }

    /**
     * Get field by name.
     *
     * Returns the \Yana\Db\Ddl\Field item with the given name from the current view.
     * If no such item can be found, an exception will be thrown.
     *
     * @access  public
     * @param   string  $name   name of a field
     * @return  \Yana\Db\Ddl\Field
     * @throws  \Yana\Core\Exceptions\NotFoundException when field does not exist.
     */
    public function getField($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        } else {
            $message = "No such field '$name' in form '{$this->getName()}'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, E_USER_WARNING);
        }
    }

    /**
     * Get list of fields.
     *
     * Returns an associative array of all \Yana\Db\Ddl\Field items in this form.
     * The keys are the unique names of the fields.
     * If no field has been defined, the returned array will be empty.
     *
     * @access  public
     * @return  array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Add field by name.
     *
     * Adds a field element by the given name and returns it.
     * Throws an exception if a field with the given name already exists.
     *
     * @access  public
     * @param   string  $name  name of a new field
     * @return  \Yana\Db\Ddl\Field
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException  when a field with the same name already exists
     */
    public function addField($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        if (isset($this->fields[$name])) {
            $message = "Another field with the name '$name' already exists in form '" . $this->getName() . "'.";
            $level = E_USER_WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($name);
            throw $exception;
        }
        // add element to list of defined fields
        $this->fields[$name] = new \Yana\Db\Ddl\Field($name);
        return $this->fields[$name];
    }

    /**
     * Removes a field element by the given name.
     *
     * Throws an exception if a field with the given name does not exist.
     *
     * @access  public
     * @param   string  $name  name of a field which would be droped
     * @return  \Yana\Db\Ddl\Field
     * @throws  \Yana\Core\Exceptions\NotFoundException  when field does not exist
     */
    public function dropField($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (!isset($this->fields[$name])) {
            $message = "No such field '$name' in form '" . $this->getName() . "'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, E_USER_WARNING);
        }
        $this->fields[$name] = null;
    }

    /**
     * Get list of events.
     *
     * Events are fired when the user clicks a certain button in the form. The types of supported
     * events depend on the UI-implementation and the form template.
     * See the manual for more details.
     *
     * Returns an array of {@see \Yana\Db\Ddl\Event} instances.
     *
     * @access  public
     * @return  array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Get event by name.
     *
     * Events are fired when the user clicks a certain button in the form. The types of supported
     * depend on the UI-implementation and the form template.
     * See the manual for more details.
     *
     * @access  public
     * @param   string  $name  event name
     * @return  \Yana\Db\Ddl\Event
     */
    public function getEvent($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        if (isset( $this->events[$name])) {
            return $this->events[$name];
        } else {
            return null;
        }
    }

    /**
     * Add event.
     *
     * Adds a new event item and returns the definition as an instance of {@see \Yana\Db\Ddl\Event}.
     *
     * If another event with the same name already exists, it throws an AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and '_'.
     *
     * @access  public
     * @param   string  $name   event name
     * @return  \Yana\Db\Ddl\Event
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    when an event with the same name already exists
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  on invalid name
     */
    public function addEvent($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->events[$name])) {
            $message = "Another action with the name '$name' is already defined.";
            $level = E_USER_WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($name);
            throw $exception;

        } else {
            $this->events[$name] = new \Yana\Db\Ddl\Event($name);
            return $this->events[$name];
        }
    }

    /**
     * Drops the event with the specified name.
     *
     * Returns bool(true) on success and bool(false) if there is no such event to drop.
     *
     * @access  public
     * @param   string  $name  event name
     * @return bool
     */
    public function dropEvent($name)
    {
        assert('is_string($name)', ' Invalid argument $name: string expected');
        if (isset($this->events["$name"])) {
            unset($this->events["$name"]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if form is selectable.
     *
     * Returns bool(true) if form is selectable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isSelectable()
    {
        if (!isset($this->_selectable)) {
            if (\Yana\Db\Ddl\Grant::checkPermissions($this->getGrants(), true)) {
                $this->_selectable = true;
            } else {
                $this->_selectable = false;
            }
        }
        return $this->_selectable;
    }

    /**
     * Check if form is insertable.
     *
     * Returns bool(true) if form is insertable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isInsertable()
    {
        if (!isset($this->_insertable)) {
            if (\Yana\Db\Ddl\Grant::checkPermissions($this->getGrants(), false, true)) {
                $this->_insertable = true;
            } else {
                $this->_insertable = false;
            }
        }
        return $this->_insertable;
    }

    /**
     * Check if form is updatable.
     *
     * Returns bool(true) if form is updatable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isUpdatable()
    {
        if (!isset($this->_updatable)) {
            if (\Yana\Db\Ddl\Grant::checkPermissions($this->getGrants(), false, false, true)) {
                $this->_updatable = true;
            } else {
                $this->_updatable = false;
            }
        }
        return $this->_updatable;
    }

    /**
     * Has all input.
     *
     * Returns bool(true) if the form is supposed to include all columns from the source table,
     * or bool(false), if it should just take the named fields.
     *
     * The attribute is meant to trigger wether or not the form is to automatically include any
     * unmentioned columns of the base table, using default values, unless it is explicitely marked
     * as hidden (blacklist).
     *
     * This is the opposite of the default approach, where only the explicitely defined input fields
     * are used and any unmentioned column is ignored (whitelist).
     *
     * @access  public
     * @return  bool
     */
    public function hasAllInput()
    {
        return !empty($this->allinput);
    }

    /**
     * Set wether form should include all input fields.
     *
     * This function sets the attribute "allinput" of the form.
     *
     * Set to true if the form is supposed to include all columns from the source table,
     * or to false, if it should just take the named fields.
     *
     * This setting has no effect if there are no input fields in the form.
     *
     * The attribute is meant to trigger wether or not the form is to automatically include any
     * unmentioned columns of the base table, using default values, unless it is explicitely marked
     * as hidden (blacklist).
     *
     * This is the opposite of the default approach, where only the explicitely defined input fields
     * are used and any unmentioned column is ignored (whitelist).
     *
     * @access  public
     * @param   bool  $allinput  use all table columns (true = yes, false = no)
     * @return  \Yana\Db\Ddl\Form
     */
    public function setAllInput($allinput)
    {
        assert('is_bool($allinput)', ' Wrong argument type argument 1. Boolean expected');
        $this->allinput = !empty($allinput);
        return $this;
    }

    /**
     * Check if form is deletable.
     *
     * Returns bool(true) if form is deletable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isDeletable()
    {
        if (!isset($this->_deletable)) {
            if (\Yana\Db\Ddl\Grant::checkPermissions($this->getGrants(), false, false, false, true)) {
                $this->_deletable = true;
            } else {
                $this->_deletable = false;
            }
        }
        return $this->_deletable;
    }

    /**
     * Check if form is grantable.
     *
     * Returns bool(true) if form is grantable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isGrantable()
    {
        if (!isset($this->_grantable)) {
            if (\Yana\Db\Ddl\Grant::checkPermissions($this->getGrants(), false, false, false, false, true)) {
                $this->_grantable = true;
            } else {
                $this->_grantable = false;
            }
        }
        return $this->_grantable;
    }

    /**
     * <<magic>> Returns a sub-form or field, with the given attribute name.
     *
     * @access  public
     * @param   string  $name  sub-form or field name
     * @return  \Yana\Db\Ddl\Field
     */
    public function __get($name)
    {
        switch (true)
        {
            case isset($this->forms[$name]):
                return $this->forms[$name];
            case isset($this->fields[$name]):
                return $this->fields[$name];
            default:
                return parent::__get($name);
        }
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
     * @return  \Yana\Db\Ddl\Form
     * @throws   \Yana\Core\Exceptions\InvalidArgumentException  when the name or table attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
        }
        if (!isset($attributes['table'])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Missing table attribute.", E_USER_WARNING);
        }
        $name = (string) $attributes['name'];
        $table = (string) $attributes['table'];
        $template = 0;
        if (isset($attributes['template'])) {
            $template = $attributes['template'];
        }
        // create instance depending on chosen template
        $ddl = new self($name, $parent);
        $ddl->_unserializeFromXDDL($node);
        if ($ddl->hasAllInput()) {
            $ddl->setAllInput(true); // this initializes the instance
        }
        $ddl->isInitialized = true;
        return $ddl;
    }

}

?>