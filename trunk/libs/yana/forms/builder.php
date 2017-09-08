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

namespace Yana\Forms;

/**
 * <<command>> Form builder.
 *
 * This is a command class. It encapsulates parameters to be used to call a complex function.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
class Builder extends \Yana\Core\Object implements \Yana\Data\Adapters\IsCacheable
{

    /**
     * Cache adapter.
     *
     * @var \Yana\Data\Adapters\IsDataAdapter
     */
    private $_cache = null;

    /**
     * Database connection.
     *
     * @var  \Yana\Db\IsConnection
     */
    private $_database;

    /**
     * Database schema.
     *
     * @var  \Yana\Db\Ddl\Database
     */
    private $_schema;

    /**
     * Form facade.
     *
     * @var  \Yana\Forms\Facade
     */
    private $_facade;

    /**
     * Query builder class.
     *
     * @var  \Yana\Forms\Worker
     */
    private $_queryBuilder;

    /**
     * Included builder.
     *
     * @var  \Yana\Forms\Setups\Builder
     */
    private $_setupBuilder = null;

    /**
     * (mandatory) path and name of structure file
     *
     * @var  string
     */
    private $_file = "";

    /**
     * (optional) name of form to use (either $id or $table must be present!)
     *
     * @var  string
     */
    private $_id = "";

    /**
     * (optional) table to choose from structure file
     *
     * @var  string
     */
    private $_table = "";

    /**
     * (optional) list of columns, that should be shown in the form
     *
     * @var  string
     */
    private $_show = array();

    /**
     * (optional) list of columns, that should NOT be shown in the form
     *
     * @var  string
     */
    private $_hide = array();

    /**
     * (optional) sequence for SQL-where clause
     *
     * @var  string
     */
    private $_where = "";

    /**
     * (optional) name of column to sort entries by
     *
     * @var  string
     */
    private $_sort = "";

    /**
     * (optional) sort entries in descending (true) or ascending (false) order
     *
     * @var  bool
     */
    private $_desc = false;

    /**
     * (optional) number of 1st entry to show
     *
     * @var  string
     */
    private $_page = 0;

    /**
     * (optional) number of entries to show on each page
     *
     * @var  string
     */
    private $_entries = 20;

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @var  string
     */
    private $_oninsert = "";

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @var  string
     */
    private $_onupdate = "";

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @var  string
     */
    private $_ondelete = "";

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @var  string
     */
    private $_onsearch = "";

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @var  string
     */
    private $_ondownload = "download_file";

    /**
     * (optional) name of action (plugin-function) to execute on the event
     *
     * @var  string
     */
    private $_onexport = "";

    /**
     * where multiple layouts are available to present the result, this allows to choose the prefered one
     *
     * @var  int
     */
    private $_layout = null;

    /**
     * base form
     *
     * @var  \Yana\Db\Ddl\Form
     */
    private $_form = null;

    /**
     * Get setup-builder.
     *
     * @return  \Yana\Forms\Setups\Builder
     */
    protected function _getSetupBuilder()
    {
        return $this->_setupBuilder;
    }

    /**
     * Get query-builder.
     *
     * @return  \Yana\Forms\Worker
     */
    protected function _getQueryBuilder()
    {
        return $this->_queryBuilder;
    }

    /**
     * Get name of database file.
     *
     * @return  string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Get id of form.
     *
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set name of form to use.
     *
     * If you wish to extract a sub-form, give the full path separate the names with a dot.
     * Example: "form.subform".
     *
     * @param   string  $id  valid form name
     * @return  \Yana\Forms\Builder
     */
    public function setId($id)
    {
        assert('is_string($id); // Invalid argument $id: String expected');
        $this->_id = (string) $id;
        return $this;
    }

    /**
     * Get name of table.
     *
     * @return  string
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Set table to choose from database.
     *
     * @param   string  $table  valid table name
     * @return  \Yana\Forms\Builder 
     */
    public function setTable($table)
    {
        assert('is_string($table); // Invalid argument $table: String expected');
        $this->_table = (string) $table;
        return $this;
    }

    /**
     * Get white-listed column names.
     *
     * @return  array
     */
    public function getShow()
    {
        return $this->_show;
    }

    /**
     * Set list of columns, that should be shown in the form.
     *
     * @param   array  $show  white-listed column names.
     * @return  \Yana\Forms\Builder 
     */
    public function setShow(array $show)
    {
        $this->_show = $show;
        return $this;
    }

    /**
     * Get black-listed column names.
     *
     * @return  array
     */
    public function getHide()
    {
        return $this->_hide;
    }

    /**
     * Set list of columns, that should NOT be shown in the form.
     *
     * @param   array  $hide  black-listed column names.
     * @return  \Yana\Forms\Builder 
     */
    public function setHide(array $hide)
    {
        $this->_hide = $hide;
        return $this;
    }

    /**
     * Get where clause.
     *
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
     * @param   array  $where  valid where clause
     * @return  \Yana\Forms\Builder
     * @see     \Yana\Db\Queries\SelectExist::setWhere()
     */
    public function setWhere(array $where)
    {
        $this->_where = $where;
        return $this;
    }

    /**
     * Get name of column to sort by.
     *
     * @return  string
     */
    public function getSort()
    {
        return $this->_sort;
    }

    /**
     * Set name of column to sort entries by.
     *
     * @param   string  $sort  valid column name
     * @return  \Yana\Forms\Builder 
     */
    public function setSort($sort)
    {
        assert('is_string($sort); // Invalid argument $sort: String expected');
        $this->_sort = (string) $sort;
        return $this;
    }

    /**
     * Check if contents are sorted descending order.
     *
     * @return  bool
     */
    public function isDescending()
    {
        return $this->_desc;
    }

    /**
     * Set sorting order for entries.
     *
     * @param   bool  $desc  true = descending, false = ascending
     * @return  \Yana\Forms\Builder 
     */
    public function setDescending($desc)
    {
        assert('is_scalar($desc); // Invalid argument $desc: Scalar expected');
        $this->_desc = (bool) $desc;
        return $this;
    }

    /**
     * Get number of 1st page to show.
     *
     * @return  int
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * Set number of 1st page to show.
     *
     * @param   int  $page  positive number (default = 0)
     * @return  \Yana\Forms\Builder 
     */
    public function setPage($page)
    {
        assert('is_numeric($page); // Invalid argument $page: Number expected');
        $this->_page = (int) $page;
        return $this;
    }

    /**
     * Get number of entries to view per page.
     *
     * @return  int
     */
    public function getEntries()
    {
        return $this->_entries;
    }

    /**
     * Set number of entries to view per page.
     *
     * @param   int  $entries  positive number (default = 20)
     * @return  \Yana\Forms\Builder 
     */
    public function setEntries($entries)
    {
        assert('is_numeric($entries); // Invalid argument $entries: Number expected');
        $this->_entries = (int) $entries;
        return $this;
    }

    /**
     * Get name of action.
     *
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
     * @param   string  $oninsert  form action name
     * @return  \Yana\Forms\Builder 
     */
    public function setOninsert($oninsert)
    {
        assert('is_string($oninsert); // Invalid argument $oninsert: String expected');
        $this->_oninsert = (string) $oninsert;
        return $this;
    }

    /**
     * Get name of action.
     *
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
     * @param   string  $onupdate  form action name
     * @return  \Yana\Forms\Builder 
     */
    public function setOnupdate($onupdate)
    {
        assert('is_string($onupdate); // Invalid argument $onupdate: String expected');
        $this->_onupdate = (string) $onupdate;
        return $this;
    }

    /**
     * Get name of action.
     *
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
     * @param   string  $ondownload  form action name
     * @return  \Yana\Forms\Builder 
     */
    public function setOndelete($ondelete)
    {
        assert('is_string($ondelete); // Invalid argument $ondelete: String expected');
        $this->_ondelete = (string) $ondelete;
        return $this;
    }

    /**
     * Get name of action.
     *
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
     * @param   string  $ondownload  form action name
     * @return  \Yana\Forms\Builder 
     */
    public function setOnsearch($onsearch)
    {
        assert('is_string($onsearch); // Invalid argument $onsearch: String expected');
        $this->_onsearch = (string) $onsearch;
        return $this;
    }

    /**
     * Get name of action.
     *
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
     * @param   string  $ondownload  form action name
     * @return  \Yana\Forms\Builder 
     */
    public function setOndownload($ondownload)
    {
        assert('is_string($ondownload); // Invalid argument $ondownload: String expected');
        $this->_ondownload = (string) $ondownload;
        return $this;
    }

    /**
     * Get name of action.
     *
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
     * @param   string  $onexport  form action name
     * @return  \Yana\Forms\Builder 
     */
    public function setOnexport($onexport)
    {
        assert('is_string($onexport); // Invalid argument $onexport: String expected');
        $this->_onexport = (string) $onexport;
        return $this;
    }

    /**
     * Get index of selected layout.
     *
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
     * @param   int  $layout  positive number (default = 0)
     * @return  \Yana\Forms\Builder 
     */
    public function setLayout($layout)
    {
        assert('is_numeric($layout); // Invalid argument $layout: Integer expected');
        $this->_layout = (int) $layout;
        return $this;
    }

    /**
     * Set bsae \Yana\Db\Ddl\Form.
     *
     * @param   \Yana\Db\Ddl\Form $form  base form definition
     * @return  \Yana\Forms\Builder 
     */
    protected function setForm(\Yana\Db\Ddl\Form $form, \Yana\Forms\Facade $parentForm = null)
    {
        $this->_form = $form;
        $this->_facade->setBaseForm($this->_form);
        if ($this->_setupBuilder) {
            $this->_setupBuilder->setForm($this->_form);
        } else {
            $this->_setupBuilder = new \Yana\Forms\Setups\Builder($this->_form);
        }
        if ($parentForm) {
            $this->_facade->setParent($parentForm);
        }
        return $this;
    }

    /**
     * Initialize instance.
     *
     * @param  string  $file  name of database to connect to
     */
    public function __construct($file)
    {
        assert('is_string($file); // Invalid argument $file: String expected');
        $this->_cache = new \Yana\Data\Adapters\SessionAdapter(__CLASS__);
        $builder = new \Yana\ApplicationBuilder();
        $this->_file = (string) $file;
        $this->_database = $builder->buildApplication()->connect($this->_file);
        $this->_schema = $this->_database->getSchema();
        $this->_facade = new \Yana\Forms\Facade();
        $this->_queryBuilder = new \Yana\Forms\Worker($this->_database, $this->_facade);
    }

    /**
     * Register a cache adapter.
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $cache  a valid cache adapter
     * @return  \Yana\Forms\Builder
     */
    public function setCache(\Yana\Data\Adapters\IsDataAdapter $cache)
    {
        $this->_cache = $cache;
        return $this;
    }

    /**
     * Returns the cache adapter.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    protected function _getCache()
    {
        return $this->_cache;
    }

    /**
     * <<magic>> Implements IsCloneable.
     *
     * Provides a shallow-copy (not a deep-copy as by default).
     *
     * @ignore
     */
    public function __clone()
    {
        // nothing to do
    }

    /**
     * <<magic>> Invoke the function.
     *
     * @return  \Yana\Forms\Facade
     */
    public function __invoke()
    {
        $form = $this->_buildForm();
        $formSetup = null;

        $cache = $this->_getCache();
        if (isset($cache[$form->getName()])) {
            $formSetup = $cache[$form->getName()];
        } else {
            $formSetup = $this->_buildSetup($form);
        }
        $this->_setupBuilder->setSetup($formSetup);
        $this->_facade->setSetup($formSetup);

        // copy search term from parent-forms
        if ($this->_facade->getParent()) {
            $formSetup->setSearchTerm($this->_facade->getParent()->getSetup()->getSearchTerm());
        }

        $request = (array) \Yana\Http\Requests\Builder::buildFromSuperGlobals()->all()->value($form->getName())->all()->asArrayOfStrings();
        $files = (array) \Yana\Http\Uploads\Builder::buildFromSuperGlobals()->all($form->getName());
        if (!empty($files)) {
            $request = \Yana\Util\Hashtable::merge($request, $files);
        }
        unset($files);
        if (!empty($request)) {
            $this->_setupBuilder->updateSetup($request);
        }

        $this->_queryBuilder->setForm($this->_facade);

        $countQuery = $this->_queryBuilder->buildCountQuery();
        $where = $this->getWhere();
        if (!empty($where)) {
            $countQuery->addWhere($where);
        }
        $formSetup->setEntryCount($countQuery->countResults());

        $selectQuery = $this->_queryBuilder->buildSelectQuery();
        if (!empty($where)) {
            $selectQuery->addWhere($where);
        }
        $selectQuery->setOffset($formSetup->getPage() * $formSetup->getEntriesPerPage());
        $values = $selectQuery->getResults();
        $this->_setupBuilder->setRows($values);
        $referenceValues = array();
        foreach ($formSetup->getForeignKeys() as $name => $reference)
        {
            $referenceValues[$name] = $this->_queryBuilder->autocomplete($name,  "", 0);
        }
        $formSetup->setReferenceValues($referenceValues);

        // This needs to be done after the rows have been set. Otherwise the user input would be overwritten.
        if ($request) {
            $this->_setupBuilder->updateValues($request);
        }

        $cache[$form->getName()] = $this->_setupBuilder->__invoke(); // add to cache
        $this->_buildSubForms($this->_facade);

        return $this->_facade;
    }

    /**
     * Build \Yana\Db\Ddl\Form object.
     *
     * @return  \Yana\Db\Ddl\Form
     * @throws  \Yana\Core\Exceptions\BadMethodCallException    when a parameter is missing
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when a paraemter is not valid
     */
    private function _buildForm()
    {
        if (!isset($this->_form)) {
            // Either parameter 'id' or 'table' is required (not both). Parameter 'id' takes precedence.
            if ($this->getId()) {
                $ids = $this->getId();
                $form = $this->_schema;
                foreach (explode('.', $ids) as $id)
                {
                    if (!$form->isForm($id)) {
                        $message = "The form with name '" . $ids . "' was not found.";
                        throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
                    }
                    $form = $form->getForm($id);
                }
                $this->_form = $form;
            } elseif ($this->getTable()) {
                    $table = $this->_schema->getTable($this->getTable());
                    if (! $table instanceof \Yana\Db\Ddl\Table) {
                        $message = "The table with name '" . $this->getTable() . "' was not found.";
                        throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
                    }
                    $this->_form = $this->_buildFormFromTable($table);
            } else {
                throw new \Yana\Core\Exceptions\BadMethodCallException("Missing either parameter 'id' or 'table'.");
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
     * @return  \Yana\Db\Ddl\Form
     */
    protected function _buildFormFromTable(\Yana\Db\Ddl\Table $table)
    {
        $genericName = $this->_database->getName() . '-' . $table->getName();

        $form = new \Yana\Db\Ddl\Form($genericName, $this->_schema); // from scratch
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
     * @param   \Yana\Db\Ddl\Form    $form    form definition
     * @param   \Yana\Db\Ddl\Column  $column  column definition
     */
    private function _addFieldByColumn(\Yana\Db\Ddl\Form $form, \Yana\Db\Ddl\Column $column)
    {
        $field = null;
        try {
            $field = $form->addField($column->getName());
        } catch (\Yana\Core\Exceptions\AlreadyExistsException $e) {
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
     * Build \Yana\Db\Ddl\Form object.
     *
     * @param   \Yana\Forms\Facade  $form  parent form
     * @throws  \Yana\Core\Exceptions\BadMethodCallException    when a parameter is missing
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when a paraemter is not valid
     */
    private function _buildSubForms(\Yana\Forms\Facade $form)
    {
        $baseForm = $form->getBaseForm();
        foreach ($baseForm->getForms() as $subForm)
        {
            /* @var $builder FormBuilder */
            $builder = null;
            if (strcasecmp($subForm->getTable(), $baseForm->getTable()) === 0) {
                $builder = clone $this;
            } else {
                $builder = new \Yana\Forms\Builder($this->_file);
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
     * @param   \Yana\Db\Ddl\Form  $form  base form
     * @return  \Yana\Forms\IsSetup
     * @throws  \Yana\Core\Exceptions\NotFoundException  when a paraemter is not valid
     */
    private function _buildSetup(\Yana\Db\Ddl\Form $form)
    {
        $formSetup = new \Yana\Forms\Setup();
        $formSetup->setPage($this->getPage());
        $formSetup->setEntriesPerPage($this->getEntries());
        $layout = $this->getLayout();
        if (!is_int($layout)) {
            $layout = $form->getTemplate();
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
     * @param   mixed  $showColumns  whitelist
     * @param   mixed  $hideColumns  blacklist
     * @return  \Yana\Forms\Builder 
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