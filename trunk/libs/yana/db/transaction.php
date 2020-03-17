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

namespace Yana\Db;

/**
 * Database transaction class.
 *
 * @package     yana
 * @subpackage  db
 */
class Transaction extends \Yana\Db\AbstractTransaction implements \Yana\Db\IsTransaction
{

    /**
     * Commit current transaction and write all changes to the database.
     *
     * @return  $this
     * @throws  \Yana\Db\CommitFailedException  when the commit did not succeed
     */
    public function commit(\Yana\Db\IsDriver $driver)
    {
        // start transaction
        $driver->beginTransaction();
        $queue = $this->_getQueue();

        assert(!isset($i), 'Cannot redeclare $i');
        for ($i = 0; $i < count($queue); $i++)
        {
            /*
             * 1) get query object
             */
            /* @var $dbQuery \Yana\Db\Queries\AbstractQuery */
            assert(is_array($queue[$i]));
            assert(isset($queue[$i][0]));
            assert(isset($queue[$i][1]));
            $dbQuery = $queue[$i][0];
            assert($dbQuery instanceof \Yana\Db\Queries\AbstractConnectionWrapper, '$dbQuery instanceof \Yana\Db\Queries\AbstractConnectionWrapper');
            $triggerCollection = $queue[$i][1];
            assert($triggerCollection instanceof \Yana\Db\Helpers\Triggers\TriggerCollection, '$triggerCollection instanceof \Yana\Db\Helpers\Triggers\TriggerCollection');

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
            try {

                $result = $dbQuery->sendQuery();

            } catch (\Yana\Db\DatabaseException $queryException) { // error - query failed

                \Yana\Log\LogManager::getLogger()->addLog("Failed: $dbQuery", \Yana\Log\TypeEnumeration::WARNING,
                    \get_class($queryException) . ': ' . $queryException->getMessage());

                try {

                    $driver->rollback();

                } catch (\Yana\Db\DatabaseException $rollBackException) { // when rollback failed, create log-entry

                    assert(!isset($message), 'Cannot redefine var $message');
                    $message = "Unable to rollback changes. Database might contain corrupt data. "
                        . $rollBackException->getMessage();
                    \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::ERROR);
                    unset($message, $rollBackException);

                }
                throw $queryException;

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
            \Yana\Log\LogManager::getLogger()->addLog("Failed: $dbQuery", \Yana\Log\TypeEnumeration::WARNING);
            $message = "Unable to commit changes.";
            throw new \Yana\Db\CommitFailedException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
        $this->_resetQueue();
        return $this;
    }

    /**
     * Update a row or cell.
     *
     * @param   \Yana\Db\Queries\Update  $updateQuery    the address of the row that should be updated
     * @return  $this
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

        assert(!isset($table), 'Cannot redeclare var $table');
        $table = $this->_getSchema()->getTable($tableName);

        // updating table / column is illegal
        assert(!isset($expectedResult), 'Cannot redeclare var $expectedResult');
        $expectedResult = $updateQuery->getExpectedResult();
        if ($expectedResult !== \Yana\Db\ResultEnumeration::ROW && $expectedResult !== \Yana\Db\ResultEnumeration::CELL) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Query is invalid. " .
                "Updating a table or column is illegal. Operation aborted.");
        }

        //before update: check constraints and triggers
        assert(!isset($constraint), 'Cannot redeclare var $constraint');
        $constraint = ($column === '*') ? $value : array($column => $value);
        assert(is_array($constraint), 'Array expected for values to update');

        assert(!isset($constraints), 'Cannot redeclare var $constraints');
        $constraints = new \Yana\Db\Helpers\ConstraintCollection($table->getConstraints(), $constraint);
        if ($constraints() === false) {
            $_message = "Update on table '{$tableName}' failed. Constraint check failed for statement '$updateQuery'.";
            throw new \Yana\Db\Queries\Exceptions\ConstraintException($_message, E_USER_WARNING);
        }
        unset($constraints);

        $triggerContainer = new \Yana\Db\Helpers\Triggers\Container($table, $updateQuery);
        $trigger = new \Yana\Db\Helpers\Triggers\BeforeUpdate($triggerContainer);
        $trigger(); // fire trigger

        assert(!isset($triggerCollection), 'Cannot redeclare var $triggerCollection');
        $triggerCollection = new \Yana\Db\Helpers\Triggers\TriggerCollection();
        $triggerCollection[] = new \Yana\Db\Helpers\Triggers\AfterUpdate($triggerContainer);

        // add SQL statement to queue
        $this->_addToQueue($updateQuery, $triggerCollection);

        return $this;
    }

    /**
     * Insert $value at position $key.
     *
     * @param   \Yana\Db\Queries\Insert  $insertQuery   the address of the row that should be inserted
     * @return  $this
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException          when either $key or $value is invalid
     * @throws  \Yana\Db\Queries\Exceptions\InvalidResultTypeException  when the query has an invalid column selector
     * @throws  \Yana\Db\Queries\Exceptions\ConstraintException         when a constraint check fails
     */
    public function insert(\Yana\Db\Queries\Insert $insertQuery)
    {
        $tableName = $insertQuery->getTable();
        $value = $insertQuery->getValues();

        assert(!isset($table), 'Cannot redeclare var $table');
        $table = $this->_getSchema()->getTable($tableName);

        // inserting or updating table or column is illegal
        $expectedResult = $insertQuery->getExpectedResult();
        if ($expectedResult !== \Yana\Db\ResultEnumeration::ROW) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Query is invalid. " .
                "Can only insert a row, not a table, cell or column.");
        }

