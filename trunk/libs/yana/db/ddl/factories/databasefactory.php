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

namespace Yana\Db\Ddl\Factories;

/**
 * <<factory>> Produces database objects.
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
 * @codeCoverageIgnore
 */
class DatabaseFactory extends \Yana\Db\Ddl\Factories\AbstractDatabaseFactory
{

    /**
     * Create database from tableInfo.
     *
     * Try to extract some information on the structure of a database from the
     * information provided by PEAR-MDB2's Reverse module.
     *
     * @param   \Yana\Db\Ddl\Factories\IsMdb2Wrapper  $connection  MDB2 database connection
     * @return  \Yana\Db\Ddl\Database
     * @throws  \Yana\Db\ConnectionException  when unable to open connection to database
     */
    public function createDatabase(\Yana\Db\Ddl\Factories\IsMdb2Wrapper $connection)
    {
        $mapper = $this->_getMapper();

        /*
         * build database
         */
        $database = new \Yana\Db\Ddl\Database($connection->getDatabaseName());

        /*
         * build sequences
         */
        assert('!isset($sequenceName); // Cannot redeclare var $sequenceName');
        assert('!isset($sequenceInfo); // Cannot redeclare var $sequenceInfo');
        foreach($connection->listSequences() as $sequenceName => $sequenceInfo)
        {
            $mapper->createSequence($database, $sequenceInfo, $sequenceName);
        }
        unset($sequenceInfo, $sequenceName);

        /*
         * build tables
         */
        assert('!isset($tableName); // Cannot redeclare var $tableName');
        foreach ($connection->listTables() as $tableName)
        {
            /*
             * remove prefix
             */
            $name = preg_replace('/^' . preg_quote(YANA_DATABASE_PREFIX, '/') . '/', '', $tableName);
            $table = $database->addTable($name); // get \Yana\Db\Ddl\Table object
            unset($name);

            /*
             * get column information
             */
            assert('!isset($columnInfo); // Cannot redeclare var $columnInfo');
            assert('!isset($columnName); // Cannot redeclare var $columnName');
            foreach ($connection->listTableColumns($tableName) as $columnName => $columnInfo)
            {
                $mapper->createColumn($table, $columnInfo, $columnName);
            }
            unset($columnInfo, $columnName);

            /*
             * get index information
             */
            assert('!isset($indexInfo); // Cannot redeclare var $indexInfo');
            assert('!isset($indexName); // Cannot redeclare var $indexName');
            foreach ($connection->listTableIndexes($tableName) as $indexName => $indexInfo)
            {
                $mapper->createIndex($table, $indexInfo, $indexName);
            }
            unset($indexInfo, $indexName);

            /*
             * get constraint/foreign key information
             */
            assert('!isset($contraintInfo); // Cannot redeclare var $contraintInfo');
            assert('!isset($contraintName); // Cannot redeclare var $contraintName');
            foreach ($connection->listTableConstraints($tableName) as $contraintName => $contraintInfo)
            {
                $mapper->createConstraint($table, $contraintInfo, $contraintName);
            }
            unset($contraintInfo, $contraintName);

        } // end foreach
        unset($tableName);

        return $database;
    }

}

?>