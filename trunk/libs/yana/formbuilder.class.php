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
 *
 * @ignore
 */

/**
 * <<command>> Form builder.
 *
 * This is a command class. It encapsulates parameters to be used to call a complex function.
 *
 * @static
 * @access      public
 * @package     yana
 * @subpackage  form
 * @ignore
 */
class FormBuilder extends Object
{

    /**
     * Database connection.
     *
     * @access  private
     * @var     DbStream
     */
    private $_database;

    /**
     * Database schema.
     *
     * @access  private
     * @var     DDLDatabase
     */
    private $_schema;

    /**
     * Form facade.
     *
     * @access  private
     * @var     FormFacade
     */
    private $_facade;

    /**
     * Query builder class.
     *
     * @access  private
     * @var     FormWorker
     */
    private $_queryBuilder;

    /**
     * Included builder.
     *
     * @access  protected
     * @var     FormSetupBuilder
     */
    private $_setupBuilder = null;

    /**
     * (mandatory) path and name of structure file
     *
     * @access  private
     * @var     string
     */
    private $_file = "";

    /**
     * (optional) name of form to use (either $id or $table must be present!)
     *
     * @access  private
     * @var     string
     */
    private $_id = "";

    /**
     * (optional) table to choose from structure file
     *
     * @access  private
     * @var     string
     */
    private $_table = "";

    /**
     * (optional) list of columns, that should be shown in the form
     *
     * @access  private
     * @var     string
     */
    private $_show = array();

    /**
     * (optional) list of columns, that should NOT be shown in the form
     *
     * @access  private
     * @var     string
     */
    private $_hide = array();

    /**
     * (optional) sequence for SQL-where clause
     *
     * @access  private
     * @var     string
     */
    private $_where = "";

    /**
     * (optional) name of column to sort entries by
     *
     * @access  private
     * @var     string
     */
    private $_sort = "";

    /**
     * (optional) sort entries in descending (true) or ascending (false) order
     *
     * @access  private
     * @var     bool
     */
    private $_desc = false;

    /**
     * (optional) number of 1st entry to show
     *
     * @access  private
     * @var     string
     */
    private $_page = 0;

    /**
     * (optional) number of entries to show on each page
     *
     * @access  private
     * @var     string
     */
    private $_entries = 20;

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @access  private
     * @var     string
     */
    private $_oninsert = "";

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @access  private
     * @var     string
     */
    private $_onupdate = "";

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @access  private
     * @var     string
     */
    private $_ondelete = "";

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @access  private
     * @var     string
     */
    private $_onsearch = "";

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @access  private
     * @var     string
     */
    private $_ondownload = "download_file";

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @access  private
     * @var     string
     */
    private $_onexport = "";

    /**
     * where multiple layouts are available to present the result, this allows to choose the prefered one
     *
     * @access  private
     * @var     int
     */
    private $_layout = null;

    /**
     * base form
     *
     * @access  private
     * @var     DDLForm
     */
    private $_form = null;

    /**
     * Get setup-builder.
     *
     * @access  protected
     * @return  FormSetupBuilder
     */
    protected function _getSetupBuilder()
    {
        return $this->_setupBuilder;
    }

    /**
     * Get query-builder.
     *
     * @access  protected
     * @return  FormWorker
     */
    protected function _getQueryBuilder()
    {
        return $this->_queryBuilder;
    }

    /**
     * Get name of database file.
     *
     * @access  public
     * @return  string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Get id of form.
     *
     * @access  public
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set name of form to use.
     *
     * @access  public
     * @param   string  $id  valid form name
     * @return  SmartFormUtility 
     */
    public function setId($id)
    {
        $this->_id = (string) $id;
        return $this;
    }

    /**
     * Get name of table.
     *
     * @access  public
     * @return  string
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Set table to choose from database.
     *
     * @access  public
     * @param   string  $table  valid table name
     * @return  SmartFormUtility 
     */
    public function setTable($table)
    {
        $this->_table = (string) $table;
        return $this;
    }

