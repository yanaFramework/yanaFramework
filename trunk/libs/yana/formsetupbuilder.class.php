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
class FormSetupBuilder extends Object
{

    /**
     * Builder product.
     *
     * @access  protected
     * @var     FormSetup
     */
    protected $object = null;

    /**
     * DDL definition object of selected table
     *
     * @access  private
     * @var     DDLTable
     */
    private $_table = null;

    /**
     * DDL definition object of selected table
     *
     * @access  private
     * @var     DDLForm
     */
    private $_form = null;

    /**
     * Whitelist of column names.
     *
     * @access  private
     * @var     array
     */
    private $_whitelistColumnNames = array();

    /**
     * Blacklist of column names.
     *
     * @access  private
     * @var     array
     */
    private $_blacklistColumnNames = array();

    /**
     * Initialize instance.
     *
     * @access  public
     * @param   DDLForm  $form  base form defintion that the setup will apply to
     */
    public function __construct(DDLForm $form)
    {
        $this->_form = $form;
        $this->object = new FormSetup();
    }

    /**
     * Overwrite existing setup.
     *
     * Set your own predefined setup, to modify it.
     *
     * @access  public
     * @param   FormSetup  $setup  basic setup to modify
     */
    public function setSetup(FormSetup $setup)
    {
        $this->object = $setup;
    }

    /**
     * Build facade object.
     * 
     * @access  public
     * @return  FormSetup
     */
    public function __invoke()
    {
        $this->_buildActions()->_buildSetupContext();
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
        return $this->_form;
    }

