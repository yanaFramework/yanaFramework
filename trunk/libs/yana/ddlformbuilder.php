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
 * @subpackage  database
 */
class DDLFormBuilder extends DDLForm implements IteratorAggregate
{

    /**
     * database (select-) query
     *
     * @access  private
     * @var     DbSelect
     * @ignore
     */
    private $_query = null;

    /**
     * last page (for multi-page layout)
     *
     * @access  private
     * @var     int
     * @ignore
     */
    private $_lastPage = null;

    /**
     * DDL definition object of selected table
     *
     * @access  private
     * @var     DDLTable
     * @ignore
     */
    private $_table = null;

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
     * form to work on
     *
     * @access  private
     * @var     DDLForm
     */
    private $_form = null;

    /**
     * setup for current form
     *
     * @access  private
     * @var     DDLFormSetup
     */
    private $_setup = null;

    /**
     * Initialize instance
     *
     * @param DDLForm      $form  used to build HTML forms
     * @param DDLFormSetup $setup setup for current form
     */
    public function __construct(DDLForm $form, DDLFormSetup $setup)
    {
        $this->_form = $form;
        $this->_setup = $setup;
    }

    /**
     * Get form object.
     *
     * @access  public
     * @return  DDLForm
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Get form setup.
     *
     * @access  public
     * @return  DDLFormSetup
     */
    public function getSetup()
    {
        return $this->_setup;
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
        $this->_query = $query;
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
    public function buildQuery()
    {
        if (!isset($this->_query)) {
            $form = $this->getForm();
            $setup = $this->getSetup();
            $database = $form->getDatabase();
            if ($database instanceof DDLDatabase) {
                $query = new DbSelect(Yana::connect($database));
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
                $this->_query = $query;
            }
        }
        return $this->_query;
    }

    /**
     * create form object settings from database query
     *
     * This function takes a database query and initializes the form using the
     * table and columns of the query.
     *
     * @access  public
     * @param   DbSelect  $selectQuery database quey object
     * @return  DDLForm
     */
    public function buildForm(DbSelect $selectQuery)
    {
        $tableName = $selectQuery->getTable();
        $form = $this->getForm();
        $form->setTable($tableName);
        // get table definition
        $table = $selectQuery->getDatabase()->getSchema()->getTable($tableName);
        assert('$table instanceof DDLTable; // Table not found');
        assert('!isset($title); // Cannot redeclare var $title');
        $title = $table->getTitle();
        if (!empty($title)) {
            $form->setTitle($title);
        } else {
            // fall back to table name if title is empty
            $form->setTitle($table->getName());
        }
        unset($title);
        assert('!isset($grant); // Cannot redeclare var $grant');
        foreach ($table->getGrants() as $grant)
        {
            $form->setGrant($grant);
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
        return $form;
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
     * set download action
     *
     * @access  public
     * @param   string  $action action name
     */
    public function setDownloadAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->_downloadAction = $action;
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
            $form = $this->getForm();
            if ($form->isSelectable()) {
                $event = $form->getEvent('download');
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
        $this->_searchAction = $action;
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
            $form = $this->getForm();
            if ($form->isSelectable()) {
                $event = $form->getEvent('search');
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
        $this->_insertAction = $action;
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
            $form = $this->getForm();
            if ($form->isInsertable()) {
                $event = $form->getEvent('insert');
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
        $this->_updateAction = $action;
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
            $form = $this->getForm();
            if ($form->isUpdatable()) {
                $event = $form->getEvent('update');
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
        $this->_deleteAction = $action;
    }

    /**
     * Get delete action.
     *
     * Returns the lower-cased name of the currently selected action, or bool(false) if none has been selected yet.
     *
     * @access  public
     * @return  string
     */
    public function getDeleteAction()
    {
        if (!isset($this->_deleteAction)) {
            $this->_deleteAction = "";
            $form = $this->getForm();
            if ($form->isDeletable()) {
                $event = $form->getEvent('delete');
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
        $this->_exportAction = $action;
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
            $form = $this->getForm();
            if ($form->isSelectable()) {
                $event = $form->getEvent('export');
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
     * Get table definition.
     *
     * Each form definition must be linked to a table in the same database.
     * This function looks it up and returns this definition.
     *
     * @access  public
     * @return  DDLTable
     * @throws  NotFoundException  when the database, or table was not found
     */
    public function getTable()
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
     * @access  public
     * @return  int
     */
    public function getLastPage()
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
     * @access  public
     * @abstract
     * @return  bool
     */
    abstract public function isLastPage();

    /**
     * get default form iterator
     *
     * This returns an object, which implements the Iterator interface.
     * Use this to walk across all {@see DDLDefaultField}s that are suitable for searching.
     *
     * @access  public
     * @return  DDLDefaultUpdateIterator
     */
    public function buildUpdateIterator()
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
    public function buildSearchIterator()
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
     * @access  public
     * @return  DDLDefaultReportIterator
     */
    public function buildReportIterator()
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
    public function buildInsertIterator()
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
     * @access  public
     * @return  array
     */
    public function getInsertValues()
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
    public function buildReadIterator()
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
     * @access  public
     * @throws  DBWarning  when no foreign key is found
     * @return  array
     * @throws  NotFoundException  when the database, or table was not found
     */
    public function getForeignKey()
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

}

?>