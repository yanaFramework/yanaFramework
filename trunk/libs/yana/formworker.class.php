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
 * @access      public
 * @package     yana
 * @subpackage  form
 */
class FormWorker extends Object
{

    /**
     * Database connection.
     *
     * @access  private
     * @var     DbStream
     */
    private $_database;

    /**
     * Form definition.
     *
     * @access  private
     * @var     FormFacade
     */
    private $_form;

    /**
     * Query builder class.
     *
     * @access  private
     * @var     FormQueryBuilder
     */
    private $_queryBuilder;

    /**
     * Initialize instance
     *
     * @access  public
     * @param   string  $file  name of database to connect to
     */
    public function __construct(DbStream $database, FormFacade $form)
    {
        $this->_database = $database;
        $this->_form = $form;
        $this->_queryBuilder = new FormQueryBuilder($this->_database);
    }

    public function create()
    {
        $newEntry = $this->_form->getInsertValues();
        $tableName = $this->_form->getBaseForm()->getTable();

        if (empty($newEntry)) {
            throw new InvalidInputWarning('No data has been provided.');
        }

        if (!$this->_database->insert($tableName, $newEntry)) {
            throw new InvalidInputWarning('Unable to insert entry. Is database read-only?');
        }
        return $this->_database->commit();
    }

    public function read()
    {
        return true;
    }

    public function autocomplete()
    {
        return true;
    }

    public function update()
    {
        $updatedEntries = $this->_form->getUpdateValues();
        $tableName = $this->_form->getBaseForm()->getTable();

        /* no data has been provided */
        if (empty($updatedEntries)) {
            throw new InvalidInputWarning();
        }

        foreach ($updatedEntries as $id => $entry)
        {
            $id = mb_strtolower($id);

            /* before doing anything, check if entry exists */
            if (!$this->_database->exists("{$tableName}.{$id}")) {

                /* error - no such entry */
                throw new InvalidInputWarning();

            /* update the row */
            } elseif (!$this->_database->update("{$tableName}.{$id}", $entry)) {
                /* error - unable to perform update - possibly readonly */
                return false;
            }
        } /* end for */
        /* commit changes */
        return $this->_database->commit();
    }

    public function delete(array $selected_entries)
    {
        // check if user forgot to mark at least 1 row
        if (empty($selected_entries)) {
            throw new MissingInputWarning();
        }
        $tableName = $this->_form->getBaseForm()->getTable();
        // remove entry from database
        foreach ($selected_entries as $id)
        {
            $this->_database->remove("{$tableName}.{$id}");
        }
        return $this->_database->commit();
    }

}

?>