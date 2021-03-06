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
 * <<builder>> Build a queries based on a given form.
 *
 * @package     yana
 * @subpackage  form
 */
class QueryBuilder extends \Yana\Forms\AbstractQueryBuilder
{

    /**
     * Initialize instance.
     *
     * @param  \Yana\Db\IsConnection  $db  database connection used to create the querys
     */
    public function __construct(\Yana\Db\IsConnection $db)
    {
        $this->_setDatabase($db);
    }

    /**
     * Create a select query.
     *
     * This returns the query object which is bound to the form.
     * You can modify this to filter the visible results.
     *
     * @return  \Yana\Db\Queries\Select
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the selected table or one of the selected columns is not found
     */
    public function buildSelectQuery()
    {
        if (!$this->_isCached(__FUNCTION__)) {
            $query = new \Yana\Db\Queries\Select($this->getDatabase());
            $form = $this->getForm();
            if ($form instanceof \Yana\Forms\Facade && $form->getBaseForm()->getTable() > "") {
                $this->_applyFormProperties($query, $form);
                $this->_applySetupSettings($query, $form->getSetup());
                $this->_applySetupFilters($query, $form->getSetup());
            }
            $this->_setCache(__FUNCTION__, $query);
        }
        return $this->_getCache(__FUNCTION__);
    }

    /**
     * Sets the source table aso of the query based on the given form.
     *
     * @param   \Yana\Db\Queries\Select  $query  set table and so on on this
     * @param   \Yana\Forms\Facade       $form   use this form as template for query
     * @return  \Yana\Db\Queries\Select
     */
    private function _applyFormProperties(\Yana\Db\Queries\Select $query, \Yana\Forms\Facade $form)
    {
        $query->setTable($form->getBaseForm()->getTable());
        // apply filters
        if ($form->getSetup()->getSearchTerm()) {
            $this->_processSearchTerm($query, $form->getSetup()->getSearchTerm(), $form->getUpdateForm());
        } else {
            $this->_processSearchValues($query, $form->getSearchForm());
        }

        // set output columns
        assert(!isset($updateForm), 'Cannot redeclare var $updateForm');
        $updateForm = $form->getUpdateForm();
        /** @var \Yana\Forms\Setups\IsContext $updateForm */
        assert(!isset($columnNames), 'Cannot redeclare var $columnNames');
        $columnNames = $form->getUpdateForm()->getColumnNames();
        if (count($columnNames) > 0) {
            $query->setColumns($columnNames); // throws NotFoundException
            $primaryKey = $form->getTable()->getPrimaryKey();
            if (!$updateForm->hasColumnName($primaryKey)) {
                $query->addColumn($form->getTable()->getPrimaryKey());
            }
        }
        unset($updateForm, $columnNames);

        $this->_buildSelectForSubForm($query);
        /* @var $reference \Yana\Db\Ddl\Reference */
        foreach ($form->getSetup()->getForeignKeys() as $columnName => $reference)
        {
            $query->setLeftJoin($reference->getTable(), $reference->getColumn(), null, $columnName);
            $query->addColumn($reference->getTable() . '.' . $reference->getColumn());
            $query->addColumn($reference->getTable() . '.' . $reference->getLabel());
        }
    }

    /**
     * Apply settings to select query obect.
     *
     * This sets the limit, offset, and order-by clauses.
     *
     * @param   \Yana\Db\Queries\Select  $query  apply settings to this object
     * @param   \Yana\Forms\IsSetup      $setup  settings to apply
     */
    private function _applySetupSettings(\Yana\Db\Queries\Select $query, \Yana\Forms\IsSetup $setup)
    {
        $query->setLimit($setup->getEntriesPerPage());
        $query->setOffset($setup->getPage() * $setup->getEntriesPerPage());
        if ($setup->getOrderByField()) {
            $query->setOrderBy((array) $setup->getOrderByField(), (array) $setup->isDescending());
        }
    }

