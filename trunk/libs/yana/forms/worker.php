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
     * Initialize instance.
     *
     * @param   \Yana\Db\IsConnection  $db    database connection used to create the querys
     * @param   \Yana\Forms\Facade     $form  meta data describing the form
     */
    public function __construct(\Yana\Db\IsConnection $db, \Yana\Forms\Facade $form)
    {
        $this->_db = $db;
        $this->setForm($form);
    }

    /**
     * Register a callback-function for a given event.
     *
     * Registers the new callback (if provided) and returns all known callbacks for the requested event.
     *
     * @param   string    $event     name of event to trigger
     * @param   callable  $callback  some call-back function
     * @return  array
     */
    private function _registerCallback($event, $callback = null)
    {
        assert('is_string($event)', ' Invalid argument $event: string expected');
        assert('is_null($callback) || is_callable($callback);', ' Invalid argument $callback: callable function expected');
        $cachedCallbacks = array();
        if (!empty($callback)) {
            $cachedCallbacks = $this->_cache['callback'][$event][] = $callback;
        } elseif (!empty($this->_cache['callback'][$event])) {
            $cachedCallbacks = $this->_cache['callback'][$event];
        }
        return (array) $cachedCallbacks;
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
        return $this->_registerCallback(__FUNCTION__, $callback);
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
        return $this->_registerCallback(__FUNCTION__, $callback);
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
        return $this->_registerCallback(__FUNCTION__, $callback);
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
     * @return  \Yana\Forms\Worker 
     */
    public function afterUpdate($callback = null)
    {
        return $this->_registerCallback(__FUNCTION__, $callback);
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
     * @return  \Yana\Forms\Worker 
     */
    public function beforeDelete($callback = null)
    {
        return $this->_registerCallback(__FUNCTION__, $callback);
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
     * @access  public
     * @param   callable  $callback  some call-back function
     * @return  \Yana\Forms\Worker 
     */
    public function afterDelete($callback = null)
    {
        return $this->_registerCallback(__FUNCTION__, $callback);
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
        $result = false;
        if ($this->_form) {
            $newEntry = $this->_form->getSetup()->getContext('insert')->getValues();

            /**
             * We need to copy the primary key of the parent form to the foreign key column of the child form.
             * Otherwise the inserted row would get rejected for a foreign key constraint mismatch.
             */
            $parentForm = $this->_form->getParent();
            if ($parentForm && $parentForm->getBaseForm()->getTable() !== $this->_form->getBaseForm()->getTable()) {
                $results = $parentForm->getUpdateValues();
                if (count($results) === 1) {
                    $foreignKey = array_shift($this->getForeignKey());
                    $newEntry[$foreignKey] = key($results);
                }
                unset($results, $foreignKey);
            }

            $tableName = $this->_form->getBaseForm()->getTable();

            if (empty($newEntry)) {
                $message = 'No data has been provided.';
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\Forms\MissingInputException($message, $level);
            }

            // execute hooks
            foreach ($this->beforeCreate() as $callback)
            {
                $callback($newEntry); // may throw exception
            }

            try {
                $this->_db->insert($tableName, $newEntry); // may throw exception
            } catch (\Yana\Core\Exceptions\NotWriteableException $e) {
                return false; // error - unable to perform update - possibly readonly
            }

            // execute hooks
            foreach ($this->afterCreate() as $callback)
            {
                $callback($newEntry); // may throw exception
            }
            try {
                $this->_db->commit(); // may throw exception
                $result = true;
            } catch (\Exception $e) {
                $result = false;
            }
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
        assert('is_string($columnName)', ' Invalid argument $columnName: string expected');
        assert('is_string($searchTerm)', ' Invalid argument $searchTerm: string expected');
        $referenceValues = array();
        if ($this->_form) {
            $references = $this->_form->getSetup()->getForeignKeys();
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
     * @return  bool
     */
    public function export()
    {
        $csv = "";
        if ($this->_form) {
            $updatedEntries = $this->_form->getSetup()->getContext('update')->getRows()->toArray();
            // @todo implement this
        }
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
        $result = false;
        if ($this->_form) {
            $updatedEntries = $this->_form->getSetup()->getContext('update')->getRows()->toArray();
            $tableName = $this->_form->getBaseForm()->getTable();

            if (empty($updatedEntries)) {
                $message = 'No data has been provided.';
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\Forms\MissingInputException($message, $level);
            }

            foreach ($updatedEntries as $id => $entry)
            {
                $id = mb_strtolower($id);

                /* before doing anything, check if entry exists */
                if (!$this->_db->exists("{$tableName}.{$id}")) {
                $message = 'Entry not found';
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    throw new \Yana\Core\Exceptions\NotFoundException($message, $level);
                }

                // execute hooks
                foreach ($this->beforeUpdate() as $callback)
                {
                    $callback($id, $entry); // may throw exception
                }

                try {
                    $this->_db->update("{$tableName}.{$id}", $entry); // update the row
                } catch (\Yana\Core\Exceptions\NotWriteableException $e) {
                    return false; // error - unable to perform update - possibly readonly
                }

                // execute hooks
                foreach ($this->afterUpdate() as $callback)
                {
                    $callback($id, $entry); // may throw exception
                }
            } // end for
            $this->_db->commit(); // may throw exception
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
        $result = false;
        if ($this->_form) {
            if (empty($selectedEntries)) {
                $message = 'No row has been selected.';
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\Forms\MissingInputException($message, $level);
            }
            $tableName = $this->_form->getBaseForm()->getTable();
            // remove entry from database
            foreach ($selectedEntries as $id)
            {

                // execute hooks
                foreach ($this->beforeDelete() as $callback)
                {
                    $callback($id); // may throw exception
                }
                try {
                    $this->_db->remove("{$tableName}.{$id}");
                } catch (\Yana\Core\Exceptions\NotWriteableException $e) {
                    return false;
                }

                // execute hooks
                foreach ($this->afterDelete() as $callback)
                {
                    $callback($id); // may throw exception
                }
            }
            try {
                $this->_db->commit(); // may throw exception
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