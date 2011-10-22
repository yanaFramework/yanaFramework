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
 * <<facade>> Form wrapper base class.
 *
 * @access      public
 * @package     yana
 * @subpackage  form
 * @ignore
 */
class FormFacade extends Object
{

    /**
     * List of sub-forms.
     *
     * @access  private
     * @var     array
     */
    private $_forms = array();

    /**
     * List of searchable fields.
     *
     * @access  private
     * @var     FormFieldFacadeCollection
     */
    private $_searchForm = null;

    /**
     * List of updatable fields.
     *
     * @access  private
     * @var     FormFieldFacadeCollection
     */
    private $_updateForm = null;

    /**
     * List of insertable fields.
     *
     * @access  private
     * @var     FormFieldFacadeCollection 
     */
    private $_insertForm = null;

    /**
     * Base table.
     *
     * @access  private
     * @var     DDLTable
     */
    private $_table = null;

    /**
     * Form definition
     *
     * @access  private
     * @var     DDLForm
     */
    private $_form = null;

    /**
     * Form setup
     *
     * @access  private
     * @var     FormSetup
     */
    private $_setup = null;

    /**
     * If the current form is a child element, this will point to it's parent.
     *
     * Leave blank if it is a root element.
     *
     * @access  private
     * @var     FormFacade
     */
    private $_parent = null;

    /**
     * create new instance
     *
     * @access  public
     */
    public function __construct()
    {
        $this->_setup = new FormSetup();
    }

    /**
     * Relay function call to wrapped object.
     *
     * @access  public
     * @param   string  $name       method name
     * @param   array   $arguments  list of arguments to pass to function
     * @return  mixed
     * @throws  NotImplementedException  when the function is not found
     */
    public function __call($name, $arguments)
    {
        if (isset($this->_form) && method_exists($this->_form, $name)) {
            return call_user_func_array(array($this->_form, $name), $arguments);
        } elseif (method_exists($this->_setup, $name)) {
            return call_user_func_array(array($this->_setup, $name), $arguments);
        } else {
            throw new NotImplementedException("Call to undefined function: '$name' in class " . __CLASS__ . ".");
        }
    }

    /**
     * Add a form element.
     *
     * @access  public
     * @param   DDLForm  $form  new form that will be wrapped
     * @return  FormFacade
     */
    public function addForm(FormFacade $form)
    {
        $name = $form->getName();
        $this->_forms[$name] = $form;
        return $this->_forms[$name];
    }

    /**
     * Returns the sub-form as a FormFacade element.
     *
     * @access  public
     * @param   string  $name  name of requested sub-form.
     * @return  FormFacade
     * @throws  InvalidArgumentException  when form does not exist
     */
    public function getForm($name)
    {
        return $this->_forms[$name];
    }

    /**
     * Returns the underlying form definition.
     *
     * This allows to access the underlying form directly, instead of using the facade.
     *
     * @access  public
     * @return  DDLForm
     */
    public function getBaseForm()
    {
        return $this->_form;
    }

