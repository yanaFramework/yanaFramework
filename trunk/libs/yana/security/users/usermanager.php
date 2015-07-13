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
class UserManager extends \Yana\Security\Users\AbstractUserManager implements \Yana\Data\Adapters\IsDataAdapter
{

    /**
     * Returns the database key for the user as: table.userId.
     *
     * @param   string  $userId  name of the account
     * @return  string
     */
    protected function _toDatabaseKey($userId)
    {
        assert('is_string($userId)', ' Invalid argument $userId: string expected');

        return \Yana\Security\Users\UserColumnEnumeration::TABLE . '.' . $userId;
    }

    /**
     * Returns bool(true) if a user by that name exists and bool(false) otherwise.
     *
     * @param   string  $userId  name of the account
     * @return  bool
     */
    public function offsetExists($userId)
    {
        assert('is_string($userId)', ' Invalid argument $userId: string expected');

        return $this->_getConnection()->exists($this->_toDatabaseKey($userId));
    }

    /**
     * Loads and returns an user account from the database.
     *
     * @param   string  $userId  name of the account
     * @return  \Yana\Security\Users\IsUser
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no such user exists
     */
    public function offsetGet($userId)
    {
        assert('is_string($userId)', ' Invalid argument $userId: string expected');

        $userId = \Yana\Util\String::toUpperCase($userId);

        assert('!isset($row)', ' Cannot redeclare var $row');
        $row = $this->_getConnection()->select($this->_toDatabaseKey($userId));

        try {
            return $this->_getEntityMapper()->toEntity($row);

        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {

            $message = "No user found with id: " . \htmlentities($userId);
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\NotFoundException($level, $message, $e);
        }
    }

    /**
     * Write an user entry to the database.
     *
     * @param  string                       $userId      name of user account (can be NULL)
     * @param  \Yana\Security\Users\IsUser  $userEntity  the account data
     * @return \Yana\Security\Users\IsUser
     * @throws \Yana\Core\Exceptions\InvalidArgumentException  when the entity is invalid
     * @throws \Yana\Core\Exceptions\User\UserException        when there was a problem with the database
     */
    public function offsetSet($userId, $userEntity)
    {
        if (!($userEntity instanceof \Yana\Security\Users\IsUser)) {
            $message = "Instance of \Yana\Security\Users\IsUser expected. Found " . \get_class($userEntity) . " instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        if (\is_null($userId)) {
            $userId = $userEntity->getId();
        }
        assert('is_string($userId)', ' Invalid argument $userId: string expected');

        assert('!isset($db)', ' Cannot redeclare var $db');
        $db = $this->_getConnection();

        try {
            $db->insertOrUpdate($this->_toDatabaseKey($userId), $userEntity); // may throw exception
            $db->commit(); // may throw exception

        } catch (\Yana\Db\DatabaseException $e) {

            $message = "User not saved due to a database error.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\UserException($level, $message, $e);
        }

        return $userEntity;
    }

    /**
     * Tries to delete the user from the database.
     *
     * @param   string  $userId  the account name
     * @throws \Yana\Core\Exceptions\User\UserException  when there was a problem with the database
     */
    public function offsetUnset($userId)
    {
        assert('is_string($userId)', ' Invalid argument $userId: string expected');

        assert('!isset($db)', ' Cannot redeclare var $db');
        $db = $this->_getConnection();
        try {
            $db->remove($this->_toDatabaseKey($userId));
            $db->commit();

        } catch (\Yana\Db\DatabaseException $e) {

            $message = "User not deleted due to a database error.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\UserException($level, $message, $e);
        }
    }

    /**
     * Return the number of users in the database.
     *
     * Since there must be at least 1 admin-account at all times, this function should never return any
     * value smaller than 1, unless there is a problem with the database.
     *
     * If the databae in fact is empty this function will return 0.
     * In which case you should evacuate children first.
     *
     * @return  int
     */
    public function count()
    {
        return $this->_getConnection()->length(\Yana\Security\Users\UserColumnEnumeration::TABLE);
    }

    /**
     * Return an array of all valid identifiers.
     *
     * @return  array
     */
    public function getIds()
    {
        assert('!isset($key)', ' Cannot redeclare var $key');
        $key = \Yana\Security\Users\UserColumnEnumeration::TABLE . '.*.' . \Yana\Security\Users\UserColumnEnumeration::ID;
        return $this->_getConnection()->select($key);
    }

    /**
     * Saves the account data to the database.
     *
     * @param  \Yana\Data\Adapters\IsEntity  $entity  object to persist
     * @throws \Yana\Core\Exceptions\InvalidArgumentException  when the entity is invalid
     * @throws \Yana\Core\Exceptions\User\UserException        when there was a problem with the database
     */
	public function saveEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        $this->offsetSet(null, $entity);
    }

}

?>