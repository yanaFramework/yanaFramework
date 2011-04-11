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
 * <<builder>> Build a form using a form object and settings.
 *
 * @access      public
 * @package     yana
 * @subpackage  form
 */
class FormFacadeBuilder extends FormFacadeAbstract
{

    /**
     * Builder product.
     *
     * @access  protected
     * @var     FormFacade
     */
    protected $object = null;

    /**
     * database schema
     *
     * @access  private
     * @var     DDLDatabase
     */
    private $_database = null;

    /**
     * last page (for multi-page layout)
     *
     * @access  private
     * @var     int
     */
    private $_lastPage = null;

    /**
     * DDL definition object of selected table
     *
     * @access  private
     * @var     DDLTable
     */
    private $_table = null;

    /**
     * iterator for {@see DDLDefaultField} elements
     *
     * @access  private
     * @var     DDLDefaultReportIterator
     */
    private $_reportIterator = null;

    /**
     * iterator for {@see DDLDefaultField} elements
     *
     * @access  private
     * @var     DDLDefaultInsertIterator
     */
    private $_insertIterator = null;

    /**
     * iterator for {@see DDLDefaultField} elements
     *
     * @access  private
     * @var     DDLDefaultUpdateIterator
     */
    private $_iterator = null;

    /**
     * iterator for {@see DDLDefaultField} elements
     *
     * @access  private
     * @var     DDLDefaultSearchIterator
     */
    private $_searchIterator = null;

    /**
     * iterator for {@see DDLDefaultField} elements
     *
     * @access  private
     * @var     DDLDefaultReadIterator
     */
    private $_readIterator = null;

    /**
     * cache list of entries
     *
     * @access  private
     * @var     string
     */
    private $_listOfEntries = null;

    /**
     * Initialize instance
     *
     * @access  public
     * @param   DDLDatabase  $database  base database to build forms upon
     */
    public function __construct(DDLDatabase $database)
    {
        $this->_database = $database;
        $this->createNewFacade();
    }

    /**
     * Create new facade instance.
     *
     * @access  public
     */
    public function createNewFacade()
    {
        $this->object = new FormFacade();
    }

    /**
     * Build facade object.
     * 
     * @access  public
     * @return  FormFacade 
     */
    public function buildFacade()
    {
        $this->_initActions();
        return $this->object;
    }

    /**
     * Get form object.
     *
     * @access  public
     * @return  DDLForm
     */
    public function getForm()
    {
        return $this->object->form;
    }

    /**
     * Set form object.
     *
     * @access  public
     * @param   DDLForm  $form  configuring the contents of the form
     * @return  FormFacadeBuilder 
     */
    public function setForm(DDLForm $form)
    {
        $this->object->form = $form;
        return $this;
    }

    /**
     * Get form setup.
     *
     * @access  public
     * @return  FormSetup
     */
    public function getSetup()
    {
        return $this->object->setup;
    }

    /**
     * Set form setup.
     *
     * @access  public
     * @param   FormSetup  $setup  configuring the behavior of the form
     * @return  FormFacadeBuilder 
     */
    public function setSetup(FormSetup $setup)
    {
        $this->object->setup = $setup;
        return $this;
    }

    /**
     * Get query.
     *
     * @access  public
     * @return  DbSelect
     */
    public function getQuery()
    {
        return $this->object->query;
    }

    /**
     * get list of foreign-key reference settings
     *
     * This returns an array of the following contents:
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
     * @ignore
     */
    private function _getReferences()
    {
        $references = array();
        assert('!isset($field);');
        /* @var $field DDLDefaultField */
        foreach ($this->getForm()->getFields() as $field)
        {
            if ($field->getType() !== 'reference') {
                continue;
            }
            assert('!isset($column);');
            $column = $field->getColumnDefinition();
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
            $references[$field->getName()] = $reference;
            unset($column);
        } // end foreach
        unset($field);
        return $references;
    }

