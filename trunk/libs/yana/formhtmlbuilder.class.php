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

/**
 * <<builder>> HTML Form builder.
 *
 * This class is meant to create HTML fields for forms.
 *
 * @static
 * @access      public
 * @package     yana
 * @subpackage  form
 */
class FormHtmlBuilder extends Object
{

    /**
     * Form facade.
     *
     * @access  private
     * @var     FormFacade
     */
    private $_facade = null;

    /**
     * Initialize new instance.
     *
     * @access  public
     * @param   FormFacade  $facade  the form that is used for building HTML
     */
    public function __construct(FormFacade $facade)
    {
        $this->createNewForm($facade);
    }

    /**
     * Reset instance and create new field.
     * 
     * @access  public
     * @param   FormFacade  $facade  the form that is used for building HTML
     * @return  FormHtmlBuilder 
     */
    public function createNewForm(FormFacade $facade)
    {
        $this->_facade = $facade;
        return $this;
    }

    /**
     * create a form from the current instance and return it
     *
     * Returns the HTML-code for this form.
     *
     * @access  public
     * @return  string
     */
    public function buildHtml()
    {
        // setting up template
        $file = Yana::getInstance()->getSkin()->getFile('gui_form');
        assert('is_file($file); // Template file not found');
        $template = new SmartView($file);
        unset($file);

        $template->setVar('form', $this->_facade);
        return $template->toString();
    }

}

?>