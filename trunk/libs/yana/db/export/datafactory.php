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

namespace Yana\Db\Export;

/**
 * <<decorator>>  database Extractor
 *
 * This decorator class is intended to create SQL DDL (data definition language)
 * statements, and DML (data manipulation language) statements from
 * YANA Framework databases and structure files.
 *
 * For this task it provides functions which create specific SQL for various DBMS.
 *
 * Example of usage:
 * <code>
 * // open new database connection
 * $db = \Yana\Application::connect('guestbook');
 * // create new instance
 * $dbe = new \Yana\Db\Export\DataExporter($db);
 * // extract contents (here: use MySQL syntax)
 * $sql = $dbe->createMySQL();
 * // print results
 * print implode("\n", $sql);
 * // extract the data only
 * $sql = $dbe->createMySQL(false);
 * // extract the structure only
 * $sql = $dbe->createMySQL(true, false);
 * </code>
 *
 * @package     yana
 * @subpackage  db
 * @since       2.9.6
 */
class DataFactory extends \Yana\Db\Export\SqlFactory
{

    /**
     * @var \Yana\Db\IsConnection
     */
    private $_db = null;

    /**
     * @var string
     */
    private $_tpl = "INSERT INTO %TABLE% (%KEYS%) VALUES (%VALUES%);";

    /**
     * Create a new instance.
     *
     * This class requires a database resource as input.
     *
     * @param  \Yana\Db\IsConnection  $db  a database resource
     */
    public function __construct(\Yana\Db\IsConnection $db)
    {
        $this->_db = $db;
        parent::__construct($this->_db->getSchema());
    }

    /**
     * Create SQL for MySQL.
     *
     * Returns a numeric array of SQL statements.
     * Each element is a single statement.
     * If you want to send the result to a SQL file
     * you should "implode()" the array to a string.
     *
     * The result may include the data and structure of a database.
     * Set the arguments $extractStructure or $extractData
     * to bool(false) to exclude one or the other.
     *
     * @param   bool  $extractStructure  for extract Structure set true otherwise false
     * @param   bool  $extractData       for extract Data set true otherwise false
     * @return  array
     */
    public function createMySQL($extractStructure = true, $extractData = true)
    {
        if ($extractStructure) {
            $sql = parent::createMySQL();
        } else {
            $sql = array();
        }

        if ($extractData) {
            @set_time_limit(500);
            foreach ($this->schema->getTableNames() as $table)
            {
                foreach($this->_db->select($table) as $row)
                {
                    /* quote values */
                    foreach (array_keys($row) as $column)
                    {
                        $row[$column] = self::quoteValue($row[$column], "mysql");
                    }

                    /* build statement */
                    $stmt = $this->_tpl;
                    $stmt = str_replace('%TABLE%', "`" . YANA_DATABASE_PREFIX . $table .  "`", $stmt);
                    $stmt = str_replace('%KEYS%', "`" . mb_strtolower(implode("`, `", array_keys($row))) .  "`", $stmt);
                    $stmt = str_replace('%VALUES%', implode(", ", $row), $stmt);
                    $sql[] = $stmt;
                }
            }
        }
        return $sql;
    }

    /**
     * Create SQL for PostgreSQL.
     *
     * Returns a numeric array of SQL statements.
     * Each element is a single statement.
     * If you want to send the result to a SQL file
     * you should "implode()" the array to a string.
     *
     * The result may include the data and structure of a database.
     * Set the arguments $extractStructure or $extractData
     * to bool(false) to exclude one or the other.
     *
     * @param   bool  $extractStructure  for extract Structure set true otherwise false
     * @param   bool  $extractData       for extract Data set true otherwise false
     * @return  array
     */
    public function createPostgreSQL($extractStructure = true, $extractData = true)
    {
        if ($extractStructure) {
            $sql = parent::createPostgreSQL();
        } else {
            $sql = array();
        }

        if ($extractData) {
            @set_time_limit(500);
            foreach ($this->schema->getTableNames() as $table)
            {
                foreach($this->_db->select($table) as $row)
                {
                    /* quote values */
                    foreach (array_keys($row) as $column)
                    {
                        $row[$column] = self::quoteValue($row[$column], "postgresql");
                    }

                    /* build statement */
                    $stmt = $this->_tpl;
                    $stmt = str_replace('%TABLE%', '"' . YANA_DATABASE_PREFIX . $table .  '"', $stmt);
                    $stmt = str_replace('%KEYS%', '"' . mb_strtolower(implode('", "', array_keys($row))) .  '"', $stmt);
                    $stmt = str_replace('%VALUES%', implode(", ", $row), $stmt);
                    $sql[] = $stmt;
                }
            }
        }
        return $sql;
    }

