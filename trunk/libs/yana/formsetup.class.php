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
 * @package     yana
 * @subpackage  form
 */
class FormSetup extends Object
{

    /**
     * currently selected page (for multi-page layout)
     *
     * @access  private
     * @var     int
     */
    private $_page = 0;

    /**
     * number of viewable pages (for multi-page layout)
     *
     * @access  private
     * @var     int
     */
    private $_pageCount = 0;

    /**
     * number of viewable entries (for multi-page layout)
     *
     * @access  private
     * @var     int
     */
    private $_entryCount = 0;

    /**
     * number of entries per page (for multi-page layout)
     *
     * @access  private
     * @var     int
     */
    private $_entriesPerPage = 5;

    /**
     * selected layout
     *
     * @access  private
     * @var     int
     */
    private $_layout = 0;

    /**
     * Columns filters (used in query's having-clause)
     *
     * @access  private
     * @var     array
     */
    private $_filters = array();

    /**
     * order by field
     *
     * Contains a field name.
     *
     * @access  private
     * @var     string
     */
    private $_orderByField = "";

    /**
     * order ascending or descending
     *
     * @access  private
     * @var     bool
     */
    private $_isDescending = false;

    /**
     * search term used
     *
     * @access  private
     * @var     string
     */
    private $_searchTerm = "";

    /**
     * Context setups.
     *
     * @access  private
     * @var     FormSetupContext[]
     */
    private $_contexts = array();

    /**
     * Defined list of auto-replaced references.
     *
     * @access  private
     * @var     DDLReference[]
     */
    private $_foreignKeyRefrences = array();

    /**
     * Name of download action.
     *
     * This global definition applies to all contexts.
     *
     * @access  private
     * @var     string
     */
    private $_downloadAction = "";

    /**
     * Name of delete action.
     *
     * This global definition applies to all contexts.
     *
     * @access  private
     * @var     string
     */
    private $_deleteAction = "";

    /**
     * export name of export action.
     *
     * This global definition applies to all contexts.
     *
     * @access  private
     * @var     string
     */
    private $_exportAction = "";

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
     * @access  public
     * @param   string  $name  context name
     * @return  FormSetupContext
     */
    public function getContext($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        if (!isset($this->_contexts[$name])) {
            $this->_contexts[$name] = new FormSetupContext($name);
        }
        return $this->_contexts[$name];
    }

