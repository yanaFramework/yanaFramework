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
 * <<builder>> Build a queries based on a given form.
 *
 * @access      public
 * @package     yana
 * @subpackage  form
 */
class FormQueryBuilder extends Object
{

    /**
     * Database connection used to create the querys.
     *
     * @access  private
     * @var     DbStream
     */
    private $_db = null;

    /**
     * Definition of form.
     *
     * @access  private
     * @var     FormFacade
     */
    private $_form = null;

    /**
     * Object cache.
     *
     * @access  private
     * @var     array
     */
    private $_cache = array();

    /**
     * Initialize instance.
     *
     * @access  public
     * @param   DbStream  $db  database connection used to create the querys
     */
    public function __construct(DbStream $db)
    {
        $this->_db = $db;
    }

    /**
     * Set form object.
     *
     * @access  public
     * @param   FormFacade  $form  configuring the contents of the form
     * @return  FormQueryBuilder
     */
    public function setForm(FormFacade $form)
    {
        $this->_form = $form;
        $this->_cache = array();
        return $this;
    }

    /**
     * Create a select query.
     *
     * This returns the query object which is bound to the form.
     * You can modify this to filter the visible results.
     *
     * @access  public
     * @return  DbSelect
     * @throws  NotFoundException  if the selected table or one of the selected columns is not found
     */
    public function buildSelectQuery()
    {
        if (!isset($this->_cache[__FUNCTION__])) {
            $query = new DbSelect($this->_db);
            if ($this->_form) {
                $setup = $this->_form->getSetup();
                $query->setTable($this->_form->getBaseForm()->getTable());
                $query->setLimit($setup->getEntriesPerPage());
                $query->setOffset($setup->getPage() * $setup->getEntriesPerPage());
                if ($setup->getOrderByField()) {
                    $query->setOrderBy((array) $setup->getOrderByField(), (array) $setup->isDescending());
                }
                // apply filters
                if ($setup->getSearchTerm()) {
                    $this->_processSearchTerm($query);
                } else {
                    $this->_processSearchValues($query);
                }
                $this->_processFilters($query);
                // set output columns
                if ($setup->getContext('update')->getColumnNames()) {
                    $query->setColumns($setup->getContext('update')->getColumnNames()); // throws NotFoundException
                    $query->addColumn($this->_form->getTable()->getPrimaryKey());
                }
                $query = $this->_buildSelectForSubForm($query);
            }
            $this->_cache[__FUNCTION__] = $query;
        }
        return $this->_cache[__FUNCTION__];
    }

    /**
     * This processes a global search-term submitted via the search-form.
     *
     * It creates a new having clause and adds it to the select query.
     * The new clause will use fuzzy-search with wildcards and be appended using the "OR" operator.
     *
     * @access  protected
     * @param   DbSelect  $select  query that is to be modified
     */
    protected function _processSearchTerm(DbSelect $select)
    {
        $setup = $this->_form->getSetup();
        $searchTerm = $setup->getSearchTerm();
        if (!empty($searchTerm)) {
            // process fields
            foreach ($this->_form->getUpdateForm() as $field)
            {
                /* @var $field FormFieldFacade */
                if ($field->isSelectable() && $field->isVisible() && $field->isFilterable()) {
                    $havingClause = array($field->getName(), 'like', $searchTerm);
                    $select->addHaving($havingClause);
                }
            }
        }
    }

    /**
     * This processes values submitted via the search-form.
     *
     * It creates a new where clause and adds it to the select query.
     * The new clause will be appended using the "AND" operator.
     *
     * @access  protected
     * @param   DbSelect  $select  query that is to be modified
     */
    protected function _processSearchValues(DbSelect $select)
    {
        if ($this->_form->getSearchValues()) {
            $clause = $select->getWhere();
            // determine new where clause
            /* @var $field FormFieldFacade */
            foreach ($this->_form->getSearchForm() as $field)
            {
                $test = $field->getValueAsWhereClause();
                if (is_null($test)) {
                    continue; // field is empty
                }
                if (!empty($clause)) {
                    $clause = array($clause, 'AND', $test);
                } else {
                    $clause = $test;
                }
            }
            unset($field, $test);
            $select->setWhere($clause); // apply created where clause
        }
    }

