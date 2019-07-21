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
 * database table structure
 *
 * This wrapper class represents the structure of a database
 *
 * @package     yana
 * @subpackage  db
 */
class Table extends \Yana\Db\Ddl\AbstractNamedObject implements \Yana\Db\Ddl\IsIncludableDDL
{
    /**#@+
     * @ignore

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "table";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'     => array('name',        'nmtoken'),
        'title'    => array('title',       'string'),
        'readonly' => array('readonly',    'bool'),
        'inherits' => array('inheritance', 'nmtoken')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string'),
        'grant'       => array('grants',      'array', 'Yana\Db\Ddl\Grant'),
        'primarykey'  => array('primaryKey',  'nmtoken'),
        'foreign'     => array('foreignKeys', 'array', 'Yana\Db\Ddl\ForeignKey'),
        'trigger'     => array('triggers',    'array', 'Yana\Db\Ddl\Trigger'),
        'constraint'  => array('constraints', 'array', 'Yana\Db\Ddl\Constraint'),
        'declaration' => array('columns',     'Yana\Db\Ddl\Column'),
        'index'       => array('indexes',     'array', 'Yana\Db\Ddl\Index')
    );

    /** @var string          */ protected $description = null;
    /** @var string          */ protected $title = null;
    /** @var bool            */ protected $readonly = null;
    /** @var string          */ protected $inheritance = null;
    /** @var string          */ protected $primaryKey = null;
    /** @var \Yana\Db\Ddl\Column[]     */ protected $columns = array();
    /** @var \Yana\Db\Ddl\ForeignKey[] */ protected $foreignKeys = array();
    /** @var \Yana\Db\Ddl\Index[]      */ protected $indexes = array();
    /** @var \Yana\Db\Ddl\Grant[]      */ protected $grants = array();
    /** @var \Yana\Db\Ddl\Trigger[]    */ protected $triggers = array();
    /** @var \Yana\Db\Ddl\Constraint[] */ protected $constraints = array();
    /** @var \Yana\Db\Ddl\Database     */ protected $parent = null;
    /** @var \Yana\Db\Ddl\Index        */ protected $primaryIndex = null;

    /**#@-*/

    /**
     * Initialize instance.
     *
     * @param  string       $name    foreign key name
     * @param  \Yana\Db\Ddl\Database  $parent  parent database
     */
    public function __construct($name = "", \Yana\Db\Ddl\Database $parent = null)
    {
        parent::__construct($name);
        $this->parent = $parent;
    }

    /**
     * Get parent database.
     *
     * May return NULL if there is none.
     *
     * @return  \Yana\Db\Ddl\Database
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
     * @param   string  $title  some text
     * @return  \Yana\Db\Ddl\Table 
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
     * Get the user description.
     *
     * The description serves two purposes:
     * 1st as offline-documentation 2nd as online-documentation.
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
     * Set the description property.
     *
     * The description serves two purposes:
     * 1st as offline-documentation 2nd as online-documentation.
     *
     * Note that the description may also contain an identifier for automatic
     * translation.
     *
     * To reset the property, leave the parameter $description empty.
     *
     * @param   string  $description  new value of this property
     * @return  \Yana\Db\Ddl\Table 
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
     * Check whether the dbo has read-only access.
     *
     * Returns bool(true) if the table is read-only and bool(false) otherwise.
     *
     * The default is bool(false).
     *
     * @return  bool
     */
    public function isReadonly()
    {
        return !empty($this->readonly);
    }

    /**
     * Set read-only access.
     *
     * You may set the table to be read-only to prevent any changes to it by setting this to
     * bool(true).
     *
     * @param   bool  $isReadonly   new value of this property
     * @return  \Yana\Db\Ddl\Table
     */
    public function setReadonly($isReadonly = false)
    {
        assert('is_bool($isReadonly); // Wrong type for argument 1. Boolean expected');
        $this->readonly = (bool) $isReadonly;
        return $this;
    }

