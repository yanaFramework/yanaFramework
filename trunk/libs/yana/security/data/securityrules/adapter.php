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

namespace Yana\Security\Data\SecurityRules;

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
class Adapter extends \Yana\Security\Data\SecurityRules\AbstractAdapter
{

    /**
     * Returns the name of the target table.
     *
     * @return  string
     */
    protected function _getTableName()
    {
        return \Yana\Security\Data\Tables\RuleEnumeration::TABLE;
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
     * Insert or replace entity.
     *
     * @param   scalar                                          $offset  index of item to replace
     * @param   \Yana\Security\Data\SecurityRules\IsRuleEntity  $entity  this will go to the database
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid entity
     * @return  mixed
     */
    public function offsetSet($offset, $entity)
    {
        assert('is_int($offset) || is_null($offset); // Wrong type argument $offset. Integer expected.');

        if (!($entity instanceof \Yana\Security\Data\SecurityRules\IsRuleEntity)) {
            assert('!isset($className); // Cannot redeclare var $className');
            $className = \is_object($entity) ? \get_class($entity) : \gettype($entity);
            assert('!isset($message); // Cannot redeclare var $message');
            $message = "Instance of \Yana\Security\Data\SecurityRules\IsRuleEntity expected. Found " . $className . " instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        try {
            parent::offsetSet($offset, $entity);

        } catch (\Exception $e) {

            assert('!isset($message); // Cannot redeclare var $message');
            $message = "Rule not saved due to a database error.";
            assert('!isset($level); // Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw $e;
            throw new \Yana\Core\Exceptions\User\UserException($message, $level, $e);
        }

        return $entity;
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
        assert('is_int($offset); // Invalid argument $offset: int expected');

        // entry does not exist
        if (!$this->offsetExists($offset)) {
            assert('!isset($message); // Cannot redeclare var $message');
            $message = "No such rule: '$offset'.";
            assert('!isset($level); // Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
        }

        try {
            parent::offsetUnset($offset);

        } catch (\Exception $e) {

            assert('!isset($message); // Cannot redeclare var $message');
            $message = "Unable to commit changes to the database server while trying to remove rule '{$offset}'.";
            assert('!isset($level); // Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException($message, $level, $e);
        }
    }

    /**
     * Get security levels.
     *
     * Returns all the user's security level as an array, where the keys are the profile names and the values are the levels.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  \Yana\Security\Data\SecurityRules\IsCollection
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching rule is found
     */
    public function findEntitiesOwnedByUser($userId, $profileId = "")
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');
        assert('is_string($profileId); // Wrong type for argument $profileId. String expected');

        assert('!isset($entities); // Cannot redeclare var $entities');
        $entities = new \Yana\Security\Data\SecurityRules\Collection();

        $entities->setItems( // From Table ...
            $this->_getEntities( // Where ...
                array(\Yana\Security\Data\Tables\RuleEnumeration::USER, '=', \Yana\Util\Strings::toUpperCase($userId)),
                $profileId
            )
        );

        if ($entities->count() === 0) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }

        return $entities;
    }

    /**
     * Get security levels the user created but does not own.
     *
     * Returns all entries this user granted to other users.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  \Yana\Security\Data\SecurityRules\IsCollection
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching rule is found
     */
    public function findEntitiesGrantedByUser($userId, $profileId = "")
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');
        assert('is_string($profileId); // Wrong type for argument $profileId. String expected');

        assert('!isset($entities); // Cannot redeclare var $entities');
        $entities = new \Yana\Security\Data\SecurityRules\Collection();

        $entities->setItems( // From Table ...
            $this->_getEntities( // Where ...
                array(
                    array(\Yana\Security\Data\Tables\RuleEnumeration::GRANTED_BY_USER, '=', \Yana\Util\Strings::toUpperCase($userId)),
                    'AND',
                    array(\Yana\Security\Data\Tables\RuleEnumeration::USER, '!=', \Yana\Util\Strings::toUpperCase($userId))
                ),
                $profileId
            )
        );

        if ($entities->count() === 0) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }

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
            $profileClause = array(\Yana\Security\Data\Tables\RuleEnumeration::PROFILE, '=', \Yana\Util\Strings::toUpperCase($profileId));
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

}

?>