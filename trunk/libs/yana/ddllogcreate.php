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
 * database change-log create operation
 *
 * This wrapper class represents the structure of a database
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLLogCreate extends DDLLog
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "create";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'        => array('name',        'nmtoken'),
        'version'     => array('version',     'string'),
        'ignoreError' => array('ignoreError', 'bool'),
        'subject'     => array('subject',     'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string')
    );

    /** @var string */ protected $subject = null;
    /** @var string */ protected $name = null;

    /**#@-*/

    /**
     * Initialize instance.
     *
     * @param  string        $name      name of logcreate
     * @param  DDLChangeLog  $parent    parent
     */
    public function __construct($name, DDLChangeLog $parent = null)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $this->setName($name);
        $this->parent = $parent;
    }

    /**
     * Get type of changed object.
     *
     * Returns the type as "table", "column", "index", "sequence", "trigger", "constraint", "view".
     *
     * Note: For columns the returned name includes the table ("table.column").
     *
     * @access  public
     * @return  string
     */
    public function getSubject()
    {
        if (is_string($this->subject)) {
            return $this->subject;
        } else {
            return null;
        }
    }

    /**
     * Set type of changed object.
     *
     * Subject may be: "table", "column", "index", "sequence", "trigger", "constraint", "view".
     *
     * @access  public
     * @param   string  $subject  new value of this property
     * @return  DDLLogCreate 
     */
    public function setSubject($subject = "")
    {
        assert('is_string($subject); // Wrong type for argument 1. String expected');
        assert('preg_match("/^(table|column|index|view|sequence|trigger|constraint)\$/", $subject); //Invalid subject');
        
        if (empty($subject)) {
            $this->subject = null;
        } else {
            $this->subject = "$subject";
        }
        return $this;
    }

    /**
     * Returns the name of the object that has changed.
     *
     * Note: For columns the returned name includes the table ("table.column").
     *
     * @access  public
     * @return  string
     */
    public function getName()
    {
        if (is_string($this->name)) {
            return $this->name;
        } else {
            return null;
        }
    }

    /**
     * Set (mandatory) name of changed object.
     *
     * @access  public
     * @param   string  $name   name of changed object
     * @throws  \Yana\Core\InvalidArgumentException  when name is empty or invalid
     * @return  DDLLogCreate
     */
    public function setName($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        if (!preg_match('/^[a-z]\w+$/is', $name)) {
            $message = "Not a valid object name: '$name'. " .
                "Must start with a letter and may only contain: a-z, 0-9, '-' and '_'.";
            throw new \Yana\Core\InvalidArgumentException($message);

        } else {
            $this->name = mb_strtolower($name);
        }
        return $this;
    }

    /**
     * Calls the provided handler function.
     *
     * Provided arguments are the object's parameter list.
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @return  bool
     */
    public function commitUpdate()
    {
        if (isset(self::$handler)) {
            return call_user_func(self::$handler, $this->getSubject(), $this->getName());
        } else {
            return false;
        }
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  DDLLogCreate
     * @throws   \Yana\Core\InvalidArgumentException  when the name attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            throw new \Yana\Core\InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
        }
        $ddl = new self((string) $attributes['name'], $parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>