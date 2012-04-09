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
 * <<abstract>> <<decorator>> Database API.
 *
 * Base class for database connection decorators.
 *
 * @package     yana
 * @subpackage  db
 */
class Transaction extends \Yana\Core\Object
{

    /**
     * Queue of statements belonging to this transaction
     *
     * @var  array
     */
    private $_queue = array();

    /**
     * database schema
     *
     * The database schema that is used in the current session.
     *
     * Please note that you should not change this schema unless
     * you REALLY know what you are doing.
     *
     * @var  \Yana\Db\Ddl\Database
     */
    private $_schema  = null;

    /**
     * Create a new instance.
     *
     * Each database connection depends on a schema file describing the database.
     * These files are to be found in config/db/*.db.xml
     *
     * @param  \Yana\Db\Ddl\Database  $schema  schema in database definition language
     */
    public function __construct(\Yana\Db\Ddl\Database $schema)
    {
        $this->_schema = $schema;
    }

    /**
     * The database schema that is used in the current session.
     *
     * Please note that you should not change this schema unless
     * you REALLY know what you are doing.
     *
     * @return \Yana\Db\Ddl\Database
     */
    protected function _getSchema()
    {
        return $this->_schema;
    }

    /**
     * Commit current transaction and write all changes to the database.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\NotWriteableException  when the database or table is locked
     */
    public function commit(\Yana\Db\IsDriver $driver)
    {
        /* Buffer empty */
        if (count($this->_queue) == 0) {
            return true;
        }

        // start transaction
        $driver->beginTransaction();

        assert('!isset($i); /* Cannot redeclare $i */');
        for ($i = 0; $i < count($this->_queue); $i++)
        {
            /*
             * 1) get query object
             */
            /* @var $dbQuery \Yana\Db\Queries\AbstractQuery */
            assert('is_array($this->_queue[$i]);');
            assert('isset($this->_queue[$i][0]);');
            assert('isset($this->_queue[$i][1]);');
            $dbQuery = $this->_queue[$i][0];
            assert('$dbQuery instanceof \Yana\Db\Queries\AbstractQuery;');
            $triggerCollection = $this->_queue[$i][1];
            assert('$triggerCollection instanceof \Yana\Db\Helpers\Triggers\TriggerCollection;');

            // skip empty queries
            if (empty($dbQuery)) {
                continue;
            }

            /*
             * 2) query log
             */
            if (defined('YANA_ERROR_REPORTING') && YANA_ERROR_REPORTING === YANA_ERROR_LOG) {
                \Yana\Log\LogManager::getLogger()->addLog("$dbQuery", \Yana\Log\TypeEnumeration::DEBUG);
            }

            /*
             * 3 send request to database
             */
            $result = $dbQuery->sendQuery();

            /*
             * 4.1) error - query failed
             */
            if ($this->isError($result)) {
                /*
                 * 4.1.2) rollback on error
                 */
                \Yana\Log\LogManager::getLogger()->addLog("Failed: $dbQuery", \Yana\Log\TypeEnumeration::WARNING,
                    $result->getMessage());
                $result = $driver->rollback();
                /*
                 * 4.1.3) when rollback failed, create entry in logs
                 */
                if ($this->isError($result)) {
                    assert('!isset($message); // Cannot redefine var $message');
                    $message = "Unable to rollback changes. Database might contain corrupt data.";
                    \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::ERROR);
                    unset($message);
                }
                return false;
            }
            // 4.2) query was successfull

            $triggerCollection(); // fire "on after ..."-trigger(s)
            unset($triggerCollection);
        } // end foreach (query)
        unset($i);

