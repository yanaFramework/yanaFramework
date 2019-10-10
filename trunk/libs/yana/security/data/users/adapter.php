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
class Adapter extends \Yana\Security\Data\Users\AbstractAdapter
{

    /**
     * <<construct>> Creates a new user-manager.
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
     * Returns the name of the target table.
     *
     * @return  string
     */
    protected function _getTableName()
    {
        return \Yana\Security\Data\Tables\UserEnumeration::TABLE;
    }

    /**
     * Serializes the entity object to a table-row.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  object to convert
     * @return  array
     */
    protected function _serializeEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        return $this->_getEntityMapper()->toDatabaseRow($entity);
    }

    /**
     * Unserializes the table-row to an entity object.
     *
     * @param   array  $dataSet  table row to convert
     * @return  \Yana\Data\Adapters\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given data is invalid
     */
    protected function _unserializeEntity(array $dataSet)
    {
        $entity = $this->_getEntityMapper()->toEntity($dataSet);
        $entity->setDataAdapter($this);
        return $entity;
    }

    /**
     * Unserializes the table-row to an entity object.
     *
     * @param   array  $formData  user data
     * @return  \Yana\Data\Adapters\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given data is invalid
     */
    public function toEntity(array $formData)
    {
        return $this->_unserializeEntity($formData);
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
        assert(is_string($userId), 'Wrong type argument $userId. String expected.');

        try {
            return parent::offsetGet(\Yana\Util\Strings::toUpperCase((string) $userId));

        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {

            $message = "No user found with id: " . \htmlentities((string) $userId);
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
     * @throws \Yana\Core\Exceptions\User\NotSavedException    when there was a problem with the database
     */
    public function offsetSet($userId, $userEntity)
    {
        assert(is_string($userId) || is_null($userId), 'Wrong type argument $userId. String expected.');

        if (!($userEntity instanceof \Yana\Security\Data\Users\IsEntity)) {
            assert(!isset($className), 'Cannot redeclare var $className');
            $className = \is_object($userEntity) ? \get_class($userEntity) : \gettype($userEntity);
            assert(!isset($message), 'Cannot redeclare var $message');
            $message = "Instance of \Yana\Security\Data\Users\IsEntity expected. Found " . $className . " instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        if (!is_null($userId)) {
            $userId = \Yana\Util\Strings::toUpperCase((string) $userId);
        }

        try {
            return parent::offsetSet($userId, $userEntity);

        } catch (\Yana\Db\DatabaseException $e) {

            assert(!isset($message), 'Cannot redeclare var $message');
            $message = "User not saved due to a database error.";
            assert(!isset($level), 'Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\NotSavedException($message, $level, $e);
        }
    }

    /**
     * Triggered when offsetSet() is called and the offset doesn't exists.
     *
     * This function adds an additional empty user profile.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity      object to be stored
     * @param   scalar                        $optionalId  primary key
     * @return  scalar
     */
    protected function _onInsert(\Yana\Data\Adapters\IsEntity $entity, $optionalId = null)
    {
        $id = parent::_onInsert($entity, $optionalId);
        $db = $this->_getDatabaseConnection();
        // There is a 1:1 connection between profile and user. Ergo, the primary keys are the same.
        $db->insert(
            \Yana\Security\Data\Tables\ProfileEnumeration::TABLE . "." . \Yana\Util\Strings::toUpperCase($id), // profile id
            array(\Yana\Security\Data\Tables\ProfileEnumeration::TIME_MODIFIED => time()) // empty profile row
        );
        return $id;
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
        assert(is_string($userId), 'Wrong type argument $userId. String expected.');

        // user does not exist
        if (!$this->offsetExists($userId)) {
            assert(!isset($message), 'Cannot redeclare var $message');
            $message = "No such user: '$userId'.";
            assert(!isset($level), 'Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
        }

        assert(!isset($upperCaseUserId), 'Cannot redeclare var $upperCaseUserId');
        $upperCaseUserId = \Yana\Util\Strings::toUpperCase($userId);

        assert(!isset($db), 'Cannot redeclare var $db');
        $db = $this->_getDatabaseConnection();
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
            parent::offsetUnset($upperCaseUserId); // may throw exception

        } catch (\Exception $e) {

            $message = "Unable to commit changes to the database server while trying to remove user '{$userId}'.";
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
        return $this->_getDatabaseConnection()->length($this->_getTableName());
    }

    /**
     * Return an array of all valid identifiers.
     *
     * @return  array
     */
    public function getIds()
    {
        assert(!isset($key), 'Cannot redeclare var $key');
        $key = $this->_getTableName() . '.*.' . \Yana\Security\Data\Tables\UserEnumeration::ID;
        return $this->_getDatabaseConnection()->select($key);
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

    /**
     * Loads and returns an user account from the database.
     *
     * @param   string  $mail  unique mail address
     * @return  \Yana\Security\Data\Users\IsEntity
     * @throws  \Yana\Core\Exceptions\User\MailNotFoundException  when no such user exists
     */
    public function findUserByMail($mail)
    {
        assert(is_string($mail), 'Wrong type argument $mail. String expected.');

        assert(!isset($entities), 'Cannot redeclare var $entities');
        $entities = parent::_findEntitiesByColumn(\Yana\Security\Data\Tables\UserEnumeration::MAIL, (string) $mail);

        if (count($entities) !== 1) {
            $message = "No user found with mail: " . \htmlentities($mail);
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\MailNotFoundException($message, $level);
        }

        assert(!isset($entity), 'Cannot redeclare var $entity');
        $entity = current($entities);
        assert($entity instanceof \Yana\Security\Data\Users\IsEntity);
        return $entity;
    }

    /**
     * Loads and returns an user account from the database.
     *
     * @param   string  $recoveryId  unique identifier
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no such user exists
     */
    public function findUserByRecoveryId($recoveryId)
    {
        assert(is_string($recoveryId), 'Invalid argument $recoveryId: string expected');

        $entities = parent::_findEntitiesByColumn(\Yana\Security\Data\Tables\UserEnumeration::PASSWORD_RECOVERY_ID, (string) $recoveryId);

        if (count($entities) !== 1) {
            $message = "No user found with recovery id: " . \htmlentities($recoveryId);
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
        }

        assert(!isset($entity), 'Cannot redeclare var $entity');
        $entity = current($entities);
        assert($entity instanceof \Yana\Security\Data\Users\IsEntity);
        return $entity;
    }

}

?>