    /**
     * Get white-listed column names.
     *
     * @access  public
     * @return  array
     */
    public function getShow()
    {
        return $this->_show;
    }

    /**
     * Set list of columns, that should be shown in the form.
     *
     * @access  public
     * @param   array  $show  white-listed column names.
     * @return  SmartFormUtility 
     */
    public function setShow(array $show)
    {
        $this->_show = $show;
        return $this;
    }

    /**
     * Get black-listed column names.
     *
     * @access  public
     * @return  array
     */
    public function getHide()
    {
        return $this->_hide;
    }

    /**
     * Set list of columns, that should NOT be shown in the form.
     *
     * @access  public
     * @param   array  $hide  black-listed column names.
     * @return  SmartFormUtility 
     */
    public function setHide(array $hide)
    {
        $this->_hide = $hide;
        return $this;
    }

    /**
     * Get where clause.
     *
     * @access  public
     * @return  string|array
     */
    public function getWhere()
    {
        return $this->_where;
    }

    /**
     * Set sequence for SQL-where clause.
     *
     * The syntax is as follows:
     * <ol>
     * <li> left operand </li>
     * <li> operator </li>
     * <li> right operand </li>
     * </ol>
     *
     * List of supported operators:
     * <ul>
     * <li> and, or (indicates that both operands are sub-clauses) </li>
     * <li> =, !=, <, <=, >, >=, like, regexp </li>
     * </ul>
     *
     * Example:
     * <code>
     * array(
     *     array('col1', '=', 'val1'),
     *     'and',
     *     array(
     *         array('col2', '<', 1),
     *         'or',
     *         array('col2', '>', 3)
     *     )
     * )
     * </code>
     *
     * @access  public
     * @param   array  $where  valid where clause
     * @return  SmartFormUtility
     * @see     DbSelectExist::setWhere()
     */
    public function setWhere(array $where)
    {
        $this->_where = $where;
        return $this;
    }

    /**
     * Get name of column to sort by.
     *
     * @access  public
     * @return  string
     */
    public function getSort()
    {
        return $this->_sort;
    }

    /**
     * Set name of column to sort entries by.
     *
     * @access  public
     * @param   string  $sort  valid column name
     * @return  SmartFormUtility 
     */
    public function setSort($sort)
    {
        $this->_sort = (string) $sort;
        return $this;
    }

    /**
     * Check if contents are sorted descending order.
     *
     * @access  public
     * @return  bool
     */
    public function isDescending()
    {
        return $this->_desc;
    }

    /**
     * Set sorting order for entries.
     *
     * @access  public
     * @param   bool  $desc  true = descending, false = ascending
     * @return  SmartFormUtility 
     */
    public function setDescending($desc)
    {
        $this->_desc = (bool) $desc;
        return $this;
    }

    /**
     * Get number of 1st page to show.
     *
     * @access  public
     * @return  int
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * Set number of 1st page to show.
     *
     * @access  public
     * @param   int  $page  positive number (default = 0)
     * @return  SmartFormUtility 
     */
    public function setPage($page)
    {
        $this->_page = (int) $page;
        return $this;
    }

    /**
     * Get number of entries to view per page.
     *
     * @access  public
     * @return  int
     */
    public function getEntries()
    {
        return $this->_entries;
    }

    /**
     * Set number of entries to view per page.
     *
     * @access  public
     * @param   int  $entries  positive number (default = 20)
     * @return  SmartFormUtility 
     */
    public function setEntries($entries)
    {
        $this->_entries = (int) $entries;
        return $this;
    }

    /**
     * Get name of action.
     *
     * @access  public
     * @return  string
     */
    public function getOninsert()
    {
        return $this->_oninsert;
    }

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @access  public
     * @param   string  $oninsert  form action name
     * @return  SmartFormUtility 
     */
    public function setOninsert($oninsert)
    {
        $this->_oninsert = (string) $oninsert;
        return $this;
    }

