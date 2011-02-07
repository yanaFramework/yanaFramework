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
 * Abstract form settings
 *
 * @access      public
 * @abstract
 * @package     yana
 * @subpackage  database
 */
abstract class DDLAbstractForm extends DDLForm implements IteratorAggregate
{
    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string'),
        'grant'       => array('grants',      'array', 'DDLGrant'),
        'form'        => array('forms',       'array', 'DDLDefaultForm'),
        'input'       => array('fields',      'array', 'DDLDefaultField'),
        'event'       => array('events',      'array', 'DDLEvent')
    );

    /**
     * database (select-) query
     *
     * @access  protected
     * @var     DbSelect
     * @ignore
     */
    protected $query = null;

    /**
     * last page (for multi-page layout)
     *
     * @access  protected
     * @var     int
     * @ignore
     */
    protected $lastPage = null;

    /**
     * DDL definition object of selected table
     *
     * @access  protected
     * @var     DDLTable
     * @ignore
     */
    protected $tableDefinition = null;

    /**
     * form data cache
     *
     * @access  protected
     * @var     array
     * @ignore
     */
    protected $values = array();

    /**
     * iterator for {@see DDLDefaultField} elements
     *
     * @access  private
     * @var     DDLDefaultReportIterator
     * @ignore
     */
    private $_reportIterator = null;

    /**
     * iterator for {@see DDLDefaultField} elements
     *
     * @access  private
     * @var     DDLDefaultInsertIterator
     * @ignore
     */
    private $_insertIterator = null;

    /**
     * iterator for {@see DDLDefaultField} elements
     *
     * @access  private
     * @var     DDLDefaultUpdateIterator
     * @ignore
     */
    private $_iterator = null;

    /**
     * iterator for {@see DDLDefaultField} elements
     *
     * @access  private
     * @var     DDLDefaultSearchIterator
     * @ignore
     */
    private $_searchIterator = null;

    /**
     * iterator for {@see DDLDefaultField} elements
     *
     * @access  private
     * @var     DDLDefaultReadIterator
     * @ignore
     */
    private $_readIterator = null;

    /**
     * cached action
     *
     * @access  private
     * @var     string
     * @ignore
     */
    private $_searchAction = null;

    /**
     * cached action
     *
     * @access  private
     * @var     string
     * @ignore
     */
    private $_downloadAction = null;

    /**
     * cached action
     *
     * @access  private
     * @var     string
     * @ignore
     */
    private $_insertAction = null;

    /**
     * cached action
     *
     * @access  private
     * @var     string
     * @ignore
     */
    private $_updateAction = null;

    /**
     * cached action
     *
     * @access  private
     * @var     string
     * @ignore
     */
    private $_deleteAction = null;

    /**
     * export action
     *
     * @access  private
     * @var     string
     * @ignore
     */
    private $_exportAction = null;

    /**
     * true, if the form was loaded from session cache
     *
     * @access  private
     * @var     bool
     * @ignore
     */
    private $_isCached = false;

    /**
     * set wether form should include all input fields
     *
     * This function sets the attribute "allinput" of the form.
     * AND if it is bool(true), it also adds the missing fields.
     *
     * Note that this cannot be undone!
     *
     * @access  public
     * @param   bool  $allinput  auto-generate input fields (yes/no)
     */
    public function setAllInput($allinput)
    {
        parent::setAllInput($allinput);
        // if allinput = true and no sub-forms, scan for missing fields
        if ($allinput) {
            $fields = $this->getFields();
            $table = $this->getTableDefinition();
            /* @var $columns DDLColumn */
            foreach ($table->getColumns() as $columns)
            {
                // add field, if it is missing
                if (!isset($fields[$columns->getName()])) {
                    $this->_addFieldByColumn($columns);
                }
            }
        }
    }

    /**
     * bind query to form object
     *
     * This must be a select query. It is bound to the contents of the form.
     * The visible rows and columns of the resulting form depend on which rows and columns are
     * included in the query.
     *
     * Note! Binding a query to an empty form will auto-detect the query settings and build
     * default contents.
     *
     * @access  public
     * @param   DbSelect  $query  select query
     */
    public function setQuery(DbSelect $query)
    {
        $this->query = $query;
        // if object is uninitialized, auto-create a default form object
        if (!$this->isInitialized) {
            $this->createFromQuery($query);
            $this->isInitialized = true;
        }
    }

    /**
     * get query
     *
     * This returns the query object which is bound to the form.
     * You can modify this to filter the visible results.
     *
     * @access  public
     * @return  DbSelect
     */
    public function getQuery()
    {
        if (!isset($this->query)) {
            $database = $this->getDatabase();
            if ($database instanceof DDLDatabase) {
                $query = new DbSelect(Yana::connect($database));
                $query->setTable($this->table);
                $this->setQuery($query);
            }
        }
        return $this->query;
    }

    /**
     * create form object settings from database query
     *
     * This function takes a database query and initializes the form using the
     * table and columns of the query.
     *
     * @access  protected
     * @param   DbSelect  $selectQuery databese quey object
     */
    protected function createFromQuery(DbSelect $selectQuery)
    {
        $tableName = $selectQuery->getTable();

        $this->setTable($tableName);
        // get table definition
        $table = $selectQuery->getDatabase()->getSchema()->getTable($tableName);
        assert('$table instanceof DDLTable; // Table not found');
        assert('!isset($title); // Cannot redeclare var $title');
        $title = $table->getTitle();
        if (!empty($title)) {
            $this->setTitle($title);
        } else {
            // fall back to table name if title is empty
            $this->setTitle($table->getName());
        }
        unset($title);
        assert('!isset($grant); // Cannot redeclare var $grant');
        foreach ($table->getGrants() as $grant)
        {
            $this->setGrant($grant);
        }
        unset($grant);
        if ($selectQuery->getExpectedResult() != DbResultEnumeration::TABLE) {
            $columns = array();
            foreach ($selectQuery->getColumns() as $alias => $columnDef)
            {
                $columnName = $columnDef[1];
                // get column definition
                $columns[$alias] = $table->getColumn($columnName);
            }
        } else {
            $columns = $table->getColumns();
        }
        assert('!isset($alias); // Cannot redeclare var $alias');
        assert('!isset($columnDef); // Cannot redeclare var $columnDef');
        foreach ($columns as $alias => $columns)
        {
            $this->_addFieldByColumn($columns, $alias);
        }
    }

    /**
     * add field by column definition
     *
     * @access  private
     * @param   DDLColumn  $columns  column definition
     * @param   string     $alias    column name alias
     */
    private function _addFieldByColumn(DDLColumn $columns, $alias = "")
    {
        $columnName = $columns->getName();

        // set alias to equal column name, if none is present
        if (!is_string($alias) || empty($alias)) {
            $alias = $columnName;
        }
        try {
            $field = $this->addField($alias, 'DDLAutoField');
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
     * set download action
     *
     * @access  public
     * @param   string  $action action name
     */
    public function setDownloadAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->setEvent('download', $action);
    }

    /**
     * get download action
     *
     * Returns the lower-cased name of the currently selected action.
     *
     * The default is 'download_file'.
     *
     * @access  public
     * @return  string
     */
    public function getDownloadAction()
    {
        if (!isset($this->_downloadAction)) {
            $this->_downloadAction = "";
            if ($this->isSelectable()) {
                $event = $this->getEvent('download');
                if ($event instanceof DDLEvent) {
                    $session = SessionManager::getInstance();
                    $action = $event->getAction();
                    if ($session->checkPermission(null, $action)) {
                        $this->_downloadAction = $action;
                    }
                } else {
                    $this->_downloadAction = "download_file";
                }
            }
        }
        return $this->_downloadAction;
    }

    /**
     * set search action
     *
     * @access  public
     * @param   string  $action  action name
     */
    public function setSearchAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->setEvent('search', $action);
    }

    /**
     * get search action
     *
     * Returns the lower-cased name of the currently selected action, or NULL if none has been
     * selected yet.
     *
     * @access  public
     * @return  string
     */
    public function getSearchAction()
    {
        if (!isset($this->_searchAction)) {
            $this->_searchAction = "";
            if ($this->isSelectable()) {
                $event = $this->getEvent('search');
                if ($event instanceof DDLEvent) {
                    $session = SessionManager::getInstance();
                    $action = $event->getAction();
                    if ($session->checkPermission(null, $action)) {
                        $this->_searchAction = $action;
                    }
                }
            }
        }
        return $this->_searchAction;
    }

    /**
     * set insert action
     *
     * @access  public
     * @param   string  $action  action name
     */
    public function setInsertAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->setEvent('insert', $action);
    }

    /**
     * get insert action
     *
     * Returns the lower-cased name of the currently selected action, or NULL if none has been
     * selected yet.
     *
     * @access  public
     * @return  string
     */
    public function getInsertAction()
    {
        if (!isset($this->_insertAction)) {
            $this->_insertAction = "";
            if ($this->isInsertable()) {
                $event = $this->getEvent('insert');
                if ($event instanceof DDLEvent) {
                    $session = SessionManager::getInstance();
                    $action = $event->getAction();
                    if ($session->checkPermission(null, $action)) {
                        $this->_insertAction = $action;
                    }
                }
            }
        }
        return $this->_insertAction;
    }

    /**
     * set update action
     *
     * @access  public
     * @param   string  $action  action name
     */
    public function setUpdateAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->setEvent('update', $action);
    }

    /**
     * get update action
     *
     * Returns the lower-cased name of the currently
     * selected action, or bool(false) if none has been
     * selected yet.
     *
     * @access  public
     * @return  string
     */
    public function getUpdateAction()
    {
        if (!isset($this->_updateAction)) {
            $this->_updateAction = "";
            if ($this->isUpdatable()) {
                $event = $this->getEvent('update');
                if ($event instanceof DDLEvent) {
                    $session = SessionManager::getInstance();
                    $action = $event->getAction();
                    if ($session->checkPermission(null, $action)) {
                        $this->_updateAction = $action;
                    }
                }
            }
        }
        return $this->_updateAction;
    }

    /**
     * set delete action
     *
     * @access  public
     * @param   string  $action action name
     */
    public function setDeleteAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->setEvent('delete', $action);
    }

    /**
     * get delete action
     *
     * Returns the lower-cased name of the currently
     * selected action, or bool(false) if none has been
     * selected yet.
     *
     * @access  public
     * @return  string
     */
    public function getDeleteAction()
    {
        if (!isset($this->_deleteAction)) {
            $this->_deleteAction = "";
            if ($this->isDeletable()) {
                $event = $this->getEvent('delete');
                if ($event instanceof DDLEvent) {
                    $session = SessionManager::getInstance();
                    $action = $event->getAction();
                    if ($session->checkPermission(null, $action)) {
                        $this->_deleteAction = $action;
                    }
                }
            }
        }
        return $this->_deleteAction;
    }

    /**
     * set export action
     *
     * @access  public
     * @param   string  $action action name
     */
    public function setExportAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->setEvent('export', $action);
    }

    /**
     * get export action
     *
     * Returns the lower-cased name of the currently
     * selected action, or bool(false) if none has been
     * selected yet.
     *
     * @access  public
     * @return  string
     */
    public function getExportAction()
    {
        if (!isset($this->_exportAction)) {
            $this->_exportAction = "";
            if ($this->isSelectable()) {
                $event = $this->getEvent('export');
                if ($event instanceof DDLEvent) {
                    $session = SessionManager::getInstance();
                    $action = $event->getAction();
                    if ($session->checkPermission(null, $action)) {
                        $this->_exportAction = $action;
                    }
                }
            }
        }
        return $this->_exportAction;
    }

    /**
     * set event
     *
     * This function creates a new event object with the given name and code an returns it.
     * If another event with the same name already exists, it gets replaced.
     *
     * @access  protected
     * @param   string  $name    event name
     * @param   string  $action  code to execute
     * @return  DDLEvent
     * @ignore
     */
    protected function setEvent($name, $action)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        assert('is_string($action); // Wrong type for argument 2. String expected');
        $event = new DDLEvent("$name");
        $event->setAction($action);
        $this->events["$name"] = $event;
        return $event;
    }

    /**
     * get table definition
     *
     * Each form definition must be linked to a table in the same database.
     * This function looks it up and returns this definition.
     *
     * @access  public
     * @return  DDLTable
     * @throws  NotFoundException  when the database, or table was not found
     */
    public function getTableDefinition()
    {
        if (!isset($this->tableDefinition)) {
            $name = $this->getTable();
            $database = $this->getDatabase();
            if (!($database instanceof DDLDatabase)) {
                $message = "Error in form '{$this->getName()}'. No parent database defined.";
                throw new NotFoundException($message);
            }
            $tableDefinition = $database->getTable($name);
            if (!($tableDefinition instanceof DDLTable)) {
                $message = "Error in form '{$this->getName()}'. Parent table '$name' not found.";
                throw new NotFoundException($message);
            }
            $this->tableDefinition = $tableDefinition;
        }
        return $this->tableDefinition;
    }

    /**
     * get title
     *
     * The title is a label text that should be displayed in the UI when viewing this object.
     *
     * It is optional. If it is not set, the function returns NULL instead.
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        if (empty($this->title)) {
            try {
                $this->title = $this->getTableDefinition()->getTitle();
            } catch (Exception $e) {
                $this->title = $this->getName(); // fall back to name if table does not exist
            }
        }
        return $this->title;
    }

    /**
     * set current page
     *
     * The first page is 0, the second is 1, aso., defaults to 0.
     *
     * @access  public
     * @param   int  $page  number of start page
     */
    public function setPage($page = 0)
    {
        assert('is_int($page); // Wrong type for argument 1. Integer expected');

        /* default values */
        if ($page < 0) {
            trigger_error("Page number was negative. " .
                "Will use the default value 0 instead.", E_USER_NOTICE);
            $page = 0;
        }

        $query = $this->getQuery();
        assert('$query instanceof DbSelect; // must be a select query to apply offset');
        $query->setOffset((int) $page);
    }

    /**
     * set number of entries per page
     *
     * The number of entries per page needs to greater 0, defaults to 5.
     *
     * @access  public
     * @param   int  $entries  number of entries per page
     */
    public function setEntriesPerPage($entries = 5)
    {
        assert('is_int($entries); // Wrong type for argument 1. Integer expected');

        if ($entries < 1) {
            trigger_error("Number of entries per page was less than one. " .
                "Will use the default value 5 instead.", E_USER_NOTICE);
            $entries = 5;
        }

        $query = $this->getQuery();
        assert('$query instanceof DbSelect;');
        $query->setLimit($entries);
        $this->lastPage = null;
    }

    /**
     * get the currently selected page
     *
     * Returns the number of the current page, or bool(false)
     * on error.
     *
     * @access  public
     * @return  int
     */
    public function getPage()
    {
        $query = $this->getQuery();
        assert('$query instanceof DbSelect;');
        return $query->getOffset();
    }

    /**
     * get the form's row-count
     *
     * Returns the number of rows in the current form.
     * If the form is empty, it returns int(0).
     *
     * @access  public
     * @return  int
     */
    public function getLastPage()
    {
        if (!isset($this->lastPage)) {
            $query = $this->getQuery();
            $offset = $query->getOffset();
            $limit = $query->getLimit();
            $query->setLimit(0);
            $query->setOffset(0);
            $this->lastPage = $query->countResults();
            $query->setLimit($limit);
            $query->setOffset($offset);
        }
        return $this->lastPage;
    }

    /**
     * check if the current page is the last page
     *
     * Returns bool(true) if the current page number + visible entries per page
     * is less than the overall number of rows.
     *
     * @access  public
     * @abstract
     * @return  bool
     */
    abstract public function isLastPage();

    /**
     * get form value
     *
     * @access  public
     * @param   string  $key  id of value to retrieve
     * @return  mixed
     */
    public function getValue($key)
    {
        assert('is_string($key); // Wrong argument type argument 1. String expected');
        $values = $this->getValues();
        return Hashtable::get($values, strtolower($key));
    }

    /**
     * get form values
     *
     * This function returns a reference to the rows that will be displayed.
     * You may use this to modify the result before it is send to the browser.
     *
     * Example:
     * <code>
     * $rows =& $form->getRows();
     * foreach ($rows as $i => $row)
     * {
     *     $rows[$i]['FOO'] = $row['BAR'] . "\n" . $row['FOO'];
     *     unset($rows[$i]['BAR']);
     * }
     * </code>
     *
     * Returns an empty array on error.
     *
     * @access  public
     * @return  array
     */
    public function getValues()
    {
        assert('is_array($this->values); // Member "values" is expected to be an array.');
        if (empty($this->values)) {
            $request = Request::getVars($this->getName());
            if (is_array($request)) {
                $this->values = $request;
            }
            $files = Request::getFiles($this->getName());
            if (is_array($files)) {
                $this->values = Hashtable::merge($this->values, $files);
            }
        }
        return $this->values;
    }

    /**
     * set form value
     *
     * @access  public
     * @param   string  $key    id of value to set
     * @param   mixed   $value  new value
     */
    public function setValue($key, $value)
    {
        assert('is_string($key); // Wrong argument type argument 1. String expected');
        $values = $this->getValues();
        $values[$key] = $value;
        $this->setValues($values);
    }

    /**
     * get default form iterator
     *
     * This returns an object, which implements the Iterator interface.
     * Use this to walk across all {@see DDLDefaultField}s that are suitable for searching.
     *
     * @access  public
     * @return  DDLDefaultUpdateIterator
     */
    public function getIterator()
    {
        if (!isset($this->_iterator)) {
            $this->_iterator = new DDLDefaultUpdateIterator($this);
        }
        return $this->_iterator;
    }

    /**
     * get values of update form
     *
     * This returns an array of values entered in the update form.
     *
     * @access  public
     * @return  array
     */
    public function getUpdateValues()
    {
        return $this->getIterator()->getValues();
    }

    /**
     * get search form iterator
     *
     * This returns an object, which implements the Iterator interface.
     * Use this to walk across all {@see DDLDefaultField}s that are suitable for searching.
     *
     * @access  public
     * @return  DDLDefaultSearchIterator
     */
    public function getSearchIterator()
    {
        if (!isset($this->_searchIterator)) {
            $this->_searchIterator = new DDLDefaultSearchIterator($this);
        }
        return $this->_searchIterator;
    }

    /**
     * get values of search form
     *
     * This returns an array of values entered in the search form.
     *
     * @access  public
     * @return  array
     */
    public function getSearchValues()
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
     * @access  public
     * @return  array
     */
    public function getSearchValuesAsWhereClause()
    {
        if (is_null($this->getSearchValues())) {
            return null;
        }
        /* @var $iterator DDLDefaultSearchIterator */
        $iterator = $this->getSearchIterator();
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
     * @access  public
     * @return  DDLDefaultReportIterator
     */
    public function getReportIterator()
    {
        if (!isset($this->_reportIterator)) {
            $this->_reportIterator = new DDLDefaultReportIterator($this);
        }
        return $this->_reportIterator;
    }

    /**
     * get values of report form
     *
     * This returns an array of values entered in the report form.
     *
     * @access  public
     * @return  array
     */
    public function getReportValues()
    {
        return $this->getReportIterator()->getValues();
    }

    /**
     * get insert form iterator
     *
     * This returns an object, which implements the Iterator interface.
     * Use this to walk across all {@see DDLDefaultField}s that are needed for inserting.
     *
     * @access  public
     * @return  DDLDefaultInsertIterator
     */
    public function getInsertIterator()
    {
        if (!isset($this->_insertIterator)) {
            $this->_insertIterator = new DDLDefaultInsertIterator($this);
        }
        return $this->_insertIterator;
    }

    /**
     * get values of insert form
     *
     * This returns an array of values entered in the insert form.
     *
     * @access  public
     * @return  array
     */
    public function getInsertValues()
    {
        $iterator = $this->getInsertIterator();
        $values = $iterator->getValues();
        $parentForm = $this->getParent();
        // copy foreign key from parent query
        if ($parentForm instanceof DDLAbstractForm && $parentForm->getTable() !== $this->getTable()) {
            $results = $parentForm->getQuery()->getResults();
            if (count($results) === 1) {
                $foreignKey = array_shift($this->getForeignKey());
                $values[$foreignKey] = key($results);
            }
            unset($results, $foreignKey);
        }
        return $values;
    }

    /**
     * get read form iterator
     *
     * This returns an object, which implements the Iterator interface.
     * Use this to walk across all {@see DDLDefaultField}s that may be viewed.
     *
     * @access  public
     * @return  DDLDefaultReadIterator
     */
    public function getReadIterator()
    {
        if (!isset($this->_readIterator)) {
            $this->_readIterator = new DDLDefaultReadIterator($this);
        }
        return $this->_readIterator;
    }

    /**
     * set form values
     *
     * @access  public
     * @param   array  $values  new values
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }

    /**
     * get foreign key for base form
     *
     * This function returns the foreign key definition for subforms.
     * The return value is an array of the source-column in the table of the subform and
     * the target-column in the table of the base-form.
     *
     * @access  public
     * @throws  DBWarning  when no foreign key is found
     * @return  array
     * @ignore
     */
    public function getForeignKey()
    {
        if (!($this->parent instanceof DDLAbstractForm)) {
            return null;
        }
        $query = $this->getQuery();
        $results = $query->getResults();
        $db = $this->getDatabase();

        $targetTable = $this->parent->getTable();
        $sourceTable = $this->getTable();
        $table = $db->getTable($sourceTable);
        $keyName = $this->getKey();
        /* @var $foreign DDLForeignKey */
        foreach ($table->getForeignKeys() as $foreign)
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
            throw new DbWarning($message, E_USER_ERROR);
        }
        return array($keyName, $columnName);
    }

    /**
     * has insertable sub-form
     *
     * Returns bool(true) if the form has embedded sub-forms and at least one
     * of them is insertable and has an insert action.
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function hasInsertableChildren()
    {
        /* @var $form DDLDefaultForm */
        foreach ($this->forms as $form)
        {
            if ($form->getInsertAction()) {
                return true;
            }
        }
        return false;
    }

    /**
     * has searchable sub-form
     *
     * Returns bool(true) if the form has embedded sub-forms and at least one
     * of them has a search action.
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function hasSearchableChildren()
    {
        /* @var $form DDLDefaultForm */
        foreach ($this->forms as $form)
        {
            if ($form->getSearchAction()) {
                return true;
            }
        }
        return false;
    }

    /**
     * instance is cached
     *
     * Returns bool(true) if the instance was loaded from session cache and bool(false)
     * if it was created using the constructor.
     *
     * @access  public
     * @return  bool
     */
    public function isCached()
    {
        return !empty($this->_isCached);
    }

    /**
     * initialize instance
     *
     * @access  public
     * @ignore
     */
    public function __wakeup()
    {
        $this->values = array();
        $values = $this->getValues();
        if (isset($values['page'])) {
            $this->setPage((int) $values['page']);
        }
        // reset cached form iterators, when new form-values are available
        if (isset($values['ddldefaultsearchiterator'])) {
            $this->_searchIterator = null;
        }
        if (isset($values['ddldefaultreportiterator'])) {
            $this->_reportIterator = null;
            $parent = $this->parent;
        }
        if (isset($values['ddldefaultinsertiterator'])) {
            $this->_insertIterator = null;
        }
        // these row-iterators must not be cached
        $this->_iterator = null;
        $this->_readIterator = null;
        // mark instance as cached
        $this->_isCached = true;
    }
}

?>