    /**
     * Set form object.
     *
     * @access  public
     * @param   DDLForm  $form  configuring the contents of the form
     * @return  FormSetupBuilder
     */
    public function setForm(DDLForm $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Update setup with request array.
     *
     * @access  public
     * @param   array  $request  initial values (e.g. Request array)
     * @return  FormSetupBuilder
     */
    public function updateSetup(array $request = array())
    {
        $setup = $this->object;
        if (isset($request['page']) && $request['page'] >= 0) {
            $setup->setPage((int) $request['page']);
        }
        if (isset($request['entries']) && $request['entries'] > 0) {
            $setup->setEntriesPerPage((int) $request['entries']);
        }
        if (isset($request['layout']) && $request['layout'] >= 0) {
            $setup->setLayout((int) $request['layout']);
        }
        if (isset($request['searchterm'])) {
            $setup->setSearchTerm($request['searchterm']);
        }
        if (!empty($request['dropfilter'])) {
            $setup->setFilters();
        }
        if (isset($request['filter']) && is_array($request['filter'])) {
            foreach ($request['filter'] as $columnName => $searchTerm)
            {
                $setup->setFilter($columnName, $searchTerm);
            }
        }
        if (!empty($request['sort'])) {
            $setup->setOrderByField($request['sort']);
        }
        if (!empty($request['orderby'])) {
            $setup->setOrderByField($request['orderby']);
        }
        if (!empty($request['desc'])) {
            $setup->setSortOrder(true);
        }
        return $this;
    }

    /**
     * Update values with request array.
     *
     * @access  public
     * @param   array  $request  initial values (e.g. Request array)
     * @return  FormSetupBuilder
     */
    public function updateValues(array $request = array())
    {
        $setup = $this->object;
        foreach (array('insert', 'search') as $name)
        {
            $context = $setup->getContext($name);
            if (isset($request[$name]) && is_array($request[$name])) {
                $context->addValues($request[$name]);
            }
        }
        $context = $setup->getContext('update');
        if (isset($request['update']) && is_array($request['update'])) {
            foreach ($request['update'] as $key => $row)
            {
                if (is_array($row)) {
                    $context->updateRow($key, $row);
                }
            }
        }
        return $this;
    }

    /**
     * Overwrite row values.
     *
     * @access  public
     * @param   array  $rows  initial values
     * @return  FormSetupBuilder
     */
    public function setRows(array $rows = array())
    {
        $this->object->getContext('update')->setRows($rows);
        $this->_buildFooter();
        return $this;
    }

    /**
     * Create links to other pages.
     *
     * @access  protected
     * @return  FormSetupBuilder
     */
    private function _buildFooter()
    {
        $context = $this->object->getContext('update');
        $entriesPerPage = $this->object->getEntriesPerPage();
        $pageCount = $this->object->getPageCount();
        $lastPage = $pageCount - 1;
        $entryCount = $this->object->getEntryCount();

        assert('$entriesPerPage > 0; // invalid number of entries to view per page');
        $currentPage = $this->object->getPage();
        $listOfEntries = "";
        assert('!isset($pluginManager); // Cannot redeclare var $pluginManager');
        $pluginManager = PluginManager::getInstance();
        $action = $pluginManager->getFirstEvent();
        $lang = Language::getInstance();
        $linkTemplate = '<a class="gui_generator_%s" href=' .
            SmartUtility::href("action=$action&" . $this->getForm()->getName() . "[page]=%s") .
            ' title="%s">%s</a>';
        // previous page
        if ($currentPage > 0) { // is not first page
            $page = $currentPage - 1;
            if ($page < 0) {
                $page = 0;
            }
            $listOfEntries .= sprintf($linkTemplate, 'previous', $page,
                $lang->getVar("TITLE_PREVIOUS"), $lang->getVar("BUTTON_PREVIOUS"));
        }
        // more pages
        if ($pageCount > 2) { // has more than 2 pages

            $dots = false;

            $title = $lang->getVar("TITLE_LIST");
            $isTooLong = $pageCount > 10; // has more than 10 pages

            for ($page = 0; $page < $pageCount; $page++)
            {
                /**
                 * if more than 10 pages exist and current page is not first page or last page
                 * and is not current page or previous or next 3 pages
                 */
                // previous 3, or next 3 pages
                $isNearCurrent = $currentPage - 3 < $page && $page < $currentPage + 3;

                // is first page, or last page
                $isFirstOrLast = $page <= 1 || $page >= $lastPage;

                if ($isTooLong && !$isFirstOrLast && !$isNearCurrent) {
                    /* this marks an elipsis */
                    if ($dots === false) {
                        $listOfEntries .= "...";
                        $dots = true;
                    } else {
                        /* ignore this page */
                        continue;
                    }
                } else {
                    $first = ($page * $entriesPerPage);
                    $last = $first + $entriesPerPage;
                    if ($last > $entryCount) {
                        $last = $entryCount;
                    }
                    $text = '';
                    // link text
                    if ($first + 1 != $last) {
                        $text = '[' . ($first + 1) . '-' . $last . ']';
                    } else {
                        $text = '[' . $last . ']';
                    }
                    if ($currentPage != $page) { // is not current page
                        $listOfEntries .= sprintf($linkTemplate, 'page', $page, $title, $text);
                    } else {
                        $listOfEntries .= $text;
                    }
                    if ($isNearCurrent) {
                        $dots = false;
                    }
                }
            } // end for
        }
        // next page
        if ($currentPage < $lastPage) { // is not last page
            $page = $currentPage + 1;
            $listOfEntries .= sprintf($linkTemplate, 'next', $page,
                $lang->getVar("TITLE_NEXT"), $lang->getVar("BUTTON_NEXT"));
        }

        $context->setFooter($listOfEntries);

        return $this;
    }

    /**
     * Scans the actions and removes those to whom the current user has no access.
     *
     * @access  private
     * @return  FormSetupBuilder
     */
    private function _buildActions()
    {
        $form = $this->getForm();
        $setup = $this->object;

        $searchAction = "";
        $exportAction = "";
        $downloadAction = "";
        if ($form->isSelectable()) {
            $searchAction = $this->_resolveAction('search');
            $exportAction = $this->_resolveAction('export');
            $downloadAction = $this->_resolveAction('download');
            if (empty($downloadAction)) {
                if (SessionManager::getInstance()->checkPermission(null, "download_file")) {
                    $downloadAction = "download_file";
                }
            }
        }
        $setup->setDownloadAction($downloadAction);
        $setup->setSearchAction($searchAction);
        $setup->setExportAction($exportAction);

        $action = "";
        if ($form->isInsertable()) {
            $action = $this->_resolveAction('insert');
        }
        $setup->setInsertAction($action);

        $action = "";
        if ($form->isUpdatable()) {
            $action = $this->_resolveAction('update');
        }
        $setup->setUpdateAction($action);

        $action = "";
        if ($form->isDeletable()) {
            $action = $this->_resolveAction('delete');
        }
        $setup->setDeleteAction($action);
        return $this;
    }

    /**
     * Get the handler-function name for the defined form-action.
     *
     * @access  private
     * @param   string  $name  'download', 'insert', 'update', 'delete', 'export'
     * @return  string 
     */
    private function _resolveAction($name)
    {
        $function = "get{$name}Action";
        $action = $this->object->$function();
        if (empty($action)) {
            $event = $this->getForm()->getEvent($name);
            if ($event instanceof DDLEvent) {
                $action = $event->getAction();
            }
        }
        if (!empty($action) && !SessionManager::getInstance()->checkPermission(null, $action)) {
            $action = "";
        }
        return $action;
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
     * Build the default setup contexts.
     *
     * This creates the contexts for: search, read, insert and update scenarios
     * and selects the visible columns for these contexts based on the table definition
     * and form settings.
     *
     * @access  private
     * @return  FormSetupBuilder
     */
    private function _buildSetupContext()
    {
        $form = $this->getForm();
        $table = $this->_getTable();
        $readCollection = new DDLColumnCollection();
        $updateCollection = new DDLColumnCollection();
        $insertCollection = new DDLColumnCollection();
        $searchCollection = new DDLColumnCollection();
        if ($form->hasAllInput()) {
            $columnNames = $table->getColumnNames();
        } else {
            $columnNames = array_keys($form->getFields());
        }
        /** @var $column DDLColumn */
        foreach ($columnNames as $columnName)
        {
            if ($form->isField($columnName)) {
                $field = $form->getField($columnName);
            } else {
                $field = new DDLField($columnName);
            }
            $column = $field->getColumn();
            if (!$column instanceof DDLColumn) {
                if ($table->isColumn($columnName)) {
                    $column = $table->getColumn($columnName);
                } else {
                    continue;
                }
            }
            if ($field->isVisible() && $field->isSelectable()) {
                $readCollection[$columnName] = $column;
                // filter fields by column type
                switch ($column->getType())
                {
                    case 'bool':
                    case 'date':
                    case 'enum':
                    case 'float':
                    case 'html':
                    case 'inet':
                    case 'integer':
                    case 'list':
                    case 'mail':
                    case 'range':
                    case 'set':
                    case 'string':
                    case 'text':
                    case 'time':
                    case 'timestamp':
                    case 'url':
                        $searchCollection[$columnName] = $column;
                        break;
                } // end switch
                if (!$table->isReadonly() && !$column->isReadonly() && !$field->isReadonly()) {
                    if ($field->isInsertable()) {
                        $insertCollection[$columnName] = $column;
                    }
                    if ($column->isUpdatable() && $field->isUpdatable()) {
                        $updateCollection = new DDLColumnCollection();
                    }
                }
            }
        }
        $this->object->getContext('read')->setColumnNames(array_keys($readCollection->toArray()));
        $this->object->getContext('update')->setColumnNames(array_keys($readCollection->toArray()));
        $this->object->getContext('insert')->setColumnNames(array_keys($insertCollection->toArray()));
        $this->object->getContext('search')->setColumnNames(array_keys($searchCollection->toArray()));
        $this->_applyWhitelistColumnNames();
        return $this;
    }

    /**
     * Select visible columns.
     *
     * Limits the visible columns to entries of this list.
     *
     * @access  public
     * @param   array  $columnNames  whitelist
     * @return  FormSetupBuilder
     */
    public function setColumnsWhitelist(array $columnNames)
    {
        $this->_whitelistColumnNames = $columnNames;
        $this->_applyWhitelistColumnNames();
        return $this;
    }

    /**
     * Select hidden columns.
     *
     * Limits the visible columns to entries not on this list.
     *
     * @access  public
     * @param   array  $columnNames  whitelist
     * @return  FormSetupBuilder
     */
    public function setColumnsBlacklist(array $columnNames)
    {
        $this->_blacklistColumnNames = $columnNames;
        $this->_applyWhitelistColumnNames();
        return $this;
    }

    /**
     * Apply selected whitelist of column names, if there is any.
     *
     * This function filters out all columns not apparent in the whitelist on all contexts.
     *
     * @access  private
     * @return  FormSetupBuilder
     */
    private function _applyWhitelistColumnNames()
    {
        foreach ($this->object->getContexts() as $context)
        {
            $columns = $context->getColumnNames();
            if (!empty($this->_whitelistColumnNames)) {
                if (!empty($columns)) {
                    $columns = array_intersect($columns, $this->_whitelistColumnNames);
                } else {
                    $columns = $this->_whitelistColumnNames;
                }
            }
            if (!empty($this->_blacklistColumnNames)) {
                $columns = array_diff($columns, $this->_blacklistColumnNames);
            }
            $context->setColumnNames($columns);
        }
        return $this;
    }
}

?>