    /**
     * Create SQL for MS SQL Server.
     *
     * Returns a numeric array of SQL statements.
     * Each element is a single statement.
     * If you want to send the result to a SQL file
     * you should "implode()" the array to a string.
     *
     * The result may include the data and structure of a database.
     * Set the arguments $extractStructure or $extractData
     * to bool(false) to exclude one or the other.
     *
     * @param   bool  $extractStructure  for extract Structure set true otherwise false
     * @param   bool  $extractData       for extract Data set true otherwise false
     * @return  array
     */
    public function createMSSQL($extractStructure = true, $extractData = true)
    {
        if ($extractStructure) {
            $sql = parent::createMSSQL();
        } else {
            $sql = array();
        }

        if ($extractData) {
            @set_time_limit(500);
            foreach ($this->schema->getTableNames() as $table)
            {
                foreach($this->_db->select($table) as $row)
                {
                    /* quote values */
                    foreach (array_keys($row) as $column)
                    {
                        $row[$column] = self::quoteValue($row[$column], "mssql");
                    }

                    /* build statement */
                    $stmt = $this->_tpl;
                    $stmt = str_replace('%TABLE%', '[' . YANA_DATABASE_PREFIX . $table .  ']', $stmt);
                    $stmt = str_replace('%KEYS%', '[' . mb_strtolower(implode('], [', array_keys($row))) .  ']', $stmt);
                    $stmt = str_replace('%VALUES%', implode(", ", $row), $stmt);
                    $sql[] = $stmt;
                }
            }
        }
        return $sql;
    }

    /**
     * Same as \Yana\Db\Export\SqlFactory::createMSSQL().
     *
     * The result may include the data and structure of a database.
     * Set the arguments $extractStructure or $extractData
     * to bool(false) to exclude one or the other.
     *
     * @param   bool  $extractStructure  for extract Structure set true otherwise false
     * @param   bool  $extractData       for extract Data set true otherwise false
     * @return  array
     * @see     \Yana\Db\Export\SqlFactory::createMSSQL()
     */
    public function createMSAccess($extractStructure = true, $extractData = true)
    {
        return $this->createMSSQL($extractStructure, $extractData);
    }

    /**
     * Create SQL for IBM DB2.
     *
     * Returns a numeric array of SQL statements.
     * Each element is a single statement.
     * If you want to send the result to a SQL file
     * you should "implode()" the array to a string.
     *
     * The result may include the data and structure of a database.
     * Set the arguments $extractStructure or $extractData
     * to bool(false) to exclude one or the other.
     *
     * @param   bool  $extractStructure  for extract Structure set true otherwise false
     * @param   bool  $extractData       for extract Data set true otherwise false
     * @return  array
     */
    public function createDB2($extractStructure = true, $extractData = true)
    {
        global $YANA;
        if ($extractStructure) {
            $sql = parent::createDB2();
        } else {
            $sql = array();
        }

        if (isset($YANA)) {
            $file = $YANA->getResource('system:/config/reserved_sql_keywords.file');
            $sqlKeywords = file($file->getPath());
            unset($file);
        } else {
            $sqlKeywords = array();
        }

        if ($extractData) {
            @set_time_limit(500);
            foreach ($this->schema->getTable() as $table)
            {
                /* @var $table \Yana\Db\Ddl\Table */
                /* quote table */
                $tableName = YANA_DATABASE_PREFIX . $table->getName();
                if (\Yana\Util\Hashtable::quickSearch($sqlKeywords, $tableName) !== false) {
                    $tableName = "\"{$tableName}\"";
                }
                /* quote columns */
                $columns = array();
                foreach ($table->getColumnNames() as $column)
                {
                    $column = mb_strtolower($column);
                    if (\Yana\Util\Hashtable::quickSearch($sqlKeywords, mb_strtoupper($column)) !== false) {
                        $columns[$column] = "\"{$column}\"";
                    } else {
                        $columns[$column] = $column;
                    }
                }
                foreach($this->_db->select($table->getName()) as $row)
                {
                    $keys = "";
                    /* quote values */
                    foreach (array_keys($row) as $column)
                    {
                        $columName = mb_strtolower($column);
                        if (isset($columns[$columName])) {
                            $keys .= ( ($keys) ? ', ' : '' ) . $columns[$columName];
                        } else {
                            $keys .= ( ($keys) ? ', ' : '' ) . $columName;
                        }
                        $row[$column] = self::quoteValue($row[$column], "db2");
                    }

                    /* build statement */
                    $stmt = $this->_tpl;
                    $stmt = str_replace('%TABLE%', $tableName, $stmt);
                    $stmt = str_replace('%KEYS%', $keys, $stmt);
                    $stmt = str_replace('%VALUES%', implode(", ", $row), $stmt);
                    $sql[] = $stmt;
                }
            }
        }
        return $sql;
    }