    /**
     * Create an autocomplete query.
     *
     * Allows you to search a specific column of the table for any values that start with a given search-term.
     * The returned query uses the aliases "VALUE" and "LABEL" for the target value-column and target label-column.
     *
     * @param   \Yana\Db\Ddl\Reference  $targetReference  defining the target table and columns
     * @param   string                  $searchTerm       find all entries that start with ...
     * @param   int                     $limit            maximum number of hits, set to 0 to get all
     * @return  \Yana\Db\Queries\Select
     */
    public function buildAutocompleteQuery(\Yana\Db\Ddl\Reference $targetReference, $searchTerm, $limit)
    {
        assert(is_string($searchTerm), 'Invalid argument $searchTerm: string expected');
        assert(is_int($limit), 'Invalid argument $limit: int expected');

        $query = new \Yana\Db\Queries\Select($this->getDatabase());
        $query->setTable($targetReference->getTable());
        $query->setLimit((int) $limit);
        $query->setOrderBy((array) $targetReference->getLabel());
        $query->addColumn($targetReference->getColumn(), 'VALUE');
        $query->addColumn($targetReference->getLabel(), 'LABEL');
        $query->setWhere(array($targetReference->getLabel(), 'LIKE' , (string) $searchTerm . '%'));
        return $query;
    }

    /**
     * This processes a global search-term submitted via the search-form.
     *
     * This function creates a new having clause and adds it to the select query.
     * The new clause will use fuzzy-search with wildcards and be appended using the "OR" operator.
     *
     * So, how do we find the table columns we need to search in?
     * And why the hell do we get the "search form" and then iterate through it?
     *
     * Well: The "search form" is a facade and a field collection.
     * It contains the form DDL object + the search "context".
     * If we iterate over it, we iterate over the fields of the form.
     *
     * So what is a "context"? A "context" object contains the parameters the form generator was called with.
     * And these parameters may include a field list.
     *
     * If the list is provided, the fields the form should contain when displayed in this context are restricted to those in the list.
     * If the list is omitted, there is no restriction; any field can be displayed in this context.
     *
     * The form object itself says which fields CAN or CANNOT be displayed on principle, INDEPENDENT of any context.
     *
     * It does this in one of two ways:
     *
     * - Either it has "all input", in which case ALL fields are allowed UNLESS they are explicitly listed as "invisible".
     * - Or it doesn't have "all input", in which case ONLY the fields listed by the form object are allowed.
     *
     * So in short: the form object provides either a blacklist, or a whitelist.
     *
     * Finally, these form fields must be related to an actual column in an actual table.
     *
     * This means a column should be searched if, and only if:
     * - It is included in the search context, or the context is empty AND
     * - The form object has "all input" and doesn't explicitly mark the field as "invisible", OR
     * - The form object doesn't have "all input" and lists the field as "visible" AND
     * - The associated table actually does have the column in question AND that column is "visible".
     *
     * Aaand guess what? The search form facade object already does all of this for us.
     * So: if we ask the search form for its field lists, we are all good :-)
     *
     * And THAT's why we DON'T ask the table, the context, or the form for the field list.
     * Understood? Great!
     *
     * @param  \Yana\Db\Queries\Select              $select      query that is to be modified
     * @param  string                               $searchTerm  the string for which to search the files
     * @param  \Yana\Forms\Fields\FieldCollectionWrapper  $columnList  use this form as template for the query
     */
    private function _processSearchTerm(\Yana\Db\Queries\Select $select, string $searchTerm, \Yana\Forms\Fields\FieldCollectionWrapper $columnList)
    {
        if (!empty($searchTerm)) {

            $clause = array();
            $lang = \Yana\Translations\Facade::getInstance();

            assert(!isset($_clause), 'Cannot redeclare var $_clause');
            assert(!isset($field), 'Cannot redeclare var $field');
            assert(!isset($column), 'Cannot redeclare var $column');

            /* @var $field \Yana\Forms\Fields\IsField */
            foreach ($columnList as $field) // process fields
            {
                $column = $field->getColumn();
                switch ($column->getType())
                {
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::REFERENCE:
                        assert(!isset($enumValues), 'Cannot redeclare var $enumValues');
                        $enumValues = $field->getForm()->getSetup()->getReferenceValues($column->getName());
                    // fall through
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::ENUM:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::SET:
                        if (!isset($enumValues)) {
                            $enumValues = $field->getColumn()->getEnumerationItems();
                        }
                        assert(!isset($enumKey), 'Cannot redeclare var $enumKey');
                        assert(!isset($enumTitle), 'Cannot redeclare var $enumTitle');
                        foreach ($enumValues as $enumKey => $enumTitle)
                        {
                            if (stripos($lang->replaceToken($enumTitle), $searchTerm) !== false) {
                                $_clause = array($field->getName(), '=', (string) $enumKey);
                                $clause = (empty($clause)) ? $_clause : array($clause, 'OR', $_clause);
                            }
                        }
                        unset($enumValues, $enumKey, $enumTitle);
                    break;
                    default:
                        if ($field->isFilterable()) {
                            $_clause = array($field->getName(), 'like', "%" . preg_replace('/\s+/s', '%', $searchTerm) . "%");
                            $clause = (empty($clause)) ? $_clause : array($clause, 'OR', $_clause);
                        }
                }
            }
            unset($_clause, $field, $column);
            $select->addWhere($clause);
        }
    }

