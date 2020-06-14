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

namespace Yana\Forms;

/**
 * <<worker, facade>> Implements CRUD-functions for form elements.
 *
 * C.R.U.D. = Create, Read, Update, Delete.
 * These stand for the standard operations on any database and form data.
 *
 * @package     yana
 * @subpackage  form
 */
class Worker extends \Yana\Forms\QueryBuilder
{

    /**
     * @var \Yana\Forms\IsCallbackSet
     */
    private static $_defaultCallbacks = null;

    /**
     * @var \Yana\Forms\IsCallbackSet
     */
    private $_callbacks = null;

    /**
     * Initialize instance.
     *
     * @param   \Yana\Db\IsConnection  $db    database connection used to create the querys
     * @param   \Yana\Forms\Facade     $form  meta data describing the form
     */
    public function __construct(\Yana\Db\IsConnection $db, \Yana\Forms\Facade $form)
    {
        $this->_setDatabase($db);
        $this->setForm($form);
    }

    /**
     * Returns callback collection.
     *
     * @return  \Yana\Forms\IsCallbackSet
     */
    public static function getDefaultCallbacks(): \Yana\Forms\IsCallbackSet
    {
        if (!isset(self::$_defaultCallbacks)) {
            self::$_defaultCallbacks = new \Yana\Forms\CallbackSet();
        }
        return self::$_defaultCallbacks;
    }

    /**
     * Returns callback collection.
     *
     * @return  \Yana\Forms\IsCallbackSet
     */
    protected function _getCallbacks(): \Yana\Forms\IsCallbackSet
    {
        if (!isset($this->_callbacks)) {
            $this->_callbacks = new \Yana\Forms\CallbackSet();
        }
        return $this->_callbacks;
    }

    /**
     * <<hook>> Register call-back function to run before creating a row.
     *
     * Returns a list of all known callbacks for this event.
     *
     * Example:
     * <code>
     * $worker->beforeCreate(function (array &$newRow) {...});
     * </code>
     * If the function throws an exception,
     * the execution will be terminated and the changes are not written to the database.
     *
     * @param   callable  $callback  some call-back function
     * @return  array
     */
    public function beforeCreate($callback = null)
    {
        return $this->_getCallbacks()->addBeforeCreate($callback)->getBeforeCreate()->toArray();
    }

    /**
     * <<hook>> Register call-back function to run after creating a row.
     *
     * Returns a list of all known callbacks for this event.
     *
     * Example:
     * <code>
     * $worker->afterCreate(function (array $newRow) {...});
     * </code>
     * If the function throws an exception,
     * the execution will be terminated and the changes are not written to the database.
     *
     * @param   callable  $callback  some call-back function
     * @return  array
     */
    public function afterCreate($callback = null)
    {
        return $this->_getCallbacks()->addAfterCreate($callback)->getAfterCreate()->toArray();
    }

    /**
     * <<hook>> Register call-back function to run before updating a row.
     *
     * Returns a list of all known callbacks for this event.
     *
     * Example:
     * <code>
     * $worker->beforeUpdate(function ($id, array &$newRow) {...});
     * </code>
     * If the function throws an exception,
     * the execution will be terminated and the changes are not written to the database.
     *
     * @param   callable  $callback  some call-back function
     * @return  array
     */
    public function beforeUpdate($callback = null)
    {
        return $this->_getCallbacks()->addBeforeUpdate($callback)->getBeforeUpdate()->toArray();
    }

    /**
     * <<hook>> Register call-back function to run after updating a row.
     *
     * Returns a list of all known callbacks for this event.
     *
     * Example:
     * <code>
     * $worker->afterUpdate(function ($id, array $newRow) {...});
     * </code>
     * If the function throws an exception,
     * the execution will be terminated and the changes are not written to the database.
     *
     * @param   callable  $callback  some call-back function
     * @return  array
     */
    public function afterUpdate($callback = null)
    {
        return $this->_getCallbacks()->addBeforeCreate($callback)->getBeforeCreate()->toArray();
    }

