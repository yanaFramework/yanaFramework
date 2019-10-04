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
 * Abstract form settings
 *
 * @package     yana
 * @subpackage  form
 */
class Setup extends \Yana\Core\StdObject implements \Yana\Forms\IsSetup
{

    /**
     * currently selected page (for multi-page layout)
     *
     * @var  int
     */
    private $_page = 0;

    /**
     * number of viewable pages (for multi-page layout)
     *
     * @var  int
     */
    private $_pageCount = 0;

    /**
     * number of viewable entries (for multi-page layout)
     *
     * @var  int
     */
    private $_entryCount = 0;

    /**
     * number of entries per page (for multi-page layout)
     *
     * @var  int
     */
    private $_entriesPerPage = 5;

    /**
     * selected layout
     *
     * @var  int
     */
    private $_layout = 0;

    /**
     * Columns filters (used in query's having-clause)
     *
     * @var  array
     */
    private $_filters = array();

    /**
     * order by field
     *
     * Contains a field name.
     *
     * @var  string
     */
    private $_orderByField = "";

    /**
     * order ascending or descending
     *
     * @var  bool
     */
    private $_isDescending = false;

    /**
     * search term used
     *
     * @var  string
     */
    private $_searchTerm = "";

    /**
     * Context setups.
     *
     * @var  \Yana\Forms\Setups\Context[]
     */
    private $_contexts = array();

    /**
     * Defined list of auto-replaced references.
     *
     * @var  \Yana\Db\Ddl\Reference[]
     */
    private $_foreignKeyRefrences = array();

    /**
     * Name of download action.
     *
     * This global definition applies to all contexts.
     *
     * @var  string
     */
    private $_downloadAction = "";

    /**
     * Name of delete action.
     *
     * This global definition applies to all contexts.
     *
     * @var  string
     */
    private $_deleteAction = "";

    /**
     * export name of export action.
     *
     * This global definition applies to all contexts.
     *
     * @var  string
     */
    private $_exportAction = "";

    /**
     * Index of autocomplete values stored for column-names.
     *
     * @var  string
     */
    private $_referenceValues = array();

    /**
     * Get a setup context.
     *
     * A context is a number of settings that apply to a form in a specified scenario.
     * E.g. using an insert-context a form may have other contents than in an edit- or search-context.
     * However: basic settings will always stay the same.
     *
     * Returns the context settings with the specified name.
     * If the context does not exist, it is created.
     *
     * @param   string  $name  describes what the form does (insert, update, search, read)
     * @return  \Yana\Forms\Setups\IsContext
     * @see \Yana\Forms\Setups\ContextNameEnumeration
     */
    public function getContext($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        if (!isset($this->_contexts[$name])) {
            $this->_contexts[$name] = new \Yana\Forms\Setups\Context($name);
        }
        return $this->_contexts[$name];
    }

    /**
     * Get an array of all registered setup contexts.
     *
     * Returns an associative array where the keys are the context names,
     * the values are instances of {@see FormSetupContext}.
     *
     * @return  array
     */
    public function getContexts()
    {
        return $this->_contexts;
    }

    /**
     * Set a setup context.
     *
     * Stores the given context settings under the specified name.
     * If the context does not exist, it is created.
     *
     * @param   \Yana\Forms\Setups\Context  $context  context settings
     * @return  $this
     */
    public function setContext(\Yana\Forms\Setups\Context $context)
    {
        $this->_contexts[$context->getContextName()] = $context;
        return $this;
    }

    /**
     * This returns an array of foreign-key reference settings.
     *
     * @return  \Yana\Db\Ddl\Reference[]
     */
    public function getForeignKeys()
    {
        return $this->_foreignKeyRefrences;
    }

