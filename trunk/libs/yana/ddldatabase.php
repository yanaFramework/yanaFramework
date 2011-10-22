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
 * database structure
 *
 * This wrapper class represents the structure of a database.
 *
 * "Database" is the root level element of a XDDL document.
 * It may contain several child elements.
 * Those may be seperated to 5 basic groups: Tables, Views, Forms, Functions and
 * Change-logs.
 *
 * The database element defines basic properties of the database itself, as well
 * as information for the client and applications that may connect with the
 * database.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLDatabase extends DDLObject
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  DDLDatabase[]
     */
    protected static $instances = array();

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "database";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'       => array('name',       'nmtoken'),
        'charset'    => array('charset',    'string'),
        'datasource' => array('datasource', 'string'),
        'readonly'   => array('readonly',   'bool'),
        'title'      => array('title',      'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description'    => array('description',    'string'),
        'include'        => array('includes',       'array'),
        'table'          => array('tables',         'array', 'DDLTable'),
        'view'           => array('views',          'array', 'DDLView'),
        'form'           => array('forms',          'array', 'DDLForm'),
        'function'       => array('functions',      'array', 'DDLFunction'),
        'sequence'       => array('sequences',      'array', 'DDLSequence'),
        'initialization' => array('initialization', 'array', 'DDLDatabaseInit'),
        'changelog'      => array('changelog',      'DDLChangeLog')
    );

    /** @var string            */ protected $charset = null;
    /** @var string            */ protected $datasource = null;
    /** @var bool              */ protected $readonly = null;
    /** @var string            */ protected $description = null;
    /** @var string            */ protected $title = null;
    /** @var DDLChangeLog      */ protected $changelog = null;
    /** @var array             */ protected $includes = array();
    /** @var DDLTable[]        */ protected $tables = array();
    /** @var DDLView[]         */ protected $views = array();
    /** @var DDLForm[]         */ protected $forms = array();
    /** @var DDLFunction[]     */ protected $functions = array();
    /** @var DDLSequence[]     */ protected $sequences = array();
    /** @var DDLDatabaseInit[] */ protected $initialization = array();
    /** @var bool              */ protected $modified = false;

    /** @var string            */ protected $path = null;
    /** @var int               */ protected $lastModified = 0;

    /**#@-*/

    /**
     * list of loaded includes
     *
     * An array of boolean values.
     * The keys are pathnames of files that have already been included.
     * The values are ignored.
     *
     * @see     DDLDatabase::loadIncludes()
     * @access  private
     * @var     array
     * @ignore
     */
    private $_loadedIncludes = array();

    /**
     * Initialize instance.
     *
     * @access  public
     * @param   string  $name  database name
     * @param   string  $path  file path
     * @throws  InvalidArgumentException  when given name is invalid
     */
    public function __construct($name = "", $path = "")
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        assert('is_string($path); // Invalid argument $path: string expected');
        assert('empty($path) || is_file($path); // Invalid argument $path. File expected');
        parent::__construct($name);
        $this->changelog = new DDLChangeLog($this);
        // save path information
        if (!empty($path)) {
            $this->path = $path;
        }
        // move new instance to cache
        if (!empty($name)) {
            $this->name = $name;
        } elseif (empty($name) && !empty($path)) {
            $this->name = DDL::getNameFromPath($path);
        }
        if (!empty($path)) {
            self::$instances[$path] = $this;
        }
    }

    /**
     * Get the path to the directory, where the database's source file is stored.
     *
     * @return string
     * @throws NotFoundException if the source file is not defined (e.g. the file is unsaved)
     */
    private function _getDirectory()
    {
        if (empty($this->path)) {
            throw new NotFoundException('No directory defined for this database');
        }
        $directory = dirname($this->path) . '/';
        assert('is_dir($directory); // Database base-directory not found');
        return $directory;
    }

    /**
     * Check if database structure has been modified.
     *
     * Returns bool(true) if the database has been marked as modified and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     * @ignore
     */
    public function isModified()
    {
        if ($this->modified) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Mark database as modified.
     *
     * Marks that the structure of the database has been modified.
     * This is to be used as an indicator for any scripts, that the database definition contains
     * unsaved changes and the database may need to be updated to comply with the given definition.
     *
     * @access  public
     * @param   bool  $isModified  new value of this property
     * @return  DDLDatabase
     * @ignore
     */
    public function setModified($isModified = true)
    {
        assert('is_bool($isModified); // Wrong type for argument 1. Boolean expected');
        if ($isModified) {
            // clear the cache (so new instances won't copy the modified version)
            if (isset($_SESSION[__CLASS__ . "/" . $this->name])) {
                unset($_SESSION[__CLASS__ . "/" . $this->name]);
            }
            if (isset(self::$instances[$this->path])) {
                unset(self::$instances[$this->path]);
            }
            $this->modified = true;
        } else {
            $this->modified = false;
        }
        return $this;
    }

    /**
     * Get list of supported DBMS.
     *
     * Returns a list with all supported DBMS as a numeric array.
     *
     * @access  public
     * @static
     * @return  array
     * @ignore
     */
    public static function getSupportedDBMS()
    {
        return array('generic','db2','dbase','frontbase','informix','interbase','msaccess','mssql',
            'mysql','oracle','postgresql','sybase','sqlite');
    }

    /**
     * Get list of include files.
     *
     * Returns the list of included XDDL-files.
     * Database definitions may be shared among others.
     * E.g. this may be necessary if you wish to create a reference to another
     * table, which was defined elsewhere.
     *
     * The list may contain either filenames, or identifiers, which can be
     * converted to filenames by the application.
     *
     * If no other file is included, an empty array is returned.
     *
     * @access  public
     * @return  array
     */
    public function getIncludes()
    {
        if (is_array($this->includes)) {
            return $this->includes;
        } else {
            return array();
        }
    }

    /**
     * Set list of include files.
     *
     * Database definitions may be shared among others.
     * E.g. this may be necessary if you wish to create a reference to another
     * table, which was defined elsewhere.
     *
     * The list may contain either filenames, or identifiers, which can be
     * converted to filenames by the application.
     *
     * Note that you should only include database definitions that use the same
     * data-source.
     *
     * Also note that adding definitions will not automatically resolve and
     * include the files.
     *
     * @access  public
     * @param   array  $includes  list of files to include
     * @see     DDL::getDataSource()
     * @return  DDLDatabase
     */
    public function setIncludes(array $includes = array())
    {
        $this->includes = $includes;
        return $this;
    }

    /**
     * Add include file name.
     *
     * Database definitions may be shared among others.
     * E.g. this may be necessary if you wish to create a reference to another
     * table, which was defined elsewhere.
     *
     * The parameter $include may either be a filename, or identifier, which can
     * be converted to a filename by the application.
     *
     * Note that you should only include database definitions that use the same
     * data-source.
     *
     * Also note that adding a definition does not automatically resolve and
     * include the file.
     *
     * @access  public
     * @param   string  $include  file to include
     * @see     DDL::getDataSource()
     */
    public function addInclude($include)
    {
        assert('is_string($include); // Wrong type for argument 1. String expected');
        $this->includes[] = "$include";
    }

    /**
     * Load included DDL files.
     *
     * To check if a table is included from another file, use the following code:
     * <code>
     * $table = $database->myTable;
     * if ($table->getParent() === $database) {
     *     print "Table was defined here.";
     * } else {
     *     $dbName = $table->getParent()->getName();
     *     print "Table is included from database: $dbName";
     * }
     * </code>
     *
     * @access  public
     * @throws  NotFoundException  when an included file was not found
     * @name    DDLDatabase::loadIncludes()
     */
    public function loadIncludes()
    {
        try {
            $baseDirectory = $this->_getDirectory();
        } catch (NotFoundException $e) {
            return; // if the base-directory is not defined, we just skip the loading process
        }
        // load each file
        foreach ($this->includes as $databaseName)
        {
            $path = $baseDirectory . $databaseName . DDL::$extension;

            // check if file is already include (include just once)
            if ($path === $this->path) {
                continue;
            }
            if (!empty($this->_loadedIncludes[$path])) {
                continue;
            }
            $this->_loadedIncludes[$path] = true;

            // check if file is already cached
            if (!isset(self::$instances[$path])) {
                if (!is_file($path)) {
                    $message = "Included XDDL file '{$databaseName}' not found. " .
                        "Defined in file '" . $this->getName() . "'.";
                    throw new NotFoundException($message, E_USER_ERROR);
                }
                $xddl = new XDDL($path);
                self::$instances[$path] = $xddl->toDatabase();
                unset($xddl);
            }
            // get file
            $database = self::$instances[$path];

            // get properties of included database
            $dataSource = $database->getDataSource();

            // include tables
            foreach ($database->getTables() as $object)
            {
                $name = $object->getName();
                if (!isset($this->tables[$name])) {
                    $this->tables[$name] = $object;
                }
            }
            unset($name, $object);

            // include views
            foreach ($database->getViews() as $object)
            {
                $name = $object->getName();
                if (!isset($this->views[$name])) {
                    $this->views[$name] = $object;
                }
            }
            unset($name, $object);

            // include forms
            foreach ($database->getForms() as $object)
            {
                $name = $object->getName();
                if (!isset($this->forms[$name])) {
                    $this->forms[$name] = $object;
                }
            }
            unset($name, $object);

            // include functions
            foreach ($database->getFunctions() as $object)
            {
                $name = $object->getName();
                if (!isset($this->functions[$name])) {
                    $this->functions[$name] = $object;
                }
            }
            unset($name, $object);

            // include sequences
            foreach ($database->getSequences() as $object)
            {
                $name = $object->getName();
                if (!isset($this->sequences[$name])) {
                    $this->sequences[$name] = $object;
                }
            }
            unset($name, $object);
        } // end foreach
    }

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
     * @return  DDLDatabase
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
     * @param   string  $title title for display in UI
     * @return  DDLDatabase
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
     * Get database charset.
     *
     * The charset may only be set upon creation of the database.
     * If you decide to use an existing database, please note, that the charset
     * which is actually used might be another.
     *
     * This information is optional. The default is DBMS dependent.
     * If no charset is specified, the function will return NULL instead.
     *
     * @access  public
     * @return  string
     */
    public function getCharset()
    {
        if (is_string($this->charset)) {
            return $this->charset;
        } else {
            return null;
        }
    }

    /**
     * Set database charset.
     *
     * The charset may only be set upon creation of the database.
     * If you decide to use an existing database, please note, that the charset
     * which is actually used might be another.
     *
     * Typical charsets are: utf8, ascii, latin1, iso-8859-1.
     * Note that some DBMS have different writings of the same charset, or may
     * support charsets that other DBMS won't.
     *
     * @access  public
     * @param   string  $charset  database charset
     * @return  DDLDatabase
     */
    public function setCharset($charset = "")
    {
        assert('is_string($charset); // Wrong type for argument 1. String expected');
        if (empty($charset)) {
            $this->charset = null;
        } else {
            $this->charset = "$charset";
        }
        return $this;
    }

    /**
     * Get data-source name.
     *
     * Returns the name of the data-source.
     *
     * Note that this information is optional. If there is none, the function
     * will return NULL instead.
     *
     * The interpretation of the datasource depends on the implementation.
     * In general it is an identifier for a particular set of connection
     * parameters to a specific database. This may either be a JDBC data-source,
     * for Java programmers, an ODBC data-source for C# programmers, or any
     * other named data-source for any other language.
     *
     * For the Yana Framework it is a named data-source. You may set up named
     * database-connection via the administration panel.
     *
     * @access  public
     * @return  string
     */
    public function getDataSource()
    {
        if (is_string($this->datasource)) {
            return $this->datasource;
        } else {
            return null;
        }
    }

    /**
     * Set data-source name.
     *
     * Note that this information is optional. To reset it, leave the parameter
     * $dataSource empty.
     *
     * Note that you should only include database definitions that use the same
     * data-source.
     *
     * @access  public
     * @param   string  $dataSource  data-source name
     * @return  DDLDatabase
     * @see     DDL::getDataSource()
     */
    public function setDataSource($dataSource = "")
    {
        assert('is_string($dataSource); // Wrong type for argument 1. String expected');
        if (empty($dataSource)) {
            $this->datasource = null;
        } else {
            $this->datasource = "$dataSource";
        }
        return $this;
    }

    /**
     * Check whether the dbo has read-only access.
     *
     * Returns bool(true) if the database is read-only and bool(false)
     * otherwise.
     *
     * You may set the database to be read-only to prevent any changes to it.
     * Use this if you wish to create a database viewer, or CD-ROM application.
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
     * You may set the database to be read-only to prevent any changes to it.
     * Use this if you wish to create a database viewer, or CD-ROM application.
     *
     * @access  public
     * @param   bool  $isReadonly   new value of this property
     * @return  DDLDatabase
     */
    public function setReadonly($isReadonly = false)
    {
        assert('is_bool($isReadonly); // Wrong type for argument 1. Boolean expected');
        $this->readonly = (bool) $isReadonly;
        return $this;
    }

    /**
     * Get table definition.
     *
     * Returns the table definition with the name $name as an instance of
     * DDLTable. If no table with the given name exists, the function returns
     * NULL instead.
     *
     * @access  public
     * @param   string  $name   table name
     * @return  DDLTable
     */
    public function getTable($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->tables[$name])) {
            return $this->tables[$name];
        } else {
            return null;
        }
    }

    /**
     * Add table definition.
     *
     * Adds a new table item to the database definition and returns the table
     * definition as an instance of DDLTable.
     *
     * If another table with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and
     * '_'. Otherwise an InvalidArgumentException is thrown.
     *
     * @access  public
     * @param   string  $name   set name for table
     * @return  DDLTable
     * @throws  AlreadyExistsException    if another table with the same name is already defined
     * @throws  InvalidArgumentException  if given an invalid table name
     */
    public function addTable($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->tables[$name])) {
            throw new AlreadyExistsException("Another table with the name '$name' is already defined.");

        } else {
            $this->tables[$name] = new DDLTable($name, $this);
            return $this->tables[$name];
        }
    }

    /**
     * List all tables by definition.
     *
     * Returns a list of table definitions, where each element is a DDLTable
     * object.
     * If no tables are defined, the list is empty.
     *
     * Important note! You can NOT add a new table to the database by adding a
     * new item to the list. Use the function {see DDL::addTable} instead.
     *
     * @access  public
     * @return  array
     */
    public function getTables()
    {
        assert('is_array($this->tables); // member "tables" is expected to be an array');
        return $this->tables;
    }

    /**
     * List all tables by name.
     *
     * Returns a numeric array with the names of all registered tables.
     *
     * @access  public
     * @return  array
     */
    public function getTableNames()
    {
        assert('is_array($this->tables); // member "tables" is expected to be an array');
        return array_keys($this->tables);
    }

    /**
     * Get view definition.
     *
     * Returns the view definition with the name $name as an instance of
     * DDLView. If no view with the given name exists, the function returns
     * NULL instead.
     *
     * @access  public
     * @param   string  $name   view name
     * @return  DDLView
     */
    public function getView($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->views[$name])) {
            return $this->views[$name];
        } else {
            return null;
        }
    }

    /**
     * Add view definition.
     *
     * Adds a new view item to the database definition and returns the view
     * definition as an instance of DDLView.
     *
     * If another view with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and
     * '_'. Otherwise an InvalidArgumentException is thrown.
     *
     * @access  public
     * @param   string  $name   name for view
     * @return  DDLView
     * @throws  AlreadyExistsException    if another view with the same name is already defined
     * @throws  InvalidArgumentException  if given an invalid view name
     */
    public function addView($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->views[$name])) {
            throw new AlreadyExistsException("Another view with the name '$name' is already defined.");

        } else {
            $this->views[$name] = new DDLView($name, $this);
            return $this->views[$name];
        }
    }

    /**
     * List all views by definition.
     *
     * Returns a list of view definitions, where each element is a DDLView object.
     * If no views are defined, the list is empty.
     *
     * Important note! You can NOT add a new view to the database by adding a
     * new item to the list. Use the function {see DDL::addView} instead.
     *
     * @access  public
     * @return  array
     */
    public function getViews()
    {
        assert('is_array($this->views); // member "views" is expected to be an array');
        return $this->views;
    }

    /**
     * List all views by name.
     *
     * Returns a numeric array with the names of all registered views.
     *
     * @access  public
     * @return  array
     */
    public function getViewNames()
    {
        assert('is_array($this->views); // member "views" is expected to be an array');
        return array_keys($this->views);
    }

    /**
     * Get function definition.
     *
     * Returns the function definition with the name $name as an instance of
     * DDLFunction. If no function with the given name exists, NULL is returned
     * instead.
     *
     * @access  public
     * @param   string  $name   name of expected function
     * @return  DDLFunction
     */
    public function getFunction($name)
    {
        $name = mb_strtolower($name);
        if (isset($this->functions[$name])) {
            return $this->functions[$name];
        } else {
            return null;
        }
    }

    /**
     * Add function definition.
     *
     * Adds a new function item to the database definition and returns the
     * function definition as an instance of DDLFunction.
     *
     * If another function with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and
     * '_'. Otherwise an InvalidArgumentException is thrown.
     *
     * @access  public
     * @param   string  $name  name for the new function
     * @return  DDLFunction
     * @throws  AlreadyExistsException    if another function with the same name is already defined
     * @throws  InvalidArgumentException  if given an invalid name
     */
    public function addFunction($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->functions[$name])) {
            throw new AlreadyExistsException("Another function with the name '$name' is already defined.");

        } else {
            $this->functions[$name] = new DDLFunction($name);
            return $this->functions[$name];
        }
    }

    /**
     * List all functions by definition.
     *
     * Returns a list of function definitions, where each element is a
     * DDLFunction object.
     * If no functions are defined, the list is empty.
     *
     * Important note! You can NOT add a new function to the database by adding
     * a new item to the list. Use the function {see DDL::addFunction} instead.
     *
     * @access  public
     * @return  array
     */
    public function getFunctions()
    {
        assert('is_array($this->functions); // $this->functions is expected to be an array');
        return $this->functions;
    }

    /**
     * List all functions by name.
     *
     * Returns a numeric array with the names of all registered functions.
     *
     * @access  public
     * @return  array
     */
    public function getFunctionNames()
    {
        assert('is_array($this->functions); // member "functions" is expected to be an array');
        return array_keys($this->functions);
    }

    /**
     * Get sequence definition.
     *
     * Returns the sequence definition with the name $name as an instance of
     * DDLSequence. If no sequence with the given name exists, NULL is returned
     * instead.
     *
     * @access  public
     * @param   string  $name  name of the expected sequence
     * @return  DDLSequence
     */
    public function getSequence($name)
    {
        $name = mb_strtolower($name);
        if (isset($this->sequences[$name])) {
            return $this->sequences[$name];
        } else {
            return null;
        }
    }

    /**
     * Add sequence definition.
     *
     * Adds a new sequence item to the database definition and returns the
     * sequence definition as an instance of DDLSequence.
     *
     * If another sequence with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and
     * '_'. Otherwise an InvalidArgumentException is thrown.
     *
     * @access  public
     * @param   string  $name   name for the new sequence
     * @return  DDLSequence
     * @throws  AlreadyExistsException    if another sequence with the same name is already defined
     * @throws  InvalidArgumentException  if given an invalid name
     */
    public function addSequence($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->sequences[$name])) {
            throw new AlreadyExistsException("Another sequence with the name '$name' is already defined.");

        } else {
            $this->sequences[$name] = new DDLSequence($name);
            return $this->sequences[$name];
        }
    }

    /**
     * List all sequences by definition.
     *
     * Returns a list of sequence definitions, where each element is a
     * DDLSequence object.
     * If no sequences are defined, the list is empty.
     *
     * Important note! You can NOT add a new sequence to the database by adding
     * a new item to the list. Use the function {see DDL::addSequence} instead.
     *
     * @access  public
     * @return  array
     */
    public function getSequences()
    {
        assert('is_array($this->sequences); // member "sequences" is expected to be an array');
        return $this->sequences;
    }

    /**
     * List all sequences by name.
     *
     * Returns a numeric array with the names of all registered sequences.
     *
     * @access  public
     * @return  array
     */
    public function getSequenceNames()
    {
        assert('is_array($this->sequences); // member "sequences" is expected to be an array');
        return array_keys($this->sequences);
    }

    /**
     * List sql for database-initialization.
     *
     * Returns an ordered list of all initialization SQL statements for the given DBMS.
     * If no DBMS is given, it defaults to "generic".
     *
     * @access  public
     * @param   string  $dbms  target database-management-system
     * @return  array
     */
    public function getInit($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('in_array($dbms, DDLDatabase::getSupportedDBMS()); // Unsupported DBMS');
        if (empty($this->initialization)) {
            return array();
        } else {
            $initialization = array();
            foreach ($this->initialization as $entry)
            {
                // target DBMS does not match
                $initDbms = $entry->getDBMS();
                if ($initDbms !== 'generic' && $initDbms !== $dbms) {
                    continue;

                } else {
                    $initialization[] = $entry->getSQL();
                }
            }
            return $initialization;
        }
    }

    /**
     * Drop database-initialization.
     *
     * Removes all previously set SQL-statements for initialization of the
     * database.
     *
     * @access  public
     */
    public function dropInit()
    {
        $this->initialization = array();
    }

    /**
     * Remove a table from the database definition.
     *
     * Note: the object is NOT deleted. Only the definition is lost.
     *
     * @access  public
     * @param   string  $name   table name
     * @throws  NotFoundException
     */
    public function dropTable($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->tables[$name])) {
            $this->tables[$name] = null;
        } else {
            throw new NotFoundException("No such table '$name'.");
        }
    }

    /**
     * Remove a view from the database definition.
     *
     * Note: the object is NOT deleted. Only the definition is lost.
     *
     * @access  public
     * @param   string  $name   view name
     * @throws  NotFoundException
     */
    public function dropView($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->views[$name])) {
            $this->views[$name] = null;
        } else {
            throw new NotFoundException("No such view '$name'.");
        }
    }

    /**
     * Remove a form from the database definition.
     *
     * Note: the object is NOT deleted. Only the definition is lost.
     *
     * @access  public
     * @param   string  $name   form name
     * @throws  NotFoundException
     */
    public function dropForm($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->forms[$name])) {
            $this->forms[$name] = null;
        } else {
            throw new NotFoundException("No such form '$name'.");
        }
    }

    /**
     * Remove a form function the database definition.
     *
     * Note: the object is NOT deleted. Only the definition is lost.
     *
     * @access  public
     * @param   string  $name   function name
     * @throws  NotFoundException
     */
    public function dropFunction($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->functions[$name])) {
            $this->functions[$name] = null;
        } else {
            throw new NotFoundException("No such function '$name'.");
        }
    }

    /**
     * Remove a sequence function the database definition.
     *
     * Note: the object is NOT deleted. Only the definition is lost.
     *
     * @access  public
     * @param   string  $name   sequence name
     * @throws  NotFoundException
     */
    public function dropSequence($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->sequences[$name])) {
            $this->sequences[$name] = null;
        } else {
            throw new NotFoundException("No such sequence '$name'.");
        }
    }

    /**
     * Add sql-initialization.
     *
     * Appends the SQL-statement for initialization of the database to the end
     * of the statements-list.
     * You may limit the statement to a certain DBMS if you provide it's name as
     * the second parameter (e.g. "mysql"). If you set the parameter to
     * "generic" or leave it off, then the SQL-statement will be carried out for
     * any DBMS.
     *
     * @access  public
     * @param   string  $sql   SQL statement
     * @param   string  $dbms  target database-management-system
     */
    public function addInit($sql, $dbms = "generic")
    {
        $dbms = strtolower($dbms);
        assert('is_string($dbms); // Wrong type for argument 2. String expected');
        assert('preg_match("/^db2|dbase|frontbase|informix|interbase|msaccess|mssql|mysql|oracle|postgresql|sybase|' .
            'sqlite|generic$/s", $dbms); // Unsupported DBMS');
        $init = new DDLDatabaseInit();
        $init->setDBMS($dbms);
        $init->setSQL($sql);
        $this->initialization[] = $init;
    }

    /**
     * Check if table exists.
     *
     * Returns bool(true) if a table with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $name  new value of this property
     * @return  bool
     */
    public function isTable($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        return isset($this->tables[$name]);
    }

    /**
     * Check if view exists.
     *
     * Returns bool(true) if a view with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $name   new value of this property
     * @return  bool
     */
    public function isView($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        return isset($this->views[$name]);
    }

    /**
     * Check if function exists.
     *
     * Returns bool(true) if a function with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $name   new value of this property
     * @return  bool
     */
    public function isFunction($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        return isset($this->functions[$name]);
    }

    /**
     * Check if sequence exists.
     *
     * Returns bool(true) if a sequence with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $name   new value of this property
     * @return  bool
     */
    public function isSequence($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        return isset($this->sequences[$name]);
    }

    /**
     * Check if form exists.
     *
     * Returns bool(true) if a form with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $name   new value of this property
     * @return  bool
     */
    public function isForm($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        return isset($this->forms[$name]);
    }

    /**
     * Get form by name.
     *
     * Returns the form definition with the name $name as an instance of
     * DDLForm. If no form with the given name exists, NULL is returned
     * instead.
     *
     * @access  public
     * @param   string  $name  form name
     * @return  DDLForm
     */
    public function getForm($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->forms[$name])) {
            return $this->forms[$name];
        } else {
            return null;
        }
    }

    /**
     * Add form.
     *
     * Adds a new form item to the database definition and returns the
     * sequence definition as an instance of DDLForm.
     *
     * If another form with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and
     * '_'. Otherwise an InvalidArgumentException is thrown.
     *
     * @access  public
     * @param   string  $name  new Form name
     * @return  DDLForm
     * @throws  AlreadyExistsException  if another form with the same name is already defined
     */
    public function addForm($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        if (isset($this->forms[$name])) {
            $message = "Another form with the name '$name' already exists in database '{$this->getName()}'.";
            throw new AlreadyExistsException($message, E_USER_WARNING);
        }
        // add element to list of defined forms
        $this->forms[$name] = new DDLForm($name, $this);
        return $this->forms[$name];
    }

    /**
     * Get list of forms.
     *
     * Returns an associative array of all forms, where the array keys are the
     * names and the values are instances of DDLForm.
     *
     * If no form has been defined (yet), an empty array is returned.
     *
     * Important note! You can NOT add a new form to the database by adding
     * a new item to the list. Use the function {see DDL::addForm} instead.
     *
     * @access  public
     * @return  array
     */
    public function getForms()
    {
        assert('is_array($this->forms); // member "forms" is expected to be an array');
        return $this->forms;
    }

    /**
     * List all forms by name.
     *
     * Returns a numeric array with the names of all registered forms.
     *
     * @access  public
     * @return  array
     */
    public function getFormNames()
    {
        assert('is_array($this->forms); // member "forms" is expected to be an array');
        return array_keys($this->forms);
    }

    /**
     * List of changes.
     *
     * Returns a list of change-log entries as a numeric array, each of which
     * are instances of DDLLog.
     *
     * The list may be empty. If so, the function returns an empty array.
     *
     * The change-logs are expected to be sorted by version-numbers in
     * descending order, where the top-most entry has the latest version.
     * You may provide a $startVersion. If so, the function will return only
     * entries which are newer. It uses the method version_compare() for
     * comparison of version-strings.
     * If you don't provide a version, all entries will be returned.
     *
     * Change-logs may be limited to a specific DBMS, esp. SQL-statements.
     * Each unrestricted logs are marked as "generic".
     * You may provide a target-DBMS of your choice. If so, all returned logs
     * must either be intended for the given target-DBMS, or be "generic"
     * entries.
     * If you don't provide a target-DBMS, only generic log-entries will be
     * returned.
     *
     * @access  public
     * @return  DDLChangeLog
     */
    public function getChangeLog()
    {
        return $this->changelog;
    }

    /**
     * Compare with another object.
     *
     * Returns bool(true) if this object and $anotherObject
     * are equal and bool(false) otherwise.
     *
     * Two instances are considered equal if and only if
     * they are both objects of the same class and they both
     * refer to the same file.
     *
     * @access   public
     * @param    \Yana\Core\IsObject $anotherObject  another object to compare
     * @return   string
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        if ($anotherObject instanceof $this) {
            if ($this->path == $anotherObject->path) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * <<magic>> Get a table, with the given attribute name.
     *
     * @access  public
     * @param   string  $name   name
     * @return  DDL
     */
    public function __get($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $name = mb_strtolower($name);
        switch (true)
        {
            case isset($this->tables[$name]):
                return $this->tables[$name];
            break;
            case isset($this->views[$name]):
                return $this->views[$name];
            break;
            case isset($this->forms[$name]):
                return $this->forms[$name];
            break;
            case isset($this->functions[$name]):
                return $this->functions[$name];
            break;
            case isset($this->sequences[$name]):
                return $this->sequences[$name];
            break;
            default:
                return null;
            break;
        }
    }

    /**
     * <<magic>> is set.
     *
     * Returns true if a named object with the given name exists in the database schema.
     *
     * @access  public
     * @param   string  $name   name
     * @return  bool
     */
    public function __isset($name)
    {
        return ($this->__get($name) !== null);
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @param   string             $path    file path
     * @return  DDLDatabase
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null, $path = "")
    {
        assert('is_string($path); // Invalid argument $path: string expected');
        $attributes = $node->attributes();
        $name = "";
        if (isset($attributes['name'])) {
            $name = mb_strtolower($attributes['name']);
        } else {
            $name = DDL::getNameFromPath($path);
        }

        // if (instance not already exists) { create new object and cache it }
        if (!isset(self::$instances[$path])) {
            // if (object is already in session cache) { unserialize object }
            if (false && isset($_SESSION[__CLASS__ . "/" . $name])) {
                self::$instances[$path] = unserialize($_SESSION[__CLASS__ . "/" . $name]);
                assert('self::$instances[$path] instanceof self;');
                // check if file has changed
                if (!is_file($path) || filemtime($path) > self::$instances[$path]->lastModified) {
                    // invalidate cache
                    self::$instances[$path] = null;
                }
            }
            // otherwise { must build new object from scratch }
            if (!isset(self::$instances[$path])) {
                self::$instances[$path] = new self($name, $path);
                if (isset($node->include)) {
                    if (is_array($node->include)) {
                        foreach ($node->include as $include)
                        {
                            self::$instances[$path]->_unserializeChild($include);
                        }
                    } else {
                        self::$instances[$path]->_unserializeChild($node->include);
                    }
                    unset($node->include);
                    self::$instances[$path]->loadIncludes();
                }
                self::$instances[$path]->_unserializeFromXDDL($node);
                self::$instances[$path]->lastModified = time();
                $_SESSION[__CLASS__ . "/" . $name] = self::$instances[$path];
            }
        }
        // return cached object
        return self::$instances[$path];
    }

}

?>