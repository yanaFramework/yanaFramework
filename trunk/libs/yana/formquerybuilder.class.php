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
 * <<builder>> Build a queries based on a given form.
 *
 * @access      public
 * @package     yana
 * @subpackage  form
 */
class FormQueryBuilder extends Object
{

    /**
     * Database connection used to create the querys.
     *
     * @access  private
     * @var     DbStream
     */
    private $_db = null;

    /**
     * DDL definition object of selected table
     *
     * @access  private
     * @var     DDLTable
     */
    private $_table = null;

    /**
     * Definition of form.
     *
     * @access  private
     * @var     FormFacade
     */
    private $_form = null;

    /**
     * Object cache.
     *
     * @access  private
     * @var     array
     */
    private $_cache = array();

    /**
     * Initialize instance.
     *
     * @access  public
     * @param   DbStream    $db    database connection used to create the querys
     * @param   FormFacade  $form  base form defintion that the query will apply to
     */
    public function __construct(DbStream $db, FormFacade  $form)
    {
        $this->_db = $db;
        $this->_form = $form;
    }

    /**
     * Set form object.
     *
     * @access  public
     * @param   FormFacade  $form  configuring the contents of the form
     * @return  FormQueryBuilder
     */
    public function setForm(FormFacade $form)
    {
        $this->_form = $form;
        $this->_cache = array();
        return $this;
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
            $name = $this->_form->getTable();
            $database = $this->_form->getDatabase();
            if (!($database instanceof DDLDatabase)) {
                $message = "Error in form '" . $this->_form->getName() . "'. No parent database defined.";
                throw new NotFoundException($message);
            }
            $tableDefinition = $database->getTable($name);
            if (!($tableDefinition instanceof DDLTable)) {
                $message = "Error in form '" . $this->_form->getName() . "'. Parent table '$name' not found.";
                throw new NotFoundException($message);
            }
            $this->_table = $tableDefinition;
        }
        return $this->_table;
    }

    /**
     * Create a select query.
     *
     * This returns the query object which is bound to the form.
     * You can modify this to filter the visible results.
     *
     * @access  public
     * @return  DbSelect
     * @throws  NotFoundException  if the selected table or one of the selected columns is not found
     */
    public function buildSelectQuery()
    {
        if (!isset($this->_cache[__FUNCTION__])) {
            $setup = $this->_form->getSetup();
            $query = new DbSelect($this->_db);
            $query->setTable($this->_form->getBaseForm()->getTable());
            $query->setLimit($setup->getEntriesPerPage());
            $query->setOffset($setup->getPage() * $setup->getEntriesPerPage());
            if ($setup->getOrderByField()) {
                $query->setOrderBy((array) $setup->getOrderByField(), (array) $setup->isDescending());
            }
            if ($setup->hasFilter()) {
                foreach ($setup->getFilters() as $columnName => $filter)
                {
                    $havingClause = array($columnName, 'like', $filter);
                    $query->addHaving($havingClause);
                }
            }
            if ($setup->getContext('update')->getColumnNames()) {
                $query->setColumns($setup->getContext('update')->getColumnNames()); // throws NotFoundException
            }
            $this->_cache[__FUNCTION__] = $query;
        }
        return $this->_cache[__FUNCTION__];
    }

    /**
     * Create a count query.
     *
     * This returns a query object bound to the form, that can be used to count the pages.
     *
     * @access  protected
     * @return  DbSelectCount
     */
    public function buildCountQuery()
    {
        if (!isset($this->_cache[__FUNCTION__])) {
            $query = $this->buildSelectQuery();
            assert('$query instanceof DbSelectCount;');
            $query->setLimit(0);
            $query->setOffset(0);
            $this->_cache[__FUNCTION__] = $query;
        }
        return $this->_cache[__FUNCTION__];
    }

}

?>