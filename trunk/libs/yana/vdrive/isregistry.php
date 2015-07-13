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

namespace Yana\VDrive;

/**
 * <<interface>> Virtual Drive with var-container.
 *
 * @package    yana
 * @subpackage vdrive
 */
interface IsRegistry extends \Yana\VDrive\IsVDrive, \Yana\Core\IsVarContainer
{

    /**
     * Merges the value at adress $key with the provided array data.
     *
     * If $overwrite is set to false, then the values of keys that already exist are ignored.
     * Otherwise these values get updated to the new ones.
     *
     * @param   string  $key        key of updated element
     * @param   array   $array      new value
     * @param   bool    $overwrite  true = update, false = ignore
     * @return  \Yana\VDrive\IsRegistry
     */
    public function mergeVars($key, array $array, $overwrite = true);

    /**
     * Removes all vars from registry.
     *
     * @return  \Yana\VDrive\IsRegistry
     */
    public function unsetVars();

    /**
     * Removes var from registry.
     *
     * Unsets the element identified by $key in the
     * registry. Returns bool(false) if the element
     * does not exist or the key is invalid.
     * Returns bool(true) otherwise.
     *
     * @param   string  $key  key of element for delete
     * @return  \Yana\VDrive\IsRegistry
     */
    public function unsetVar($key);

    /**
     * Set the data type of the element identified by $key to $type.
     *
     * Returns bool(false) if the element is NULL or does not exist,
     * or the $type parameter is invalid. Returns bool(true) otherwise.
     *
     * @param   string  $key   target index
     * @param   string  $type  name of scalar data type
     * @return  \Yana\VDrive\IsRegistry
     */
    public function setType($key, $type);

    /**
     * retrieves var from registry and returns it by reference
     *
     * This returns the var identified by $key.
     * Returns null (not bool(false)) on error.
     *
     * Note: this function may return false but also
     * other values that evaluates to false.
     * To check for an error use: is_null($result).
     * To check for bool(false) use: $result === false.
     *
     * @param   string  $key  (optional)
     * @return  mixed
     */
    public function &getVarByReference($key);

    /**
     * Retrieves all vars from registry and returns them by reference.
     *
     * @return  array
     */
    public function &getVarsByReference();

}

?>