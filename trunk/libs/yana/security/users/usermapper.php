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
class UserMapper extends \Yana\Core\Object implements \Yana\Data\Adapters\IsEntityMapper
{

    /**
     * Creates an user entity based on a database row.
     *
     * @param   array  $databaseRow  row containing user info
     * @return  \Yana\Security\Users\IsUser
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given user has no name
     */
    public function toEntity(array $databaseRow)
    {
        if (!isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::ID])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Given user has no name.");
        }

        $user = new \Yana\Security\Users\User($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::ID]);

        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::LANGUAGE])) {
            $user->setLanguage($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::LANGUAGE]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD])) {
            $user->setPassword($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::MAIL])) {
            $user->setMail($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::MAIL]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::IS_ACTIVE])) {
            $user->setActive((bool) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::IS_ACTIVE]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::LOGIN_FAILURE_COUNT])) {
            $user->setFailureCount((int) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::LOGIN_FAILURE_COUNT]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::LOGIN_FAILURE_TIME])) {
            $user->setFailureTime((int) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::LOGIN_FAILURE_TIME]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::LOGIN_COUNT])) {
            $user->setLoginCount((int) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::LOGIN_COUNT]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::LOGIN_TIME])) {
            $user->setLoginTime((int) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::LOGIN_TIME]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::IS_EXPERT_MODE])) {
            $user->setExpert((int) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::IS_EXPERT_MODE]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD_RECOVERY_ID])) {
            $user->setPasswordRecoveryId((int) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD_RECOVERY_ID]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD_RECOVERY_TIME])) {
            $user->setPasswordRecoveryTime((int) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD_RECOVERY_TIME]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD_TIME])) {
            $user->setPasswordTime((int) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD_TIME]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::RECENT_PASSWORDS])) {
            $user->setRecentPasswords((array) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::RECENT_PASSWORDS]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::TIME_CREATED])) {
            $user->setTimeCreated((array) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::TIME_CREATED]);
        }
        if (isset($databaseRow[\Yana\Security\Users\Tables\UserEnumeration::SESSION_CHECKSUM])) {
            $user->setSessionCheckSum((array) $databaseRow[\Yana\Security\Users\Tables\UserEnumeration::SESSION_CHECKSUM]);
        }
        return $user;
    }

    /**
     * Creates a database row based on an user entity.
     *
     * Note: groups and roles are not converted.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $user  entity containing user info
     * @return  array
     */
    public function toDatabaseRow(\Yana\Data\Adapters\IsEntity $user)
    {
        assert('!isset($row); // Cannot redeclare var $row');
        $row = array();

        if ($user instanceof \Yana\Security\Users\IsUser) {

            $row = array(
                \Yana\Security\Users\Tables\UserEnumeration::ID => $user->getId(),
                \Yana\Security\Users\Tables\UserEnumeration::IS_ACTIVE => $user->getActive(),
                \Yana\Security\Users\Tables\UserEnumeration::LOGIN_FAILURE_COUNT => $user->getFailureCount(),
                \Yana\Security\Users\Tables\UserEnumeration::LOGIN_FAILURE_TIME => $user->getFailureTime(),
                \Yana\Security\Users\Tables\UserEnumeration::LOGIN_COUNT => $user->getLoginCount(),
                \Yana\Security\Users\Tables\UserEnumeration::LOGIN_TIME => $user->getLoginTime(),
                \Yana\Security\Users\Tables\UserEnumeration::IS_EXPERT_MODE => $user->getExpert(),
                \Yana\Security\Users\Tables\UserEnumeration::RECENT_PASSWORDS => $user->getRecentPasswords()
            );
            if ($user->getLanguage() !== null) {
                $row[\Yana\Security\Users\Tables\UserEnumeration::LANGUAGE] = $user->getLanguage();
            }
            if ($user->getPassword() !== null) {
                $row[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD] = $user->getPassword();
            }
            if ($user->getMail() !== null) {
                $row[\Yana\Security\Users\Tables\UserEnumeration::MAIL] = $user->getMail();
            }
            if ($user->getPasswordRecoveryId() !== null) {
                $row[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD_RECOVERY_ID] = $user->getPasswordRecoveryId();
            }
            if ($user->getPasswordRecoveryTime() !== null) {
                $row[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD_RECOVERY_TIME] = $user->getPasswordRecoveryTime();
            }
            if ($user->getPasswordTime() !== null) {
                $row[\Yana\Security\Users\Tables\UserEnumeration::PASSWORD_TIME] = $user->getPasswordTime();
            }
            if ($user->getTimeCreated() !== null) {
                $row[\Yana\Security\Users\Tables\UserEnumeration::TIME_CREATED] = $user->getTimeCreated();
            }
            if ($user->getSessionCheckSum() !== null) {
                $row[\Yana\Security\Users\Tables\UserEnumeration::SESSION_CHECKSUM] = $user->getSessionCheckSum();
            }   
        }
        return $row;
    }

}

?>