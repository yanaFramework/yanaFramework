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
class Builder extends \Yana\Forms\AbstractBuilder
{

    /**
     * <<magic>> Invoke the function.
     *
     * @return  \Yana\Forms\Facade
     * @throws  \Yana\Core\Exceptions\BadMethodCallException    when a parameter is missing
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when a paraemter is not valid
     */
    public function __invoke()
    {
        $formName = $this->_getForm()->getName();
        $formSetup = null;

        $cache = $this->_getCache();
        if (isset($cache[$formName])) {
            $formSetup = $cache[$formName];
        } else {
            $formSetup = $this->_buildSetup($this->_getForm());
        }
        $this->_getSetupBuilder()->setSetup($formSetup);
        $this->_getFacade()->setSetup($formSetup);

        // copy search term from parent-forms
        if ($this->_getFacade()->getParent()) {
            $formSetup->setSearchTerm($this->_getFacade()->getParent()->getSetup()->getSearchTerm());
        }

        $request = (array) $this->_getDependencyContainer()->getRequest()->all()->value($formName)->all()->asArrayOfStrings();
        $uploadWrapper = $this->_getDependencyContainer()->getRequest()->files();
        if ($uploadWrapper->has($formName) && $uploadWrapper->isListOfFiles($formName)) {
            $files = (array) $uploadWrapper->all($formName);
        }
        if (!empty($files)) {
            $request = \Yana\Util\Hashtable::merge($request, $files);
        }
        unset($files);
        if (!empty($request)) {
            $this->_getSetupBuilder()->updateSetup($request);
        }

        $this->_getQueryBuilder()->setForm($this->_getFacade());

        $countQuery = $this->_getQueryBuilder()->buildCountQuery();
        $where = $this->getWhere();
        if (!empty($where)) {
            $countQuery->addWhere($where);
        }
        $formSetup->setEntryCount($countQuery->countResults());

        $selectQuery = $this->_getQueryBuilder()->buildSelectQuery();
        if (!empty($where)) {
            $selectQuery->addWhere($where);
        }
        $selectQuery->setOffset($formSetup->getPage() * $formSetup->getEntriesPerPage());
        $values = $selectQuery->getResults();
        if ($selectQuery->getExpectedResult() === \Yana\Db\ResultEnumeration::ROW) {
            $values = array($values);
        }
        $this->_getSetupBuilder()->setRows($values);
        $referenceValues = array();
        foreach ($formSetup->getForeignKeys() as $name => $reference)
        {
            $referenceValues[$name] = $this->_getQueryBuilder()->autocomplete($name,  "", 0);
        }
        $formSetup->setReferenceValues($referenceValues);

        // This needs to be done after the rows have been set. Otherwise the user input would be overwritten.
        if ($request) {
            $this->_getSetupBuilder()->updateValues($request);
        }

        $cache[$formName] = $this->_getSetupBuilder()->__invoke(); // add to cache
        $this->_buildSubForms($this->_getFacade());

        return $this->_getFacade();
    }

    /**
     * Build \Yana\Db\Ddl\Form object.
     *
     * @return  \Yana\Db\Ddl\Form
     * @throws  \Yana\Core\Exceptions\BadMethodCallException    when a parameter is missing
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when a paraemter is not valid
     */
    protected function _buildForm()
    {
        // Either parameter 'id' or 'table' is required (not both). Parameter 'id' takes precedence.
        /* @var $form \Yana\Db\Ddl\Form */
        $form = null;
        if ($this->getId()) {
            $ids = $this->getId();
            $form = $this->_getDatabaseSchema();
            foreach (explode('.', $ids) as $id)
            {
                if (!$form->isForm($id)) {
                    $message = "The form with name '" . $ids . "' was not found.";
                    throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
                }
                $form = $form->getForm($id);
            }
        } elseif ($this->getTable()) {
                $table = $this->_getDatabaseSchema()->getTable($this->getTable());
                if (! $table instanceof \Yana\Db\Ddl\Table) {
                    $message = "The table with name '" . $this->getTable() . "' was not found.";
                    throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
                }
                $form = $this->_buildFormFromTable($table);
        } else {
            throw new \Yana\Core\Exceptions\BadMethodCallException("Missing either parameter 'id' or 'table'.");
        }
        return $form;
    }

    /**
     * Create form object from table definition.
     *
     * This function takes a table and initializes the form based on it's structure and columns.
     *
     * @return  \Yana\Db\Ddl\Form
     */
    private function _buildFormFromTable(\Yana\Db\Ddl\Table $table)
    {
        $genericName = $this->_getDatabase()->getName() . '-' . $table->getName();

        $form = new \Yana\Db\Ddl\Form($genericName, $this->_getDatabaseSchema()); // from scratch
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
                $builder = new \Yana\Forms\Builder($this->getFile(), $this->_getDependencyContainer());
            }
            $builder->_setForm($subForm, $form);
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
     * @return  $this 
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
            $this->_getSetupBuilder()->setColumnsWhitelist(array_diff($whitelist, $blacklist));
        }
        if (!empty($blacklist)) {
            $this->_getSetupBuilder()->setColumnsBlacklist($blacklist);
        }
        return $this;
    }

}

?>