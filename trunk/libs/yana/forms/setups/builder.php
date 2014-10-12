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

namespace Yana\Forms\Setups;

/**
 * <<builder>> Build a form using a form object and settings.
 *
 * @access      public
 * @package     yana
 * @subpackage  form
 */
class Builder extends \Yana\Core\Object
{

    /**
     * Builder product.
     *
     * @access  protected
     * @var     \Yana\Forms\Setup
     */
    protected $object = null;

    /**
     * DDL definition object of selected table
     *
     * @access  private
     * @var     \Yana\Db\Ddl\Table
     */
    private $_table = null;

    /**
     * DDL definition object of selected table
     *
     * @access  private
     * @var     \Yana\Db\Ddl\Form
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
     * @param   \Yana\Db\Ddl\Form  $form  base form defintion that the setup will apply to
     */
    public function __construct(\Yana\Db\Ddl\Form $form)
    {
        $this->_form = $form;
        $this->object = new \Yana\Forms\Setup();
    }

    /**
     * Overwrite existing setup.
     *
     * Set your own predefined setup, to modify it.
     *
     * @access  public
     * @param   \Yana\Forms\Setup  $setup  basic setup to modify
     */
    public function setSetup(\Yana\Forms\Setup $setup)
    {
        $this->object = $setup;
    }

