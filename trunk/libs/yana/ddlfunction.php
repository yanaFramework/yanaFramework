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
 * database function structure
 *
 * This wrapper class represents the structure of a database
 *
 * Note that functions are not supported by all DBMS. The implementation is
 * DBMS and language specific.
 *
 * MySQL distinguishes between "procedures" and "functions", where the main
 * difference is, that procedures may not have a return value. If you don't
 * specify a return value, the function will be treated as a "procedure" for
 * MySQL.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLFunction extends DDLNamedObject implements IsIncludableDDL
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "function";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'  => array('name',  'nmtoken'),
        'title' => array('title', 'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description'    => array('description',     'string'),
        'implementation' => array('implementations', 'array', 'DDLFunctionImplementation', 'dbms')
    );

    /** @var string                      */ protected $description = null;
    /** @var string                      */ protected $title = null;
    /** @var DDLFunctionImplementation[] */ protected $implementations = array();
    /** @var DDLDatabase                 */ protected $parent = null;

    /**#@-*/

    /**
     * constructor
     *
     * @param  string       $name    foreign key name
     * @param  DDLDatabase  $parent  parent database
     */
    public function __construct($name, DDLDatabase $parent = null)
    {
        parent::__construct($name);
        $this->parent = $parent;
    }

    /**
     * Get parent.
     *
     * @return  DDLDatabase
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
     * @param   string  $title  any text is valid
     * @return  DDLFunction
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
     * Set the description.
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
     * @param   string  $description  any text is valid
     * @return  DDLFunction
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
     * get function implementation
     *
     * Get the function implementation for the given DBMS.
     *
     * Returns NULL if no implementation is available.
     *
     * @access  public
     * @param   string  $dbms   target DBMS, defaults to "generic"
     * @return  DDLFunctionImplementation
     */
    public function getImplementation($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('in_array($dbms, DDLDatabase::getSupportedDBMS()); // Unsupported DBMS');
        if (!isset($this->implementations[$dbms])) {
            return null;
        } else {
            return $this->implementations[$dbms];
        }
    }

    /**
     * get function implementations
     *
     * Returns the list of function implementations for all DBMS.
     *
     * Returns an empty array if no implementation is available.
     *
     * @access  public
     * @return  array
     */
    public function getImplementations()
    {
        return $this->implementations;
    }

    /**
     * set function implementation
     *
     * This adds an implementation for a given DBMS.
     * Returns the implementation as an object.
     *
     * An exception is thrown if the implementation already exists.
     *
     * @access  public
     * @param   string $dbms    target DBMS, defaults to "generic"
     * @return  DDLFunctionImplementation
     * @throws  AlreadyExistsException  when an implementation for the chosen DBMS already exists
     */
    public function addImplementation($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        if (!isset($this->implementations[$dbms])) {
            $implementation = new DDLFunctionImplementation($dbms);
            $this->implementations[$dbms] = $implementation;
            return $implementation;
        } else {
            throw new AlreadyExistsException("Implementation for DBMS '$dbms' is already defined.");
        }
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  DDLFunction
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            throw new InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
        }
        $ddl = new self((string) $attributes['name'], $parent);
        $ddl->_unserializeFromXDDL($node);
        // implementations
        foreach (array_keys($ddl->implementations) as $i)
        {
            if (is_int($i)) {
                $ddl->implementations['generic'] = $ddl->implementations[$i];
                unset($ddl->implementations[$i]);
            }
        }
        return $ddl;
    }
}

?>