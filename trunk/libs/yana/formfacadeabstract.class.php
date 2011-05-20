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
 * @abstract
 * @package     yana
 * @subpackage  form
 * @ignore
 */
abstract class FormFacadeAbstract extends Object
{

    /**
     * This function must initialize the protected class-members.
     *
     * @access  protected
     * @abstract
     */
    abstract protected function __construct();

    /**
     * Form definition
     *
     * @access  protected
     * @var     DDLForm
     */
    protected $form = null;

    /**
     * Form setup
     *
     * @access  protected
     * @var     FormSetup
     */
    protected $setup = null;

    /**
     * If the current form is a child element, this will point to it's parent.
     *
     * Leave blank if it is a root element.
     *
     * @access  protected
     * @var     FormFacade
     */
    protected $parent = null;

}

?>