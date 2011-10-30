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
 * database foreign-key constraints
 *
 * Indexes are meant to improve the performance of select-statements. An index is a sorted list
 * of column values. Scanning an index is usually faster than scanning a whole table.
 * However: as creating and maintaining an index causes some overhead, insert- and update-statements
 * will be slower when using an index. Also some DBMS may require that you update your indexes quite
 * often.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLIndex extends DDLObject
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var string
     */
    protected $xddlTag = "index";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'      => array('name',      'nmtoken'),
        'clustered' => array('clustered', 'bool'),
        'unique'    => array('unique',    'bool'),
        'title'     => array('title',     'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string'),
        'column'      => array('columns',     'array', 'DDLIndexColumn')
    );

    /** @var string           */ protected $description = null;
    /** @var string           */ protected $title = null;
    /** @var bool             */ protected $clustered = null;
    /** @var bool             */ protected $unique = null;
    /** @var DDLIndexColumn[] */ protected $columns = array();
    /** @var DDLTable         */ protected $parent = null;

    /**#@-*/

    /**
     * Initialize instance.
     *
     * @access  public
     * @param   string    $name     index name
     * @param   DDLTable  $parent   parent
     */
    public function __construct($name = "", DDLTable $parent = null)
    {
        $this->setName($name);
        $this->parent = $parent;
    }

    /**
     * Get parent table.
     *
     * @return  DDLTable
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get title of index.
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
     * @return  DDLIndex
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
     * Set a description.
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
     * @return  DDLIndex
     */
    public function setDescription($description = "")
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
     * Get name of the source table.
     *
     * The source table is where the foreign-key-constraint is defined.
     *
     * @access  public
     * @return  string
     */
    public function getSourceTable()
    {
        if (isset($this->parent)) {
            return $this->parent->getName();
        } else {
            return null;
        }
    }

    /**
     * Get list of indexed columns.
     *
     * Returns an associative array of indexed columns, where the keys are
     * the column names and the values are instances of DDLIndexColumn.
     *
     * @access  public
     * @return  array
     */
    public function getColumns()
    {
        assert('is_array($this->columns); // member "columns" is expected to be an array');
        return $this->columns;
    }

    /**
     * Adds a column to be indexed.
     *
     * The argument $isAscending may be set to false, to create an index that
     * is sorted in descending order for the given column.
     *
     * @access  public
     * @param   string  $name             name of indexed column
     * @param   bool    $isAscending      optional sorting argument
     * @return  DDLIndexColumn
     * @throws  \Yana\Core\Exceptions\NotFoundException         when column does not exists
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when name is invalid
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    if the column is part of the index
     */
    public function addColumn($name, $isAscending = true)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        assert('is_bool($isAscending); // Wrong type for argument 2. Boolean expected');

        if (isset($this->parent) && !$this->parent->isColumn($name)) {
            $message = "No such column '$name' in table '{$this->parent->getName()}'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, E_USER_WARNING);
        }

        $indexColumn = new DDLIndexColumn($name);
        $indexColumn->setSorting($isAscending);
        $name = $indexColumn->getName();
        assert('is_string($name);');

        if (isset($this->columns[$name])) {
            $message = "Column '$name' already defined in index.";
            throw new Yana\Core\Exceptions\AlreadyExistsException($message, E_USER_WARNING);;
        }

        $this->columns[$name] = $indexColumn;
        return $indexColumn;
    }

    /**
     * Removes the column from the index, if it is defined.
     *
     * @access  public
     * @param   string  $name name of indexed column
     */
    public function dropColumn($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $name = strtolower($name);
        if (isset($this->columns[$name])) {
            unset($this->columns[$name]);
        }
    }

    /**
     * Check wether index contains only unique values.
     *
     * Note: a unique index demands an unique-constraint on the column and vice
     * versa.
     *
     * @access  public
     * @return  bool
     */
    public function isUnique()
    {
        return !empty($this->unique);
    }

    /**
     * Set wether index contains only unique values.
     *
     * Note: a unique index demands an unique-constraint on the column and vice
     * versa.
     *
     * @access  public
     * @param   bool  $isUnique   new value of this property
     * @return  DDLIndex 
     */
    public function setUnique($isUnique)
    {
        assert('is_bool($isUnique); // Wrong type for argument 1. Boolean expected');
        $this->unique = (bool) $isUnique;
        return $this;
    }

    /**
     * Check wether index is clustered (MSSQL).
     *
     * This applies to MSSQL only.
     * A clustered index means, that the DBS should try to store values, which
     * are close to each other in the index, close to each other in the
     * tablespace, so that they fit inside the same memory page, when retrieving
     * data from a table.
     *
     * @access  public
     * @return  bool
     */
    public function isClustered()
    {
        return !empty($this->clustered);
    }

    /**
     * Set wether index should be clustered (MSSQL).
     *
     * This applies to MSSQL only.
     * A clustered index means, that the DBS should try to store values, which
     * are close to each other in the index, close to each other in the
     * tablespace, so that they fit inside the same memory page, when retrieving
     * data from a table.
     *
     * Typically you might want the primary index to be clustered (which is the
     * default anyway), or an index used for a sorting column.
     *
     * Also you may have only one clustered index per table. So if you set an
     * index to be clustered, any previously clustered index will become
     * unclustered.
     *
     * @access  public
     * @param   bool  $isClustered  new value of this property
     * @return  DDLIndex
     */
    public function setClustered($isClustered)
    {
        assert('is_bool($isClustered); // Wrong type for argument 1. Boolean expected');
        if ($isClustered) {

            $this->clustered = true;

            // propagate new setting to table
            if (isset($this->parent)) {
                $this->parent->setPrimaryIndex($this);
            }
        } else {
            $this->clustered = false;
        }
        return $this;
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  DDLIndex
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        $name = "";
        if (isset($attributes['name'])) {
            $name = (string) $attributes['name'];
        }
        $ddl = new self($name, $parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>