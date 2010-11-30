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
 * Form object
 *
 * This class is a form-generator, that produces HTML-code from database structures.
 *
 * Example of usage:
 * <code>
 * // use database structure in file 'project'
 * // use table 'effort' from file 'project'
 * $db = Yana::connect('project');
 * $form = $db->getForm('effort');
 * $query = $form->getQuery();
 * // limit entries to show by adding a where clause
 * $query->setWhere( array (
 *     array("effort_duration", '>', 2),
 *     'and',
 *     array("effort_duration", '<', 20)
 * ));
 * // select a column to sort the entries
 * $query->addOrderBy('effort_id');
 * // set the number of entries to show per page
 * $form->setEntriesPerPage(5);
 * // set what action (function) should be triggered,
 * // when the user clicks the "edit" button
 * $form->setEditAction('project_edit_effort');
 * // set what action (function) should be triggered,
 * // when the user clicks the "new" button
 * $form->setNewAction('project_new_effort');
 * // generate the HTML code of the requested form
 * print (string) $form;
 * </code>
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLDefaultForm extends DDLAbstractForm
{
    /**
     * selected layout
     *
     * @access  protected
     * @var     int
     * @ignore
     */
    protected $layout = 0;

    /**
     * Columns filters (used in query's having-clause)
     *
     * @access  protected
     * @var     array
     * @ignore
     */
    protected $filters = array();

    /**
     * order by field
     *
     * Contains a field name.
     *
     * @access  protected
     * @var     string
     * @ignore
     */
    protected $orderByField = "";

    /**
     * order ascending or descending
     *
     * @access  private
     * @var     bool
     * @ignore
     */
    private $isDescending = false;

    /**
     * cache list of entries
     *
     * @access  private
     * @var     string
     * @ignore
     */
    private $listOfEntries = null;

    /**
     * add field by name
     *
     * Adds a field element by the given name and returns it.
     * Throws an exception if a field with the given name already exists.
     *
     * @access  public
     * @param   string  $name       name of a new field
     * @param   string  $className  field class name
     * @return  DDLDefaultField
     * @throws  AlreadyExistsException    when a field with the same name already exists
     * @throws  InvalidArgumentException  if given an invalid name or class
     */
    public function addField($name, $className = 'DDLDefaultField')
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        return parent::addField($name, $className);
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
        parent::setQuery($query);
        // check if initial values are valid
        if ($this->getEntriesPerPage() < 1) {
            $this->setEntriesPerPage();
        }
    }

    /**
     * check if form has a filter
     *
     * This funciton returns bool(true) if a filter has been set on any of the forms columns,
     * and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function hasFilter()
    {
        /* @var $field DDLDefaultField */
        foreach ($this->getFields() as $field)
        {
            if ($field->hasFilter()) {
                return true;
            }
        }
        return false;
    }

    /**
     * drop all filters
     *
     * Unsets all filters for this form.
     *
     * @access  public
     */
    public function dropFilters()
    {
        $this->lastPage = null;
        $this->listOfEntries = null;
        /* @var $field DDLDefaultField */
        foreach ($this->getFields() as $field)
        {
            $field->dropFilter();
        }
    }

    /**
     * select a template for output
     *
     * Forms offer mulitple alternative form layouts to choose from.
     * These are numbered (0..n), where 0 is always the default.
     *
     * @access  public
     * @param   int  $layout  template settings (int 0...n)
     */
    public function setLayout($layout = 0)
    {
        assert('is_int($layout); // Wrong type for argument 1. Integer expected');
        assert('$layout >= 0; // Invalid argument. Index must not be negative');
        $this->layout = (int) $layout;
    }

    /**
     * get selected a layout for output
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
        assert('is_int($this->layout); // Member "layout" is expected to be an integer.');
        return $this->layout;
    }

    /**
     * set number of entries per page
     *
     * The number of entries per page needs to greater 0, defaults to 5.
     *
     * @access  public
     * @param   int  $entries  number of entries per page
     */
    public function setEntriesPerPage($entries = 5)
    {
        assert('is_int($entries); // Wrong type for argument 1. Integer expected');

        if ($entries < 1) {
            trigger_error("Number of entries per page was less than one. " .
                "Will use the default value 5 instead.", E_USER_NOTICE);
            $entries = 5;
        }

        $query = $this->getQuery();
        assert('$query instanceof DbSelect;');
        $query->setLimit($entries);
        $this->lastPage = null;
    }

    /**
     * get number of entries to show per page
     *
     * Returns the number of the current page, or bool(false)
     * on error.
     *
     * @access  public
     * @return  int
     */
    public function getEntriesPerPage()
    {
        $query = $this->getQuery();
        assert('$query instanceof DbSelect;');
        $limit = $query->getLimit();
        if ($limit > 0) {
            return $limit;
        } else {
            $query->setLimit(5);
            return $query->getLimit();
        }
    }

    /**
     * check if the current page is the last page
     *
     * Returns bool(true) if the current page number + visible entries per page
     * is less than the overall number of rows.
     *
     * @access  public
     * @return  bool
     */
    public function isLastPage()
    {
        return ($this->getPage() + $this->getEntriesPerPage() >= $this->getLastPage());
    }

    /**
     * get name of field to order output by
     *
     * This returns the name of the custom field, that a user selected to sort the table contents.
     *
     * @access  public
     * @return  string
     */
    public function getOrderByField()
    {
        assert('is_string($this->orderByField); // Member "orderByField" is expected to be a string.');
        return $this->orderByField;
    }

    /**
     * get name of field to order output by
     *
     * This returns the name of the custom field, that a user selected to sort the table contents.
     *
     * @access  public
     * @param   string  $fieldName  name of field to order by
     * @param   bool    $desc       descending order (true=yes, false=no)
     * @throws  NotFoundException when field does not exist.
     */
    public function setOrderByField($fieldName, $desc = false)
    {
        assert('is_string($fieldName); // Wrong argument type argument 1. String expected');
        assert('is_bool($desc); // Wrong argument type argument 2. Boolean expected');

        $field = $this->getField($fieldName);
        if (!$field->refersToTable()) {
            return; // can't order by a virtual column, that is not part of the table
        }
        $columnName = $field->getColumnDefinition()->getName();
        assert('is_string($columnName) && !empty($columnName);');
        $query = $this->getQuery();
        assert('$query instanceof DbSelect;');
        $query->setOrderBy(array($columnName), array($desc));
        $this->orderByField = $fieldName;
        $this->isDescending = !empty($desc);
    }

    /**
     * check if resultset is sorted in descending order
     *
     * Returns a boolean value: true = descending, false = ascending.
     *
     * @access  public
     * @return  bool
     */
    public function isDescending()
    {
        assert('is_bool($this->isDescending); // Member "isDescending" is expected to be bool.');
        return !empty($this->isDescending);
    }

    /**
     * get foreign key
     *
     * If the form is associated with the parent form via a foreign key,
     * this function will return it. If there is none, it will return NULL instead.
     *
     * If no key is set this function will try to resolve it.
     *
     * @access  public
     * @return  string
     */
    public function getKey()
    {
        if (!isset($this->key)) {
            if ($this->parent instanceof DDLForm) {
                $parentTable = $this->parent->getTable();
                if ($parentTable !== $this->getTable()) {
                    $table = $this->getTableDefinition();
                    /* @var $foreign DDLForeignKey */
                    foreach ($table->getForeignKeys() as $foreign)
                    {
                        if ($foreign->getTargetTable() === $parentTable) {
                            $columns = $foreign->getColumns();
                            reset($columns);
                            if ($table->getPrimaryKey() !== key($columns)) {
                                $this->key = key($columns);
                            }
                        }
                    }
                }
            }
        }
        return parent::getKey();
    }

    /**
     * set current page
     *
     * The first page is 0, the second is 1, aso., defaults to 0.
     *
     * @access  public
     * @param   int  $page  number of start page
     */
    public function setPage($page = 0)
    {
        $query = $this->getQuery();
        assert('$query instanceof DbSelect;');
        if ($query->getOffset() !== $page) {
            $this->listOfEntries = null;
            parent::setPage($page);
        }
    }

    /**
     * create links to other pages
     *
     * @access  public
     * @return  string
     * @ignore
     */
    public function getListOfEntries()
    {
        if (!isset($this->listOfEntries)) {
            $lastPage = $this->getLastPage();
            $entriesPerPage = $this->getEntriesPerPage();
            assert('$entriesPerPage > 0; // invalid number of entries to view per page');
            $currentPage = $this->getPage();
            $this->listOfEntries = "";
            assert('!isset($pluginManager); // Cannot redeclare var $pluginManager');
            $pluginManager = PluginManager::getInstance();
            $action = $pluginManager->getFirstEvent();
            $lang = Language::getInstance();
            $linkTemplate = '<a class="gui_generator_%s" href=' .
                SmartUtility::href("action=$action&" . $this->getName() . "[page]=%s") .
                ' title="%s">%s</a>';
            // previous page
            if ($currentPage > 0) { // is not first page
                $page = $currentPage - $entriesPerPage;
                if ($page < 0) {
                    $page = 0;
                }
                $this->listOfEntries .= sprintf($linkTemplate, 'previous', $page,
                    $lang->getVar("TITLE_PREVIOUS"), $lang->getVar("BUTTON_PREVIOUS"));
            }
            // more pages
            if ($lastPage > ($entriesPerPage * 2)) { // has more than 2 pages

                $dots = false;

                $title = $lang->getVar("TITLE_LIST");
                $isTooLong = $lastPage > (10 * $entriesPerPage); // has more than 10 pages

                for ($page = 0; $page < ceil($lastPage / $entriesPerPage); $page++)
                {
                    /**
                     * if more than 10 pages exist and current page is not first page or last page
                     * and is not current page or previous or next 3 pages
                     */
                    $isNearCurrent = (floor($currentPage / $entriesPerPage) - 3) < $page && // previous 3 pages, or
                        $page < (floor($currentPage / $entriesPerPage) + 3);                // next 3 pages

                    $isFirstOrLast = $page <= 1 ||                         // is first page, or
                        $page >= (ceil($lastPage / $entriesPerPage) - 2 ); // is last page

                    if ($isTooLong && !$isFirstOrLast && !$isNearCurrent) {
                        /* this marks an elipsis */
                        if ($dots === false) {
                            $this->listOfEntries .= "...";
                            $dots = true;
                        } else {
                            /* ignore this page */
                            continue;
                        }
                    } else {
                        $first = ($page * $entriesPerPage);
                        if (($page + 1) * $entriesPerPage < $lastPage) {
                            $last = ($page + 1) * $entriesPerPage;
                        } else {
                            $last = $lastPage;
                        }
                        // link text
                        if ($first + 1 != $last) {
                            $text = '[' . ($first + 1) . '-' . $last . ']';
                        } else {
                            $text = '[' . $last . ']';
                        }
                        if ($currentPage < $first || $currentPage > $last - 1) { // is not current page
                            $this->listOfEntries .= sprintf($linkTemplate, 'page', $first,
                                $title, $text);
                        } else {
                            $this->listOfEntries .= $text;
                        }
                        if ($isNearCurrent) {
                            $dots = false;
                        }
                    }
                } // end for
            }
            // next page
            if (!$this->isLastPage()) { // is not last page
                $page = $currentPage + $entriesPerPage;
                $this->listOfEntries .= sprintf($linkTemplate, 'next', $page,
                    $lang->getVar("TITLE_NEXT"), $lang->getVar("BUTTON_NEXT"));
            }
        }
        return $this->listOfEntries;
    }

    /**
     * set search term
     *
     * This function will work the form and all sub-forms, iterate over all fields and
     * add "OR foo LIKE '%search%'" tests to the where clause of the query.
     *
     * @access  protected
     * @param   string  $searchTerm  term entered in global search box
     * @ignore
     */
    protected function setSearchTerm($searchTerm)
    {
        // clear cache
        $this->listOfEntries = null;
        $this->lastPage = null;
        // process fields
        if (!empty($this->fields)) {
            /* @var $field DDLDefaultField */
            foreach ($this->fields as $field)
            {
                if ($field->isSelectable() && $field->isVisible() && $field->isFilterable()) {
                    if (empty($searchTerm)) {
                        $field->dropFilter();
                    } else {
                        $field->setFilter("%$searchTerm%", false);
                    }
                }
            }
        }
        // process subforms
        foreach ($this->forms as $form)
        {
            if (!$form->getKey() || $this->getEntriesPerPage() === 1) {
                $form->setSearchTerm($searchTerm);
            }
        }
    }

    /**
     * initialize instance
     *
     * @access  public
     * @ignore
     */
    public function  __wakeup()
    {
        parent::__wakeup();
        $values = $this->getValues();
        if (isset($values['entries'])) {
            $this->listOfEntries = null;
            $this->setEntriesPerPage((int) $values['entries']);
        }
        if (isset($values['layout'])) {
            $this->setLayout((int) $values['layout']);
        }
        if (isset($values['search'])) {
            $this->setSearchTerm($values['search']);
        }
        if (!empty($values['dropfilter'])) {
            $this->dropFilters();
        }
        if (isset($values['filter']) && is_array($values['filter'])) {
            $this->lastPage = null;
            $this->listOfEntries = null;
            foreach ($values['filter'] as $fieldName => $searchTerm)
            {
                $field = $this->getField($fieldName);
                if ($field instanceof DDLDefaultField && $field->isVisible() && $field->isFilterable()) {
                    if (!empty($searchTerm)) {
                        $field->setFilter("$searchTerm");
                    } else {
                        $field->dropFilter();
                    }
                }
            }
        }
        $isDelete = Request::getVars('selected_entries');
        if (isset($values['ddldefaultsearchiterator']) || !empty($isDelete)) {
            $this->lastPage = null;
            $this->listOfEntries = null;
        }
        if (!empty($values['orderby'])) {
            $field = $this->setOrderByField($values['orderby'], !empty($values['desc']));
        }
    }

    /**
     * has insertable sub-form
     *
     * Returns bool(true) if the form has embedded sub-forms and at least one
     * of them is insertable and has an insert action.
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function hasInsertableChildren()
    {
        /* @var $form DDLDefaultForm */
        foreach ($this->forms as $form)
        {
            if ($form->getInsertAction()) {
                return true;
            }
        }
        return false;
    }

    /**
     * has searchable sub-form
     *
     * Returns bool(true) if the form has embedded sub-forms and at least one
     * of them has a search action.
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function hasSearchableChildren()
    {
        /* @var $form DDLDefaultForm */
        foreach ($this->forms as $form)
        {
            if ($form->getSearchAction()) {
                return true;
            }
        }
        return false;
    }

    /**
     * create a form from the current instance and return it
     *
     * Returns the HTML-code for this form.
     *
     * @access  public
     * @return  string
     */
    public function toString()
    {
        // setting up template
        $file = Skin::getInstance()->getFile('gui_form');
        assert('is_file($file); // Template file not found');
        $template = new SmartView($file);
        unset($file);

        $template->setVar('form', $this);
        return $template->toString();
    }
}

?>