    /**
     * Get column definition.
     *
     * Returns the column definition with the name $name as an instance of
     * \Yana\Db\Ddl\Column. If no column with the given name exists, the function returns
     * NULL instead.
     *
     * @param   string  $name   column name
     * @return  \Yana\Db\Ddl\Column
     */
    public function getColumn($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $name = mb_strtolower($name);
        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        } else {
            return null;
        }
    }

    /**
     * List all columns that match a certain type.
     *
     * @param   string  $type   datatype ('string', 'text', 'int', ...)
     * @return  array
     */
    public function getColumnsByType($type)
    {
        assert('is_string($type); // Wrong type for argument 1. String expected');
        assert('in_array($type, \Yana\Db\Ddl\ColumnTypeEnumeration::getSupportedTypes()); // Undefined column type "' . $type . '". ');
        assert('is_array($this->columns); // Member "columns" is expected to be an array.');
        $columns = array();
        foreach ($this->columns as $column)
        {
            if ($column->getType() == $type) {
                $columns[] = $column;
            }
        }
        return $columns;
    }

    /**
     * List of all columns.
     *
     * Returns an associative array of all columns in the table, where each key is the column name
     * and each value is and object of type {@see \Yana\Db\Ddl\Column}.
     *
     * @return  array
     */
    public function getColumns()
    {
        assert('is_array($this->columns); // Member "columns" is expected to be an array.');
        return $this->columns;
    }

    /**
     * List names of all columns in a table.
     *
     * Returns a numeric array of the names of all columns in the table.
     *
     * @return  array
     */
    public function getColumnNames()
    {
        assert('is_array($this->columns); // Member "columns" is expected to be an array.');
        return array_keys($this->columns);
    }

    /**
     * List all columns that contain blobs.
     *
     * This function provides a list of all columns, which are of type "image" or "file" in the
     * table as a numeric array of objects of type {@see \Yana\Db\Ddl\Column}.
     *
     * @return  array
     */
    public function getFileColumns()
    {
        assert('is_array($this->columns); // Member "columns" is expected to be an array.');
        $columns = array();
        foreach ($this->columns as $column)
        {
            if ($column->isFile()) {
                $columns[] = $column;
            }
        }
        return $columns;
    }

    /**
     * Get name of table, a foreign key refers to.
     *
     * Returns the lower-cased table name. Throws a NotFoundException when the column does not
     * exist. If there is no foreign key for the given column, NULL is returned.
     *
     * @param   string  $columnName  name of column containing the foreign key
     * @return  string
     * @throws  \Yana\Core\Exceptions\NotFoundException  when column does not exist
     */
    public function getTableByForeignKey($columnName)
    {
        if (!$this->isColumn($columnName)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such column '$columnName'.", \Yana\Log\TypeEnumeration::WARNING);
        }

        $columnName = mb_strtolower($columnName);
        foreach($this->foreignKeys as $key)
        {
            $columns = $key->getColumns();
            if (isset($columns[$columnName])) {
                return $key->getTargetTable();
            }
        }
        return null;
    }

    /**
     * Get target column, a foreign key refers to.
     *
     * Returns a column object.
     * Throws a NotFoundException when the column does not exist
     * If there is no foreign key for the given column, NULL is returned.
     *
     * @param   string  $columnName  name of column containing the foreign key
     * @return  \Yana\Db\Ddl\Column
     * @throws  \Yana\Core\Exceptions\NotFoundException  when column does not exist
     * @ignore
     */
    public function getColumnByForeignKey($columnName)
    {
        if (!$this->isColumn($columnName)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such column '$columnName'.", \Yana\Log\TypeEnumeration::WARNING);
        }
        if (! $this->parent instanceof \Yana\Db\Ddl\Database) {
            throw new \Yana\Core\Exceptions\NotFoundException("Target table is undefined.", \Yana\Log\TypeEnumeration::WARNING);
        }

        $columnName = mb_strtolower($columnName);
        /* @var $key \Yana\Db\Ddl\ForeignKey */
        foreach($this->foreignKeys as $key)
        {
            $columns = $key->getColumns();
            if (isset($columns[$columnName])) {
                $table = $this->parent->getTable($key->getTargetTable());
                if (! $table instanceof \Yana\Db\Ddl\Table) {
                    return null;
                }
                // get target column
                if (!empty($columns[$columnName])) {
                    $columnName = $columns[$columnName];
                // if no target column is set, fall back to primary key
                } else {
                    $columnName = $table->getPrimaryKey();
                }
                return $table->getColumn($columnName);
            }
        }
        return null;
    }

    /**
     * Check whether a column exists in the current structure.
     *
     * Returns bool(true) if the column is listed. Returns bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @param   string  $columnName  name of column
     * @return  bool
     */
    public function isColumn($columnName)
    {
        assert('is_string($columnName); // Wrong type for argument 1. String expected');
        return isset($this->columns[mb_strtolower($columnName)]);
    }

    /**
     * Add a new column.
     *
     * Adds a column of the given type and name and returns it as an instance of {@see \Yana\Db\Ddl\Column}.
     *
     * @param   string  $columnName  name of column
     * @param   string  $type        data-type
     * @return  \Yana\Db\Ddl\Column
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException   when another column with the same name already exists
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException if the name is not valid
     */
    public function addColumn($columnName, $type)
    {
        assert('is_string($columnName); // Wrong type for argument 1. String expected');
        assert('in_array($type, \Yana\Db\Ddl\ColumnTypeEnumeration::getSupportedTypes()); // Undefined column type "' . $type . '". ');
        $columnName = mb_strtolower($columnName);
        if (isset($this->columns[$columnName])) {
            $message = "Another column with the name '$columnName' already exists in table '{$this->getName()}'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($columnName);
            throw $exception;
        } else {
            // may throw \Yana\Core\Exceptions\InvalidArgumentException
            $column = new \Yana\Db\Ddl\Column($columnName, $this);
            $column->setType("$type");
            $this->columns[$columnName] = $column;
            return $column;
        }
    }

    /**
     * Check whether the table has a column containing a profile id.
     *
     * Returns bool(true) if there is a column named 'profile_id' and bool(false) otherwise.
     * The profile id is meant to allow rows in a table to be visible to seperate client profiles
     * only, which share the same database.
     *
     * @return  bool
     */
    public function hasProfile()
    {
        return isset($this->columns['profile_id']);
    }

    /**
     * Add/remove a profile constraint.
     *
     * The profile id is meant to allow rows in a table to be visible to seperate client profiles
     * only, which share the same database.
     *
     * Setting this to bool(false) removes any such column, if it exists, while bool(true) adds a
     * column of this name, if it is missing.
     *
     * @param   bool  $hasProfileConstraint  profile constraint
     * @return  \Yana\Db\Ddl\Table 
     */
    public function setProfile($hasProfileConstraint)
    {
        assert('is_bool($hasProfileConstraint); // Wrong type for argument 1. Boolean expected');
        if ($this->hasProfile()) {
            // remove profile
            if (!$hasProfileConstraint) {
                $this->dropColumn('profile_id');
            }
        } else {
            // create profile
            if ($hasProfileConstraint) {
                $this->addColumn('profile_id', 'string');
            }
        }
        return $this;
    }

    /**
     * Removes the column if it exists.
     *
     * @param   string  $columnName   column name
     * @throws  \Yana\Core\Exceptions\NotFoundException  when column does not exist
     */
    public function dropColumn($columnName)
    {
        assert('is_string($columnName); // Wrong type for argument 1. String expected');
        $columnName = mb_strtolower($columnName);
        if (isset($this->columns[$columnName])) {
            unset($this->columns[$columnName]);
        } else {
            $message = "No such column '$columnName' in table '{$this->getName()}'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
    }

    /**
     * Check whether the table has columns used for version control.
     *
     * The framework is capable of automatic version control. This means, if
     * two users work on the same row in the same table at the same time and one
     * of them modifies it, then the other will get a notice, that the source he
     * is currently editing has changed. He will not be able to overwrite the
     * changes made.
     *
     * This is meant for situations like a booking-system, where two agents book
     * the same resource. If version-checks were off, both may open the
     * edit-form and book the free resource. The first agent will book the
     * resource and get a sucess message. The second agent will book the
     * resource again and also get a message that the operation did suceed.
     * Both agents may even print a bill.
     * But in fact: only the second agent got the resource, because his changes
     * came last. The first agent sold a product he doesn't have.
     *
     * Automatic version checking prevents such situations by detecting them and
     * presenting an error message to the second agent.
     *
     * @param   bool  $lastModified  true: check for time_modified, false check for time_created
     * @return  bool
     */
    public function hasVersionCheck($lastModified = true)
    {
        assert('is_bool($lastModified); // Wrong type for argument 1. Boolean expected');
        if ($lastModified) {
            if (isset($this->columns['time_modified'])) {
                return true;
            } else {
                return false;
            }
        } else {
            if (isset($this->columns['time_created'])) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Add/remove a version check.
     *
     * The framework is capable of automatic version control. This means, if
     * two users work on the same row in the same table at the same time and one
     * of them modifies it, then the other will get a notice, that the source he
     * is currently editing has changed. He will not be able to overwrite the
     * changes made.
     *
     * @param   bool  $hasVersionCheck  new value of this property
     * @param   bool  $lastModified     true: check for time_modified, false check for time_created
     * @return  \Yana\Db\Ddl\Table 
     */
    public function setVersionCheck($hasVersionCheck, $lastModified = true)
    {
        assert('is_bool($hasVersionCheck); // Wrong type for argument 1. Boolean expected');
        assert('is_bool($lastModified); // Wrong type for argument 2. Boolean expected');
        if ($this->hasVersionCheck($lastModified)) {
            // remove version check
            if (!$hasVersionCheck) {
                if ($lastModified) {
                    $this->dropColumn('time_modified');
                } else {
                    $this->dropColumn('time_created');
                }
            }
        } else {
            // create version check
            if ($hasVersionCheck) {
                if ($lastModified) {
                    $this->addColumn('time_modified', 'timestamp');
                } else {
                    $this->addColumn('time_created', 'timestamp');
                }
            }
        }
        return $this;
    }

    /**
     * Check whether the table has columns used for author.
     *
     * The framework may protocol the name of the author who created and/or modified a row.
     *
     * @param   bool  $lastModified  true: check for user_modified, false check for user_created
     * @return  bool
     */
    public function hasAuthorLog($lastModified = true)
    {
        assert('is_bool($lastModified); // Wrong type for argument 1. Boolean expected');
        if ($lastModified) {
            return isset($this->columns['user_modified']);
        } else {
            return isset($this->columns['user_created']);
        }
    }

    /**
     * Add/remove a author.
     *
     * The framework may protocol the name of the author who created and/or modified a row.
     *
     * @param   bool  $hasAuthorLog  new value of this property
     * @param   bool  $lastModified  true: check for user_modified, false check for user_created
     * @return  \Yana\Db\Ddl\Table
     */
    public function setAuthorLog($hasAuthorLog, $lastModified = true)
    {
        assert('is_bool($hasAuthorLog); // Wrong type for argument 1. Boolean expected');
        assert('is_bool($lastModified); // Wrong type for argument 2. Boolean expected');
        if ($this->hasAuthorLog($lastModified)) {
            // remove version check
            if (!$hasAuthorLog) {
                $column = ($lastModified) ? 'user_modified' : 'user_created';
                $this->dropColumn($column);
            }
        } else {
            // create version check
            if ($hasAuthorLog) {
                $column = ($lastModified) ? 'user_modified' : 'user_created';
                $this->addColumn($column, 'string');
            }
        }
        return $this;
    }

    /**
     * Returns an array of foreign keys.
     *
     * If the table has no foreign keys, an empty array is returned.
     *
     * The returned results will be objects of type {@see \Yana\Db\Ddl\ForeignKey}.
     *
     * @return  array
     */
    public function getForeignKeys()
    {
        assert('is_array($this->foreignKeys); // Member "foreignKeys" is expected to be an array.');
        return $this->foreignKeys;
    }

    /**
     * Get foreign key.
     *
     * Returns the foreign key definition with the name $name as an instance of
     * {@see \Yana\Db\Ddl\ForeignKey}. If no foreign key with the given name exists, the function returns NULL instead.
     *
     * @param   string  $name   name of a foreign key
     * @return  \Yana\Db\Ddl\ForeignKey
     */
    public function getForeignKey($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $name = mb_strtolower($name);
        if (isset($this->foreignKeys[$name])) {
            return $this->foreignKeys[$name];
        } else {
            return null;
        }
    }

    /**
     * Add a foreign key constraint.
     *
     * Sets a foreign key constraint on a column.
     * The foreign key will point to the target $table.
     *
     * @param   string  $table            name of target table
     * @param   string  $constraintName   optional name of foreign-key constraint
     * @return  \Yana\Db\Ddl\ForeignKey
     * @throws  \Yana\Core\Exceptions\NotFoundException         if target table does not exist
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if constraint name is not valid or column
     */
    public function addForeignKey($table, $constraintName = "")
    {
        assert('is_string($table); // Wrong type for argument 1. String expected');
        assert('is_string($constraintName); // Wrong type for argument 2. String expected');

        if (isset($this->parent)) {
            if (!$this->parent->isTable($table)) {
                throw new \Yana\Core\Exceptions\NotFoundException("No such table '$table'.");
            }
        }

        $newForeignKey = new \Yana\Db\Ddl\ForeignKey($constraintName, $this);
        $newForeignKey->setTargetTable($table);
       
        if (empty($constraintName)) {
            $this->foreignKeys[] = $newForeignKey;
        } else {
            $constraintName = $newForeignKey->getName();
            $this->foreignKeys[$constraintName] = $newForeignKey;
        }
        return $newForeignKey;
    }

    /**
     * Get the primary key of a table.
     *
     * Returns the name of the primary key column of the table as a lower-cased string.
     * Returns NULL and issues a Warning if there is no primary key for $table.
     *
     * @return  string
     */
    public function getPrimaryKey()
    {
        if (isset($this->primaryKey)) {
            return $this->primaryKey;
        } else {
            \Yana\Log\LogManager::getLogger()
                ->addLog("Table '{$this->name}' has no primary key declaration.", \Yana\Log\TypeEnumeration::WARNING);
            return null;
        }
    }

    /**
     * Set the primary key.
     *
     * Select $columnName as the primary key of the table.
     * Throws a NotFoundException, if the column does not exist.
     *
     * @param   string  $columnName  name of column
     * @throws  \Yana\Core\Exceptions\NotFoundException  if column does not exist
     * @return  \Yana\Db\Ddl\Table 
     */
    public function setPrimaryKey($columnName)
    {
        assert('is_string($columnName); // Wrong type for argument 1. String expected');
        $name = mb_strtolower($columnName);
        if (isset($this->columns[$name])) {
            $this->primaryKey = $name;
        } else {
            $message = "No such column '$columnName' in table '{$this->getName()}'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
        return $this;
    }

    /**
     * Get parent table.
     *
     * Returns the name of the parent table as a string, or NULL if there is none.
     *
     * @return  string
     */
    public function getInheritance()
    {
        return $this->inheritance;
    }

    /**
     * set parent table
     *
     * Parameter $name is the name of the parent table (if any).
     * Note that only one parent is allowed.
     * This is unlike PostgreSQL where you may define multiple-inheritance with
     * n parent tables - with all consequences and issues linked to such
     * behavior.
     *
     * Set parameter $name to NULL to reset the setting.
     *
     * @param   string  $name  name of the parent table
     * @return  \Yana\Db\Ddl\Table 
     */
    public function setInheritance($name)
    {
        if (!empty($name)) {
            $this->inheritance = $name;
        } else {
            $this->inheritance = null;
        }
        return $this;
    }

    /**
     * List of all indexes.
     *
     * Returns a list of all defined indexes on the table as a numeric array of objects of type {@see \Yana\Db\Ddl\Index}.
     *
     * @return  array
     */
    public function getIndexes()
    {
        assert('is_array($this->indexes); // Member "columns" is expected to be an array.');
        return $this->indexes;
    }

    /**
     * Get index by name.
     *
     * Returns the index with the given name as an instance of {@see \Yana\Db\Ddl\Index}, or NULL if it does not exist.
     *
     * @param   string  $name  name of index
     * @return  \Yana\Db\Ddl\Index
     */
    public function getIndex($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $lowerCaseName = mb_strtolower($name);
        if (isset($this->indexes[$lowerCaseName])) {
            return $this->indexes[$lowerCaseName];
        } else {
            return null;
        }
    }

    /**
     * Add an index.
     *
     * Adds an index to the given column and returns it as an instance of {@see \Yana\Db\Ddl\Index}.
     *
     * @param   string  $indexName  optional name of index
     * @return  \Yana\Db\Ddl\Index
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException   if another index with the same name already exists
     * @throws  \Yana\Core\Exceptions\InvalidArgumenException  if index name is not valid
     */
    public function addIndex($indexName = "")
    {
        assert('is_string($indexName); // Wrong type for argument 1. String expected');

        $newIndex = new \Yana\Db\Ddl\Index($indexName, $this); // may throw InvalidArgumenException
        if (empty($indexName)) {
            $this->indexes[] = $newIndex;
        } elseif (!isset($this->indexes[$indexName])) {
            $this->indexes[$indexName] = $newIndex;
        } else {
            $message = "Another index by the name '$indexName' already exists.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($indexName);
            throw $exception;
        }

        return $newIndex;
    }

    /**
     * List all columns with unique-constraints.
     *
     * Returns a list of all {@see \Yana\Db\Ddl\Column}s, that define an unique constraint.
     *
     * @return  array
     */
    public function getUniqueConstraints()
    {
        $result = array();
        if (!empty($this->columns)) {
            
            foreach($this->columns as $column)
            {
                if ($column->isUnique()) {
                    $result[] = $column;
                }
            }
        }
        return $result;
    }

    /**
     * Get the database name associated with a table.
     *
     * Returns NULL if the schema name is unknown or an empty string if the schema name is undefined.
     *
     * @return  string
     * @ignore
     */
    public function getSchemaName()
    {
        if (isset($this->parent)) {
            return $this->parent->getName();
        } else {
            return null;
        }
    }

    /**
     * List all constraints.
     *
     * Retrieves all "constraint" entries that apply to the given DBMS and returns the results as a
     * numeric array.
     *
     * If no constraints have been defined the returned array will be empty.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     * @return  array
     */
    public function getConstraints($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('in_array($dbms, \Yana\Db\Ddl\Database::getSupportedDBMS()); // Unsupported DBMS');

        $constraints = array();
        foreach ($this->constraints as $constraint)
        {
            /* @var $constraint \Yana\Db\Ddl\Constraint */
            assert($constraint instanceof \Yana\Db\Ddl\Constraint);

            if ($constraint->getDBMS() === $dbms) {
                $constraints[] = $constraint;
            }
        }

        return $constraints;
    }

    /**
     * Get constraint.
     *
     * Returns the an instance of \Yana\Db\Ddl\Constraint, that matches the given name and target DBMS.
     * If no such instance is found the function returns NULL instead.
     *
     * @param   string  $name  constraint name
     * @param   string  $dbms  target DBMS, defaults to "generic"
     * @return  \Yana\Db\Ddl\Constraint
     */
    public function getConstraint($name, $dbms = "generic")
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        assert('is_string($dbms); // Wrong type for argument 2. String expected');
        assert('in_array($dbms, \Yana\Db\Ddl\Database::getSupportedDBMS()); // Unsupported DBMS');
        $dbms = strtolower($dbms);

        foreach ((array) $this->constraints as $constraint)
        {
            /* @var $constraint \Yana\Db\Ddl\Constraint */
            assert($constraint instanceof \Yana\Db\Ddl\Constraint);
            if ($constraint->getDBMS() === $dbms && $constraint->getName() === $name) {
                return $constraint;
            }
        }

        return null;
    }

    /**
     * Add constraint.
     *
     * Note: This function can't ensure that your codes makes sense.
     * So keep in mind that it is your job in the first place to ensure the constraint is valid!
     * The syntax depends on the target DBMS. For type "generic" the feature is emulated using PHP code.
     *
     * BE WARNED: As always - do NOT use this function with any unchecked user input.
     *
     * Note that the name should be unique for each DBMS.
     * You may however have several constraints with the same name for different DBMS.
     * The function will not check this!
     *
     * A constraint is a boolean expression that must evaluate to true at all times for the row to
     * be valid. The database should ensure that. For databases that don't have that feature, you
     * may use the vendor-independent type "generic" to simluate it.
     *
     * @param   string  $constraint  Code
     * @param   string  $name        optional constraint-name
     * @param   string  $dbms        target DBMS, defaults to "generic"
     */
    public function addConstraint($constraint, $name = "", $dbms = "generic")
    {
        assert('is_string($constraint); // Wrong type for argument 1. String expected');
        assert('is_string($name); // Wrong type for argument 2. String expected');
        assert('is_string($dbms); // Wrong type for argument 3. String expected');
        $dbms = strtolower($dbms);
        assert('in_array($dbms, \Yana\Db\Ddl\Database::getSupportedDBMS()); // Unsupported DBMS');
        $object = new \Yana\Db\Ddl\Constraint($name);
        $object->setDBMS($dbms);
        $object->setConstraint($constraint);
        $this->constraints[] = $object;
    }

    /**
     * Drops the list of all defined constraints.
     *
     * @return \Yana\Db\Ddl\Table 
     */
    public function dropConstraints()
    {
        $this->constraints = array();
        return $this;
    }

    /**#@+
     *
     * Retrieve the trigger code and return it.
     * The syntax of the code depends on the type of DBMS used.
     *
     * Before refers to triggers that fire BEFORE the statement is carried out.
     *
     * After refers to triggers that fire AFTER the statement or transaction has been successfully
     * carried out. It is not fired if the statement results in an error.
     *
     * Instead referes to triggers that fire INSTEAD of the statement. The statement is not
     * executed. This option is not supported by all DBMS. However: if it is not, you may emulate
     * this (with some limitations) by using PHP code.
     *
     * @return  string
     */

    /**
     * Get code triggered before insert.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     */
    public function getTriggerBeforeInsert($dbms = "generic")
    {
        return $this->_getTrigger($dbms, 0, 0);
    }

    /**
     * Get code triggered before update.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     */
    public function getTriggerBeforeUpdate($dbms = "generic")
    {
        return $this->_getTrigger($dbms, 0, 1);
    }

    /**
     * Get code triggered before delete.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     */
    public function getTriggerBeforeDelete($dbms = "generic")
    {
        return $this->_getTrigger($dbms, 0, 2);
    }

    /**
     * Get code triggered after insert.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     */
    public function getTriggerAfterInsert($dbms = "generic")
    {
        return $this->_getTrigger($dbms, 1, 0);
    }

    /**
     * Get code triggered after update.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     */
    public function getTriggerAfterUpdate($dbms = "generic")
    {
        return $this->_getTrigger($dbms, 1, 1);
    }

    /**
     * Get code triggered after delete.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     */
    public function getTriggerAfterDelete($dbms = "generic")
    {
        return $this->_getTrigger($dbms, 1, 2);
    }

    /**
     * Get code triggered instead of insert.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     */
    public function getTriggerInsteadInsert($dbms = "generic")
    {
        return $this->_getTrigger($dbms, 2, 0);
    }

    /**
     * Get code triggered instead of update.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     */
    public function getTriggerInsteadUpdate($dbms = "generic")
    {
        return $this->_getTrigger($dbms, 2, 1);
    }

    /**
     * Get code triggered instead of delete.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     */
    public function getTriggerInsteadDelete($dbms = "generic")
    {
        return $this->_getTrigger($dbms, 2, 2);
    }

    /**#@-*/
    /**#@+
     *
     * Set the trigger code that should be executed when the trigger is fired.
     *
     * Note: This function can't ensure that your codes makes sense.
     * So keep in mind that it is your job in the first place to ensure it is valid!
     * The syntax depends on the target DBMS. For type "generic" the feature is emulated using PHP
     * code.
     *
     * BE WARNED: As always - do NOT use this function with any unchecked user input.
     *
     * Note that some DBMS require that the code is a function call, not a function by itself.
     *
     * Before refers to triggers that fire BEFORE the statement is carried out.
     *
     * After refers to triggers that fire AFTER the statement or transaction has been successfully
     * carried out. It is not fired if the statement results in an error.
     *
     * Instead referes to triggers that fire INSTEAD of the statement. The statement is not
     * executed. This option is not supported by all DBMS. However: if it is not, you may emulate
     * this (with some limitations) by using PHP code.
     *
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     */

    /**
     * Set trigger before insert.
     *
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     * @return  \Yana\Db\Ddl\Trigger
     */
    public function setTriggerBeforeInsert($trigger, $dbms = "generic", $name = "")
    {
        return $this->_setTrigger($trigger, $dbms, $name, 0, 0);
    }

    /**
     * Set trigger before update.
     *
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     * @return  \Yana\Db\Ddl\Trigger
     */
    public function setTriggerBeforeUpdate($trigger, $dbms = "generic", $name = "")
    {
        return $this->_setTrigger($trigger, $dbms, $name, 0, 1);
    }

    /**
     * Set trigger before delete.
     *
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     * @return  \Yana\Db\Ddl\Trigger
     */
    public function setTriggerBeforeDelete($trigger, $dbms = "generic", $name = "")
    {
        return $this->_setTrigger($trigger, $dbms, $name, 0, 2);
    }

    /**
     * Set trigger after insert.
     *
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     * @return  \Yana\Db\Ddl\Trigger
     */
    public function setTriggerAfterInsert($trigger, $dbms = "generic", $name = "")
    {
        return $this->_setTrigger($trigger, $dbms, $name, 1, 0);
    }

    /**
     * set trigger after update
     *
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     * @return  \Yana\Db\Ddl\Trigger
     */
    public function setTriggerAfterUpdate($trigger, $dbms = "generic", $name = "")
    {
        return $this->_setTrigger($trigger, $dbms, $name, 1, 1);
    }

    /**
     * Set trigger after delete.
     *
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     * @return  \Yana\Db\Ddl\Trigger
     */
    public function setTriggerAfterDelete($trigger, $dbms = "generic", $name = "")
    {
        return $this->_setTrigger($trigger, $dbms, $name, 1, 2);
    }

    /**
     * Set trigger instead of insert.
     *
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     * @return  \Yana\Db\Ddl\Trigger
     */
    public function setTriggerInsteadInsert($trigger, $dbms = "generic", $name = "")
    {
        return $this->_setTrigger($trigger, $dbms, $name, 2, 0);
    }

    /**
     * Set trigger instead of update.
     *
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     * @return  \Yana\Db\Ddl\Trigger
     */
    public function setTriggerInsteadUpdate($trigger, $dbms = "generic", $name = "")
    {
        return $this->_setTrigger($trigger, $dbms, $name, 2, 1);
    }

    /**
     * Set trigger instead of delete.
     *
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     * @return  \Yana\Db\Ddl\Trigger
     */
    public function setTriggerInsteadDelete($trigger, $dbms = "generic", $name = "")
    {
        return $this->_setTrigger($trigger, $dbms, $name, 2, 2);
    }
    /**#@-*/

    /**
     * Get trigger code.
     *
     * @param   string  $dbms   target DBMS, defaults to "generic"
     * @param   int     $on     on
     * @param   int     $event  event
     * @return  string
     */
    private function _getTrigger($dbms, $on, $event)
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);

        foreach ((array) $this->triggers as $trigger)
        {
            /* @var $trigger \Yana\Db\Ddl\Trigger */
            assert($trigger instanceof \Yana\Db\Ddl\Trigger);

            switch (true)
            {
                case ($trigger->getDBMS() !== $dbms):
                case ($on === 0 && !$trigger->isBefore()):
                case ($on === 1 && !$trigger->isAfter()):
                case ($on === 2 && !$trigger->isInstead()):
                case ($event === 0 && !$trigger->isInsert()):
                case ($event === 1 && !$trigger->isUpdate()):
                case ($event === 2 && !$trigger->isDelete()):
                    continue;
                default:
                    return $trigger->getTrigger();
            }
        }

        return null;
    }

    /**
     * Set trigger code.
     *
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     * @param   int     $on       on
     * @param   int     $event    event
     * @return  \Yana\Db\Ddl\Trigger
     */
    private function _setTrigger($trigger, $dbms, $name, $on, $event)
    {
        assert('is_string($trigger); // Invalid argument $trigger: String expected');
        assert('is_string($dbms); // Invalid argument $dbms: String expected');
        assert('is_string($name); // Invalid argument $name: String expected');
        assert('is_int($on); // Invalid argument $on: Integer expected');
        assert('is_int($event); // Invalid argument $event: Integer expected');

        $dbms = strtolower($dbms);
        $object = new \Yana\Db\Ddl\Trigger($name);
        $object->setDBMS($dbms);
        switch ($on)
        {
            case 0:
                $object->setBefore();
            break;
            case 1:
                $object->setAfter();
            break;
            case 2:
                $object->setInstead();
            break;
        }
        switch ($event)
        {
            case 0:
                $object->setInsert();
            break;
            case 1:
                $object->setUpdate();
            break;
            case 2:
                $object->setDelete();
            break;
        }
        $object->setTrigger($trigger);
        $this->triggers[] = $object;
        return $object;
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
     * @return  array
     */
    public function getGrants()
    {
        assert('is_array($this->grants); // Member "grants" is expected to be an array.');
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
    }

    /**
     * Add rights management setting.
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
    public function addGrant($user = null, $role = null, $level = null)
    {
        assert('is_null($user) || is_string($user); // Invalid argument $user: String expected');
        assert('is_null($role) || is_string($role); // Invalid argument $role: String expected');
        assert('is_null($level) || is_int($level); // Invalid argument $level: Integer expected');
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
     * @param   \Yana\Db\Ddl\Grant  $grant    expected an grand object ( rights management)
     * @return  \Yana\Db\Ddl\Table 
     */
    public function setGrant(\Yana\Db\Ddl\Grant $grant)
    {
        $this->grants[] = $grant;
        return $this;
    }

    /**
     * Set primary/clustered index.
     *
     * Removes attribute clustered from previous clustered index.
     * Sets index as new clustered index.
     * Expects that the index is defined in the current table (not checked).
     *
     * Meant to be called from underlying function {@see \Yana\Db\Ddl\Index::setClustered()}.
     * Do not call directly.
     *
     * @param   \Yana\Db\Ddl\Index $index primary/clustered index
     * @return  \Yana\Db\Ddl\Table 
     */
    public function setPrimaryIndex(\Yana\Db\Ddl\Index $index)
    {
        if ($this->primaryIndex !== $index) {
            if ($this->primaryIndex instanceof \Yana\Db\Ddl\Index) {
                $this->primaryIndex->setClustered(false);
            }
            $this->primaryIndex = $index;
        }
        return $this;
    }

    /**
     * <<magic>> Returns a column with the given name.
     *
     * @param   string $name   name
     * @return  \Yana\Db\Ddl\Column
     */
    public function __get($name)
    {
        return $this->getColumn($name);
    }

    /**
     * Unserialize a XDDL-node to an object.
     *
     * Returns the unserialized object.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\Table
     * @throws  \Yana\Db\Ddl\NoNameException  when the name attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            $message = "Missing name attribute.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Ddl\NoNameException($message, $level);
        }
        $ddl = new self((string) $attributes['name'], $parent);
        $ddl->_unserializeFromXDDL($node);
        // columns
        foreach ($ddl->columns as $i => $column)
        {
            if (is_int($i)) {
                $name = $column->getName();
                $ddl->columns[$name] = $column;
                unset($ddl->columns[$i]);
            }
        }
        // indexes
        foreach ($ddl->indexes as $i => $index)
        {
            if ($index->isClustered()) {
                $ddl->primaryIndex = $index;
                break;
            }
        }
        return $ddl;
    }

}

?>