    /**
     * Get reference values.
     *
     * This function returns an array, where the keys are the values of a unique key in the
     * target table and the values are the labels for those keys.
     *
     * Use this function for AJAX auto-completion in reference column.
     *
     * The list can be limited to a maximum length by setting the $limit argument. Default is 50 rows.
     * The search term allows to find all rows whose labels start with a given text.
     * You may use the wildcards '%' and '_'.
     *
     * Note: you may want to introduce an index on the label-column of your database.
     *
     * If the field is no reference in the current form, then an empty array will be returned.
     *
     * @access  protected
     * @param   string  $fieldName   name of field to look up
     * @param   string  $searchTerm  find all entries that start with ...
     * @param   int     $limit       maximum number of hits, set to 0 to get all
     * @return  array
     * @ignore
     */
    protected function _getReferenceValues($fieldName, $searchTerm = "", $limit = 50)
    {
        assert('is_string($fieldName); // Invalid argument $fieldName: string expected');
        assert('is_string($searchTerm); // Invalid argument $searchTerm: string expected');
        $referenceValues = array();
        $references = $this->_getReferences();
        if (isset($references[$fieldName])) {
            $reference = $references[$fieldName];
            $db = $this->getForm()->getDatabase()->getName();
            $select = new DbSelect(Yana::connect($db));
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

    /**
     * get query
     *
     * This returns the query object which is bound to the form.
     * You can modify this to filter the visible results.
     *
     * @access  public
     * @return  DbSelect
     * @throws  NotFoundException  if the selected table or one of the selected columns is not found
     */
    public function buildQuery(DbStream $connection)
    {
        $form = $this->getForm();
        $setup = $this->getSetup();
        $query = new DbSelect($connection);
        $query->setTable($form->getTable());
        $query->setLimit($setup->getEntriesPerPage());
        $query->setOffset($setup->getPage() * $setup->getEntriesPerPage());
        if ($setup->getOrderByField()) {
            $query->setOrderBy((array) $setup->getOrderByField(), (array) $setup->isDescending());
        }
        if ($setup->hasFilter()) {
            foreach ($setup->getFilters() as $columnName => $filter)
            {
                $havingClause = array($columnName, 'like', $filter);
                $query->addHaving($havingClause);
            }
        }
        if ($setup->getColumnNames()) {
            $query->setColumns($setup->getColumnNames()); // throws NotFoundException
        }
        return $this->object->query = $query;
    }

    /**
     * create form object settings from database query
     *
     * This function takes a database query and initializes the form using the
     * table and columns of the query.
     *
     * @access  public
     * @return  DDLForm
     */
    public function buildFormFromTable(DDLTable $table)
    {
        $genericName = $this->_database->getName() . '-' . $table->getName();

        // check if the form already exists
        if ($this->_database->isForm($genericName)) {
            return $this->object->form = $this->_database->getForm($genericName);
        }
        // otherwise create a new form

        $form = $this->object->form;
        if (! $this->object->form instanceof DDLForm) {
            $form = new DDLForm($genericName); // from scratch
        } else {
            $form->setName($genericName); // from cache
        }
        $form->setTable($table->getName());

        // get table definition
        $title = $table->getTitle();
        // fall back to table name if title is empty
        if (empty($title)) {
            $title = $table->getName();
        }
        $form->setTitle($title);

        // copy security settings from table to form
        assert('!isset($grant); // Cannot redeclare var $grant');
        foreach ($table->getGrants() as $grant)
        {
            $form->setGrant($grant);
        }

        return $this->object->form = $form;
    }

    /**
     * create form object settings from database query
     *
     * This function takes a database query and initializes the form using the
     * table and columns of the query.
     *
     * @access  public
     * @return  DDLForm
     */
    public function setQuery(DbSelect $query)
    {
        if ($query->getExpectedResult() != DbResultEnumeration::TABLE) {
            $columns = array();
            foreach ($query->getColumns() as $alias => $columnDef)
            {
                $columnName = $columnDef[1];
                // get column definition
                $columns[$alias] = $table->getColumn($columnName); // @todo FIXME
            }
        } else {
            $columns = $table->getColumns(); // @todo FIXME
        }
        assert('!isset($alias); // Cannot redeclare var $alias');
        assert('!isset($columnDef); // Cannot redeclare var $columnDef');
        foreach ($columns as $alias => $columns)
        {
            $this->_addFieldByColumn($columns, $alias);
        }
        $this->object->query = $query;

        return $this->object->form;
    }

    /**
     * Update setup with request array.
     *
     * @access  public
     * @param   array  $request  initial values (e.g. Request array)
     * @return  FormFacadeBuilder 
     */
    public function updateSetup(array $request = array())
    {
        if (isset($request['page'])) {
            $this->setup->setPage((int) $request['page']);
        }
        if (isset($request['entries'])) {
            $this->setup->setEntriesPerPage((int) $request['entries']);
        }
        if (isset($request['layout'])) {
            $this->setup->setLayout((int) $request['layout']);
        }
        if (isset($request['search'])) {
            $this->setup->setSearchTerm($request['search']);
        }
        if (!empty($request['dropfilter'])) {
            $this->setup->setFilters();
        }
        if (isset($request['filter']) && is_array($request['filter'])) {
            foreach ($request['filter'] as $columnName => $searchTerm)
            {
                $this->setup->setFilter($columnName, $searchTerm);
            }
        }
        if (!empty($request['sort'])) {
            $this->setup->setOrderByField($request['sort']);
        }
        if (!empty($request['orderby'])) {
            $this->setup->setOrderByField($request['orderby']);
        }
        if (!empty($request['desc'])) {
            $this->setup->setSortOrder(true);
        }
        return $this;
    }

    /**
     * add field by column definition
     *
     * @access  private
     * @param   DDLForm    $form     form definition
     * @param   DDLColumn  $columns  column definition
     * @param   string     $alias    column name alias
     */
    private function _addFieldByColumn(DDLForm $form, DDLColumn $columns, $alias = "")
    {
        $columnName = $columns->getName();

        // set alias to equal column name, if none is present
        if (!is_string($alias) || empty($alias)) {
            $alias = $columnName;
        }
        $field = null;
        try {
            $field = $form->addField($alias, 'DDLAutoField');
        } catch (AlreadyExistsException $e) {
            return; // field already exists - nothing to do!
        }

        // set the column title (aka "label")
        assert('!isset($title); // Cannot redeclare var $title');
        $title = $columns->getTitle();
        if (!empty($title)) {
            $field->setTitle($title);
        } elseif ($columns->isPrimaryKey()) {
            $field->setTitle("ID");
        } else {
            // fall back to column name if title is empty
            $field->setTitle($columns->getName());
        }
        unset($title);

        // copy column grants to field
        foreach ($columns->getGrants() as $grant)
        {
            $field->setGrant($grant);
        }
    }

    /**
     * Scans the actions and removes those to whom the current user has no access.
     *
     * @access  protected
     */
    protected function _initActions()
    {
        $form = $this->getForm();
        $setup = $this->getSetup();
        $session = SessionManager::getInstance();

        $action = $setup->getDownloadAction()
            || ($event = $form->getEvent('download') && $action = $event->getAction())
            || $action = "download_file";
        unset($event);

        if (!$form->isSelectable() || !$session->checkPermission(null, $action)) {
            $action = "";
        }
        $setup->setDownloadAction($action);

        $action = $setup->getSearchAction()
            || ($event = $form->getEvent('search') && $action = $event->getAction());
        unset($event);

        if ($action) {
            if (!$form->isSelectable() || !$session->checkPermission(null, $action)) {
                $action = "";
            }
            $setup->setSearchAction($action);
        }

        $action = $setup->getInsertAction()
            || ($event = $form->getEvent('insert') && $action = $event->getAction());
        unset($event);

        if ($action) {
            if (!$form->isInsertable() || !$session->checkPermission(null, $action)) {
                $action = "";
            }
            $setup->setInsertAction($action);
        }

        $action = $setup->getUpdateAction()
            || ($event = $form->getEvent('update') && $action = $event->getAction());
        unset($event);

        if ($action) {
            if (!$form->isUpdatable() || !$session->checkPermission(null, $action)) {
                $action = "";
            }
            $setup->setUpdateAction($action);
        }

        $action = $setup->getDeleteAction()
            || ($event = $form->getEvent('delete') && $action = $event->getAction());
        unset($event);

        if ($action) {
            if (!$form->isDeletable() || !$session->checkPermission(null, $action)) {
                $action = "";
            }
            $setup->setDeleteAction($action);
        }

        $action = $setup->getExportAction()
            || ($event = $form->getEvent('export') && $action = $event->getAction());
        unset($event);

        if ($action) {
            if (!$form->isSelectable() || !$session->checkPermission(null, $action)) {
                $action = "";
            }
            $setup->setExportAction($action);
        }
    }

    /**
     * Get table definition.
     *
     * Each form definition must be linked to a table in the same database.
     * This function looks it up and returns this definition.
     *
     * @access  protected
     * @return  DDLTable
     * @throws  NotFoundException  when the database, or table was not found
     */
    protected function _getTable()
    {
        if (!isset($this->_table)) {
            $form = $this->getForm();
            $name = $form->getTable();
            $database = $form->getDatabase();
            if (!($database instanceof DDLDatabase)) {
                $message = "Error in form '" . $form->getName() . "'. No parent database defined.";
                throw new NotFoundException($message);
            }
            $tableDefinition = $database->getTable($name);
            if (!($tableDefinition instanceof DDLTable)) {
                $message = "Error in form '" . $form->getName() . "'. Parent table '$name' not found.";
                throw new NotFoundException($message);
            }
            $this->_table = $tableDefinition;
        }
        return $this->_table;
    }

    /**
     * Get the form's row-count.
     *
     * Returns the number of rows in the current form.
     * If the form is empty, it returns int(0).
     *
     * @access  protected
     * @return  int
     */
    protected function _getLastPage()
    {
        if (!isset($this->_lastPage)) {
            $query = $this->getQuery();
            $offset = $query->getOffset();
            $limit = $query->getLimit();
            $query->setLimit(0);
            $query->setOffset(0);
            $this->_lastPage = $query->countResults();
            $query->setLimit($limit);
            $query->setOffset($offset);
        }
        return $this->_lastPage;
    }

    /**
     * check if the current page is the last page
     *
     * Returns bool(true) if the current page number + visible entries per page
     * is less than the overall number of rows.
     *
     * @access  protected
     * @abstract
     * @return  bool
     */
    //abstract public function _isLastPage();

    /**
     * get default form iterator
     *
     * This returns an object, which implements the Iterator interface.
     * Use this to walk across all {@see DDLDefaultField}s that are suitable for searching.
     *
     * @access  protected
     * @return  DDLDefaultUpdateIterator
     */
    protected function _buildUpdateIterator()
    {
        if (!isset($this->_iterator)) {
            $this->_iterator = new DDLDefaultUpdateIterator($this->getForm());
        }
        return $this->_iterator;
    }

    /**
     * get values of update form
     *
     * This returns an array of values entered in the update form.
     *
     * @access  protected
     * @return  array
     */
    protected function _getUpdateValues()
    {
        return $this->getIterator()->getValues();
    }

    /**
     * get search form iterator
     *
     * This returns an object, which implements the Iterator interface.
     * Use this to walk across all {@see DDLDefaultField}s that are suitable for searching.
     *
     * @access  protected
     * @return  DDLDefaultSearchIterator
     */
    protected function _buildSearchIterator()
    {
        if (!isset($this->_searchIterator)) {
            $this->_searchIterator = new DDLDefaultSearchIterator($this->getForm());
        }
        return $this->_searchIterator;
    }

    /**
     * get values of search form
     *
     * This returns an array of values entered in the search form.
     *
     * @access  protected
     * @return  array
     */
    protected function _getSearchValues()
    {
        return $this->getSearchIterator()->getValues();
    }

    /**
     * get values of search form as where clause
     *
     * This returns an array of values entered in the search form.
     * If there are no entries, the function will return NULL.
     *
     * Example:
     * <code>
     * $query = $form->getQuery();
     * $where = $form->getSearchValuesAsWhereClause();
     * if (!is_null($where)) {
     *     $query->setWhere($where);
     * }
     * $results = $query->getResults();
     * </code>
     *
     * @access  protected
     * @return  array
     */
    protected function _getSearchValuesAsWhereClause()
    {
        if (is_null($this->getSearchValues())) {
            return null;
        }
        /* @var $iterator DDLDefaultSearchIterator */
        $iterator = $this->buildSearchIterator();
        $clause = array();
        /* @var $field DDLDefaultField */
        foreach ($iterator as $field)
        {
            $test = $iterator->getValueAsWhereClause();
            if (is_null($test)) {
                continue; // field is empty
            }
            if (!empty($clause)) {
                $clause = array($clause, 'AND', $test);
            } else {
                $clause = $test;
            }
        }
        return $clause;
    }

    /**
     * get search form iterator
     *
     * This returns an object, which implements the Iterator interface.
     * Use this to walk across all {@see DDLDefaultField}s that are suitable for searching.
     *
     * @access  protected
     * @return  DDLDefaultReportIterator
     */
    protected function _buildReportIterator()
    {
        if (!isset($this->_reportIterator)) {
            $this->_reportIterator = new DDLDefaultReportIterator($this->getForm());
        }
        return $this->_reportIterator;
    }

    /**
     * get values of report form
     *
     * This returns an array of values entered in the report form.
     *
     * @access  protected
     * @return  array
     */
    protected function _getReportValues()
    {
        return $this->getReportIterator()->getValues();
    }

    /**
     * get insert form iterator
     *
     * This returns an object, which implements the Iterator interface.
     * Use this to walk across all {@see DDLDefaultField}s that are needed for inserting.
     *
     * @access  protected
     * @return  DDLDefaultInsertIterator
     */
    protected function _buildInsertIterator()
    {
        if (!isset($this->_insertIterator)) {
            $this->_insertIterator = new DDLDefaultInsertIterator($this->getForm());
        }
        return $this->_insertIterator;
    }

    /**
     * get values of insert form
     *
     * This returns an array of values entered in the insert form.
     *
     * @access  protected
     * @return  array
     */
    protected function _getInsertValues()
    {
        $iterator = $this->buildInsertIterator();
        $values = $iterator->getValues();
        $form = $this->getForm();
        $parentForm = $form->getParent();
        // copy foreign key from parent query
        if ($parentForm instanceof DDLAbstractForm && $parentForm->getTable() !== $form->getTable()) {
            $results = $parentForm->getQuery()->getResults();
            if (count($results) === 1) {
                $foreignKey = array_shift($this->getForeignKey());
                $values[$foreignKey] = key($results);
                unset($foreignKey);
            }
            unset($results);
        }
        return $values;
    }

    /**
     * get read form iterator
     *
     * This returns an object, which implements the Iterator interface.
     * Use this to walk across all {@see DDLDefaultField}s that may be viewed.
     *
     * @access  protected
     * @return  DDLDefaultReadIterator
     */
    protected function _buildReadIterator()
    {
        if (!isset($this->_readIterator)) {
            $this->_readIterator = new DDLDefaultReadIterator($this->getForm());
        }
        return $this->_readIterator;
    }

    /**
     * get foreign key for base form
     *
     * This function returns the foreign key definition for subforms.
     * The return value is an array of the source-column in the table of the subform and
     * the target-column in the table of the base-form.
     *
     * @access  pubprotectedlic
     * @throws  DBWarning  when no foreign key is found
     * @return  array
     * @throws  NotFoundException  when the database, or table was not found
     */
    protected function _getForeignKey()
    {
        $form = $this->getForm();
        if (!($form->getParent() instanceof DDLForm)) {
            return null;
        }
        $query = $this->getQuery();
        $results = $query->getResults();
        $db = $form->getDatabase();

        $targetTable = $form->getParent()->getTable();
        $sourceTable = $this->getTable();
        $keyName = $this->getKey();
        $columnName = null;
        /* @var $foreign DDLForeignKey */
        foreach ($sourceTable->getForeignKeys() as $foreign)
        {
            if ($targetTable != $foreign->getTargetTable()) {
                continue;
            }
            $columns = $foreign->getColumns();
            if (!empty($keyName)) {
                if (!isset($columns[$keyName])) {
                    continue;
                } elseif (!empty($columns[$keyName])) {
                    $columnName = $columns[$keyName];
                } else {
                    $columnName = $db->getTable($targetTable)->getPrimaryKey();
                }
                break;
            } else {
                $keyName = key($columns);
                $columnName = current($columns);
                break;
            }
        }
        if (empty($keyName) || empty($columnName)) {
            $message = "No suitable foreign-key found in form '{$this->getName()}'.";
            throw new DbWarning($message, E_USER_WARNING);
        }
        return array($keyName, $columnName);
    }

    /**
     * create links to other pages
     *
     * @access  protected
     * @return  string
     */
    protected function _buildListOfEntries()
    {
        if (!isset($this->_listOfEntries)) {
            $setup = $this->getSetup();
            $form = $this->getForm();
            $lastPage = $this->getLastPage();
            $entriesPerPage = $setup->getEntriesPerPage();
            assert('$entriesPerPage > 0; // invalid number of entries to view per page');
            $currentPage = $setup->getPage();
            $this->_listOfEntries = "";
            assert('!isset($pluginManager); // Cannot redeclare var $pluginManager');
            $pluginManager = PluginManager::getInstance();
            $action = $pluginManager->getFirstEvent();
            $lang = Language::getInstance();
            $linkTemplate = '<a class="gui_generator_%s" href=' .
                SmartUtility::href("action=$action&" . $form->getName() . "[page]=%s") .
                ' title="%s">%s</a>';
            // previous page
            if ($currentPage > 0) { // is not first page
                $page = $currentPage - $entriesPerPage;
                if ($page < 0) {
                    $page = 0;
                }
                $this->_listOfEntries .= sprintf($linkTemplate, 'previous', $page,
                    $lang->getVar("TITLE_PREVIOUS"), $lang->getVar("BUTTON_PREVIOUS"));
            }
            // more pages
            if ($lastPage > ($entriesPerPage * 2)) { // has more than 2 pages

                $dots = false;

                $title = $lang->getVar("TITLE_LIST");
                $isTooLong = $lastPage > (10 * $entriesPerPage); // has more than 10 pages

                for ($page = 0; $page < ceil($lastPage / $entriesPerPage); $page++)
                {
                    /**
                     * if more than 10 pages exist and current page is not first page or last page
                     * and is not current page or previous or next 3 pages
                     */
                    $isNearCurrent = (floor($currentPage / $entriesPerPage) - 3) < $page && // previous 3 pages, or
                        $page < (floor($currentPage / $entriesPerPage) + 3);                // next 3 pages

                    $isFirstOrLast = $page <= 1 ||                         // is first page, or
                        $page >= (ceil($lastPage / $entriesPerPage) - 2 ); // is last page

                    if ($isTooLong && !$isFirstOrLast && !$isNearCurrent) {
                        /* this marks an elipsis */
                        if ($dots === false) {
                            $this->_listOfEntries .= "...";
                            $dots = true;
                        } else {
                            /* ignore this page */
                            continue;
                        }
                    } else {
                        $first = ($page * $entriesPerPage);
                        $last = $lastPage;
                        if (($page + 1) * $entriesPerPage < $lastPage) {
                            $last = ($page + 1) * $entriesPerPage;
                        }
                        $text = '';
                        // link text
                        if ($first + 1 != $last) {
                            $text = '[' . ($first + 1) . '-' . $last . ']';
                        } else {
                            $text = '[' . $last . ']';
                        }
                        if ($currentPage < $first || $currentPage > $last - 1) { // is not current page
                            $this->_listOfEntries .= sprintf($linkTemplate, 'page', $first,
                                $title, $text);
                        } else {
                            $this->_listOfEntries .= $text;
                        }
                        if ($isNearCurrent) {
                            $dots = false;
                        }
                    }
                } // end for
            }
            if (true) {
               $b = $a;
            }
            // next page
            if (!$this->isLastPage()) { // is not last page
                $page = $currentPage + $entriesPerPage;
                $this->_listOfEntries .= sprintf($linkTemplate, 'next', $page,
                    $lang->getVar("TITLE_NEXT"), $lang->getVar("BUTTON_NEXT"));
            }
        }
        return $this->_listOfEntries;
    }

}

?>