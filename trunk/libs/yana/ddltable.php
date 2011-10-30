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
 * database table structure
 *
 * This wrapper class represents the structure of a database
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLTable extends DDLNamedObject implements IsIncludableDDL
{
    /**#@+
     * @ignore
     * @access  protected
     */

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
        'grant'       => array('grants',      'array', 'DDLGrant'),
        'primarykey'  => array('primaryKey',  'nmtoken'),
        'foreign'     => array('foreignKeys', 'array', 'DDLForeignKey'),
        'trigger'     => array('triggers',    'array', 'DDLTrigger'),
        'constraint'  => array('constraints', 'array', 'DDLConstraint'),
        'declaration' => array('columns',     'DDLColumn'),
        'index'       => array('indexes',     'array', 'DDLIndex')
    );

    /** @var string          */ protected $description = null;
    /** @var string          */ protected $title = null;
    /** @var bool            */ protected $readonly = null;
    /** @var string          */ protected $inheritance = null;
    /** @var string          */ protected $primaryKey = null;
    /** @var DDLColumn[]     */ protected $columns = array();
    /** @var DDLForeignKey[] */ protected $foreignKeys = array();
    /** @var DDLIndex[]      */ protected $indexes = array();
    /** @var DDLGrant[]      */ protected $grants = array();
    /** @var DDLTrigger[]    */ protected $triggers = array();
    /** @var DDLConstraint[] */ protected $constraints = array();
    /** @var DDLDatabase     */ protected $parent = null;
    /** @var DDLIndex        */ protected $primaryIndex = null;

    /**#@-*/

    /**
     * Initialize instance.
     *
     * @param  string       $name    foreign key name
     * @param  DDLDatabase  $parent  parent database
     */
    public function __construct($name = "", DDLDatabase $parent = null)
    {
        parent::__construct($name);
        $this->parent = $parent;
    }

    /**
     * Get parent database.
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
     * @param   string  $title  some text
     * @return  DDLTable 
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
     * @access  public
     * @param   string  $description  new value of this property
     * @return  DDLTable 
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
     * You may set the table to be read-only to prevent any changes to it by setting this to
     * bool(true).
     *
     * @access  public
     * @param   bool  $isReadonly   new value of this property
     * @return  DDLTable
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
     * DDLColumn. If no column with the given name exists, the function returns
     * NULL instead.
     *
     * @access  public
     * @param   string  $name   column name
     * @return  DDLColumn
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
     * @access  public
     * @param   string  $type   datatype ('string', 'text', 'int', ...)
     * @return  array
     */
    public function getColumnsByType($type)
    {
        assert('is_string($type); // Wrong type for argument 1. String expected');
        assert('in_array($type, DDLColumn::getSupportedTypes()); // Undefined column type "' . $type . '". ');
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
     * and each value is and object of type {@see DDLColumn}.
     *
     * @access  public
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
     * @access  public
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
     * table as a numeric array of objects of type {@see DDLColumn}.
     *
     * @access  public
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
     * @access  public
     * @param   string  $columnName  name of column containing the foreign key
     * @return  string
     * @throws  \Yana\Core\Exceptions\NotFoundException  when column does not exist
     */
    public function getTableByForeignKey($columnName)
    {
        if (!$this->isColumn($columnName)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such column '$columnName'.", E_USER_WARNING);
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
     * @access  public
     * @param   string  $columnName  name of column containing the foreign key
     * @return  DDLColumn
     * @throws  \Yana\Core\Exceptions\NotFoundException  when column does not exist
     * @ignore
     */
    public function getColumnByForeignKey($columnName)
    {
        if (!$this->isColumn($columnName)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such column '$columnName'.", E_USER_WARNING);
        }
        if (! $this->parent instanceof DDLDatabase) {
            throw new \Yana\Core\Exceptions\NotFoundException("Target table is undefined.", E_USER_WARNING);
        }

        $columnName = mb_strtolower($columnName);
        /* @var $key DDLForeignKey */
        foreach($this->foreignKeys as $key)
        {
            $columns = $key->getColumns();
            if (isset($columns[$columnName])) {
                $table = $this->parent->getTable($key->getTargetTable());
                if (! $table instanceof DDLTable) {
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
     * @access  public
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
     * Adds a column of the given type and name and returns it as an instance of {@see DDLColumn}.
     *
     * @access  public
     * @param   string  $columnName  name of column
     * @param   string  $type        data-type
     * @return  DDLColumn
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException   when another column with the same name already exists
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException if the name is not valid
     */
    public function addColumn($columnName, $type)
    {
        assert('is_string($columnName); // Wrong type for argument 1. String expected');
        assert('in_array($type, DDLColumn::getSupportedTypes()); // Undefined column type "' . $type . '". ');
        $columnName = mb_strtolower($columnName);
        if (isset($this->columns[$columnName])) {
            $message = "Another column with the name '$columnName' already exists in table '{$this->getName()}'.";
            throw new \Yana\Core\Exceptions\AlreadyExistsException($message, E_USER_WARNING);
        } else {
            // may throw \Yana\Core\Exceptions\InvalidArgumentException
            $column = new DDLColumn($columnName, $this);
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
     * @access  public
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
     * @access  public
     * @param   bool  $hasProfileConstraint  profile constraint
     * @return  DDLTable 
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
     * @access  public
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
            throw new \Yana\Core\Exceptions\NotFoundException($message, E_USER_WARNING);
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
     * @access  public
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
     * @access  public
     * @param   bool  $hasVersionCheck  new value of this property
     * @param   bool  $lastModified     true: check for time_modified, false check for time_created
     * @return  DDLTable 
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
     * @access  public
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
     * @access  public
     * @param   bool  $hasAuthorLog  new value of this property
     * @param   bool  $lastModified  true: check for user_modified, false check for user_created
     * @return  DDLTable
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
     * The returned results will be objects of type {@see DDLForeignKey}.
     *
     * @access  public
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
     * {@see DDLForeignKey}. If no foreign key with the given name exists, the function returns NULL instead.
     *
     * @access  public
     * @param   string  $name   name of a foreign key
     * @return  DDLForeignKey
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
     * @access  public
     * @param   string  $table            name of target table
     * @param   string  $constraintName   optional name of foreign-key constraint
     * @return  DDLForeignKey
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

        $newForeignKey = new DDLForeignKey($constraintName, $this);
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
     * Returns NULL and issues an E_USER_WARNING if there is no primary key for $table.
     *
     * @access  public
     * @return  string
     */
    public function getPrimaryKey()
    {
        if (isset($this->primaryKey)) {
            return $this->primaryKey;
        } else {
            trigger_error("Table '{$this->name}' has no primary key declaration.", E_USER_WARNING);
            return null;
        }
    }

    /**
     * Set the primary key.
     *
     * Select $columnName as the primary key of the table.
     * Throws a NotFoundException, if the column does not exist.
     *
     * @access  public
     * @param   string  $columnName  name of column
     * @throws  \Yana\Core\Exceptions\NotFoundException  if column does not exist
     * @return  DDLTable 
     */
    public function setPrimaryKey($columnName)
    {
        assert('is_string($columnName); // Wrong type for argument 1. String expected');
        $name = mb_strtolower($columnName);
        if (isset($this->columns[$name])) {
            $this->primaryKey = $name;
        } else {
            $message = "No such column '$columnName' in table '{$this->getName()}'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, E_USER_WARNING);
        }
        return $this;
    }

    /**
     * Get parent table.
     *
     * Returns the name of the parent table as a string, or NULL if there is none.
     *
     * @access  public
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
     * @access  public
     * @param   string  $name  name of the parent table
     * @return  DDLTable 
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
     * Returns a list of all defined indexes on the table as a numeric array of objects of type {@see DDLIndex}.
     *
     * @access  public
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
     * Returns the index with the given name as an instance of {@see DDLIndex}, or NULL if it does not exist.
     *
     * @access  public
     * @param   string  $name  name of index
     * @return  DDLIndex
     */
    public function getIndex($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $name = mb_strtolower($name);
        if (isset($this->indexes[$name])) {
            return $this->indexes[$name];
        } else {
            return null;
        }
    }

    /**
     * Add an index.
     *
     * Adds an index to the given column and returns it as an instance of {@see DDLIndex}.
     *
     * @access  public
     * @param   string  $indexName  optional name of index
     * @return  DDLIndex
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException   if another index with the same name already exists
     * @throws  \Yana\Core\Exceptions\InvalidArgumenException  if index name is not valid
     */
    public function addIndex($indexName = "")
    {
        assert('is_string($indexName); // Wrong type for argument 1. String expected');

        $newDDLIndex = new DDLIndex($indexName, $this); // may throw InvalidArgumenException
        if (empty($indexName)) {
            $this->indexes[] = $newDDLIndex;
        } elseif (!isset($this->indexes[$indexName])) {
            $this->indexes[$indexName] = $newDDLIndex;
        } else {
            throw new \Yana\Core\Exceptions\AlreadyExistsException("Another index by the name '$indexName' already exists.");
        }

        return $newDDLIndex;
    }

    /**
     * List all columns with unique-constraints.
     *
     * Returns a list of all {@see DDLColumn}s, that define an unique constraint.
     *
     * @access  public
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
     * @access  public
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
     * @access  public
     * @param   string  $dbms  target DBMS, defaults to "generic"
     * @return  array
     */
    public function getConstraints($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('in_array($dbms, DDLDatabase::getSupportedDBMS()); // Unsupported DBMS');
        $constraints = array();
        if (!empty($this->constraints)) {
            foreach ($this->constraints as $constraint)
            {
                if ($constraint->getDBMS() === $dbms) {
                    $constraints[] = $constraint;
                }
            }
        }
        return $constraints;
    }

    /**
     * Get constraint.
     *
     * Returns the an instance of DDLConstraint, that matches the given name and target DBMS.
     * If no such instance is found the function returns NULL instead.
     *
     * @access  public
     * @param   string  $name  constraint name
     * @param   string  $dbms  target DBMS, defaults to "generic"
     * @return  DDLConstraint
     */
    public function getConstraint($name, $dbms = "generic")
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        assert('is_string($dbms); // Wrong type for argument 2. String expected');
        assert('in_array($dbms, DDLDatabase::getSupportedDBMS()); // Unsupported DBMS');
        $dbms = strtolower($dbms);
        if (!empty($this->constraints)) {
            foreach ($this->constraints as $constraint)
            {
                if ($constraint->getDBMS() === $dbms && $constraint->getName() === $name) {
                    return $constraint;
                }
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
     * @access  public
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
        assert('in_array($dbms, DDLDatabase::getSupportedDBMS()); // Unsupported DBMS');
        $object = new DDLConstraint($name);
        $object->setDBMS($dbms);
        $object->setConstraint($constraint);
        $this->constraints[] = $object;
    }

    /**
     * Drops the list of all defined constraints.
     *
     * @access  public
     */
    public function dropConstraints()
    {
        $this->constraints = array();
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
     * @access  public
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
     * @access  public
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
     * @return  DDLTrigger
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
     * @return  DDLTrigger
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
     * @return  DDLTrigger
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
     * @return  DDLTrigger
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
     * @return  DDLTrigger
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
     * @return  DDLTrigger
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
     * @return  DDLTrigger
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
     * @return  DDLTrigger
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
     * @return  DDLTrigger
     */
    public function setTriggerInsteadDelete($trigger, $dbms = "generic", $name = "")
    {
        return $this->_setTrigger($trigger, $dbms, $name, 2, 2);
    }
    /**#@-*/

    /**
     * Get trigger code.
     *
     * @access  private
     * @param   string  $dbms   target DBMS, defaults to "generic"
     * @param   int     $on     on
     * @param   int     $event  event
     * @return  string
     */
    private function _getTrigger($dbms, $on, $event)
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        if (!empty($this->triggers)) {
            foreach ($this->triggers as $trigger)
            {
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
                    break;
                    default:
                        return $trigger->getTrigger();
                    break;
                }
            }
        }
        return null;
    }

    /**
     * Set trigger code.
     *
     * @access  private
     * @param   string  $trigger  code (possibly a function call)
     * @param   string  $dbms     target DBMS
     * @param   string  $name     optional trigger name
     * @param   int     $on       on
     * @param   int     $event    event
     * @return  DDLTrigger
     */
    private function _setTrigger($trigger, $dbms, $name, $on, $event)
    {
        assert('is_string($trigger); // Wrong type for argument 1. String expected');
        assert('is_string($name); // Wrong type for argument 2. String expected');
        assert('is_string($dbms); // Wrong type for argument 3. String expected');
        $dbms = strtolower($dbms);
        $object = new DDLTrigger($name);
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
     * Returns an array of DDLGrant objects.
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
        return $this->grants;
    }

    /**
     * Drop rights management settings.
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
        $this->grants = array();
    }

    /**
     * Add rights management setting.
     *
     * {@link DDLGrant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the form settings by using the given
     * options and returns it as an DDLGrant object.
     *
     * @access  public
     * @param   string  $user   user group
     * @param   string  $role   user role
     * @param   int     $level  security level
     * @return  DDLGrant
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when $level is out of range [0,100]
     */
    public function addGrant($user = null, $role = null, $level = null)
    {
        assert('is_string($user); // Wrong type for argument 1. String expected');
        assert('is_string($role); // Wrong type for argument 2. String expected');
        assert('is_null($level) || is_int($level); // Wrong type for argument 3. Integer expected');
        $grant = new DDLGrant();
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
     * {@link DDLGrant}s control the access permissions granted to the user.
     *
     * This function adds a new grant to the configuration.
     *
     * @access  public
     * @param   DDLGrant  $grant    expected an grand object ( rights management)
     * @return  DDLTable 
     */
    public function setGrant(DDLGrant $grant)
    {
        $this->grants[] = $grant;
        return $this;
    }

    /**
     * Validate a row against database schema.
     *
     * The argument $row is expected to be an associative array of values, representing
     * a row that should be inserted or updated in the table. The keys of the array $row are
     * expected to be the lowercased column names.
     *
     * Returns bool(true) if $row is valid and bool(false) otherwise.
     *
     * @access  public
     * @param   array   $row       values of the inserted/updated row
     * @param   string  $dbms      target DBMS, defaults to "generic"
     * @param   bool    $isInsert  type of operation (true = insert, false = update)
     * @param   array   &$files    list of modified or inserted columns of type file or image
     * @return  array
     * @throws  \Yana\Core\Exceptions\NotWriteableException  if a target column or table is not writeable
     * @throws  InvalidValueWarning                          if a given value is missing or not valid
     */
    public function sanitizeRow(array $row, $dbms = "generic", $isInsert = true, array &$files = array())
    {
        assert('is_bool($isInsert); // Wrong type for argument 2. Boolean expected');
        /* @var $column DDLColumn */
        foreach ($this->getColumns() as $column)
        {
            $columnName = $column->getName();
            /*
             * error - not writeable
             */
            if (!$isInsert && $column->isReadonly() && isset($row[$columnName])) {
                throw new \Yana\Core\Exceptions\NotWriteableException("Database is readonly. " .
                    "Update operation on table '{$this->getName()}' aborted.");
            }
            /*
             * valid - value may be empty for update-queries
             */
            if (!$isInsert && !isset($row[$columnName])) {
                continue;
            }
            /*
             * 3) value is not set (and requires closer investigation)
             */
            if (!isset($row[$columnName]) || $row[$columnName] === "") {

                $default = $column->getAutoValue($dbms);

                /*
                 * autofill column
                 */
                if (!is_null($default)) {
                    $row[$columnName] = $default;
                    continue;
                } elseif ($column->isAutoIncrement()) {
                    continue;
                }

                /*
                 * error - value is missing
                 */
                if (!$column->isNullable()) {
                    $title = $column->getTitle();
                    if (empty($title)) {
                        $title = $column->getName();
                    }
                    $warning = new MissingFieldWarning();
                    throw $warning->setField($title);
                } else {
                    $row[$columnName] = null;
                }
            /*
             * 4) this input is valid - move to next
             */
            } else {
                if (isset($row[$columnName])) {
                    $row[$columnName] = $column->sanitizeValue($row[$columnName], $dbms, $files);
                }
                continue;
            } // end if
        } // end for

        return $row;
    }

    /**
     * Set primary/clustered index.
     *
     * Removes attribute clustered from previous clustered index.
     * Sets index as new clustered index.
     * Expects that the index is defined in the current table (not checked).
     *
     * Meant to be called from underlying function {@see DDLIndex::setClustered()}.
     * Do not call directly.
     *
     * @access  public
     * @param   DDLIndex $index primary/clustered index
     * @return  DDLTable 
     */
    public function setPrimaryIndex(DDLIndex $index)
    {
        if ($this->primaryIndex !== $index) {
            if (isset($this->primaryIndex)) {
                $this->primaryIndex->setClustered(false);
            }
            $this->primaryIndex = $index;
        }
        return $this;
    }

    /**
     * <<magic>> Returns a column with the given name.
     *
     * @access  public
     * @param   string $name   name
     * @return  DDLColumn
     */
    public function __get($name)
    {
        return $this->getColumn($name);
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
     * @return  DDLTable
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the name attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
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