    /**
     * Create SQL for Oracle.
     *
     * Returns a numeric array of SQL statements.
     * Each element is a single statement.
     * If you want to send the result to a SQL file
     * you should "implode()" the array to a string.
     *
     * The result may include the data and structure of a database.
     * Set the arguments $extractStructure or $extractData
     * to bool(false) to exclude one or the other.
     *
     * @param   bool  $extractStructure  for extract Structure set true otherwise false
     * @param   bool  $extractData       for extract Data set true otherwise false
     * @return  array
     */
    public function createOracleDB($extractStructure = true, $extractData = true)
    {
        if ($extractStructure) {
            $sql = parent::createOracleDB();
        } else {
            $sql = array();
        }

        if ($extractData) {
            @set_time_limit(500);
            foreach ($this->schema->getTableNames() as $table)
            {
                foreach($this->_db->select($table) as $row)
                {
                    /* quote values */
                    foreach (array_keys($row) as $column)
                    {
                        $row[$column] = self::quoteValue($row[$column], "oracle");
                    }

                    /* build statement */
                    $stmt = $this->_tpl;
                    $stmt = str_replace('%TABLE%', '"' . YANA_DATABASE_PREFIX . $table .  '"', $stmt);
                    $stmt = str_replace('%KEYS%', '"' . mb_strtolower(implode('", "', array_keys($row))) .  '"', $stmt);
                    $stmt = str_replace('%VALUES%', implode(", ", $row), $stmt);
                    $sql[] = $stmt;
                }
            }
        }
        return $sql;
    }

