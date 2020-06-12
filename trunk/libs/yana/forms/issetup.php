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
 * <<interface>> Form settings.
 *
 * @package     yana
 * @subpackage  form
 */
interface IsSetup
{
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
     * @param   string  $name  context name
     * @return  $thiss\IsContext
     */
    public function getContext(string $name): \Yana\Forms\Setups\IsContext;

    /**
     * Get an array of all registered setup contexts.
     *
     * Returns an associative array where the keys are the context names,
     * the values are instances of {@see FormSetupContext}.
     *
     * @return  array
     */
    public function getContexts(): array;

    /**
     * Set a setup context.
     *
     * Stores the given context settings under the specified name.
     * If the context does not exist, it is created.
     *
     * @param   \Yana\Forms\Setups\Context  $context  context settings
     * @return  $this
     */
    public function setContext(\Yana\Forms\Setups\Context $context);

    /**
     * This returns an array of foreign-key reference settings.
     *
     * @return  \Yana\Db\Ddl\Reference[]
     */
    public function getForeignKeys(): array;

    /**
     * Add a foreign key reference.
     *
     * You may setup a different column to view instead of a (possibly numeric) forein key.
     * To do this, just add a foreign-key reference by naming the source and target column,
     * plus the column you wish to use as a label.
     *
     * @param   string       $columnName  name of source column
     * @param   \Yana\Db\Ddl\Reference $foreignKey  settings of source reference
     * @return  $this
     */
    public function addForeignKeyReference(string $columnName, \Yana\Db\Ddl\Reference $foreignKey);

    /**
     * Set current page.
     *
     * The first page is 0, the second is 1, aso., defaults to 0.
     * This function does not check if the page number is beyond the last viewable page.
     * In that case your implementation should check and correct the value before using it.
     *
     * @param   int  $page  number of start page
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if $page is < 0
     * @return  $this
     */
    public function setPage(int $page = 0);

    /**
     * Get the currently selected page.
     *
     * Expected to default to 0.
     *
     * @return  int
     */
    public function getPage(): int;

    /**
     * Set number of rows.
     *
     * This function sets the number of viewable rows and pages.
     * If the current page lies beyond the last page, it is reset to 0 (the first page).
     *
     * @param   int  $entryCount  number of entry
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if $entryCount is < 0
     * @return  $this
     */
    public function setEntryCount(int $entryCount);

    /**
     * Get the number of entries.
     *
     * Expected to default to 0.
     *
     * @return  int
     */
    public function getEntryCount(): int;

    /**
     * Get the currently selected page.
     *
     * Expected to default to 0.
     *
     * @return  int
     */
    public function getPageCount(): int;

    /**
     * Set number of entries per page.
     *
     * @param   int  $entries  number of entries per page, must be >= 1
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if $entries is < 1
     * @return  $this
     */
    public function setEntriesPerPage(int $entries = 10);

    /**
     * Get number of entries to show per page.
     *
     * Expected to default to 10.
     *
     * @return  int
     */
    public function getEntriesPerPage(): int;

    /**
     * Check if form has a filter.
     *
     * This funciton returns bool(true) if a filter has been set on any of the forms columns,
     * and bool(false) otherwise.
     *
     * @return  bool
     */
    public function hasFilter(): bool;

    /**
     * Get filter value.
     *
     * Returns an empty string, if there is no filter.
     *
     * @param   string  $columnName  where to apply the filter on
     * @return  string
     */
    public function getFilter(string $columnName): string;

    /**
     * Get filter values.
     *
     * Returns an associative array, where the keys are the colum names and the values are the filter strings.
     *
     * @return  array
     */
    public function getFilters(): array;

    /**
     * Set filter value for the selected column.
     *
     * Leave the second argument empty to reset the value.
     * You may use the chars '?', '_' as wild-cards for 1 char and '*', '%' as wild-cards for multiple chars.
     *
     * @param   string  $columnName  where to apply the filter on
     * @param   string  $value       new filter value
     * @return  $this
     */
    public function setFilter(string $columnName, string $value = "");

