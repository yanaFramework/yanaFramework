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
     * @var \Yana\Db\NullConnection
     */
    private $_nullConnection = null;

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
            $this->_nullConnection = new \Yana\Db\NullConnection($db->getSchema(), \Yana\Db\DriverEnumeration::GENERIC, $sqlKeywordChecker);
        }
        $this->_db = $db;
        parent::__construct($this->_db->getSchema());
    }

    /**
     * Returns a fake connection.
     *
     * This "connection" can be used to build an insert query to create a SQL statement for a DBMS of your choice.
     *
     * @return  \Yana\Db\Helpers\IsSqlKeywordChecker
     */
    protected function _getNullConnection()
    {
        if (!isset($this->_nullConnection)) {
            // @codeCoverageIgnoreStart
            $this->_nullConnection = new \Yana\Db\NullConnection($this->_db->getSchema());
            // @codeCoverageIgnoreEnd
        }
        return $this->_nullConnection;
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

            $select = new \Yana\Db\Queries\Select($this->_db);
            $select->useInheritance(false);
            $insert = new \Yana\Db\Queries\Insert($this->_getNullConnection()->setDBMS(\Yana\Db\DriverEnumeration::MYSQL));
            // Loop through all tables in the database and extract each one
            foreach ($this->schema->getTables() as $table)
            {
                // Select * From table
                foreach($select->setTable($table->getName())->getResults() as $row)
                {
                    $insert
                        ->resetQuery()
                        ->useInheritance(false)
                        ->setTable($table->getName())
                        ->setValues($row);

                    /* build statement */
                    $sql[] = (string) $insert . ';';
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

            $select = new \Yana\Db\Queries\Select($this->_db);
            $select->useInheritance(false);
            $insert = new \Yana\Db\Queries\Insert($this->_getNullConnection()->setDBMS(\Yana\Db\DriverEnumeration::POSTGRESQL));
            // Loop through all tables in the database and extract each one
            foreach ($this->schema->getTables() as $table)
            {
                // Select * From table
                foreach($select->setTable($table->getName())->getResults() as $row)
                {
                    $insert
                        ->resetQuery()
                        ->useInheritance(false)
                        ->setTable($table->getName())
                        ->setValues($row);

                    /* build statement */
                    $sql[] = (string) $insert . ';';
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

            $select = new \Yana\Db\Queries\Select($this->_db);
            $select->useInheritance(false);
            $insert = new \Yana\Db\Queries\Insert($this->_getNullConnection()->setDBMS(\Yana\Db\DriverEnumeration::MSSQL));
            // Loop through all tables in the database and extract each one
            foreach ($this->schema->getTables() as $table)
            {
                // Select * From table
                foreach($select->setTable($table->getName())->getResults() as $row)
                {
                    $insert
                        ->resetQuery()
                        ->useInheritance(false)
                        ->setTable($table->getName())
                        ->setValues($row);

                    /* build statement */
                    $sql[] = (string) $insert . ';';
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
            @set_time_limit(500);

            $select = new \Yana\Db\Queries\Select($this->_db);
            $select->useInheritance(false);
            $insert = new \Yana\Db\Queries\Insert($this->_getNullConnection()->setDBMS(\Yana\Db\DriverEnumeration::DB2));
            // Loop through all tables in the database and extract each one
            foreach ($this->schema->getTables() as $table)
            {
                // Select * From table
                foreach($select->setTable($table->getName())->getResults() as $row)
                {
                    $insert
                        ->resetQuery()
                        ->useInheritance(false)
                        ->setTable($table->getName())
                        ->setValues($row);

                    /* build statement */
                    $sql[] = (string) $insert . ";";
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

            $select = new \Yana\Db\Queries\Select($this->_db);
            $select->useInheritance(false);
            $insert = new \Yana\Db\Queries\Insert($this->_getNullConnection()->setDBMS(\Yana\Db\DriverEnumeration::ORACLE));
            // Loop through all tables in the database and extract each one
            foreach ($this->schema->getTables() as $table)
            {
                // Select * From table
                foreach($select->setTable($table->getName())->getResults() as $row)
                {
                    $insert
                        ->resetQuery()
                        ->useInheritance(false)
                        ->setTable($table->getName())
                        ->setValues($row);

                    /* build statement */
                    $sql[] = (string) $insert . ';';
                }
            }
        }
        return $sql;
    }

}

?>