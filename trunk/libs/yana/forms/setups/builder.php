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
declare(strict_types=1);

namespace Yana\Forms\Setups;

/**
 * <<builder>> Build a form using a form object and settings.
 *
 * @package     yana
 * @subpackage  form
 */
class Builder extends \Yana\Core\StdObject implements \Yana\Forms\Setups\IsBuilder
{

    use \Yana\Forms\Dependencies\HasContainer;

    /**
     * Builder product.
     *
     * @var  \Yana\Forms\Setup
     * @ignore
     */
    protected $object = null;

    /**
     * DDL definition object of selected table
     *
     * @var  \Yana\Db\Ddl\Table
     */
    private $_table = null;

    /**
     * DDL definition object of selected table
     *
     * @var  \Yana\Db\Ddl\Form
     */
    private $_form = null;

    /**
     * Whitelist of column names.
     *
     * @var  array
     */
    private $_whitelistColumnNames = array();

    /**
     * Blacklist of column names.
     *
     * @var  array
     */
    private $_blacklistColumnNames = array();

    /**
     * The values of the rows to show in the form, if any.
     *
     * @var array
     */
    private $_rows = array();

    /**
     * Initialize instance.
     *
     * @param  \Yana\Db\Ddl\Form  $form  base form defintion that the setup will apply to
     */
    public function __construct(\Yana\Db\Ddl\Form $form, \Yana\Core\Dependencies\IsFormContainer $container)
    {
        $this->_form = $form;
        $this->object = new \Yana\Forms\Setup();
        $this->_setDependencyContainer($container);
    }

    /**
     * Returns values of the rows to show in the form, if any.
     *
     * @return  array
     */
    public function getRows(): array
    {
        return $this->_rows;
    }

    /**
     * Overwrite existing setup.
     *
     * Set your own predefined setup, to modify it.
     *
     * @param   \Yana\Forms\IsSetup  $setup  basic setup to modify
     * @return  $this
     */
    public function setSetup(\Yana\Forms\IsSetup $setup)
    {
        $this->object = $setup;
        return $this;
    }

    /**
     * Build facade object.
     *
     * @return  \Yana\Forms\IsSetup
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the database, or table was not found
     */
    public function __invoke()
    {
        $this->_buildActions()->_buildSetupContext();
        $this->_resetUpdateContextRows()->_setUpdateContextRows($this->getRows());
        $this->_buildHeader();
        $this->_buildFooter();
        return $this->object;
    }

    /**
     * Get form object.
     *
     * @return  \Yana\Db\Ddl\Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Set form object.
     *
     * @param   \Yana\Db\Ddl\Form  $form  configuring the contents of the form
     * @return  $this
     */
    public function setForm(\Yana\Db\Ddl\Form $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Update setup with request array.
     *
     * @param   array  $request  initial values (e.g. Request array)
     * @return  $this
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
     * @param   array  $request  initial values (e.g. Request array)
     * @return  $this
     */
    public function updateValues(array $request = array())
    {
        assert('!isset($setup); // Cannot redeclare var $setup');
        $setup = $this->object;

        assert('!isset($contextNames); // Cannot redeclare var $contextNames');
        $contextNames = array();
        if ($setup->getInsertAction()) {
            $contextNames[] = \Yana\Forms\Setups\ContextNameEnumeration::INSERT;
        }
        if ($setup->getSearchAction()) {
            $contextNames[] = \Yana\Forms\Setups\ContextNameEnumeration::SEARCH;
        }
        if ($setup->getUpdateAction()) {
            $contextNames[] = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        }

        assert('!isset($name); // Cannot redeclare var $name');
        foreach ($contextNames as $name)
        {
            if (!isset($request[$name]) || !is_array($request[$name])) {
                continue;
            }
            assert('!isset($requestValues); // Cannot redeclare var $requestValues');
            $requestValues = \Yana\Util\Hashtable::changeCase($request[$name], \CASE_UPPER);
            assert('!isset($context); // Cannot redeclare var $context');
            assert('!isset($columnNames); // Cannot redeclare var $columnNames');
            $context = $setup->getContext($name);
            if ($name === \Yana\Forms\Setups\ContextNameEnumeration::UPDATE) {
                $this->setRows($requestValues);

            } else {
                $columnNames = array_flip($context->getColumnNames());
                assert('!isset($values); // Cannot redeclare var $values');
                // security check: allow only fields, that exist in the form
                $values = array_intersect_key($requestValues, $columnNames);
                $context->setValues($values);
                unset($values);
            }
            unset($context, $columnNames, $requestValues);
        }
        unset($name);

        return $this;
    }

    /**
     * Overwrite row values.
     *
     * @param   array  $rows  initial values
     * @return  $this
     */
    public function setRows(array $rows = array())
    {
        $this->_rows = $rows;
        return $this;
    }

    /**
     * Reset (empty) all rows in the update context.
     *
     * @return  $this
     */
    private function _resetUpdateContextRows()
    {
        $this->object->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE)->setRows(array());
        return $this;
    }

