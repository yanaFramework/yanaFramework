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

namespace Yana\Security\Data\SecurityLevels;

/**
 * Security level rule data-adapter.
 *
 * This persistent class provides access to security data.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Deprecated_Adapter extends \Yana\Security\Data\AbstractAdapter
{

    /**
     * <<construct>> Creates a new entity-manager.
     *
     * To create the required connection you may use the following short-hand function:
     * <code>
     * $connection = \Yana\Application::connect("user");
     * </code>
     *
     * If no mapper is given, this function creates and uses an instance of \Yana\Security\Data\SecurityLevels\Mapper
     *
     * @param  \Yana\Db\IsConnection               $connection  database connection to table user
     * @param  \Yana\Data\Adapters\IsEntityMapper  $mapper      simple OR-mapper to convert database entries to objects
     */
    public function __construct(\Yana\Db\IsConnection $connection, \Yana\Data\Adapters\IsEntityMapper $mapper = null)
    {
        if (\is_null($mapper)) {
            $mapper = new \Yana\Security\Data\SecurityLevels\Mapper();
        }
        parent::__construct($connection, $mapper);
    }

    /**
     * Returns the database key for the user as: table.userId.
     *
     * @param   int  $id  primary key
     * @return  string
     */
    protected function _toDatabaseKey($id)
    {
        assert('is_int($id); // Wrong type argument $id. Integer expected.');

        return \Yana\Security\Data\Tables\LevelEnumeration::TABLE . '.' . $id;
    }

    /**
     * Returns bool(true) if a rule with that id exists and bool(false) otherwise.
     *
     * @param   int  $id  primary key
     * @return  bool
     */
    public function offsetExists($id)
    {
        assert('is_int($id); // Wrong type argument $id. Integer expected.');

        return $this->_getConnection()->exists($this->_toDatabaseKey($id));
    }

    /**
     * Loads and returns an user account from the database.
     *
     * @param   int  $id  primary key
     * @return  \Yana\Security\Data\SecurityLevels\Entity
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no such entry exists
     */
    public function offsetGet($id)
    {
        assert('is_int($id); // Wrong type argument $id. Integer expected.');

        assert('!isset($row); // Cannot redeclare var $row');
        $row = $this->_getConnection()->select($this->_toDatabaseKey($id));

        try {
            assert('!isset($entity); // Cannot redeclare var $entity');
            $entity = $this->_getEntityMapper()->toEntity($row);
            $entity->setDataAdapter($this);
            return $entity;

        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {

            throw new \Yana\Core\Exceptions\User\NotFoundException(\Yana\Log\TypeEnumeration::ERROR, "No user found with id: " . $id, $e);
        }
    }

    /**
     * Write an user entry to the database.
     *
     * @param  int                                         $id      primary key
     * @param  \Yana\Security\Data\SecurityLevels\Entity  $entity  the account data
     * @return \Yana\Security\Data\SecurityLevels\Entity
     * @throws \Yana\Core\Exceptions\InvalidArgumentException  when the entity is invalid
     * @throws \Yana\Core\Exceptions\User\UserException        when there was a problem with the database
     */
    public function offsetSet($id, $entity)
    {
        assert('is_int($id); // Wrong type argument $id. Integer expected.');

        if (!($entity instanceof \Yana\Security\Data\SecurityLevels\Entity)) {
            assert('!isset($message); // Cannot redeclare var $message');
            $message = "Instance of \Yana\Security\Data\SecurityLevels\Entity expected. Found " . \get_class($entity) . " instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        if (is_null($id)) {
            $id = (int) $entity->getId();
        }

        assert('!isset($db); // Cannot redeclare var $db');
        assert('!isset($row); // Cannot redeclare var $row');

        $db = $this->_getConnection();
        $row = $this->_getEntityMapper()->toDatabaseRow($entity);

        try {
            if ($this->offsetExists($id)) { // user exists
                $db->update($this->_toDatabaseKey($id), $row);

            } else { // new user
                $db->insert($this->_toDatabaseKey($id), $row);

            }
            $db->commit(); // may throw exception

        } catch (\Yana\Db\DatabaseException $e) {

            throw new \Yana\Core\Exceptions\User\UserException(\Yana\Log\TypeEnumeration::ERROR, "Entity not saved due to a database error.", $e);
        }

        return $entity;
    }

    /**
     * Tries to delete the entry from the database.
     *
     * @param   int  $id  primary key
     * @throws  \Yana\Core\Exceptions\User\NotFoundException     when no such entry exists
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  when there was a problem with the database
     */
    public function offsetUnset($id)
    {
        assert('is_string($userId); // Wrong type argument $userId. String expected.');

        // user does not exist
        if (!$this->offsetExists($id)) {
            throw new \Yana\Core\Exceptions\User\NotFoundException("No such entry: '$id'.", E_USER_WARNING);
        }

        assert('!isset($db); // Cannot redeclare var $db');
        $db = $this->_getConnection();
        try {

            $db->remove(\Yana\Security\Data\Tables\LevelEnumeration::TABLE . "." . $id)
                ->commit(); // may throw exception

        } catch (\Exception $e) {

            $message = "Unable to commit changes to the database server while trying to remove".
                "user '{$id}'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException($message, $level, $e);
        }
    }

    /**
     * Return the number of entries in the database.
     *
     * If the databae is empty this function will return 0.
     *
     * @return  int
     */
    public function count()
    {
        return $this->_getConnection()->length(\Yana\Security\Data\Tables\LevelEnumeration::TABLE);
    }

    /**
     * Return an array of all valid identifiers.
     *
     * @return  array
     */
    public function getIds()
    {
        assert('!isset($key); // Cannot redeclare var $key');
        $key = \Yana\Security\Data\Tables\LevelEnumeration::TABLE . '.*.' . \Yana\Security\Data\Tables\LevelEnumeration::ID;
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
     * Set security level.
     *
     * Sets the user's security level to an integer value.
     * The value must be greater or equal 0 and less or equal 100.
     *
     * @param   int     $level          new security level [0,100]
     * @param   string  $userId         user to update
     * @param   string  $profileId      profile to update
     * @param   string  $currentUserId  currently logged in user
     * @return  \Yana\Security\Data\Users\Adapter
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
            $database->remove(\Yana\Security\Data\Tables\LevelEnumeration::TABLE, array(
                    array(\Yana\Security\Data\Tables\LevelEnumeration::USER, '=', $userIdUpperCase),
                    'and',
                    array(
                        array(\Yana\Security\Data\Tables\LevelEnumeration::PROFILE, '=', $profileIdUpperCase),
                        'and',
                        array(\Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER, '=', $currentUserIdUpperCase)
                    )
                ), 1);
            $database->commit(); // may throw exception
            $database->insert(\Yana\Security\Data\Tables\LevelEnumeration::TABLE, array(
                    \Yana\Security\Data\Tables\LevelEnumeration::USER => $userIdUpperCase,
                    \Yana\Security\Data\Tables\LevelEnumeration::PROFILE => $profileIdUpperCase,
                    \Yana\Security\Data\Tables\LevelEnumeration::LEVEL => $level,
                    \Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER => $currentUserIdUpperCase,
                    \Yana\Security\Data\Tables\LevelEnumeration::IS_PROXY => true
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
    public function findEntity($userId, $profileId)
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
    public function findEntities($userId)
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');

        assert('!isset($levelColumn); // Cannot redeclare var $levelColumn');
        $levelColumn = \Yana\Util\String::toUpperCase(\Yana\Security\Data\Tables\LevelEnumeration::LEVEL);
        assert('!isset($profileColumn); // Cannot redeclare var $profileColumn');
        $profileColumn = \Yana\Util\String::toUpperCase(\Yana\Security\Data\Tables\LevelEnumeration::PROFILE);
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
        $query->setTable(\Yana\Security\Data\Tables\LevelEnumeration::TABLE);
        if ($profileId === "") {

            // Select level, profile from table where user = $1
            $query->setColumns(
                array(\Yana\Security\Data\Tables\LevelEnumeration::LEVEL, \Yana\Security\Data\Tables\LevelEnumeration::PROFILE)
            );
            $query->setWhere(array(\Yana\Security\Data\Tables\LevelEnumeration::USER, '=', $userIdUpperCase));

        } else {

            // Select level from table where user = $1 and profile = $2 order by level desc limit 1
            $query->setColumn(\Yana\Security\Data\Tables\LevelEnumeration::LEVEL);
            $query->setWhere(array(
                array(\Yana\Security\Data\Tables\LevelEnumeration::USER, '=', $userIdUpperCase),
                'and',
                array(\Yana\Security\Data\Tables\LevelEnumeration::PROFILE, '=', $profileIdUpperCase)
            ));
            $query->setOrderBy(array(\Yana\Security\Data\Tables\LevelEnumeration::LEVEL), array(true));
            $query->setLimit(1);
        }

        return $query;
    }

}

?>