    /**
     * Get name of action.
     *
     * @access  public
     * @return  string
     */
    public function getOnupdate()
    {
        return $this->_onupdate;
    }

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @access  public
     * @param   string  $onupdate  form action name
     * @return  SmartFormUtility 
     */
    public function setOnupdate($onupdate)
    {
        $this->_onupdate = (string) $onupdate;
        return $this;
    }

    /**
     * Get name of action.
     *
     * @access  public
     * @return  string
     */
    public function getOndelete()
    {
        return $this->_ondelete;
    }

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @access  public
     * @param   string  $ondownload  form action name
     * @return  SmartFormUtility 
     */
    public function setOndelete($ondelete)
    {
        $this->_ondelete = (string) $ondelete;
        return $this;
    }

    /**
     * Get name of action.
     *
     * @access  public
     * @return  string
     */
    public function getOnsearch()
    {
        return $this->_onsearch;
    }

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @access  public
     * @param   string  $ondownload  form action name
     * @return  SmartFormUtility 
     */
    public function setOnsearch($onsearch)
    {
        $this->_onsearch = (string) $onsearch;
        return $this;
    }

    /**
     * Get name of action.
     *
     * @access  public
     * @return  string
     */
    public function getOndownload()
    {
        return $this->_ondownload;
    }

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @access  public
     * @param   string  $ondownload  form action name
     * @return  SmartFormUtility 
     */
    public function setOndownload($ondownload)
    {
        $this->_ondownload = (string) $ondownload;
        return $this;
    }

    /**
     * Get name of action.
     *
     * @access  public
     * @return  string
     */
    public function getOnexport()
    {
        return $this->_onexport;
    }

    /**
     * Set action.
     *
     * Name of action (plugin-function) to execute on the event
     *
     * @access  public
     * @param   string  $onexport  form action name
     * @return  SmartFormUtility 
     */
    public function setOnexport($onexport)
    {
        $this->_onexport = (string) $onexport;
        return $this;
    }

    /**
     * Get index of selected layout.
     *
     * @access  public
     * @return  int
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Set layout.
     *
     * Where multiple layouts are available to present the result, this allows to choose the prefered one.
     *
     * @access  public
     * @param   int  $layout  positive number (default = 0)
     * @return  SmartFormUtility 
     */
    public function setLayout($layout)
    {
        $this->_layout = (int) $layout;
        return $this;
    }

    /**
     * Set bsae DDLForm.
     *
     * @access  protected
     * @param   DDLForm $form  base form definition
     * @return  SmartFormUtility 
     */
    protected function setForm(DDLForm $form, FormFacade $parentForm = null)
    {
        $this->_form = $form;
        $this->_facade->setBaseForm($this->_form);
        if ($this->_setupBuilder) {
            $this->_setupBuilder->setForm($this->_form);
        } else {
            $this->_setupBuilder = new FormSetupBuilder($this->_form);
        }
        if ($parentForm) {
            $this->_facade->setParent($parentForm);
        }
        return $this;
    }

    /**
     * Initialize instance
     *
     * @access  public
     * @param   string  $file  name of database to connect to
     */
    public function __construct($file)
    {
        $this->_file = (string) $file;
        $this->_database = Yana::connect($this->_file);
        $this->_schema = $this->_database->getSchema();
        $this->_facade = new FormFacade();
        $this->_queryBuilder = new FormWorker($this->_database, $this->_facade);
    }

    /**
     * <<magic>> Implements IsCloneable.
     *
     * Provides a shallow-copy (not a deep-copy as by default).
     *
     * @access  public
     * @ignore
     */
    public function __clone()
    {
        // nothing to do
    }

