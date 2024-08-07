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
declare(strict_types=1);

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

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $xddlTag = "form";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlAttributes = array(
        'name'     => array('name',     'nmtoken'),
        'table'    => array('table',    'string'),
        'template' => array('template', 'nmtoken'),
        'key'      => array('key',      'nmtoken'),
        'title'    => array('title',    'string'),
        'allinput' => array('allinput', 'bool')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
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
     * @ignore
     */
    protected $description = null;

    /**
     * @var string
     * @ignore
     */
    protected $title = null;

    /**
     * @var string
     * @ignore
     */
    protected $table = null;

    /**
     * @var string
     * @ignore
     */
    protected $template = null;

    /**
     * @var string
     * @ignore
     */
    protected $key = null;

    /**
     * @var \Yana\Db\Ddl\Event[]
     * @ignore
     */
    protected $events = array();

    /**
     * @var \Yana\Db\Ddl\Grant[]
     * @ignore
     */
    protected $grants = array();

    /**
     * @var \Yana\Db\Ddl\Form[]
     * @ignore
     */
    protected $forms = array();

    /**
     * @var \Yana\Db\Ddl\Field[]
     * @ignore
     */
    protected $fields = array();

    /**
     * @var \Yana\Db\Ddl\Database
     * @ignore
     */
    protected $parent = null;

    /**
     * @var bool
     * @ignore
     */
    protected $isInitialized = false;

    /**
     * @var bool
     * @ignore
     */
    protected $allinput = null;

    /**
     * cached value
     *
     * @var  bool
     */
    private $_selectable = null;

    /**
     * cached value
     *
     * @var  bool
     */
    private $_insertable = null;

    /**
     * cached value
     *
     * @var  bool
     */
    private $_updatable = null;

    /**
     * cached value
     *
     * @var  bool
     */
    private $_deletable = null;

    /**
     * cached value
     *
     * @var  bool
     */
    private $_grantable = null;

    /**
     * Initialize instance.
     *
     * @param   string  $name    form name
     * @param   \Yana\Db\Ddl\DDL     $parent  parent form or parent database
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given parent is not valid
     */
    public function __construct($name, ?\Yana\Db\Ddl\DDL $parent = null)
    {
        parent::__construct($name);
        if (is_null($parent) || $parent instanceof \Yana\Db\Ddl\Database || $parent instanceof \Yana\Db\Ddl\Form) {
            $this->parent = $parent;
        } else {
            $message = "Wrong argument type argument 1. \Yana\Db\Ddl\Database or \Yana\Db\Ddl\Form expected";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::ERROR);
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
     * @return  \Yana\Db\Ddl\DDL
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get database.
     *
     * @return  \Yana\Db\Ddl\Database
     */
    public function getDatabase(): ?\Yana\Db\Ddl\Database
    {
        if ($this->parent instanceof \Yana\Db\Ddl\Form) {
            return $this->parent->getDatabase();
        } else {
            return $this->getParent();
        }
    }

    /**
     * Get the database name associated with a table.
     *
     * Returns NULL if the schema name is unknown or an empty string if the schema name is
     * undefined.
     *
     * @return  string|null
     * @ignore
     */
    public function getSchemaName(): ?string
    {
        $database = $this->getDatabase();
        if ($database instanceof \Yana\Db\Ddl\Database) {
            return $database->getName();
        } else {
            assert(is_null($database), 'Expecting return value to be instance of \Yana\Db\Ddl\Database or NULL.');
            return null;
        }
    }

    /**
     * Get table name.
     *
     * Returns the name of the source table or view.
     * If none has been defined the function returns NULL instead.
     *
     * @return  string|null
     */
    public function getTable(): ?string
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
     * @param   string  $table  table name
     * @return  $this
     */
    public function setTable(string $table)
    {
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
     * @return  string|null
     */
    public function getTitle(): ?string
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
     * @param   string  $title  some text
     * @return  $this
     */
    public function setTitle(string $title = "")
    {
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
     * @return  string|null
     */
    public function getDescription(): ?string
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
     * @param   string  $description  new value of this property
     * @return  $this
     */
    public function setDescription(string $description)
    {
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
     * @return  string
     */
    public function getTemplate(): ?string
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
     * @param   string  $template  name or id of template to use
     * @return  $this
     */
    public function setTemplate(string $template)
    {
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
     * @return  string
     */
    public function getKey(): ?string
    {
        if (is_string($this->key)) {
            return $this->key;
        } else {
            return null;
        }
    }

    /**
     * Set name of foreign key.
     *
     * The form may be associated with the parent form via a foreign key, if the
     * form and the parent form inherit from different base tables.
     * If so, a foreign key must exist linking both tables.
     *
     * @param   string  $key  name of foreign key column (case-sensitive)
     * @return  \Yana\Db\Ddl\Form
     */
    public function setKey(string $key)
    {
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
     * @return  \Yana\Db\Ddl\Grant[]
     */
    public function getGrants(): array
    {
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
     */
    public function dropGrants()
    {
        $this->grants = array();
        $this->_resetGrantCache();
    }

    /**
     * Add rights management object.
     *
     * {@link \Yana\Db\Ddl\Grant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the configuration.
     *
     * @param   \Yana\Db\Ddl\Grant  $grant  grant object expected (rights managment)
     * @return  $this
     */
    public function addGrantObject(\Yana\Db\Ddl\Grant $grant)
    {
        $this->grants[] = $grant;
        $this->_resetGrantCache();
        return $this;
    }

    /**
     * Reset all attributes for caching the permission status of the object.
     */
    private function _resetGrantCache()
    {
        $this->_deletable = null;
        $this->_grantable = null;
        $this->_insertable = null;
        $this->_selectable = null;
        $this->_updatable = null;
    }

    /**
     * Create and add rights management setting.
     *
     * {@link \Yana\Db\Ddl\Grant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the form settings by using the given
     * options and returns it as an \Yana\Db\Ddl\Grant object.
     *
     * @param   string  $user   user group
     * @param   string  $role   user role
     * @param   int     $level  security level
     * @return  \Yana\Db\Ddl\Grant
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when $level is out of range [0,100]
     */
    public function addGrant(?string $user = null, ?string $role = null, ?int $level = null): \Yana\Db\Ddl\Grant
    {
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
        $this->addGrantObject($grant);
        return $grant;
    }

    /**
     * Check if sub-form exists.
     *
     * Returns bool(true) if a form with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @param   string  $name   new value of this property
     * @return  bool
     */
    public function isForm(string $name): bool
    {
        $name = mb_strtolower($name);
        return isset($this->forms[$name]);
    }

    /**
     * Get form by name.
     *
     * Returns the \Yana\Db\Ddl\Form sub-form with the given name from the current form.
     * If no such item can be found, an exception will be thrown.
     *
     * @param   string  $name  form name
     * @return  \Yana\Db\Ddl\Form
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when form does not exist
     */
    public function getForm(string $name): \Yana\Db\Ddl\Form
    {
        $name = mb_strtolower($name);
        if (isset($this->forms[$name])) {
            return $this->forms[$name];
        } else {
            $message = "No such sub-form '$name' in form '{$this->getName()}'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
    }

    /**
     * Add sub-form by name.
     *
     * Adds a form element by the given name and returns it.
     *
     * @param   string  $name  form name
     * @return  \Yana\Db\Ddl\Form
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException  when a sub-form with the same name already exists
     */
    public function addForm(string $name): \Yana\Db\Ddl\Form
    {
        $name = mb_strtolower($name);
        if (isset($this->forms[$name])) {
            $message = "Another form with the name '$name' already exists in form '{$this->getName()}'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
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
     * @return  array
     */
    public function getForms(): array
    {
        return $this->forms;
    }

    /**
     * Get list of form names as an array of strings.
     *
     * @return  array
     */
    public function getFormNames(): array
    {
        return array_keys($this->forms);
    }

    /**
     * Drop the sub-form with the specified name.
     *
     * @param   string  $name  form name
     */
    public function dropForm(string $name)
    {
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
     * @param   string  $name  name of a field
     * @return  bool
     */
    public function isField(string $name): bool
    {
        return isset($this->fields[mb_strtolower($name)]);
    }

    /**
     * Get field by name.
     *
     * Returns the \Yana\Db\Ddl\Field item with the given name from the current view.
     * If no such item can be found, an exception will be thrown.
     *
     * @param   string  $name   name of a field
     * @return  \Yana\Db\Ddl\Field
     * @throws  \Yana\Core\Exceptions\NotFoundException when field does not exist.
     */
    public function getField(string $name): \Yana\Db\Ddl\Field
    {
        $name = mb_strtolower($name);
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        } else {
            $message = "No such field '$name' in form '{$this->getName()}'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
    }

    /**
     * Get list of fields.
     *
     * Returns an associative array of all \Yana\Db\Ddl\Field items in this form.
     * The keys are the unique names of the fields.
     * If no field has been defined, the returned array will be empty.
     *
     * @return  array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get list of field names.
     *
     * Returns the unique names of the fields.
     * If no field has been defined, the returned array will be empty.
     *
     * @return  array
     */
    public function getFieldNames(): array
    {
        return \array_keys($this->fields);
    }

    /**
     * Add field by name.
     *
     * Adds a field element by the given name and returns it.
     * Throws an exception if a field with the given name already exists.
     *
     * @param   string  $name  name of a new field
     * @return  \Yana\Db\Ddl\Field
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException  when a field with the same name already exists
     */
    public function addField(string $name): \Yana\Db\Ddl\Field
    {
        $name = mb_strtolower($name);
        if (isset($this->fields[$name])) {
            $message = "Another field with the name '$name' already exists in form '" . $this->getName() . "'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
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
     * @param   string  $name  name of a field which would be droped
     * @throws  \Yana\Core\Exceptions\NotFoundException  when field does not exist
     */
    public function dropField(string $name)
    {
        $name = mb_strtolower($name);
        if (!isset($this->fields[$name])) {
            $message = "No such field '$name' in form '" . $this->getName() . "'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
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
     * @return  array
     */
    public function getEvents(): array
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
     * @param   string  $name  event name
     * @return  \Yana\Db\Ddl\Event|null
     */
    public function getEvent(string $name): ?\Yana\Db\Ddl\Event
    {
        $name = mb_strtolower($name);
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
     * @param   string  $name   event name
     * @return  \Yana\Db\Ddl\Event
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    when an event with the same name already exists
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  on invalid name
     */
    public function addEvent(string $name): \Yana\Db\Ddl\Event
    {
        $name = mb_strtolower($name);
        if (isset($this->events[$name])) {
            $message = "Another action with the name '$name' is already defined.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
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
     * @param   string  $name  event name
     * @return bool
     */
    public function dropEvent(string $name): bool
    {
        $name = mb_strtolower($name);
        if (isset($this->events[$name])) {
            unset($this->events[$name]);
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
     * @return  bool
     */
    public function isSelectable(): bool
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
     * @return  bool
     */
    public function isInsertable(): bool
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
     * @return  bool
     */
    public function isUpdatable(): bool
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
     * @return  bool
     */
    public function hasAllInput(): bool
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
     * @param   bool  $allinput  use all table columns (true = yes, false = no)
     * @return  $this
     */
    public function setAllInput(bool $allinput)
    {
        assert(is_bool($allinput), 'Wrong argument type argument 1. Boolean expected');
        $this->allinput = !empty($allinput);
        return $this;
    }

    /**
     * Check if form is deletable.
     *
     * Returns bool(true) if form is deletable to the current user and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isDeletable(): bool
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
     * @return  bool
     */
    public function isGrantable(): bool
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
     * @param   string  $name  sub-form or field name
     * @return  \Yana\Db\Ddl\Field
     */
    public function __get($name)
    {
        switch (true)
        {
            case $this->isForm($name):
                return $this->getForm($name);
            case $this->isField($name):
                return $this->getField($name);
            default:
                return parent::__get($name);
        }
    }

    /**
     * Unserialize a XDDL-node to an object.
     *
     * Returns the unserialized object.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  self
     * @throws   \Yana\Core\Exceptions\InvalidArgumentException  when the name or table attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null): self
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            $message = "Missing name attribute.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        if (!isset($attributes['table'])) {
            $message = "Missing table attribute.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $name = (string) $attributes['name'];
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
