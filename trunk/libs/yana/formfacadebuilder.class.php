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
class FormFacadeBuilder extends FormFacadeAbstract
{

    /**
     * Builder product.
     *
     * @access  protected
     * @var     FormFacade
     */
    protected $object = null;

    /**
     * Included builder.
     *
     * @access  protected
     * @var     FormSetupBuilder
     */
    protected $formSetupBuilder = null;

    /**
     * database schema
     *
     * @access  private
     * @var     DDLDatabase
     */
    private $_database = null;

    /**
     * DDL definition object of selected table
     *
     * @access  private
     * @var     DDLTable
     */
    private $_table = null;

    /**
     * Initialize instance
     *
     * @access  public
     * @param   DDLDatabase  $database  base database to build forms upon
     */
    public function __construct(DDLDatabase $database)
    {
        $this->_database = $database;
        $this->createNewFacade();
    }

    /**
     * Transparent wrapping functions.
     *
     * @access  public
     * @param   string  $name  function name
     * @param   array   $args  function arguments
     * @return  mixed
     */
    public function __call($name, array $args)
    {
        if (method_exists($this->formSetupBuilder, $name)) {
            return call_user_func_array(array($this->formSetupBuilder, $name), $arguments);
        } else {
            throw new NotImplementedException("Call to undefined function: '$name' in class " . __CLASS__ . ".");
        }
    }

    /**
     * Create new facade instance.
     *
     * @access  public
     */
    public function createNewFacade()
    {
        $this->object = new FormFacade();
    }

    /**
     * Build facade object.
     * 
     * @access  public
     * @return  FormFacade 
     */
    public function buildFacade()
    {
        $this->object->setup = $this->formSetupBuilder->buildSetup();
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
        return $this->object->form;
    }

    /**
     * Set form object.
     *
     * @access  public
     * @param   DDLForm  $form  configuring the contents of the form
     * @return  FormFacadeBuilder 
     */
    public function setForm(DDLForm $form)
    {
        $this->object->form = $form;
        // initialize setup too
        if (!isset($this->formSetupBuilder)) {
            $this->formSetupBuilder = new FormSetupBuilder($form);
        } else {
            $this->formSetupBuilder->setForm($form);
        }
        return $this;
    }

    /**
     * Get form setup.
     *
     * @access  public
     * @return  FormSetup
     */
    public function getSetup()
    {
        return $this->object->setup;
    }

    /**
     * Set form setup.
     *
     * @access  public
     * @param   FormSetup  $setup  configuring the behavior of the form
     * @return  FormFacadeBuilder 
     */
    public function setSetup(FormSetup $setup)
    {
        $this->formSetupBuilder->setSetup($setup);
        $this->object->setup = $setup;
        return $this;
    }

    /**
     * Get query.
     *
     * @access  public
     * @return  DbSelect
     */
    public function getQuery()
    {
        return $this->object->query;
    }

    /**
     * Get setup builder.
     *
     * The setup builder is an internal builder that allows more detailed settings on the form's configuration.
     *
     * @access  public
     * @return  FormSetupBuilder
     */
    public function getSetupBuilder()
    {
        return $this->formSetupBuilder;
    }

    /**
     * get list of foreign-key reference settings
     *
     * This returns an array of the following contents:
     * <code>
     * array(
     *   'primaryKey1' => array(
     *     'table' => 'name of target table'
     *     'column' => 'name of target column'
     *     'label' => 'name of a column in target table that should be used as a label'
     * }
     * </code>
     *
     * @access  private
     * @return  array
     * @ignore
     */
    private function _getReferences()
    {
        $references = array();
        assert('!isset($field);');
        /* @var $field DDLDefaultField */
        foreach ($this->getForm()->getFields() as $field)
        {
            if ($field->getType() !== 'reference') {
                continue;
            }
            assert('!isset($column);');
            $column = $field->getColumnDefinition();
            $reference = $column->getReferenceSettings();
            if (!isset($reference['column'])) {
                $reference['column'] = $column->getReferenceColumn()->getName();
            }
            if (!isset($reference['label'])) {
                $reference['label'] = $reference['column'];
            }
            if (!isset($reference['table'])) {
                $reference['table'] = $column->getReferenceColumn()->getParent()->getName();
            }
            $references[$field->getName()] = $reference;
            unset($column);
        } // end foreach
        unset($field);
        return $references;
    }

