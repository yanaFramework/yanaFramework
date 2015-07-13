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

namespace Yana\Db;

/**
 * database structure file
 *
 * This wrapper class represents the structure of a database
 *
 * @access      public
 * @package     yana
 * @subpackage  db
 * @deprecated  since 3.1.0
 */
class Structure extends \Yana\Files\SML
{
    /**
     * File extensions
     *
     * @access  private
     * @static
     * @var     string
     */
    private static $_extension = ".config";

    /**#@+
     * @ignore
     * @access  private
     */

    /** @var  array  */   private $_cachedFields = array();
    /** @var  array  */   private $_changedItems = array();
    /** @var  string */   private $_logText = "";
    /** @var  string */   private $_dbName = null;

    /**#@-*/

    /**
     * @ignore
     */
    const AUTO = "auto";

    /**
     * constructor
     *
     * Create a new instance of this class.
     *
     * The argument $filename may either be a path to a structure file, or,
     * if the file refers to an active database and is stored in the database
     * configuration directory, the name of a database without any path or
     * file extension.
     *
     * @param  string  $filename  name of database structure file
     */
    public function __construct($filename)
    {
        assert('is_string($filename)', ' Wrong type for argument 1. String expected');
        $filename = \Yana\Db\Structure::_getFilename($filename);
        parent::__construct($filename, CASE_UPPER);

        $this->resetStats();
    }

    /**
     * get database name
     *
     * Returns the name of the database file.
     *
     * @access  public
     * @return  string
     * @since   3.1.0
     */
    public function getDatabaseName()
    {
        if (!isset($this->_dbName)) {
            $this->_dbName = basename($this->getPath(), self::$_extension);
        }
        return $this->_dbName;
    }

    /**
     * read and initialize the file
     *
     * Always call this first before anything else.
     * Returns the file content on success and bool(false) on error.
     *
     * @access  public
     */
    public function read()
    {
        parent::read();
        if (isset($this->content['TABLES']) && is_array($this->content['TABLES'])) {
            foreach ($this->content['TABLES'] as $k => $v)
            {
                $this->content['TABLES'][$k]['PRIMARY_KEY'] = mb_strtolower($v['PRIMARY_KEY']);
                if (isset($v['FOREIGN_KEYS'])) {
                    assert('!isset($k1)', 'cannot redeclare variable $k1');
                    assert('!isset($v1)', 'cannot redeclare variable $v1');
                    foreach ($v['FOREIGN_KEYS'] as $k1 => $v1)
                    {
                        $this->content['TABLES'][$k]['FOREIGN_KEYS'][$k1] = mb_strtolower($v1);
                    }
                    unset($k1, $v1);
                }
            }
        } /* end if */
        if (isset($this->content['INCLUDE'])) {
            if (is_string($this->content['INCLUDE'])) {
                $test = $this->includeFile($this->content['INCLUDE']);
                if ($test === false) {
                    trigger_error("Error importing file '".$this->content['INCLUDE']."'.", E_USER_WARNING);
                }
            } elseif (is_array($this->content['INCLUDE'])) {
                foreach ($this->content['INCLUDE'] as $filename)
                {
                    if (is_string($filename)) {
                        $test = $this->includeFile($filename);
                        if ($test === false) {
                            trigger_error("Error importing file '{$filename}'.", E_USER_WARNING);
                        }
                    } else {
                        $message = "Invalid field type in ".__METHOD__."().\n\t\tContents of array 'INCLUDE' are " .
                            "supposed to be strings. Found '".gettype($filename)."' instead.";
                        trigger_error($message, E_USER_NOTICE);
                    }
                } /* end foreach */
            } else {
                $message = "Invalid field type in ".__METHOD__."().\n\t\tField 'INCLUDE' is supposed to be array " .
                    "or string. Found '".gettype($filename)."' instead." ;
                trigger_error($message, E_USER_NOTICE);
            }
        } /* end if*/
        return $this->content;
    }

    /**
     * get the compiled structure of the database
     *
     * @access  public
     * @return  mixed
     */
    public function getStructure()
    {
        return $this->content;
    }

    /**
     * get the file source
     *
     * Returns the text of the source file containing
     * the database structure as a string or bool(false)
     * on error.
     *
     * @access  public
     * @return  mixed
     */
    public function getSource()
    {
        $source = file($this->getPath());
        if (!is_array($source)) {
            return false;

        } else {
            $source = implode("", $source);
            assert('is_string($source)', ' Unexpected result $source. String expected');
            if (empty($source)) {
                return false;
            } else {
                return $source;
            }
        }
    }

    /**
     * check whether a table exists in the current structure
     *
     * Returns bool(true) if a table with the given name is listed
     * in the current structure file and bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $table  name of table
     * @return  bool
     */
    public function isTable($table)
    {
        return is_array($this->_getTable($table));
    }

    /**
     * add a new table
     *
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * @access  public
     * @param   string  $table  name of table
     * @return  bool
     * @since   2.9
     * @name    DbStructure::addTable()
     * @see     DbStructure::renameTable()
     * @see     DbStructure::dropTable()
     */
    public function addTable($table)
    {
        if (preg_match('/^[\w\d-_]+$/s', $table) && !isset($this->content['TABLES'][mb_strtoupper($table)])) {
            if (!isset($this->content['TABLES'])) {
                $this->content['TABLES'] = array();
            }
            $this->content['TABLES'][mb_strtoupper($table)] = array('CONTENT' => array());

            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($table, null, "create table '$table'", 'create');

            return true;
        } else {
            return false;
        }
    }

    /**
     * rename a table
     *
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * @access  public
     * @param   string  $oldTable   previous name of table
     * @param   string  $newTable   new name of table
     * @return  bool
     * @since   2.9.7
     * @name    DbStructure::renameTable()
     * @see     DbStructure::addTable()
     * @see     DbStructure::dropTable()
     */
    public function renameTable($oldTable, $newTable)
    {
        assert('is_string($oldTable)', ' Wrong type for argument 1. String expected');
        assert('is_string($newTable)', ' Wrong type for argument 2. String expected');
        $newTable = mb_strtoupper("$newTable");
        $oldTable = mb_strtoupper("$oldTable");

        if (!isset($this->content['TABLES'][$oldTable])) {
            /* error: column not found */
            trigger_error("Unable to rename table. Old table not found: '$oldTable'", E_USER_NOTICE);
            return false;
        }

        if (isset($this->content['TABLES'][$newTable])) {
            /* error: column already exists */
            trigger_error("Unable to rename table. Table with same name already exists: '$newTable'", E_USER_NOTICE);
            return false;

        } else {
            $this->content['TABLES'][$newTable] = $this->content['TABLES'][$oldTable];
            unset($this->content['TABLES'][$oldTable]);

            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($oldTable, null, "rename table '$oldTable' to '$newTable'", 'rename', $newTable);

            return true;
        }
    }

    /**
     * drop a table
     *
     * Removes a table definition from the database.
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * @access  public
     * @param   string  $table  previous name of table
     * @return  bool
     * @since   2.9.7
     * @name    DbStructure::dropTable()
     * @see     DbStructure::addTable()
     * @see     DbStructure::renameTable()
     */
    public function dropTable($table)
    {
        assert('is_string($table)', ' Wrong type for argument 1. String expected');
        $table = mb_strtoupper("$table");

        if (!isset($this->content['TABLES'][$table])) {
            /* error: column not found */
            trigger_error("Unable to drop table '$table'. The table does not exist", E_USER_NOTICE);
            return false;

        } else {
            unset($this->content['TABLES'][$table]);

            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($table, null, "drop table '$table'", 'drop');

            return true;
        }
    }

    /**
     * check whether a column exists in the current structure
     *
     * Returns bool(true) if a table named $table is listed
     * in the current structure file and it has a column named $column
     * in its list of contents. Returns bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     */
    public function isColumn($table, $column)
    {
        return is_array($this->_getColumn($table, $column));
    }

    /**
     * add a new column
     *
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     * @since   2.9
     * @name    DbStructure::addColumn()
     * @see     DbStructure::renameColumn()
     * @see     DbStructure::dropColumn()
     */
    public function addColumn($table, $column)
    {
        $tbl =& $this->_getTable($table);
        if (!is_array($tbl)) {
            /* error: table not found */
            return false;

        } elseif (preg_match('/^[\w\d-_]+$/s', $column) && !isset($tbl['CONTENT'][mb_strtoupper($column)])) {
            $tbl['CONTENT'][mb_strtoupper($column)] = array();

            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($table, $column, "create column '$column' on table '$table'", 'create');

            return true;

        } else {
            return false;
        }
    }

    /**
     * rename a column
     *
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * @access  public
     * @param   string  $table      name of table
     * @param   string  $oldColumn  previous name of column
     * @param   string  $newColumn  new name of column
     * @return  bool
     * @since   2.9.7
     * @name    DbStructure::renameColumn()
     * @see     DbStructure::addColumn()
     * @see     DbStructure::dropColumn()
     */
    public function renameColumn($table, $oldColumn, $newColumn)
    {
        assert('is_string($table)', ' Invalid argument $table: string expected');
        assert('is_string($oldColumn)', ' Invalid argument $oldColumn: string expected');
        assert('is_string($newColumn)', ' Invalid argument $newColumn: string expected');

        $oldColumn = mb_strtoupper($oldColumn);
        $newColumn = mb_strtoupper($newColumn);

        $tbl =& $this->_getTable($table);
        if (!is_array($tbl)) {
            /* error: table not found */
            return false;

        } elseif (!isset($tbl['CONTENT'][$oldColumn])) {
            /* error: column not found */
            trigger_error("Unable to rename column. Old column not found: '$oldColumn'", E_USER_NOTICE);
            return false;

        } elseif (isset($tbl['CONTENT'][$newColumn])) {
            /* error: column already exists */
            trigger_error("Unable to rename column. Column with same name already exists: '$newColumn'", E_USER_NOTICE);
            return false;

        } else {
            $tbl['CONTENT'][$newColumn] = $tbl['CONTENT'][$oldColumn];
            unset($tbl['CONTENT'][$oldColumn]);

            /*
             * write protocol to keep track of changes
             */
            $comment = "rename column '$oldColumn' to '$newColumn' on table '$table'";
            $this->_logChanges($table, $oldColumn, $comment, 'rename', $newColumn);

            return true;
        }
    }

    /**
     * drop a column
     *
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  previous name of column
     * @return  bool
     * @since   2.9.7
     * @name    DbStructure::dropColumn()
     * @see     DbStructure::addColumn()
     * @see     DbStructure::renameColumn()
     */
    public function dropColumn($table, $column)
    {
        assert('is_string($table)', ' Invalid argument $table: string expected');
        assert('is_string($column)', ' Invalid argument $column: string expected');

        $column = mb_strtoupper($column);

        $tbl =& $this->_getTable($table);
        if (!is_array($tbl)) {
            /* error: table not found */
            return false;

        } elseif (!isset($tbl['CONTENT'][$column])) {
            /* error: column not found */
            trigger_error("Unable to drop column '$column'. The column does not exist.", E_USER_NOTICE);
            return false;

        } else {
            unset($tbl['CONTENT'][$column]);

            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($table, $column, "drop column '$column' on table '$table'", 'drop');

            return true;
        }
    }

    /**
     * set sql statements for initialization of a table
     *
     * This adds a list of standard SQL statements to a table,
     * that will be auto-run, when the database is installed.
     * This can be particularly usefull to insert standard rows
     * in a table on creation, that otherwise would be empty.
     *
     * To unset the currently selected initialization statements,
     * set the second argument to NULL.
     *
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * 1) Note on SQL syntax. The syntax of the statements is
     * limited, since it needs to be DBMS-independent. This is,
     * only Insert, Update and Delete statements are allowed.
     * These statements must comply with the syntax understood
     * by the framework's internal query parser.
     * E.g. this means
     * 1) no quoting of identifiers and
     * 2) double-quotes (") need to be used as string delimiters.
     * If you don't limit yourself to these restrictions, it might
     * still run for the choosen DBMS, but it may not work with
     * the internal virtual database or any other DBMS, that does
     * not support the syntax you have used.
     *
     * To test the syntax of your statements for a structure file
     * foo.config you may use the following code:
     * <code>
     * global $YANA;
     * $parser = new DbQueryParser(\Yana\Application::connect('foo'));
     * foreach ($statements as $statement)
     * {
     *     if (!$parser->parseSQL($statement)) {
     *         die("Invalid sql: $statement");
     *     }
     * }
     * </code>
     *
     * Example for a valid sql statement:
     * <code>
     * insert into bar (bar_id, bar_value) values("FOO", 1)
     * </code>
     *
     * 2) Note on alternatives. You may have several external
     * sql files for each DBMS you wish to support. To do so
     * you should name your files after the associated structure
     * file and place each of them in the config/db/.install/
     * directory. E.g. if your structure file is named
     * "foo.config", you should have your sql files named
     * "foo.sql" and put them in "config/db/.install/mysql/foo.sql"
     * for MySQL, "config/db/.install/postgresql/foo.sql" for
     * PostgreSQL aso.
     *
     * @access  public
     * @param   string  $table       name of table
     * @param   array   $statements  list of sql statements
     * @return  bool
     * @name    DbStructure::setInit()
     * @see     DbStructure::getInit()
     * @since   2.9.7
     */
    public function setInit($table, array $statements = null)
    {
        $tbl =& $this->_getTable($table);
        if (!is_array($tbl)) {
            /* error: table not found */
            return false;

        /*
         * unset property
         */
        } elseif (is_null($statements)) {
            if (isset($tbl['INITIALIZATION'])) {
                unset($tbl['INITIALIZATION']);
            }
            return true;

        /*
         * set property
         */
        } else {
            $tbl['INITIALIZATION'] = array_values($statements);
            return true;

        }
    }