    /**
     * Create XML.
     *
     * This static function will export the content of the database to a xml string.
     * It returns bool(false) on error.
     *
     * You may limit the output to a certain table or structure file, by setting
     * the arguments $structure and $table. Otherwise the whole database is exported,
     * which is also the default behavior. You may set the argument $structure to NULL,
     * if you just need $table.
     *
     * Note that both arguments may also be provided as a list of files or tables.
     *
     * You may set the argument $useForeignKeys to bool(true), if you want references
     * (foreign keys) between tables to be respected. This way tables may be
     * containers for other tables, where there is a relation between both.
     *
     * To "resolve a foreign key relation" in this case actually means, each foreign
     * key is interpreted as a parent-child relation between two tables.
     * Each row of the child table (the referencing table) is then
     * copied to it's parent row (the row in the referenced table,
     * as identified by the value of the foreign key column in the
     * current row of the child table).
     *
     * Note, that a table may have multiple parents.
     * This will result in multiple copies of the same row.
     *
     * Also note, that this function does not detect circular
     * references. However, this is not much of a restriction, as
     * such references are not a legal construct in RDBMSs.
     * Although some RDBMS allow such constructs, it would be
     * "practically" impossible to add any data, without breaking
     * referential integrity, because a row should not contain a
     * checked reference to itself, while it does not exist.
     *
     * Note: this may result in an error for DBMS that ignore
     * referential integrity, like MyISAM tables in MySQL.
     *
     * Here is an example to illustrate the behavior of this function.
     * May "foo" and "bar" be tables, with "foo" having a property "bar_id",
     * that is a foreign key, referencing "bar".
     *
     * The following function call will output the XML representation of both tables:
     * <code>
     * print \Yana\Db\Export\DataExporter::createXML(true, null, array('foo', 'bar'));
     * </code>
     *
     * The result would look something like this:
     * <code>
     * ... XML-head ...
     * <bar>
     *   <item id="1">
     *     <bar_id>1</bar_id>
     *     <bar_value>0.0</bar_value>
     *     <!-- here come some entries of table foo -->
     *     <bar.foo>
     *       <item id="2">
     *         <foo_id>2</foo_id>
     *         <bar_id>1</bar_id>
     *         <!-- other values -->
     *       </item>
     *       <item id="5">
     *         <foo_id>5</foo_id>
     *         <bar_id>1</bar_id>
     *         <!-- other values -->
     *       </item>
     *     </bar.foo>
     *   </item>
     * </bar>
     * </code>
     *
     * @param   bool          $useForeignKeys  toogle wether to export "flat" structure or
     *                                         use foreign keys to create recursive containers
     * @param   string|array  $ddlFile         limit output to certain schema file(s)
     * @param   string|array  $table           limit output to certain table(s)
     * @param   array         $rows            limit output to certain rows(s)
     *                                         e.g. array('tab1' => array(1, 2, 3))
     * @return  string
     */
    public static function createXML($useForeignKeys = false, $ddlFile = null, $table = null, array $rows = null)
    {
        assert('is_null($ddlFile) || is_string($ddlFile) || is_array($ddlFile)', ' '.
            'Wrong type for argument 1. String expected');
        assert('is_null($table) || is_string($table) || is_array($table)', ' '.
            'Wrong type for argument 2. String expected');

        $data = array(); // declare output variable of type array
        @set_time_limit(500); // This may take a while. Raise limit to avoid time-out.

        switch (true)
        {
            case is_string($ddlFile): // get only 1 database
                $ddlFiles = array($ddlFile);
            break;
            case is_array($ddlFile): // get all database definitions
                $ddlFiles = array_values($ddlFile);
            break;
            default:
                $ddlFiles = \Yana\Db\Ddl\DDL::getListOfFiles(true);
            break;
        }
        unset($ddlFile);

        /*
         * loop through files
         */
        assert('!isset($structure)', ' Cannot redeclare var $structure');
        /* @var $ddlFile string */
        foreach ($ddlFiles as $ddlFile)
        {
            $db = \Yana\Application::connect($ddlFile);
            $dbSchema = $db->getSchema();
            $nodes = array();

            switch (true)
            {
                case empty($table): // get all tables (default)
                    $tables = $dbSchema->getTableNames();
                break;
                case is_string($table): // get only 1 table
                    if (!$dbSchema->isTable($table)) {
                        continue; // table is not here, try next file
                    }
                    $data[$table] = $db->select($table);
                break 2; // no more tables - abort!
                case is_array($table): // get some tables
                    $tables = array_values($table);
                break;
                default: // get all tables (default)
                    $tables = $dbSchema->getTableNames();
                break;
            }
            unset($table);

            if (!empty($tables) && is_array($tables)) {

                /**
                 * loop through tables
                 */
                foreach ($tables as $table)
                {
                    if (!$dbSchema->isTable($table)) {
                        continue;
                    }
                    /*
                     * 1) limit to certain rows
                     */
                    if (is_array($rows) && isset($rows[$table])) {
                        $data[$table] = array();
                        /* Note: $nodes is a "flat" list of references */
                        $nodes[$table] =& $data[$table];

                        /* add entries */
                        assert('!isset($i)', ' Cannot redeclare var $i');
                        foreach ($rows[$table] as $i)
                        {
                            $data[$table][$i] = $db->select("$table.$i");
                        }
                        unset($i);

                    /*
                     * 2) all rows
                     */
                    } else {
                        $data[$table] = $db->select($table);
                        /* Note: $nodes is a "flat" list of references */
                        $nodes[$table] =& $data[$table];
                    }
                }
                unset($table);

                /**
                 * resolve foreign keys on demand
                 */
                if ($useForeignKeys !== false) {
                    /**
                     * loop through tables
                     */
                    assert('!isset($tableName)', ' Cannot redeclare var $tableName');
                    assert('!isset($table)', ' Cannot redeclare var $table');
                    assert('!isset($hasFKey)', ' Cannot redeclare var $hasFKey');
                    /* declare temporary variables */
                    assert('!isset($_attr)', ' Cannot redeclare var $_attr');
                    assert('!isset($_fKey)', ' Cannot redeclare var $_fKey');
                    assert('!isset($_fTable)', ' Cannot redeclare var $_fTable');
                    assert('!isset($_row)', ' Cannot redeclare var $_row');
                    foreach (array_keys($nodes) as $tableName)
                    {
                        $_attr = "@$tableName";
                        $table = $dbSchema->getTable($tableName);
                        $_fKey = null;
                        $_fTable = null;
                        $_row = null;
                        $hasFKey = false;

                        /**
                         * loop through foreign keys
                         */
                        assert('!isset($fCol)', ' Cannot redeclare var $fCol');
                        assert('!isset($fTableName)', ' Cannot redeclare var $fTable');
                        assert('!isset($column)', ' Cannot redeclare var $column');
                        foreach ($table->getForeignKeys() as $column)
                        {
                            /* @var $column \Yana\Db\Ddl\ForeignKey */
                            assert('!isset($_fKeys)', ' Cannot redeclare var $_fKeys');
                            $_fKeys = $column->getColumns();
                            $fTableName = $column->getTargetTable();
                            if (count($_fKeys) > 1) {
                                unset($_fKeys);
                                continue; // ignore compound foreign keys
                            }
                            $hasFKey = true;
                            $fCol = current($_fKeys); // value = target column of fkey constraint
                            if (empty($fCol)) {
                                $fCol = key($_fKeys); // fall back to key = source column
                            }
                            $fCol = strtoupper($fCol);
                            unset($_fKeys);
                            /**
                             * {@internal
                             *
                             * Keep in mind that foreign key references
                             * are reversed compared to arrays:
                             *   $_fTable is reference to the target table,
                             *   - NOT the source table!
                             *
                             * }}
                             */
                            $_fTable =& $nodes[$fTableName];
                            /**
                             * loop through rows in foreign table
                             */
                            assert('!isset($pKey)', ' Cannot redeclare var $pKey');
                            foreach (array_keys($nodes[$tableName]) as $pKey)
                            {
                                $_row =& $nodes[$tableName][$pKey];
                                if (isset($_row[$fCol])) {
                                    $_fKey = $_row[$fCol];
                                    /* skip value if referenced row does not exist */
                                    if (isset($_fTable[$_fKey])) {
                                        if (!isset($_fTable[$_fKey][$_attr])) {
                                            $_fTable[$_fKey][$_attr] = array();
                                        }
                                        $_fTable[$_fKey][$_attr][$pKey] =& $_row;
                                    }
                                }
                            }
                            unset($pKey);
                        } // end foreach (foreign key)
                        unset($fCol, $fTableName, $column);
                        if ($hasFKey && isset($data[$tableName])) {
                            unset($data[$tableName]);
                        }
                    }
                    // clean up temporary variables
                    unset($_attr, $_fKey, $_fTable, $_row, $table, $tableName, $hasFKey);

                } // end if ($useForeignKeys)

            } // end if (get all tables)

        } // end foreach (structure)
        unset($ddlFile);

        /**
         * error - result is empty or invalid
         */
        if (empty($data) || !is_array($data)) {
            return false;
        }
        /*
         * encode data array to xml string
         */
        $data = self::_xmlEncode($data);
        assert('is_string($data)', ' Unexpected argument type. String expected');
        return "<?xml version=\"1.0\"?>\n" . $data;
    }