    /**
     * Get an array of all registered setup contexts.
     *
     * Returns an associative array where the keys are the context names,
     * the values are instances of {@see FormSetupContext}.
     *
     * @access  public
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
     * @access  public
     * @param   string            $name     context name
     * @param   FormSetupContext  $context  context settings
     * @return  FormSetup 
     */
    public function setContext($name, FormSetupContext $context)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        if (!isset($this->_contexts[$name])) {
            $this->_contexts[$name] = new FormSetupContext($name);
        }
        return $this;
    }

    /**
     * This returns an array of foreign-key reference settings.
     *
     * @access  public
     * @return  DDLReference[]
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
     * @access  public
     * @param   string       $columnName  name of source column
     * @param   DDLReference $foreignKey  settings of source reference
     * @return  FormSetup 
     */
    public function addForeignKeyReference($columnName, DDLReference $foreignKey)
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
     * @access  public
     * @param   int  $page  number of start page
     * @throws  InvalidArgumentException if $page is < 0
     * @return  FormSetup
     */
    public function setPage($page = 0)
    {
        assert('is_int($page); // Wrong type for argument 1. Integer expected');

        /* default values */
        if ($page < 0) {
            throw new InvalidArgumentException("Page number must be a positive integer.");
        }
        $this->_page = (int) $page;
        return $this;
    }

    /**
     * Get the currently selected page.
     *
     * Expected to default to 0.
     *
     * @access  public
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
     * @access  public
     * @param   int  $entryCount  number of entry
     * @throws  InvalidArgumentException if $entryCount is < 0
     * @return  FormSetup
     */
    public function setEntryCount($entryCount)
    {
        assert('is_int($entryCount); // Invalid argument $entryCount: int expected');

        /* default values */
        if ($entryCount < 0) {
            throw new InvalidArgumentException("Entry count must be a positive integer.");
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
     * @access  public
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
     * @access  public
     * @return  int
     */
    public function getPageCount()
    {
        return $this->_pageCount;
    }

    /**
     * Set number of entries per page.
     *
     * @access  public
     * @param   int  $entries  number of entries per page, must be >= 1
     * @throws  InvalidArgumentException if $entries is < 1
     * @return  FormSetup
     */
    public function setEntriesPerPage($entries = 5)
    {
        assert('is_int($entries); // Wrong type for argument 1. Integer expected');

        if ($entries < 1) {
            throw new InvalidArgumentException("Number of entries per page must be an integer > 0.");
        }
        $this->_entriesPerPage = (int) $entries;
        return $this;
    }

    /**
     * Get number of entries to show per page.
     *
     * Expected to default to 5.
     *
     * @access  public
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
     * @access  public
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
     * @access  public
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
     * @access  public
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
     * @access  public
     * @param   string  $columnName  where to apply the filter on
     * @param   string  $value       new filter value
     * @return  FormSetup
     */
    public function setFilter($columnName, $value = "")
    {
        assert('is_string($columnName); // Wrong argument type argument 1. String expected');
        assert('is_string($value); // Wrong argument type argument 2. String expected');
        if (!empty($value)) {
            $value = strtr($value, '*?', '%_'); // translate wildcards
            $value = String::htmlSpecialChars($value);
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
     * @access  public
     * @param   array  $filters  associative array, where keys are the colum names and values are the filter strings
     * @return  FormSetup
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
     * Select a template for output.
     *
     * Forms offer mulitple alternative form layouts to choose from.
     * These are numbered (0..n), where 0 is always the default.
     *
     * @access  public
     * @param   int  $layout  template settings (int 0...n)
     * @return  FormSetup
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
     * @access  public
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
     * @access  public
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
     * @access  public
     * @param   string  $fieldName  name of field to order by
     * @return  FormSetup
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
     * @access  public
     * @param   bool $isDescending  True = descending, False = ascending order
     * @return  FormSetup
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
     * @access  public
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
     * @access  public
     * @param   string  $searchTerm  term entered in global search box
     * @return  FormSetup
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
     * @access  public
     * @return  bool
     */
    public function getSearchTerm()
    {
        return $this->_searchTerm;
    }

    /**
     * set download action
     *
     * @access  public
     * @param   string  $action action name
     * @return  FormSetup
     */
    public function setDownloadAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->_downloadAction = $action;
        return $this;
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
        return $this->_downloadAction;
    }

    /**
     * set search action
     *
     * @access  public
     * @param   string  $action  action name
     * @return  FormSetup
     */
    public function setSearchAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->getContext('search')->setAction($action);
        return $this;
    }

    /**
     * get search action
     *
     * @access  public
     * @return  string
     */
    public function getSearchAction()
    {
        return $this->getContext('search')->getAction();
    }

    /**
     * set insert action
     *
     * @access  public
     * @param   string  $action  action name
     * @return  FormSetup
     */
    public function setInsertAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->getContext('insert')->setAction($action);
        return $this;
    }

    /**
     * get insert action
     *
     * @access  public
     * @return  string
     */
    public function getInsertAction()
    {
        return $this->getContext('insert')->getAction();
    }

    /**
     * set update action
     *
     * @access  public
     * @param   string  $action  action name
     * @return  FormSetup
     */
    public function setUpdateAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->getContext('update')->setAction($action);
        return $this;
    }

    /**
     * get update action
     *
     * @access  public
     * @return  string
     */
    public function getUpdateAction()
    {
        return $this->getContext('update')->getAction();
    }

    /**
     * set delete action
     *
     * @access  public
     * @param   string  $action action name
     * @return  FormSetup
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
     * @access  public
     * @return  string
     */
    public function getDeleteAction()
    {
        return $this->_deleteAction;
    }

    /**
     * set export action
     *
     * @access  public
     * @param   string  $action action name
     * @return  FormSetup
     */
    public function setExportAction($action)
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        $this->_exportAction = $action;
        return $this;
    }

    /**
     * get export action
     *
     * @access  public
     * @return  string
     */
    public function getExportAction()
    {
        return $this->_exportAction;
    }

}

?>