    /**
     * get sql statements for initialization of a table
     *
     * Returns a list of sql statements as a numeric array
     * on success and bool(false) on error.
     *
     * This can be particularly usefull to insert standard rows
     * in a table on creation, that otherwise would be empty.
     *
     * You may leave argument $table blank to get all statements
     * for all tables.
     *
     * @access  public
     * @param   string  $table  name of table
     * @return  bool
     * @name    DbStructure::getInit()
     * @see     DbStructure::setInit()
     * @since   2.9.7
     */
    public function getInit($table = null)
    {
        if (is_null($table)) {
            $tables = (array) $this->getTables();
        } elseif (is_string($table)) {
            $tables = array($table);
        } else {
            /* error: invalid argument */
            return false;
        }

        $stmts = array();

        /*
         * loop through tables;
         */
        foreach ($tables as $table)
        {
            $tbl =& $this->_getTable($table);
            if (!is_array($tbl)) {
                /* error: table not found */
                return false;

            } elseif (!isset($tbl['INITIALIZATION']) || !is_array($tbl['INITIALIZATION'])) {
                /* error: property not set or invalid */
                continue;

            } else {
                $stmts = array_merge($stmts, $tbl['INITIALIZATION']);
            }
        } /* end foreach */

        if (empty($stmts)) {
            return false;
        } else {
            return $stmts;
        }
    }

