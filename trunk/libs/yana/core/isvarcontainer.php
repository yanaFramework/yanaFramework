<?php
/**
 * YANA library
 *
 * Primary controller class
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
declare(strict_types=1);

namespace Yana\Core;

/**
 * <<interface>> Container interface for classes managing variables.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsVarContainer
{

    /**
     * Returns the var identified by $key.
     *
     * Returns NULL if there is no such key.
     *
     * Note: this function may return false but also other values that evaluate to false.
     * To check for an error use: is_null($result).
     * To check for bool(false) use: $result === false.
     *
     * @param   string  $key  the var to retrieve
     * @return  mixed
     */
    public function getVar($key);

    /**
     * Returns all contained vars.
     *
     * @return  array
     */
    public function getVars();

    /**
     * Check if a var exists.
     *
     * Returns bool(true) if the key is known and bool(false) otherwise.
     *
     * @param   string  $key  some key (case insensitive)
     * @return  bool
     */
    public function isVar($key);

    /**
     * Sets the element identified by $key to $value by passing it's reference.
     *
     * If the value does not exist it gets inserted.
     * If a previous value existed the value gets updated.
     *
     * @param   string  $key        key of updated element
     * @param   mixed   &$value     new value
     * @return  $this
     */
    public function setVarByReference($key, &$value);

    /**
     * Replaces all elements of the container by reference.
     *
     * @param   array  &$value  set of new values
     * @return  $this
     */
    public function setVarsByReference(array &$value);

    /**
     * Sets the element identified by $key  to $value.
     *
     * If the value does not exist it gets inserted.
     * If a previous value existed the value gets updated.
     *
     * @param   string  $key        key of updated element
     * @param   mixed   $value      new value
     * @return  $this
     */
    public function setVar($key, $value);

    /**
     * Replaces all elements of the container.
     *
     * @param   array  $value  set of new values
     * @return  $this
     */
    public function setVars(array $value);

}

?>