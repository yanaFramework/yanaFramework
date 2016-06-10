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

namespace Yana\Security\Users;

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
interface IsDataAdapter extends \Yana\Data\Adapters\IsDataAdapter
{


    /**
     * Get user groups.
     *
     * Returns an array of group names, where the keys are the group ids and the values are
     * the human-readable group names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function getGroups($userId);

    /**
     * Get user roles.
     *
     * Returns an array of role names, where the keys are the group ids and the values are
     * the human-readable role names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @param   string  $userId  
     * @return  array
     */
    public function getRoles($userId);

    /**
     * Set security level.
     *
     * Sets the user's security level to an integer value.
     * The value must be greater or equal 0 and less or equal 100.
     *
     * @param   int     $level          new security level [0,100]
     * @param   string  $userId         user to update
     * @param   string  $profileId      profile to update
     * @param   string  $currentUserId  currently logged in user
     * @return  \Yana\Security\Users\UserAdapter
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  on database error
     * @throws  \Yana\Db\CommitFailedException                   on database error
     * @throws  \Yana\Core\Exceptions\User\NotFoundException     when user not found
     */
    public function setSecurityLevel($level, $userId, $profileId, $currentUserId);

    /**
     * Get security level.
     *
     * Returns the user's security level as an integer value.
     * The default is 0.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  int
     */
    public function getSecurityLevel($userId, $profileId);

}

?>