    /**
     * check whether a column allows NULL values
     *
     * Returns bool(true) if a table named $table is listed
     * in the current structure file and it has a column named $column
     * that allows undefined values (NULL). Returns bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     * @since   2.8.6
     * @name    DbStructure::isNullable()
     * @see     DbStructure::setNullable()
     */
    public function isNullable($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;

        } elseif (!isset($col['REQUIRED'])) {
            if ($this->isPrimaryKey($table, $column)) {
                return false;
            } else {
                return true;
            }

        } elseif ($col['REQUIRED'] === false) {
            return true;

        } elseif (strcasecmp($col['REQUIRED'], 'false') === 0) {
            return true;

        } else {
            return false;
        }
    }

    /**
     * choose wether a column should be nullable
     *
     * Sets the "required" property of the column.
     * If the argument $isNullable is bool(true), then
     * "required" is set to false. Otherwise it is
     * set to true.
     *
     * If the table or column is not defined, the function
     * returns bool(false).
     * Otherwise it returns bool(true).
     *
     * @access  public
     * @param   string  $table       name of table
     * @param   string  $column      name of column
     * @param   bool    $isNullable  new value of this property
     * @return  bool
     * @since   2.9
     * @name    DbStructure::setNullable()
     * @see     DbStructure::isNullable()
     */
    public function setNullable($table, $column, $isNullable)
    {
        $col =& $this->_getColumn($table, $column);

        /*
         * 1) error: table or column not found
         */
        if (!is_array($col)) {
            return false;

        /*
         * 2) set property
         */
        } else {
            /*
             * write protocol to keep track of changes
             */
            $comment = "set property 'nullable' on column '$column' on table '$table'";
            $this->_logChanges($table, $column, $comment, 'update');

            if ($isNullable) {
                $col['REQUIRED'] = false;
            } else {
                $col['REQUIRED'] = true;
            }

            return true;
        }
    }

    /**
     * make a column use auto-number / auto-filled values
     *
     * Sets the property "required" of the selected column to
     * the value "auto".
     *
     * This enables the "autofill" feature, which is available for
     * columns of several types. On columns of type "integer" it
     * mimics MySQL's "auto increment" feature.
     * On columns of type "ip" it enters the visitor's remote
     * address (IP) automatically.
     * For type "time" it enters the current server time.
     *
     * However: you should note, that the user input takes
     * precedence over the autofill feature, which defines a default value.
     *
     * Note: this function does not check wether "autofill" does "make sense"
     * for the type of column you selected. It also does not clear the
     * "default" property of the column, if any.
     *
     * Also note that this property is "virtual". This means, there is not really
     * a property "auto" in structure files. Instead this will set the property
     * "required" to the value "auto".  If the argument $isAuto is set to false,
     * the property "required" will be set back to bool(true).
     * This is identical to calling {@link DbStructure::setNullable()} with
     * the value bool(false). Thus calling {@link DbStructure::isNullable()}
     * on this column will return bool(false).
     *
     * Returns bool(false) if the table or column does not exist.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @param   bool    $isAuto  new value of this property
     * @return  bool
     * @since   2.9
     * @name    DbStructure::setAuto()
     * @see     DbStructure::isAuto()
     * @see     DbStructure::setNullable()
     * @see     DbStructure::isNullable()
     */
    public function setAuto($table, $column, $isAuto = true)
    {
        $col =& $this->_getColumn($table, $column);

        /*
         * 1) error: table or column not found
         */
        if (!is_array($col)) {
            return false;

        /*
         * 2) set property
         */
        } else {
            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($table, $column, "set autonumber on column '$column' on table '$table'", 'update');

            if ($isAuto) {
                $col['REQUIRED'] = self::AUTO;
            } else {
                $col['REQUIRED'] = true;
            }

            return true;
        }
    }

    /**
     * check whether a column uses the "autofill" feature
     *
     * Returns bool(true) if a table named $table is listed
     * in the current structure file and it has a column named $column
     * that uses the autofill feature. Returns bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * Autofill is activated by setting the property "REQUIRED"
     * to "AUTO".
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     * @since   2.9
     * @name    DbStructure::isAuto()
     * @see     DbStructure::isAutonumber()
     */
    public function isAuto($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;
        } elseif (!isset($col['REQUIRED'])) {
            /* property is undefined */
            return false;
        } else {
            if (strcasecmp($col['REQUIRED'], self::AUTO) === 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * check whether a column is autonumber/autoincrement
     *
     * Returns bool(true) if a table named $table is listed
     * in the current structure file and it has a column named $column
     * that is an autonumber colummn. Returns bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     * @since   2.8.6
     * @name    DbStructure::isAutonumber()
     */
    public function isAutonumber($table, $column)
    {
        $type = $this->getType($table, $column);
        $type = mb_strtolower($type);
        if ($type !== 'int' && $type !== 'integer') {
            return false;
        } else {
            return $this->isAuto($table, $column);
        }
    }

    /**
     * check whether a column is indexed in the current structure
     *
     * Returns bool(true) if a table named $table is listed
     * in the current structure file and it has a column named $column,
     * which has an index. Returns bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     * @since   2.8.5
     * @name    DbStructure::hasIndex()
     * @see     DbStructure::setIndex()
     */
    public function hasIndex($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;

        } elseif (!isset($col['INDEX'])) {
            /* index property is undefined */
            return false;

        } else {
            if (is_string($col['INDEX'])) {
                if (strcasecmp($col['INDEX'], 'true') === 0) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($col['INDEX'] == true) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * add/remove an index on a column
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @param   string  $table     name of table
     * @param   string  $column    name of column
     * @param   bool    $hasIndex  new value of this property
     * @return  bool
     * @since   2.9
     * @name    DbStructure::setIndex()
     * @see     DbStructure::hasIndex()
     */
    public function setIndex($table, $column, $hasIndex)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;
        } else {
            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($table, $column, "create/drop 'index' on column '$column' on table '$table'", 'update');

            if ($hasIndex) {
                $col['INDEX'] = true;

            } else {
                $col['INDEX'] = false;
            }

            return true;
        }
    }

    /**
     * check whether the table has a column containing a profile id
     *
     * Returns the name of a column that is supposed to contain the
     * profile id (if any).
     *
     * If no table named $table is listed in the current structure file
     * or $table does not have a column using a profile id, or the
     * specified column does not exist, the function returns bool(false).
     *
     * @access  public
     * @param   string  $table   name of table
     * @return  string
     * @since   2.9
     * @name    DbStructure::getProfile()
     * @see     DbStructure::setProfile()
     */
    public function getProfile($table)
    {

        $tbl =& $this->_getTable($table);

        if (!is_array($tbl)) {
            /* error: table not found */
            return false;

        } elseif (!isset($tbl['PROFILE_KEY'])) {
            return false;

        } elseif (YANA_DB_STRICT && !$this->isColumn($table, $tbl['PROFILE_KEY'])) {
            $message = "Error in structure file '".$this->getPath()."'.\n\t\tTable '$table' " .
                "refers to a non-existing column '".$tbl['PROFILE_KEY']."' as profile id.";
            trigger_error($message, E_USER_WARNING);
            return false;

        } else {
            return $tbl['PROFILE_KEY'];
        }
    }

    /**
     * add/remove a profile reference on a column
     *
     * To unset, leave $column blank.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     * @since   2.9
     * @name    DbStructure::setProfile()
     * @see     DbStructure::getProfile()
     */
    public function setProfile($table, $column = null)
    {
        $tbl =& $this->_getTable($table);

        if (!is_array($tbl)) {
            /* error: table not found */
            return false;

        } elseif (is_null($column)) {
            if (isset($tbl['PROFILE_KEY'])) {
                unset($tbl['PROFILE_KEY']);
            }
            return true;

        } elseif (!$this->isColumn($table, $column)) {
            /* error: column not found */
            return false;

        } else {
            /* settype to STRING */
            $column = (string) $column;
            $column = mb_strtolower($column);
            $tbl['PROFILE_KEY'] = $column;
            return true;
        }
    }

    /**
     * check whether a foreign key exists in the current structure
     *
     * Returns bool(true) if a table named $table is listed
     * in the current structure file and it has a column named $column
     * in its list of foreign keys. Returns bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     */
    public function isForeignKey($table, $column)
    {
        /* settype to STRING */
        $column = (string) $column;
        $column = mb_strtoupper($column);

        $tbl =& $this->_getTable($table);

        if (!is_array($tbl)) {
            /* error: table not found */
            return false;

        } elseif (!isset($tbl['FOREIGN_KEYS'][$column])) {
            return false;

        } else {
            return true;
        }
    }

    /**
     * add a foreign key constraint
     *
     * Sets a foreign key constraint on a $column in $table.
     * The foreign key will point to table $ftable.
     *
     * To unset the constraint, leave $ftable blank, or set it to null.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @param   string  $table   name of base table
     * @param   string  $column  name of column
     * @param   string  $ftable  name of target table
     * @return  bool
     */
    public function setForeignKey($table, $column, $ftable = null)
    {
        /* settype to STRING */
        $column = (string) $column;
        $column = mb_strtoupper($column);

        $tbl =& $this->_getTable($table);

        if (!is_array($tbl)) {
            /* error: table not found */
            return false;

        } elseif (!is_null($ftable) && !$this->isTable($ftable)) {
            /* error: table not found */
            return false;

        } elseif (!$this->isColumn($table, $column)) {
            /* error: column not found */
            return false;

        } elseif (is_null($ftable)) {
            if (isset($tbl['FOREIGN_KEYS'][$column])) {
                unset($tbl['FOREIGN_KEYS'][$column]);
                /*
                 * write protocol to keep track of changes
                 */
                $comment = "remove foreign key on column '$column' on table '$table'";
                $this->_logChanges($table, $column, $comment, 'update');
            }
            return true;

        } else {
            if (!isset($tbl['FOREIGN_KEYS']) || !is_array($tbl['FOREIGN_KEYS'])) {
                $tbl['FOREIGN_KEYS'] = array();
            }
            /* settype to STRING */
            $ftable = (string) $ftable;
            $ftable = mb_strtolower($ftable);
            $tbl['FOREIGN_KEYS'][$column] = $ftable;

            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($table, $column, "set foreign key on column '$column' on table '$table'", 'update');

            return true;
        }
    }

    /**
     * check whether a primary key exists in the current structure
     *
     * Returns bool(true) if a table named $table is listed
     * in the current structure file and its primary key is named $column.
     * Returns bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     */
    public function isPrimaryKey($table, $column)
    {
        /* settype to STRING */
        $column = (string) $column;
        if (strcasecmp($this->getPrimaryKey($table), $column) === 0) {
            return true;

        } else {
            return false;
        }
    }

    /**
     * check whether a column has a unique constraint
     *
     * Returns bool(true) if a table named $table is listed
     * in the current structure file, it has a column named $column
     * in its list of contents and this column has a unique constraint.
     * Returns bool(false) otherwise.
     * Note that this operation is not case sensitive.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     * @since   2.8.5
     * @name    DbStructure::isUnique()
     * @see     DbStructure::setUnique()
     */
    public function isUnique($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;

        } elseif (!isset($col['UNIQUE'])) {
            /* property is undefined */
            return false;

        } elseif ($col['UNIQUE'] === true || $col['UNIQUE'] === 'true') {
            return true;

        } else {
            return false;
        }
    }

    /**
     * add/remove a unique constraint on a column
     *
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * Note: you don't need to set a "unique" constraint on a
     * primary key. A "primary key" column always requires its
     * values to be unique.
     *
     * Primary keys implicitely have a unique constraint.
     *
     * @access  public
     * @param   string  $table     name of table
     * @param   string  $column    name of column
     * @param   bool    $isUnique  new value of this property
     *                  (true = use unique constraint, false = don't use constraint)
     * @return  bool
     * @since   2.9
     * @name    DbStructure::setUnique()
     * @see     DbStructure::isUnique()
     */
    public function setUnique($table, $column, $isUnique)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;
        } else {
            if ($isUnique) {
                $col['UNIQUE'] = true;
                $this->_logChanges($table, $column, "add unique constraint on '$table.$column'", 'update');
            } else {
                $col['UNIQUE'] = false;
                $this->_logChanges($table, $column, "remove unique constraint on '$table.$column'", 'update');
            }

            return true;
        }
    }

    /**
     * check whether a column is an unsigned number
     *
     * Returns bool(true) if a table named $table is listed
     * in the current structure file, it has a column named $column
     * in its list of contents and this column has the flag unsigned set
     * to bool(true).
     * Returns bool(false) otherwise.
     *
     * This function will also return bool(true), if the property "zerofill"
     * is set to true, as "zerofill" requires the "unsigned" flag to be set.
     *
     * Important note: if unsigned is not supported by your DBMS, it is
     * emulated by the framework's database API.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     * @since   2.9.6
     * @name    DbStructure::isUnsigned()
     * @see     DbStructure::setUnsigned()
     */
    public function isUnsigned($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;

        } elseif (isset($col['UNSIGNED']) && ($col['UNSIGNED'] === true || $col['UNSIGNED'] === 'true')) {
            return true;

        } elseif (isset($col['ZEROFILL']) && ($col['ZEROFILL'] === true || $col['ZEROFILL'] === 'true')) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set a column to an unsigned number
     *
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * If the type set on this column is not numeric, the function returns bool(false).
     *
     * An "unsigned" number is supposed to be interpreted as a positive value.
     * This means, with "unsigned" = true, any value lesser than 0 is invalid.
     *
     * Important note: if zerofill is not supported by your DBMS, it is
     * emulated by the framework's database API.
     *
     * If the framework's API encounters an invalid number, it returns false and
     * issues an error. Note that this is unlike MySQL, which automatically and silently
     * replaces an invalid value by 0 - which *MIGHT* lead to an error or unexpected
     * behavior of an application working on the database.
     *
     * @access  public
     * @param   string  $table       name of table
     * @param   string  $column      name of column
     * @param   bool    $isUnsigned  new value of this property
     *                               (true = use unique constraint, false = don't use constraint)
     * @return  bool
     * @since   2.9.6
     * @name    DbStructure::setUnsigned()
     * @see     DbStructure::isUnsigned()
     */
    public function setUnsigned($table, $column, $isUnsigned)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;

        } elseif (isset($col['TYPE']) && !$this->isNumber($table, $column)) {
            /* error: not a number */
            $message = "Cannot use flag unsigned on a non-numeric column " .
                "'$column' of type '{$col['TYPE']}' on table '$table'.";
            trigger_error($message, E_USER_NOTICE);
            return false;

        } else {
            /*
             * write protocol to keep track of changes
             */
            $comment = "set property 'unsigned' on column '$column' on table '$table'";
            $this->_logChanges($table, $column, $comment, 'update');

            if ($isUnsigned) {
                $col['UNSIGNED'] = true;
            } else {
                $col['UNSIGNED'] = false;
            }

            return true;
        }
    }

    /**
     * check whether a column is a number with the zerofill flag set
     *
     * Returns bool(true) if a table named $table is listed
     * in the current structure file, it has a column named $column
     * in its list of contents and this column has the flag zerofill set
     * to bool(true).
     * Returns bool(false) otherwise.
     *
     * Zerofill only makes sense, if the column has a numeric data type,
     * which has a fixed length.
     *
     * It is meant to be interpreted as follows:
     * If zerofill is set to bool(true), the number is always expanded
     * to the maximum number of digits, defined by the property "length".
     * If length is not set, it is to be ignored.
     *
     * Important note: if zerofill is not supported by your DBMS, it is
     * emulated by the framework's database API.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     * @since   2.9.6
     * @name    DbStructure::isZerofill()
     * @see     DbStructure::setZerofill()
     */
    public function isZerofill($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;

        } elseif (!isset($col['ZEROFILL'])) {
            /* property is undefined */
            return false;

        } elseif ($col['ZEROFILL'] === true || $col['ZEROFILL'] === 'true') {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set a numeric column to zerofill
     *
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * If the type set on this column is not numeric, the function returns bool(false).
     *
     * Be aware that setting "zerofill" to bool(true) will also set the property "unsigned",
     * as "zerofill" depends on this.
     *
     * Important note: if zerofill is not supported by your DBMS, it is
     * emulated by the framework's database API.
     *
     * @access  public
     * @param   string  $table       name of table
     * @param   string  $column      name of column
     * @param   bool    $isZerofill  new value of this property (true = use zerofill feature, false = don't use it)
     * @return  bool
     * @since   2.9.6
     * @name    DbStructure::setZerofill()
     * @see     DbStructure::isZerofill()
     */
    public function setZerofill($table, $column, $isZerofill)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;

        } elseif (isset($col['TYPE']) && !$this->isNumber($table, $column)) {
            /* error: not a number */
            trigger_error("Cannot use flag zerofill on a non-numeric column.", E_USER_NOTICE);
            return false;

        } else {
            /*
             * write protocol to keep track of changes
             */
            $comment = "set property 'zerofill' on column '$column' on table '$table'";
            $this->_logChanges($table, $column, $comment, 'update');

            if ($isZerofill) {
                $col['ZEROFILL'] = true;
                $col['UNSIGNED'] = true;
            } else {
                $col['ZEROFILL'] = false;
            }

            return true;
        }
    }

    /**
     * check if column has a numeric data type
     *
     * Returns bool(true) if the table and colum exist and the type of the column is numeric.
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @param   string  $table     name of table
     * @param   string  $column    name of column
     * @return  bool
     * @since   2.9.6
     */
    public function isNumber($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;

        } elseif (isset($col['TYPE'])) {
            switch (mb_strtolower($col['TYPE']))
            {
                case 'int':
                case 'integer':
                case 'float':
                case 'double':
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'bigint':
                case 'real':
                case 'decimal':
                case 'numeric':
                    return true;
                break;
                default:
                    return false;
                break;
            } /* end switch */

        } else {
            /* error: type not set */
            return false;
        }
    }

    /**
     * return a list of foreign keys defined on a table
     *
     * Returns an associative array of foreign keys.
     * If $table is not specified in the current structure file,
     * then NULL is returned instead.
     * If the table has no foreign keys, an empty array is returned.
     *
     * Note that this operation is not case sensitive.
     *
     * The returned result will look as follows:
     * <code>
     * array(
     *   'column_1' => 'table_1',
     *   'column_2' => 'table_2'
     * );
     * </code>
     *
     * Example of usage:
     * <code>
     * foreach ($structure->getForeignKeys($table) as $fColumn => $fTable)
     * {
     *     print "Column $fColumn in $table points to primary key of table $fTable.\n";
     * }
     * </code>
     *
     * @access  public
     * @param   string  $table   name of table
     * @return  array
     */
    public function getForeignKeys($table)
    {
        $tbl =& $this->_getTable($table);

        if (!is_array($tbl)) {
            $message = "Unable to get foreign keys. There is no table '{$table}' in current structure file.";
            trigger_error($message, E_USER_WARNING);
            /* error: table not found */
            return false;

        } elseif (!isset($tbl['FOREIGN_KEYS'])) {
            return array();

        } else {
            return $tbl['FOREIGN_KEYS'];
        }
    }

    /**
     * get the primary key of a table
     *
     * Returns the name of the primary key column of $table as a
     * lower-cased string.
     * Returns NULL and issues an E_USER_WARNING if $table is not a listed table in the current
     * structure file.
     * Returns NULL and issues an E_USER_WARNING if there is no primary key for $table.
     *
     * @access  public
     * @param   string  $table   name of table
     * @return  string
     */
    public function getPrimaryKey($table)
    {
        $tbl =& $this->_getTable($table);

        if (!is_array($tbl)) {
            $message = "Unable to get primary key. There is no table '{$table}' in current structure file.";
            trigger_error($message, E_USER_WARNING);
            /* error: table not found */
            return null;

        } elseif (!isset($tbl['PRIMARY_KEY'])) {
            trigger_error("Table '{$table}' has no primary key declaration.", E_USER_WARNING);
            return null;

        } else {
            return mb_strtolower($tbl['PRIMARY_KEY']);
        }
    }

    /**
     * set the primary key of a table
     *
     * Select $column as the primary key of $table.
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     */
    public function setPrimaryKey($table, $column)
    {
        /* settype to STRING */
        $column = (string) $column;
        $column = mb_strtolower($column);

        $tbl =& $this->_getTable($table);

        if (!is_array($tbl)) {
            /* error: table not found */
            return false;

        } elseif (!$this->isColumn($table, $column)) {
            /* error: column not found */
            return false;

        } else {
            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($table, $column, "make column '$column' the primary key for table '$table'", 'update');

            $tbl['PRIMARY_KEY'] = $column;

            return true;
        }
    }

    /**
     * get the user description of a column
     *
     * Returns the description text (=comment) of $column in $table as a string or bool(false) if
     * none exists.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  string
     * @since   2.8.6
     * @name    DbStructure::getDescription()
     * @see     DbStructure::setDescription()
     */
    public function getDescription($table = null, $column = null)
    {
        /*
         * get description on database
         */
        if (is_null($table)) {

            if (!isset($this->content['DESCRIPTION'])) {
                return false;

            } else {
                return (string) $this->content['DESCRIPTION'];
            }

        /*
         * get description on table
         */
        } elseif (is_null($column)) {
            $tbl =& $this->_getTable($table);

            if (!is_array($tbl)) {
                /* error: table not found */
                return false;
            } elseif (!isset($tbl['DESCRIPTION'])) {
                return false;
            } else {
                return (string) $tbl['DESCRIPTION'];
            }

        /*
         * get description on column
         */
        } else {
            $col =& $this->_getColumn($table, $column);

            if (!is_array($col)) {
                /* error: table or column not found */
                return false;
            } elseif (!isset($col['DESCRIPTION'])) {
                return false;
            } else {
                return (string) $col['DESCRIPTION'];
            }
        } /* end if */
    }

    /**
     * set the description property of a column
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * A description is the "label" of a column. A human-readable
     * small phrase that should tell readers what it is.
     *
     * Note: though you may take any length of text you want,
     * you are best adviced to use really short phrases like
     * 'name', 'address' or 'phone' as a description.
     *
     * Note: to set the property description of a table instead
     * of a column, set the argument $column to NULL.
     * To set the property of a database,
     * set the argument $table to NULL.
     *
     * @access  public
     * @param   string  $table        name of table
     * @param   string  $column       name of column (set to NULL to modify the description property of a table)
     * @param   string  $description  new value of this property
     * @return  bool
     * @since   2.9
     * @name    DbStructure::setDescription()
     * @see     DbStructure::getDescription()
     */
    public function setDescription($table, $column, $description)
    {
        /*
         * set description on database
         */
        if (is_null($table)) {

            $this->content['DESCRIPTION'] = (string) $description;
            return true;

        /*
         * set description on table
         */
        } elseif (is_null($column)) {
            $tbl =& $this->_getTable($table);

            if (!is_array($tbl)) {
                /* error: table not found */
                return false;
            } else {
                $tbl['DESCRIPTION'] = (string) $description;
                return true;
            }

        /*
         * set description on column
         */
        } else {
            $col =& $this->_getColumn($table, $column);

            if (!is_array($col)) {
                /* error: table or column not found */
                return false;
            } else {
                $col['DESCRIPTION'] = (string) $description;
                return true;
            }
        } /* end if */
    }

    /**
     * get the maximum length of a column as specified in the structure
     *
     * Returns the 'length' attribute of $column in $table.
     * Returns int(0) if there is no $table, or the table has no
     * column named $column, or the column does not have a 'length'
     * attribute.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  int
     * @name    DbStructure::getLength()
     * @see     DbStructure::setLength()
     */
    public function getLength($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;
        } else {
            if (isset($col['LENGTH'])) {
                $length = $col['LENGTH'];
                if (!is_numeric($length)) {
                    $message = "Property 'length' has an invalid value '{$length}' in table ".
                        "'{$table}' column '{$column}'.";
                    trigger_error($message, E_USER_NOTICE);
                    $length = 0;
                } else {
                    /* settype to INTEGER */
                    $length = (int) $length;
                }
            } else {
                /* property is undefined, defaults to 0 */
                $length = 0;
            }
            return $length;
        }
    }

    /**
     * get the maximum length of the decimal fraction of a float
     *
     * Returns the 'precision' attribute of $column in $table.
     * Returns int(0) if there is no $table, or the table has no
     * column named $column, or the column does not have a 'precision'
     * attribute.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  int
     * @since   2.9.7
     * @name    DbStructure::getPrecision()
     * @see     DbStructure::getLength()
     * @see     DbStructure::setLength()
     */
    public function getPrecision($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return 0;
        } else {
            if (isset($col['PRECISION'])) {
                $precision = $col['PRECISION'];
                if (!is_numeric($precision)) {
                    $message = "Property 'precision' has an invalid value '{$precision}' in table " .
                        "'{$table}' column '{$column}'.";
                    trigger_error($message, E_USER_NOTICE);
                    return 0;

                } else {
                    /* settype to INTEGER */
                    $precision = (int) $precision;
                }

                if ($precision > 0) {
                    return $precision;

                } else {
                    return 0;
                }

            } else {
                /* error - property is undefined */
                return 0;
            }
        }
    }

    /**
     * set the maximum length property of a column
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * The argument $length must be a positive integer.
     *
     * The argument $precision has been added in version 2.9.7.
     * It applies to floating point values only and defines the
     * length of the decimal fraction of the input number.
     *
     * @access  public
     * @param   string  $table      name of table
     * @param   string  $column     name of column
     * @param   int     $length     new value of this property
     * @param   int     $precision  applies to type float only
     * @return  bool
     * @since   2.9
     * @name    DbStructure::setLength()
     * @see     DbStructure::getLength()
     * @see     DbStructure::getPrecision()
     */
    public function setLength($table, $column, $length, $precision = -1)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;

        } elseif (is_int($length) && $length > 0) {
            $col['LENGTH'] = $length;

            if (is_int($precision) && $precision >= 0) {
                $col['PRECISION'] = $precision;

            } elseif (isset($col['PRECISION'])) {
                unset($col['PRECISION']);
            }

            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($table, $column, "set maximum length on column '$column' on table '$table'", 'update');

            return true;

        } else {
            trigger_error("Invalid length argument '{$length}'. Positive integer expected.", E_USER_WARNING);
            /* error: invalid argument */
            return false;

        }
    }

    /**
     * get the data type of a field as specified in the structure
     *
     * Returns the 'type' attribute of $column in $table as a lower-cased string.
     * Returns string("") if there is no $table, or the table has no
     * column named $column, or the column does not have a 'type'
     * attribute.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  string
     * @name    DbStructure::getType()
     * @see     DbStructure::setType()
     */
    public function getType($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return '';
        } elseif (!isset($col['TYPE'])) {
            /* property is undefined */
            return '';
        } else {
            return mb_strtolower($col['TYPE']);
        }
    }

    /**
     * set the type of a field as specified in the structure
     *
     * Note: this function does not check if the provided
     * string is a valid type name.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @param   midex   $value   new value of this property
     * @return  bool
     * @since   2.9 RC3
     * @name    DbStructure::setType()
     * @see     DbStructure::getType()
     */
    public function setType($table, $column, $value)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;

        } else {
            $col['TYPE'] = $value;

            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($table, $column, "set type of column '$column' on table '$table'", 'update');

            return true;
        }
    }

    /**
     * get the properties of a field of type 'image'
     *
     * Returns an array of the following values:
     * <code>
     * array (
     *    'size'       : int,  // maximum size in bytes
     *    'width'      : int,  // horizontal dimension in px
     *    'height'     : int,  // vertical dimension in px
     *    'ratio'      : bool, // keep aspect-ratio (true=yes, false=no)
     *    'background' : array(red, green, blue) // color of canvas
     * )
     * </code>
     * If one of the values above does not exist,
     * the field is set to 'null'.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  array
     * @since   2.8.9
     * @name    DbStructure::getImageSettings()
     * @see     DbStructure::setImageSettings()
     */
    public function getImageSettings($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;
        }

        if (strcasecmp($this->getType($table, $column), 'image') === 0) {
            $result = array();
            /*
             * here: maximum size in bytes
             *
             * Note: the size after (!) the conversion might
             * be different from the size of the uploaded file.
             */
            if (isset($col['LENGTH'])) {
                $result['size'] = (int) $col['LENGTH'];
            } else {
                $result['size'] = null;
            }
            if (isset($col['WIDTH'])) {
                $result['width'] = (int) $col['WIDTH'];
            } else {
                $result['width'] = null;
            }
            if (isset($col['HEIGHT'])) {
                $result['height'] = (int) $col['HEIGHT'];
            } else {
                $result['height'] = null;
            }
            if (isset($col['RATIO'])) {
                $result['ratio'] = (bool) $col['RATIO'];
            } else {
                $result['ratio'] = null;
            }
            if (isset($col['BACKGROUND'])) {
                $result['background'] = $col['BACKGROUND'];
                if (!is_array($result['background']) || count($result['background']) !== 3) {
                    $result['background'] = null;
                } else {
                    settype($result['background'][0], 'int');
                    settype($result['background'][1], 'int');
                    settype($result['background'][2], 'int');
                }
            } else {
                $result['background'] = null;
            }
            return $result;
        } else {
            return false;
        }
    }

    /**
     * set the properties of a field of type 'image'
     *
     * The argument $settings is an array of the following values:
     * <code>
     * array (
     *    'size'       : int,  // maximum size in bytes
     *    'width'      : int,  // horizontal dimension in px
     *    'height'     : int,  // vertical dimension in px
     *    'ratio'      : bool, // keep aspect-ratio (true=yes, false=no)
     *    'background' : array(red, green, blue) // color of canvas
     * )
     * </code>
     * If one of the values above does not exist,
     * the field is set to 'null'.
     *
     * @access  public
     * @param   string  $table     name of table
     * @param   string  $column    name of column
     * @param   array   $settings  new set of image settings
     * @return  array
     * @since   2.9
     * @name    DbStructure::setImageSettings()
     * @see     DbStructure::getImageSettings()
     */
    public function setImageSettings($table, $column, array $settings)
    {
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;
        }

        if (strcasecmp($this->getType($table, $column), 'image') === 0) {
            if (isset($settings['size']) && $settings['size'] > 0) {
                $col['LENGTH'] = (int) $settings['size'];
            } else {
                $col['LENGTH'] = null;
            }
            if (isset($settings['width']) && $settings['width'] > 0) {
                $col['WIDTH'] = (int) $settings['width'];
            } else {
                $col['WIDTH'] = null;
            }
            if (isset($settings['height']) && $settings['height'] > 0) {
                $col['HEIGHT'] = (int) $settings['height'];
            } else {
                $col['HEIGHT'] = null;
            }
            if (isset($settings['ratio'])) {
                $col['RATIO'] = (bool) $settings['ratio'];
            } else {
                $col['RATIO'] = null;
            }
            if (isset($settings['background'])) {
                if (is_array($settings['background']) && count($settings['background']) === 3) {
                    $col['BACKGROUND'] = array();
                    $col['BACKGROUND'][] = (int) array_shift($settings['background']);
                    $col['BACKGROUND'][] = (int) array_shift($settings['background']);
                    $col['BACKGROUND'][] = (int) array_shift($settings['background']);
                } else {
                    $col['BACKGROUND'] = null;
                }
            } else {
                $col['BACKGROUND'] = null;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * get a list of all columns that match a certain type
     *
     * Returns false if the table does not exist.
     * Otherwise it returns a list of column names.
     *
     * @access  public
     * @param   string  $table  name of table
     * @param   string  $type   datatype ('string', 'text', 'int', ...)
     * @return  array
     * @since   2.9 RC3
     */
    public function getColumnsByType($table, $type)
    {
        /* settype to STRING */
        $table = (string) $table;
        $type  = (string) $type;

        /* get list of all columns */
        $columnList = $this->getColumns($table);
        if (is_array($columnList)) {
            $result = array();
            foreach ($columnList as $column)
            {
                $columnType = $this->getType($table, $column);
                if (is_string($columnType) && strcasecmp($columnType, $type) === 0) {
                    $result[] = $column;
                }
            } /* end foreach */
            return $result;
        } else {
            /* error: table not found */
            return false;
        }
    }

    /**
     * get a list of all columns that contain blobs
     *
     * This function provides a list of all columns,
     * which are of type "image" or "file" in $table
     * as a numeric array of strings.
     *
     * It returns bool(false) if the table does not
     * exist.
     *
     * @access  public
     * @param   string  $table   name of table
     * @return  array
     * @since   2.8.9
     */
    public function getFiles($table)
    {
        /* settype to STRING */
        $table  = (string) $table;
        /* get list of all columns */
        $columnList = $this->getColumns($table);
        if (is_array($columnList)) {
            $result = array();
            foreach ($columnList as $column)
            {
                $type = $this->getType($table, $column);
                if (is_string($type) && ($type === 'image' || $type === 'file')) {
                    $result[] = $column;
                }
            } /* end foreach */
            return $result;
        } else {
            /* error: table not found */
            return false;
        }
    }

    /**
     * get the default value of a field as specified in the structure
     *
     * Returns the default value of $column in $table (where available).
     * The type of the default value returned depends on the type of the
     * column.
     *
     * Returns constant NULL (not bool(false)) if the default value is not
     * specified, or the table/column does not exist.
     * Use is_null($result) to test for existance, don't use empty($result).
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  mixed
     */
    public function getDefault($table, $column)
    {
        $property =& $this->_getColumnProperty($table, $column, 'DEFAULT');
        $type = $this->getType($table, $column);

        if (is_null($property)) {
            /* error: table or column not found or property undefined */
            return null;
        } elseif (is_array($property) && $type !== 'select' && $type !== 'array' && $type !== 'set') {
            if ($type !== 'string') {
                return false;
            }
            // reg-exp matches '{FOO}'
            $regEx = '/^' . YANA_LEFT_DELIMITER_REGEXP . '\$\w+' . YANA_RIGHT_DELIMITER_REGEXP . '$/';
            foreach ($property as $default)
            {
                if (empty($default)) {
                    continue;
                }
                if (preg_match($regEx, $default)) {
                    continue;
                }
                return \Yana\Data\StringValidator::sanitize($default, 0, \Yana\Data\StringValidator::TOKEN);
            } // end foreach
            return null;
        } else {
            return $property;
        }
    }

    /**
     * set the default value of a field as specified in the structure
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @param   mixed   $value   new value of this property
     * @return  bool
     * @since   2.8.9
     */
    public function setDefault($table, $column, $value)
    {
        if ($this->_setColumnProperty($table, $column, 'DEFAULT', $value)) {
            /*
             * write protocol to keep track of changes
             */
            $this->_logChanges($table, $column, "set default value of column '$column' on table '$table'", 'update');

            return true;
        } else {
            return false;
        }
    }

    /**
     * get the names of all columns in a table
     *
     * Returns a numeric array of all columns in $table.
     * Issues an E_USER_NOTICE and returns an empty array, if $table does not exist
     * in current structure file.
     *
     * @access  public
     * @param   string  $table  name of table
     * @return  array
     */
    public function getColumns($table)
    {
        $tbl =& $this->_getTable($table);

        if (!is_array($tbl)) {
            trigger_error("The table '{$table}' does not exist.", E_USER_NOTICE);
            /* error: table not found */
            return array();
        } else {
            return array_keys(array_change_key_case($tbl['CONTENT'], CASE_LOWER));
        }
    }

    /**
     * get the name of the table, a foreign key points to
     *
     * Returns the lower-cased table name. If the foreign
     * key does not exist, an empty string is returned.
     *
     * @access  public
     * @param   string  $table       name of base table
     * @param   string  $foreignKey  name of column containing the foreign key
     * @return  string
     */
    public function getTableByForeignKey($table, $foreignKey)
    {
        $foreignKey = mb_strtoupper("$foreignKey");

        $tbl =& $this->_getTable($table);

        if (!is_array($tbl)) {
            /* error: table not found */
            return "";
        } else {
            return mb_strtolower(@$tbl['FOREIGN_KEYS'][$foreignKey]);
        }
    }

    /**
     * check whether the structure defines the "USE_STRICT" setting as bool(true)
     *
     * Returns bool(true) if the "USE_STRICT" property
     * of the database is true and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     * @name    DbStructure::isStrict()
     * @see     DbStructure::setStrict()
     * @deprecated  since 3.1.0 - use YANA_DB_STRICT instead
     */
    public function isStrict()
    {
        if (YANA_DB_STRICT) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * select whether the structure should use the "strict" directive
     *
     * Set the property "USE_STRICT" of the database file to
     * the argument $isStrict;
     *
     * @access  public
     * @param   bool  $isStrict  new value of this property
     * @since   2.9
     * @name    DbStructure::setStrict()
     * @see     DbStructure::isStrict()
     * @deprecated  since 3.1.0 - use YANA_DB_STRICT instead
     */
    public function setStrict($isStrict)
    {
        if ($isStrict) {
            $this->content['USE_STRICT'] = true;
        } else {
            $this->content['USE_STRICT'] = false;
        }
    }

    /**
     * get a list of all tables in the current database
     *
     * Returns a numeric array of all tables in the current structure file.
     * Issues an E_USER_NOTICE and returns an empty array, if the list of
     * tables is empty.
     *
     * @access  public
     * @return  array
     */
    public function getTables()
    {
        if (!isset($this->content['TABLES'])) {
            trigger_error("The list of tables is empty.", E_USER_NOTICE);
            return array();
        } else {
            return array_keys(array_change_key_case($this->content['TABLES'], CASE_LOWER));
        }
    }

    /**
     * get a list of all indexed columns in a table
     *
     * Returns a numeric array of all columns in $table, that are marked with the
     * option INDEX => true (which means, all columns that have an index).
     *
     * @access  public
     * @param   string  $table  name of table
     * @return  array
     * @since   2.8.5
     */
    public function getIndexes($table)
    {
        if (!isset($this->content['TABLES'])) {
            trigger_error("The list of tables is empty.", E_USER_NOTICE);
            return array();
        } elseif (!$this->isTable($table)) {
            /* table does not exist */
            trigger_error("The table '{$table}' does not exist.", E_USER_NOTICE);
            return array();
        } else {
            $result = array();
            $table = mb_strtoupper($table);
            foreach ($this->getColumns($table) as $columnName)
            {
                if ($this->hasIndex($table, $columnName) === true) {
                    array_push($result, $columnName);
                }
            } /* end foreach */
            return $result;
        }
    }

    /**
     * get a list of all unique columns of a table
     *
     * Returns a numeric array of all columns in $table, that are marked with the
     * option UNQIUE => true (which means, all columns that have an unique constraint).
     *
     * @access  public
     * @param   string  $table  name of table
     * @return  array
     * @since   2.8.5
     */
    public function getUniqueConstraints($table)
    {
        if (!isset($this->content['TABLES'])) {
            trigger_error("The list of tables is empty.", E_USER_NOTICE);
            return array();
        } elseif (!$this->isTable($table)) {
            /* table does not exist */
            trigger_error("The table '{$table}' does not exist.", E_USER_NOTICE);
            return array();
        } else {
            $result = array();
            $table = mb_strtoupper($table);
            foreach ($this->getColumns($table) as $columnName)
            {
                if ($this->isUnique($table, $columnName) === true) {
                    $result[] = $columnName;
                }
            } /* end foreach */
            return $result;
        }
    }

    /**
     * get the database name associated with a table
     *
     * Returns the associated database name as string or null if no
     * association is found.
     *
     * This is meant to be used when one structure file includes
     * another and you need to know in which file a certain table is
     * defined.
     *
     * Currently this is used by the FileDB driver to determine in
     * which directory a table's source file is to be placed.
     *
     * @access  public
     * @param   string  $table  name of table
     * @return  string
     * @ignore
     */
    public function getAssociation($table)
    {
        assert('is_string($table)', 'Wrong argument type for argument 1. String expected.');
        $table = mb_strtoupper($table);

        /*
         * 1) get table property
         */
        if (@isset($this->content['ASSOCIATIONS'][$table])) {
            return $this->content['ASSOCIATIONS'][$table];

        /*
         * 2) none available
         */
        } else {
            return null;
        }
    }

    /**
     * check if a table is associated with another data source
     *
     * Return bool(true) if an association on the given table exists
     * and bool(false) otherwise.
     *
     * @access  public
     * @param   string  $table  name of table
     * @return  bool
     * @ignore
     */
    public function hasAssociation($table)
    {
        assert('is_string($table)', 'Wrong argument type for argument 1. String expected.');

        if (!isset($this->content['ASSOCIATIONS'])) {
            return false;

        } elseif (isset($this->content['ASSOCIATIONS'][mb_strtoupper($table)])) {
            return true;

        } else {
            return false;
        }
    }

    /**
     * set the database name associated with a table
     *
     * This is meant to be used when one structure file includes
     * another and you need to know in which file a certain table is
     * defined.
     *
     * @access  public
     * @param   string  $dbName  name of database
     * @param   string  $table   name of table
     * @return  bool
     * @ignore
     */
    public function setAssociation($dbName, $table)
    {
        assert('is_string($dbName)', 'Wrong argument type for argument 1. String expected.');
        assert('is_string($table)', 'Wrong argument type for argument 2. String expected.');

        if (!isset($this->content['ASSOCIATIONS']) || !is_array($this->content['ASSOCIATIONS'])) {
            $this->content['ASSOCIATIONS'] = array();
        }

        /*
         * 1) set table property
         */
        if ($table !== "") {
            $table = mb_strtoupper("$table");
            $this->content['ASSOCIATIONS'][$table] = (string) $dbName;
            return true;

        /*
         * 2) error - invalid input
         */
        } else {
            return false;
        }
    }

    /**
     * unset the database name associated with a table
     *
     * Removes an association.
     *
     * Returns bool(true) on success and bool(false),
     * if no such association exists.
     *
     * @access  public
     * @param   string  $table  name of table
     * @return  bool
     * @ignore
     */
    public function unsetAssociation($table = "")
    {
        assert('is_string($table)', 'Wrong argument type for argument 1. String expected.');

        if ($table == "") {
            if (isset($this->content['ASSOCIATIONS'])) {
                unset($this->content['ASSOCIATIONS']);
                return true;
            } else {
                return false;
            }

        } else {
            $table = mb_strtoupper("$table");
            if (isset($this->content['ASSOCIATIONS'][$table])) {
                unset($this->content['ASSOCIATIONS'][$table]);
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * get all constraints for an address
     *
     * Retrieves all "constraint" entries that apply to the given
     * operation on the dataset and returns the results as an numeric
     * array.
     *
     * @access  public
     * @param   string  $table      name of table
     * @param   array   $columns    list of columns
     * @return  array
     * @since   version 2.8.4
     * @name    DbStructure::getConstraint()
     * @see     DbStructure::setConstraint()
     */
    public function getConstraint($table, array $columns = array())
    {
        return $this->getFields('constraint', '', $table, $columns, true);
    }

    /**
     * set constraint
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * Note: This function will check some syntax of your code.
     * However, it can't ensure that your codes makes sense.
     * So keep in mind that it is your job in the first place
     * to ensure the constraint is valid!
     *
     * BE WARNED: As always - do NOT use this function with any
     * unchecked user input.
     *
     * @access  public
     * @param   string  $constraint  PHP-Code
     * @param   string  $table       name of table
     * @param   string  $column      name of column
     * @return  bool
     * @since   2.9
     * @name    DbStructure::setConstraint()
     * @see     DbStructure::getConstraint()
     */
    public function setConstraint($constraint, $table, $column = null)
    {
        assert('is_string($constraint)', ' Invalid argument $constraint: string expected');
        assert('is_string($table)', ' Invalid argument $table: string expected');
        assert('is_null($column) || is_string($column)', ' Invalid argument $column: string expected');

        if (!preg_match(\Yana\Db\Helpers\ConstraintCollection::CONSTRAINT_SYNTAX, $constraint)) {
            trigger_error("Syntax error in constraint: '".trim($constraint)."'.", E_USER_WARNING);
            return false;
        }

        if (!is_null($column)) {
            $col =& $this->_getColumn($table, $column);
            if (!is_array($col)) {
                /* error: table or column not found */
                return false;
            } else {
                $col['CONSTRAINT'] = $constraint;
                return true;
            }
        } else {
            $tbl =& $this->_getTable($table);
            if (!is_array($tbl)) {
                /* error: table not found */
                return false;
            } else {
                $tbl['CONSTRAINT'] = $constraint;
                return true;
            }
        }

    }

    /**
     * get all triggers for an address
     *
     * Retrieves all "trigger" entries that apply to the given operation on the dataset
     * and returns the results as an numeric array.
     *
     * @access  public
     * @param   string  $operation  one of insert, update, delete
     * @param   string  $table      name of table
     * @param   array   $columns    list of columns
     * @return  array
     * @since   2.8.5
     * @name    DbStructure::getTrigger()
     * @see     DbStructure::setTrigger()
     */
    public function getTrigger($operation, $table, array $columns = array())
    {
        return $this->getFields('trigger', $operation, $table, $columns);
    }

    /**
     * set trigger
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * BE WARNED: As always - do NOT use this function with any
     * unchecked user input.
     *
     * @access  public
     * @param   string  $trigger    PHP-Code
     * @param   string  $operation  one of insert, update, delete
     * @param   string  $table      name of table
     * @param   string  $column     name of column
     * @return  bool
     * @since   2.9
     * @name    DbStructure::setTrigger()
     * @see     DbStructure::getTrigger()
     */
    public function setTrigger($trigger, $operation, $table, $column = null)
    {
        assert('is_string($name)', ' Wrong type for argument 1. String expected');
        assert('is_string($operation)', ' Wrong type for argument 2. String expected');
        assert('is_string($table) || is_null($table)', ' Wrong type for argument 3. String expected');
        assert('is_string($column) || is_null($column)', ' Wrong type for argument 4. String expected');

        $operation = mb_strtoupper("$operation");
        assert('preg_match("/^(BEFORE|AFTER)_(INSERT|UPDATE|DELETE)$/", $operation)', ' Invalid operation');

        if (!is_null($column)) {
            $col =& $this->_getColumn($table, $column);
            if (!is_array($col)) {
                /* error: table or column not found */
                return false;
            } else {
                if (!isset($col['TRIGGER']) || !is_array($col['TRIGGER'])) {
                    $col['TRIGGER'] = array();
                }
                $col['TRIGGER'][$operation] = $trigger;
                return true;
            }
        } else {
            $tbl =& $this->_getTable($table);
            if (!is_array($tbl)) {
                /* error: table not found */
                return false;
            } else {
                if (!isset($tbl['TRIGGER']) || !is_array($tbl['TRIGGER'])) {
                    $tbl['TRIGGER'] = array();
                }
                $tbl['TRIGGER'][$operation] = $trigger;
                return true;
            }
        }

    }

    /**
     * check whether the "READONLY" flag is set to bool(true)
     *
     * Returns bool(true) if:
     * <ol>
     *    <li>database is set to 'readonly=true', or</li>
     *    <li>$table is set to 'readonly=true', or</li>
     *    <li>$column in $table is set to 'readonly=true'</li>
     * </ol>
     *
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     *
     * @see     DbStructure::isVisible()
     * @see     DbStructure::isEditable()
     * @name    DbStructure::isReadonly()
     */
    public function isReadonly($table = "", $column = "")
    {
        /* 1) the database is set to readonly */
        if (!empty($this->content['READONLY'])) {
            return true;
        }

        /* 2) the column is set to readonly */
        if (!empty($table) && !empty($column)) {
            $col =& $this->_getColumn($table, $column);

            /* error: table or column not found */
            if (!is_array($col)) {
                return false;

            /* property is true */
            } elseif (!empty($col['READONLY'])) {
                return true;

            /* property is undefined */
            } else {
                /* intentionally left blank */
            }
        }

        /* 3) the table is set to readonly */
        if (!empty($table)) {
            $tbl =& $this->_getTable($table);

            /* error: table not found */
            if (!is_array($tbl)) {
                return false;

            /* property is true */
            } elseif (!empty($tbl['READONLY'])) {
                return true;

            /* property is undefined */
            } else {
                /* intentionally left blank */
            }
        }

        /* no readonly flag has been set */
        return false;
    }

    /**
     * set the "readonly" property
     *
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * @access  public
     * @param   bool    $isReadonly  new value of this property
     * @param   string  $table       name of table
     * @param   string  $column      name of column
     * @return  bool
     *
     * @see     DbStructure::isReadonly()
     * @name    DbStructure::setReadonly()
     */
    public function setReadonly($isReadonly, $table = null, $column = null)
    {
        if (!is_null($column)) {
            $col =& $this->_getColumn($table, $column);
            if (!is_array($col)) {
                /* error: table or column not found */
                return false;
            } else {
                $readonly =& $col['READONLY'];
            }
        } elseif (!is_null($table)) {
            $tbl =& $this->_getTable($table);
            if (!is_array($tbl)) {
                /* error: table not found */
                return false;
            } else {
                $readonly =& $tbl['READONLY'];
            }
        } else {
            $readonly =& $this->content['READONLY'];
        }

        if ($isReadonly) {
            $readonly = true;
        } else {
            $readonly = false;
        }
    }

    /**
     * check whether the column should be visible
     *
     * Returns bool(true) if:
     * <ol>
     *    <li>the table and column exist AND</li>
     *    <li>the column setting DISPLAY.HIDDEN is not set, or false AND</li>
     *    <li>the column setting DISPLAY.HIDDEN.$action is not set OR</li>
     *    <li>the column setting DISPLAY.HIDDEN.$action exists and is set to false.</li>
     * </ol>
     * Returns bool(false) otherwise.
     *
     * If the argument $action is not provided, the last two do not apply.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @param   string  $action  namespace of form ('SELECT', 'EDIT', 'NEW', 'SEARCH')
     * @return  bool
     * @since   2.9.0 RC1
     * @see     DbStructure::isEditable()
     * @see     DbStructure::isReadonly()
     * @name    DbStructure::isVisible()
     */
    public function isVisible($table, $column, $action = "")
    {
        $action = mb_strtoupper("$action");

        $col =& $this->_getColumn($table, $column);

        /*
         * 1) error - table / column do not exist
         */
        if (!is_array($col)) {
            return false;

        /*
         * 2) display.hidden.$action property is set
         */
        } elseif (!empty($action) && isset($col['DISPLAY']['HIDDEN'][$action])) {
            /* 2.1) decide by permission */
            if (is_numeric($col['DISPLAY']['HIDDEN'][$action])) {
                return ($GLOBALS['YANA']->getVar('PERMISSION') >= $col['DISPLAY']['HIDDEN'][$action]);
            /* 2.2) decide by structure */
            } elseif ($col['DISPLAY']['HIDDEN'][$action] === false) {
                return true;
            } else {
                return false;
            }

        /*
         * 3) display.hidden property is set
         */
        } elseif (isset($col['DISPLAY']['HIDDEN']) && is_scalar($col['DISPLAY']['HIDDEN'])) {
            /* 3.1) decide by permission */
            if (is_numeric($col['DISPLAY']['HIDDEN'])) {
                return ($GLOBALS['YANA']->getVar('PERMISSION') >= $col['DISPLAY']['HIDDEN']);
            /* 3.2) decide by structure */
            } elseif ($col['DISPLAY']['HIDDEN'] === false) {
                return true;
            } else {
                return false;
            }

        /*
         * 4) search form handling
         */
        } elseif ($action === 'SEARCH' && is_array($col)) {
            switch ($this->getType($table, $column))
            {
                case 'array': case 'set': case 'image': case 'file':
                    return false;
                break;
                default:
                    return true;
                break;
            }

        /*
         * 5) display.hidden is undefined, defaults to bool(true)
         */
        } else {
            return true;
        }
    }

    /**
     * select whether the column should be visible
     *
     * This sets the property "display.hidden" of the column
     * to bool(true), if $isVisible is bool(false) and vice versa.
     *
     * The argument $isVisible may also be an integer of 0 through 100.
     * If so, this value will be compared with the security level
     * of the user and the column will be visible, if the user
     * has a level of permission of $isVisible or higher.
     * E.g. if $isVisible is set to 100, the column will only be
     * visible to an administrator, while setting it to 30 will make
     * it visible to administrators and all other users, which at
     * least have a security level of 30 or above.
     *
     * This function returns bool(true) on success and bool(false)
     * on error.
     *
     * Note: if you don't provide the attribute $action then
     * settings will apply to all actions. Otherwise you need
     * to set the display property for all actions separately.
     *
     * @access  public
     * @param   bool|int  $isVisible  new value of this property
     * @param   string    $table      name of table
     * @param   string    $column     name of column
     * @param   string    $action     namespace of form ('SELECT', 'EDIT', 'NEW', 'SEARCH')
     * @return  bool
     * @since   2.9
     * @see     DbStructure::isVisible()
     * @name    DbStructure::setVisible()
     */
    public function setVisible($isVisible, $table, $column, $action = "")
    {
        $action = mb_strtoupper("$action");

        $col =& $this->_getColumn($table, $column);

        /*
         * 1) error table.column does not exist
         */
        if (!is_array($col)) {
            return false;
        } else {
            if (!isset($col['DISPLAY'])) {
                $col['DISPLAY'] = array();
            }
            if (!isset($col['DISPLAY']['HIDDEN'])) {
                $col['DISPLAY']['HIDDEN'] = array();
            }
            $prop =& $col['DISPLAY']['HIDDEN'];

            /*
             * 2.1) set property for ALL actions
             */
            if (!empty($action)) {
                if (!is_array($prop)) {
                    $prop = array();
                }
                if (is_int($isVisible)) {
                    if ($isVisible > -1 && $isVisible <= 100) {
                        $prop[$action] = (int) $isVisible;
                    } else {
                        $message = "Invalid argument 1. Index '{$isVisible}' out of bounds [0,100].";
                        trigger_error($message, E_USER_WARNING);
                        return false;
                    }
                } elseif ($isVisible) {
                    $prop[$action] = false;
                } else {
                    $prop[$action] = true;
                }
                return true;

            /*
             * 2.2) set property for ONE action
             */
            } else {
                if (is_int($isVisible)) {
                    if ($isVisible > -1 && $isVisible <= 100) {
                        $prop = (int) $isVisible;
                    } else {
                        $message = "Invalid argument 1. Index '{$isVisible}' out of bounds [0,100].";
                        trigger_error($message, E_USER_WARNING);
                        return false;
                    }
                } elseif ($isVisible) {
                    $prop = false;
                } else {
                    $prop = true;
                }
                return true;
            }
        } /* end if */
    }

    /**
     * check whether the column has a list-style type
     *
     * Returns bool(true) if:
     * <ol>
     *    <li>the table and column exist AND</li>
     *    <li>the column's type is 'array' AND</li>
     *    <li>the column setting DISPLAY.NUMERIC is set to 'true'</li>
     * </ol>
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     * @since   2.9.4
     */
    public function isNumericArray($table, $column)
    {
        $col =& $this->_getColumn($table, $column);

        /*
         * 1) error - table / column do not exist
         */
        if (!is_array($col)) {
            return false;

        /*
         * 2) is no array
         */
        } elseif (!$this->getType($table, $column) === 'array') {
            return false;

        /*
         * 3) setting 'numeric' found
         */
        } elseif (isset($col['DISPLAY']['NUMERIC'])) {
            if (is_string($col['DISPLAY']['NUMERIC'])) {
                if (strcasecmp($col['DISPLAY']['NUMERIC'], 'true') === 0) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($col['DISPLAY']['NUMERIC'] == true) {
                return true;
            } else {
                return false;
            }

        /*
         * 4) setting 'numeric' not found (return default)
         *
         * Note: defaults to false.
         */
        } else {
            return false;
        }
    }

    /**
     * set's the type of the column to be a numeric array
     *
     * This sets the type property of the column to 'array'
     * and sets the display property of the column to
     * 'numeric'.
     * Note that the display property is interpreted by
     * Yana's automated form generator only.
     *
     * Calling this function with $isNumeric set to
     * bool(false) will NOT reset the type of the column.
     * It will just set the display property.
     *
     * Note: numeric arrays are just like standard arrays,
     * expect that the array's keys are not displayed in
     * automatically generated forms, so that they appear
     * to the user as simple lists. In cases where the
     * array keys do not matter, this might be easier for
     * users to read an edit.
     *
     * This also means, that the array's keys are also
     * generated automatically by such forms; you don't
     * have precise control over the keys.
     * Use this feature for lists, where you just don't
     * care about the keys of the array.
     *
     * @access  public
     * @param   string  $table      name of table
     * @param   string  $column     name of column
     * @param   bool    $isNumeric  turn on / off
     * @return  bool
     * @since   2.9.4
     */
    public function setNumericArray($table, $column, $isNumeric = true)
    {
        $col =& $this->_getColumn($table, $column);

        /*
         * 1) error - table / column do not exist
         */
        if (!is_array($col)) {
            return false;
        }

        /*
         * 2) set numeric array
         */
        if ($isNumeric) {
            if (!$this->setType($table, $column, 'array')) {
                return false;
            }
            if (!isset($col['DISPLAY'])) {
                $col['DISPLAY'] = array();
            }
            $col['DISPLAY']['NUMERIC'] = true;

        /*
         * 3) unset numeric array
         */
        } else {
            if (!isset($col['DISPLAY'])) {
                $col['DISPLAY'] = array();
            }
            $col['DISPLAY']['NUMERIC'] = true;

        }

        return true;
    }

    /**
     * check whether the column should be editable
     *
     * Returns bool(true) if:
     * <ol>
     *    <li>the table and column exist AND</li>
     *    <li>the column's readonly flag is set to true (see {@link DbStructure::isReadonly}) AND</li>
     *    <li>the column is not visible (see {@link DbStructure::isVisible}) AND</li>
     *    <li>the column setting DISPLAY.READONLY is not set, or false AND</li>
     *    <li>the column setting DISPLAY.READONLY.$action is not set OR</li>
     *    <li>the column setting DISPLAY.READONLY.$action exists and is set to false.</li>
     * </ol>
     * Returns bool(false) otherwise.
     *
     * If the argument $action is not provided, the last two do not apply.
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @param   string  $action  namespace of form ('SELECT', 'EDIT', 'NEW', 'SEARCH')
     * @return  bool
     *
     * @see     DbStructure::isVisible()
     * @see     DbStructure::isReadonly()
     * @since   2.9.0 RC1
     * @name    DbStructure::isEditable()
     */
    public function isEditable($table, $column, $action = "")
    {
        $action = mb_strtoupper("$action");

        $col =& $this->_getColumn($table, $column);

        /*
         * 1) error - table / column do not exist
         */
        if (!is_array($col)) {
            return false;

        /*
         * 2) the readonly flag has been set
         */
        } elseif ($this->isReadonly($table, $column)) {
            return false;

        /*
         * 3) the column is not visible
         */
        } elseif (!$this->isVisible($table, $column, $action)) {
            return false;

        /*
         * 4) display.readonly.$action property is set
         */
        } elseif (!empty($action) && isset($col['DISPLAY']['READONLY'][$action])) {
            /* 4.1) decide by permission */
            if (is_numeric($col['DISPLAY']['READONLY'][$action])) {
                return ($GLOBALS['YANA']->getVar('PERMISSION') >= $col['DISPLAY']['READONLY'][$action]);
            /* 4.2) decide by structure */
            } elseif ($col['DISPLAY']['READONLY'][$action] === false) {
                return true;
            } else {
                return false;
            }

        /*
         * 5) display.readonly property is set
         */
        } elseif (isset($col['DISPLAY']['READONLY']) && is_scalar($col['DISPLAY']['READONLY'])) {
            /* 5.1) decide by permission */
            if (is_numeric($col['DISPLAY']['READONLY'])) {
                return ($GLOBALS['YANA']->getVar('PERMISSION') >= $col['DISPLAY']['READONLY']);
            /* 5.2) decide by structure */
            } elseif ($col['DISPLAY']['READONLY'] === false) {
                return true;
            } else {
                return false;
            }

        /*
         * 6) display.readonly is undefined, defaults to bool(true)
         */
        } else {
            return true;
        }
    }

    /**
     * select whether the column should be editable
     *
     * This sets the "display.readonly" property of the column
     * to bool(false), if $isEditable is bool(true) and vice versa.
     *
     * The argument $isEditable may also be an integer of 0 through 100.
     * If so, this value will be compared with the security level
     * of the user and the column will be editable, if the user
     * has a level of permission of $isVisible or higher.
     * E.g. if $isEditable is set to 100, the column will only be
     * editable by administrators, while setting it to 30 will make
     * it editable to administrators and all other users, which at
     * least have a security level of 30 or above.
     *
     * This function returns bool(true) on success and bool(false)
     * on error.
     *
     * Note: if you don't provide the attribute $action then
     * settings will apply to all actions. Otherwise you need
     * to set the display property for all actions separately.
     *
     * @access  public
     * @param   bool|int  $isEditable  new value of this property
     * @param   string    $table       name of table
     * @param   string    $column      name of column
     * @param   string    $action      namespace of form ('SELECT', 'EDIT', 'NEW', 'SEARCH')
     * @return  bool
     * @since   2.9
     * @see     DbStructure::isVisible()
     * @name    DbStructure::setVisible()
     */
    public function setEditable($isEditable, $table, $column, $action = "")
    {
        $action = mb_strtoupper("$action");

        $col =& $this->_getColumn($table, $column);

        /*
         * 1) error table.column does not exist
         */
        if (!is_array($col)) {
            return false;
        } else {
            if (!isset($col['DISPLAY'])) {
                $col['DISPLAY'] = array();
            }
            if (!isset($col['DISPLAY']['READONLY'])) {
                $col['DISPLAY']['READONLY'] = array();
            }
            $prop =& $col['DISPLAY']['READONLY'];

            /*
             * 2.1) set property for ALL actions
             */
            if (!empty($action)) {
                if (!is_array($prop)) {
                    $prop = array();
                }
                if (is_int($isEditable)) {
                    if ($isEditable > -1 && $isEditable <= 100) {
                        $prop[$action] = (int) $isEditable;
                    } else {
                        $message = "Invalid argument 1. Index '{$isEditable}' out of bounds [0,100].";
                        trigger_error($message, E_USER_WARNING);
                        return false;
                    }
                } elseif ($isEditable) {
                    $prop[$action] = false;
                } else {
                    $prop[$action] = true;
                }
                return true;

            /*
             * 2.2) set property for ONE action
             */
            } else {
                if (is_int($isEditable)) {
                    if ($isEditable > -1 && $isEditable <= 100) {
                        $prop = (int) $isEditable;
                    } else {
                        $message = "Invalid argument 1. Index '{$isEditable}' out of bounds [0,100].";
                        trigger_error($message, E_USER_WARNING);
                        return false;
                    }
                } elseif ($isEditable) {
                    $prop = false;
                } else {
                    $prop = true;
                }
                return true;
            }
        } /* end if */
    }

    /**
     * check if column is a scalar type
     *
     * Returns bool(true) if the column exists and has a scalar type,
     * which's values can be displayed without line-breaks.
     * Returns bool(false) otherwise.
     *
     * Note that this returns bool(false) for type "text" and bool(true)
     * for type "select".
     *
     * @access  public
     * @param   string  $table   name of table
     * @param   string  $column  name of column
     * @return  bool
     *
     * @name    DbStructure::isScalar()
     * @since   2.9.4
     * @ignore
     */
    public function isScalar($table, $column)
    {
        switch ($this->getType($table, $column))
        {
            case 'file':
            case 'image':
            case 'array':
            case 'set':
            case 'text':
                return false;
            break;
            default:
                return true;
            break;
        }
    }

    /**
     * get the action property of a field as specified in the structure
     *
     * Returns constant NULL (not bool(false)) if value is not specified.
     * Use is_null($result) to test for existance, don't use empty($result).
     *
     * The argument $namespace can be either 'edit', 'select',
     * or empty. See {@link FormCreator::getNamespace()} for details.
     *
     * @access  public
     * @param   string  $table      name of table
     * @param   string  $column     name of column
     * @param   string  $namespace  namespace of form
     * @return  mixed
     * @since   2.9.4
     * @name    DbStructure::getAction()
     * @see     DbStructure::getActions()
     * @see     DbStructure::setAction()
     * @see     FormCreator::getNamespace()
     */
    public function getAction($table, $column, $namespace = 'DEFAULT')
    {
        $col =& $this->_getColumn($table, $column);
        $namespace = mb_strtoupper("$namespace");

        if (!is_array($col)) {
            /* error: table or column not found */
            return null;

        } elseif (!isset($col['ACTION'])) {
            /* property is undefined */
            return null;

        } elseif (is_string($col['ACTION'])) {
            return $col['ACTION'];

        } elseif (!isset($col['ACTION'][$namespace])) {
            if (!isset($col['ACTION']['DEFAULT'])) {
                /* property is undefined */
                return null;

            } else {
                /* property is undefined - switch to default */
                $namespace = 'DEFAULT';
            }

        }

        if (is_string($col['ACTION'][$namespace])) {
            return $col['ACTION'][$namespace];

        } elseif (!isset($col['ACTION'][$namespace]['ACTION'])) {
            /* property is undefined */
            return null;

        } else {
            return $col['ACTION'][$namespace]['ACTION'];

        }
    }

    /**
     * get all columns of a table, where the action property is set
     *
     * Returns the list of columns as an associative array, where
     * the names of the columns will be the array keys and the value
     * of the 'action' property will be the array values.
     * If no such columns are found, the array will be empty.
     *
     * Note that the values may either be of type 'string', or
     * an array of strings, with 1 or 2 elements, where the keys are
     * the namespace of the form the setting applies to and the values
     * are the name of the action to link to.
     *
     * The array will use the following syntax (example):
     * <code>
     * array(
     *   'column1' => array(
     *     'EDIT' => 'actionName1',
     *     'SELECT' => 'actionName2'
     *   ),
     *   'column2' => array(
     *     'EDIT' => 'actionName3',
     *     'SELECT' => 'actionName4'
     *   ),
     *   'column2' => 'actionName5'
     * );
     * </code>
     *
     * @access  public
     * @param   string  $table  name of table
     * @return  array
     * @since   2.9.4
     * @name    DbStructure::getActions()
     * @see     DbStructure::getAction()
     * @see     DbStructure::setAction()
     * @see     FormCreator::getNamespace()
     */
    public function getActions($table)
    {
        $result = array();
        foreach ($this->getColumns($table) as $column)
        {
            $action = $this->getAction($table, $column);
            if (!is_null($action)) {
                $result[$column] = $action;
            }
        }
        return $result;
    }

    /**
     * set the action property of a field
     *
     * This property can be used to tell the form generator
     * to produce a clickable link on this column.
     * The link will point to the action provided here.
     *
     * You may use the argument $linkText to specify some text
     * or image to display. For images just enter the file's
     * name and path as text (e.g. "common_files/icon_new.gif").
     *
     * The argument $namespace can be either 'edit', 'select',
     * or empty. See {@link FormCreator::getNamespace()} for details.
     *
     * To unset the action property, leave off the argument $action,
     * or set it to NULL. Note: when the property is unset, the arguments
     * $linkText and $tooltip are ignored.
     *
     * Returns constant bool(false) on error.
     *
     * @access  public
     * @param   string  $table      name of table to modify
     * @param   string  $column     name of column to modify
     * @param   string  $action     name of action to link to
     * @param   string  $namespace  namespace of form to add action to
     * @param   string  $linkText   text of created hyperlink (may also be path to an image)
     * @param   string  $tooltip    title-attribute of hyperlink (displayed as tooltip)
     * @return  mixed
     * @since   2.9.4
     * @name    DbStructure::setAction()
     * @see     DbStructure::getAction()
     * @see     DbStructure::getActions()
     * @see     FormCreator::getNamespace()
     */
    public function setAction($table, $column, $action = null, $namespace = 'DEFAULT', $linkText = '', $tooltip = '')
    {
        assert('is_null($action) || is_string($action)', ' Invalid argument $action: string expected');
        assert('is_string($namespace)', ' Invalid argument $namespace: string expected');
        assert('is_string($linkText)', ' Invalid argument $linkText: string expected');
        assert('is_string($tooltip)', ' Invalid argument $tooltip: string expected');

        $col =& $this->_getColumn($table, $column);
        $namespace = mb_strtoupper("$namespace");
        if (!is_null($action)) {
            $action = mb_strtolower("$action");
        }

        /*
         * 1) error: table or column not found
         */
        if (!is_array($col)) {
            trigger_error("No such column '$column' in table '$table'.", E_USER_WARNING);
            return false;
        }
        /*
         * 2) valid
         */

        /*
         * 2.1) init: action property
         */
        if (!isset($col['ACTION']) || !is_array($col['ACTION'])) {
            $col['ACTION'] = array();
        }
        /*
         * 2.2) init: namespace property
         */
        if (!isset($col['ACTION'][$namespace]) || !is_array($col['ACTION'][$namespace])) {
            $col['ACTION'][$namespace] = array();
        }

        /*
         * 2.3) set action
         */

        /*
         * 2.3.1) unset property
         *
         * $action == NULL
         */
        if (is_null($action)) {
            unset($col['ACTION'][$namespace]);
            return true;

        /*
         * 2.3.2) set property
         *
         * $action != NULL
         */
        } else {
            $col['ACTION'][$namespace]['ACTION'] = $action;
        }

        /*
         * 2.4) set link text
         */
        if (!empty($linkText)) {
            $col['ACTION'][$namespace]['TEXT'] = $linkText;
        }
        /*
         * 2.5 set tooltip
         */
        if (!empty($tooltip)) {
            $col['ACTION'][$namespace]['TITLE'] = $tooltip;
        }
        return true;
    }

    /**
     * get the title property of a field as specified in the structure
     *
     * Returns constant NULL (not bool(false)) if value is not specified.
     * Use is_null($result) to test for existance, don't use empty($result).
     *
     * The argument $namespace can be either 'edit', 'select',
     * or empty. See {@link FormCreator::getNamespace()} for details.
     *
     * @access  public
     * @param   string  $table      name of table
     * @param   string  $column     name of column
     * @param   string  $namespace  namespace of form
     * @return  mixed
     * @since   2.9.5
     * @name    DbStructure::getTitle()
     * @see     DbStructure::setAction()
     * @see     FormCreator::getNamespace()
     * @ignore
     */
    public function getTitle($table, $column, $namespace = 'DEFAULT')
    {
        $col =& $this->_getColumn($table, $column);
        $namespace = mb_strtoupper("$namespace");

        if (!is_array($col)) {
            /* error: table or column not found */
            return null;

        } elseif (isset($col['ACTION']['TITLE']) && is_string($col['ACTION']['TITLE'])) {
            return $col['ACTION']['TITLE'];

        } elseif (!is_array($col['ACTION'][$namespace]) || !isset($col['ACTION'][$namespace])) {
            /* property is undefined - switch to default */
            $namespace = 'DEFAULT';

        }

        if (!is_array($col['ACTION'][$namespace]) || !isset($col['ACTION'][$namespace]['TITLE'])) {
            /* property is undefined */
            return null;

        } else {
            return $col['ACTION'][$namespace]['TITLE'];

        }
    }

    /**
     * get the tooltip property of a field as specified in the structure
     *
     * Returns constant NULL (not bool(false)) if value is not specified.
     * Use is_null($result) to test for existance, don't use empty($result).
     *
     * The argument $namespace can be either 'edit', 'select',
     * or empty. See {@link FormCreator::getNamespace()} for details.
     *
     * @access  public
     * @param   string  $table      name of table
     * @param   string  $column     name of column
     * @param   string  $namespace  namespace of form
     * @return  mixed
     * @since   2.9.5
     * @name    DbStructure::getLinkText()
     * @see     DbStructure::setAction()
     * @see     FormCreator::getNamespace()
     * @ignore
     */
    public function getLinkText($table, $column, $namespace = 'DEFAULT')
    {
        $col =& $this->_getColumn($table, $column);
        $namespace = mb_strtoupper("$namespace");

        if (!is_array($col)) {
            /* error: table or column not found */
            return null;

        } elseif (isset($col['ACTION']['TEXT']) && is_string($col['ACTION']['TEXT'])) {
            return $col['ACTION']['TEXT'];

        } elseif (!is_array($col['ACTION'][$namespace]) || !isset($col['ACTION'][$namespace]['TEXT'])) {
            /* property is undefined - switch to default */
            $namespace = 'DEFAULT';

        }

        if (!is_array($col['ACTION'][$namespace]) || !isset($col['ACTION'][$namespace]['TEXT'])) {
            /* property is undefined */
            return null;

        } else {
            return $col['ACTION'][$namespace]['TEXT'];

        }
    }

    /**
     * get all fields for an address
     *
     * Retrieves all "$fieldname" entries that apply to the given
     * operation on the dataset and returns the results as an numeric
     * array.
     *
     * If the optional parameter $assoc is set to true, the result
     * will be an associative array.
     *
     * @access  public
     * @param   string  $fieldname  e.g. 'constraint', or 'trigger'
     * @param   string  $operation  one of select, insert, update, delete
     * @param   string  $table      name of table
     * @param   array   $columns    list of columns
     * @param   bool    $as_assoc   return as associative array
     * @return  array
     * @ignore
     */
    public function getFields($fieldname, $operation, $table, array $columns = array(), $as_assoc = false)
    {
        $fieldname = mb_strtoupper("$fieldname");
        $operation = mb_strtoupper("$operation");
        $table = mb_strtoupper("$table");

        $cache =& $this->_cachedFields[(int)$as_assoc][$fieldname][$operation][$table];

        /* if this result has already been cached than there is nothing to do here */
        if (isset($cache)) {
            if (count($columns) === 0) {
                return $cache;
            } else {
                if (!is_array($cache) || count($cache) === 0) {
                    return array();
                }
                $list_of_results = array();
                if (isset($cache[0])) {
                    $list_of_results[0] =& $cache[0];
                }
                if (isset($cache[1])) {
                    $list_of_results[1] =& $cache[1];
                }
                $columns = \Yana\Util\Hashtable::changeCase($columns, CASE_UPPER);;
                $match = array_intersect(array_keys($cache), $columns);

                foreach ($match as $key => $definition)
                {
                    if ($as_assoc) {
                        $list_of_results[$key] =& $cache[$definition];
                    } else {
                        $list_of_results[count($list_of_results)] =& $cache[$definition];
                    }
                }
                return $list_of_results;
            }
        /* if no cached result is available it has to be resolved */
        } else {

            /* 1. places where fields are allowed */
            $db_table_col = array();

            /* 1.1 Database-wide definitions */
            $db_table_col[0] =& $this->content;

            /* 1.2 table-wide definitions */
            if (!$this->isTable($table)) {
                /* table does not exist */
                trigger_error("The table '{$table}' does not exist.", E_USER_NOTICE);
                return array();
            } else {
                $db_table_col[1] =& $this->content['TABLES'][$table];
            }

            /* 1.3 column specific definitions */
            if (!is_array($columns) || count($columns) === 0) {
                $columns = array_keys($this->content['TABLES'][$table]['CONTENT']);
            }

            assert('is_array($columns)', ' the table '.$table.' has no contents');
            if (is_array($columns)) {
                foreach ($columns as $column)
                {
                    $column = mb_strtoupper($column);
                    if (!$this->isColumn($table, $column)) {
                        /* column does not exist */
                        trigger_error("The column '{$column}' does not exist in table '{$table}.'", E_USER_NOTICE);
                        return array();
                    } else {
                        $db_table_col[$column] =& $this->content['TABLES'][$table]['CONTENT'][$column];
                    }
                } /* end foreach */
            } /* end if */

            /* results are put here */
            $list_of_results = array();

            /* 2. find definitions */
            foreach ($db_table_col as $key => $value)
            {
                if (isset($value[$fieldname])) {
                    $definition =& $value[$fieldname];
                    /* fieldname.operation = string */
                    if (is_array($definition) && isset($definition[$operation]) && is_scalar($definition[$operation])) {
                        if ($as_assoc) {
                            $list_of_results[$key] =& $definition[$operation];
                        } else {
                            $list_of_results[count($list_of_results)] =& $definition[$operation];
                        }
                    /* fieldname = string */
                    } elseif (is_scalar($definition)) {
                        if ($as_assoc) {
                            $list_of_results[$key] =& $definition;
                        } else {
                            $list_of_results[count($list_of_results)] =& $definition;
                        }
                    /* none */
                    } else {
                        /* no definition present */
                    }
                }
            } /* end foreach */

            $cache = $list_of_results;

            return $list_of_results;
        }

    }

    /**
     * include structure file
     *
     * This function merges the contents of another file with the current structure.
     *
     * Note that you can reach the same result by using the 'INCLUDE' tag in the
     * structure file. See the developer's cookbook for an example.
     *
     * @access  public
     * @param   string  $filename  path to another structure file
     * @return  bool
     * @throws  \Yana\Core\Exceptions\NotFoundException         when the included file is not found
     * @throws  \Yana\Core\Exceptions\NotReadableException      when the included file is not readable
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the provided file name is not valid
     */
    public function includeFile($filename)
    {
        assert('is_string($filename)', ' Wrong argument type argument 1. String expected');
        $file = new \Yana\Db\Structure($filename);
        $file->read();
        assert('!isset($current_list)', ' cannot redeclare $current_list');
        assert('!isset($import_list)', ' cannot redeclare $import_list');
        assert('!isset($intersection)', ' cannot redeclare $intersection');
        $current_list = array_keys($this->content['TABLES']);
        $import_list  = array_keys($file->content['TABLES']);
        $intersection = array_intersect($current_list, $import_list);
        if (is_array($intersection) && count($intersection) > 0) {
            $message = "Unable to import structure file '{$filename}' into structure file '" .
                $this->getPath()."'.\n\t\tAmbigious table declarations found for: " .
                implode(', ', $intersection);
            trigger_error($message, E_USER_WARNING);
            return false;
        }
        assert('!isset($table)', ' cannot redeclare $table');
        /* $this->unsetAssociation(); */
        $databaseName = $file->getDatabaseName();
        foreach ($import_list as $table)
        {
            if ($this->hasAssociation($table)) {
                $message = "For table '{$table}' replacing illegal table association '" .
                    $this->getAssociation($table)."' with '{$databaseName}'.";
                trigger_error($message, E_USER_NOTICE);
            }
            $this->setAssociation($databaseName, $table);
            $this->content['TABLES'][$table] = $file->content['TABLES'][$table];
        }
        return true;
    }

    /**
     * addStructure
     *
     * Try to extract some information on the structure of
     * a database from the information provided by PEAR-DB's
     * tableInfo() function.
     *
     * see DbStream::buildStructure() for additional
     * information on the input
     *
     * @access  public
     * @param   string  $table     current table name
     * @param   array   $array     value returned by getTableInfo() -function
     * @see     DbStream::buildStructure()
     * @ignore
     */
    public function addStructure($table, array $array)
    {
        assert('is_string($table)', ' Wrong type for argument 1. String expected');

        /* init header */
        if (empty($this->content)) {
            $this->setStrict(true);
            $this->setReadonly(false);
        }
        $this->addTable($table);
        if (!empty($array['comment'])) {
            $this->setDescription($table, null, $array['comment']);
        }
        if (!empty($array['init']) && is_array($array['init'])) {
            $this->setInit($table, $array['init']);
        }

        for ($i = 0; $i < $array['length']; $i++)
        {
            $col = $array[$i];
            $name = $col['name'];

            $this->addColumn($table, $name);

            /*
             * set type
             */
            if (!$this->getType($table, $name)) {
                switch (mb_strtolower($col['type']))
                {
                    case 'tinytext':
                    case 'mediumtext':
                    case 'longtext':
                    case 'text':
                    case 'tinyblob':
                    case 'mediumblob':
                    case 'longblob':
                    case 'blob':
                        $this->setType($table, $name, "text");
                    break;

                    case 'tinyint':
                        if ($col['length'] == 1) {
                            $this->setType($table, $name, "boolean");
                            $col['unsigned'] = null;
                            $col['length'] = null;
                            if (!is_null($col['default'])) {
                                $this->setDefault($table, $name, ($col['default'] == true));
                            }
                        } else {
                            $this->setType($table, $name, "integer");
                            if (is_numeric($col['default'])) {
                                $this->setDefault($table, $name, (int) $col['default']);
                            }
                        }
                    break;

                    case 'smallint':
                    case 'mediumint':
                    case 'bigint':
                    case 'int':
                    case 'integer':
                        $this->setType($table, $name, "integer");
                        if (is_numeric($col['default'])) {
                            $this->setDefault($table, $name, (int) $col['default']);
                        }
                    break;

                    case 'year':
                        $this->setType($table, $name, "integer");
                        $this->setLength($table, $name, 4);
                        if (is_numeric($col['default'])) {
                            $this->setDefault($table, $name, (int) $col['default']);
                        }
                    break;

                    case 'binary':
                    case 'bit':
                    case 'bool':
                    case 'boolean':
                        $this->setType($table, $name, "boolean");
                        if (!is_null($col['default'])) {
                            $this->setDefault($table, $name, ($col['default'] == true));
                        }
                    break;

                    case 'double':
                    case 'double precision':
                    case 'numeric':
                    case 'float':
                    case 'real':
                    case 'decimal':
                        $this->setType($table, $name, "float");
                        if (!is_numeric($col['default'])) {
                            $this->setDefault($table, $name, (float) $col['default']);
                        }
                    break;

                    case 'timestamp':
                    case 'time':
                        $this->setType($table, $name, "time");
                        $this->setAuto($table, $name, true);
                        if (!is_numeric($col['default'])) {
                            $this->setDefault($table, $name, (int) $col['default']);
                        }
                    break;

                    case 'date':
                    case 'datetime':
                    case 'string':
                    case 'varchar':
                        $this->setType($table, $name, "string");
                        if (!empty($col['default'])) {
                            $this->setDefault($table, $name, $col['default']);
                        }
                    break;

                    case 'array':
                    case 'set':
                        $this->setType($table, $name, "array");
                        if (!empty($col['default'])) {
                            if (is_array($col['default'])) {
                                $this->setDefault($table, $name, $col['default']);
                            } elseif (preg_match('/\S, ?\S/', $col['default'])) {
                                $this->setDefault($table, $name, preg_split('/, ?/', $col['default']));
                            }
                        }
                    break;

                    case 'select':
                    case 'enum':
                        $this->setType($table, $name, "select");
                        if (!empty($col['default'])) {
                            if (is_array($col['default'])) {
                                $this->setDefault($table, $name, $col['default']);
                            } elseif (preg_match('/\S, ?\S/', $col['default'])) {
                                $this->setDefault($table, $name, preg_split('/, ?/', $col['default']));
                            }
                        }
                    break;

                    case 'char':
                        $this->setType($table, $name, "string");
                        $this->setLength($table, $name, 1);
                        if (!empty($col['default'])) {
                            $this->setDefault($table, $name, $col['default']);
                        }
                    break;

                    case 'mail':
                    case 'url':
                    case 'ip':
                    case 'image':
                    case 'file':
                        $this->setType($table, $name, mb_strtolower($col['type']));
                        if (!empty($col['default'])) {
                            $this->setDefault($table, $name, $col['default']);
                        }
                    break;

                    /* more ? */
                    default:
                        /* convert unrecognized type to string */
                        $this->setType($table, $name, "string");
                        if (!empty($col['default'])) {
                            $this->setDefault($table, $name, $col['default']);
                        }
                    break;

                } /* end switch */

                /*
                 * set visibility
                 */
                if ($col['primarykey'] && $col['auto']) {
                    $this->setVisible(false, $table, $name);
                }

            } /* end if */

            /*
             * set length
             */
            if (is_numeric($col['length']) && !$this->getLength($table, $name)) {
                $this->setLength($table, $name, (int) $col['length']);
            }

            /*
             * set nullable
             */
            if (is_bool($col['nullable'])) {
                $this->setNullable($table, $name, $col['nullable']);
            }

            /*
             * set auto
             */
            if ($col['auto']) {
                $this->setAuto($table, $name, true);
            }

            /*
             * set unique constraint
             */
            if ($col['unique']) {
                $this->setUnique($table, $name, true);
            }

            /*
             * set index
             */
            if ($col['index'] === true) {
                $this->setIndex($table, $name, true);
            }

            /*
             * set unsigned
             */
            if ($col['unsigned'] === true) {
                $this->setUnsigned($table, $name, true);
            }

            /*
             * set zerofill
             */
            if ($col['zerofill'] === true) {
                $this->setZerofill($table, $name, true);
            }

            /*
             * set foreign key
             */
            if ($col['foreignkey'] === true && $this->getType($table, $name) !== 'select') {
                $this->setType($table, $name, 'select');
                if (is_array($col['references'])) {
                    assert('!isset($fTable)', ' Cannot redeclare var $fTable');
                    assert('!isset($fId)', ' Cannot redeclare var $fId');
                    assert('!isset($fName)', ' Cannot redeclare var $fName');
                    $fTable = array_shift($col['references']);
                    $fId = array_shift($col['references']);
                    if ($this->isColumn($fTable, 'name')) {
                        $fName = 'name';
                    } elseif ($this->isColumn($fTable, 'title')) {
                        $fName = 'title';
                    } elseif ($this->isColumn($fTable, $fTable . '_name')) {
                        $fName = mb_strtolower($fTable . '_name');
                    } elseif ($this->isColumn($fTable, $fTable . '_title')) {
                        $fName = mb_strtolower($fTable . '_title');
                    } else {
                        $fName = $fId;
                    }
                    $this->setDefault($table, $name, array($fId => $fName));
                    unset($fTable, $fId, $fName);
                }
            }

            /*
             * set display properties
             */
            if ($col['select'] === false) {
                $this->setVisible(false, $table, $name);
            }

            if ($col['update'] === false) {
                $this->setReadonly(true, $table, $name);
            }

            if ($col['insert'] === false) {
                $this->setVisible(true, $table, $name, 'new');
            }

            /*
             * set description
             */
            if (!empty($col['comment'])) {
                $this->setDescription($table, $name, $col['comment']);
            }

            if (strcasecmp($name, 'profile_id') === 0) {
                $this->setProfile($table, $name);
                $this->setType($table, $name, "profile");
                $this->setAuto($table, $name, true);
                $this->setLength($table, $name, 255);
            }

        }

        /*
         * set primary key
         */
        if (isset($array['primarykey']) && !$this->setPrimaryKey($table, $array['primarykey'])) {
            return false;
        }

        /*
         * set foreign key
         */
        if (isset($array['foreignkeys']) && is_array($array['foreignkeys'])) {
            assert('!isset($element)', ' Cannot redeclare var $element');
            foreach ($array['foreignkeys'] as $element)
            {
                $this->setForeignKey($table, $element['column'], $element['foreigntable'], $element['foreigncolumn']);
            } /* end foreach */
            unset($element); /* clean up garbage */
            return false;
        }
    }

    /**
     * get filename
     *
     * check input $filename
     *
     * @static
     * @access  private
     * @param   string  $databaseName  name of database structure file
     * @return  string
     * @ignore
     */
    private static function _getFilename($databaseName)
    {
        assert('is_string($databaseName)', ' Wrong argument type argument 1. String expected');
        if (preg_match('/^([\w\d_]+)$/', $databaseName)) {
            $databaseName = self::getDirectory() . "$databaseName" . self::$_extension;
        }
        return "$databaseName";
    }

    /**
     * get database directory
     *
     * Returns the path to the directory where Structure files are to be stored.
     *
     * @access  public
     * @static
     * @return  string
     * @ignore
     */
    public static function getDirectory()
    {
        return \Yana\Db\Ddl\DDL::getDirectory();
    }

    /**
     * return list of known structure files
     *
     * This function returns a numeric list of filenames of known structure files.
     * Each item is a valid argument for calling the constructor of this class.
     *
     * If the argument $fullFilename is set to bool(true) the items are complete filenames,
     * including the path, relative to the framework's root directory.
     * Otherwise only the names of the structures are returned, which are easier to read for humans.
     * Both may be used as input to create a new instance.
     *
     * In case of an unexpected error, this function returns an empty array.
     *
     * @static
     * @access  public
     * @param   bool  $fullFilename  return items as full filenames (true = yes, false = no)
     * @return  array
     * @since   2.9.7
     */
    public static function getListOfFiles($fullFilename = false)
    {
        $directory = self::getDirectory();
        $dir = new \Yana\Files\Dir($directory);
        $list = array();
        $dirList = $dir->dirlist('*' . self::$_extension);
        if (is_array($dirList)) {
            foreach ($dirList as $filename)
            {
                /* prepend path */
                if ($fullFilename) {
                    $list[] = $directory . $filename;

                /* remove suffix: '.config' */
                } else {
                    $list[] = basename($filename, self::$_extension);
                }
            }
        }
        return $list;
    }

    /**
     * get list of changes for your documentation purposes
     *
     * This class automatically logs changes to the data model, which
     * have been introduced by using functions of this class.
     * A changelog is created, that may be used for various purposes,
     * e.g. creating automatic documentation.
     *
     * This function returns a list of changes that have been applied
     * to the current database structure as a multidimensional
     * associative array.
     * On error, or if no changelog exists, it returns bool(false)
     * instead.
     *
     * The changelog entries use the following syntax:
     * <code>
     * array(
     *     'DESCRIPTION' => "comment of your choice",
     *     'FUNCTION' => 'create' or 'rename' or 'update' or 'drop',
     *     'ARGS' => array(
     *         0 => 'table',
     *         1 => 'column'
     *    )
     * )
     * </code>
     *
     * Here is an example of what your result might look like:
     * <code>
     * array(
     *     0 => array(
     *         'DESCRIPTION' => "12 Dec 2000 (ADMIN) create table",
     *         'FUNCTION' => 'create',
     *         'ARGS' => array(
     *             0 => 'foo'
     *         ),
     *     1 => array(
     *         'DESCRIPTION' => "12 Dec 2000 (ADMIN) update column",
     *         'FUNCTION' => 'update',
     *         'ARGS' => array(
     *             0 => 'bar',
     *             1 => 'id'
     *         ),
     * )
     * </code>
     *
     * Here is an example on how to export the changelog to XML:
     * <code>
     * $log = $structure->getChangelog();
     * print \Yana\Util\Hashtable::toXML($log);
     * </code>
     * The created xml code may then be converted to HTML using
     * a xml-converter of your choice. As an alternative, you might
     * also decide to loop through the array and output all entries
     * directly as (X)HTML.
     *
     * @access  public
     * @return  array
     * @since   2.9.7
     * @name    DbStructure::getChangelog()
     * @see     DbStructure::dropChangelog()
     */
    public function getChangelog()
    {
        if (isset($this->content['CHANGELOG'])) {
            assert('is_array($this->content["CHANGELOG"]);');
            return $this->content['CHANGELOG'];
        } else {
            return false;
        }
    }

    /**
     * flush the changelog
     *
     * This clears all entries in the changelog.
     *
     * The function returns bool(true) on succes.
     * If the log is already empty, it returns bool(false) instead.
     *
     * @access  public
     * @return  bool
     * @since   2.9.7
     * @name    DbStructure::dropChangelog()
     * @see     DbStructure::getChangelog()
     */
    public function dropChangelog()
    {
        if (isset($this->content['CHANGELOG'])) {
            unset($this->content['CHANGELOG']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * _getTable
     *
     * Check if table exists and if so
     * return a reference to it.
     *
     * Returns bool(false) on error.
     *
     * @access  private
     * @param   string  &$table  table name
     * @return  array
     * @since   2.9
     * @ignore
     */
    private function &_getTable(&$table)
    {
        $table  = mb_strtoupper("$table");
        if (isset($this->content['TABLES'][$table])) {
            return $this->content['TABLES'][$table];
        } else {
            $col = false;
            return $col;
        }

    }

    /**
     * _getColumn
     *
     * Check if column exists and if so
     * return a reference to it.
     *
     * Returns bool(false) on error.
     *
     * @access  private
     * @param   string  &$table   table name
     * @param   string  &$column  column name
     * @return  array
     * @since   2.9
     * @ignore
     */
    private function &_getColumn(&$table, &$column)
    {
        $column = mb_strtoupper("$column");

        $tbl =& $this->_getTable($table);

        if (!is_array($tbl)) {
            $col = false;
            return $col;
        } elseif (isset($tbl['CONTENT'][$column])) {
            return $tbl['CONTENT'][$column];
        } else {
            $col = false;
            return $col;
        } /* end if */
    }

    /**
     * get specified property
     *
     * @access  private
     * @param   string  &$table      table name
     * @param   string  &$column     column name
     * @param   string  $property    property name
     * @param   string  $namespace   namespace
     * @return  mixed
     * @since   2.9.5
     * @name    DbStructure::_getColumnProperty()
     * @see     FormCreator::getNamespace()
     * @see     DbStructure::_setColumnProperty()
     */
    private function &_getColumnProperty(&$table, &$column, $property, $namespace = '')
    {
        assert('is_string($table)', ' Wrong type for argument 1. String expected');
        assert('is_string($column)', ' Wrong type for argument 2. String expected');
        assert('is_string($property)', ' Wrong type for argument 3. String expected');
        assert('is_string($namespace)', ' Wrong type for argument 4. String expected');
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            $result = null;
            return $result;
        } elseif ($namespace !== '') {
            if (!isset($col[$property][$namespace])) {
                $result = null;
                return $result;
            } else {
                return $col[$property][$namespace];
            }
        } elseif (!isset($col[$property])) {
            /* property is undefined */
            $result = null;
            return $result;
        } else {
            return $col[$property];
        }
    }

    /**
     * set specified property
     *
     * @access  private
     * @param   string  &$table      table name
     * @param   string  &$column     column name
     * @param   string  $property    property name
     * @param   mixed   $value       value
     * @return  bool
     * @since   2.9.5
     * @name    DbStructure::_setColumnProperty()
     * @see     DbStructure::_getColumnProperty()
     */
    private function _setColumnProperty(&$table, &$column, $property, $value)
    {
        assert('is_string($table)', ' Wrong type for argument 1. String expected');
        assert('is_string($column)', ' Wrong type for argument 2. String expected');
        assert('is_string($property)', ' Wrong type for argument 3. String expected');
        $col =& $this->_getColumn($table, $column);

        if (!is_array($col)) {
            /* error: table or column not found */
            return false;

        } else {
            $col[$property] = $value;
            return true;
        }
    }

    /**
     * add a new entry to changelog
     *
     * @access  private
     * @param   string  $table          table name
     * @param   string  $column         column name
     * @param   string  $comment        log comment
     * @param   string  $function       name of the function which was executed
     * @param   string  $renamedObject  used only when $function === "rename"
     * @since   2.9.7
     * @name    DbStructure::_logChanges()
     */
    private function _logChanges($table, $column, $comment, $function, $renamedObject = '')
    {
        assert('is_string($table)', ' Wrong type for argument 1. String expected');
        assert('is_string($column) || is_null($column)', ' Wrong type for argument 2. String expected');
        assert('is_string($comment)', ' Wrong type for argument 3. String expected');
        assert('is_string($function)', ' Wrong type for argument 4. String expected');
        assert('is_string($renamedObject)', ' Wrong type for argument 5. String expected');

        $table = mb_strtolower("$table");
        $column = mb_strtolower("$column");

        /*
         * don't log changes on newly created tables and columns
         */
        if (isset($this->_changedItems[$table])) {
            $this->content['CHANGELOG'][] = array('DESCRIPTION' => $comment);
            return null;
        }
        if (empty($column)) {
            $this->_changedItems[$table] = true;

        } elseif (isset($this->_changedItems["$table.$column"])) {
            $this->content['CHANGELOG'][] = array('DESCRIPTION' => $comment);
            return null;

        } else {
            $this->_changedItems["$table.$column"] = true;
        }

        /*
         * prepare data for new entry
         */
        if ($this->_logText == "") {
            $date = (string) date('d M Y h:i:s');

            if (isset($_SESSION['user_name'])) {
                $user = ' (' . $_SESSION['user_name'] .  ')';
            } else {
                $user = '';
            }
            $this->_logText = "$date$user ";
        }
        $arguments = array($table);
        if (!empty($column)) {
            $arguments[] = $column;
        }
        if (!empty($renamedObject)) {
            $arguments[] = $renamedObject;
        }

        /*
         * create log entry
         */
        if (!isset($this->content['CHANGELOG'])) {
            $this->content['CHANGELOG'] = array();
        }

        $this->content['CHANGELOG'][] = array(
            'DESCRIPTION' => $this->_logText . $comment,
            'FUNCTION' => $function,
            'ARGS' => $arguments
        );
    }

}

?>