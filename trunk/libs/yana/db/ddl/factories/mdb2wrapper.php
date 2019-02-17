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
 * <<wrapper>> MDB2 wrapper.
 *
 * Consumed by DatabaseFactory.
 *
 * This class exports a number of relevant function of the MDB2 driver and hides the rest.
 *
 * @package     yana
 * @subpackage  db
 * @ignore
 * @codeCoverageIgnore
 */
class Mdb2Wrapper extends \Yana\Db\Ddl\Factories\AbstractMdb2Wrapper
{

    /**
     * Get name of selected database.
     *
     * @return  string
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the connection is invalid/closed
     */
    public function getDatabaseName()
    {
        assert('!isset($name); // Cannot redeclare var $name');
        $name = $this->_getConnection()->getDatabase();
        if ($name instanceof \MDB2_Error) {
            throw new \Yana\Db\ConnectionException($name->getMessage());
        }
        return $name;
    }

    /**
     * Get list of sequences in the database.
     *
     * Returns array where the keys are the sequence names and the values are the info-arrays returned by MDB2->getSequenceDefinition().
     *
     * @return  array
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the connection is invalid/closed
     */
    public function listSequences()
    {
        assert('!isset($connection); // Cannot redeclare var $connection');
        $connection = $this->_getConnection();

        assert('!isset($sequences); // Cannot redeclare var $sequences');
        $sequences = @$connection->listSequences(); // Muted since this call will otherwise raise deprecated warning
        if ($sequences instanceof \MDB2_Error) {
            throw new \Yana\Db\DatabaseException($sequences->getMessage());
        }

        assert('!isset($list); // Cannot redeclare var $list');
        $list = array();
        assert('!isset($name); // Cannot redeclare var $name');
        foreach($sequences as $name)
        {
            $info = $connection->getSequenceDefinition($name);
            if ($info instanceof \MDB2_Error) {
                throw new \Yana\Db\DatabaseException($info->getMessage());
            }
            assert('is_array($info);');
            $list[$name] = $info;
        }
        unset($sequences, $info, $name);

        return $list;
    }

    /**
     * Get list of tables.
     *
     * @return  array
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the connection is invalid/closed
     */
    public function listTables()
    {
        assert('!isset($connection); // Cannot redeclare var $connection');
        $connection = $this->_getConnection();

        assert('!isset($tables); // Cannot redeclare var $tables');
        $tables = @$connection->listTables(); // Muted since this call will otherwise raise deprecated warning
        if ($tables instanceof \MDB2_Error) {
            throw new \Yana\Db\DatabaseException($tables->getMessage());
        }

        return $tables;
    }

    /**
     * Get list of columns associated with the table.
     *
     * Returns array where the keys are the column names and the values are the info-arrays returned by MDB2->getTableFieldDefinition().
     *
     * @param   string  $tableName  must be a valid database table
     * @return  array
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the table doesn't exist
     */
    public function listTableColumns($tableName)
    {
        assert('!isset($connection); // Cannot redeclare var $connection');
        $connection = $this->_getConnection();

        assert('!isset($columns); // Cannot redeclare var $columns');
        $columns = @$connection->listTableFields($tableName); // Muted since this call will otherwise raise deprecated warning

        assert('!isset($list); // Cannot redeclare var $list');
        $list = array();
        assert('!isset($name); // Cannot redeclare var $name');
        /* @var $name string */
        foreach ($columns as $name)
        {
            assert('!isset($info); // Cannot redeclare var $info');
            $info = @$connection->getTableFieldDefinition($tableName, $name); // Muted since this call will otherwise raise deprecated warning
            if ($info instanceof \MDB2_Error) {
                throw new \Yana\Db\DatabaseException($info->getMessage());
            }
            /* MDB2 "suggests" multiple options for data-types.
             * Since we don't know which is the best guess we simply take the first one.
             */
            $list[$name] = array_shift($info);
            assert('is_array($list[$name]);');
            unset($info);
        }
        unset($name, $columns);

        return $list;
    }

    /**
     * Get list of indexes associated with the table.
     *
     * Returns array where the keys are the index names and the values are the info-arrays returned by MDB2->getTableIndexDefinition().
     *
     * @param   string  $tableName  must be a valid database table
     * @return  array
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the table doesn't exist
     */
    public function listTableIndexes($tableName)
    {
        assert('!isset($connection); // Cannot redeclare var $connection');
        $connection = $this->_getConnection();

        assert('!isset($indexes); // Cannot redeclare var $indexes');
        $indexes = @$connection->listTableIndexes($tableName); // Muted since this call will otherwise raise deprecated warning
        if ($indexes instanceof \MDB2_Error) {
            throw new \Yana\Db\DatabaseException($indexes->getMessage());
        }

        assert('!isset($list); // Cannot redeclare var $list');
        $list = array();
        assert('!isset($info); // Cannot redeclare var $info');
        assert('!isset($name); // Cannot redeclare var $name');
        /* @var $name string */
        foreach ($indexes as $name)
        {
            $info = @$connection->getTableIndexDefinition($tableName, $name); // Muted since this call will otherwise raise deprecated warning
            if ($info instanceof \MDB2_Error) {
                throw new \Yana\Db\DatabaseException($info->getMessage());
            }
            assert('is_array($info);');
            $list[$name] = $info;
        }
        unset($info, $name, $indexes);

        return $list;
    }

    /**
     * Get list of constraints associated with the table.
     *
     * Returns array where the keys are the constraint names and the values are the info-arrays returned by MDB2->getTableConstraintDefinition().
     *
     * @param   string  $tableName  must be a valid database table
     * @return  array
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the table doesn't exist
     */
    public function listTableConstraints($tableName)
    {
        assert('!isset($connection); // Cannot redeclare var $connection');
        $connection = $this->_getConnection();

        // get constraint/foreign key information
        assert('!isset($constraints); // Cannot redeclare var $constraints');
        $constraints = @$connection->listTableConstraints($tableName); // Muted since this call will otherwise raise deprecated warning
        if ($constraints instanceof \MDB2_Error) {
            throw new \Yana\Db\DatabaseException($constraints->getMessage());
        }

        assert('!isset($list); // Cannot redeclare var $list');
        $list = array();
        assert('!isset($info); // Cannot redeclare var $info');
        assert('!isset($name); // Cannot redeclare var $name');
        /* @var $name string */
        foreach ($constraints as $name)
        {
            $info = @$connection->getTableConstraintDefinition($tableName, $name); // Muted since this call will otherwise raise deprecated warning
            if ($info instanceof \MDB2_Error) {
                throw new \Yana\Db\DatabaseException($info->getMessage());
            }
            assert('is_array($info);');
            $list[$name] = $info;
        }
        unset($info, $name, $constraints);

        return $list;
    }

    /**
     * @test
     */
    public function listViews()
    {
        assert('!isset($connection); // Cannot redeclare var $connection');
        $connection = $this->_getConnection();

        assert('!isset($views); // Cannot redeclare var $views');
        $views = @$connection->listViews(); // Muted since this call will otherwise raise deprecated warning
        if ($views instanceof \MDB2_Error) {
            throw new \Yana\Db\DatabaseException($views->getMessage());
        }

        return $views;
    }

}

?>