    /**
     * Add a foreign key reference.
     *
     * You may setup a different column to view instead of a (possibly numeric) forein key.
     * To do this, just add a foreign-key reference by naming the source and target column,
     * plus the column you wish to use as a label.
     *
     * @param   string                  $columnName  name of source column
     * @param   \Yana\Db\Ddl\Reference  $foreignKey  settings of source reference
     * @return  $this
     */
    public function addForeignKeyReference($columnName, \Yana\Db\Ddl\Reference $foreignKey)
    {
        assert('is_string($columnName); // Invalid argument $columnName: string expected');
        $this->_foreignKeyRefrences[$columnName] = $foreignKey;
        return $this;
    }

    /**
     * Set current page.
     *
     * The first page is 0, the second is 1, aso., defaults to 0.
     * This function does not check if the page number is beyond the last viewable page.
     * In that case your implementation should check and correct the value before using it.
     *
     * @param   int  $page  number of start page
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if $page is < 0
     * @return  \Yana\Forms\Setup
     */
    public function setPage($page = 0)
    {
        assert('is_int($page); // Wrong type for argument 1. Integer expected');

        /* default values */
        if ($page < 0) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Page number must be a positive integer.");
        }
        $this->_page = (int) $page;
        return $this;
    }

    /**
     * Get the currently selected page.
     *
     * Expected to default to 0.
     *
     * @return  int
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * Set number of rows.
     *
     * This function sets the number of viewable rows and pages.
     * If the current page lies beyond the last page, it is reset to 0 (the first page).
     *
     * @param   int  $entryCount  number of entry
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if $entryCount is < 0
     * @return  \Yana\Forms\Setup
     */
    public function setEntryCount($entryCount)
    {
        assert('is_int($entryCount); // Invalid argument $entryCount: int expected');

        /* default values */
        if ($entryCount < 0) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Entry count must be a positive integer.");
        }
        $this->_entryCount = (int) $entryCount;
        $this->_pageCount = (int) ceil($this->_entryCount / $this->getEntriesPerPage());
        if ($this->getPage() >= $this->_pageCount) {
            $this->setPage(0); // make sure the user cannot go beyond the last page
        }
        return $this;
    }

    /**
     * Get the number of entries.
     *
     * Expected to default to 0.
     *
     * @return  int
     */
    public function getEntryCount()
    {
        return $this->_entryCount;
    }

    /**
     * Get the currently selected page.
     *
     * Expected to default to 0.
     *
     * @return  int
     */
    public function getPageCount()
    {
        return $this->_pageCount;
    }

    /**
     * Set number of entries per page.
     *
     * @param   int  $entries  number of entries per page, must be >= 1
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if $entries is < 1
     * @return  \Yana\Forms\Setup
     */
    public function setEntriesPerPage($entries = 5)
    {
        assert('is_int($entries); // Wrong type for argument 1. Integer expected');

        if ($entries < 1) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Number of entries per page must be an integer > 0.");
        }
        $this->_entriesPerPage = (int) $entries;
        return $this;
    }

    /**
     * Get number of entries to show per page.
     *
     * Expected to default to 5.
     *
     * @return  int
     */
    public function getEntriesPerPage()
    {
        return $this->_entriesPerPage;
    }

    /**
     * Check if form has a filter.
     *
     * This funciton returns bool(true) if a filter has been set on any of the forms columns,
     * and bool(false) otherwise.
     *
     * @return  bool
     */
    public function hasFilter()
    {
        return !empty($this->_filters);
    }

    /**
     * Get filter value.
     *
     * Returns an empty string, if there is no filter.
     *
     * @param   string  $columnName  where to apply the filter on
     * @return  string
     */
    public function getFilter($columnName)
    {
        assert('is_string($columnName); // Wrong argument type argument 1. String expected');
        return isset($this->_filters[$columnName]) ? $this->_filters[$columnName] : "";
    }

    /**
     * Get filter values.
     *
     * Returns an associative array, where the keys are the colum names and the values are the filter strings.
     *
     * @return  array
     */
    public function getFilters()
    {
        assert('is_array($this->_filters); // Member "filters" is expected to be an array.');
        return $this->_filters;
    }

    /**
     * Set filter value for the selected column.
     *
     * Leave the second argument empty to reset the value.
     * You may use the chars '?', '_' as wild-cards for 1 char and '*', '%' as wild-cards for multiple chars.
     *
     * @param   string  $columnName  where to apply the filter on
     * @param   string  $value       new filter value
     * @return  \Yana\Forms\Setup
     */
    public function setFilter($columnName, $value = "")
    {
        assert('is_string($columnName); // Wrong argument type argument 1. String expected');
        assert('is_string($value); // Wrong argument type argument 2. String expected');
        if (!empty($value)) {
            $value = strtr($value, '*?', '%_'); // translate wildcards
            $value = \Yana\Util\Strings::htmlSpecialChars($value);
            $this->_filters[$columnName] = $value;
        } else {
            unset($this->_filters[$columnName]);
        }
        return $this;
    }

    /**
     * Set filter values for all columns.
     *
     * Leave the parameter empty to reset all filters.
     *
     * @param   array  $filters  associative array, where keys are the colum names and values are the filter strings
     * @return  \Yana\Forms\Setup
     */
    public function setFilters(array $filters = array())
    {
        $this->_filters = array();
        foreach ($filters as $columnName => $filter)
        {
            $this->setFilter($columnName, $filter);
        }
        return $this;
    }

    /**
     * Set values for autocompletion of columns.
     *
     * @param   array  $values  associative array, where keys are the colum names and values rows
     * @return  \Yana\Forms\Setup
     */
    public function setReferenceValues(array $values)
    {
        $this->_referenceValues = array_change_key_case($values, CASE_UPPER);
        return $this;
    }

    /**
     * Get values for autocompletion of columns.
     *
     * Returns an empty array if the column is not found or has no references.
     *
     * @param   string  $columnName  name of column-index to look up
     * @return  array
     */
    public function getReferenceValues($columnName)
    {
        assert('is_string($columnName); // Invalid argument $name: string expected');
        assert('!isset($columnNameUpper); // Cannot redeclare $columnNameUpper');
        $columnNameUpper = strtoupper($columnName);
        assert('!isset($values); // Cannot redeclare $values');
        $values = array();
        if (isset($this->_referenceValues[$columnNameUpper]) && is_array($this->_referenceValues[$columnNameUpper])) {
            $values = $this->_referenceValues[$columnNameUpper];
        }
        return $values;
    }

    /**
     * Select a template for output.
     *
     * Forms offer mulitple alternative form layouts to choose from.
     * These are numbered (0..n), where 0 is always the default.
     *
     * @param   int  $layout  template settings (int 0...n)
     * @return  \Yana\Forms\Setup
     */
    public function setLayout($layout = 0)
    {
        assert('is_int($layout); // Wrong type for argument 1. Integer expected');
        assert('$layout >= 0; // Invalid argument. Layout must be a positive integer');
        $this->_layout = (int) $layout;
        return $this;
    }

    /**
     * Get selected a layout for output.
     *
     * Forms offer mulitple alternative form layouts to choose from.
     * These are numbered (0..n), where 0 is always the default.
     * This function returns the currently selected number.
     *
     * @return  int
     */
    public function getLayout()
    {
        assert('is_int($this->_layout); // Member "layout" is expected to be an integer.');
        return $this->_layout;
    }

    /**
     * Get name of field that should be used to sort the table contents.
     *
     * Returns empty string if the table is expected to be sorted by primary key.
     *
     * @return  string
     */
    public function getOrderByField()
    {
        assert('is_string($this->_orderByField); // Member "orderByField" is expected to be a string.');
        return $this->_orderByField;
    }

    /**
     * Set name of field to order output by
     *
     * Call this without input to reset the value.
     *
     * @param   string  $fieldName  name of field to order by
     * @return  \Yana\Forms\Setup
     */
    public function setOrderByField($fieldName = "")
    {
        assert('is_string($fieldName); // Wrong argument type argument 1. String expected');
        $this->_orderByField = $fieldName;
        return $this;
    }

    /**
     * Set order in which the resultset should be sorted.
     *
     * @param   bool $isDescending  True = descending, False = ascending order
     * @return  \Yana\Forms\Setup
     */
    public function setSortOrder($isDescending = false)
    {
        assert('is_bool($isDescending); // Wrong argument type argument 1. Boolean expected');
        $this->_isDescending = !empty($isDescending);
        return $this;
    }

    /**
     * Check if resultset should be sorted in descending order.
     *
     * True = descending, False = ascending order.
     * Defaults to false.
     *
     * @return  bool
     */
    public function isDescending()
    {
        assert('is_bool($this->_isDescending); // Member "isDescending" is expected to be bool.');
        return !empty($this->_isDescending);
    }

    /**
     * Set search term.
     *
     * This is expected to select 1 term with wilcards '_' and '%' to search for in
     * the values of the form and all subforms.
     * To reset the value, leave the parameter empty.
     *
     * @param   string  $searchTerm  term entered in global search box
     * @return  \Yana\Forms\Setup
     */
    public function setSearchTerm($searchTerm = "")
    {
        assert('is_string($searchTerm); // Wrong argument type argument 1. String expected');
        $this->_searchTerm = $searchTerm;
        return $this;
    }

    /**
     * Get currently selected search term.
     *
     * Returns an empty string if no search term was set.
     *
     * @return  string
     */
    public function getSearchTerm()
    {
        return $this->_searchTerm;
    }

    /**
     * Set download action.
     *
     * @param   string  $action action name
     * @return  \Yana\Forms\Setup
     */
    public function setDownloadAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->_downloadAction = $action;
        return $this;
    }

    /**
     * Get download action.
     *
     * Returns the lower-cased name of the currently selected action.
     *
     * The default is 'download_file'.
     *
     * @return  string
     */
    public function getDownloadAction()
    {
        return $this->_downloadAction;
    }

    /**
     * Set search action.
     *
     * @param   string  $action  action name
     * @return  \Yana\Forms\Setup
     */
    public function setSearchAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->getContext(\Yana\Forms\Setups\ContextNameEnumeration::SEARCH)->setAction($action);
        return $this;
    }

    /**
     * Get search action.
     *
     * @return  string
     */
    public function getSearchAction()
    {
        return $this->getContext(\Yana\Forms\Setups\ContextNameEnumeration::SEARCH)->getAction();
    }

    /**
     * Set insert action.
     *
     * @param   string  $action  action name
     * @return  \Yana\Forms\Setup
     */
    public function setInsertAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT)->setAction($action);
        return $this;
    }

    /**
     * Get insert action.
     *
     * @return  string
     */
    public function getInsertAction()
    {
        return $this->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT)->getAction();
    }

    /**
     * Set update action.
     *
     * @param   string  $action  action name
     * @return  \Yana\Forms\Setup
     */
    public function setUpdateAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE)->setAction($action);
        return $this;
    }

    /**
     * Get update action.
     *
     * @return  string
     */
    public function getUpdateAction()
    {
        return $this->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE)->getAction();
    }

    /**
     * Set delete action.
     *
     * @param   string  $action action name
     * @return  \Yana\Forms\Setup
     */
    public function setDeleteAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->_deleteAction = $action;
        return $this;
    }

    /**
     * Get delete action.
     *
     * @return  string
     */
    public function getDeleteAction()
    {
        return $this->_deleteAction;
    }

    /**
     * Set export action.
     *
     * @param   string  $action action name
     * @return  \Yana\Forms\Setup
     */
    public function setExportAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->_exportAction = $action;
        return $this;
    }

    /**
     * Get export action.
     *
     * @return  string
     */
    public function getExportAction()
    {
        return $this->_exportAction;
    }

}

?>