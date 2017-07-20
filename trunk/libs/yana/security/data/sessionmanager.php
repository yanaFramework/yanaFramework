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
 * Session manager.
 *
 * This is a manager class to handle user data and
 * permission levels.
 *
 * @name        SessionManager
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 * @deprecated since version 4.0
 */
class SessionManager extends \Yana\Core\AbstractSingleton
{

    /**
     * Returns the class name of the called class.
     *
     * @return string
     */
    protected static function _getClassName()
    {
        return __CLASS__;
    }

    /**
     * Set security level.
     *
     * Sets the user's security level to an integer value.
     * The value must be greater or equal 0 and less or equal 100.
     *
     * @param   int     $level      new security level [0,100]
     * @param   string  $userName   user to update
     * @param   string  $profileId  profile to update
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  on database error
     * @throws  \Yana\Db\CommitFailedException                   on database error
     * @throws  \Yana\Core\Exceptions\NotFoundException          when user not found
     */
    public function setSecurityLevel($level, $userName = '', $profileId = '')
    {
        assert('is_int($level); // Wrong type for argument 1. Integer expected');
        assert('$level >= 0; // Argument 1 must not be lesser 0');
        assert('$level <= 100; // Argument 1 must not be greater 100');
        assert('is_string($userName); // Wrong type for argument 2. String expected');
        assert('is_string($profileId); // Wrong type for argument 3. String expected');

        if (empty($profileId)) {
            $profileId = mb_strtoupper(\Yana\Application::getInstance()->getProfileId());
        } else {
            $profileId = mb_strtoupper($profileId);
        }


        /**
         * {@internal
         * The user id is resolved by the "user" plugin and stored
         * in a session var called "user_name", so other plugins can look it up.
         * }}
         */
        assert('!isset($currentUser); // Cannot redeclare variable $currentUser');
        if (!empty($_SESSION['user_name'])) {
            $currentUser = $_SESSION['user_name'];

        /* default user
         *
         * if no value is provided, switch to default instead
         */
        } else {
            $userName = mb_strtoupper($userName);
            $currentUser = $userName;
        }
        if (empty($userName)) {
            $userName = mb_strtoupper($currentUser);
        }

        if (empty($userName) || !\Yana\User::isUser($userName)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such user '$userName'.", \Yana\Log\TypeEnumeration::WARNING);
        }

        try {
            $database = self::getDatasource();
            $database->remove("securitylevel", array(
                    array("user_id", '=', $userName),
                    'and',
                    array(
                        array("profile", '=', $profileId),
                        'and',
                        array("user_created", '=', $currentUser)
                    )
                ), 1);
            $database->commit(); // may throw exception
            $database->insert("securitylevel", array(
                    "user_id" => $userName,
                    "profile" => $profileId,
                    "security_level" => $level,
                    "user_created" => $currentUser,
                    "user_proxy_active" => true
                ));
            $database->commit(); // may throw exception

        } catch (\Exception $e) {
            $message = "Unable to commit changed security level for user '$userName'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\NotCreatedException($message, $level, $e);
        }
    }

    /**
     * Get security level.
     *
     * Returns the user's security level as an integer value.
     * The default is 0.
     *
     * @param   string  $userName   user name
     * @param   string  $profileId  profile id
     * @return  int
     */
    public function getSecurityLevel($userName = '', $profileId = '')
    {
        assert('is_string($userName); // Wrong type for argument 1. String expected');
        assert('is_string($profileId); // Wrong type for argument 2. String expected');
        /* Argument 1 */
        if (empty($profileId)) {
            $profileId = \Yana\Application::getInstance()->getProfileId();
        }
        $profileId = mb_strtoupper($profileId);

        /* Argument 2 */
        /**
         * {@internal
         * The user id is resolved by the "user" plugin and stored
         * in a session var called "user_name", so other plugins can look it up.
         * }}
         */
        if (empty($userName)) {
            $userName = (string) \Yana\User::getUserName();
        }

        $level = 0;

        if (!empty($userName)) {
            $database = self::getDatasource();
            // 1) get security level for current profile
            $query = new \Yana\Db\Queries\Select($database);
            $query->setKey('securitylevel.*.security_level');
            $query->setWhere(array(
                array('user_id', '=', $userName),
                'and',
                array('profile', '=', $profileId)
            ));
            $query->setOrderBy(array('security_level'), array(true));
            $query->setLimit(1);
            $level = $database->select($query);

            // 2) fall-back to security level for default profile
            if ((empty($level) || !is_array($level)) && self::$_defaultProfileId != $profileId) {
                $query->setWhere(array(
                    array('user_id', '=', $userName),
                    'and',
                    array('profile', '=', self::$_defaultProfileId)
                ));
                $level = $database->select($query);
            }

            // 3) fall-back to default security level
            if (empty($level) || !is_array($level)) {
                return (int) \Yana\Application::getInstance()->getDefault('user.level');
            }

            $level = array_pop($level);
            assert('is_numeric($level);');
            return (int) $level;

        } else {
            return (int) \Yana\Application::getInstance()->getDefault('user.level');

        }
    }

}

?>