        /*
         * 5) commit changes
         *
         * The time when the database was last modified
         * is updated, to provide protection from race
         * conditions where two transaction try to modify
         * the same data.
         */
        if (!$driver->commit()) {
            // commit failed
            \Yana\Log\LogManager::getLogger()->addLog("Failed: $dbQuery", \Yana\Log\TypeEnumeration::WARNING,
                $result->getMessage());
            return false;
        }
        $this->_queue = array();
        return true;
    }

    /**
     * Update a row or cell.
     *
     * @param   \Yana\Db\Queries\Update  $updateQuery    the address of the row that should be updated
     * @return  \Yana\Db\Transaction
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException          when either the given $key or $value is invalid
     * @throws  \Yana\Db\Queries\Exceptions\InvalidResultTypeException  when the query has an invalid column selector
     * @throws  \Yana\Db\Queries\Exceptions\ConstraintException         when a constraint check fails
     */
    public function update(\Yana\Db\Queries\Update $updateQuery)
    {
        // get properties
        $tableName = $updateQuery->getTable();
        $column = $updateQuery->getColumn();
        $value = $updateQuery->getValues(); // get values by reference

        assert('!isset($table); /* Cannot redeclare var $table */');
        $table = $this->_getSchema()->getTable($tableName);

        // updating table / column is illegal
        assert('!isset($expectedResult); /* Cannot redeclare var $expectedResult */');
        $expectedResult = $updateQuery->getExpectedResult();
        if ($expectedResult !== \Yana\Db\ResultEnumeration::ROW && $expectedResult !== \Yana\Db\ResultEnumeration::CELL) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Query is invalid. " .
                "Updating a table or column is illegal. Operation aborted.");
        }

        //before update: check constraints and triggers
        assert('!isset($constraint); // Cannot redeclare var $constraint');
        $constraint = ($column === '*') ? $value : array($column => $value);

        $constraints = new \Yana\Db\Helpers\ConstraintCollection($table->getConstraints(), $constraint);
        if ($constraints() === false) {
            $_message = "Update on table '{$tableName}' failed. Constraint check failed for statement '$updateQuery'.";
            throw new \Yana\Db\Queries\Exceptions\ConstraintException($_message, E_USER_WARNING);
        }

        $triggerContainer = new \Yana\Db\Helpers\Triggers\Container($table, $column, $value, $updateQuery->getRow());
        $trigger = new \Yana\Db\Helpers\Triggers\BeforeUpdate($triggerContainer);
        $trigger(); // fire trigger

        assert('!isset($triggerCollection); /* Cannot redeclare var $triggerCollection */');
        $triggerCollection = new \Yana\Db\Helpers\Triggers\TriggerCollection();
        $triggerCollection[] = new \Yana\Db\Helpers\Triggers\AfterUpdate($triggerContainer);

        // add SQL statement to queue
        $this->_queue[] = array($updateQuery, $triggerCollection);

        return $this;
    }

    /**
     * Insert $value at position $key.
     *
     * @param   \Yana\Db\Queries\Insert  $insertQuery   the address of the row that should be inserted
     * @return  \Yana\Db\Transaction
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException          when either $key or $value is invalid
     * @throws  \Yana\Db\Queries\Exceptions\InvalidResultTypeException  when the query has an invalid column selector
     * @throws  \Yana\Db\Queries\Exceptions\ConstraintException         when a constraint check fails
     */
    public function insert(\Yana\Db\Queries\Insert $insertQuery)
    {
        $tableName = $insertQuery->getTable();
        $value = $insertQuery->getValues();

        assert('!isset($table); /* Cannot redeclare var $table */');
        $table = $this->_getSchema()->getTable($tableName);

        // inserting updating table / column is illegal
        $expectedResult = $insertQuery->getExpectedResult();
        if ($expectedResult !== \Yana\Db\ResultEnumeration::ROW) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Query is invalid. " .
                "Can only insert a row, not a table, cell or column.");
        }

        // constraint check failed
        $constraints = new \Yana\Db\Helpers\ConstraintCollection($table->getConstraints(), $value);
        if ($constraints() === false) {
            throw new \Yana\Db\Queries\Exceptions\ConstraintException("Insert on table '{$tableName}' failed. " .
                "Constraint check failed for statement '$insertQuery'.", E_USER_WARNING);
        }
        $triggerContainer = new \Yana\Db\Helpers\Triggers\Container($table, $value, $insertQuery->getRow());
        $trigger = new \Yana\Db\Helpers\Triggers\BeforeInsert($triggerContainer);
        $trigger(); // fire trigger

        assert('!isset($triggerCollection); /* Cannot redeclare var $triggerCollection */');
        $triggerCollection = new \Yana\Db\Helpers\Triggers\TriggerCollection();
        $triggerCollection[] = new \Yana\Db\Helpers\Triggers\AfterInsert($triggerContainer);

        // untaint input
        if ($insertQuery->getExpectedResult() !== \Yana\Db\ResultEnumeration::ROW) {
            // this point should be impossible to reach
            throw new \Yana\Db\Queries\Exceptions\InvalidResultTypeException("Query is invalid. " .
                "Can only insert a row, not a table, cell or column.");
        }

        // add statement to queue
        $this->_queue[] = array($insertQuery, $triggerCollection);

        return $this;
    }

    /**
     * Remove row.
     *
     * @param   \Yana\Db\Queries\Delete  $deleteQuery   the address of the row that should be removed
     * @return  \Yana\Db\Transaction
     */
    public function remove(\Yana\Db\Queries\Delete $deleteQuery)
    {
        $tableName = $deleteQuery->getTable();

        assert('!isset($table); // Cannot redeclare var $table');
        $table = $this->_getSchema()->getTable($tableName);

        // get old row for logging an generic triggers
        assert('!isset($oldRows); /* Cannot redeclare var $oldRows */');
        $oldRows = $deleteQuery->getOldValues();

        if (empty($oldRows)) {
            return $this; // abort: there is nothing to delete
        }

        if ($deleteQuery->getLimit() === 1) {
            $oldRows = array($oldRows);
        }

        // loop through deleted rows
        assert('!isset($triggerCollection); /* Cannot redeclare var $triggerCollection */');
        $triggerCollection = new \Yana\Db\Helpers\Triggers\TriggerCollection();
        assert('!isset($oldRow); /* Cannot redeclare var $oldRow */');
        foreach ($oldRows as $oldRow)
        {
            $triggerContainer = new \Yana\Db\Helpers\Triggers\Container($table, $oldRow, $deleteQuery->getRow());
            $trigger = new \Yana\Db\Helpers\Triggers\BeforeDelete($triggerContainer);
            $trigger(); // fire trigger

            // save trigger settings for onAfterDelete
            $triggerCollection[] = new \Yana\Db\Helpers\Triggers\AfterDelete($triggerContainer);
        }
        unset($oldRow);

        // add query to queue
        $this->_queue[] = array($deleteQuery, $triggerCollection);

        return $this;
    }

    /**
     * Reset the object to default values
     */
    public function rollback()
    {
        $this->_queue = array();
    }

}

?>