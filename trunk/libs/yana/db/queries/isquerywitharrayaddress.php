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
declare(strict_types=1);

namespace Yana\Db\Queries;

/**
 * <<interface>> Queries that have a where clause.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsQueryWithArrayAddress extends \Yana\Db\Queries\IsQuery
{

    /**
     * Set array address.
     *
     * Applies to columns of type 'array' only.
     *
     * You may provide the array key inside the value of the column that you wish to get.
     * If it is a multidimensional array, you may traverse in deeper dimensions by linking
     * keys with a dot '.' - for example: "foo.bar" gets $result['foo']['bar'].
     *
     * Note: this will not check if the key that you provided is
     * a valid key or if it really points to a value. If it is not,
     * the resultset will be empty.
     *
     * @param   string  $arrayAddress   array address
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if a given argument is invalid
     * @return  $this
     */
    public function setArrayAddress(string $arrayAddress = "");

    /**
     * Returns the currently selected address as a string.
     *
     * If none has been selected yet, an empty string is returned.
     *
     * @return  string
     */
    public function getArrayAddress(): string;

}

?>