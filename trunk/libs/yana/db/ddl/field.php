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
 * Database form field structure.
 *
 * A field represents an UI input-element inside a form.
 * Each field has a source column and several attributes to change it's representation.
 *
 * The type of the input-element depends on the type of the source column.
 *
 * @package     yana
 * @subpackage  db
 */
class Field extends \Yana\Db\Ddl\AbstractNamedObject
{

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $xddlTag = "input";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
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
     *
     * @var  array
     * @ignore
     */
    protected $xddlTags = array(
        'description'   => array('description', 'string'),
        'grant'         => array('grants',      'array', 'Yana\Db\Ddl\Grant'),
        'column'        => array('column',      'Yana\Db\Ddl\Column'),
        'event'         => array('events',      'array', 'Yana\Db\Ddl\Event')
    );

    /**
     * @var  string
     * @ignore
     */
    protected $description = null;

    /**
     * @var  string
     * @ignore
     */
    protected $title = null;

    /**
     * @var  bool
     * @ignore
     */
    protected $hidden = null;

    /**
     * @var  bool
     * @ignore
     */
    protected $readonly = null;

    /**
     * @var  string
     * @ignore
     */
    protected $cssClass = null;

    /**
     * @var  int
     * @ignore
     */
    protected $tabIndex = null;

    /**
     * @var  \Yana\Db\Ddl\Grant[]
     * @ignore
     */
    protected $grants = array();

    /**
     * @var  \Yana\Db\Ddl\Event[]
     * @ignore
     */
    protected $events = array();

    /**
     * @var  \Yana\Db\Ddl\Column
     * @ignore
     */
    protected $column = null;

    /**
     * cached value
     *
     * @var  bool
     * @ignore
     */
    protected $isSelectable = null;

    /**
     * cached value
     *
     * @var  bool
     * @ignore
     */
    protected $isInsertable = null;

    /**
     * cached value
     *
     * @var  bool
     * @ignore
     */
    protected $isUpdatable = null;

    /**
     * cached value
     *
     * @var  bool
     * @ignore
     */
    protected $isDeletable = null;

    /**
     * cached value
     *
     * @var  bool
     * @ignore
     */
    protected $isGrantable = null;

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
     * @return  string|NULL
     */
    public function getDescription(): ?string
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
     * @param   string  $description  new value of this property
     * @return  $this
     */
    public function setDescription(string $description = "")
    {
        assert(is_string($description), 'Invalid argument $description: string expected');
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
     * @return  string|NULL
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
     * A text that may be used in the UI as a label for the control element associated with the
     * field.
     * Note that this may also be a language token, which is to be translated to the selected
     * language.
     *
     * To reset this property, leave the parameter empty.
     *
     * @param   string  $title  title 
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
     * Get list of events.
     *
     * Events are Javascript functions that are fired when the user clicks the field.
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
     * Actions are (Javascript) functions that are fired when the user clicks the field.
     * See the manual for more details.
     *
     * @param   string  $name    event name
     * @return  \Yana\Db\Ddl\Event|NULL
     */
    public function getEvent(string $name): ?\Yana\Db\Ddl\Event
    {
        $name = mb_strtolower($name);
        if (isset($this->events[$name])) {
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
     * @param   string  $name   case-insensitive event name
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
     * Drop the event with the specified name.
     *
     * Returns bool(true) on success and bool(false) if there is no such event to drop.
     *
     * @param   string  $name  event name
     * @return  bool
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
     * Check whether the column should be visible
     *
     * A field may be hidden, in which case it is not to be shown in the UI.
     * Note that an element may also not be visible due to the fact, that the user has no permission
     * to see it, because he is not granted to do so.
     *
     * @return  bool
     */
    public function isVisible(): bool
    {
        return empty($this->hidden);
    }

    /**
     * Celect whether the column should be visible
     *
     * A field may be hidden, in which case it is not to be shown in the UI.
     * Set this to bool(false) to hide the field or bool(true) to make it visible.
     *
     * @param   bool  $isVisible  new value of this property
     * @return  $this
     */
    public function setVisible(bool $isVisible)
    {
        $this->hidden = !$isVisible;
        return $this;
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
     * @return  bool
     */
    public function isInsertable(): bool
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
     
     * @return  bool
     */
    public function isUpdatable(): bool
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
     * @return  bool
     */
    public function isDeletable(): bool
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
     * @return  bool
     */
    public function isGrantable(): bool
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
     * @return  bool
     */
    public function isReadonly(): bool
    {
        return !empty($this->readonly);
    }

    /**
     * Set read-only access.
     *
     * You may set the field to be read-only to prevent any changes to it by setting this to
     * bool(true).
     *
     * @param   bool  $isReadonly   new value of this property
     * @return  $this
     */
    public function setReadonly(bool $isReadonly)
    {
        $this->readonly = (bool) $isReadonly;
        return $this;
    }

    /**
     * Get CSS class attribute.
     *
     * Returns the prefered CSS-class for this field as a string or NULL if there is none.
     *
     * @return  string|NULL
     */
    public function getCssClass(): ?string
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
     * @param   string  $class  name of a css class
     * @return  $this
     */
    public function setCssClass(string $class = "")
    {
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
     * @return  int|NULL
     */
    public function getTabIndex(): ?int
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
     * @param   int  $index  tab-index
     * @return  $this
     */
    public function setTabIndex(?int $index = null)
    {
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
     * @return  array
     */
    public function getGrants(): array
    {
        assert(is_array($this->grants), 'Member "grants" is expected to be an array.');
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
     * @param   string  $user   user group
     * @param   string  $role   user role
     * @param   int     $level  security level
     * @return  \Yana\Db\Ddl\Grant
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when $level is out of range [0,100]
     */
    public function addGrant(?string $user = null, ?string $role = null, ?int $level = null): \Yana\Db\Ddl\Grant
    {
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
     * @param   \Yana\Db\Ddl\Grant  $grant  new grant object
     * @return  $this
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
     * @return  \Yana\Db\Ddl\Column|NULL
     */
    public function getColumn(): ?\Yana\Db\Ddl\Column
    {
        return $this->column;
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @param   \SimpleXMLElement  $node  XML node
     * @return  $this
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the name attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            $message = "Missing name attribute.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
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