    /**
     * This processes values submitted via the search-form.
     *
     * It creates a new where clause and adds it to the select query.
     * The new clause will be appended using the "AND" operator.
     *
     * @param  \Yana\Db\Queries\Select              $select      query that is to be modified
     * @param  \Yana\Forms\Fields\FieldCollectionWrapper  $columnList  contains user input (search values)
     */
    private function _processSearchValues(\Yana\Db\Queries\Select $select, \Yana\Forms\Fields\FieldCollectionWrapper $columnList)
    {
        if ($columnList->getContext()->getValues()) {
            $clause = $select->getWhere();
            // determine new where clause
            /* @var $field \Yana\Forms\Fields\IsField */
            foreach ($columnList as $field)
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
     * @param  \Yana\Db\Queries\Select  $select  query that is to be modified
     * @param  \Yana\Forms\IsSetup      $setup   contains filter settings that need to be applied
     */
    private function _applySetupFilters(\Yana\Db\Queries\Select $select, \Yana\Forms\IsSetup $setup)
    {
        if ($setup->hasFilter()) {
            assert(!isset($updateForm), 'Cannot redeclare var $updateForm');
            $updateForm = $this->getForm()->getUpdateForm();
            foreach ($setup->getFilters() as $columnName => $filter)
            {
                /* @var $field FormFieldFacade */
                $field = $updateForm->offsetGet($columnName);
                if ($field && $field->isSelectable() && $field->isFilterable()) {
                    $field->setFilter((string) $filter);
                    $havingClause = array($columnName, 'like', (string) $filter);
                    $select->addHaving($havingClause);
                }
            }
            unset($updateForm);
        }
    }

    /**
     * Checks if a parent form exists and modifies the query accordingly.
     *
     * @param   \Yana\Db\Queries\Select $select  base query for current form
     */
    private function _buildSelectForSubForm(\Yana\Db\Queries\Select $select)
    {
        assert(!isset($form), 'Cannot redeclare var $form');
        $form = $this->getForm();
        assert(!isset($parentForm), 'Cannot redeclare var $parentForm');
        $parentForm = $form->getParent();
        // copy foreign key from parent query
        if ($parentForm instanceof \Yana\Forms\Facade) {

            $parentResults = $parentForm->getSetup()->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE)->getRows();
            if ($parentForm->getBaseForm()->getTable() === $form->getBaseForm()->getTable()) {
                $rowId = $parentResults->key();
                if (!is_null($rowId)) {
                    $select->setRow($parentResults->key());
                }
                $form->getSetup()->setEntriesPerPage(1);
            } else {
                assert(!isset($sourceColumnName), 'Cannot redeclare var $sourceColumnName');
                assert(!isset($targetColumnName), 'Cannot redeclare var $targetColumnName');
                list($sourceColumnName, $targetColumnName) = $this->getForeignKey();
                $targetColumnName = strtoupper($targetColumnName);
                $results = $parentResults->toArray();
                if (count($results) === 1) {
                    $primaryKey = key($results);
                    $results = current($results);
                    $results[\mb_strtoupper($parentForm->getTable()->getPrimaryKey())] = $primaryKey;
                    if (isset($results[$targetColumnName])) {
                        $select->addWhere(array($sourceColumnName, '=', $results[$targetColumnName]));
                    }
                }
            }
        }
    }

    /**
     * Create a count query.
     *
     * This returns a query object bound to the form, that can be used to count the pages.
     *
     * @return  \Yana\Db\Queries\Select
     */
    public function buildCountQuery()
    {
        if (!$this->_isCached(__FUNCTION__)) {
            $query = clone $this->buildSelectQuery();
            assert($query instanceof \Yana\Db\Queries\SelectCount);
            $query->setLimit(0);
            $query->setOffset(0);
            $this->_setCache(__FUNCTION__, $query);
        }
        return $this->_getCache(__FUNCTION__);
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
     * @return  array
     * @throws  \Yana\Db\Queries\Exceptions\NotFoundException  when no foreign key is found
     */
    protected function getForeignKeys()
    {
        assert(!isset($form), 'Cannot redeclare var $form');
        $form = $this->getForm();
        assert($form instanceof \Yana\Forms\Facade, '$form instanceof \Yana\Forms\Facade');
        assert(!isset($baseForm), 'Cannot redeclare var $baseForm');
        $baseForm = $form->getBaseForm();
        assert(!isset($parentForm), 'Cannot redeclare var $parentForm');
        $parentForm = $baseForm->getParent();
        if (!$parentForm instanceof \Yana\Db\Ddl\Form) {
            return null;
        }
        assert(!isset($db), 'Cannot redeclare var $db');
        $db = $baseForm->getDatabase();

        assert(!isset($targetTable), 'Cannot redeclare var $targetTable');
        $targetTable = $parentForm->getTable();

        assert(!isset($foreignKeys), 'Cannot redeclare var $foreignKeys');
        $foreignKeys = array();

        assert(!isset($columns), 'Cannot redeclare var $columns');
        assert(!isset($keyName), 'Cannot redeclare var $keyName');
        assert(!isset($columnName), 'Cannot redeclare var $columnName');
        /* @var $foreign \Yana\Db\Ddl\ForeignKey */
        foreach ($form->getTable()->getForeignKeys() as $foreign)
        {
            if ($targetTable !== $foreign->getTargetTable()) {
                continue;
            }
            $columns = $foreign->getColumns();
            $keyName = key($columns);
            $columnName = current($columns);
            reset($columns);
            // fall back to primary key, if the target is undefined
            if (empty($columnName)) {
                $columnName = $db->getTable($targetTable)->getPrimaryKey();
            }
            $foreignKeys[] = array($keyName, $columnName);
        }
        if (empty($foreignKeys)) {
            $message = "No suitable foreign-key found in form '" . $baseForm->getName() . "'.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Db\Queries\Exceptions\NotFoundException($message, $level);
        }
        return $foreignKeys;
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
     * @return  array
     * @throws  \Yana\Db\Queries\Exceptions\NotFoundException  when no foreign key is found
     */
    protected function getForeignKey()
    {
        assert(!isset($form), 'Cannot redeclare var $form');
        $form = $this->getForm();
        assert($form instanceof \Yana\Forms\Facade);
        assert(!isset($baseForm), 'Cannot redeclare var $baseForm');
        $baseForm = $form->getBaseForm();
        assert($baseForm instanceof \Yana\Db\Ddl\Form);

        assert(!isset($keyName), 'Cannot redeclare var $keyName');
        $keyName = $baseForm->getKey();
        assert(!isset($columnName), 'Cannot redeclare var $columnName');
        $columnName = "";
        assert(!isset($columns), 'Cannot redeclare var $columns');
        foreach ($this->getForeignKeys() as $columns)
        {
            assert(is_array($columns));
            if (!empty($keyName)) {
                // Form explicitely defines a key-column, so all we need is the target
                if ($keyName === $columns[0]) {
                    $columnName = $columns[1];
                    break;
                }
            } else {
                // try to determine a matching source and target column
                $keyName = $columns[0];
                $columnName = $columns[1];
                break;
            }
        }
        if (empty($keyName) || empty($columnName)) {
            $message = "No suitable foreign-key found in form '" . $baseForm->getName() . "'.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Db\Queries\Exceptions\NotFoundException($message, $level);
        }
        return array($keyName, $columnName);
    }

}

?>