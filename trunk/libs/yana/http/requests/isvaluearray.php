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

namespace Yana\Http\Requests;

/**
 * <<interface>> Request variable.
 *
 * Allows validaty checks and conversion.
 *
 * @package     yana
 * @subpackage  http
 */
interface IsValueArray
{

    /**
     * Returns contents as unsanitized array.
     *
     * If the contents are not an array, they are wrapped in a numeric array, where the value is at index "0".
     *
     * @return  array
     */
    public function asUnsafeArray();

    /**
     * Returns contents as sanitized array.
     *
     * If the contents are not an array, they are wrapped in a numeric array, where the value is at index "0".
     *
     * @return  array
     */
    public function asArrayOfSafeStrings();

    /**
     * Returns contents as collection for sanitation.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function all();

}

?>