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

namespace Yana\Db\Mdb2;

/**
 * <<interface>> Takes a MDB2 error constant and returns the appropriate exception.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsExceptionFactory
{

    /**
     * Convert MDB2 error code to the appropriate exception object.
     *
     * This also sets the message according to the given type.
     *
     * @param   int  $errorCode  Some \MDB2_ERROR code
     * @return  \Yana\Core\Exceptions\AbstractException
     */
    public function toException($errorCode);

}

?>