    /**
     * <<magic>> Invoke the function.
     *
     * @access  public
     * @return  FormFacade
     */
    public function __invoke()
    {
        $form = $this->_buildForm();

        $where = array();
        $formSetup = null;

        $cache = new FormSetupCacheManager();
        if (isset($cache->{$form->getName()})) {
            $formSetup = $cache->{$form->getName()};
        } else {
            $formSetup = $this->_buildSetup($form);
            $where = $this->getWhere();
        }
        $this->_setupBuilder->setSetup($formSetup);
        $this->_facade->setSetup($formSetup);

        // copy search term from parent-forms
        if ($this->_facade->getParent()) {
            $formSetup->setSearchTerm($this->_facade->getParent()->getSetup()->getSearchTerm());
        }

        $request = (array) Request::getVars($form->getName());
        $files = (array) Request::getFiles($form->getName());
        if (!empty($files)) {
            $request = Hashtable::merge($request, $files);
        }
        if (!empty($request)) {
            $this->_setupBuilder->updateSetup($request);
        }

        $this->_queryBuilder->setForm($this->_facade);

        $countQuery = $this->_queryBuilder->buildCountQuery();
        if (!empty($where)) {
            $countQuery->setWhere($where);
        }
        $formSetup->setEntryCount($countQuery->countResults());

        $selectQuery = $this->_queryBuilder->buildSelectQuery();
        if (!empty($where)) {
            $selectQuery->setWhere($where);
        }
        $selectQuery->setOffset($formSetup->getPage() * $formSetup->getEntriesPerPage());
        $values = $selectQuery->getResults();
        $this->_setupBuilder->setRows($values);
        $referenceValues = array();
        foreach ($formSetup->getForeignKeys() as $name => $reference)
        {
            $autocompleteQuery = $this->_queryBuilder->buildAutocompleteQuery($reference, "", 0);
            $referenceValues[$name] = $autocompleteQuery->getResults();
        }
        $formSetup->setReferenceValues($referenceValues);

        // This needs to be done after the rows have been set. Otherwise the user input would be overwritten.
        if ($request) {
            $this->_setupBuilder->updateValues($request);
        }

        $cache->{$form->getName()} = $this->_setupBuilder->__invoke(); // add to cache
        $this->_buildSubForms($this->_facade);

        return $this->_facade;
    }

    /**
     * Build DDLForm object.
     *
     * @access  private
     * @return  DDLForm
     * @throws  \BadMethodCallException    when a parameter is missing
     * @throws  \InvalidArgumentException  when a paraemter is not valid
     */
    private function _buildForm()
    {
        if (!isset($this->_form)) {
            // Either parameter 'id' or 'table' is required (not both). Parameter 'id' takes precedence.
            if ($this->getId()) {
                $id = $this->getId();
                if (!$this->_schema->isForm($id)) {
                    throw new \InvalidArgumentException("The form with name '" . $id . "' was not found.");
                }
                $this->_form = $this->_schema->getForm($id);
            } elseif ($this->getTable()) {
                    $table = $this->_schema->getTable($this->getTable());
                    if (! $table instanceof DDLTable) {
                        throw new \InvalidArgumentException("The table with name '" . $this->getTable() . "' was not found.");
                    }
                    $this->_form = $this->_buildFormFromTable($table);
            } else {
                throw new \BadMethodCallException("Missing either parameter 'id' or 'table'.");
            }
            $this->setForm($this->_form);
        }
        return $this->_form;
    }

    /**
     * Create form object from table definition.
     *
     * This function takes a table and initializes the form based on it's structure and columns.
     *
     * @access  protected
     * @return  DDLForm
     */
    protected function _buildFormFromTable(DDLTable $table)
    {
        $genericName = $this->_database->getName() . '-' . $table->getName();

        $form = new DDLForm($genericName); // from scratch
        $form->setTable($table->getName());

        $title = $table->getTitle();
        if (empty($title)) {
            $title = $table->getName(); // fall back to table name if title is empty
        }
        $form->setTitle($title);

        // copy security settings from table to form
        assert('!isset($grant); // Cannot redeclare var $grant');
        foreach ($table->getGrants() as $grant)
        {
            $form->setGrant($grant);
        }
        unset($grant);
        assert('!isset($column); // Cannot redeclare var $column');
        foreach ($table->getColumns() as $column)
        {
            $this->_addFieldByColumn($form, $column);
        }
        unset($column);
        $this->setForm($form);

        return $form;
    }