    /**
     * This processes filters submitted via the update-form.
     *
     * It creates a new having clause and adds it to the select query.
     * The new clause will be appended using the "AND" operator.
     *
     * @access  protected
     * @param   DbSelect  $select  query that is to be modified
     */
    protected function _processFilters(DbSelect $select)
    {
        $setup = $this->_form->getSetup();
        if ($setup->hasFilter()) {
            assert('!isset($updateForm); // Cannot redeclare var $updateForm');
            $updateForm = $this->_form->getUpdateForm();
            foreach ($setup->getFilters() as $columnName => $filter)
            {
                /* @var $field FormFieldFacade */
                $field = $updateForm->offsetGet($columnName);
                if ($field && $field->isSelectable() && $field->getField()->isVisible() && $field->isFilterable()) {
                    $havingClause = array($columnName, 'like', $filter);
                    $select->addHaving($havingClause);
                }
            }
            unset($updateForm);
        }
    }

    /**
     * Checks if a parent form exists and modifies the query accordingly.
     *
     * @access  private
     * @param   DbSelect  $select  base query for current form
     * @return  DbSelect
     */
    private function _buildSelectForSubForm(DbSelect $select)
    {
        $parentForm = $this->_form->getParent();
        // copy foreign key from parent query
        if ($parentForm instanceof FormFacade) {

            $parentResults = $parentForm->getSetup()->getContext('update')->getRows();
            if ($parentForm->getBaseForm()->getTable() === $this->_form->getBaseForm()->getTable()) {
                $select->setRow($parentResults->key());
                $this->_form->getSetup()->setEntriesPerPage(1);
            } else {
                $source = $target = "";
                list($source, $target) = $this->getForeignKey($select);
                $target = strtoupper($target);
                $results = $parentResults->toArray();
                if (count($results) === 1) {
                    $results = current($results);
                    if (isset($results[$target])) {
                        $where = $select->getWhere();
                        $foreignKeyClause = array($source, '=', $results[$target]);
                        if (empty($where)) {
                            $where = $foreignKeyClause;
                        } else {
                            $where = array($where, 'AND', $foreignKeyClause);
                        }
                        $select->setWhere($where);
                    }
                }
            }
        }
        return $select;
    }

    /**
     * Create a count query.
     *
     * This returns a query object bound to the form, that can be used to count the pages.
     *
     * @access  protected
     * @return  DbSelectCount
     */
    public function buildCountQuery()
    {
        if (!isset($this->_cache[__FUNCTION__])) {
            $query = clone $this->buildSelectQuery();
            assert('$query instanceof DbSelectCount;');
            $query->setLimit(0);
            $query->setOffset(0);
            $this->_cache[__FUNCTION__] = $query;
        }
        return $this->_cache[__FUNCTION__];
    }

    /**
     * Get the foreign key definition for subforms.
     *
     * If the form is associated with the parent form via a foreign key,
     * this function will return it. If there is none, it will return NULL instead.
     *
     * If no key is set this function will try to resolve it.
     *
     * The return value is an array of the source-column in the table of the subform and
     * the target-column in the table of the base-form.
     *
     * @access  protected
     * @param   DbSelect  $select  base query for current form
     * @return  array
     * @throws  DBWarning  when no foreign key is found
     */
    protected function getForeignKey(DbSelect $select)
    {
        assert('$this->_form instanceof FormFacade;');
        $form = $this->_form->getBaseForm();
        $parentForm = $form->getParent();
        if (!$parentForm instanceof DDLForm) {
            return null;
        }
        $results = $select->getResults();
        $db = $form->getDatabase();

        $targetTable = $parentForm->getTable();
        $sourceTable = $this->_form->getTable();
        $keyName = $form->getKey();
        $columnName = "";
        /* @var $foreign DDLForeignKey */
        foreach ($sourceTable->getForeignKeys() as $foreign)
        {
            if ($targetTable !== $foreign->getTargetTable()) {
                continue;
            }
            $columns = $foreign->getColumns();
            if (!empty($keyName)) {
                // Form explicitely defines a key-column, so all we need is the target
                if (!isset($columns[$keyName])) {
                    continue;
                } elseif (!empty($columns[$keyName])) {
                    $columnName = $columns[$keyName];
                }
            } else {
                // try to determine a matching source and target column
                $keyName = key($columns);
                $columnName = current($columns);
                reset($columns);
            }
            // fall back to primary key, if the target is undefined
            if (empty($columnName)) {
                $columnName = $db->getTable($targetTable)->getPrimaryKey();
            }
            break;
        }
        if (empty($keyName) || empty($columnName)) {
            $message = "No suitable foreign-key found in form '" . $form->getName() . "'.";
            throw new DbWarning($message, E_USER_ERROR);
        }
        return array($keyName, $columnName);
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
    public function buildReferenceValues($fieldName, $searchTerm = "", $limit = 50)
    {
        assert('is_string($fieldName); // Invalid argument $fieldName: string expected');
        assert('is_string($searchTerm); // Invalid argument $searchTerm: string expected');
        $referenceValues = array();
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
        return $referenceValues;
    }

}

?>