        // constraint check failed
        assert(!isset($constraints), 'Cannot redeclare var $constraints');
        $constraints = new \Yana\Db\Helpers\ConstraintCollection($table->getConstraints(), $value);
        if ($constraints() === false) {
            throw new \Yana\Db\Queries\Exceptions\ConstraintException("Insert on table '{$tableName}' failed. " .
                "Constraint check failed for statement '$insertQuery'.", E_USER_WARNING);
        }
        unset($constraints);
        $triggerContainer = new \Yana\Db\Helpers\Triggers\Container($table, $insertQuery);
        $trigger = new \Yana\Db\Helpers\Triggers\BeforeInsert($triggerContainer);
        $trigger(); // fire trigger

        assert(!isset($triggerCollection), 'Cannot redeclare var $triggerCollection');
        $triggerCollection = new \Yana\Db\Helpers\Triggers\TriggerCollection();
        $triggerCollection[] = new \Yana\Db\Helpers\Triggers\AfterInsert($triggerContainer);

        // untaint input
        if ($insertQuery->getExpectedResult() !== \Yana\Db\ResultEnumeration::ROW) {
            // this point should be impossible to reach
            throw new \Yana\Db\Queries\Exceptions\InvalidResultTypeException("Query is invalid. " .
                "Can only insert a row, not a table, cell or column.");
        }

        // add statement to queue
        $this->_addToQueue($insertQuery, $triggerCollection);

        return $this;
    }

    /**
     * Remove row.
     *
     * @param   \Yana\Db\Queries\Delete  $deleteQuery   the address of the row that should be removed
     * @return  $this
     */
    public function remove(\Yana\Db\Queries\Delete $deleteQuery)
    {
        $tableName = $deleteQuery->getTable();

        assert(!isset($table), 'Cannot redeclare var $table');
        $table = $this->_getSchema()->getTable($tableName);

        // loop through deleted rows
        $triggerContainer = new \Yana\Db\Helpers\Triggers\Container($table, $deleteQuery);
        $trigger = new \Yana\Db\Helpers\Triggers\BeforeDelete($triggerContainer);
        $trigger(); // fire trigger

        // save trigger settings for onAfterDelete
        assert(!isset($triggerCollection), 'Cannot redeclare var $triggerCollection');
        $triggerCollection = new \Yana\Db\Helpers\Triggers\TriggerCollection();
        $triggerCollection[] = new \Yana\Db\Helpers\Triggers\AfterDelete($triggerContainer);

        // add query to queue
        $this->_addToQueue($deleteQuery, $triggerCollection);

        return $this;
    }

    /**
     * Add a SQL string to execute.
     *
     * Note that the string is not checked for validity.
     *
     * @param \Yana\Db\Queries\Sql $statement  one single SQL statement
     * @return  $this
     */
    public function sql(\Yana\Db\Queries\Sql $statement)
    {
        $this->_addToQueue($statement, new \Yana\Db\Helpers\Triggers\TriggerCollection());
        return $this;
    }

    /**
     * Check if the transaction queue is empty.
     *
     * @return  bool
     */
    public function isEmpty()
    {
        return 0 === count($this->_getQueue());
    }

    /**
     * Reset the object to default values.
     *
     * @return  $this
     */
    public function rollback()
    {
        $this->_resetQueue();
        return $this;
    }

}

?>