    /**
     * Build facade object.
     * 
     * @access  public
     * @return  \Yana\Forms\Setup
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
     * @return  \Yana\Db\Ddl\Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Set form object.
     *
     * @access  public
     * @param   \Yana\Db\Ddl\Form  $form  configuring the contents of the form
     * @return  \Yana\Forms\Setups\Builder
     */
    public function setForm(\Yana\Db\Ddl\Form $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Update setup with request array.
     *
     * @access  public
     * @param   array  $request  initial values (e.g. Request array)
     * @return  \Yana\Forms\Setups\Builder
     */
    public function updateSetup(array $request = array())
    {
        if (isset($request['page']) && $request['page'] >= 0) {
            $this->object->setPage((int) $request['page']);
        }
        if (isset($request['entries']) && $request['entries'] > 0) {
            $this->object->setEntriesPerPage((int) $request['entries']);
        }
        if (isset($request['layout']) && $request['layout'] >= 0) {
            $this->object->setLayout((int) $request['layout']);
        }
        if (isset($request['searchterm'])) {
            $this->object->setSearchTerm($request['searchterm']);
        }
        if (!empty($request['dropfilter'])) {
            $this->object->setFilters();
        }
        if (isset($request['filter']) && is_array($request['filter'])) {
            foreach ($request['filter'] as $columnName => $searchTerm)
            {
                $this->object->setFilter($columnName, $searchTerm);
            }
        }
        if (!empty($request['sort'])) {
            $this->object->setOrderByField($request['sort']);
        }
        if (!empty($request['orderby'])) {
            $this->object->setOrderByField($request['orderby']);
        }
        if (!empty($request['desc'])) {
            $this->object->setSortOrder(true);
        }
        return $this;
    }

    /**
     * Update values with request array.
     *
     * @access  public
     * @param   array  $request  initial values (e.g. Request array)
     * @return  \Yana\Forms\Setups\Builder
     */
    public function updateValues(array $request = array())
    {
        $setup = $this->object;

        $contextNames = array();
        if ($setup->getInsertAction()) {
            $contextNames[] = 'insert';
        }
        if ($setup->getSearchAction()) {
            $contextNames[] = 'search';
        }
        if ($setup->getUpdateAction()) {
            $contextNames[] = 'update';
        }

        foreach ($contextNames as $name)
        {
            if (isset($request[$name]) && is_array($request[$name])) {
                $context = $setup->getContext($name);
                if ($name == 'update') {
                    $columnNames = array_flip($setup->getContext('editable')->getColumnNames());
                    foreach ($request[$name] as $key => $row)
                    {
                        if (is_array($row)) {
                            // security check: allow only fields, that exist in the form
                            $row = array_intersect_key($row, $columnNames);
                            $context->updateRow($key, $row);
                        }
                    }
                } else {
                    $columnNames = array_flip($context->getColumnNames());
                    // security check: allow only fields, that exist in the form
                    $values = array_intersect_key($request[$name], $columnNames);
                    $context->setValues($values);
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
     * @return  \Yana\Forms\Setups\Builder
     */
    public function setRows(array $rows = array())
    {
        $this->object->getContext('update')->setRows($rows);
        $this->_buildHeader();
        $this->_buildFooter();
        return $this;
    }

    /**
     * Create info on visible entries.
     *
     * @access  protected
     * @return  \Yana\Forms\Setups\Builder
     */
    private function _buildHeader()
    {
        $entriesPerPage = $this->object->getEntriesPerPage();
        $firstPage = ($this->object->getPage() * $entriesPerPage) + 1;
        $offsetPage = $firstPage + $entriesPerPage - 1;
        $lastPage = $this->object->getEntryCount();
        if ($offsetPage > $lastPage) {
            $offsetPage = $lastPage;
        }
        $params = array(
            'FIRST_PAGE' => $firstPage,
            'OFFSET_PAGE' => $offsetPage,
            'LAST_PAGE' => $lastPage
        );
        $lang = \Yana\Translations\Language::getInstance();
        $header = $lang->getVar("DESCR_SHOW");
        $header = \Yana\Util\String::replaceToken($header, $params);
        $this->object->getContext('update')->setHeader($header);
        return $this;
    }

    /**
     * Create links to other pages.
     *
     * @access  protected
     * @return  \Yana\Forms\Setups\Builder
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
        $pluginManager = \Yana\Plugins\Manager::getInstance();
        $action = $pluginManager->getFirstEvent();
        $lang = \Yana\Translations\Language::getInstance();
        $formatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
        $linkTemplate = '<a class="gui_generator_%s" href="' .
            $formatter("action=$action&" . $this->getForm()->getName() . "[page]=%s", false, false) .
            '" title="%s">%s</a>';
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
     * @return  \Yana\Forms\Setups\Builder
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
                if (\Yana\Security\Users\SessionManager::getInstance()->checkPermission(null, "download_file")) {
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
     * @param   string  $name  'download', 'insert', 'update', 'delete', 'export'
     * @return  string 
     */
    private function _resolveAction($name)
    {
        $function = "get{$name}Action";
        $action = $this->object->$function();
        if (empty($action)) {
            $event = $this->getForm()->getEvent($name);
            if ($event instanceof \Yana\Db\Ddl\Event) {
                $action = $event->getAction();
            }
        }
        if (!empty($action) && !\Yana\Security\Users\SessionManager::getInstance()->checkPermission(null, $action)) {
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
     * @return  \Yana\Db\Ddl\Table
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the database, or table was not found
     */
    protected function _getTable()
    {
        if (!isset($this->_table)) {
            $form = $this->getForm();
            $name = $form->getTable();
            $database = $form->getDatabase();
            if (!($database instanceof \Yana\Db\Ddl\Database)) {
                $message = "Error in form '" . $form->getName() . "'. No parent database defined.";
                throw new \Yana\Core\Exceptions\NotFoundException($message);
            }
            $tableDefinition = $database->getTable($name);
            if (!($tableDefinition instanceof \Yana\Db\Ddl\Table)) {
                $message = "Error in form '" . $form->getName() . "'. Parent table '$name' not found.";
                throw new \Yana\Core\Exceptions\NotFoundException($message);
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
     * @return  \Yana\Forms\Setups\Builder
     */
    private function _buildSetupContext()
    {
        $form = $this->getForm();
        $table = $this->_getTable();
        $readCollection = new \Yana\Db\Ddl\ColumnCollection();
        $updateCollection = new \Yana\Db\Ddl\ColumnCollection();
        $insertCollection = new \Yana\Db\Ddl\ColumnCollection();
        $searchCollection = new \Yana\Db\Ddl\ColumnCollection();
        if ($form->hasAllInput()) {
            $columnNames = $table->getColumnNames();
        } else {
            $columnNames = array_keys($form->getFields());
        }
        /** @var $column \Yana\Db\Ddl\Column */
        foreach ($columnNames as $columnName)
        {
            if ($form->isField($columnName)) {
                $field = $form->getField($columnName);
            } else {
                $field = new \Yana\Db\Ddl\Field($columnName);
            }
            $column = $field->getColumn();
            if (!$column instanceof \Yana\Db\Ddl\Column) {
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
                if (!$table->isReadonly()) {
                    if ($field->isInsertable()) {
                        $insertCollection[$columnName] = $column;
                    }
                    if ($column->isUpdatable() && $field->isUpdatable()) {
                        $updateCollection[$columnName] = $column;
                    }
                }
            }
        }
        $this->object->getContext('editable')->setColumnNames(array_keys($updateCollection->toArray()));
        $this->object->getContext('update')->setColumnNames(array_keys($readCollection->toArray()));
        $this->object->getContext('insert')->setColumnNames(array_keys($insertCollection->toArray()));
        $this->object->getContext('search')->setColumnNames(array_keys($searchCollection->toArray()));
        $this->_applyWhitelistColumnNames();
        $this->_buildForeignKeyReferences($readCollection);
        return $this;
    }

    /**
     * This returns an array of foreign-key reference settings.
     *
     * @return  \Yana\Db\Ddl\Reference[]
     */
    private function _buildForeignKeyReferences(\Yana\Db\Ddl\ColumnCollection $collection)
    {
        /* @var $column \Yana\Db\Ddl\Column */
        foreach ($collection as $columnName => $column)
        {
            if ($column->getType() !== 'reference') {
                continue;
            }
            $reference = $column->getReferenceSettings();
            if (!$reference->getColumn()) {
                $reference->setColumn($column->getReferenceColumn()->getName());
            }
            if (!$reference->getLabel()) {
                $reference->setLabel($column->getReferenceColumn()->getName());
            }
            if (!$reference->getTable()) {
                $reference->setTable($column->getReferenceColumn()->getParent()->getName());
            }
            $this->object->addForeignKeyReference($columnName, $reference);
        } // end foreach
    }

    /**
     * Select visible columns.
     *
     * Limits the visible columns to entries of this list.
     *
     * @param   array  $columnNames  whitelist
     * @return  \Yana\Forms\Setups\Builder
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
     * @param   array  $columnNames  whitelist
     * @return  \Yana\Forms\Setups\Builder
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
     * @return  \Yana\Forms\Setups\Builder
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