    /**
     * Add field by column definition.
     *
     * @access  private
     * @param   DDLForm    $form    form definition
     * @param   DDLColumn  $column  column definition
     */
    private function _addFieldByColumn(DDLForm $form, DDLColumn $column)
    {
        $field = null;
        try {
            $field = $form->addField($column->getName());
        } catch (AlreadyExistsException $e) {
            return; // field already exists - nothing to do!
        }

        // set the column title (aka "label")
        assert('!isset($title); // Cannot redeclare var $title');
        $title = $column->getTitle();
        if (!empty($title)) {
            $field->setTitle($title);
        } elseif ($column->isPrimaryKey()) {
            $field->setTitle("ID");
        } else {
            // fall back to column name if title is empty
            $field->setTitle($column->getName());
        }
        unset($title);

        // copy column grants to field
        assert('!isset($grant); // Cannot redeclare var $grant');
        foreach ($column->getGrants() as $grant)
        {
            $field->setGrant($grant);
        }
        unset($grant);
    }

    /**
     * Build DDLForm object.
     *
     * @access  private
     * @param   FormFacade  $form  parent form
     * @throws  \BadMethodCallException    when a parameter is missing
     * @throws  \InvalidArgumentException  when a paraemter is not valid
     */
    private function _buildSubForms(FormFacade $form)
    {
        $baseForm = $form->getBaseForm();
        foreach ($baseForm->getForms() as $subForm)
        {
            /* @var $builder FormBuilder */
            $builder = null;
            if (strcasecmp($subForm->getTable(), $baseForm->getTable()) === 0) {
                $builder = clone $this;
            } else {
                $builder = new FormBuilder($this->_file);
            }
            $builder->setForm($subForm, $form);
            // build sub-form
            $subFormFacade = $builder->__invoke();
            $form->addForm($subFormFacade);
        }
        return $form;
    }

    /**
     * Build FormSetup object.
     *
     * @access  private
     * @param   DDLForm  $form  base form
     * @return  FormSetup
     * @throws  NotFoundException  when a paraemter is not valid
     */
    private function _buildSetup(DDLForm $form)
    {
        $formSetup = new FormSetup();
        $formSetup->setPage($this->getPage());
        $formSetup->setEntriesPerPage($this->getEntries());
        $layout = $this->getLayout();
        if (!is_int($layout)) {
            $layout = $this->_form->getTemplate();
        }
        if (is_numeric($layout)) {
            $formSetup->setLayout((int) $layout);
        }
        $formSetup->setOrderByField($this->getSort());
        $formSetup->setSortOrder($this->isDescending());

        $show = $this->getShow();
        $hide = $this->getHide();
        $this->_selectColumns($show, $hide);

        $formSetup->setInsertAction($this->getOninsert());
        $formSetup->setUpdateAction($this->getOnupdate());
        $formSetup->setDeleteAction($this->getOndelete());
        $formSetup->setSearchAction($this->getOnsearch());
        $formSetup->setExportAction($this->getOnexport());
        $formSetup->setDownloadAction($this->getOndownload());
        return $formSetup;
    }

    /**
     * Select visible columns.
     *
     * This column list is used to auto-generate a whitelist of column names for the generated form.
     *
     * @access  private
     * @param   mixed  $showColumns  whitelist
     * @param   mixed  $hideColumns  blacklist
     * @return  SmartFormUtility 
     */
    private function _selectColumns($showColumns, $hideColumns)
    {
        $whitelist = array();
        $blacklist = array();
        if (!empty($showColumns) && !is_array($showColumns)) {
            $whitelist = explode(',', $showColumns);
        }
        if (!empty($hideColumns) && !is_array($hideColumns)) {
            $blacklist = explode(',', $hideColumns);
        }
        if (!empty($whitelist)) {
            $this->_setupBuilder->setColumnsWhitelist(array_diff($whitelist, $blacklist));
        }
        if (!empty($blacklist)) {
            $this->_setupBuilder->setColumnsBlacklist($blacklist);
        }
        return $this;
    }

}

?>