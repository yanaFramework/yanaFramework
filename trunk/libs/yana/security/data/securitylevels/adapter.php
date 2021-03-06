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
 * Provides access to security data.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Adapter extends \Yana\Security\Data\SecurityLevels\AbstractAdapter
{

    /**
     * Returns the name of the target table.
     *
     * @return  string
     */
    protected function _getTableName()
    {
        return \Yana\Security\Data\Tables\LevelEnumeration::TABLE;
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
     * Triggered when offsetSet() is called and the offset already exists.
     *
     * Returns the Id used.
     * Note! This doesn't commit the query!
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity      object to be stored
     * @param   scalar                        $optionalId  primary key
     * @return  scalar
     */
    protected function _onUpdate(\Yana\Data\Adapters\IsEntity $entity, $optionalId = null)
    {
        if (is_null($optionalId) || $optionalId == 0) {
            $optionalId = $entity->getId();
        }
        $db = $this->_getDatabaseConnection();
        if ($this->offsetExists($optionalId)) { // entry exists
            $db->remove($this->_getTableName() . '.' . $optionalId);
        }
        $db->insert($this->_getTableName() . '.' . $optionalId, $this->_serializeEntity($entity));
        $db->commit(); // may throw exception
        return $optionalId;
    }

    /**
     * Triggered when offsetSet() is called and the offset doesn't exists.
     *
     * Returns the Id used.
     * Note! This doesn't commit the query!
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity      object to be stored
     * @param   scalar                        $optionalId  primary key
     * @return  scalar
     * @throws  \Yana\Core\Exceptions\User\LevelAlreadyExistsException  if a similar entry already exists
     */
    protected function _onInsert(\Yana\Data\Adapters\IsEntity $entity, $optionalId = null)
    {
        if ($this->hasEntitiesLike($entity)) {
            assert(!isset($message), 'Cannot redeclare var $message');
            $message = "A similar entry does already exists. " .
                "Can't save entity, as this would violate the unique constraint.";
            throw new \Yana\Core\Exceptions\User\LevelAlreadyExistsException($message);
        }
        return parent::_onInsert($entity, $optionalId);
    }

    /**
     * Get security level.
     *
     * Returns the user's highest security level.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  \Yana\Security\Data\SecurityLevels\IsLevel
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching level is found
     */
    public function findEntityOwnedByUser($userId, $profileId)
    {
        assert(is_string($userId), 'Wrong type for argument $userId. String expected');
        assert(is_string($profileId), 'Wrong type for argument $profileId. String expected');

        assert(!isset($listOfEntities), 'Cannot redeclare var $listOfEntities');
        $listOfEntities = $this->_getEntities(
            array(\Yana\Security\Data\Tables\LevelEnumeration::USER, '=', \Yana\Util\Strings::toUpperCase($userId)),
            $profileId
        );
        if (!is_array($listOfEntities) || count($listOfEntities) === 0) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }
        assert(!isset($maxEntity), 'Cannot redeclare var $maxEntity');
        $maxEntity = current($listOfEntities);
        assert(!isset($entity), 'Cannot redeclare var $entity');
        foreach ($listOfEntities as $entity)
        {
            /* @var $entity \Yana\Security\Data\SecurityLevels\IsLevel */
            if ($entity->getSecurityLevel() > $maxEntity->getSecurityLevel()) {
                $maxEntity = $entity;
            }
        }
        unset($entity);

        return $maxEntity;
    }

    /**
     * Get security levels.
     *
     * Returns all the user's security level as a collection.
     *
     * @param   string  $userId  user name
     * @return  \Yana\Security\Data\SecurityLevels\IsCollection
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching level is found
     */
    public function findEntitiesOwnedByUser($userId)
    {
        assert(is_string($userId), 'Wrong type for argument $userId. String expected');

        assert(!isset($listOfEntities), 'Cannot redeclare var $listOfEntities');
        $listOfEntities = $this->_getEntities(
            array(\Yana\Security\Data\Tables\LevelEnumeration::USER, '=', \Yana\Util\Strings::toUpperCase($userId))
        );
        if (!is_array($listOfEntities) || count($listOfEntities) === 0) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }
        assert(!isset($entities), 'Cannot redeclare var $entities');
        $entities = new \Yana\Security\Data\SecurityLevels\Collection();
        $entities->setItems($listOfEntities);

        return $entities;
    }

    /**
     * Get security levels the user created but does not own.
     *
     * Returns all entries this user granted to other users.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  \Yana\Security\Data\SecurityLevels\IsCollection
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching rule is found
     */
    public function findEntitiesGrantedByUser($userId, $profileId = "")
    {
        assert(is_string($userId), 'Wrong type for argument $userId. String expected');

        assert(!isset($listOfEntities), 'Cannot redeclare var $listOfEntities');
        $listOfEntities = $this->_getEntities(
            array(
                array(\Yana\Security\Data\Tables\LevelEnumeration::USER, '!=', \Yana\Util\Strings::toUpperCase($userId)),
                'AND',
                array(\Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER, '=', \Yana\Util\Strings::toUpperCase($userId))
            ),
            $profileId
        );
        if (!is_array($listOfEntities) || count($listOfEntities) === 0) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }
        assert(!isset($entities), 'Cannot redeclare var $entities');
        $entities = new \Yana\Security\Data\SecurityLevels\Collection();
        $entities->setItems($listOfEntities);

        return $entities;
    }

    /**
     * Finds and returns all entities based on the given where clause.
     * 
     * @param   array  $where  where clause to use in SELECT statement
     * @return  \Yana\Data\Adapters\IsEntity[]
     */
    protected function _getEntities(array $where = array(), $profileId = "")
    {
        if ($profileId > "") {
            $profileClause = array(\Yana\Security\Data\Tables\LevelEnumeration::PROFILE, '=', \Yana\Util\Strings::toUpperCase($profileId));
            if (!empty($where)) {
                $where = array(
                    $where,
                    'and',
                    $profileClause
                );
            } else {
                $where = $profileClause;
            }
        }
        return parent::_getEntities($where);
    }

    /**
     * Tries to delete the rule from the database.
     *
     * @param   int  $offset  rule id
     * @throws  \Yana\Core\Exceptions\User\NotFoundException     when no such rule exists
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  when there was a problem with the database
     */
    public function offsetUnset($offset)
    {
        assert(is_int($offset), 'Invalid argument $offset: int expected');

        // entry does not exist
        if (!$this->offsetExists($offset)) {
            assert(!isset($message), 'Cannot redeclare var $message');
            $message = "No such level: '$offset'.";
            assert(!isset($level), 'Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
        }

        try {
            parent::offsetUnset($offset);

        } catch (\Exception $e) {

            assert(!isset($message), 'Cannot redeclare var $message');
            $message = "Unable to commit changes to the database server while trying to remove level '{$offset}'.";
            assert(!isset($level), 'Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException($message, $level, $e);
        }
    }

    /**
     * Checks if a similar entry exists and if so, returns bool(true).
     *
     * This only takes into account the parts of the entity that have non-empty
     * values, and ignores the primary key.
     *
     * If you have to check a primary key, use offsetExists() instead.
     *
     * @param   \Yana\Security\Data\SecurityLevels\IsLevelEntity  $level  compare to this entity
     * @return  bool
     */
    public function hasEntitiesLike(\Yana\Security\Data\SecurityLevels\IsLevelEntity $level)
    {
        assert(!isset($where), 'Cannot redeclare var $where');
        $where = array();
        assert(!isset($columnName), 'Cannot redeclare var $columnName');
        assert(!isset($value), 'Cannot redeclare var $value');
        assert(!isset($clause), 'Cannot redeclare var $clause');
        foreach ($this->_serializeEntity($level) as $columnName => $value)
        {
            // We skip the empty entries
            if ($value === "") {
                continue;
            }
            if ($columnName === \Yana\Security\Data\Tables\LevelEnumeration::ID) {
                continue;
            }
            if ($columnName === \Yana\Security\Data\Tables\LevelEnumeration::HAS_GRANT_OPTION) {
                continue; // The grant option is not part of the unique constraint
            }
            $clause = array($columnName, '=', $value);
            if (empty($where)) {
                $where = $clause;
            } else {
                $where = array($where, 'AND', $clause);
            }
        }
        unset($columnName, $value, $clause);
        assert(!isset($query), 'Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\SelectExist($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName())->setWhere($where);
        return $query->doesExist();
    }

    /**
     * Insert or replace item.
     *
     * The method returns the used entity to allow chained assignments (like this: $a = $b[1] = $c).
     *
     * @param   scalar                                            $offset  index of item to replace
     * @param   \Yana\Security\Data\SecurityLevels\IsLevelEntity  $entity  save this entity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException          if the value is not a valid entity
     * @throws  \Yana\Core\Exceptions\User\LevelAlreadyExistsException  if a similar entry already exists
     * @throws  \Yana\Core\Exceptions\User\LevelNotSavedException       if the commit statement failed
     * @return  \Yana\Security\Data\SecurityLevels\IsLevelEntity
     */
    public function offsetSet($offset, $entity)
    {
        assert(is_int($offset) || is_null($offset), 'Wrong type argument $offset. Integer expected.');

        if (!($entity instanceof \Yana\Security\Data\SecurityLevels\IsLevelEntity)) {
            assert(!isset($className), 'Cannot redeclare var $className');
            $className = \is_object($entity) ? \get_class($entity) : \gettype($entity);
            assert(!isset($message), 'Cannot redeclare var $message');
            $message = "Instance of IsLevelEntity expected. Found " . $className . " instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        try {
            return parent::offsetSet($offset, $entity);

        } catch (\Yana\Db\DatabaseException $e) {

            assert(!isset($message), 'Cannot redeclare var $message');
            $message = "Entity not saved due to a database error.";
            assert(!isset($level), 'Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\LevelNotSavedException($message, $level, $e);
        }
    }

}

?>