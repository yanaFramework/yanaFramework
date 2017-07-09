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

namespace Yana\Security\Data\Users;

/**
 * User data-adapter.
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Adapter extends \Yana\Security\Data\Users\AbstractAdapter implements \Yana\Security\Data\IsDataAdapter
{

    /**
     * <<construct>> Creates a new user-manager.
     *
     * To create the required connection you may use the following short-hand function:
     * <code>
     * $connection = \Yana\Application::connect("user");
     * </code>
     *
     * If no mapper is given, this function creates and uses an instance of \Yana\Security\Data\UserMapper.
     *
     * @param  \Yana\Db\IsConnection               $connection  database connection to table user
     * @param  \Yana\Data\Adapters\IsEntityMapper  $mapper      simple OR-mapper to convert database entries to objects
     */
    public function __construct(\Yana\Db\IsConnection $connection, \Yana\Data\Adapters\IsEntityMapper $mapper = null)
    {
        if (\is_null($mapper)) {
            $mapper = new \Yana\Security\Data\Users\Mapper();
        }
        parent::__construct($connection, $mapper);
    }

    /**
     * Returns the database key for the user as: table.userId.
     *
     * @param   string  $userId  name of the account
     * @return  string
     */
    protected function _toDatabaseKey($userId)
    {
        assert('is_string($userId); // Wrong type argument $userId. String expected.');

        return \Yana\Security\Data\Tables\UserEnumeration::TABLE . '.' . \Yana\Util\Strings::toUpperCase($userId);
    }

    /**
     * Returns bool(true) if a user by that name exists and bool(false) otherwise.
     *
     * @param   string  $userId  name of the account
     * @return  bool
     */
    public function offsetExists($userId)
    {
        assert('is_string($userId); // Wrong type argument $userId. String expected.');

        return $this->_getConnection()->exists($this->_toDatabaseKey($userId));
    }

    /**
     * Loads and returns an user account from the database.
     *
     * @param   string  $userId  name of the account
     * @return  \Yana\Security\Data\Users\IsEntity
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no such user exists
     */
    public function offsetGet($userId)
    {
        assert('is_string($userId); // Wrong type argument $userId. String expected.');

        assert('!isset($row); // Cannot redeclare var $row');
        $row = $this->_getConnection()->select($this->_toDatabaseKey($userId));

        try {
            assert('!isset($entity); // Cannot redeclare var $entity');
            $entity = $this->_getEntityMapper()->toEntity($row);
            $entity->setDataAdapter($this);
            return $entity;

        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {

            $message = "No user found with id: " . \htmlentities($userId);
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level, $e);
        }
    }

    /**
     * Write an user entry to the database.
     *
     * @param  string                              $userId      name of user account (can be NULL)
     * @param  \Yana\Security\Data\Users\IsEntity  $userEntity  the account data
     * @return \Yana\Security\Data\Users\IsEntity
     * @throws \Yana\Core\Exceptions\InvalidArgumentException  when the entity is invalid
     * @throws \Yana\Core\Exceptions\User\UserException        when there was a problem with the database
     */
    public function offsetSet($userId, $userEntity)
    {
        assert('is_string($userId) || is_null($userId); // Wrong type argument $userId. String expected.');

        if (!($userEntity instanceof \Yana\Security\Data\Users\IsEntity)) {
            assert('!isset($className); // Cannot redeclare var $className');
            $className = \is_object($userEntity) ? \get_class($userEntity) : \gettype($userEntity);
            assert('!isset($message); // Cannot redeclare var $message');
            $message = "Instance of \Yana\Security\Data\Users\IsEntity expected. Found " . $className . " instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        if (is_null($userId) || $userId == '') {
            $userId = $userEntity->getId();
        }

        assert('!isset($db); // Cannot redeclare var $db');
        assert('!isset($userRow); // Cannot redeclare var $userRow');

        $db = $this->_getConnection();
        $userRow = $this->_getEntityMapper()->toDatabaseRow($userEntity);

        try {
            if ($this->offsetExists($userId)) { // user exists
                $db->update($this->_toDatabaseKey($userId), $userRow);

            } else { // new user
                $db->insert($this->_toDatabaseKey($userId), $userRow);
                $db->insert(
                    \Yana\Security\Data\Tables\ProfileEnumeration::TABLE . "." . \Yana\Util\Strings::toUpperCase($userId), // profile id
                    array(\Yana\Security\Data\Tables\ProfileEnumeration::TIME_MODIFIED => time()) // profile row
                );

            }
            $db->commit(); // may throw exception

        } catch (\Yana\Db\DatabaseException $e) {

            assert('!isset($message); // Cannot redeclare var $message');
            $message = "User not saved due to a database error.";
            assert('!isset($level); // Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\UserException($level, $message, $e);
        }

        return $userEntity;
    }

    /**
     * Tries to delete the user from the database.
     *
     * @param   string  $userId  the account name
     * @throws  \Yana\Core\Exceptions\User\NotFoundException     when no such user exists
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  when there was a problem with the database
     */
    public function offsetUnset($userId)
    {
        assert('is_string($userId); // Wrong type argument $userId. String expected.');

        // user does not exist
        if (!$this->offsetExists($userId)) {
            throw new \Yana\Core\Exceptions\User\NotFoundException("No such user: '$userId'.", E_USER_WARNING);
        }

        $upperCaseUserId = \Yana\Util\Strings::toUpperCase($userId);

        assert('!isset($db); // Cannot redeclare var $db');
        $db = $this->_getConnection();
        try {

            // delete profile (if any)
            try {
                $db->remove(\Yana\Security\Data\Tables\ProfileEnumeration::TABLE . "." . $upperCaseUserId)
                    ->commit(); // may throw exception

            } catch (\Yana\Core\Exceptions\NotFoundException $e) {

                $db->rollback(); // don't try to commit this statement again
            }
            // delete user's security level (if any)
            try {
                $db->remove(\Yana\Security\Data\Tables\LevelEnumeration::TABLE,
                    array(\Yana\Security\Data\Tables\LevelEnumeration::USER, "=", $upperCaseUserId), 0)
                    ->commit(); // may throw exception

            } catch (\Yana\Core\Exceptions\NotFoundException $e) {

                $db->rollback(); // don't try to commit this statement again
            }
            // delete access permissions (temporarily) granted by this user (if any)
            try {
                $db->remove(\Yana\Security\Data\Tables\RuleEnumeration::TABLE,
                    array(\Yana\Security\Data\Tables\RuleEnumeration::GRANTED_BY_USER, "=", $upperCaseUserId), 0)
                    ->commit(); // may throw exception

            } catch (\Yana\Core\Exceptions\NotFoundException $e) {

                $db->rollback(); // don't try to commit this statement again
            }
            try {
                $db->remove(\Yana\Security\Data\Tables\LevelEnumeration::TABLE . ".*",
                    array(\Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER, "=", $upperCaseUserId), 0)
                    ->commit(); // may throw exception

            } catch (\Yana\Core\Exceptions\NotFoundException $e) {

                $db->rollback(); // don't try to commit this statement again
            }
            // delete user settings
            $db->remove($this->_toDatabaseKey($upperCaseUserId))
                ->commit(); // may throw exception

        } catch (\Exception $e) {

            $db->rollback(); // don't try to commit the faulty statement again
            $message = "Unable to commit changes to the database server while trying to remove".
                "user '{$userId}'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException($message, $level, $e);
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
        return $this->_getConnection()->length(\Yana\Security\Data\Tables\UserEnumeration::TABLE);
    }

    /**
     * Return an array of all valid identifiers.
     *
     * @return  array
     */
    public function getIds()
    {
        assert('!isset($key); // Cannot redeclare var $key');
        $key = \Yana\Security\Data\Tables\UserEnumeration::TABLE . '.*.' . \Yana\Security\Data\Tables\UserEnumeration::ID;
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