    /**
     * <<hook>> Register call-back function to run before deleting a row.
     *
     * Returns a list of all known callbacks for this event.
     *
     * Example:
     * <code>
     * $worker->beforeDelete(function ($id) {...});
     * </code>
     * If the function throws an exception,
     * the execution will be terminated and the changes are not written to the database.
     *
     * @param   callable  $callback  some call-back function
     * @return  array
     */
    public function beforeDelete($callback = null)
    {
        return $this->_getCallbacks()->addBeforeDelete($callback)->getBeforeDelete()->toArray();
    }

    /**
     * <<hook>> Register call-back function to run after deleting a row.
     *
     * Returns a list of all known callbacks for this event.
     *
     * Example:
     * <code>
     * $worker->afterDelete(function ($id) {...});
     * </code>
     * If the function throws an exception,
     * the execution will be terminated and the changes are not written to the database.
     *
     * @param   callable  $callback  some call-back function
     * @return  array
     */
    public function afterDelete($callback = null)
    {
        return $this->_getCallbacks()->addAfterDelete($callback)->getAfterDelete()->toArray();
    }

    /**
     * Create a new row in the database, using the provided user input.
     *
     * This function determines if the given form has a new row and if so it
     * will try to insert it into the underlying table.
     *
     * This may fail either because there is no new row, or the database operation causes an error.
     *
     * The function will return bool(true) on success and bool(false) on error.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\Forms\MissingInputException  when no input data has been provided
     */
    public function create()
    {
        assert(!isset($result), 'Cannot redeclare var $result');
        $result = false;
        assert(!isset($form), 'Cannot redeclare var $form');
        $form = $this->getForm();
        if ($form) {
            assert(!isset($newEntry), 'Cannot redeclare var $newEntry');
            $newEntry = $form->getSetup()->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT)->getValues();
            assert(!isset($tableName), 'Cannot redeclare var $tableName');
            $tableName = $form->getBaseForm()->getTable();
            if ($tableName == "") {
                $message = "No base table defined for the given form.";
                throw new \Yana\Core\Exceptions\Forms\MissingInputException($message, \Yana\Log\TypeEnumeration::WARNING);
            }
            assert(!isset($database), 'Cannot redeclare var $database');
            $database = $this->getDatabase();
            assert(!isset($baseTable), 'Cannot redeclare var $baseTable');
            $baseTable = $database->getTable($tableName);

            /**
             * We need to copy the primary key of the parent form to the foreign key column of the child form.
             * Otherwise the inserted row would get rejected for a foreign key constraint mismatch.
             */
            $parentForm = $form->getParent();
            if ($parentForm && $parentForm->getBaseForm()->getTable() !== $tableName) {
                $results = $parentForm->getUpdateValues();
                if (count($results) === 1) {
                    foreach ($this->getForeignKeys() as $foreignKeyArray)
                    {
                        // $foreignKeyArray is an array, where the first entry is the source column in the sub-form
                        $foreignKey = \mb_strtoupper(\array_shift($foreignKeyArray));
                        if ((!isset($newEntry[$foreignKey]) || $newEntry[$foreignKey] === "")) {
                            if ($baseTable->getColumn($foreignKey)->hasDefault()) {
                                unset($newEntry[$foreignKey]);
                            } else {
                                $newEntry[$foreignKey] = key($results);
                            }
                        }
                    }
                }
                unset($results, $foreignKey, $foreignKeyArray);
            }

            $tableName = $form->getBaseForm()->getTable();

            if (empty($newEntry) || !is_string($tableName)) {
                $message = 'No data has been provided.';
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\Forms\MissingInputException($message, $level);
            }

            // execute hooks
            $beforeHooks = array_merge(
                self::getDefaultCallbacks()->getBeforeCreate()->toArray(), $this->_getCallbacks()->getBeforeCreate()->toArray()
            );
            foreach ($beforeHooks as $callback)
            {
                $callback($newEntry); // may throw exception
            }

            try {
                $database->insert($tableName, $newEntry); // may throw exception
            } catch (\Exception $e) {
                $database->rollback();
                return false; // error - unable to perform update - possibly readonly
            }

            // execute hooks
            $afterHooks = array_merge(
                self::getDefaultCallbacks()->getAfterCreate()->toArray(), $this->_getCallbacks()->getAfterCreate()->toArray()
            );
            foreach ($afterHooks as $callback)
            {
                $callback($newEntry); // may throw exception
            }
            // @codeCoverageIgnoreStart
            try {
                $database->commit(); // may throw exception
                $result = true;
            } catch (\Exception $e) {
                $database->rollback();
                $result = false;
            }
            // @codeCoverageIgnoreEnd
        }
        return $result;
    }

    /**
     * Look up list of reference values.
     *
     * This function returns an array, where the keys are the values of a unique key in the
     * target table and the values are the labels for those keys.
     *
     * Use this function for AJAX auto-completion in reference column.
     *
     * The search term allows to find all rows whose labels start with a given text.
     * You may use the wildcards '%' and '_'.
     *
     * Note: you may want to introduce an index on the label-column of your database.
     *
     * If the field does not refer to a column of type "reference", then an empty array will be returned.
     *
     * @param   string  $columnName  name of column to look up
     * @param   string  $searchTerm  find all entries that start with ...
     * @param   int     $limit       maximum number of hits, set to 0 to get all (default = 50)
     * @return  array
     */
    public function autocomplete($columnName, $searchTerm = "", $limit = 50)
    {
        assert(is_string($columnName), 'Invalid argument $columnName: string expected');
        assert(is_string($searchTerm), 'Invalid argument $searchTerm: string expected');

        assert(!isset($referenceValues), 'Cannot redeclare var $referenceValues');
        $referenceValues = array();
        assert(!isset($form), 'Cannot redeclare var $form');
        $form = $this->getForm();
        if ($form) {
            $references = $form->getSetup()->getForeignKeys();
            if (isset($references[$columnName])) {
                $query = $this->buildAutocompleteQuery($references[$columnName], $searchTerm, $limit);
                $referenceValues = array();
                foreach ($query->getResults() as $row)
                {
                    $referenceValues[$row['VALUE']] = $row['LABEL'];
                }
            }
        }
        return $referenceValues;
    }

    /**
     * Returns the contents of the form as CSV string.
     *
     * @param   int   $col        column seperator: 1 = ";", 2 = ",", 3 = "\t"
     * @param   int   $row        row seperator: 1 = "\n", 2 = ";"
     * @param   bool  $hasHeader  add column names as first line (yes/no)
     * @param   int   $text       text seperator: 1 = '"', 2 = "'", 3 = none
     * @return  string
     * @throws  \Yana\Core\Exceptions\InvalidValueException  if the database query is incomplete or invalid
     */
    public function export(int $col = 1, int $row = 1, bool $hasHeader = true, int $text = 1): string
    {
        switch ($col)
        {
            case 3:
                $columnDelimiter = "\t";
            break;
            case 2:
                $columnDelimiter = ",";
            break;
            case 1:
            default:
                $columnDelimiter = ";";
            break;
        }
        switch ($row)
        {
            case 2:
                $rowDelimiter = ";";
            break;
            case 1:
            default:
                $rowDelimiter = "\n";
            break;
        }
        switch ($text)
        {
            case 3:
                $textDelimiter = "";
            break;
            case 2:
                $textDelimiter = "'";
            break;
            case 1:
            default:
                $textDelimiter = '"';
            break;
        }

        assert(!isset($select), 'Cannot redeclare var $select');
        $select = $this->buildSelectQuery()->setLimit(0)->setOffset(0);
        assert(!isset($csv), 'Cannot redeclare var $csv');
        $csv = $select->toCSV($columnDelimiter, $rowDelimiter, $hasHeader, $textDelimiter);
        return $csv;
    }

    /**
     * Update a set of existing rows in the database, using the provided user input.
     *
     * Only existing rows will be updated. All updates are done in a transaction (if the database supports it).
     * The update may fail either because there is nothing to update, or the database operation causes an error.
     * The function will return bool(true) on success and bool(false) on error.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\NotFoundException            when an updated row does not exist
     * @throws  \Yana\Core\Exceptions\Forms\MissingInputException  when no input data has been provided
     */
    public function update()
    {
        assert(!isset($result), 'Cannot redeclare var $result');
        $result = false;
        assert(!isset($form), 'Cannot redeclare var $form');
        $form = $this->getForm();
        if ($form) {
            $updatedEntries = $form->getUpdateValues();
            $tableName = $form->getBaseForm()->getTable();

            if (empty($updatedEntries)) {
                $message = 'No data has been provided.';
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\Forms\MissingInputException($message, $level);
            }
            assert(!isset($database), 'Cannot redeclare var $database');
            $database = $this->getDatabase();
            $beforeHooks = array_merge(
                self::getDefaultCallbacks()->getBeforeUpdate()->toArray(), $this->_getCallbacks()->getBeforeUpdate()->toArray()
            );
            $afterHooks = array_merge(
                self::getDefaultCallbacks()->getAfterUpdate()->toArray(), $this->_getCallbacks()->getAfterUpdate()->toArray()
            );

            foreach ($updatedEntries as $id => $entry)
            {
                $id = mb_strtolower($id);

                /* before doing anything, check if entry exists */
                if (!$database->exists("{$tableName}.{$id}")) {
                    $message = 'Entry not found';
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    throw new \Yana\Core\Exceptions\NotFoundException($message, $level);
                }

                // execute hooks
                foreach ($beforeHooks as $callback)
                {
                    $callback($id, $entry); // may throw exception
                }

                try {
                    $query = new \Yana\Db\Queries\Update($database);
                    $query->setTable($tableName)->setRow($id)->setValues($entry)->sendQuery(); // update the row
                } catch (\Yana\Core\Exceptions\NotWriteableException $e) {
                    return false; // error - unable to perform update - possibly readonly
                }

                // execute hooks
                foreach ($afterHooks as $callback)
                {
                    $callback($id, $entry); // may throw exception
                }
            } // end for
            $database->commit(); // may throw exception
            $result = true;
        }
        return $result;
    }

    /**
     * Delete a set of rows in the database, using the provided user input.
     *
     * If a selected row does not exist (anymore), it is ignored.
     * The operation uses a transaction (if the database supports it).
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * @param   array  $selectedEntries list of primary keys, of the rows to be removed
     * @return  bool
     * @throws  \Yana\Core\Exceptions\Forms\MissingInputException  when no input data has been provided
     */
    public function delete(array $selectedEntries)
    {
        assert(!isset($result), 'Cannot redeclare var $result');
        $result = false;
        assert(!isset($form), 'Cannot redeclare var $form');
        $form = $this->getForm();
        if ($form) {
            if (empty($selectedEntries)) {
                $message = 'No row has been selected.';
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\Forms\MissingInputException($message, $level);
            }
            assert(!isset($database), 'Cannot redeclare var $database');
            $database = $this->getDatabase();
            $tableName = $form->getBaseForm()->getTable();

            $beforeHooks = array_merge(
                self::getDefaultCallbacks()->getBeforeDelete()->toArray(), $this->_getCallbacks()->getBeforeDelete()->toArray()
            );
            $afterHooks = array_merge(
                self::getDefaultCallbacks()->getAfterDelete()->toArray(), $this->_getCallbacks()->getAfterDelete()->toArray()
            );
            // remove entry from database
            foreach ($selectedEntries as $id)
            {

                // execute hooks
                foreach ($beforeHooks as $callback)
                {
                    $callback($id); // may throw exception
                }
                try {
                    $query = new \Yana\Db\Queries\Delete($database);
                    $query->setTable($tableName)->setRow($id)->sendQuery(); // update the row
                } catch (\Yana\Core\Exceptions\NotWriteableException $e) {
                    return false;
                }

                // execute hooks
                foreach ($afterHooks as $callback)
                {
                    $callback($id); // may throw exception
                }
            }
            try {
                $database->commit(); // may throw exception
                $result = true;
            } catch (\Exception $e) {
                unset($e);
                $result = false;
            }
        }
        return $result;
    }

}

?>