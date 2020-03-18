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

namespace Yana\Db\Export;

/**
 * <<decorator>>  database Creator
 *
 * This decorator class is intended to create SQL DDL (data definition language)
 * from YANA Framework - database structure files.
 *
 * For this task it provides functions which create specific
 * DDL for various DBS.
 *
 * @package     yana
 * @subpackage  db
 */
class SqlFactory extends \Yana\Db\Export\AbstractSqlFactory
{

    /**
     * @var \Yana\Db\Ddl\Database
     * @ignore
     */
    protected $schema = null;

    /**
     * create a new instance
     *
     * This class requires an object of class DbStructure
     * as input.
     *
     * Example of usage:
     * <code>
     * // create new instance of this class
     * $dbc = new \Yana\Db\Export\SqlFactory( XDDL::getDatabase('guestbook'));
     * // since version 2.9.6 you may also write
     * $db = 'guestbook';
     * $dbc = new \Yana\Db\Export\SqlFactory($db);
     * // create SQL DDL (here: using MySQL syntax)
     * $arrayOfStmts = $dbc->createMySQL();
     * // This will output the result as text
     * print implode("\n", $arrayOfStmts);
     * // This will send the statements to the database
     * $db = new DbStream(new DbStructure('guestbook'));
     * foreach ($arrayOfStmts as $stmt)
     * {
     *    $db->query($stmt);
     * }
     * </code>
     *
     * @param   \Yana\Db\Ddl\Database  $schema  contains the XDDL source
     */
    public function __construct(\Yana\Db\Ddl\Database $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Transform the schema XDDL source to an array of SQL statements.
     *
     * The function returns a numeric array of SQL statements.
     * If you want to send the result to a SQL file you should "implode()" the array to a string.
     *
     * @param   int  $dbmsType  index of DBMS to use
     * @return  array
     */
    private function _transformToSql($dbmsType)
    {
        $xslDocument = $this->_getProvider()->getXslDocument($dbmsType);
        $xmlDocument = new \DOMDocument();
        $xddlDocument = $this->schema->serializeToXDDL();
        $xmlDocument->loadXML($xddlDocument->asXML()); // Source file
        return $this->_getProcessor()->transformDocument($xmlDocument, $xslDocument);
    }

    /**
     * Create SQL for MySQL.
     *
     * Returns a numeric array of SQL statements.
     * Each element is a single statement.
     * If you want to send the result to a SQL file
     * you should "implode()" the array to a string.
     *
     * Note: This function might take a while to create a result.
     * Consider increasing the PHP execution time limit by calling set_time_limit() with a value of 500 or higher.
     *
     * @return  array
     */
    public function createMySQL()
    {
        $sqlStatements = $this->_transformToSql(\Yana\Db\Export\Xsl\IsProvider::MYSQL);
        return $sqlStatements;
    }

    /**
     * Create SQL for PostgreSQL.
     *
     * Returns a numeric array of SQL statements.
     * Each element is a single statement.
     * If you want to send the result to a SQL file
     * you should "implode()" the array to a string.
     *
     * Note: This function might take a while to create a result.
     * Consider increasing the PHP execution time limit by calling set_time_limit() with a value of 500 or higher.
     *
     * @return  array
     */
    public function createPostgreSQL()
    {
        $sqlStatements = $this->_transformToSql(\Yana\Db\Export\Xsl\IsProvider::POSTGRESQL);
        return $sqlStatements;
    }

    /**
     * create SQL for MS SQL Server
     *
     * Returns a numeric array of SQL statements.
     * Each element is a single statement.
     * If you want to send the result to a SQL file
     * you should "implode()" the array to a string.
     *
     * Note: This function might take a while to create a result.
     * Consider increasing the PHP execution time limit by calling set_time_limit() with a value of 500 or higher.
     *
     * @return  array
     * @codeCoverageIgnore
     */
    public function createMSSQL()
    {
        $sqlStatements = $this->_transformToSql(\Yana\Db\Export\Xsl\IsProvider::MSSQL);
        return $sqlStatements;
        /* this is the result var that will be returned when finished */
        $SQL = array();

        /* this is for statements, which have to come last */
        $lastSQL = array();

        // Auto-Increment example:
        //CREATE TABLE new_employees
        //(
        // id_num int IDENTITY(1,1),
        // fname varchar (20),
        // minit char(1),
        // lname varchar(30)
        //)
        /*
         *  Create Table
         */
        assert(!isset($table), 'cannot redeclare variable $table');
        foreach ($this->structure->getTables() as $table)
        {
            assert(is_string($table) && !empty($table), 'is_string($table) && !empty($table)');
            $stmt = "CREATE TABLE [dbo].[".YANA_DATABASE_PREFIX."{$table}] (\n";

            $listOfColumns = $this->structure->getColumns($table);
            $listOfIndexes = $this->structure->getIndexes($table);

            /*
             *  Create Column
             */
            assert(!isset($i), 'cannot redeclare variable $i');
            for ($i = 0; $i < count($listOfColumns); $i++)
            {
                $column = $listOfColumns[$i];

                $type = $this->structure->getType($table, $column);
                $length = $this->structure->getLength($table, $column);
                $precision = $this->structure->getPrecision($table, $column);
                $default = $this->structure->getDefault($table, $column);
                $comment = $this->structure->getDescription($table, $column);
                $stmt .= "\t[{$column}] ";

                /*  MS-SQL:
                 *  Add Type
                 */
                if ($this->structure->isForeignKey($table, $column)) {
                    $foreignTable = $this->structure->getTableByForeignKey($table, $column);
                    $foreignPrimaryKey = $this->structure->getPrimaryKey($foreignTable);
                    $type = $this->structure->getType($foreignTable, $foreignPrimaryKey);
                    $length = $this->structure->getLength($foreignTable, $foreignPrimaryKey);
                    $precision = $this->structure->getPrecision($foreignTable, $foreignPrimaryKey);
                }
                switch (mb_strtolower($type))
                {
                    case "time":
                        $type   = "integer";
                        $length = 0;
                    break;

                    case "reference":
                        $type   = "varchar";
                        if (empty($length)) {
                            $length = 255;
                        }
                    break;

                    case "image": case "file":
                        $type   = "varchar";
                        $length = 128;
                    break;

                    case "ip":
                        $type   = "varchar";
                        $length = 15;
                    break;

                    case "profile":
                        $type   = "varchar";
                        $length = 128;
                    break;

                    case "mail": case "url": case "uri": case "string":
                        if (empty($length)) {
                            $type = 'text';
                        } else {
                            $type = 'varchar';
                        }
                    break;

                    case "select": case "array": case "text":
                        $type  = 'text';
                        $length = 0;
                    break;

                    case "int": case "integer":
                        $type = "integer";
                        $length = 0;
                    break;

                    case "float": case "double":
                        $type = "float";
                        $length = 0;
                    break;

                    case "boolean": case "bool":
                        $type = "bit";
                    break;

                    default:
                        if ($length == 0) {
                            $type = 'text';
                        } else {
                            $type = 'varchar';
                        }
                    break;

                } /* end switch */
                $stmt .= "$type";

                /*  MS-SQL:
                 *  Add Length
                 */
                if (is_int($length) && $length > 0 && $type !== 'text') {
                    $stmt .= "({$length})";
                }

                /*  MS-SQL:
                 *  Decide wether this column is nullable
                 */
                if ($this->structure->isNullable($table, $column) === false) {
                    $stmt .= " NOT NULL";
                } else {
                    $stmt .= " NULL";
                }

                /*  MS-SQL:
                 *  Add Autonumber
                 */
                if ($this->structure->isAutonumber($table, $column) === true) {
                    $stmt .= " IDENTITY (1,1)";

                /*  MS-SQL:
                 *  Add Default-Value
                 */
                } elseif (is_string($default)) {
                    $stmt .= " DEFAULT '{$default}'";
                } elseif (is_bool($default)) {
                    $stmt .= " DEFAULT " . ( ($default) ? '1' : '0' );
                } elseif (is_scalar($default)) {
                    $stmt .= " DEFAULT {$default}";
                }

                /*  MS-SQL:
                 *  Add Unique Constraint
                 */
                if ($this->structure->isUnique($table, $column) === true) {
                    assert(is_array($lastSQL), 'is_array($lastSQL)');
                    $lastSQL[] = "ALTER TABLE [dbo].[".YANA_DATABASE_PREFIX."{$table}] " .
                        "ADD CONSTRAINT {$table}_{$column}_uq UNIQUE ({$column});";
                }

                /*  MS-SQL:
                 *  Add Primary Key
                 */
                if ($this->structure->isPrimaryKey($table, $column) === true) {
                    $stmt .= " CONSTRAINT {$table}_pk PRIMARY KEY";
                }

                /*  MS-SQL:
                 *  Create Foreign Key Constraints
                 */
                if ($this->structure->isForeignKey($table, $column) === true) {
                    $ftable = $this->structure->getTableByForeignKey($table, $column);
                    assert(is_array($lastSQL), 'is_array($lastSQL)');
                    $lastSQL[] = "ALTER TABLE [dbo].[".YANA_DATABASE_PREFIX."{$table}] " .
                        "ADD CONSTRAINT {$table}_{$ftable}_fk FOREIGN KEY ({$column}) " .
                        "REFERENCES [dbo].[".YANA_DATABASE_PREFIX."{$ftable}];";
                }

                /*  MS-SQL:
                 *  Add Comment
                 */
                if (!empty($comment)) {
                    /* intentionally left blank */
                }

                if ($i<count($listOfColumns) - 1) {
                    $stmt .= ",";
                }
                $stmt .= "\n";

            } /* end foreach column */
            unset($i);

            assert(is_array($SQL), 'is_array($SQL)');
            $SQL[] = $stmt.");";
            unset($stmt);

            /*  MS-SQL:
             *  Create Indexes
             */
            if (!empty($listOfIndexes)) {
                assert(!isset($index), 'cannot redeclare variable $index');
                foreach ($listOfIndexes as $index)
                {
                    assert(is_array($SQL), 'is_array($SQL)');
                    $SQL[] = "CREATE NONCLUSTERED INDEX {$table}_{$index}_idx " .
                        "ON [".YANA_DATABASE_PREFIX."{$table}] ([{$index}]);";
                }
                unset($index);
            }

        } /* end foreach table */
        unset($table);

        assert(is_array($lastSQL), 'is_array($lastSQL)');
        assert(!isset($stmt), 'cannot redeclare variable $stmt');
        foreach ($lastSQL as $stmt)
        {
            assert(is_array($SQL), 'is_array($SQL)');
            $SQL[] = $stmt;
        }
        unset($stmt);

        return $SQL;
    }

    /**
     * Same as \Yana\Db\Export\SqlFactory::createMSSQL().
     *
     * @return  array
     * @see     \Yana\Db\Export\SqlFactory::createMSSQL()
     * @codeCoverageIgnore
     */
    public function createMSAccess()
    {
        return $this->createMSSQL();
    }

    /**
     * Create SQL for IBM DB2.
     *
     * Returns a numeric array of SQL statements.
     * Each element is a single statement.
     * If you want to send the result to a SQL file
     * you should "implode()" the array to a string.
     *
     * Note: This function might take a while to create a result.
     * Consider increasing the PHP execution time limit by calling set_time_limit() with a value of 500 or higher.
     *
     * @return  array
     * @codeCoverageIgnore
     */
    public function createDB2()
    {
        $sqlStatements = $this->_transformToSql(\Yana\Db\Export\Xsl\IsProvider::DB2);
        return $sqlStatements;
        /* this is the result var that will be returned when finished */
        $SQL = array();

        /* this is for statements, which have to come last */
        $lastSQL = array();

        $builder = new \Yana\ApplicationBuilder();
        $application = $builder->buildApplication();
        $file = $application->getResource('system:/config/reserved_sql_keywords.file');
        $sql_keywords = file($file->getPath());
        unset($file);

        /*
         *  Create Table
         */
        assert(!isset($table), 'cannot redeclare variable $table');
        foreach ($this->structure->getTables() as $table)
        {
            assert(is_string($table) && !empty($table), 'is_string($table) && !empty($table)');
            $tableName = YANA_DATABASE_PREFIX . $table;
            if (\Yana\Util\Hashtable::quickSearch($sql_keywords, $tableName) !== false) {
                $tableName = "\"{$tableName}\"";
            }

            $stmt = "CREATE TABLE {$tableName}(\n";

            $listOfColumns = $this->structure->getColumns($table);
            $listOfForeignKeys = $this->structure->getForeignKeys($table);
            $listOfIndexes = $this->structure->getIndexes($table);

            /*
             *  Create Column
             */
            assert(!isset($i), 'cannot redeclare variable $i');
            for ($i = 0; $i < count($listOfColumns); $i++)
            {
                $column = $listOfColumns[$i];

                if (\Yana\Util\Hashtable::quickSearch($sql_keywords, $column) !== false) {
                    $columnName = "\"{$column}\"";
                } else {
                    $columnName = $column;
                }

                $type = $this->structure->getType($table, $column);
                $length = $this->structure->getLength($table, $column);
                $precision = $this->structure->getPrecision($table, $column);
                $default = $this->structure->getDefault($table, $column);
                $comment = $this->structure->getDescription($table, $column);
                $stmt .= "\t{$columnName} ";

                /*  DB2:
                 *  Add Type
                 */
                if ($this->structure->isForeignKey($table, $column)) {
                    $foreignTable = $this->structure->getTableByForeignKey($table, $column);
                    $foreignPrimaryKey = $this->structure->getPrimaryKey($foreignTable);
                    $type = $this->structure->getType($foreignTable, $foreignPrimaryKey);
                    $length = $this->structure->getLength($foreignTable, $foreignPrimaryKey);
                    $precision = $this->structure->getPrecision($foreignTable, $foreignPrimaryKey);
                }
                switch (mb_strtolower($type))
                {
                    case "time":
                        $type   = "INTEGER";
                        $length = 0;
                    break;

                    case "reference":
                        $type   = "VARCHAR";
                        if (empty($length)) {
                            $length = 255;
                        }
                    break;

                    case "image": case "file":
                        $type   = "VARCHAR";
                        $length = 128;
                    break;

                    case "ip":
                        $type   = "VARCHAR";
                        $length = 15;
                    break;

                    case "profile":
                        $type   = "VARCHAR";
                        $length = 128;
                    break;

                    case "mail": case "url": case "uri": case "string":
                        if (empty($length)) {
                            $type = 'LONG VARCHAR';
                        } else {
                            $type = 'VARCHAR';
                        }
                    break;

                    case "select": case "array": case "text":
                        $type  = 'LONG VARCHAR';
                        $length = 0;
                    break;

                    case "int": case "integer":
                        $type = "INTEGER";
                        $length = 0;
                    break;

                    case "float": case "double":
                        if ($length > 0 || $precision > 0) {
                            $type .= "($length,$precision)";
                            $length = 0;
                        }
                    break;

                    case "boolean": case "bool":
                        $type = "SMALLINT";
                        $length = 1;
                    break;

                    default:
                        if ($length == 0) {
                            $type = 'CLOB';
                        } else {
                            $type = 'VARCHAR';
                        }
                    break;

                } /* end switch */
                $stmt .= "$type";

                /*  DB2:
                 *  Add Length
                 */
                if (is_int($length) && $length > 0 && $type !== 'text') {
                    $stmt .= "({$length})";
                }

                /*  DB2:
                 *  Decide wether this column is nullable
                 */
                $isNullable = $this->structure->isNullable($table, $column);
                if ($isNullable === false || $this->structure->isAutonumber($table, $column) === true) {
                    $stmt .= " NOT NULL";
                }
                unset ($isNullable);
                /*  DB2:
                 *  Add Autonumber
                 */
                if ($this->structure->isAutonumber($table, $column) === true) {
                    $stmt .= " GENERATED ALWAYS AS IDENTITY (START WITH 1, INCREMENT BY 1, NO CACHE)";

                /*  DB2:
                 *  Add Default-Value
                 */
                } elseif (is_string($default)) {
                    $stmt .= " DEFAULT '{$default}'";
                } elseif (is_bool($default)) {
                    $stmt .= " DEFAULT " . ( ($default) ? '1' : '0' );
                } elseif (is_scalar($default)) {
                    $stmt .= " DEFAULT {$default}";
                }

                /*  DB2:
                 *  Add Primary Key
                 */
                if ($this->structure->isPrimaryKey($table, $column) === true) {
                    $constraintName = "{$table}_pk";
                    if (mb_strlen($constraintName) > 18) {
                        assert(!isset($constStart), 'Cannot redeclare var $constStart');
                        $constStart = mb_strlen($constraintName)-18;
                        assert(!isset($constLength), 'Cannot redeclare var $constLength');
                        $constLength = mb_strlen($constraintName);
                        $constraintName = mb_substr($constraintName, $constStart, $constLength);
                        unset($constStart, $constLength);
                    }
                    $stmt .= " CONSTRAINT {$constraintName} PRIMARY KEY";
                }

                /*  DB2:
                 *  Add Primary Key
                 */
                if ($this->structure->isUnique($table, $column) === true) {
                    $constraintName = "{$table}_{$column}_uq";
                    if (mb_strlen($constraintName) > 18) {
                        $constraintName = "{$column}_uq";
                    }
                    if (mb_strlen($constraintName) > 18) {
                        assert(!isset($constStart), 'Cannot redeclare var $constStart');
                        $constStart = mb_strlen($constraintName)-18;
                        assert(!isset($constLength), 'Cannot redeclare var $constLength');
                        $constLength = mb_strlen($constraintName);
                        $constraintName = mb_substr($constraintName, $constStart, $constLength);
                        unset($constStart, $constLength);
                    }
                    $stmt .= " CONSTRAINT {$constraintName} UNIQUE";
                }

                /*  DB2:
                 *  Add Comment
                 */
                if (!empty($comment)) {
                    assert(is_array($lastSQL), 'is_array($lastSQL)');
                    $lastSQL[] = "COMMENT ON {$tableName} ({$columnName} IS " .
                        "'".addcslashes($comment, "'")."');";
                }

                if ($i<count($listOfColumns) - 1) {
                    $stmt .= ",";
                }
                $stmt .= "\n";

            } /* end foreach column */
            unset($i);

            assert(is_array($SQL), 'is_array($SQL)');
            $SQL[] = $stmt.");";
            unset($stmt);

            /*  DB2:
             *  Create Indexes
             */
            if (!empty($listOfIndexes)) {
                assert(!isset($index), 'cannot redeclare variable $index');
                foreach ($listOfIndexes as $index)
                {
                    if (\Yana\Util\Hashtable::quickSearch($sql_keywords, $index) !== false) {
                        $indexName = "\"{$index}\"";
                    } else {
                        $indexName = $index;
                    }
                    assert(is_array($SQL), 'is_array($SQL)');
                    $SQL[] = "CREATE INDEX {$table}_{$index}_idx ON {$tableName} ({$indexName});";
                } /* end foreach index */
                unset($index);
            }

            /*  DB2:
             *  Create Foreign Key Constraints
             */
            if (!empty($listOfForeignKeys)) {
                assert(!isset($i), 'Cannot redeclare variable $i');
                $i = 0;
                foreach ($listOfForeignKeys as $foreignKey => $foreignTable)
                {
                    $foreignKey = mb_strtolower($foreignKey);
                    $foreignPrimaryKey = $this->structure->getPrimaryKey($foreignTable);
                    $foreignTableName = YANA_DATABASE_PREFIX . $foreignTable;
                    if (\Yana\Util\Hashtable::quickSearch($sql_keywords, $foreignTableName) !== false) {
                        $foreignTableName = "\"{$foreignTableName}\"";
                    } else {
                        $foreignTableName = $foreignTableName;
                    }
                    if (\Yana\Util\Hashtable::quickSearch($sql_keywords, $foreignKey) !== false) {
                        $foreignKeyName = "\"{$foreignKey}\"";
                    } else {
                        $foreignKeyName = $foreignKey;
                    }

                    $constraintName = "{$table}_fk_{$i}";
                    assert(is_array($lastSQL), 'is_array($lastSQL)');
                    $lastSQL[] = "ALTER TABLE {$tableName} ADD CONSTRAINT {$constraintName} " .
                        "FOREIGN KEY ({$foreignKeyName}) REFERENCES {$foreignTableName};";
                    ++$i;
                }
                unset($i);
            }

        } /* end foreach table */
        unset($table);

        assert(is_array($lastSQL), 'is_array($lastSQL)');
        assert(!isset($stmt), 'cannot redeclare variable $stmt');
        foreach ($lastSQL as $stmt)
        {
            assert(is_array($SQL), 'is_array($SQL)');
            $SQL[] = $stmt;
        }
        unset($stmt);

        return $SQL;

    }

    /**
     * Create SQL for Oracle.
     *
     * Returns a numeric array of SQL statements.
     * Each element is a single statement.
     * If you want to send the result to a SQL file
     * you should "implode()" the array to a string.
     *
     * Note: This function might take a while to create a result.
     * Consider increasing the PHP execution time limit by calling set_time_limit() with a value of 500 or higher.
     *
     * @return  array
     * @codeCoverageIgnore
     */
    public function createOracleDB()
    {
        $sqlStatements = $this->_transformToSql(\Yana\Db\Export\Xsl\IsProvider::ORACLEDB);
        return $sqlStatements;
        /* this is the result var that will be returned when finished */
        $SQL = array();

        /* this is for statements, which have to come last */
        $lastSQL = array();

        /*
         *  Create Table
         */
        assert(!isset($table), 'cannot redeclare variable $table');
        foreach ($this->structure->getTables() as $table)
        {
            assert(is_string($table) && !empty($table), 'is_string($table) && !empty($table)');
            $stmt = "CREATE TABLE \"".YANA_DATABASE_PREFIX."{$table}\" (\n";

            $listOfColumns      = $this->structure->getColumns($table);
            $listOfIndexes      = $this->structure->getIndexes($table);
            $listOfForeignKeys = $this->structure->getForeignKeys($table);
            $seq_cnt              = 0;

            /*
             *  Create Column
             */
            assert(!isset($i), 'cannot redeclare variable $i');
            for ($i = 0; $i < count($listOfColumns); $i++)
            {
                $column = $listOfColumns[$i];

                $type = $this->structure->getType($table, $column);
                $length = $this->structure->getLength($table, $column);
                $precision = $this->structure->getPrecision($table, $column);
                $default = $this->structure->getDefault($table, $column);
                $comment = $this->structure->getDescription($table, $column);
                $stmt .= "\t\"{$column}\" ";

                /*  Oracle:
                 *  Add Type + Length
                 */
                switch (mb_strtolower($type))
                {
                    case "time":
                        $type   = 'INTEGER';
                    break;

                    case "reference":
                        if (empty($length)) {
                            $type   = "VARCHAR2(255 CHAR)";
                        } else {
                            $type   = "VARCHAR2({$length} CHAR)";
                        }
                    break;

                    case "image": case "file":
                        $type   = "VARCHAR2(128 CHAR)";
                    break;

                    case "ip":
                        $type   = 'VARCHAR2(15 CHAR)';
                    break;

                    case "profile":
                        $type   = "VARCHAR2(128 CHAR)";
                    break;

                    case "mail": case "url": case "uri": case "string":
                        if (empty($length)) {
                            $type = 'CLOB';
                        } else {
                            $type = 'VARCHAR2';
                            if (!empty($length)) {
                                $type .= "({$length} CHAR)";
                            }
                        }
                    break;

                    case "select": case "array": case "text":
                        $type  = 'CLOB';
                    break;

                    case "int": case "integer":
                        $type = 'INTEGER';
                    break;

                    case "float": case "double":
                        $type  = 'NUMBER';
                        if ($length > 0 || $precision > 0) {
                            $type .= "($length, $precision)";
                            $length = 0;
                        }
                    break;

                    case "boolean": case "bool":
                        $type = 'NUMBER(1)';
                    break;

                    default:
                        if ($length == 0) {
                            $type = 'CLOB';
                        } else {
                            $type = 'VARCHAR2';
                            if (!empty($length)) {
                                $type .= "({$length} CHAR)";
                            }
                        }
                    break;

                } /* end switch */
                $stmt .= "$type";

                /*  Oracle:
                 *  Decide wether this column is nullable
                 */
                $isNullable = $this->structure->isNullable($table, $column);
                if ($isNullable === false || $this->structure->isAutonumber($table, $column) === true) {
                    $stmt .= " NOT NULL";
                }

                /*  Oracle:
                 *  Add Autonumber
                 */
                if ($this->structure->isAutonumber($table, $column) === true) {
                    $lastSQL[] = "CREATE SEQUENCE \"{$table}_sq{$seq_cnt}\";";
                    assert(!isset($tmp), 'cannot redeclare variable $tmp');
                    $tmp  = "CREATE OR REPLACE TRIGGER \"{$table}_{$column}_inc\"\n";
                    $tmp .= "\tBEFORE INSERT ON \"{$table}\"\n";
                    $tmp .= "\tFOR EACH ROW BEGIN\n";
                    $tmp .= "\tSELECT \"{$table}_{$column}_sq\".nextval INTO :new.\"{$column}\" " .
                        "FROM dual;\n";
                    $tmp .= "\tEND;\n;";
                    $lastSQL[] = $tmp;
                    $seq_cnt++;
                    unset($tmp);

                /*  Oracle:
                 *  Add Default-Value
                 */
                } elseif (is_string($default)) {
                    $stmt .= " DEFAULT '{$default}'";
                } elseif (is_bool($default)) {
                    $stmt .= " DEFAULT " . ( ($default) ? '1' : '0' );
                } elseif (is_scalar($default)) {
                    $stmt .= " DEFAULT {$default}";
                }

                /*  Oracle:
                 *  Add Unique Constraint
                 */
                if ($this->structure->isUnique($table, $column) === true) {
                    assert(is_array($lastSQL), 'is_array($lastSQL)');
                    $lastSQL[] = "ALTER TABLE \"".YANA_DATABASE_PREFIX."{$table}\" ADD CONSTRAINT " .
                        "\"{$table}_{$column}_uq\" UNIQUE (\"{$column}\");";
                }

                /*  Oracle:
                 *  Add Comment
                 */
                if (!empty($comment)) {
                    assert(is_array($lastSQL), 'is_array($lastSQL)');
                    $lastSQL[] = "COMMENT ON COLUMN \"".YANA_DATABASE_PREFIX."{$table}\"." .
                        "\"{$column}\" IS '".addcslashes($comment, "'")."';";
                }

                $stmt .= ",\n";

            } /* end foreach column */
            unset($i,$seq_cnt);

            /*  Oracle:
             *  Add Primary Key
             */
            $stmt .= "\tCONSTRAINT \"{$table}_pk\" PRIMARY KEY (\"" .
                $this->structure->getPrimaryKey($table)."\")\n";

            assert(is_array($SQL), 'is_array($SQL)');
            $SQL[] = $stmt.");";
            unset($stmt);

            /*  Oracle:
             *  Create Indexes
             */
            if (!empty($listOfIndexes)) {
                assert(!isset($index), 'cannot redeclare variable $index');
                $i = 0;
                foreach ($listOfIndexes as $index)
                {
                    assert(is_array($SQL), 'is_array($SQL)');
                    $SQL[] = "CREATE INDEX \"{$table}_idx{$i}\" ON \"" . YANA_DATABASE_PREFIX .
                        "{$table}\" (\"{$index}\");";
                    $i++;
                }
                unset($i, $index);
            }

            /*
             *  Create Foreign Key Constraints
             */
            if (!empty($listOfForeignKeys)) {
                $i = 0;
                foreach ($listOfForeignKeys as $foreignKey => $foreignTable)
                {
                    $foreignKey = mb_strtolower($foreignKey);
                    $foreignPrimaryKey = $this->structure->getPrimaryKey($foreignTable);
                    $constraintName = "{$table}_fk{$i}";
                    assert(is_array($lastSQL), 'is_array($lastSQL)');
                    $lastSQL[] = "ALTER TABLE \"".YANA_DATABASE_PREFIX."{$table}\" ADD CONSTRAINT " .
                        "\"$constraintName\" FOREIGN KEY (\"{$foreignKey}\") REFERENCES " .
                        "\"".YANA_DATABASE_PREFIX."{$foreignTable}\" (\"{$foreignPrimaryKey}\");";
                    $i++;
                }
                unset($i, $foreignKey, $foreignTable);
            }

        } /* end foreach table */
        unset($table);

        assert(is_array($lastSQL), 'is_array($lastSQL)');
        assert(!isset($stmt), 'cannot redeclare variable $stmt');
        foreach ($lastSQL as $stmt)
        {
            assert(is_array($SQL), 'is_array($SQL)');
            $SQL[] = $stmt;
        }
        unset($stmt);

        return $SQL;
    }
}

?>