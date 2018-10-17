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
 * <<facade>> Form wrapper base class.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
class Facade extends \Yana\Core\Object
{

    /**
     * List of sub-forms.
     *
     * @var  array
     */
    private $_forms = array();

    /**
     * List of searchable fields.
     *
     * @var  \Yana\Forms\Fields\FacadeCollection
     */
    private $_searchForm = null;

    /**
     * List of updatable fields.
     *
     * @var  \Yana\Forms\Fields\FacadeCollection
     */
    private $_updateForm = null;

    /**
     * List of insertable fields.
     *
     * @var  \Yana\Forms\Fields\FacadeCollection
     */
    private $_insertForm = null;

    /**
     * Base table.
     *
     * @var  \Yana\Db\Ddl\Table
     */
    private $_table = null;

    /**
     * Form definition
     *
     * @var  \Yana\Db\Ddl\Form
     */
    private $_form = null;

    /**
     * Form setup
     *
     * @var  \Yana\Forms\Setup
     */
    private $_setup = null;

    /**
     * If the current form is a child element, this will point to it's parent.
     *
     * Leave blank if it is a root element.
     *
     * @var  \Yana\Forms\Facade
     */
    private $_parent = null;

    /**
     * <<constructor>> Initialize name and setup.
     */
    public function __construct()
    {
        $this->_setup = new \Yana\Forms\Setup();
    }

    /**
     * Relay function call to wrapped object.
     *
     * @param   string  $name       method name
     * @param   array   $arguments  list of arguments to pass to function
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\UndefinedMethodException  when the called method is not found
     */
    public function __call($name, array $arguments)
    {
        if (isset($this->_form) && method_exists($this->_form, $name)) {
            return call_user_func_array(array($this->_form, $name), $arguments);
        } elseif (method_exists($this->_setup, $name)) {
            return call_user_func_array(array($this->_setup, $name), $arguments);
        } else {
            return parent::__call($name, $arguments);
        }
    }

    /**
     * Add a form element.
     *
     * @param   \Yana\Db\Ddl\Form  $form  new form that will be wrapped
     * @return  \Yana\Forms\Facade
     */
    public function addForm(\Yana\Forms\Facade $form)
    {
        $name = $form->getName();
        $this->_forms[$name] = $form;
        return $this->_forms[$name];
    }

    /**
     * Returns the sub-form as a FormFacade element.
     *
     * @param   string  $name  name of requested sub-form.
     * @return  \Yana\Forms\Facade
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
     * @return  \Yana\Db\Ddl\Form
     */
    public function getBaseForm()
    {
        if (!isset($this->_form)) {
            $this->_form = new \Yana\Db\Ddl\Form("form");
        }
        return $this->_form;
    }

    /**
     * Set form object.
     *
     * @param   \Yana\Db\Ddl\Form  $form  configuring the contents of the form
     * @return  \Yana\Forms\Facade
     */
    public function setBaseForm(\Yana\Db\Ddl\Form $form)
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
     * @return  \Yana\Forms\Facade
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
     * @param   \Yana\Forms\Facade  $parentForm  configuring the contents of the parent form
     * @return  \Yana\Forms\Facade
     */
    public function setParent(\Yana\Forms\Facade $parentForm = null)
    {
        $this->_parent = $parentForm;
        return $this;
    }

    /**
     * Get the setup configuration of this form.
     *
     * @return  \Yana\Forms\Setup
     */
    public function getSetup()
    {
        return $this->_setup;
    }

    /**
     * Set form setup.
     *
     * @param   \Yana\Forms\Setup  $setup  configuring the behavior of the form
     * @return  \Yana\Forms\Facade
     */
    public function setSetup(\Yana\Forms\Setup $setup)
    {
        $this->_setup = $setup;
        return $this;
    }

    /**
     * Returns an array of sub-forms as FormFacade elements.
     *
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
     * @return  bool
     */
    public function hasInsertableChildren()
    {
        /* @var $form \Yana\Db\Ddl\Form */
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
     * @return  bool
     */
    public function hasUpdatableChildren()
    {
        /* @var $form \Yana\Db\Ddl\Form */
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
     * @return  bool
     */
    public function hasSearchableChildren()
    {
        /* @var $form \Yana\Db\Ddl\Form */
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
     * @return  \Yana\Forms\ContextSensitiveWrapper
     */
    public function getSearchForm()
    {
        if (!isset($this->_searchForm)) {
            $context = $this->_setup->getContext(\Yana\Forms\Setups\ContextNameEnumeration::SEARCH);
            $this->_searchForm = new \Yana\Forms\ContextSensitiveWrapper($this, $context);
        }
        return $this->_searchForm;
    }

    /**
     * Get updatable form.
     *
     * @return  \Yana\Forms\ContextSensitiveWrapper
     */
    public function getUpdateForm()
    {
        if (!isset($this->_updateForm)) {
            $context = $this->_setup->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE);
            $this->_updateForm = new \Yana\Forms\ContextSensitiveWrapper($this, $context);
        }
        return $this->_updateForm;
    }

    /**
     * Get insertable form.
     *
     * @return  \Yana\Forms\ContextSensitiveWrapper
     */
    public function getInsertForm()
    {
        if (!isset($this->_insertForm)) {
            $context = $this->_setup->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT);
            $this->_insertForm = new \Yana\Forms\ContextSensitiveWrapper($this, $context);
        }
        return $this->_insertForm;
    }

    /**
     * Get values of update form.
     *
     * This returns an array of values entered in the update form.
     *
     * @return  array
     */
    public function getUpdateValues()
    {
        return $this->_setup->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE)->getRows()->toArray();
    }

    /**
     * get values of insert form
     *
     * This returns an array of values entered in the insert form.
     *
     * @return  array
     */
    public function getInsertValues()
    {
        return $this->_setup->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT)->getValues();
    }

    /**
     * Get values of search form.
     *
     * This returns an array of values entered in the search form.
     *
     * @return  array
     */
    public function getSearchValues()
    {
        return $this->_setup->getContext(\Yana\Forms\Setups\ContextNameEnumeration::SEARCH)->getValues();
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
    public function getTable()
    {
        if (!isset($this->_table)) {
            if (!isset($this->_form)) {
                throw new \Yana\Core\Exceptions\NotFoundException("No base form defined for table.");
            }
            $tableName = $this->_form->getTable();
            $database = $this->_form->getDatabase();
            if (!($database instanceof \Yana\Db\Ddl\Database)) {
                $message = "Error in form '" . $this->_form->getName() . "'. No parent database defined.";
                throw new \Yana\Core\Exceptions\NotFoundException($message);
            }
            $table = $database->getTable($tableName);
            if (!($table instanceof \Yana\Db\Ddl\Table)) {
                $message = "Error in form '" . $this->_form->getName() . "'. Parent table '" . $tableName . "' not found.";
                throw new \Yana\Core\Exceptions\NotFoundException($message);
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
                unset($e);
            }
        }
        return $title;
    }

    /**
     * Returns the form name.
     *
     * The form name must be a valid id and cannot be empty.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->getBaseForm()->getName();
    }

    /**
     * Convert to HTML code.
     *
     * @return  string
     */
    public function __toString()
    {
        $htmlBuilder = new \Yana\Forms\HtmlBuilder($this);
        return $htmlBuilder->__invoke();
    }

}

?>