    /**
     * Set form object.
     *
     * @access  public
     * @param   DDLForm  $form  configuring the contents of the form
     * @return  FormFacade
     */
    public function setBaseForm(DDLForm $form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Get parent form.
     *
     * Forms may have sub-forms, which then have a parent.
     * This function returns it.
     *
     * The result may be null, if there is no parent at all.
     * Check the result object!
     *
     * @access  public
     * @return  FormFacade
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Set parent form.
     *
     * If the current form is a child element, this will point to it's parent.
     * Set to NULL if it is a root element and there is no parent.
     *
     * @access  public
     * @param   FormFacade  $parentForm  configuring the contents of the parent form
     * @return  FormFacade
     */
    public function setParent(FormFacade $parentForm = null)
    {
        $this->_parent = $parentForm;
        return $this;
    }

    /**
     * Get the setup configuration of this form.
     *
     * @access  public
     * @return  FormSetup
     */
    public function getSetup()
    {
        return $this->_setup;
    }

    /**
     * Set form setup.
     *
     * @access  public
     * @param   FormSetup  $setup  configuring the behavior of the form
     * @return  FormFacade
     */
    public function setSetup(FormSetup $setup)
    {
        $this->_setup = $setup;
        return $this;
    }

    /**
     * Returns an array of sub-forms as FormFacade elements.
     *
     * @access  public
     * @return  array 
     */
    public function getForms()
    {
        return $this->_forms;
    }

    /**
     * Check if the form has an insertable sub-form.
     *
     * Returns bool(true) if the form has embedded sub-forms and at least one of them has an insert action.
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function hasInsertableChildren()
    {
        /* @var $form DDLForm */
        foreach ($this->_form->getForms() as $form)
        {
            if ($form->getEvent('insert')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the form has an updatable sub-form.
     *
     * Returns bool(true) if the form has embedded sub-forms and at least one of them has an update action.
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function hasUpdatableChildren()
    {
        /* @var $form DDLForm */
        foreach ($this->_form->getForms() as $form)
        {
            if ($form->getEvent('update')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the form has a searchable sub-form.
     *
     * Returns bool(true) if the form has embedded sub-forms and at least one of them has a search action.
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function hasSearchableChildren()
    {
        /* @var $form DDLDefaultForm */
        foreach ($this->_form->getForms() as $form)
        {
            if ($form->getEvent('search')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get searchable form.
     *
     * @access  public
     * @return  FormContextSensitiveWrapper
     */
    public function getSearchForm()
    {
        if (!isset($this->_searchForm)) {
            $context = $this->_setup->getContext('search');
            $this->_searchForm = new FormContextSensitiveWrapper($this, $context);
        }
        return $this->_searchForm;
    }

    /**
     * Get updatable form.
     *
     * @access  public
     * @return  FormContextSensitiveWrapper
     */
    public function getUpdateForm()
    {
        if (!isset($this->_updateForm)) {
            $context = $this->_setup->getContext('update');
            $this->_updateForm = new FormContextSensitiveWrapper($this, $context);
        }
        return $this->_updateForm;
    }

    /**
     * Get insertable form.
     *
     * @access  public
     * @return  FormContextSensitiveWrapper
     */
    public function getInsertForm()
    {
        if (!isset($this->_insertForm)) {
            $context = $this->_setup->getContext('insert');
            $this->_insertForm = new FormContextSensitiveWrapper($this, $context);
        }
        return $this->_insertForm;
    }

    /**
     * Get values of update form.
     *
     * This returns an array of values entered in the update form.
     *
     * @access  public
     * @return  array
     */
    public function getUpdateValues()
    {
        return $this->_setup->getContext('update')->getRows()->toArray();
    }

    /**
     * get values of insert form
     *
     * This returns an array of values entered in the insert form.
     *
     * @access  public
     * @return  array
     */
    public function getInsertValues()
    {
        return $this->_setup->getContext('insert')->getValues();
    }

    /**
     * Get values of search form.
     *
     * This returns an array of values entered in the search form.
     *
     * @access  protected
     * @return  array
     */
    public function getSearchValues()
    {
        return $this->_setup->getContext('search')->getValues();
    }

    /**
     * Get table definition.
     *
     * Each form definition must be linked to a table in the same database.
     * This function looks it up and returns this definition.
     *
     * @access  public
     * @return  DDLTable
     * @throws  NotFoundException  when the database, or table was not found
     */
    public function getTable()
    {
        if (!isset($this->_table)) {
            $tableName = $this->_form->getTable();
            $database = $this->_form->getDatabase();
            if (!($database instanceof DDLDatabase)) {
                $message = "Error in form '" . $this->_form->getName() . "'. No parent database defined.";
                throw new NotFoundException($message);
            }
            $table = $database->getTable($tableName);
            if (!($table instanceof DDLTable)) {
                $message = "Error in form '" . $this->_form->getName() . "'. Parent table '" . $tableName . "' not found.";
                throw new NotFoundException($message);
            }
            $this->_table = $table;
        }
        return $this->_table;
    }

    /**
     * Get title.
     *
     * The title is a label text that should be displayed in the UI when viewing this object.
     *
     * It is optional. If it is not set, the function returns NULL instead.
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        $title = $this->_form->getTitle();
        if (empty($title)) {
            try {
                $title = $this->getTable()->getTitle();
            } catch (\Exception $e) {
                $title = $this->_form->getName(); // fall back to name if table does not exist
            }
        }
        return $title;
    }

    /**
     * Convert to HTML code.
     * 
     * @access  public
     * @return  string
     */
    public function __toString()
    {
        $htmlBuilder = new FormHtmlBuilder($this);
        return $htmlBuilder->__invoke();
    }

}

?>