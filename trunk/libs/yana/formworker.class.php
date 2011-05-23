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

/**
 * <<worker, facade>> Implements CRUD-functions for form elements.
 *
 * C.R.U.D. = Create, Read, Update, Delete.
 * These stand for the standard operations on any database and form data.
 *
 * @access      public
 * @package     yana
 * @subpackage  form
 */
class FormWorker extends FormQueryBuilder
{

    /**
     * Initialize instance.
     *
     * @access  public
     * @param   DbStream    $db    database connection used to create the querys
     * @param   FormFacade  $form  database connection used to create the querys
     */
    public function __construct(DbStream $db, FormFacade $form)
    {
        $this->_db = $db;
        $this->setForm($form);
    }

    /**
     * Register a callback-function for a given event.
     *
     * Registers the new callback (if provided) and returns all known callbacks for the requested event.
     *
     * @access  private
     * @param   string    $event     name of event to trigger
     * @param   callable  $callback  some call-back function
     * @return  array
     */
    private function _registerCallback($event, $callback = null)
    {
        assert('is_string($event); // Invalid argument $event: string expected');
        assert('is_null($callback) || is_callable($callback);; // Invalid argument $callback: callable function expected');
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
     * @access  public
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
     * @access  public
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
     * @access  public
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
     * @access  public
     * @param   callable  $callback  some call-back function
     * @return  FormWorker 
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
     * @access  public
     * @param   callable  $callback  some call-back function
     * @return  FormWorker 
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
     * @return  FormWorker 
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
     * @access  public
     * @return  bool
     * @throws  InvalidInputWarning  when the value could not be inserted
     * @throws  MissinginputWarning  when no input data has been provided
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
                throw new MissinginputWarning('No data has been provided.');
            }

            // execute hooks
            foreach ($this->beforeCreate() as $callback)
            {
                $callback($newEntry); // may throw exception
            }

            if (!$this->_db->insert($tableName, $newEntry)) {
                throw new InvalidInputWarning('Unable to insert entry. Is database read-only or the sequence out of sync?');
            }

            // execute hooks
            foreach ($this->afterCreate() as $callback)
            {
                $callback($newEntry); // may throw exception
            }
            $result = $this->_db->commit();
        }
        return $result;
    }

    /**
     * This returns an array of foreign-key reference settings.
     *
     * Example:
     * <code>
     * array(
     *   'primaryKey1' => array(
     *     'table' => 'name of target table'
     *     'column' => 'name of target column'
     *     'label' => 'name of a column in target table that should be used as a label'
     * }
     * </code>
     *
     * @access  private
     * @return  array
     */
    private function _getReferences()
    {
        if (!isset($this->_cache[__FUNCTION__])) {
            $this->_cache[__FUNCTION__] = array();
            assert('!isset($field);');
            /* @var $field FormFieldFacade */
            foreach ($this->_form->getUpdateForm() as $field)
            {
                $column = $field->getColumn();
                if ($column->getType() !== 'reference') {
                    continue;
                }
                assert('!isset($column);');
                $reference = $column->getReferenceSettings();
                if (!isset($reference['column'])) {
                    $reference['column'] = $column->getReferenceColumn()->getName();
                }
                if (!isset($reference['label'])) {
                    $reference['label'] = $reference['column'];
                }
                if (!isset($reference['table'])) {
                    $reference['table'] = $column->getReferenceColumn()->getParent()->getName();
                }
                $this->_cache[__FUNCTION__][$field->getName()] = $reference;
            } // end foreach
        }
        return $this->_cache[__FUNCTION__];
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
     * @access  public
     * @param   string  $fieldName   name of field to look up
     * @param   string  $searchTerm  find all entries that start with ...
     * @param   int     $limit       maximum number of hits, set to 0 to get all (default = 50)
     * @return  array
     */
    public function autocomplete($fieldName, $searchTerm = "", $limit = 50)
    {
        assert('is_string($fieldName); // Invalid argument $fieldName: string expected');
        assert('is_string($searchTerm); // Invalid argument $searchTerm: string expected');
        $referenceValues = array();
        if ($this->_form) {
            $references = $this->_getReferences();
            if (isset($references[$fieldName])) {
                $reference = $references[$fieldName];
                $select = new DbSelect($this->_db);
                $select->setTable($reference['table']);
                $columns = array('LABEL' => $reference['label'], 'VALUE' => $reference['column']);
                $select->setColumns($columns);
                if ($limit > 0) {
                    $select->setLimit($limit);
                }
                $select->setOrderBy($reference['label']);
                if (!empty($searchTerm)) {
                    $select->setWhere(array($reference['label'], 'like', $searchTerm . '%'));
                }
                $values = array();
                foreach ($select->getResults() as $row)
                {
                    $values[$row['VALUE']] = $row['LABEL'];
                }
                $referenceValues = $values;
            }
        }
        return $referenceValues;
    }

    /**
     * Returns the contents of the form as CSV string.
     *
     * @access  public
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
     * @access  public
     * @return  bool
     * @throws  InvalidInputWarning  when an updated row does not exist
     * @throws  MissinginputWarning  when no input data has been provided
     */
    public function update()
    {
        $result = "";
        if ($this->_form) {
            $updatedEntries = $this->_form->getSetup()->getContext('update')->getRows()->toArray();
            $tableName = $this->_form->getBaseForm()->getTable();

            if (empty($updatedEntries)) {
                throw new MissinginputWarning('No data has been provided.');
            }

            foreach ($updatedEntries as $id => $entry)
            {
                $id = mb_strtolower($id);

                /* before doing anything, check if entry exists */
                if (!$this->_db->exists("{$tableName}.{$id}")) {
                    throw new InvalidInputWarning('Entry not found');
                }

                // execute hooks
                foreach ($this->beforeUpdate() as $callback)
                {
                    $callback($id, $entry); // may throw exception
                }

                if (!$this->_db->update("{$tableName}.{$id}", $entry)) { // update the row
                    return false; // error - unable to perform update - possibly readonly
                }

                // execute hooks
                foreach ($this->afterUpdate() as $callback)
                {
                    $callback($id, $entry); // may throw exception
                }
            } // end for
            $result = $this->_db->commit();
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
     * @access  public
     * @param   array  $selectedEntries list of primary keys, of the rows to be removed
     * @return  bool
     * @throws  MissinginputWarning  when no input data has been provided
     */
    public function delete(array $selectedEntries)
    {
        $result = "";
        if ($this->_form) {
            if (empty($selectedEntries)) {
                throw new MissinginputWarning('No row has been selected.');
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
                $this->_db->remove("{$tableName}.{$id}");

                // execute hooks
                foreach ($this->afterDelete() as $callback)
                {
                    $callback($id); // may throw exception
                }
            }
            $result = $this->_db->commit();
        }
        return $result;
    }

}

?>