    /**
     * Create xml.
     *
     * @param   array   $table      input table
     * @param   string  $tableName  name of input table
     * @param   string  $prefix     prefix of root element
     * @param   int     $indent     number of tabs to indent
     * @since   2.9.7
     */
    private static function _xmlEncode(array $table, $tableName = "", $prefix = "", $indent = 0)
    {
        assert('is_string($tableName)', 'Wrong argument type for argument 2. String expected.');
        assert('is_string($prefix)', 'Wrong argument type for argument 3. String expected.');
        assert('is_int($indent)', 'Wrong argument type for argument 4. Integer expected.');

        /*
         * settype to STRING
         *            INTEGER
         */
        $tableName = (string) $tableName;
        $prefix = (string) $prefix;
        $indent = (int) $indent;

        if (!empty($prefix)) {
            $prefix .= ".";
        }

        $tab = "";
        $xml = ""; // containts output

        /*
         * Create xml header.
         *
         * This applies to first iteration only (top-most call).
         */
        if ($indent === 0) {
            $xml .= "<database>\n";
            foreach ($table as $name => $value)
            {
                if (is_array($value)) {
                    $xml .= self::_xmlEncode($value, $name, "", 1);
                }
            }
            $xml .= "</database>";
            return $xml;
        }

        /*
         * Create xml body.
         *
         * This applies to all following iterations only.
         */
        for ($i = 0; $i < $indent; $i++)
        {
            $tab .= "\t";
        }
        unset($i);

        $xml .= "$tab<$prefix$tableName>\n";
        foreach ($table as $pKey => $row)
        {
            if (!is_array($row)) {
                continue; // not a row
            }
            $xml .= "$tab\t<item id=\"$pKey\">\n";
            foreach ($row as $column => $value)
            {
                $column = mb_strtolower($column);
                if (is_bool($value)) {
                    $xml .= "$tab\t\t<$column>" . ( ($value) ? "true" : "false" ) . "</$column>\n";

                } elseif (is_array($value)) {
                    if ($column[0] === '@') {
                        $column = mb_substr($column, 1);
                        $xml .= self::_xmlEncode($value, $column, $tableName, $indent + 2);

                    } else {
                        $xml .= "$tab\t\t<$column>\n";
                        foreach ($value as $key => $item)
                        {
                            $xml .= \Yana\Util\Hashtable::toXML($item, $key, CASE_MIXED, $indent + 3);
                        }
                        $xml .= "$tab\t\t</$column>\n";
                    }

                } else {
                    $xml .= "$tab\t\t<$column>" . htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8') . "</$column>\n";

                }
            }
            $xml .= "$tab\t</item>\n";
        }
        $xml .= "$tab</$prefix$tableName>\n";

        return $xml;
    }

