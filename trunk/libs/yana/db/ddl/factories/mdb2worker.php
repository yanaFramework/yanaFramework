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
 * <<worker>> Process database reverse engineering task.
 *
 * @package     yana
 * @subpackage  db
 */
class Mdb2Worker extends \Yana\Db\Ddl\Factories\AbstractMdb2Worker
{

    /**
     * Create database.
     *
     * Try to extract some information on the structure of a database from the
     * schema objects provided by Doctrine DBAL schema.
     *
     * @return  \Yana\Db\Ddl\Database
     * @throws  \Yana\Db\ConnectionException  when unable to open connection to database
     */
    public function createDatabase()
    {
        $connection = $this->_getWrapper();

        /*
         * build database
         */
        $database = new \Yana\Db\Ddl\Database($connection->getDatabaseName());

        $this->_createSequences($database);

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

            $this->_createColumns($table, $tableName);
            $this->_createIndexes($table, $tableName);
            $this->_createConstraints($table, $tableName);

        } // end foreach
        unset($tableName);

        return $database;
    }

    /**
     * Build sequences.
     *
     * @param   \Yana\Db\Ddl\Database  $database  to add sequences to
     */
    protected function _createSequences(\Yana\Db\Ddl\Database $database)
    {
        assert('!isset($mapper); // Cannot redeclare var $mapper');
        $mapper = $this->_getMapper();

        assert('!isset($sequenceName); // Cannot redeclare var $sequenceName');
        assert('!isset($sequenceInfo); // Cannot redeclare var $sequenceInfo');
        foreach ($this->_getWrapper()->listSequences() as $sequenceName => $sequenceInfo)
        {
            try {
                $mapper->createSequence($database, $sequenceInfo, $sequenceName);
            } catch (\Yana\Core\Exceptions\AlreadyExistsException $e) {
                // skip
            }
        }
        unset($sequenceInfo, $sequenceName);
    }

    /**
     * Get column information.
     *
     * @param  \Yana\Db\Ddl\Table  $table      to add columns to
     * @param  string              $tableName  in source table name (may contain prefix)
     */
    protected function _createColumns(\Yana\Db\Ddl\Table $table, $tableName)
    {
        assert('!isset($mapper); // Cannot redeclare var $mapper');
        $mapper = $this->_getMapper();

        assert('!isset($columnInfo); // Cannot redeclare var $columnInfo');
        assert('!isset($columnName); // Cannot redeclare var $columnName');
        foreach ($this->_getWrapper()->listTableColumns($tableName) as $columnName => $columnInfo)
        {
            $mapper->createColumn($table, $columnInfo, $columnName);
        }
        unset($columnInfo, $columnName);
    }

    /**
     * Get index information.
     *
     * @param  \Yana\Db\Ddl\Table  $table      to add indexes to
     * @param  string              $tableName  in source table name (may contain prefix)
     */
    protected function _createIndexes(\Yana\Db\Ddl\Table $table, $tableName)
    {
        assert('!isset($mapper); // Cannot redeclare var $mapper');
        $mapper = $this->_getMapper();

        assert('!isset($indexInfo); // Cannot redeclare var $indexInfo');
        assert('!isset($indexName); // Cannot redeclare var $indexName');
        foreach ($this->_getWrapper()->listTableIndexes($tableName) as $indexName => $indexInfo)
        {
            $mapper->createIndex($table, $indexInfo, $indexName);
        }
        unset($indexInfo, $indexName);
    }

    /**
     * Get constraint/foreign key information.
     *
     * @param  \Yana\Db\Ddl\Table  $table      to add constraints to
     * @param  string              $tableName  in source table name (may contain prefix)
     */
    protected function _createConstraints(\Yana\Db\Ddl\Table $table, $tableName)
    {
        assert('!isset($mapper); // Cannot redeclare var $mapper');
        $mapper = $this->_getMapper();

        assert('!isset($contraintInfo); // Cannot redeclare var $contraintInfo');
        assert('!isset($contraintName); // Cannot redeclare var $contraintName');
        foreach ($this->_getWrapper()->listTableConstraints($tableName) as $contraintName => $contraintInfo)
        {
            $mapper->createConstraint($table, $contraintInfo, $contraintName);
        }
        unset($contraintInfo, $contraintName);
    }

}

?>