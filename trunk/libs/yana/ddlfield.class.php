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
 * database form field structure
 *
 * A field represents an UI input-element inside a form.
 * Each field has a source column and several attributes to change it's representation.
 *
 * The type of the input-element depends on the type of the source column.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLField extends DDLNamedObject
{
    /**#@+
     * @access  protected
     * @ignore
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "input";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'     => array('name', 'nmtoken'),
        'title'    => array('title', 'string'),
        'hidden'   => array('hidden', 'bool'),
        'readonly' => array('readonly', 'bool'),
        'cssclass' => array('cssClass', 'string'),
        'tabindex' => array('tabIndex', 'int')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description'   => array('description', 'string'),
        'grant'         => array('grants',      'array', 'DDLGrant'),
        'column'        => array('column',      'DDLColumn'),
        'event'         => array('events',      'array', 'DDLEvent')
    );

    /** @var string      */ protected $description = null;
    /** @var string      */ protected $title = null;
    /** @var bool        */ protected $hidden = null;
    /** @var bool        */ protected $readonly = null;
    /** @var string      */ protected $cssClass = null;
    /** @var int         */ protected $tabIndex = null;
    /** @var DDLGrant[]  */ protected $grants = array();
    /** @var DDLEvent[]  */ protected $events = array();
    /** @var DDLForm     */ protected $parent = null;
    /** @var DDLColumn   */ protected $column = null;

    /**#@-*/

    /**#@+
     * cached value
     *
     * @access  protected
     * @var     bool
     * @ignore
     */

    protected $isSelectable = null;
    protected $isInsertable = null;
    protected $isUpdatable = null;
    protected $isDeletable = null;
    protected $isGrantable = null;

    /**#@-*/

    /**
     * constructor
     *
     * @param  string   $name    form field name
     * @param  DDLForm  $parent  parent database
     */
    public function __construct($name, DDLForm $parent = null)
    {
        parent::__construct($name);
        $this->parent = $parent;
    }

    /**
     * get parent
     *
     * @return  DDLForm
     */
    public function getParent()
    {
        return $this->parent;
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
        } elseif ($this->column instanceof DDLColumn) {
            return $this->column->getDescription();
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
    public function setDescription($description = "")
    {
        assert('is_string($description); // Wrong type for argument 1. String expected');
        if ($this->column instanceof DDLColumn) {
            $this->column->setDescription($description);
        } elseif (empty($description)) {
            $this->description = null;
        } else {
            $this->description = "$description";
        }
    }

    /**
     * get title
     *
     * A text that may be used in the UI as a label for the control element associated with the
     * field.
     * Note that this may also be a language token, which is to be translated to the selected
     * language.
     *
     * Returns the label as a string or NULL, if it is undefined. If no label is defined, it should
     * default to the name of the field.
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
     * A text that may be used in the UI as a label for the control element associated with the
     * field.
     * Note that this may also be a language token, which is to be translated to the selected
     * language.
     *
     * To reset this property, leave the parameter empty.
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
     * get list of events
     *
     * Events are Javascript functions that are fired when the user clicks the field.
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
     * Actions are (Javascript) functions that are fired when the user clicks the field.
     * See the manual for more details.
     *
     * @access  public
     * @param   string  $name    event name
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
     * @return  bool
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
     * check whether the column should be visible
     *
     * A field may be hidden, in which case it is not to be shown in the UI.
     * Note that an element may also not be visible due to the fact, that the user has no permission
     * to see it, because he is not granted to do so.
     *
     * @access  public
     * @return  bool
     */
    public function isVisible()
    {
        if (empty($this->hidden)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * select whether the column should be visible
     *
     * A field may be hidden, in which case it is not to be shown in the UI.
     * Set this to bool(false) to hide the field or bool(true) to make it visible.
     *
     * @access  public
     * @param   bool  $isVisible  new value of this property
     */
    public function setVisible($isVisible)
    {
        assert('is_bool($isVisible); // Wrong type for argument 1. Boolean expected');
        if ($isVisible) {
            $this->hidden = false;
        } else {
            $this->hidden = true;
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
        if (!isset($this->isSelectable)) {
            if (DDLGrant::checkPermissions($this->getGrants(), true)) {
                $this->isSelectable = true;
            } else {
                $this->isSelectable = false;
            }
        }
        return $this->isSelectable;
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
        if (!isset($this->isInsertable)) {
            if (DDLGrant::checkPermissions($this->getGrants(), false, true)) {
                $this->isInsertable = true;
            } else {
                $this->isInsertable = false;
            }
        }
        return $this->isInsertable;
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
        if (!isset($this->isUpdatable)) {
            if (DDLGrant::checkPermissions($this->getGrants(), false, false, true)) {
                $this->isUpdatable = true;
            } else {
                $this->isUpdatable = false;
            }
        }
        return $this->isUpdatable;
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
        if (!isset($this->isDeletable)) {
            if (DDLGrant::checkPermissions($this->getGrants(), false, false, false, true)) {
                $this->isDeletable = true;
            } else {
                $this->isDeletable = false;
            }
        }
        return $this->isDeletable;
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
        if (!isset($this->isGrantable)) {
            if (DDLGrant::checkPermissions($this->getGrants(), false, false, false, false, true)) {
                $this->isGrantable = true;
            } else {
                $this->isGrantable = false;
            }
        }
        return $this->isGrantable;
    }

    /**
     * check whether the dbo has read-only access
     *
     * Returns bool(true) if the field is read-only and bool(false)
     * otherwise.
     *
     * The default is bool(false).
     *
     * @access  public
     * @return  bool
     */
    public function isReadonly()
    {
        if (empty($this->readonly)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * set read-only access
     *
     * You may set the field to be read-only to prevent any changes to it by setting this to
     * bool(true).
     *
     * @access  public
     * @param   bool  $isReadonly   new value of this property
     */
    public function setReadonly($isReadonly)
    {
        assert('is_bool($isReadonly); // Wrong type for argument 1. Boolean expected');
        if ($isReadonly) {
            $this->readonly = true;
        } else {
            $this->readonly = false;
        }
    }

    /**
     * get CSS class attribute
     *
     * Returns the prefered CSS-class for this field as a string or NULL if there is none.
     *
     * @access  public
     * @return  string
     */
    public function getCssClass()
    {
        if (is_string($this->cssClass)) {
            return $this->cssClass;
        } else {
            return null;
        }
    }

    /**
     * set CSS class attribute
     *
     * The UI usually should provide id-attributes and CSS-classes for fields automatically.
     * However: you may add your own CSS-class here.
     *
     * To reset the property, leave the parameter empty.
     *
     * @access  public
     * @param   string  $class  name of a css class
     */
    public function setCssClass($class = "")
    {
        assert('is_string($class); // Wrong type for argument 1. String expected');
        if (empty($class)) {
            $this->cssClass = null;
        } else {
            $this->cssClass = "$class";
        }
    }

    /**
     * get tab-index attribute
     *
     * Returns the prefered tab-index for this field as an integer or NULL if there is none.
     *
     * @access  public
     * @return  int
     */
    public function getTabIndex()
    {
        if (is_int($this->tabIndex)) {
            return $this->tabIndex;
        } else {
            return null;
        }
    }

    /**
     * set tab-index attribute
     *
     * The tab-index is usually to be generated autoamtically by the UI. But you may overwrite this
     * setting.
     *
     * To reset the property, leave the parameter empty.
     *
     * @access  public
     * @param   int  $index   tab-index
     */
    public function setTabIndex($index = null)
    {
        assert('is_null($index) || is_int($index); // Wrong type for argument 1. Integer expected');
        if (is_null($index)) {
            $this->tabIndex = null;
        } else {
            $this->tabIndex = (int) $index;
        }
    }

    /**
     * get rights management settings
     *
     * Returns an array of {@link DDLGrant} objects.
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
        if ($this->column instanceof DDLColumn) {
            // if a column is present, the field must not have any grants itself
            return $this->column->getGrants();
        } else {
            return $this->grants;
        }
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
        if ($this->column instanceof DDLColumn) {
            // if a column is present, the field must not have any grants itself
            $this->column->dropGrants();
        } else {
            $this->grants = array();
        }
    }

    /**
     * add rights management setting
     *
     * {@link DDLGrant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the configuration by using the given
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
        if ($this->column instanceof DDLColumn) {
            // if a column is present, the field must not have any grants itself
            return $this->column->addGrant($user, $role, $level);
        }
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
     * set rights management setting
     *
     * {@link DDLGrant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the configuration.
     *
     * @access  public
     * @param   DDLGrant  $grant set a new grnat object
     */
    public function setGrant(DDLGrant $grant)
    {
        if ($this->column instanceof DDLColumn) {
            // if a column is present, the field must not have any grants itself
            $this->column->setGrant($grant);
        } else {
            $this->grants[] = $grant;
        }
    }

    /**
     * check if the field has a column element
     *
     * If the field has a column as child element, it does not refer to a column in a real table.
     * Therefore it must not be included in any queries on the database.
     *
     * @access  public
     * @return  bool
     */
    public function refersToTable()
    {
        return !($this->column instanceof DDLColumn);
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
     * @return  DDLField
     */
    public static function unserializeFromXDDL(SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (isset($attributes['name'])) {
            $ddl = new self((string) $attributes['name'], $parent);
        } else {
            throw new InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
        }
        /* @var $child SimpleXMLElement */
        foreach ($node->children() as $child)
        {
            if (!isset($ddl->xddlTags[$child->getName()])) {
                $ddl->column = DDLColumn::unserializeFromXDDL($child);
                break; // there must be no more than 1 column
            }
        }
        unset($child);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }
}

?>