    /**
     * Returns a quoted value.
     *
     * @param   mixed   $value  value to quote
     * @param   string  $dbms   database name
     * @return  string
     * @ignore
     */
    public static function quoteValue($value, $dbms = null)
    {
        /*
         * FileDb
         */
        if (is_null($dbms)) {
            if (is_null($value)) {
                return YANA_DB_DELIMITER . YANA_DB_DELIMITER;

            } elseif (is_scalar($value)) {
                if (is_string($value)) {
                    $value = stripslashes("$value");
                    $value = str_replace('\\', '\\\\', $value);
                    $value = str_replace(YANA_DB_DELIMITER, '\\' . YANA_DB_DELIMITER, $value);
                    $value = str_replace("\n", '\n', $value);
                    $value = str_replace("\r", '\r', $value);
                    $value = str_replace("\f", '\f', $value);
                    $value = str_replace(chr(0), '', $value);
                };
                return YANA_DB_DELIMITER . "$value" . YANA_DB_DELIMITER;

            } else {
                trigger_error("A value of non-scalar type '" . gettype($value) .
                    "' has been found in an SQL statement and will be converted to string.", E_USER_NOTICE);
                return YANA_DB_DELIMITER . \Yana\Files\SML::encode($value) . YANA_DB_DELIMITER;'"NULL"';

            }
        } /* end if */
        /*
         * ... other DBMS ...
         */

        switch (true)
        {
            /*
             * constant NULL
             */
            case is_null($value):
                return 'NULL';
            break;

            /*
             * integer
             */
            case is_int($value):
                return "$value";
            break;

            /*
             * boolean
             */
            case is_bool($value):
                switch ($dbms)
                {
                    case 'dbase':
                        if ($value === true) {
                            return "T";
                        } else {
                            return "F";
                        }
                    break;
                    case 'frontbase':
                    case 'postgresql':
                        if ($value === true) {
                            return "TRUE";
                        } else {
                            return "FALSE";
                        }
                    break;
                    default:
                        if ($value === true) {
                            return "1";
                        } else {
                            return "0";
                        }
                    break;
                }
            break;

            /*
             * array
             */
            case is_array($value):
                $value = \Yana\Files\SML::encode($value);
            break;

            /*
             * float
             */
            case is_float($value):
                $value = str_replace(',', '.', "$value");
            break;

            /*
             * string
             */
            case is_string($value):
                /* intentionally left blank */
            break;

            /*
             * default
             */
            default:
                /* intentionally left blank */
            break;
        }
        /*
         * add quotes
         */
        switch ($dbms)
        {
            /*
             * MySQL
             */
            case 'mysql':
                $value = mysql_escape_string($value);
            break;
            /*
             * PostgreSQL
             */
            case 'postgresql':
                if (function_exists('pg_escape_string')) {
                    $value = pg_escape_string($value);
                } else {
                    $value = str_replace("'", "''", $value);
                    $value = str_replace('\\', '\\\\', $value);
                    $value = str_replace("\n", '\n', $value);
                    $value = str_replace("\r", '\r', $value);
                    $value = str_replace("\f", '\f', $value);
                }
            break;
            /*
             * other
             */
            default:
                $value = str_replace("'", "''", $value);
                $value = str_replace("\n", '\n', $value);
                $value = str_replace("\r", '\r', $value);
                $value = str_replace("\f", '\f', $value);
            break;
        }
        return "'$value'";
    }

}

?>