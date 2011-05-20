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
     * database schema
     *
     * @access  private
     * @var     DDLDatabase
     */
    private $_database = null;

    /**
     * Initialize instance
     *
     * @access  public
     * @param   DDLDatabase  $database  base database to build forms upon
     */
    public function __construct(DDLDatabase $database)
    {
        $this->_database = $database;
        $this->object = new FormFacade();
    }

    /**
     * Build facade object.
     * 
     * @access  public
     * @return  FormFacade 
     */
    public function __invoke()
    {
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
        return $this;
    }

    /**
     * Get parent form.
     *
     * Carefull: may return NULL, if there is no parent.
     * Check results!
     *
     * @access  public
     * @return  FormFacade
     */
    public function getParentForm()
    {
        return $this->object->parent;
    }

    /**
     * Set parent form.
     *
     * If the current form is a child element, this will point to it's parent.
     * Set to NULL if it is a root element and there is no parent.
     *
     * @access  public
     * @param   FormFacade  $parentForm  configuring the contents of the parent form
     * @return  FormFacadeBuilder
     */
    public function setParentForm(FormFacade $parentForm = null)
    {
        $this->object->parent = $parentForm;
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
        $this->object->setup = $setup;
        return $this;
    }

}

?>