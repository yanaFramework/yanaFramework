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

namespace Yana\Forms\Setups;

/**
 * <<interface>> Build a form using a form object and settings.
 *
 * @package     yana
 * @subpackage  form
 */
interface IsBuilder
{

    /**
     * Overwrite existing setup.
     *
     * Set your own predefined setup, to modify it.
     *
     * @param  \Yana\Forms\Setup  $setup  basic setup to modify
     */
    public function setSetup(\Yana\Forms\Setup $setup);

    /**
     * Build facade object.
     * 
     * @return  \Yana\Forms\Setup
     */
    public function __invoke();

    /**
     * Get form object.
     *
     * @return  \Yana\Db\Ddl\Form
     */
    public function getForm();

    /**
     * Set form object.
     *
     * @param   \Yana\Db\Ddl\Form  $form  configuring the contents of the form
     * @return  $this
     */
    public function setForm(\Yana\Db\Ddl\Form $form);

    /**
     * Update setup with request array.
     *
     * @param   array  $request  initial values (e.g. Request array)
     * @return  $this
     */
    public function updateSetup(array $request = array());

    /**
     * Update values with request array.
     *
     * @param   array  $request  initial values (e.g. Request array)
     * @return  $this
     */
    public function updateValues(array $request = array());

    /**
     * Overwrite row values.
     *
     * @param   array  $rows  initial values
     * @return  $this
     */
    public function setRows(array $rows = array());

    /**
     * Select visible columns.
     *
     * Limits the visible columns to entries of this list.
     *
     * @param   array  $columnNames  whitelist
     * @return  $this
     */
    public function setColumnsWhitelist(array $columnNames);

    /**
     * Select hidden columns.
     *
     * Limits the visible columns to entries not on this list.
     *
     * @param   array  $columnNames  whitelist
     * @return  $this
     */
    public function setColumnsBlacklist(array $columnNames);

    
}

?>