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
 * database form structure
 *
 * This wrapper class represents the structure of a database
 *
 * Forms are named objects, that are used as input for a form or GUI generator. They must have at
 * least a base table and a template.
 * The base table may of course also be an (updatable) view.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLForm extends DDLNamedObject implements IsIncludableDDL
{
    /**#@+
     * @ignore
     * @access  protected
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
        'grant'       => array('grants',      'array', 'DDLGrant'),
        'form'        => array('forms',       'array', 'DDLForm'),
        'input'       => array('fields',      'array', 'DDLField'),
        'event'       => array('events',      'array', 'DDLEvent')
    );

    /** @var string        */ protected $description = null;
    /** @var string        */ protected $title = null;
    /** @var string        */ protected $table = null;
    /** @var string        */ protected $template = null;
    /** @var string        */ protected $key = null;
    /** @var DDLEvent[]    */ protected $events = array();
    /** @var DDLGrant[]    */ protected $grants = array();
    /** @var DDLForm[]     */ protected $forms = array();
    /** @var DDLField[]    */ protected $fields = array();
    /** @var DDLDatabase   */ protected $parent = null;
    /** @var bool          */ protected $isInitialized = false;
    /** @var bool          */ protected $allinput = null;

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
     * constructor
     *
     * @access  public
     * @param   string  $name    form name
     * @param   DDL     $parent  parent form or parent database
     * @throws  InvalidArgumentException
     */
    public function __construct($name, DDL $parent = null)
    {
        parent::__construct($name);
        if (is_null($parent) || $parent instanceof DDLDatabase || $parent instanceof DDLForm) {
            $this->parent = $parent;
        } else {
            $message = "Wrong argument type argument 1. DDLDatabase or DDLForm expected";
            throw new InvalidArgumentException($message, E_USER_ERROR);
        }
    }

    /**
     * get parent
     *
     * The result is wether an instance of {@see DDLDatabase} or {@see DDLForm}.
     * This depends on wether this is a sub-form of a parent-form, or not.
     * The result may also be null, if there is no parent at all.
     *
     * Thus you should check the result object by using instanceof.
     *
     * @return  DDL
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * get database
     *
     * @return  DDLDatabase
     */
    public function getDatabase()
    {
        if ($this->parent instanceof DDLForm) {
            return $this->parent->getDatabase();
        } else {
            return $this->parent;
        }
    }

    /**
     * get the database name associated with a table
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
        if ($database instanceof DDLDatabase) {
            return $database->getName();
        } else {
            assert('is_null($database); // Expecting return value to be instance of DDLDatabase or NULL.');
            return null;
        }
    }

    /**
     * get table name
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
     * set table name
     *
     * Sets the name of the source table or view.
     * Note that for views the view should be updatable or otherwise you won't be able to use an
     * edit form.
     *
     * This setting is mandatory.
     *
     * @access  public
     * @param   string  $table  table name
     */
    public function setTable($table)
    {
        assert('is_string($table); // Wrong type for argument 1. String expected');
        $this->table = "$table";
    }

    /**
     * get title
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
     * set title
     *
     * Sets the title used to display the object in the UI.
     * To reset the property, leave the parameter empty.
     *
     * @access  public
     * @param   string  $title  title
     */
    public function setTitle($title = "")
    {
        assert('is_string($title); // Wrong type for argument 1. String expected');
        if (empty($title)) {
            $this->title = null;
        } else {
            $this->title = "$title";
        }
    }

    /**
     * get the user description
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
     * set the description property
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
     */
    public function setDescription($description)
    {
        assert('is_string($description); // Wrong type for argument 1. String expected');
        if (empty($description)) {
            $this->description = null;
        } else {
            $this->description = "$description";
        }
    }

    /**
     * get form template
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
     * set form template
     *
     * The template may be any value supported by a form-generator class.
     * It informs the generator how present the contents of the form.
     *
     * @access  public
     * @param   string  $template  name or id of template to use
     */
    public function setTemplate($template)
    {
        assert('is_string($template); // Wrong type for argument 1. String expected');
        if (empty($template)) {
            $this->template = null;
        } else {
            $this->template = "$template";
        }
    }

    /**
     * get foreign key
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
     * set form template
     *
     * The form may be associated with the parent form via a foreign key, if the
     * form and the parent form inherit from different base tables.
     * If so, a foreign key must exist linking both tables.
     *
     * @access  public
     * @param   string  $key  name of foreign key column
     */
    public function setKey($key)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        if (empty($key)) {
            $this->key = null;
        } else {
            $this->key = "$key";
        }
    }

    /**
     * get rights management settings
     *
     * Returns an array of DDLGrant objects.
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
     * drop rights management settings
     *
     * {@link DDLGrant}s control the access permissions granted to the user.
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
     * set rights management setting
     *
     * {@link DDLGrant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the configuration.
     *
     * @access  public
     * @param   DDLGrant  $grant    grant object expected (rights managment)
     */
    public function setGrant(DDLGrant $grant)
    {
        $this->grants[] = $grant;
    }

    /**
     * add rights management setting
     *
     * {@link DDLGrant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the form settings by using the given
     * options and returns it as an DDLGrant object.
     *
     * @access  public
     * @param   string  $user   user group
     * @param   string  $role   user role
     * @param   int     $level  security level
     * @return  DDLGrant
     * @throws  InvalidArgumentException  when $level is out of range [0,100]
     */
    public function addGrant($user = null, $role = null, $level = null)
    {
        assert('is_null($user) || is_string($user); // Wrong type for argument 1. String expected');
        assert('is_null($role) || is_string($role); // Wrong type for argument 2. String expected');
        assert('is_null($level) || is_int($level);  // Wrong type for argument 3. Integer expected');
        $grant = new DDLGrant();
        if (!empty($user)) {
            $grant->setUser($user);
        }
        if (!empty($role)) {
            $grant->setRole($role);
        }
        // may throw an InvalidArgumentException
        if (!is_null($level)) {
            $grant->setLevel($level);
        }
        $this->grants[] = $grant;
        return $grant;
    }

    /**
     * get form by name
     *
     * Returns the DDLForm sub-form with the given name from the current form.
     * If no such item can be found, an exception will be thrown.
     *
     * @access  public
     * @param   string  $name  form name
     * @return  DDLForm
     * @throws  InvalidArgumentException  when form does not exist
     */
    public function getForm($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (isset($this->forms[$name])) {
            return $this->forms[$name];
        } else {
            $message = "No such sub-form '$name' in form '{$this->getName()}'.";
            throw new InvalidArgumentException($message, E_USER_WARNING);
        }
    }

    /**
     * add sub-form by name
     *
     * Adds a form element by the given name and returns it.
     * Throws an exception if a field with the given name already exists.
     *
     * @access  public
     * @param   string  $name       form name
     * @param   string  $className  form class name
     * @return  DDLForm
     * @throws  AlreadyExistsException    when a sub-form with the same name already exists
     * @throws  InvalidArgumentException  if given an invalid name or class
     */
    public function addForm($name, $className = __CLASS__)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (isset($this->forms[$name])) {
            $message = "Another form with the name '$name' already exists in form '{$this->getName()}'.";
            throw new AlreadyExistsException($message, E_USER_WARNING);

        } elseif ($className !== __CLASS__ && !is_subclass_of($className, __CLASS__)) {
            throw new InvalidArgumentException("The class '$className' must be a sub-class of DDLForm.");

        } else {
            $this->forms[$name] = new $className($name, $this);
            return $this->forms[$name];
        }
    }

    /**
     * get list of sub-forms
     *
     * Returns an array of DDLForm elements.
     *
     * @access  public
     * @return  array
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * get list of form names
     *
     * Returns an array of form names as strings.
     *
     * @access  public
     * @return  array
     */
    public function getFormNames()
    {
        return array_keys($this->forms);
    }

    /**
     * drop sub-form
     *
     * Drops the form with the specified name.
     *
     * @access  public
     * @param   string  $name    form name
     *
     */
    public function dropForm($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (isset($this->forms["$name"])) {
            unset($this->forms["$name"]);
        }
    }

    /**
     * check if field exists
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
        assert('is_string($name); // Wrong type for argument 1. String expected');
        return isset($this->fields[$name]);
    }

    /**
     * get field by name
     *
     * Returns the DDLField item with the given name from the current view.
     * If no such item can be found, an exception will be thrown.
     *
     * @access  public
     * @param   string  $name   name of a field
     * @return  DDLField
     * @throws  NotFoundException when field does not exist.
     */
    public function getField($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        } else {
            $message = "No such field '$name' in form '{$this->getName()}'.";
            throw new NotFoundException($message, E_USER_WARNING);
        }
    }

    /**
     * get list of fields
     *
     * Returns an associative array of all DDLField items in this form.
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
     * add field by name
     *
     * Adds a field element by the given name and returns it.
     * Throws an exception if a field with the given name already exists.
     *
     * @access  public
     * @param   string  $name       name of a new field
     * @param   string  $className  field class name
     * @return  DDLField
     * @throws  AlreadyExistsException    when a field with the same name already exists
     * @throws  InvalidArgumentException  if given an invalid name or class
     */
    public function addField($name, $className = 'DDLField')
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (isset($this->fields[$name])) {
            $message = "Another field with the name '$name' already exists in form '{$this->getName()}'.";
            throw new AlreadyExistsException($message, E_USER_WARNING);

        } elseif ($className !== 'DDLField' && !is_subclass_of($className, 'DDLField')) {
            throw new InvalidArgumentException("The class '$className' must be a sub-class of DDLField.");

        } else {
            // add element to list of defined fields
            $this->fields[$name] = new $className($name, $this);
            return $this->fields[$name];
        }
    }

    /**
     * drop field by name
     *
     * Removes a field element by the given.
     * Throws an exception if a field with the given name does not exist.
     *
     * @access  public
     * @param   string  $name   name of a field which would be droped
     * @return  DDLField
     * @throws  NotFoundException    when field does not exist
     */
    public function dropField($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $name = mb_strtolower($name);
        if (isset($this->fields[$name])) {
            $this->fields[$name] = null;
        } else {
            $message = "No such field '$name' in form '{$this->getName()}'.";
            throw new NotFoundException($message, E_USER_WARNING);
        }
    }

    /**
     * get list of events
     *
     * Events are fired when the user clicks a certain button in the form. The types of supported
     * events depend on the UI-implementation and the form template.
     * See the manual for more details.
     *
     * Returns an array of {@see DDLEvent} instances.
     *
     * @access  public
     * @return  array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * get event by name
     *
     * Events are fired when the user clicks a certain button in the form. The types of supported
     * depend on the UI-implementation and the form template.
     * See the manual for more details.
     *
     * @access  public
     * @param   string  $name  event name
     * @return  DDLEvent
     */
    public function getEvent($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (isset( $this->events[$name])) {
            return $this->events[$name];
        } else {
            return null;
        }
    }

    /**
     * add event
     *
     * Adds a new event item and returns the definition as an instance of {@see DDLEvent}.
     *
     * If another event with the same name already exists, it throws an AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and
     * '_'. Otherwise an InvalidArgumentException is thrown.
     *
     * @access  public
     * @param   string  $name   event name
     * @return  DDLEvent
     * @throws  AlreadyExistsException    when an event with the same name already exists
     * @throws  InvalidArgumentException  on invalid name
     */
    public function addEvent($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $name = mb_strtolower($name);
        if (isset($this->events[$name])) {
            throw new AlreadyExistsException("Another action with the name '$name' is already defined.");

        } else {
            $this->events[$name] = new DDLEvent($name);
            return $this->events[$name];
        }
    }

    /**
     * drop event
     *
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
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (isset($this->events["$name"])) {
            unset($this->events["$name"]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * check if form is selectable
     *
     * Returns bool(true) if form is selectable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isSelectable()
    {
        if (!isset($this->_selectable)) {
            if (DDLGrant::checkPermissions($this->getGrants(), true)) {
                $this->_selectable = true;
            } else {
                $this->_selectable = false;
            }
        }
        return $this->_selectable;
    }

    /**
     * check if form is insertable
     *
     * Returns bool(true) if form is insertable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isInsertable()
    {
        if (!isset($this->_insertable)) {
            if (DDLGrant::checkPermissions($this->getGrants(), false, true)) {
                $this->_insertable = true;
            } else {
                $this->_insertable = false;
            }
        }
        return $this->_insertable;
    }

    /**
     * check if form is updatable
     *
     * Returns bool(true) if form is updatable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isUpdatable()
    {
        if (!isset($this->_updatable)) {
            if (DDLGrant::checkPermissions($this->getGrants(), false, false, true)) {
                $this->_updatable = true;
            } else {
                $this->_updatable = false;
            }
        }
        return $this->_updatable;
    }

    /**
     * has all input
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
     * set wether form should include all input fields
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
     */
    public function setAllInput($allinput)
    {
        assert('is_bool($allinput); // Wrong argument type argument 1. Boolean expected');
        $this->allinput = !empty($allinput);
    }

    /**
     * check if form is deletable
     *
     * Returns bool(true) if form is deletable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isDeletable()
    {
        if (!isset($this->_deletable)) {
            if (DDLGrant::checkPermissions($this->getGrants(), false, false, false, true)) {
                $this->_deletable = true;
            } else {
                $this->_deletable = false;
            }
        }
        return $this->_deletable;
    }

    /**
     * check if form is grantable
     *
     * Returns bool(true) if form is grantable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isGrantable()
    {
        if (!isset($this->_grantable)) {
            if (DDLGrant::checkPermissions($this->getGrants(), false, false, false, false, true)) {
                $this->_grantable = true;
            } else {
                $this->_grantable = false;
            }
        }
        return $this->_grantable;
    }

    /**
     * magic get
     *
     * Returns a sub-form or field, with the given attribute name.
     *
     * @access  public
     * @param   string  $name  sub-form or field name
     * @return  DDLField
     */
    public function __get($name)
    {
        switch (true)
        {
            case isset($this->forms[$name]):
                return $this->forms[$name];
            break;
            case isset($this->fields[$name]):
                return $this->fields[$name];
            break;
            default:
                return parent::__get($name);
            break;
        }
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @access  public
     * @static
     * @param   SimpleXMLElement  $node    XML node
     * @param   mixed             $parent  parent node (if any)
     * @return  DDLForm
     */
    public static function unserializeFromXDDL(SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            throw new InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
        }
        if (!isset($attributes['table'])) {
            throw new InvalidArgumentException("Missing table attribute.", E_USER_WARNING);
        }
        $name = (string) $attributes['name'];
        $table = (string) $attributes['table'];
        if (isset($attributes['template'])) {
            $template = $attributes['template'];
        } else {
            $template = 0;
        }
        // create instance depending on chosen template
        $isDefaultForm = ($parent instanceof DDLDefaultForm) || ($parent instanceof DDLDatabase);
        if ($isDefaultForm || is_numeric($template)) {
            // registered templates (with default handler)
            $ddl = new DDLDefaultForm($name, $parent);
            $ddl->setLayout((int) $template);
        } elseif (class_exists($template) && is_subclass_of($template, DDLForm)) {
            // registered templates (with custom handler)
            $ddl = new $attributes['template']($name, $parent);
            if (!($ddl instanceof DDLForm)) {
                $message = "Invalid template attribue. Must be a subclass of DDLForm.";
                throw new InvalidArgumentException($message, E_USER_WARNING);
            }
        } else {
            $ddl = new self($name, $parent);
        }
        $ddl->_unserializeFromXDDL($node);
        if ($ddl->hasAllInput()) {
            $ddl->setAllInput(true); // this initializes the instance
        }
        $ddl->isInitialized = true;
        return $ddl;
    }

}

?>