    /**
     * Get reference values.
     *
     * This function returns an array, where the keys are the values of a unique key in the
     * target table and the values are the labels for those keys.
     *
     * Use this function for AJAX auto-completion in reference column.
     *
     * The list can be limited to a maximum length by setting the $limit argument. Default is 50 rows.
     * The search term allows to find all rows whose labels start with a given text.
     * You may use the wildcards '%' and '_'.
     *
     * Note: you may want to introduce an index on the label-column of your database.
     *
     * If the field is no reference in the current form, then an empty array will be returned.
     *
     * @access  protected
     * @param   string  $fieldName   name of field to look up
     * @param   string  $searchTerm  find all entries that start with ...
     * @param   int     $limit       maximum number of hits, set to 0 to get all
     * @return  array
     * @ignore
     */
    protected function _getReferenceValues($fieldName, $searchTerm = "", $limit = 50)
    {
        assert('is_string($fieldName); // Invalid argument $fieldName: string expected');
        assert('is_string($searchTerm); // Invalid argument $searchTerm: string expected');
        $referenceValues = array();
        $references = $this->_getReferences();
        if (isset($references[$fieldName])) {
            $reference = $references[$fieldName];
            $db = $this->getForm()->getDatabase()->getName();
            $select = new DbSelect(Yana::connect($db));
            $select->setTable($reference['table']);
            $columns = array('LABEL' => $reference['label'], 'VALUE' => $reference['column']);
            $select->setColumns($columns);
            if ($limit > 0) {
                $select->setLimit($limit);
            }
            $select->setOrderBy($reference['label']);
            if (!empty($searchTerm)) {
                $select->setWhere(array($reference['label'], 'like', $searchTerm . '%'));
            }
            $values = array();
            foreach ($select->getResults() as $row)
            {
                $values[$row['VALUE']] = $row['LABEL'];
            }
            $referenceValues = $values;
        }
        return $referenceValues;
    }

    /**
     * create form object settings from database query
     *
     * This function takes a database query and initializes the form using the
     * table and columns of the query.
     *
     * @access  public
     * @return  DDLForm
     */
    public function buildFormFromTable(DDLTable $table)
    {
        $genericName = $this->_database->getName() . '-' . $table->getName();

        // check if the form already exists
        if ($this->_database->isForm($genericName)) {
            return $this->object->form = $this->_database->getForm($genericName);
        }
        // otherwise create a new form

        $form = $this->object->form;
        if (! $this->object->form instanceof DDLForm) {
            $form = new DDLForm($genericName); // from scratch
        } else {
            $form->setName($genericName); // from cache
        }
        $form->setTable($table->getName());

        // get table definition
        $title = $table->getTitle();
        // fall back to table name if title is empty
        if (empty($title)) {
            $title = $table->getName();
        }
        $form->setTitle($title);

        // copy security settings from table to form
        assert('!isset($grant); // Cannot redeclare var $grant');
        foreach ($table->getGrants() as $grant)
        {
            $form->setGrant($grant);
        }
        $this->setForm($form);

        return $form;
    }

    /**
     * create form object settings from database query
     *
     * This function takes a database query and initializes the form using the
     * table and columns of the query.
     *
     * @access  public
     * @return  DDLForm
     */
    public function setQuery(DbSelect $query)
    {
        if ($query->getExpectedResult() != DbResultEnumeration::TABLE) {
            $columns = array();
            foreach ($query->getColumns() as $alias => $columnDef)
            {
                $columnName = $columnDef[1];
                // get column definition
                $columns[$alias] = $table->getColumn($columnName); // @todo FIXME
            }
        } else {
            $columns = $table->getColumns(); // @todo FIXME
        }
        assert('!isset($alias); // Cannot redeclare var $alias');
        assert('!isset($columnDef); // Cannot redeclare var $columnDef');
        foreach ($columns as $alias => $columns)
        {
            $this->_addFieldByColumn($columns, $alias);
        }
        $this->object->query = $query;

        return $this->object->form;
    }

    /**
     * Update the form values.
     *
     * @access  public
     * @param   array  $request  initial values (e.g. Request array)
     * @return  FormFacadeBuilder 
     */
    public function updateValues(array $request = array())
    {
        $this->formSetupBuilder->updateValues($request);
        return $this;
    }

    /**
     * Update setup with request array.
     *
     * @access  public
     * @param   array  $request  initial values (e.g. Request array)
     * @return  FormFacadeBuilder 
     */
    public function updateSetup(array $request = array())
    {
        $this->formSetupBuilder->updateSetup($request);
        return $this;
    }

    /**
     * add field by column definition
     *
     * @access  private
     * @param   DDLForm    $form     form definition
     * @param   DDLColumn  $columns  column definition
     * @param   string     $alias    column name alias
     */
    private function _addFieldByColumn(DDLForm $form, DDLColumn $columns, $alias = "")
    {
        $columnName = $columns->getName();

        // set alias to equal column name, if none is present
        if (!is_string($alias) || empty($alias)) {
            $alias = $columnName;
        }
        $field = null;
        try {
            $field = $form->addField($alias, 'DDLAutoField');
        } catch (AlreadyExistsException $e) {
            return; // field already exists - nothing to do!
        }

        // set the column title (aka "label")
        assert('!isset($title); // Cannot redeclare var $title');
        $title = $columns->getTitle();
        if (!empty($title)) {
            $field->setTitle($title);
        } elseif ($columns->isPrimaryKey()) {
            $field->setTitle("ID");
        } else {
            // fall back to column name if title is empty
            $field->setTitle($columns->getName());
        }
        unset($title);

        // copy column grants to field
        foreach ($columns->getGrants() as $grant)
        {
            $field->setGrant($grant);
        }
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

}

?>