    /**
     * Set filter values for all columns.
     *
     * Leave the parameter empty to reset all filters.
     *
     * @param   array  $filters  associative array, where keys are the colum names and values are the filter strings
     * @return  $this
     */
    public function setFilters(array $filters = array());

    /**
     * Set values for autocompletion of columns.
     *
     * @param   array  $values  associative array, where keys are the colum names and values rows
     * @return  $this
     */
    public function setReferenceValues(array $values);

    /**
     * Get values for autocompletion of columns.
     *
     * Returns an empty array if the column is not found or has no references.
     *
     * @param   string  $columnName  name of column-index to look up
     * @return  array
     */
    public function getReferenceValues(string $columnName): array;

    /**
     * Select a template for output.
     *
     * Forms offer mulitple alternative form layouts to choose from.
     * These are numbered (0..n), where 0 is always the default.
     *
     * @param   int  $layout  template settings (int 0...n)
     * @return  $this
     */
    public function setLayout(int $layout = 0);

    /**
     * Get selected a layout for output.
     *
     * Forms offer mulitple alternative form layouts to choose from.
     * These are numbered (0..n), where 0 is always the default.
     * This function returns the currently selected number.
     *
     * @return  int
     */
    public function getLayout(): int;

    /**
     * Get name of field that should be used to sort the table contents.
     *
     * Returns empty string if the table is expected to be sorted by primary key.
     *
     * @return  string
     */
    public function getOrderByField(): string;

    /**
     * Set name of field to order output by
     *
     * Call this without input to reset the value.
     *
     * @param   string  $fieldName  name of field to order by
     * @return  $this
     */
    public function setOrderByField(string $fieldName = "");

    /**
     * Set order in which the resultset should be sorted.
     *
     * @param   bool $isDescending  True = descending, False = ascending order
     * @return  $this
     */
    public function setSortOrder(bool $isDescending = false);

    /**
     * Check if resultset should be sorted in descending order.
     *
     * True = descending, False = ascending order.
     * Defaults to false.
     *
     * @return  bool
     */
    public function isDescending(): bool;

    /**
     * Set search term.
     *
     * This is expected to select 1 term with wilcards '_' and '%' to search for in
     * the values of the form and all subforms.
     * To reset the value, leave the parameter empty.
     *
     * @param   string  $searchTerm  term entered in global search box
     * @return  $this
     */
    public function setSearchTerm(string $searchTerm = "");

    /**
     * Get currently selected search term.
     *
     * Returns an empty string if no search term was set.
     *
     * @return  string
     */
    public function getSearchTerm(): string;

    /**
     * Set download action.
     *
     * @param   string  $action action name
     * @return  $this
     */
    public function setDownloadAction(string $action);

    /**
     * Get download action.
     *
     * Returns the lower-cased name of the currently selected action.
     *
     * The default is 'download_file'.
     *
     * @return  string
     */
    public function getDownloadAction(): string;

    /**
     * Set search action.
     *
     * @param   string  $action  action name
     * @return  $this
     */
    public function setSearchAction(string $action);

    /**
     * Get search action.
     *
     * @return  string
     */
    public function getSearchAction(): string;

    /**
     * Set insert action.
     *
     * @param   string  $action  action name
     * @return  $this
     */
    public function setInsertAction(string $action);

    /**
     * Get insert action.
     *
     * @return  string
     */
    public function getInsertAction(): string;

    /**
     * Set update action.
     *
     * @param   string  $action  action name
     * @return  $this
     */
    public function setUpdateAction(string $action);

    /**
     * Get update action.
     *
     * @return  string
     */
    public function getUpdateAction(): string;

    /**
     * Set delete action.
     *
     * @param   string  $action action name
     * @return  $this
     */
    public function setDeleteAction(string $action);

    /**
     * Get delete action.
     *
     * @return  string
     */
    public function getDeleteAction(): string;

    /**
     * Set export action.
     *
     * @param   string  $action action name
     * @return  $this
     */
    public function setExportAction(string $action);

    /**
     * Get export action.
     *
     * @return  string
     */
    public function getExportAction(): string;

}

?>