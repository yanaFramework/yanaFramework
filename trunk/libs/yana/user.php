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

namespace Yana;

/**
 * User
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 * @deprecated  since version 4.0
 */
class User extends \Yana\Core\Object
{

    /**
     * Name of currently selected user
     *
     * @var  string
     * @ignore
     */
    private static $selectedUser = null;

    /**
     * List of existing instances
     *
     * @var  array
     * @ignore
     */
    private static $instances = array();

    /**
     * database connection
     *
     * @var  \Yana\Db\IsConnection
     */
    private static $_database = null;

    /** @var string */ private $_name = null;

    /**
     * update cache
     *
     * @var  array
     * @ignore
     */
    private $updates = array();

    /**
     * get instance of this class
     *
     * Looks up an returns the instance by the given name.
     * If there is none, it creates a new one.
     *
     * If $skinName is NULL the function will return the currently
     * selected main skin instead.
     *
     * @param   string  $userName  name of instance to get
     * @return  \Yana\User
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the requested user does not exist
     */
    public static function &getInstance($userName = null)
    {
        if (empty($userName)) {
            $userName = self::getUserName();
            if (empty($userName)) {
                throw new \Yana\Core\Exceptions\NotFoundException();
            }
        } else {
            $userName = mb_strtoupper($userName);
        }

        if (!isset(self::$instances[$userName])) {
            self::$instances[$userName] = new self($userName);
        }
        return self::$instances[$userName];
    }

    /**
     * check if user exists
     *
     * Returns bool(true) if a user named $userName can be found in the current database.
     * Returns bool(false) otherwise.
     *
     * @param   string  $userName   user name
     * @return  bool
     */
    public static function isUser($userName)
    {
        assert('is_string($userName); // Wrong type for argument 1. String expected');

        $db = self::_getDatasource();
        return $db->exists("user.$userName");
    }

    /**
     * get currently selected user's name
     *
     * Returns the name of the currently logged-in user as a string.
     * If there is none NULL is returned.
     *
     * @return  string
     */
    public static function getUserName()
    {
        if (!isset(self::$selectedUser)) {
            if (isset($_SESSION['user_name'])) {
                self::$selectedUser = $_SESSION['user_name'];
            }
        }
        return self::$selectedUser;
    }

    /**
     * get datasource
     *
     * @return  \Yana\Db\IsConnection
     * @ignore
     */
    private static function _getDatasource()
    {
        if (!isset(self::$_database)) {
            self::$_database = \Yana\Application::connect('user');
        }
        return self::$_database;
    }
}

?>