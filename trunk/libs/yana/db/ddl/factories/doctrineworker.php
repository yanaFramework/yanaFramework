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

namespace Yana\Db\Ddl\Factories;

/**
 * <<worker>> Process database reverse engineering task.
 *
 * @package     yana
 * @subpackage  db
 */
class DoctrineWorker extends \Yana\Db\Ddl\Factories\AbstractDoctrineWorker
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
    public function createDatabase(): \Yana\Db\Ddl\Database
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
        assert(!isset($table), 'Cannot redeclare var $table');
        foreach ($connection->listTables() as $table)
        {
            assert(!isset($tableName), 'Cannot redeclare var $tableName');
            $tableName = $table->getName();
            $table = $database->addTable($tableName); // get \Yana\Db\Ddl\Table object

            $this->_createColumns($table, $tableName);
            $this->_createIndexes($table, $tableName);

            unset($tableName);
        } // end foreach
        unset($table);

        assert(!isset($table), 'Cannot redeclare var $table');
        foreach ($database->getTables() as $table)
        {
            $this->_createConstraints($table, $table->getName());
        } // end foreach
        unset($table);

        return $database;
    }

    /**
     * Build sequences.
     *
     * @param  \Yana\Db\Ddl\Database  $database  to add sequences to
     */
    protected function _createSequences(\Yana\Db\Ddl\Database $database)
    {
        assert(!isset($mapper), 'Cannot redeclare var $mapper');
        $mapper = $this->_getMapper();

        assert(!isset($sequenceName), 'Cannot redeclare var $sequenceName');
        assert(!isset($sequence), 'Cannot redeclare var $sequence');
        foreach($this->_getWrapper()->listSequences() as $sequence)
        {
            $sequenceName = (string) $sequence->getName();
            if (!$database->isSequence($sequenceName)) {
                $mapper->createSequence($database, $sequence, $sequence->getName());
            }
        }
        unset($sequence, $sequenceName);
    }

    /**
     * Get column information.
     *
     * @param  \Yana\Db\Ddl\Table  $table      to add columns to
     * @param  string              $tableName  in source table name (may contain prefix)
     */
    protected function _createColumns(\Yana\Db\Ddl\Table $table, string $tableName)
    {
        assert(!isset($mapper), 'Cannot redeclare var $mapper');
        $mapper = $this->_getMapper();

        assert(!isset($columnInfo), 'Cannot redeclare var $columnInfo');
        assert(!isset($columnName), 'Cannot redeclare var $columnName');
        foreach ($this->_getWrapper()->listTableColumns($tableName) as $columnName => $columnInfo)
        {
            $columnName = $columnInfo->getName();
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
    protected function _createIndexes(\Yana\Db\Ddl\Table $table, string $tableName)
    {
        assert(!isset($mapper), 'Cannot redeclare var $mapper');
        $mapper = $this->_getMapper();

        assert(!isset($indexInfo), 'Cannot redeclare var $indexInfo');
        assert(!isset($indexName), 'Cannot redeclare var $indexName');
        foreach ($this->_getWrapper()->listTableIndexes($tableName) as $indexInfo)
        {
            $indexName = $indexInfo->getName();
            try {
                $mapper->createIndex($table, $indexInfo, $indexName);

            } catch (\Yana\Core\Exceptions\NotImplementedException $e) {
                // Compound primary keys are not supported. We skip this one.
            }
        }
        unset($indexInfo, $indexName);
    }

    /**
     * Get constraint/foreign key information.
     *
     * @param  \Yana\Db\Ddl\Table  $table      to add constraints to
     * @param  string              $tableName  in source table name (may contain prefix)
     */
    protected function _createConstraints(\Yana\Db\Ddl\Table $table, string $tableName)
    {
        assert(!isset($mapper), 'Cannot redeclare var $mapper');
        $mapper = $this->_getMapper();

        assert(!isset($contraintInfo), 'Cannot redeclare var $contraintInfo');
        assert(!isset($contraintName), 'Cannot redeclare var $contraintName');
        foreach ($this->_getWrapper()->listTableConstraints($tableName) as $contraintInfo)
        {
            $contraintName = $contraintInfo->getName();
            $mapper->createConstraint($table, $contraintInfo, (string) $contraintName);
        }
        unset($contraintInfo, $contraintName);
    }

}

?>