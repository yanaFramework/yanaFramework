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
     * Form query
     *
     * @access  protected
     * @var     DbSelect
     */
    protected $query = null;

    /**
     * Get form structure.
     *
     * @access  public
     * @return  DDLForm
     */
    public function getForm()
    {
        return $this->setup;
    }

    /**
     * Get form setup.
     *
     * @access  public
     * @return  FormSetup
     */
    public function getSetup()
    {
        return $this->setup;
    }

    /**
     * Get query to load form contents.
     *
     * @access  public
     * @return  DbQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

}

?>