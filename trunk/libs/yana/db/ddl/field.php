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
 * database form field structure
 *
 * A field represents an UI input-element inside a form.
 * Each field has a source column and several attributes to change it's representation.
 *
 * The type of the input-element depends on the type of the source column.
 *
 * @access      public
 * @package     yana
 * @subpackage  db
 */
class Field extends \Yana\Db\Ddl\AbstractNamedObject
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
        'grant'         => array('grants',      'array', 'Yana\Db\Ddl\Grant'),
        'column'        => array('column',      'Yana\Db\Ddl\Column'),
        'event'         => array('events',      'array', 'Yana\Db\Ddl\Event')
    );

    /** @var string      */ protected $description = null;
    /** @var string      */ protected $title = null;
    /** @var bool        */ protected $hidden = null;
    /** @var bool        */ protected $readonly = null;
    /** @var string      */ protected $cssClass = null;
    /** @var int         */ protected $tabIndex = null;
    /** @var \Yana\Db\Ddl\Grant[]  */ protected $grants = array();
    /** @var \Yana\Db\Ddl\Event[]  */ protected $events = array();
    /** @var \Yana\Db\Ddl\Column   */ protected $column = null;

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
        } elseif ($this->column instanceof \Yana\Db\Ddl\Column) {
            return $this->column->getDescription();
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
     * @return  \Yana\Db\Ddl\Field
     */
    public function setDescription($description = "")
    {
        assert('is_string($description); // Wrong type for argument 1. String expected');
        if ($this->column instanceof \Yana\Db\Ddl\Column) {
            $this->column->setDescription($description);
        } elseif (empty($description)) {
            $this->description = null;
        } else {
            $this->description = "$description";
        }
        return $this;
    }

    /**
     * Get title.
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
     * Set title.
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
     * @return  \Yana\Db\Ddl\Field
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
     * Get list of events.
     *
     * Events are Javascript functions that are fired when the user clicks the field.
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
     * Actions are (Javascript) functions that are fired when the user clicks the field.
     * See the manual for more details.
     *
     * @access  public
     * @param   string  $name    event name
     * @return  \Yana\Db\Ddl\Event
     */
    public function getEvent($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
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
        assert('is_string($name); // Invalid argument $name: string expected');
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
     * Drop the event with the specified name.
     *
     * Returns bool(true) on success and bool(false) if there is no such event to drop.
     *
     * @access  public
     * @param   string  $name  event name
     * @return  bool
     */
    public function dropEvent($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        if (isset($this->events["$name"])) {
            unset($this->events["$name"]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check whether the column should be visible
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
        return empty($this->hidden);
    }

    /**
     * Celect whether the column should be visible
     *
     * A field may be hidden, in which case it is not to be shown in the UI.
     * Set this to bool(false) to hide the field or bool(true) to make it visible.
     *
     * @access  public
     * @param   bool  $isVisible  new value of this property
     * @return  \Yana\Db\Ddl\Field
     */
    public function setVisible($isVisible)
    {
        assert('is_bool($isVisible); // Wrong type for argument 1. Boolean expected');
        $this->hidden = ! (bool) $isVisible;
        return $this;
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
        if (!isset($this->isSelectable)) {
            if (\Yana\Db\Ddl\Grant::checkPermissions($this->getGrants(), true)) {
                $this->isSelectable = true;
            } else {
                $this->isSelectable = false;
            }
        }
        return $this->isSelectable;
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
        if (!isset($this->isInsertable)) {
            if (\Yana\Db\Ddl\Grant::checkPermissions($this->getGrants(), false, true)) {
                $this->isInsertable = true;
            } else {
                $this->isInsertable = false;
            }
        }
        return $this->isInsertable;
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
        if (!isset($this->isUpdatable)) {
            if (!$this->isReadonly() && \Yana\Db\Ddl\Grant::checkPermissions($this->getGrants(), false, false, true)) {
                $this->isUpdatable = true;
            } else {
                $this->isUpdatable = false;
            }
        }
        return $this->isUpdatable;
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
        if (!isset($this->isDeletable)) {
            if (!$this->isReadonly() && \Yana\Db\Ddl\Grant::checkPermissions($this->getGrants(), false, false, false, true)) {
                $this->isDeletable = true;
            } else {
                $this->isDeletable = false;
            }
        }
        return $this->isDeletable;
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
        if (!isset($this->isGrantable)) {
            if (\Yana\Db\Ddl\Grant::checkPermissions($this->getGrants(), false, false, false, false, true)) {
                $this->isGrantable = true;
            } else {
                $this->isGrantable = false;
            }
        }
        return $this->isGrantable;
    }

    /**
     * Check whether the dbo has read-only access.
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
        return !empty($this->readonly);
    }

    /**
     * Set read-only access.
     *
     * You may set the field to be read-only to prevent any changes to it by setting this to
     * bool(true).
     *
     * @access  public
     * @param   bool  $isReadonly   new value of this property
     * @return  \Yana\Db\Ddl\Field
     */
    public function setReadonly($isReadonly)
    {
        assert('is_bool($isReadonly); // Wrong type for argument 1. Boolean expected');
        $this->readonly = (bool) $isReadonly;
        return $this;
    }

    /**
     * Get CSS class attribute.
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
     * Set CSS class attribute.
     *
     * The UI usually should provide id-attributes and CSS-classes for fields automatically.
     * However: you may add your own CSS-class here.
     *
     * To reset the property, leave the parameter empty.
     *
     * @access  public
     * @param   string  $class  name of a css class
     * @return  \Yana\Db\Ddl\Field
     */
    public function setCssClass($class = "")
    {
        assert('is_string($class); // Wrong type for argument 1. String expected');
        if (empty($class)) {
            $this->cssClass = null;
        } else {
            $this->cssClass = "$class";
        }
        return $this;
    }

    /**
     * Get tab-index attribute.
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
     * Set tab-index attribute.
     *
     * The tab-index is usually to be generated autoamtically by the UI. But you may overwrite this
     * setting.
     *
     * To reset the property, leave the parameter empty.
     *
     * @access  public
     * @param   int  $index  tab-index
     * @return  \Yana\Db\Ddl\Field
     */
    public function setTabIndex($index = null)
    {
        assert('is_null($index) || is_int($index); // Wrong type for argument 1. Integer expected');
        if (is_null($index)) {
            $this->tabIndex = null;
        } else {
            $this->tabIndex = (int) $index;
        }
        return $this;
    }

    /**
     * Get rights management settings.
     *
     * Returns an array of {@link \Yana\Db\Ddl\Grant} objects.
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
        if ($this->column instanceof \Yana\Db\Ddl\Column) {
            // if a column is present, the field must not have any grants itself
            return $this->column->getGrants();
        } else {
            return $this->grants;
        }
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
        if ($this->column instanceof \Yana\Db\Ddl\Column) {
            // if a column is present, the field must not have any grants itself
            $this->column->dropGrants();
        } else {
            $this->grants = array();
        }
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
        assert('is_null($level) || is_int($level); // Wrong type for argument 3. Integer expected');
        if ($this->column instanceof \Yana\Db\Ddl\Column) {
            // if a column is present, the field must not have any grants itself
            return $this->column->addGrant($user, $role, $level);
        }
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
     * Set rights management setting.
     *
     * {@link \Yana\Db\Ddl\Grant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the configuration.
     *
     * @access  public
     * @param   \Yana\Db\Ddl\Grant  $grant  new grant object
     * @return  \Yana\Db\Ddl\Field
     */
    public function setGrant(\Yana\Db\Ddl\Grant $grant)
    {
        if ($this->column instanceof \Yana\Db\Ddl\Column) {
            // if a column is present, the field must not have any grants itself
            $this->column->setGrant($grant);
        } else {
            $this->grants[] = $grant;
        }
        return $this;
    }

    /**
     * Get underlying column (if any).
     *
     * If the field has a column as child element, it does not refer to a column in a real table.
     * If such a column element exists, this function returns it.
     * If not, it returns NULL instead.
     *
     * @access  public
     * @return  \Yana\Db\Ddl\Column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node  XML node
     * @return  \Yana\Db\Ddl\Field
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the name attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
        }
        $ddl = new self((string) $attributes['name']);
        /* @var $child \SimpleXMLElement */
        foreach ($node->children() as $child)
        {
            if (!isset($ddl->xddlTags[$child->getName()])) {
                $ddl->column = \Yana\Db\Ddl\Column::unserializeFromXDDL($child);
                break; // there must be no more than 1 column
            }
        }
        unset($child);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>