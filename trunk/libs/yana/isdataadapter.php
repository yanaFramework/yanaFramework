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
 * <<Interface>> Data Adapter
 *
 * The DataAdapter is used to inject a dependency into the {@see AbstractDataContainer}.
 *
 * @static
 * @access      public
 * @package     yana
 * @subpackage  core
 * @ignore
 */
interface IsDataAdapter
{
    /**
     * check if a instance of the given id exists
     *
     * Returns bool(true) if there is an instance with the given id and
     * bool(false) otherwise.
     *
     * @access  public
     * @param   string  $id  instance id
     * @return  bool
     */
    public function isValid($id);

    /**
     * get instance
     *
     * Return an associative array of data associated with the requested instance.
     * Throws an exception if the given id is not valid.
     *
     * @access  public
     * @param   string  $id  instance id
     * @return  array
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the instance does not exist
     */
    public function getInstance($id);

    /**
     * return array of ids in use
     *
     * @access  public
     * @return  array
     */
    public function getIds();

    /**
     * update instance
     *
     * Takes an instance, checks it's state and updates the data respondingly.
     *
     * @access  public
     * @param   DataContainerAbstract $container  instance that should be updated
     */
    public function updateInstance(DataContainerAbstract $container);
}

?>