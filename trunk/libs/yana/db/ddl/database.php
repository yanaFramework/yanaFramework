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
 * @package     yana
 * @subpackage  db
 */
class Database extends \Yana\Db\Ddl\AbstractUnnamedObject
{
    /**#@+
     * @ignore
     */

    /**
     * @var  \Yana\Data\Adapters\IsDataAdapter
     */
    private static $_cache = null;

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
        'table'          => array('tables',         'array', 'Yana\Db\Ddl\Table'),
        'view'           => array('views',          'array', 'Yana\Db\Ddl\Views\View'),
        'form'           => array('forms',          'array', 'Yana\Db\Ddl\Form'),
        'function'       => array('functions',      'array', 'Yana\Db\Ddl\Functions\Definition'),
        'sequence'       => array('sequences',      'array', 'Yana\Db\Ddl\Sequence'),
        'initialization' => array('initialization', 'array', 'Yana\Db\Ddl\DatabaseInit'),
        'changelog'      => array('changelog',      'Yana\Db\Ddl\ChangeLog')
    );

    /**
     * @var string
     */
    protected $charset = null;

    /**
     * @var string
     */
    protected $datasource = null;

    /**
     * @var bool
     */
    protected $readonly = null;

    /**
     * @var string
     */
    protected $description = null;

    /**
     * @var string
     */
    protected $title = null;

    /**
     * @var \Yana\Db\Ddl\ChangeLog
     */
    protected $changelog = null;

    /**
     * @var array
     */
    protected $includes = array();

    /**
     * @var \Yana\Db\Ddl\Table[]
     */
    protected $tables = array();

    /**
     * @var \Yana\Db\Ddl\Views\View[]
     */
    protected $views = array();

    /**
     * @var \Yana\Db\Ddl\Form[]
     */
    protected $forms = array();

    /**
     * @var \Yana\Db\Ddl\Functions\Definition[]
     */
    protected $functions = array();

    /**
     * @var \Yana\Db\Ddl\Sequence[]
     */
    protected $sequences = array();

    /**
     * @var \Yana\Db\Ddl\DatabaseInit[]
     */
    protected $initialization = array();

    /**
     * @var bool
     */
    protected $modified = false;

    /**
     * @var string
     */
    protected $path = null;

    /**
     * @var int
     */
    protected $lastModified = 0;

    /**#@-*/

    /**
     * list of loaded includes
     *
     * An array of boolean values.
     * The keys are pathnames of files that have already been included.
     * The values are ignored.
     *
     * @see     \Yana\Db\Ddl\Database::loadIncludes()
     * @var     array
     * @ignore
     */
    private $_loadedIncludes = array();

    /**
     * Initialize instance.
     *
     * @param   string  $name  database name
     * @param   string  $path  file path
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given name is invalid
     */
    public function __construct(string $name = "", string $path = "")
    {
        assert(empty($path) || is_file($path), 'Invalid argument $path. File expected');
        parent::__construct($name);
        $this->changelog = new \Yana\Db\Ddl\ChangeLog($this);
        // save path information
        if (!empty($path)) {
            $this->path = $path;
        }
        // move new instance to cache
        if (!empty($name)) {
            $this->name = $name;
        } elseif (empty($name) && !empty($path)) {
            $this->name = \Yana\Db\Ddl\DDL::getNameFromPath($path);
        }
        if ($path > "") {
            $cache = self::_getCache();
            $cache[$path] = $this;
        }
    }

    /**
     * Replace the cache adapter.
     *
     * Overwrite only for unit-tests, or if you are absolutely sure you need to and know what you are doing.
     * Replacing this by the wrong adapter might introduce a security risk,
     * unless you are in a very specific usage scenario.
     *
     * Note that this may also replace the cache contents.
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $cache  new cache adapter
     */
    public static function setCache(\Yana\Data\Adapters\IsDataAdapter $cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Returns the currently selected cache adapter.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    protected static function _getCache()
    {
        if (!isset(self::$_cache)) {
            // @codeCoverageIgnoreStart
            self::$_cache = new \Yana\Data\Adapters\SessionAdapter(__CLASS__);
            // @codeCoverageIgnoreEnd
        }
        return self::$_cache;
    }

    /**
     * Get the path to the directory, where the database's source file is stored.
     *
     * @throws \Yana\Core\Exceptions\NotFoundException  if the source file is not defined (e.g. the file is unsaved)
     * @return string
     */
    private function _getDirectory(): string
    {
        if (empty($this->path)) {
            throw new \Yana\Core\Exceptions\NotFoundException('No directory defined for this database');
        }
        $directory = dirname($this->path) . '/';
        assert(is_dir($directory), 'Database base-directory not found');
        return $directory;
    }

    /**
     * Check if database structure has been modified.
     *
     * Returns bool(true) if the database has been marked as modified and bool(false) otherwise.
     *
     * @return  bool
     * @ignore
     */
    public function isModified(): bool
    {
        return (bool) $this->modified;
    }

    /**
     * Mark database as modified.
     *
     * Marks that the structure of the database has been modified.
     * This is to be used as an indicator for any scripts, that the database definition contains
     * unsaved changes and the database may need to be updated to comply with the given definition.
     *
     * @param   bool  $isModified  new value of this property
     * @return  $this
     * @ignore
     */
    public function setModified(bool $isModified = true)
    {
        if ($isModified && $this->path > "") {
            // clear the cache (so new instances won't copy the modified version)
            self::_getCache()->offsetUnset($this->path);
        }
        $this->modified = (bool) $isModified;
        return $this;
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
     * @return  array
     */
    public function getIncludes(): array
    {
        return $this->includes;
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
     * @param   array  $includes  list of files to include
     * @see     \Yana\Db\Ddl\Ddl::getDataSource()
     * @return  $this
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
     * @param   string  $include  file to include
     * @see     \Yana\Db\Ddl\Ddl::getDataSource()
     */
    public function addInclude(string $include)
    {
        $this->includes[] = $include;
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
     * @throws  \Yana\Core\Exceptions\NotFoundException  when an included file was not found
     * @name    \Yana\Db\Ddl\Database::loadIncludes()
     */
    public function loadIncludes()
    {
        try {
            $baseDirectory = $this->_getDirectory();
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            return; // if the base-directory is not defined, we just skip the loading process
        }
        // load each file
        foreach ($this->includes as $databaseName)
        {
            $path = $baseDirectory . $databaseName . \Yana\Db\Ddl\DDL::$extension;

            // check if file is already include (include just once)
            if ($path === $this->path) {
                continue;
            }
            if (!empty($this->_loadedIncludes[$path])) {
                continue;
            }
            $this->_loadedIncludes[$path] = true;

            $cache = self::_getCache();
            // check if file is already cached
            if (!isset($cache[$path])) {
                if (!is_file($path)) {
                    $message = "Included XDDL file '{$databaseName}' not found. " .
                        "Defined in file '" . $this->getName() . "'.";
                    throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::ERROR);
                }
                $xddl = new \Yana\Files\XDDL($path);
                $cache[$path] = $xddl->toDatabase();
                unset($xddl);
            }
            // get file
            $database = $cache[$path];

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
     * @return  string|null
     */
    public function getDescription(): ?string
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
     * @param   string  $description  new value of this property
     * @return  $this
     */
    public function setDescription(string $description = "")
    {
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
     * @return  string|null
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
     * Sets the title used to display the object in the UI.
     * To reset the property, leave the parameter empty.
     *
     * @param   string  $title title for display in UI
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
     * Get database charset.
     *
     * The charset may only be set upon creation of the database.
     * If you decide to use an existing database, please note, that the charset
     * which is actually used might be another.
     *
     * This information is optional. The default is DBMS dependent.
     * If no charset is specified, the function will return NULL instead.
     *
     * @return  string|null
     */
    public function getCharset(): ?string
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
     * @param   string  $charset  database charset
     * @return  $this
     */
    public function setCharset(string $charset = "")
    {
        if ($charset === "") {
            $this->charset = null;
        } else {
            $this->charset = $charset;
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
     * @return  string|null
     */
    public function getDataSource(): ?string
    {
        $dataSource = null;
        if (is_string($this->datasource)) {
            $dataSource = $this->datasource;
        }
        return $dataSource;
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
     * @param   string  $dataSource  data-source name
     * @return  $this
     * @see     \Yana\Db\Ddl\Ddl::getDataSource()
     */
    public function setDataSource(string $dataSource = "")
    {
        $this->datasource = (empty($dataSource)) ? null : "$dataSource";
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
     * @return  bool
     */
    public function isReadonly(): bool
    {
        return !empty($this->readonly);
    }

    /**
     * Set read-only access.
     *
     * You may set the database to be read-only to prevent any changes to it.
     * Use this if you wish to create a database viewer, or CD-ROM application.
     *
     * @param   bool  $isReadonly   new value of this property
     * @return  $this
     */
    public function setReadonly(bool $isReadonly = false)
    {
        $this->readonly = $isReadonly;
        return $this;
    }

    /**
     * Get table definition.
     *
     * Returns the table definition with the name $name as an instance of
     * \Yana\Db\Ddl\Table. If no table with the given name exists, the function returns
     * NULL instead.
     *
     * @param   string  $name   table name
     * @return  \Yana\Db\Ddl\Table|null
     */
    public function getTable(string $name): ?\Yana\Db\Ddl\Table
    {
        $lowerCaseName = mb_strtolower($name);

        $table = null;
        if (isset($this->tables[$lowerCaseName])) {
            $table = $this->tables[$lowerCaseName];
        }
        return $table;
    }

    /**
     * Add table definition.
     *
     * Adds a new table item to the database definition and returns the table
     * definition as an instance of \Yana\Db\Ddl\Table.
     *
     * If another table with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and '_'.
     *
     * @param   string  $name   set name for table
     * @return  \Yana\Db\Ddl\Table
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    if another table with the same name is already defined
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if given an invalid table name
     */
    public function addTable(string $name): \Yana\Db\Ddl\Table
    {
        $lowerCaseName = mb_strtolower($name);
        if (isset($this->tables[$lowerCaseName])) {
            $message = "Another table with the name '$lowerCaseName' is already defined.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($lowerCaseName);
            throw $exception;
            
        }

        $this->tables[$lowerCaseName] = new \Yana\Db\Ddl\Table($lowerCaseName, $this);
        return $this->tables[$lowerCaseName];
    }

    /**
     * List all tables by definition.
     *
     * Returns a list of table definitions, where each element is a \Yana\Db\Ddl\Table
     * object.
     * If no tables are defined, the list is empty.
     *
     * Important note! You can NOT add a new table to the database by adding a
     * new item to the list. Use the function {see \Yana\Db\Ddl\Table::addTable} instead.
     *
     * @return  array
     */
    public function getTables(): array
    {
        assert(is_array($this->tables), 'member "tables" is expected to be an array');
        return $this->tables;
    }

    /**
     * Returns a list of table definitions sorted by foreign keys.
     *
     * This should be used when you plan to copy/insert data into a database.
     * Call this function so that you know which tables to insert first and
     * not run into trouble with foreign key constraints being violated.
     *
     * Note! Your schema should NOT contain circular references (for obvious
     * reasons). If it does, this function will, however, not run into an
     * endless loop, it will simply abort and return an incomplete list.
     *
     * @return  array
     */
    public function getTablesSortedByForeignKey(): array
    {
        assert(!isset($tables), 'Cannot redeclare var $tables');
        $tables = array();
        assert(!isset($tablesToBeSorted), 'Cannot redeclare var $tablesToBeSorted');
        $tablesToBeSorted = $this->getTables();
        assert(!isset($sortedThisRound), 'Cannot redeclare var $abortCount');
        $sortedThisRound = -1;
        while (!empty($tablesToBeSorted) && $sortedThisRound !== 0)
        {
            $sortedThisRound = 0;
            assert(!isset($i), 'Cannot redeclare var $i');
            assert(!isset($table), 'Cannot redeclare var $table');
            foreach ($tablesToBeSorted as $i => $table)
            {
                assert($table instanceof \Yana\Db\Ddl\Table);
                assert(!isset($foreignKey), 'Cannot redeclare var $foreignKey');
                foreach ($table->getForeignKeys() as $foreignKey)
                {
                    /* @var $foreignKey \Yana\Db\Ddl\ForeignKey */
                    if (!\in_array($foreignKey->getTargetTable(), array_keys($tables))) {
                        unset($foreignKey);
                        continue 2;
                    }
                }
                unset($foreignKey);
                $sortedThisRound++;
                $tables[$table->getName()] = $table;
                unset($tablesToBeSorted[$i]);
            }
            unset($i, $table);
        }

        return $tables;
    }

    /**
     * List all tables by name.
     *
     * Returns a numeric array with the names of all registered tables.
     *
     * @return  array
     */
    public function getTableNames(): array
    {
        assert(is_array($this->tables), 'member "tables" is expected to be an array');
        return array_keys($this->tables);
    }

    /**
     * Get view definition.
     *
     * Returns the view definition with the name $name as an instance of
     * \Yana\Db\Ddl\Views\View. If no view with the given name exists, the function returns
     * NULL instead.
     *
     * @param   string  $name   view name
     * @return  \Yana\Db\Ddl\Views\View|null
     */
    public function getView(string $name): ?\Yana\Db\Ddl\Views\View
    {
        $lowerCaseName = mb_strtolower($name);

        $view = null;
        if (isset($this->views[$lowerCaseName])) {
            $view = $this->views[$lowerCaseName];
            assert($view instanceof \Yana\Db\Ddl\Views\View);
        }

        return $view;
    }

    /**
     * Add view definition.
     *
     * Adds a new view item to the database definition and returns the view
     * definition as an instance of \Yana\Db\Ddl\Views\View.
     *
     * If another view with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and '_'.
     *
     * @param   string  $name   name for view
     * @return  \Yana\Db\Ddl\Views\View
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    if another view with the same name is already defined
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if given an invalid view name
     */
    public function addView(string $name): \Yana\Db\Ddl\Views\View
    {
        $lowerCaseName = mb_strtolower($name);
        if (isset($this->views[$lowerCaseName])) {
            $message = "Another view with the name '$lowerCaseName' is already defined.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($lowerCaseName);
            throw $exception;
        }

        $this->views[$lowerCaseName] = new \Yana\Db\Ddl\Views\View($lowerCaseName, $this);
        return $this->views[$lowerCaseName];
    }

    /**
     * List all views by definition.
     *
     * Returns a list of view definitions, where each element is a \Yana\Db\Ddl\Views\View object.
     * If no views are defined, the list is empty.
     *
     * Important note! You can NOT add a new view to the database by adding a
     * new item to the list. Use the function {see \Yana\Db\Ddl\Ddl::addView} instead.
     *
     * @return  array
     */
    public function getViews(): array
    {
        assert(is_array($this->views), 'member "views" is expected to be an array');
        return $this->views;
    }

    /**
     * List all views by name.
     *
     * Returns a numeric array with the names of all registered views.
     *
     * @return  array
     */
    public function getViewNames(): array
    {
        assert(is_array($this->views), 'member "views" is expected to be an array');
        return array_keys($this->views);
    }

    /**
     * Get function definition.
     *
     * Returns the function definition with the name $name as an instance of
     * \Yana\Db\Ddl\Functions\Definition. If no function with the given name exists, NULL is returned
     * instead.
     *
     * @param   string  $name   name of expected function
     * @return  \Yana\Db\Ddl\Functions\Definition|null
     */
    public function getFunction(string $name): ?\Yana\Db\Ddl\Functions\Definition
    {
        $lowerCaseName = mb_strtolower($name);

        $function = null;
        if (isset($this->functions[$lowerCaseName])) {
            $function = $this->functions[$lowerCaseName];
            assert($function instanceof \Yana\Db\Ddl\Functions\Definition);
        }

        return $function;
    }

    /**
     * Add function definition.
     *
     * Adds a new function item to the database definition and returns the
     * function definition as an instance of \Yana\Db\Ddl\Functions\Definition.
     *
     * If another function with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and '_'.
     *
     * @param   string  $name  name for the new function
     * @return  \Yana\Db\Ddl\Functions\Definition
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    if another function with the same name is already defined
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if given an invalid name
     */
    public function addFunction(string $name): \Yana\Db\Ddl\Functions\Definition
    {
        $lowerCaseName = mb_strtolower($name);
        if (isset($this->functions[$lowerCaseName])) {
            $message = "Another function with the name '$lowerCaseName' is already defined.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($lowerCaseName);
            throw $exception;
        }

        $this->functions[$lowerCaseName] = new \Yana\Db\Ddl\Functions\Definition($lowerCaseName);
        return $this->functions[$lowerCaseName];
    }

    /**
     * List all functions by definition.
     *
     * Returns a list of function definitions, where each element is a
     * \Yana\Db\Ddl\Functions\Definition object.
     * If no functions are defined, the list is empty.
     *
     * Important note! You can NOT add a new function to the database by adding
     * a new item to the list. Use the function {see \Yana\Db\Ddl\Ddl::addFunction} instead.
     *
     * @return  array
     */
    public function getFunctions(): array
    {
        assert(is_array($this->functions), '$this->functions is expected to be an array');
        return $this->functions;
    }

    /**
     * List all functions by name.
     *
     * Returns a numeric array with the names of all registered functions.
     *
     * @return  array
     */
    public function getFunctionNames(): array
    {
        assert(is_array($this->functions), 'member "functions" is expected to be an array');
        return array_keys($this->functions);
    }

    /**
     * Get sequence definition.
     *
     * Returns the sequence definition with the name $name as an instance of
     * \Yana\Db\Ddl\Sequence. If no sequence with the given name exists, NULL is returned
     * instead.
     *
     * @param   string  $name  name of the expected sequence
     * @return  \Yana\Db\Ddl\Sequence|null
     */
    public function getSequence(string $name): ?\Yana\Db\Ddl\Sequence
    {
        $lowerCaseName = mb_strtolower($name);

        $sequence = null;
        if (isset($this->sequences[$lowerCaseName])) {
            $sequence = $this->sequences[$lowerCaseName];
            assert($sequence instanceof \Yana\Db\Ddl\Sequence);
        }

        return $sequence;
    }

    /**
     * Add sequence definition.
     *
     * Adds a new sequence item to the database definition and returns the
     * sequence definition as an instance of \Yana\Db\Ddl\Sequence.
     *
     * If another sequence with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and '_'.
     *
     * @param   string  $name   name for the new sequence
     * @return  \Yana\Db\Ddl\Sequence
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    if another sequence with the same name is already defined
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if given an invalid name
     */
    public function addSequence(string $name): \Yana\Db\Ddl\Sequence
    {
        $lowerCaseName = mb_strtolower($name);
        if (isset($this->sequences[$lowerCaseName])) {
            $message = "Another sequence with the name '$lowerCaseName' is already defined.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($lowerCaseName);
            throw $exception;
        }

        $this->sequences[$lowerCaseName] = new \Yana\Db\Ddl\Sequence($lowerCaseName);
        return $this->sequences[$lowerCaseName];
    }

    /**
     * List all sequences by definition.
     *
     * Returns a list of sequence definitions, where each element is a
     * \Yana\Db\Ddl\Sequence object.
     * If no sequences are defined, the list is empty.
     *
     * Important note! You can NOT add a new sequence to the database by adding
     * a new item to the list. Use the function {see \Yana\Db\Ddl\Ddl::addSequence} instead.
     *
     * @return  array
     */
    public function getSequences(): array
    {
        assert(is_array($this->sequences), 'member "sequences" is expected to be an array');
        return $this->sequences;
    }

    /**
     * List all sequences by name.
     *
     * Returns a numeric array with the names of all registered sequences.
     *
     * @return  array
     */
    public function getSequenceNames(): array
    {
        assert(is_array($this->sequences), 'member "sequences" is expected to be an array');
        return array_keys($this->sequences);
    }

    /**
     * List sql for database-initialization.
     *
     * Returns an ordered list of all initialization SQL statements for the given DBMS.
     * If no DBMS is given, it defaults to "generic".
     *
     * @param   string  $dbms  target database-management-system
     * @return  array
     */
    public function getInit(string $dbms = \Yana\Db\DriverEnumeration::GENERIC): array
    {
        $lowerCaseDbms = strtolower($dbms);

        if (empty($this->initialization)) {
            return array();
        }

        $initialization = array();
        foreach ((array) $this->initialization as $entry)
        {
            /* @var $entry \Yana\Db\Ddl\DatabaseInit */
            assert($entry instanceof \Yana\Db\Ddl\DatabaseInit);

            // target DBMS does not match
            $initDbms = $entry->getDBMS();
            if ($initDbms !== \Yana\Db\DriverEnumeration::GENERIC && $initDbms !== $lowerCaseDbms) {
                continue;
            }

            $initialization[] = $entry->getSQL();
        }
        return $initialization;
    }

    /**
     * Drop database-initialization.
     *
     * Removes all previously set SQL-statements for initialization of the
     * database.
     *
     * @return  $this
     */
    public function dropInit()
    {
        $this->initialization = array();
        return $this;
    }

    /**
     * Remove a table from the database definition.
     *
     * Note: the object is NOT deleted. Only the definition is lost.
     *
     * @param   string  $name  table name
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the table is not found
     * @return  $this
     */
    public function dropTable(string $name)
    {
        $lowerCaseName = mb_strtolower($name);
        if (!isset($this->tables[$lowerCaseName])) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such table '$lowerCaseName'.");
        }

        $this->tables[$lowerCaseName] = null;
        return $this;
    }

    /**
     * Remove a view from the database definition.
     *
     * Note: the object is NOT deleted. Only the definition is lost.
     *
     * @param   string  $name  view name
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the view is not found
     * @return  $this
     */
    public function dropView(string $name)
    {
        $lowerCaseName = mb_strtolower($name);
        if (!isset($this->views[$lowerCaseName])) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such view '$lowerCaseName'.");
        }

        $this->views[$lowerCaseName] = null;
        return $this;
    }

    /**
     * Remove a form from the database definition.
     *
     * Note: the object is NOT deleted. Only the definition is lost.
     *
     * @param   string  $name  form name
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the form is not found
     * @return  $this
     */
    public function dropForm(string $name)
    {
        $lowerCaseName = mb_strtolower($name);
        if (!isset($this->forms[$lowerCaseName])) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such form '$lowerCaseName'.");
        }

        $this->forms[$lowerCaseName] = null;
        return $this;
    }

    /**
     * Remove a form function the database definition.
     *
     * Note: the object is NOT deleted. Only the definition is lost.
     *
     * @param   string  $name  function name
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the function is not found
     * @return  $this
     */
    public function dropFunction(string $name)
    {
        $lowerCaseName = mb_strtolower($name);
        if (!isset($this->functions[$lowerCaseName])) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such function '$lowerCaseName'.");
        }

        $this->functions[$lowerCaseName] = null;
        return $this;
    }

    /**
     * Remove a sequence function the database definition.
     *
     * Note: the object is NOT deleted. Only the definition is lost.
     *
     * @param   string  $name  sequence name
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the sequence is not found
     * @return  $this
     */
    public function dropSequence(string $name)
    {
        $lowerCaseName = mb_strtolower($name);
        if (!isset($this->sequences[$lowerCaseName])) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such sequence '$lowerCaseName'.");
        }

        $this->sequences[$lowerCaseName] = null;
        return $this;
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
     * @param   string  $sql   SQL statement
     * @param   string  $dbms  target database-management-system
     * @return  $this
     */
    public function addInit(string $sql, string $dbms = \Yana\Db\DriverEnumeration::GENERIC)
    {
        $init = new \Yana\Db\Ddl\DatabaseInit();
        $init->setDBMS(strtolower($dbms));
        $init->setSQL($sql);
        $this->initialization[] = $init;
        return $this;
    }

    /**
     * Check if table exists.
     *
     * Returns bool(true) if a table with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @param   string  $name  new value of this property
     * @return  bool
     */
    public function isTable(string $name): bool
    {
        $lowerCaseName = mb_strtolower($name);
        return isset($this->tables[$lowerCaseName]);
    }

    /**
     * Check if view exists.
     *
     * Returns bool(true) if a view with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @param   string  $name   new value of this property
     * @return  bool
     */
    public function isView(string $name): bool
    {
        $lowerCaseName = mb_strtolower($name);
        return isset($this->views[$lowerCaseName]);
    }

    /**
     * Check if function exists.
     *
     * Returns bool(true) if a function with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @param   string  $name   new value of this property
     * @return  bool
     */
    public function isFunction(string $name): bool
    {
        $lowerCaseName = mb_strtolower($name);
        return isset($this->functions[$lowerCaseName]);
    }

    /**
     * Check if sequence exists.
     *
     * Returns bool(true) if a sequence with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @param   string  $name   new value of this property
     * @return  bool
     */
    public function isSequence(string $name): bool
    {
        $lowerCaseName = mb_strtolower($name);
        return isset($this->sequences[$lowerCaseName]);
    }

    /**
     * Check if form exists.
     *
     * Returns bool(true) if a form with the given name is registered and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @param   string  $name   new value of this property
     * @return  bool
     */
    public function isForm(string $name): bool
    {
        $lowerCaseName = mb_strtolower($name);
        return isset($this->forms[$lowerCaseName]);
    }

    /**
     * Get form by name.
     *
     * Returns the form definition with the name $name as an instance of
     * \Yana\Db\Ddl\Form. If no form with the given name exists, NULL is returned
     * instead.
     *
     * @param   string  $name  form name
     * @return  \Yana\Db\Ddl\Form|null
     */
    public function getForm(string $name): ?\Yana\Db\Ddl\Form
    {
        $lowerCaseName = mb_strtolower($name);

        $form = null;
        if (isset($this->forms[$lowerCaseName])) {
            $form = $this->forms[$lowerCaseName];
            assert($form instanceof \Yana\Db\Ddl\Form);
        }

        return $form;
    }

    /**
     * Add form.
     *
     * Adds a new form item to the database definition and returns the
     * sequence definition as an instance of \Yana\Db\Ddl\Form.
     *
     * If another form with the same name already exists, it throws an
     * AlreadyExistsException.
     * The name must start with a letter and may only contain: a-z, 0-9, '-' and '_'.
     *
     * @param   string  $name  new Form name
     * @return  \Yana\Db\Ddl\Form
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException  if another form with the same name is already defined
     */
    public function addForm(string $name): \Yana\Db\Ddl\Form
    {
        $lowerCaseName = mb_strtolower($name);
        if (isset($this->forms[$lowerCaseName])) {
            $message = "Another form with the name '$lowerCaseName' already exists in database '{$this->getName()}'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $exception = new \Yana\Core\Exceptions\AlreadyExistsException($message, $level);
            $exception->setId($this->getName());
            throw $exception;
        }
        // add element to list of defined forms
        $this->forms[$lowerCaseName] = new \Yana\Db\Ddl\Form($lowerCaseName, $this);
        return $this->forms[$lowerCaseName];
    }

    /**
     * Get list of forms.
     *
     * Returns an associative array of all forms, where the array keys are the
     * names and the values are instances of \Yana\Db\Ddl\Form.
     *
     * If no form has been defined (yet), an empty array is returned.
     *
     * Important note! You can NOT add a new form to the database by adding
     * a new item to the list. Use the function {see \Yana\Db\Ddl\Ddl::addForm} instead.
     *
     * @return  array
     */
    public function getForms(): array
    {
        assert(is_array($this->forms), 'member "forms" is expected to be an array');
        return $this->forms;
    }

    /**
     * List all forms by name.
     *
     * Returns a numeric array with the names of all registered forms.
     *
     * @return  array
     */
    public function getFormNames(): array
    {
        assert(is_array($this->forms), 'member "forms" is expected to be an array');
        return array_keys($this->forms);
    }

    /**
     * List of changes.
     *
     * Returns a list of change-log entries as a numeric array, each of which
     * are instances of \Yana\Db\Ddl\Logs\Log.
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
     * @return  \Yana\Db\Ddl\ChangeLog
     */
    public function getChangeLog(): \Yana\Db\Ddl\ChangeLog
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
     * @param    \Yana\Core\IsObject $anotherObject  another object to compare
     * @return   string
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        return ($anotherObject instanceof $this) && ($this->path == $anotherObject->path);
    }

    /**
     * <<magic>> Get a table, with the given attribute name.
     *
     * @param   string  $name   name
     * @return  \Yana\Db\Ddl\DDL
     */
    public function __get($name)
    {
        assert(is_string($name), 'Invalid argument $name: string expected');
        $lowerCaseName = mb_strtolower($name);
        switch (true)
        {
            case isset($this->tables[$lowerCaseName]):
                return $this->tables[$lowerCaseName];
            case isset($this->views[$lowerCaseName]):
                return $this->views[$lowerCaseName];
            case isset($this->forms[$lowerCaseName]):
                return $this->forms[$lowerCaseName];
            case isset($this->functions[$lowerCaseName]):
                return $this->functions[$lowerCaseName];
            case isset($this->sequences[$lowerCaseName]):
                return $this->sequences[$lowerCaseName];
            default:
                return null;
        }
    }

    /**
     * <<magic>> is set.
     *
     * Returns true if a named object with the given name exists in the database schema.
     *
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
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @param   string             $path    file path
     * @return  \Yana\Db\Ddl\Database
     * @throws  \Yana\Db\Ddl\XddlException  on syntax error
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null, string $path = "")
    {
        $attributes = $node->attributes();
        $schemaName = \Yana\Db\Ddl\DDL::getNameFromPath($path);
        $name = isset($attributes['name']) ? mb_strtolower((string) $attributes['name']) : $schemaName;
        $cache = self::_getCache();

        // if (object is already in cache) { unserialize object }
        if ($path > "" && isset($cache[$path])) {
            $database = $cache[$path];
            assert($database instanceof self);
            // check if file has changed
            if (!is_file($path) || filemtime($path) > $database->lastModified) {
                // The cached file is outdated. Invalidate cache.
                unset($cache[$path]);
                unset($database);
            }
        }
        // otherwise { must build new object from scratch }
        if (!isset($database)) {
            $database = new self($name, $path);
            if (isset($node->include)) {
                if (is_array($node->include)) {
                    foreach ($node->include as $include)
                    {
                        $database->_unserializeChild($include);
                    }
                } else {
                    $database->_unserializeChild($node->include);
                }
                unset($node->include);
                $database->loadIncludes();
            }
            $database->_unserializeFromXDDL($node);
            $database->lastModified = time();
            if ($path > "") {
                $cache[$path] = $database;
            }
        }
        return $database;
    }

}

?>