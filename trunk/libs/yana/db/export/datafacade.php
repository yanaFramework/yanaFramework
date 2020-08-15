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
 * <<facade>> Database copier.
 *
 * Compares a source and target to find the missing entries and creates the necessary insert statements to create them.
 *
 * @package     yana
 * @subpackage  db
 */
class DataFacade extends \Yana\Core\StdObject
{

    /**
     * @var  \Yana\Db\IsConnection
     */
    private $_sourceConnection = null;

    /**
     * @var  \Yana\Db\IsConnection
     */
    private $_targetConnection = null;

    /**
     * @var  \Yana\Db\Export\DataFactory
     */
    private $_sourceDataFactory = null;

    /**
     * @var  \Yana\Db\Export\DataFactory
     */
    private $_targetDataFactory = null;

    /**
     * <<constructor>> Initialize a new instance.
     *
     * @param   \Yana\Db\IsConnection  $sourceConnection  source database
     * @param   \Yana\Db\IsConnection  $targetConnection  target database
     */
    public function __construct(\Yana\Db\IsConnection $sourceConnection, \Yana\Db\IsConnection $targetConnection)
    {
        $this->_sourceConnection = $sourceConnection;
        $this->_targetConnection = $targetConnection;
    }

    /**
     * Loads and returns data factory for source connection.
     *
     * @return  \Yana\Db\Export\DataFactory
     */
    protected function _getSourceDataFactory(): \Yana\Db\Export\DataFactory
    {
        if (!isset($this->_sourceDataFactory)) {
            $this->_sourceDataFactory = new \Yana\Db\Export\DataFactory($this->getConnection()->getSchema());
        }
        return $this->_sourceDataFactory;
    }

    /**
     * Loads and returns data factory for target connection.
     *
     * @return  \Yana\Db\Export\DataFactory
     */
    protected function _getTargetDataFactory(): \Yana\Db\Export\DataFactory
    {
        if (!isset($this->_targetDataFactory)) {
            $this->_targetDataFactory = new \Yana\Db\Export\DataFactory($this->getConnection()->getSchema());
        }
        return $this->_targetDataFactory;
    }

    /**
     * Get source database connection.
     *
     * @return \Yana\Db\IsConnection
     */
    public function getSourceConnection(): \Yana\Db\IsConnection
    {
        return $this->_sourceConnection;
    }

    /**
     * Get target database connection.
     *
     * @return \Yana\Db\IsConnection
     */
    public function getTargetConnection(): \Yana\Db\IsConnection
    {
        return $this->_sourceConnection;
    }

    /**
     * Copy rows from source table to target table.
     *
     * Returns the number of rows copied.
     *
     * @param   string  $tableName  name of table to copy
     * @param   array   $ids        primary keys of rows to copy
     * @return  int
     * @throws  \Yana\Db\CommitFailedException  when insert statements failed to execute
     */
    public function copyTable(string $tableName, array $ids): int
    {
        assert(!isset($noOfCopiedRows), 'Cannot redeclare var $noOfCopiedRows');
        $noOfCopiedRows = 0;
        assert(!isset($target), 'Cannot redeclare var $target');
        $target = $this->getTargetConnection();
        assert(!isset($source), 'Cannot redeclare var $source');
        $source = $this->getSourceConnection();

        assert(!isset($i), 'Cannot redeclare var $i');
        $i = 0;
        assert(!isset($id), 'Cannot redeclare var $id');
        foreach ($ids as $id)
        {
            try {
                $key = $tableName . "." . (string) $id;
                $target->insert($key, $source->select($key));
                $i++;

            } catch (\Exception $e) {
                $message = "Unable to copy row $key to target.";
                \Yana\Log\LogManager::getLogger()->addLog($message);
            }
            if ($i === 20) { // safe point all 20 inserts
                $this->_commitChangesToTarget();
                $noOfCopiedRows += $i;
                $i = 0;
            }
        }
        $this->_commitChangesToTarget();
        $noOfCopiedRows += $i;

        return $noOfCopiedRows;
    }

    /**
     * Called by copyTable() to commit changes.
     *
     * @throws  \Yana\Db\CommitFailedException  when insert statements failed to execute
     */
    private function _commitChangesToTarget()
    {
        try {
            $this->getTargetConnection()->commit(); // may throw exception

        } catch (\Exception $e) {
            $this->getTargetConnection()->rollback();
            $message = "Failed to commit changes to target.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            \Yana\Log\LogManager::getLogger()->addLog($message . " " . $e->getMessage(), $level);
            throw new \Yana\Db\CommitFailedException($message, $level, $e);
        }
    }
    /**
     * Returns the IDs of all rows present in the source but not the target.
     *
     * In the returned array, the index is the table names and the values are the IDs of
     * missing rows.
     *
     * For example:
     * <code>
     * array(
     *     'table1' => array(1, 2, 3, 4),
     *     'table2' => array(3, 6, 8)
     * )
     * </code>
     *
     * For performance reasons this function does not compare rows that exist in both tables.
     * Note that this function is not suited for any but small tables, i.e. during installation.
     *
     * @param   \Yana\Db\Ddl\Table  $table  what should be compared
     * @return  array
     */
    public function compareAllTables(): array
    {
        assert(!isset($differences), 'Cannot redeclare var $differences');
        $differences = aray();

        /* @var $table \Yana\Db\Ddl\Table */
        foreach ($this->getTargetConnection()->getSchema()->getTablesSortedByForeignKey() as $table)
        {
            $tableName = $table->getName();
            if (!isset($differences[$tableName])) {
                $differences[$tableName] = $this->compareTable($table);
            }
        }
        return $differences;
    }

    /**
     * Returns the IDs of all rows present in the source but not the target.
     *
     * For performance reasons this function does not compare rows that exist in both tables.
     * Note that this function is not suited for any but small tables, i.e. during installation.
     *
     * @param   \Yana\Db\Ddl\Table  $table  what should be compared
     * @return  array
     */
    public function compareTable(\Yana\Db\Ddl\Table $table): array
    {
        assert(!isset($sourceDb), 'Cannot redeclare var $sourceDb');
        $sourceDb = $this->getSourceConnection();
        assert(!isset($targetDb), 'Cannot redeclare var $targetDb');
        $targetDb = $this->getTargetConnection();
        assert(!isset($tableName), 'Cannot redeclare var $tableName');
        $tableName = $table->getName();

        /* get primary key */
        assert(!isset($primaryKey), 'Cannot redeclare var $primaryKey');
        $primaryKey = $table->getPrimaryKey();

        assert(!isset($targetKeys), 'Cannot redeclare var $targetKeys');
        $targetKeys = array();
        if ($targetDb->exists($tableName)) {
            $targetKeys = $targetDb->select("$tableName.*.$primaryKey");
            assert(is_array($targetKeys));
        }
        assert(!isset($sourceKeys), 'Cannot redeclare var $sourceKeys');
        $sourceKeys = array();
        if ($sourceDb->exists($tableName)) {
            $sourceKeys = $sourceDb->select("$tableName.*.$primaryKey");
            assert(is_array($sourceKeys));
        }

        $differences = array_diff($sourceKeys, $targetKeys);
        return $differences;
    }

}

?>