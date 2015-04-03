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
 * User manager.
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class UserMapper extends \Yana\Core\Object
{

    /**
     * Creates an user entity based on a database row.
     *
     * @param   array  $databaseRow  row containing user info
     * @return  \Yana\Security\Users\IsUser
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given user has no name
     */
    public function toUserEntity(array $databaseRow)
    {
        if (!isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::ID])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Given user has no name.");
        }

        $user = new \Yana\Security\Users\User($databaseRow[\Yana\Security\Users\UserColumnEnumeration::ID]);

        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::LANGUAGE])) {
            $user->setLanguage($databaseRow[\Yana\Security\Users\UserColumnEnumeration::LANGUAGE]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::PASSWORD])) {
            $user->setPassword($databaseRow[\Yana\Security\Users\UserColumnEnumeration::PASSWORD]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::MAIL])) {
            $user->setMail($databaseRow[\Yana\Security\Users\UserColumnEnumeration::MAIL]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::IS_ACTIVE])) {
            $user->setActive((bool) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::IS_ACTIVE]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::LOGIN_FAILURE_COUNT])) {
            $user->setFailureCount((int) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::LOGIN_FAILURE_COUNT]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::LOGIN_FAILURE_TIME])) {
            $user->setFailureTime((int) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::LOGIN_FAILURE_TIME]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::LOGIN_COUNT])) {
            $user->setLoginCount((int) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::LOGIN_COUNT]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::LOGIN_TIME])) {
            $user->setLoginTime((int) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::LOGIN_TIME]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::IS_EXPERT_MODE])) {
            $user->setExpert((int) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::IS_EXPERT_MODE]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::PASSWORD_RECOVERY_ID])) {
            $user->setPasswordRecoveryId((int) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::PASSWORD_RECOVERY_ID]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::PASSWORD_RECOVERY_TIME])) {
            $user->setPasswordRecoveryTime((int) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::PASSWORD_RECOVERY_TIME]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::PASSWORD_TIME])) {
            $user->setPasswordTime((int) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::PASSWORD_TIME]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::RECENT_PASSWORDS])) {
            $user->setRecentPasswords((array) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::RECENT_PASSWORDS]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::TIME_CREATED])) {
            $user->setTimeCreated((array) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::TIME_CREATED]);
        }
        if (isset($databaseRow[\Yana\Security\Users\UserColumnEnumeration::SESSION_CHECKSUM])) {
            $user->setSessionCheckSum((array) $databaseRow[\Yana\Security\Users\UserColumnEnumeration::SESSION_CHECKSUM]);
        }
        return $user;
    }


}

?>