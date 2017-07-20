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
class UserBuilder extends \Yana\Core\Object implements \Yana\Security\Data\IsUserBuilder
{

    /**
     * @var  \Yana\Security\Data\IsDataAdapter
     */
    private $_userAdapter = null;

    /**
     * <<constructor>> Set up and initialize user adapter.
     *
     * @param  \Yana\Security\Data\IsDataAdapter  $userAdapter  inject a NULL-adapter for Unit-tests
     */
    public function __construct(\Yana\Security\Data\IsDataAdapter $userAdapter = null)
    {
        $this->_userAdapter = $userAdapter;
    }

    /**
     * Returns a user adapter.
     *
     * If there is none, it will create a fitting adapter automatically.
     *
     * @return  \Yana\Security\Data\IsDataAdapter
     */
    protected function _getUserAdapter()
    {
        if (!isset($this->_userAdapter)) {
            assert('!isset($factory); // Cannot redeclare var $factory.');
            $factory = new \Yana\Db\ConnectionFactory(new \Yana\Db\SchemaFactory());
            $this->_userAdapter = new \Yana\Security\Data\Users\Adapter($factory->createConnection('user'));
            unset($factory);
        }
        return $this->_userAdapter;
    }

    /**
     * Check if a given user name is registered in the database.
     *
     * Returns bool(true) if the name is found and bool(false) otherwise.
     *
     * @param   string  $userName  may contain only A-Z, 0-9, '-' and '_'
     * @return  bool
     */
    public function isExistingUserName($userName)
    {
        assert('is_string($userName); // Invalid argument $userName: string expected');
        return $this->_getUserAdapter()->offsetExists((string) $userName);
    }

    /**
     * Build an user object from the current user name saved in the session data.
     *
     * Returns a \Yana\Security\Data\GuestUser if the session contains no username.
     * Returns an \Yana\Security\Data\User otherwise.
     *
     * @param   \Yana\Security\Sessions\IsWrapper  $session  with the user name at index 'user_name'
     * @return  \Yana\Security\Data\Users\IsEntity
     * @throws  \Yana\Core\Exceptions\NotFoundException  if no such user is found in the database
     */
    public function buildFromSession(\Yana\Security\Sessions\IsWrapper $session = null)
    {
        if (\is_null($session)) {
            $session = new \Yana\Security\Sessions\Wrapper();
        }
        assert('!isset($userName); // Cannot redeclare var $userName');
        $userName = $session->getCurrentUserName();
        if ($userName === "") {
            return new \Yana\Security\Data\Users\Guest();
        }

        assert('!isset($userAccount); // Cannot redeclare var $userAccount');
        $userAccount = $this->_buildFromUserName($userName);
        assert('$userAccount instanceof \Yana\Security\Data\Users\IsEntity; // Return value must be an instance of IsEntity');

        return $userAccount;
    }

    /**
     * Build an user object based on a given name.
     *
     * @param   string  $userId  the name/id of the user as it is stored in the database
     * @return  \Yana\Security\Data\Users\IsEntity
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found in the database
     */
    private function _buildFromUserName($userId)
    {
        assert('is_string($userId); // Invalid argument $userId: string expected');

        assert('!isset($adapter); // Cannot redeclare var $adapter');
        $adapter = $this->_getUserAdapter();

        if (!$this->isExistingUserName($userId)) {
            throw new \Yana\Core\Exceptions\User\NotFoundException("User '" . $userId . "' not found.");;
        }
        assert('!isset($userAccount); // Cannot redeclare var $userAccount');
        $userAccount = $adapter[$userId];

        assert('$userAccount instanceof \Yana\Security\Data\Users\IsEntity; // Return value must be an instance of IsEntity');
        return $userAccount;
    }

    /**
     * Build an user object based on a given name.
     *
     * @param   string  $userId  the name/id of the user as it is stored in the database
     * @return  \Yana\Security\Data\Users\IsEntity
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found in the database
     */
    public function buildFromUserName($userId)
    {
        assert('is_string($userId); // Invalid argument $userId: string expected');
        return $this->_buildFromUserName((string) $userId);
    }

    /**
     * Build an user object based on a given mail address.
     *
     * @param   string  $mail  an unique mail address
     * @return  \Yana\Security\Data\Users\IsEntity
     * @throws  \Yana\Core\Exceptions\User\MailNotFoundException  if no such user is found in the database
     */
    public function buildFromUserMail($mail)
    {
        assert('is_string($mail); // Invalid argument $mail: string expected');

        return $this->_getUserAdapter()->findUserByMail($mail); // may throw exception
    }

    /**
     * Build an user object based on a given name.
     *
     * @param   string  $userId  the name/id of the user as it is stored in the database
     * @param   string  $mail    the user's e-mail address (must be unique)
     * @return  \Yana\Security\Data\Users\IsEntity
     */
    public function buildNewUser($userId, $mail)
    {
        assert('is_string($userId); // Invalid argument $userId: string expected');
        assert('is_string($mail); // Invalid argument $mail: string expected');


        if ($this->isExistingUserName($userId)) {
            throw new \Yana\Core\Exceptions\User\AlreadyExistsException(
                "A user with the name '$userId' already exists.", \Yana\Log\TypeEnumeration::WARNING
            );
        }

        $user = new \Yana\Security\Data\Users\Entity(\Yana\Util\Strings::toUpperCase((string) $userId));
        $user->setMail((string) $mail);
        $user->setDataAdapter($this->_getUserAdapter());
        return $user;
    }

}

?>