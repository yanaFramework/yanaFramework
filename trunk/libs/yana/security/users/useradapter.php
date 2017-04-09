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
 * User data-adapter.
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class UserAdapter extends \Yana\Security\Users\AbstractUserAdapter
{

    /**
     * Returns the database key for the user as: table.userId.
     *
     * @param   string  $userId  name of the account
     * @return  string
     */
    protected function _toDatabaseKey($userId)
    {
        assert('is_string($userId); // Wrong type argument $userId. String expected.');

        return \Yana\Security\Users\Tables\UserEnumeration::TABLE . '.' . \Yana\Util\String::toUpperCase($userId);
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
     * @return  \Yana\Security\Users\IsUser
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
        assert('is_string($userId); // Wrong type argument $userId. String expected.');

        if (!($userEntity instanceof \Yana\Security\Users\IsUser)) {
            assert('!isset($message); // Cannot redeclare var $message');
            $message = "Instance of \Yana\Security\Users\IsUser expected. Found " . \get_class($userEntity) . " instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        if (is_null($userId) || $userId == '') {
            $userId = $userEntity->getId();
        }

        assert('!isset($db); // Cannot redeclare var $db');
        assert('!isset($mapper); // Cannot redeclare var $mapper');
        assert('!isset($userRow); // Cannot redeclare var $userRow');

        $db = $this->_getConnection();
        $mapper = new \Yana\Security\Users\UserMapper();
        $userRow = $mapper->toDatabaseRow($userEntity);
        unset($mapper);

        try {
            if ($this->offsetExists($userId)) { // user exists
                $db->update($this->_toDatabaseKey($userId), $userRow);

            } else { // new user
                $db->insert($this->_toDatabaseKey($userId), $userRow);
                $db->insert(
                    \Yana\Security\Users\Tables\ProfileEnumeration::TABLE . "." . \Yana\Util\String::toUpperCase($userId), // profile id
                    array(\Yana\Security\Users\Tables\ProfileEnumeration::TIME_MODIFIED => time()) // profile row
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

        $upperCaseUserId = \Yana\Util\String::toUpperCase($userId);

        assert('!isset($db); // Cannot redeclare var $db');
        $db = $this->_getConnection();
        try {

            $db // delete profile
                ->remove(\Yana\Security\Users\Tables\ProfileEnumeration::TABLE . "." . $upperCaseUserId)
                // delete user's security level
                ->remove(\Yana\Security\Users\Tables\LevelEnumeration::TABLE,
                    array(\Yana\Security\Users\Tables\LevelEnumeration::USER, "=", $upperCaseUserId), 0)
                // delete access permissions (temporarily) granted by this user
                ->remove(\Yana\Security\Users\Tables\RuleEnumeration::TABLE,
                    array(\Yana\Security\Users\Tables\RuleEnumeration::GRANTED_BY_USER, "=", $upperCaseUserId), 0)
                ->remove(\Yana\Security\Users\Tables\LevelEnumeration::TABLE . ".*",
                    array(\Yana\Security\Users\Tables\LevelEnumeration::GRANTED_BY_USER, "=", $upperCaseUserId), 0)
                // delete user settings
                ->remove($this->_toDatabaseKey($upperCaseUserId))
                // commit changes
                ->commit(); // may throw exception

        } catch (\Exception $e) {

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
        return $this->_getConnection()->length(\Yana\Security\Users\Tables\UserEnumeration::TABLE);
    }

    /**
     * Return an array of all valid identifiers.
     *
     * @return  array
     */
    public function getIds()
    {
        assert('!isset($key); // Cannot redeclare var $key');
        $key = \Yana\Security\Users\Tables\UserEnumeration::TABLE . '.*.' . \Yana\Security\Users\Tables\UserEnumeration::ID;
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


    /**
     * Get user groups.
     *
     * Returns an array of group names, where the keys are the group ids and the values are
     * the human-readable group names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function getGroups($userId)
    {
        assert('is_string($userId); // Wrong type argument $userId. String expected.');

        $from = \Yana\Security\Users\Tables\RuleEnumeration::TABLE . ".*." . \Yana\Security\Users\Tables\RuleEnumeration::GROUP;
        $where = array(\Yana\Security\Users\Tables\RuleEnumeration::USER, '=', \Yana\Util\String::toUpperCase($userId));
        // The database API adds the profile-id to the where clause automatically. So there is not need for us to check for that here
        return $this->_getConnection()->select($from, $where);
    }

    /**
     * Get user roles.
     *
     * Returns an array of role names, where the keys are the group ids and the values are
     * the human-readable role names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @param   string  $userId  
     * @return  array
     */
    public function getRoles($userId)
    {
        assert('is_string($userId); // Wrong type argument $userId. String expected.');

        $from = \Yana\Security\Users\Tables\RuleEnumeration::TABLE . ".*." . \Yana\Security\Users\Tables\RuleEnumeration::ROLE;
        $where = array(\Yana\Security\Users\Tables\RuleEnumeration::USER, '=', \Yana\Util\String::toUpperCase($userId));
        // The database API adds the profile-id to the where clause automatically. So there is not need for us to check for that here
        return $this->_getConnection()->select($from, $where);
    }

    /**
     * Set security level.
     *
     * Sets the user's security level to an integer value.
     * The value must be greater or equal 0 and less or equal 100.
     *
     * @param   int     $level          new security level [0,100]
     * @param   string  $userId         user to update
     * @param   string  $profileId      profile to update
     * @param   string  $currentUserId  currently logged in user
     * @return  \Yana\Security\Users\UserAdapter
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  on database error
     * @throws  \Yana\Db\CommitFailedException                   on database error
     * @throws  \Yana\Core\Exceptions\User\NotFoundException     when user not found
     */
    public function setSecurityLevel($level, $userId, $profileId, $currentUserId)
    {
        assert('is_int($level); // Wrong type for argument $level. Integer expected');
        assert('$level >= 0; // Argument $level must not be lesser 0');
        assert('$level <= 100; // Argument $level must not be greater 100');
        assert('is_string($userId); // Wrong type for argument $userId. String expected');
        assert('is_string($profileId); // Wrong type for argument $profileId. String expected');
        assert('is_string($currentUserId); // Wrong type for argument $currentUserId. String expected');

        if (empty($userId) || !$this->offsetExists($userId)) {
            throw new \Yana\Core\Exceptions\User\NotFoundException("No such user '$userId'.", \Yana\Log\TypeEnumeration::WARNING);
        }
        if (empty($currentUserId) || !$this->offsetExists($currentUserId)) {
            throw new \Yana\Core\Exceptions\User\NotFoundException("No such user '$currentUserId'.", \Yana\Log\TypeEnumeration::WARNING);
        }

        $profileIdUpperCase = \Yana\Util\String::toUpperCase($profileId);
        $userIdUpperCase = \Yana\Util\String::toUpperCase($userId);
        $currentUserIdUpperCase = \Yana\Util\String::toUpperCase($currentUserId);

        try {
            $database = self::getDatasource();
            $database->remove(\Yana\Security\Users\Tables\LevelEnumeration::TABLE, array(
                    array(\Yana\Security\Users\Tables\LevelEnumeration::USER, '=', $userIdUpperCase),
                    'and',
                    array(
                        array(\Yana\Security\Users\Tables\LevelEnumeration::PROFILE, '=', $profileIdUpperCase),
                        'and',
                        array(\Yana\Security\Users\Tables\LevelEnumeration::GRANTED_BY_USER, '=', $currentUserIdUpperCase)
                    )
                ), 1);
            $database->commit(); // may throw exception
            $database->insert(\Yana\Security\Users\Tables\LevelEnumeration::TABLE, array(
                    \Yana\Security\Users\Tables\LevelEnumeration::USER => $userIdUpperCase,
                    \Yana\Security\Users\Tables\LevelEnumeration::PROFILE => $profileIdUpperCase,
                    \Yana\Security\Users\Tables\LevelEnumeration::LEVEL => $level,
                    \Yana\Security\Users\Tables\LevelEnumeration::GRANTED_BY_USER => $currentUserIdUpperCase,
                    \Yana\Security\Users\Tables\LevelEnumeration::IS_PROXY => true
                ));
            $database->commit(); // may throw exception

        } catch (\Exception $e) {
            $message = "Unable to commit changed security level for user '$userId'.";
            throw new \Yana\Db\Queries\Exceptions\NotCreatedException($message, \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        return $this;
    }

    /**
     * Get security level.
     *
     * Returns the user's security level as an integer value.
     * The default is 0.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  int
     */
    public function getSecurityLevel($userId, $profileId)
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');
        assert('is_string($profileId); // Wrong type for argument $profileId. String expected');

        assert('!isset($query); // Cannot redeclare var $query');
        $query = $this->_buildQueryForSecurityLevels($userId, $profileId);
        return (int) self::getDatasource()->select($query);
    }

    /**
     * Get security levels.
     *
     * Returns all the user's security level as an array, where the keys are the profile names and the values are the levels.
     *
     * @param   string  $userId  user name
     * @return  array
     */
    public function getSecurityLevels($userId)
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');

        assert('!isset($levelColumn); // Cannot redeclare var $levelColumn');
        $levelColumn = \Yana\Util\String::toUpperCase(\Yana\Security\Users\Tables\LevelEnumeration::LEVEL);
        assert('!isset($profileColumn); // Cannot redeclare var $profileColumn');
        $profileColumn = \Yana\Util\String::toUpperCase(\Yana\Security\Users\Tables\LevelEnumeration::PROFILE);
        assert('!isset($database); // Cannot redeclare var $database');
        $database = self::getDatasource();

        assert('!isset($securityLevels); // Cannot redeclare var $securityLevels');
        $securityLevels = array();
        assert('!isset($query); // Cannot redeclare var $query');
        $query = $this->_buildQueryForSecurityLevels($userId);
        assert('!isset($row); // Cannot redeclare var $row');
        foreach ($database->select($query) as $row)
        {
            if (isset($row[$levelColumn]) && isset($row[$profileColumn])) {
                $securityLevels[(string) $row[$profileColumn]] = (int) $row[$levelColumn];
            }
        }
        unset($row);

        return $securityLevels;
    }

    /**
     * Build and return query to select all security levels.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  \Yana\Db\Queries\Select
     */
    private function _buildQueryForSecurityLevels($userId, $profileId = '')
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');
        assert('is_string($profileId); // Wrong type for argument $profileId. String expected');

        assert('!isset($userIdUpperCase); // Cannot redeclare var $userIdUpperCase');
        $userIdUpperCase = \Yana\Util\String::toUpperCase($userId);
        assert('!isset($profileIdUpperCase); // Cannot redeclare var $profileIdUpperCase');
        $profileIdUpperCase = \Yana\Util\String::toUpperCase($profileId);

        $query = new \Yana\Db\Queries\Select($this->_getConnection());
        $query->setTable(\Yana\Security\Users\Tables\LevelEnumeration::TABLE);
        if ($profileId === "") {

            // Select level, profile from table where user = $1
            $query->setColumns(
                array(\Yana\Security\Users\Tables\LevelEnumeration::LEVEL, \Yana\Security\Users\Tables\LevelEnumeration::PROFILE)
            );
            $query->setWhere(array(\Yana\Security\Users\Tables\LevelEnumeration::USER, '=', $userIdUpperCase));

        } else {

            // Select level from table where user = $1 and profile = $2 order by level desc limit 1
            $query->setColumn(\Yana\Security\Users\Tables\LevelEnumeration::LEVEL);
            $query->setWhere(array(
                array(\Yana\Security\Users\Tables\LevelEnumeration::USER, '=', $userIdUpperCase),
                'and',
                array(\Yana\Security\Users\Tables\LevelEnumeration::PROFILE, '=', $profileIdUpperCase)
            ));
            $query->setOrderBy(array(\Yana\Security\Users\Tables\LevelEnumeration::LEVEL), array(true));
            $query->setLimit(1);
        }

        return $query;
    }

}

?>