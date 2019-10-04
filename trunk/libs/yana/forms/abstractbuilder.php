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
 * <<abstract>> Form builder.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
abstract class AbstractBuilder extends \Yana\Core\StdObject implements \Yana\Forms\IsBuilder
{

    use \Yana\Forms\Dependencies\HasContainer;

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
     * @var  \Yana\Forms\Setups\IsBuilder
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
     * Set name of database file.
     *
     * Schema the form is based upon.
     * 
     * @param   string  $file  name of database file
     * @return  $this
     */
    protected function _setFile($file)
    {
        assert('is_string($file); // Invalid argument $file: String expected');
        $this->_file = (string) $file;
        return $this;
    }

    /**
     * Get setup-builder.
     *
     * @return  \Yana\Forms\Setups\IsBuilder
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
        if (!isset($this->_queryBuilder)) {
            $this->_queryBuilder = new \Yana\Forms\Worker($this->_getDatabase(), $this->_getFacade());
        }
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
     * @return  $this
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
     * @return  $this 
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
     * @return  $this 
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
     * @return  $this 
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
     * @return  $this
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
     * @return  $this 
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
     * @return  $this 
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
     * @return  $this 
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
     * The default is 20.
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
     * @return  $this 
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
     * @return  $this 
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
     * @return  $this 
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
     * @return  $this 
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
     * @param   string  $onsearch  form action name
     * @return  $this 
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
     * @return  $this 
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
     * @return  $this 
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
     * @return  $this 
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
     * @param   \Yana\Db\Ddl\Form  $form  base form definition
     * @return  $this 
     */
    protected function _setForm(\Yana\Db\Ddl\Form $form, \Yana\Forms\Facade $parentForm = null)
    {
        $this->_form = $form;
        $this->_getFacade()->setBaseForm($this->_form);
        if ($this->_setupBuilder) {
            $this->_setupBuilder->setForm($this->_form);
        } else {
            $this->_setupBuilder = new \Yana\Forms\Setups\Builder($this->_form, $this->_getDependencyContainer());
        }
        if ($parentForm) {
            $this->_getFacade()->setParent($parentForm);
        }
        return $this;
    }

    /**
     * Build \Yana\Db\Ddl\Form object.
     *
     * @return  \Yana\Db\Ddl\Form
     * @throws  \Yana\Core\Exceptions\BadMethodCallException    when a parameter is missing
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when a paraemter is not valid
     */
    protected function _getForm()
    {
        if (!isset($this->_form)) {
            $this->_form = $this->_buildForm();
            $this->_setForm($this->_form);
        }
        return $this->_form;
    }

    /**
     * Build \Yana\Db\Ddl\Form object.
     *
     * @return  \Yana\Db\Ddl\Form
     * @throws  \Yana\Core\Exceptions\BadMethodCallException    when a parameter is missing
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when a paraemter is not valid
     */
    abstract protected function _buildForm();

    /**
     * Return connection to database bound to this form.
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getDatabase()
    {
        if (!isset($this->_database)) {
            $this->_database = $this->_getDependencyContainer()->getConnectionFactory()->createConnection($this->getFile());
        }
        return $this->_database;
    }

    /**
     * Return connection to database bound to this form.
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getDatabaseSchema()
    {
        return $this->_getDatabase()->getSchema();
    }

    /**
     * Return form facade.
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getFacade()
    {
        if (!isset($this->_facade)) {
            $this->_facade = new \Yana\Forms\Facade();
        }
        return $this->_facade;
    }

    /**
     * Register a cache adapter.
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $cache  a valid cache adapter
     * @return  $this
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
        if (!isset($this->_cache)) {
            $this->_cache = new \Yana\Data\Adapters\SessionAdapter(__CLASS__);
        }
        return $this->_cache;
    }

    /**
     * <<magic>> Implements IsCloneable.
     *
     * Provides a shallow-copy (not a deep-copy as by default).
     *
     * @ignore
     * @codeCoverageIgnore
     */
    public function __clone()
    {
        // nothing to do
    }

}

?>