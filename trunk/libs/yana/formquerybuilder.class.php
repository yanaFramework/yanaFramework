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
     * @access  protected
     * @var     DbStream
     */
    protected $_db = null;

    /**
     * Definition of form.
     *
     * @access  protected
     * @var     FormFacade
     */
    protected $_form = null;

    /**
     * Object cache.
     *
     * @access  protected
     * @var     array
     */
    protected $_cache = array();

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
                    $havingClause = array($field->getName(), 'like', "%$searchTerm%");
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
        if ($this->_form->getSetup()->getContext('search')->getValues()) {
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
                if ($field && $field->isSelectable() && $field->isFilterable()) {
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
                $sourceColumnName = $targetColumnName = "";
                list($sourceColumnName, $targetColumnName) = $this->getForeignKey();
                $targetColumnName = strtoupper($targetColumnName);
                $results = $parentResults->toArray();
                if (count($results) === 1) {
                    $results = current($results);
                    if (isset($results[$targetColumnName])) {
                        $where = $select->getWhere();
                        $foreignKeyClause = array($sourceColumnName, '=', $results[$targetColumnName]);
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
     * @return  array
     * @throws  DBWarning  when no foreign key is found
     */
    protected function getForeignKey()
    {
        assert('$this->_form instanceof FormFacade;');
        $form = $this->_form->getBaseForm();
        $parentForm = $form->getParent();
        if (!$parentForm instanceof DDLForm) {
            return null;
        }
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

}

?>