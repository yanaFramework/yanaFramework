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
 * <<builder>> Produces instances of IsUser.
 *
 * Meant to help with retrieving user-objects from the database.
 *
 * Example:
 * <code>
 * $builder = new UserBuilder();
 * $user = $builder->buildFromSession();
 * </code>
 *
 * Alternative:
 * <code>
 * $builder = new UserBuilder();
 * $user = $builder->buildFromName('administrator');
 * </code>
 *
 * For unit-tests provide the optional paramters:
 * <code>
 * $nullAdapter = new \Yana\Data\Adapters\ArrayAdapter();
 * $nullAdapter['user'] = new User();
 * $builder = new UserBuilder($nullAdapter);
 * $user = $builder->buildFromSession(array('user_name' => 'user'));
 * // or
 * $user = $builder->buildFromName('user');
 * </code>
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class UserBuilder extends \Yana\Core\Object
{

    /**
     * @var  \Yana\Data\Adapters\IsDataAdapter
     */
    private $_userAdapter = null;

    /**
     * <<constructor>> Set up and initialize database connection.
     *
     * @param  \Yana\Data\Adapters\IsDataAdapter  $userAdapter  inject a NULL-adapter for Unit-tests
     */
    public function __construct(\Yana\Data\Adapters\IsDataAdapter $userAdapter = null)
    {
        if (!isset($userAdapter)) {
            $userAdapter = new \Yana\Security\Users\UserAdapter(\Yana\Application::connect('user'));
        }
        $this->_userAdapter = $userAdapter;
    }
    /**
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    protected function _getUserAdapter()
    {
        return $this->_userAdapter;
    }

    /**
     * Build an user object from the current user name saved in the session data.
     *
     * Returns a \Yana\Security\Users\GuestUser if the session contains no username,
     * or if the user isn't found in the database.
     * Returns an \Yana\Security\Users\User otherwise.
     *
     * @param   array  $sessionData  with the user name at index 'user_name'
     * @return  \Yana\Security\Users\IsUser
     */
    public function buildFromSession(array $sessionData = null)
    {
        if (\is_null($sessionData)) {
            $sessionData = $_SESSION;
        }
        if (isset($sessionData['user_name'])) {
            return new \Yana\Security\Users\GuestUser();
        }

        assert('!isset($userAccount); // Cannot redeclare var $userAccount');
        $userAccount = $this->buildFromName($sessionData['user_name']);
        assert('$userAccount instanceof \Yana\Security\Users\IsUser; // Return value must be an instance of IsUser');

        return $userAccount;
    }

    /**
     * Build an user object based on a given name.
     *
     * Returns a \Yana\Security\Users\GuestUser if no user with the given name is found in the database.
     * Returns an \Yana\Security\Users\User otherwise.
     *
     * @param   string  $userId  the name/id of the user as it is stored in the database
     * @return  \Yana\Security\Users\IsUser
     */
    public function buildFromName($userId)
    {
        assert('is_string($userId); // Invalid argument $userId: string expected');

        assert('!isset($adapter); // Cannot redeclare var $adapter');
        $adapter = $this->_getUserAdapter();

        if (!isset($adapter[$userId])) {
            return new \Yana\Security\Users\GuestUser();
        }
        assert('!isset($userAccount); // Cannot redeclare var $userAccount');
        $userAccount = $adapter[$userId];

        assert('$userAccount instanceof \Yana\Security\Users\IsUser; // Return value must be an instance of IsUser');
        return $userAccount;
    }

}

?>