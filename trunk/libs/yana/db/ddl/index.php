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
 * Database foreign-key constraints.
 *
 * Indexes are meant to improve the performance of select-statements. An index is a sorted list
 * of column values. Scanning an index is usually faster than scanning a whole table.
 * However: as creating and maintaining an index causes some overhead, insert- and update-statements
 * will be slower when using an index. Also some DBMS may require that you update your indexes quite
 * often.
 *
 * @package     yana
 * @subpackage  db
 */
class Index extends \Yana\Db\Ddl\AbstractUnnamedObject
{

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var string
     * @ignore
     */
    protected $xddlTag = "index";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlAttributes = array(
        'name'      => array('name',      'nmtoken'),
        'clustered' => array('clustered', 'bool'),
        'unique'    => array('unique',    'bool'),
        'title'     => array('title',     'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlTags = array(
        'description' => array('description', 'string'),
        'column'      => array('columns',     'array', 'Yana\Db\Ddl\IndexColumn')
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
    protected $clustered = null;

    /**
     * @var  bool
     * @ignore
     */
    protected $fulltext = null;

    /**
     * @var  bool
     * @ignore
     */
    protected $unique = null;

    /**
     * @var  \Yana\Db\Ddl\IndexColumn[]
     * @ignore
     */
    protected $columns = array();

    /**
     * @var  \Yana\Db\Ddl\Table
     * @ignore
     */
    protected $parent = null;

    /**
     * Initialize instance.
     *
     * @param  string              $name    index name
     * @param  \Yana\Db\Ddl\Table  $parent  parent
     */
    public function __construct($name = "", \Yana\Db\Ddl\Table $parent = null)
    {
        assert('is_string($name); // Invalid argument $name: String expected');

        $this->setName($name);
        $this->parent = $parent;
    }

    /**
     * Get parent table.
     *
     * @return  \Yana\Db\Ddl\Table
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
     * @param   string  $title  any text is valid
     * @return  \Yana\Db\Ddl\Index
     */
    public function setTitle($title = "")
    {
        assert('is_string($title); // Invalid argument $title: String expected');

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
     * @param   string  $description  new value of this property
     * @return  \Yana\Db\Ddl\Index
     */
    public function setDescription($description = "")
    {
        assert('is_string($description); // Invalid argument $description: String expected');

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
     * the column names and the values are instances of \Yana\Db\Ddl\IndexColumn.
     *
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
     * @param   string  $name             name of indexed column
     * @param   bool    $isAscending      optional sorting argument
     * @return  \Yana\Db\Ddl\IndexColumn
     * @throws  \Yana\Core\Exceptions\NotFoundException         when column does not exists
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when name is invalid
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    if the column is part of the index
     */
    public function addColumn($name, $isAscending = true)
    {
        assert('is_string($name); // Invalid argument $name: String expected');
        assert('is_bool($isAscending); // Invalid argument $isAscending: Boolean expected');

        if (isset($this->parent) && !$this->parent->isColumn($name)) {
            $message = "No such column '$name' in table '{$this->parent->getName()}'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        $indexColumn = new \Yana\Db\Ddl\IndexColumn($name);
        $indexColumn->setSorting($isAscending);
        $name = $indexColumn->getName();
        assert('is_string($name);');

        if (isset($this->columns[$name])) {
            $message = "Column '$name' already defined in index.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($name);
            throw $exception;
        }

        $this->columns[$name] = $indexColumn;
        return $indexColumn;
    }

    /**
     * Removes the column from the index, if it is defined.
     *
     * @param   string  $name name of indexed column
     * @return  self
     */
    public function dropColumn($name)
    {
        assert('is_string($name); // Invalid argument $name: String expected');

        $name = strtolower($name);
        if (isset($this->columns[$name])) {
            unset($this->columns[$name]);
        }
        return $this;
    }

    /**
     * Check wether index contains only unique values.
     *
     * Note: a unique index demands an unique-constraint on the column and vice
     * versa.
     *
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
     * @param   bool  $isUnique   new value of this property
     * @return  \Yana\Db\Ddl\Index 
     */
    public function setUnique($isUnique)
    {
        assert('is_bool($isUnique); // Invalid argument $isUnique: Boolean expected');

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
     * @param   bool  $isClustered  new value of this property
     * @return  \Yana\Db\Ddl\Index
     */
    public function setClustered($isClustered)
    {
        assert('is_bool($isClustered); // Invalid argument $isClustered: Boolean expected');

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
     * Set wether this is a fulltext index.
     *
     * This works differently on database drivers.
     * Microsoft SQL-Server allows only 1 fulltext index (we don't check).
     * In MySQL, however, you can have as many as you want.
     * PostgreSQL doesn't support them, but IBM DB2 does with a different syntax.
     * Also the list of supported data types may vary.
     *
     * Furthermore, IBM DB2 allows the index to be created on a view.
     * MySQL doesn't and neither does MSSQL (AFAIK).
     *
     * @param   bool  $isFulltext  new value of this property
     * @return  \Yana\Db\Ddl\Index
     */
    public function setFulltext($isFulltext)
    {
        assert('is_bool($isFulltext); // Invalid argument $isFulltext: Boolean expected');

        if ($isFulltext) {
            $this->fulltext = true;

        } else {
            $this->fulltext = false;
        }
        return $this;
    }

    /**
     * Check wether this is a fulltext index.
     *
     * Returns bool(true) if it is, and bool(false) if it isn't.
     *
     * @return  bool
     */
    public function isFulltext()
    {
        return !empty($this->fulltext);
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\Index
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