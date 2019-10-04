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
 * <<abstract>> HTML Form builder.
 *
 * This class is meant to create HTML fields for forms.
 *
 * @package     yana
 * @subpackage  form
 */
abstract class AbstractHtmlBuilder extends \Yana\Core\StdObject
{

    /**
     * Form facade.
     *
     * @var  \Yana\Forms\Facade
     */
    private $_facade = null;

    /**
     * @var  \Yana\Views\Templates\IsTemplate
     */
    private $_template = null;

    /**
     * Initialize new instance.
     *
     * @param  \Yana\Forms\Facade                $facade    the form that is used for building HTML
     * @param  \Yana\Views\Templates\IsTemplate  $template  form template
     */
    public function __construct(\Yana\Forms\Facade $facade, $template = null)
    {
        $this->_facade = $facade;
        $this->_template = $template;
    }

    /**
     * Returns form facade.
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getFacade()
    {
        return $this->_facade;
    }

    /**
     * Returns form facade.
     *
     * @return  \Yana\Views\Templates\IsTemplate
     */
    protected function _getTemplate()
    {
        if (!isset($this->_template)) {
            // setting up template
            $builder = new \Yana\ApplicationBuilder();
            $application = $builder->buildApplication();
            $this->_template = $application->getView()->createContentTemplate('id:gui_form');
        }
        return $this->_template;
    }

    /**
     * Create a form from the current instance and return it.
     *
     * Returns the HTML-code for this form.
     *
     * @return  string
     */
    abstract public function __invoke();

}

?>