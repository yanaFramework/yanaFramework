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

namespace Yana\Security\Data\Tables;

/**
 * <<enumeration>> Column names for table user.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 * @codeCoverageIgnore
 */
class UserEnumeration extends \Yana\Core\AbstractEnumeration
{

    const TABLE = 'user';
    const ID = 'USER_ID';
    const PASSWORD = 'USER_PWD';
    const SESSION_CHECKSUM = 'USER_SESSION';
    const MAIL = 'USER_MAIL';
    const IS_ACTIVE = 'USER_ACTIVE';
    const LANGUAGE = 'USER_LANGUAGE';
    const LOGIN_FAILURE_COUNT = 'USER_FAILURE_COUNT';
    const LOGIN_FAILURE_TIME = 'USER_FAILURE_TIME';
    const LOGIN_COUNT = 'USER_LOGIN_COUNT';
    const LOGIN_TIME = 'USER_LOGIN_LAST';
    const TIME_CREATED = 'USER_INSERTED';
    const IS_EXPERT_MODE = 'USER_IS_EXPERT';
    const RECENT_PASSWORDS = 'USER_PWD_LIST';
    const PASSWORD_TIME = 'USER_PWD_TIME';
    const PASSWORD_RECOVERY_ID = 'USER_RECOVER_ID';
    const PASSWORD_RECOVERY_TIME = 'USER_RECOVER_UTC';
    const AUTHENTICATION_ID = 'AUTH_ID';

}

?>