    /**
     * Merges the given rows with the existing values.
     *
     * @param   array  $rows  array of rows to update
     * @return  $this
     */
    private function _setUpdateContextRows(array $rows)
    {
        assert('!isset($columnNames); // Cannot redeclare var $columnNames');
        $columnNames = array_flip($this->object->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE)->getColumnNames());
        assert('!isset($context); // Cannot redeclare var $context');
        $context = $this->object->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE);

        assert('!isset($key); // Cannot redeclare var $key');
        assert('!isset($row); // Cannot redeclare var $row');
        foreach ($rows as $key => $row)
        {
            if (is_array($row)) {
                $upperCaseRow = \array_change_key_case($row, \CASE_UPPER);
                // security check: allow only fields, that exist in the form
                $row = array_intersect_key($upperCaseRow, $columnNames);
                $context->updateRow($key, $row);
            }
        }
        unset($key, $row);

        return $this;
    }

    /**
     * Create info on visible entries.
     *
     * @return  $this
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
        $header = \Yana\Util\Strings::replaceToken($this->_getDependencyContainer()->getLanguage()->getVar("DESCR_SHOW"), $params);
        $this->object->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE)->setHeader($header);
        return $this;
    }

    /**
     * Create links to other pages.
     *
     * @return  $this
     */
    private function _buildFooter()
    {
        $context = $this->object->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE);
        $entriesPerPage = $this->object->getEntriesPerPage();
        $pageCount = $this->object->getPageCount();
        $lastPage = $pageCount - 1;
        $entryCount = $this->object->getEntryCount();

        assert('$entriesPerPage > 0; // invalid number of entries to view per page');
        $currentPage = $this->object->getPage();
        $listOfEntries = "";
        assert('!isset($pluginManager); // Cannot redeclare var $pluginManager');
        $pluginManager = $this->_getDependencyContainer()->getPlugins();
        $action = $pluginManager->getFirstEvent();
        $lang = $this->_getDependencyContainer()->getLanguage();
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
     * @return  $this
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
                if ($this->_getDependencyContainer()->getSecurity()->checkRules(null, "download_file")) {
                    $downloadAction = "download_file";
                }
            }
        }
        $setup->setDownloadAction($downloadAction);
        $setup->setSearchAction($searchAction);
        $setup->setExportAction($exportAction);

        $insertAction = "";
        if ($form->isInsertable()) {
            $insertAction = $this->_resolveAction('insert');
        }
        $setup->setInsertAction($insertAction);

        $updateAction = "";
        if ($form->isUpdatable()) {
            $updateAction = $this->_resolveAction('update');
        }
        $setup->setUpdateAction($updateAction);

        $deleteAction = "";
        if ($form->isDeletable()) {
            $deleteAction = $this->_resolveAction('delete');
        }
        $setup->setDeleteAction($deleteAction);

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
                $action = (string) $event->getAction();
            }
        }
        $security = $this->_getDependencyContainer()->getSecurity();
        if (!empty($action) && !$security->checkRules(null, $action)) {
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
            if ($name == "") {
                $message = "Error in form '" . $form->getName() . "'. No parent table defined.";
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
     * @return  $this
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the database, or table was not found
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
            $columnNames = $form->getFieldNames();
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
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::DATE:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::ENUM:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::HTML:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::INET:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::INT:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::LST:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::MAIL:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::RANGE:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::SET:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::STRING:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::TEXT:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::TIME:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::TIMESTAMP:
                    case \Yana\Db\Ddl\ColumnTypeEnumeration::URL:
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
        $this->object->getContext(\Yana\Forms\Setups\ContextNameEnumeration::EDITABLE)->setColumnNames(array_keys($updateCollection->toArray()));
        $this->object->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE)->setColumnNames(array_keys($updateCollection->toArray()));
        $this->object->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT)->setColumnNames(array_keys($insertCollection->toArray()));
        $this->object->getContext(\Yana\Forms\Setups\ContextNameEnumeration::SEARCH)->setColumnNames(array_keys($searchCollection->toArray()));
        $this->object->getContext(\Yana\Forms\Setups\ContextNameEnumeration::READ)->setColumnNames(array_keys($readCollection->toArray()));
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
            if (!$column->isReference()) {
                continue;
            }
            $reference = $column->autoFillReferenceSettings();
            $this->object->addForeignKeyReference($columnName, $reference);
        } // end foreach
    }

    /**
     * Select visible columns.
     *
     * Limits the visible columns to entries of this list.
     *
     * @param   array  $columnNames  whitelist
     * @return  $this
     */
    public function setColumnsWhitelist(array $columnNames)
    {
        $this->_whitelistColumnNames = $columnNames;
        $this->_applyWhitelistColumnNames();
        return $this;
    }

    /**
     * Get list of visible columns.
     *
     * If empty, all columns are visible. Otherwise only those in the list can be viewed.
     *
     * @return  array
     */
    public function getColumnsWhitelist()
    {
        return $this->_whitelistColumnNames;
    }

    /**
     * Select hidden columns.
     *
     * Limits the visible columns to entries not on this list.
     *
     * @param   array  $columnNames  whitelist
     * @return  $this
     */
    public function setColumnsBlacklist(array $columnNames)
    {
        $this->_blacklistColumnNames = $columnNames;
        $this->_applyWhitelistColumnNames();
        return $this;
    }

    /**
     * Get list of hidden columns.
     *
     * @return  array
     */
    public function getColumnsBlacklist()
    {
        return $this->_blacklistColumnNames;
    }

    /**
     * Apply selected whitelist of column names, if there is any.
     *
     * This function filters out all columns not apparent in the whitelist on all contexts.
     *
     * @return  $this
     */
    private function _applyWhitelistColumnNames()
    {
        assert('!isset($whiteList); // Cannot redeclare var $whiteList');
        $whiteList = \array_change_key_case($this->getColumnsWhitelist(), CASE_UPPER);
        assert('!isset($blackList); // Cannot redeclare var $blackList');
        $blackList = \array_change_key_case($this->getColumnsBlacklist(), CASE_UPPER);
        foreach ($this->object->getContexts() as $context)
        {
            $columns = $context->getColumnNames();
            if (!empty($whiteList)) {
                if (!empty($columns)) {
                    $columns = array_intersect($columns, $whiteList);
                } else {
                    $columns = $whiteList;
                }
            }
            if (!empty($blackList)) {
                $columns = array_diff($columns, $blackList);
            }
            $context->setColumnNames($columns);
        }
        return $this;
    }
}

?>