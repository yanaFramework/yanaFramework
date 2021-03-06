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

namespace Yana\Security\Data\Users;

/**
 * <<interface>> User data-adapter.
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsDataAdapter extends \Yana\Data\Adapters\IsDataBaseAdapter
{

    /**
     * Loads and returns an user account from the database.
     *
     * @param   string  $mail  unique mail address
     * @return  \Yana\Security\Data\Users\IsEntity
     * @throws  \Yana\Core\Exceptions\User\MailNotFoundException  when no such user exists
     */
    public function findUserByMail($mail);

    /**
     * Loads and returns an user account from the database.
     *
     * @param   string  $recoveryId  unique identifier
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no such user exists
     */
    public function findUserByRecoveryId($recoveryId);

    /**
     * Unserializes the table-row to an entity object.
     *
     * @param   array  $formData  user data
     * @return  \Yana\Data\Adapters\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given data is invalid
     */
    public function toEntity(array $formData);

}

?>