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
 * Database event declaration.
 *
 * This wrapper class represents the structure of a database
 *
 * Form-Events are elements like buttons or hyperlinks, that are created on input or form elements.
 * They may have an icon and/or label. The event is fired when the user clicks the link.
 * The type of the event and the syntax is dependent on the chosen language.
 *
 * @package     yana
 * @subpackage  db
 */
class Event extends \Yana\Db\Ddl\AbstractNamedObject
{

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $xddlTag = "event";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlAttributes = array(
        'name'     => array('name',     'nmtoken'),
        'language' => array('language', 'string'),
        'title'    => array('title',    'string'),
        'label'    => array('label',    'string'),
        'icon'     => array('icon',     'string'),
        '#pcdata'  => array('action',   'string')
    );

    /**
     * @var  string
     * @ignore
     */
    protected $action = "";

    /**
     * @var  string
     * @ignore
     */
    protected $language = null;

    /**
     * @var  string
     * @ignore
     */
    protected $title = null;

    /**
     * @var  string
     * @ignore
     */
    protected $label = null;

    /**
     * @var  string
     * @ignore
     */
    protected $icon = null;

    /**
     * Get action code.
     *
     * The code or function name that should be executed when the event is fired.
     * The syntax is dependent on the chosen language.
     *
     * @return  string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Set action code.
     *
     * Set the code or function name that should be executed when the event is fired.
     * The syntax is dependent on the chosen language.
     *
     * @param   string  $action  function name (if language = php) or program code (if language = javascript)
     * @return  $this
     */
    public function setAction(string $action = "")
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Get programming language.
     *
     * Returns the programming language of the event-implementation as a string or NULL if the
     * option is not set.
     *
     * @return  string|NULL
     */
    public function getLanguage(): ?string
    {
        if (is_string($this->language)) {
            return $this->language;
        } else {
            return null;
        }
    }

    /**
     * Set programming language.
     *
     * The programming language of the event-implementation. May be any string.
     * If the option is not set, the framework will interpret the event-handler as the name of a
     * defined plugin action.
     *
     * To reset the property, leave the parameter empty.
     *
     * @param   string  $language   name of programming language (currently just "javascript" and "php" are supported)
     * @return  $this
     */
    public function setLanguage(string $language = "")
    {
        if ($language === "") {
            $this->language = null;
        } else {
            $this->language = $language;
        }
        return $this;
    }

    /**
     * Get label.
     *
     * Returns the label used for the clickable link as a string or NULL if the property is not set.
     *
     * @return  string|NULL
     */
    public function getLabel(): ?string
    {
        if (is_string($this->label)) {
            return $this->label;
        } else {
            return null;
        }
    }

    /**
     * Set text-label.
     *
     * Sets the label used for the clickable link.
     * To reset the property, leave the parameter empty.
     *
     * @param   string  $label  any text
     * @return  $this
     */
    public function setLabel(string $label = "")
    {
        if ($label === "") {
            $this->label = null;
        } else {
            $this->label = $label;
        }
        return $this;
    }

    /**
     * Get title.
     *
     * Returns the title-attribute used for the clickable link as a string or NULL if the property
     * is not set.
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
     * Sets the title-attribute used for the clickable link.
     * To reset the property, leave the parameter empty.
     *
     * @param   string  $title  any text, but no HTML
     * @return  $this
     */
    public function setTitle(string $title = "")
    {
        if ($title === "") {
            $this->title = null;
        } else {
            $this->title = $title;
        }
        return $this;
    }

    /**
     * Get path to icon image.
     *
     * Returns the file path for the icon image that should be displayed on the clickable link
     * or NULL if the property is not set.
     *
     * @return  string|NULL
     */
    public function getIcon(): ?string
    {
        if (is_string($this->icon)) {
            return $this->icon;
        } else {
            return null;
        }
    }

    /**
     * Set path to icon image.
     *
     * Sets the source file for the image used to create the clickable link.
     * To reset the property, leave the parameter empty.
     *
     * @param   string  $icon   icon image
     * @return  $this
     */
    public function setIcon(string $icon = "")
    {
        assert(empty($icon) || is_file($icon), 'Invalid argument type argument 1. File expected');
        if ($icon === "") {
            $this->icon = null;
        } else {
            $this->icon = $icon;
        }
        return $this;
    }

    /**
     * Unserialize a XDDL-node to an object.
     *
     * Returns the unserialized object.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\Event
     * @throws   \Yana\Core\Exceptions\InvalidArgumentException  if the name attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            $message = "Missing name attribute.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $ddl = new self((string) $attributes['name'], $parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>