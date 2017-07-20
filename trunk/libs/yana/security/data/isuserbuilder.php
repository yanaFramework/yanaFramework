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

namespace Yana\Security\Data;

/**
 * <<interface>> Produces instances of IsUser.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsUserBuilder
{

    /**
     * Check if a given user name is registered in the database.
     *
     * Returns bool(true) if the name is found and bool(false) otherwise.
     *
     * @param   string  $userName  may contain only A-Z, 0-9, '-' and '_'
     * @return  bool
     */
    public function isExistingUserName($userName);

    /**
     * Build an user object from the current user name saved in the session data.
     *
     * Returns a \Yana\Security\Data\GuestUser if the session contains no username.
     * Returns an \Yana\Security\Data\User otherwise.
     *
     * @param   \Yana\Core\Sessions\IsWrapper  $session  with the user name at index 'user_name'
     * @return  \Yana\Security\Data\Users\IsEntity
     * @throws  \Yana\Core\Exceptions\NotFoundException  if no such user is found in the database
     */
    public function buildFromSession(\Yana\Security\Sessions\IsWrapper $session = null);

    /**
     * Build an user object based on a given name.
     *
     * @param   string  $userId  the name/id of the user as it is stored in the database
     * @return  \Yana\Security\Data\Users\IsEntity
     * @throws  \Yana\Core\Exceptions\NotFoundException  if no such user is found in the database
     */
    public function buildFromUserName($userId);

    /**
     * Build an user object based on a given mail address.
     *
     * @param   string  $mail  an unique mail address
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\MailNotFoundException  if no such user is found in the database
     */
    public function buildFromUserMail($mail);

}

?>