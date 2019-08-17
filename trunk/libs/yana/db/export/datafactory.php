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
 * <<decorator>> Database extractor.
 *
 * This class creates database exports and backups from Yana FileDb.
 * It will output "create table" statements followed by "insert" statements,
 * that may then be pushed to a database.
 *
 * For this task it provides functions which create specific SQL for various target DBMS.
 *
 * Example of usage:
 * <code>
 * // open new database connection
 * $db = $yana->connect('guestbook');
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
     * @var \Yana\Db\Helpers\IsSqlKeywordChecker
     */
    private $_sqlKeywordChecker = null;

    /**
     * @var string
     */
    private $_tpl = "INSERT INTO %TABLE% (%KEYS%) VALUES (%VALUES%);";

    /**
     * Create a new instance.
     *
     * This class requires a database resource as input.
     *
     * @param  \Yana\Db\IsConnection                 $db                 a database resource
     * @param  \Yana\Db\Helpers\IsSqlKeywordChecker  $sqlKeywordChecker  a class that checks if a given string is a reserved SQL keyword
     */
    public function __construct(\Yana\Db\IsConnection $db, \Yana\Db\Helpers\IsSqlKeywordChecker $sqlKeywordChecker = null)
    {
        if (!is_null($sqlKeywordChecker)) {
            $this->_sqlKeywordChecker = $sqlKeywordChecker;
        }
        $this->_db = $db;
        parent::__construct($this->_db->getSchema());
    }

    /**
     * Returns a class that checks if a given string is a reserved SQL keyword.
     *
     * We need this functionality for quoting the names of IBM DB2 database object names.
     *
     * @return  \Yana\Db\Helpers\IsSqlKeywordChecker
     */
    protected function _getSqlKeywordChecker()
    {
        if (!isset($this->_sqlKeywordChecker)) {
            $this->_sqlKeywordChecker = \Yana\Db\Helpers\SqlKeywordChecker::createFromApplicationDefault();
        }
        return $this->_sqlKeywordChecker;
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
        $sql = array();
        if ($extractStructure) {
            // @codeCoverageIgnoreStart
            $sql = parent::createMySQL();
            // @codeCoverageIgnoreEnd
        }

        if ($extractData) {
            @set_time_limit(500); // this may take a while: increase time limit so we don't run into a timeout

            // Loop through all tables in the database and extract each one
            foreach ($this->schema->getTableNames() as $table)
            {
                // Select * From table
                foreach($this->_db->select($table) as $row)
                {
                    /* quote values */
                    foreach (array_keys($row) as $column)
                    {
                        $row[$column] = self::quoteValue($row[$column], "mysql");
                    }

                    /* build statement */
                    $stmt = $this->_tpl; // Copy the template
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
        $sql = array();
        if ($extractStructure) {
            // @codeCoverageIgnoreStart
            $sql = parent::createPostgreSQL();
            // @codeCoverageIgnoreEnd
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
                    $stmt = $this->_tpl; // Copy the template
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
        $sql = array();
        if ($extractStructure) {
            // @codeCoverageIgnoreStart
            $sql = parent::createMSSQL();
            // @codeCoverageIgnoreEnd
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
        $sql = array();
        if ($extractStructure) {
            // @codeCoverageIgnoreStart
            $sql = parent::createDB2();
            // @codeCoverageIgnoreEnd
        }

        if ($extractData) {

            $sqlKeywords = $this->_getSqlKeywordChecker();
            unset($file);

            @set_time_limit(500);
            foreach ($this->schema->getTables() as $table)
            {
                /* @var $table \Yana\Db\Ddl\Table */
                /* quote table */
                $tableName = YANA_DATABASE_PREFIX . $table->getName();
                if ($sqlKeywords->isSqlKeyword($tableName) !== false) {
                    $tableName = "\"" . $tableName . "\"";
                }
                /* quote columns */
                $columns = array();
                foreach ($table->getColumnNames() as $column)
                {
                    $column = mb_strtolower($column);
                    if ($sqlKeywords->isSqlKeyword($column) !== false) {
                        $columns[$column] = "\"" . $column . "\"";
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
        $sql = array();
        if ($extractStructure) {
            // @codeCoverageIgnoreStart
            $sql = parent::createOracleDB();
            // @codeCoverageIgnoreEnd
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
                    $stmt = str_replace('%KEYS%', '"' . implode('", "', array_keys($row)) .  '"', $stmt);
                    $stmt = str_replace('%VALUES%', implode(", ", $row), $stmt);
                    $sql[] = $stmt;
                }
            }
        }
        return $sql;
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
                $message = "A value of non-scalar type '" . gettype($value) .
                    "' has been found in an SQL statement and will be converted to string.";
                $level = \Yana\Log\TypeEnumeration::INFO;
                \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                return YANA_DB_DELIMITER . \Yana\Files\SML::encode($value) . YANA_DB_DELIMITER;

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
             * float
             */
            case is_float($value):
                $value = str_replace(',', '.', "$value");
            break;

            /*
             * array
             */
            case is_array($value):
                $value = \json_encode($value);
            // fall through

            /*
             * string
             */
            case is_string($value):
            default:
                $value = preg_replace('/[\x00\x1A\x22\x27\x5C]/us', '\\\$0', $value);
                $value = preg_replace("/\n/us", '\\n', $value);
                $value = preg_replace("/\r/us", '\\r', $value);
                $value = preg_replace("/\f/us", '\\f', $value);
            break;
        }
        return "'" . $value . "'";
    }

}

?>