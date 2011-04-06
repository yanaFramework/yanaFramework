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
 * database change-log update operation
 *
 * This wrapper class represents the structure of a database
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLLogUpdate extends DDLLogCreate
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "update";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'        => array('name',             'nmtoken'),
        'version'     => array('version',          'string'),
        'ignoreError' => array('ignoreError',      'bool'),
        'subject'     => array('subject',          'string'),
        'property'    => array('propertyName',     'string'),
        'value'       => array('propertyValue',    'string'),
        'oldvalue'    => array('oldPropertyValue', 'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string')
    );

    /** @var string */ protected $propertyName = null;
    /** @var string */ protected $propertyValue = null;
    /** @var string */ protected $oldPropertyValue = null;

    /**#@-*/

    /**
     * get name of updated property
     *
     * Specifies which property of the object has been updated.
     * Returns the name of the property.
     *
     * @access  public
     * @return  string
     */
    public function getPropertyName()
    {
        if (is_string($this->propertyName)) {
            return $this->propertyName;
        } else {
            return null;
        }
    }

    /**
     * set name of updated property
     *
     * Specifies which property of the object has been updated.
     *
     * @access  public
     * @param   string  $name   name of updated property
     */
    public function setPropertyName($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (empty($name)) {
            $this->propertyName = null;
        } else {
            $this->propertyName = "$name";
        }
    }

    /**
     * get new value of updated property
     *
     * Returns the new value of the property.
     * Note that the value may be a serialized string, depending on the
     * implementation you use.
     *
     * @access  public
     * @return  string
     */
    public function getPropertyValue()
    {
        if (is_string($this->propertyValue)) {
            return $this->propertyValue;
        } else {
            return null;
        }
    }

    /**
     * set new value of updated property
     *
     * Note that the value may be a serialized string, depending on the
     * implementation you use.
     *
     * @access  public
     * @param   string  $value  value of updated property
     */
    public function setPropertyValue($value)
    {
        assert('is_string($value); // Wrong type for argument 1. String expected');
        if (empty($value)) {
            $this->propertyValue = null;
        } else {
            $this->propertyValue = "$value";
        }
    }

    /**
     * get old value of updated property
     *
     * Returns the old value of the property.
     * Note that the value may be a serialized string, depending on the
     * implementation you use.
     *
     * @access  public
     * @return  string
     */
    public function getOldPropertyValue()
    {
        if (is_string($this->oldPropertyValue)) {
            return $this->oldPropertyValue;
        } else {
            return null;
        }
    }

    /**
     * set old value of updated property
     *
     * Note that the value may be a serialized string, depending on the
     * implementation you use.
     *
     * @access  public
     * @param   string  $oldValue   old value of updated property
     */
    public function setOldPropertyValue($oldValue)
    {
        assert('is_string($oldValue); // Wrong type for argument 1. String expected');
        if (empty($oldValue)) {
            $this->oldPropertyValue = null;
        } else {
            $this->oldPropertyValue = "$oldValue";
        }
    }

    /**
     * carry out the update
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @return  bool
     */
    public function commitUpdate()
    {
        if (isset(self::$handler)) {
            $propertyName = $this->getPropertyName();
            $propertyValue = $this->getPropertyValue();
            return call_user_func(self::$handler, $this->getSubject(), $this->getName(), $propertyName, $propertyValue);
        } else {
            return false;
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
     * @param   mixed             $parent  parent node (if any)
     * @return  DDLLogUpdate
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            throw new InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
        }
        $ddl = new self((string) $attributes['name'], $parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }
}

?>