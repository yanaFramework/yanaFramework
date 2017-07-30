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
     * Returns bool(true) if an entry with the given id exists.
     *
     * @param   int  $offset  primary key
     * @return  bool
     */
    public function offsetExists($offset)
    {
        assert('is_int($offset); // Invalid argument $offset: int expected');

        assert('!isset($query); // Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\SelectExist($this->_getConnection());
        $query
            ->setTable(\Yana\Security\Data\Tables\RuleEnumeration::TABLE)
            ->setRow((int) $offset);
        return $query->doesExist();
    }

    /**
     * Find and return the requested entity.
     *
     * When there is none, returns NULL instead.
     *
     * @param   int  $offset  primary key
     * @return  \Yana\Security\Data\SecurityRules\IsRule
     */
    public function offsetGet($offset)
    {
        assert('is_int($offset); // Invalid argument $offset: int expected');

        assert('!isset($query); // Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\Select($this->_getConnection());
        $query
            ->setTable(\Yana\Security\Data\Tables\RuleEnumeration::TABLE)
            ->setRow((int) $offset);
        assert('!isset($row); // Cannot redeclare var $row');
        return $this->_getEntityMapper()->toEntity($query->getResults());
    }

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

        if (is_null($offset) || $offset == 0) {
            $offset = $entity->getId();
        }

        assert('!isset($db); // Cannot redeclare var $db');
        $db = $this->_getConnection();
        assert('!isset($row); // Cannot redeclare var $row');
        $row = $this->_getEntityMapper()->toDatabaseRow($entity);

        try {
            if ($this->offsetExists($offset)) { // entry exists
                $db->update($this->_toDatabaseKey($offset), $row);

            } else { // new user
                $db->insert($this->_toDatabaseKey($offset), $row);
                $db->insert(
                    \Yana\Security\Data\Tables\ProfileEnumeration::TABLE . "." . \Yana\Util\Strings::toUpperCase($offset), // profile id
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

        return $entity;

        assert('!isset($mapper); // Cannot redeclare var $mapper');
        $mapper = $this->_getEntityMapper();
        $row = $mapper->toDatabaseRow($entity);
        $db = $this->_getConnection();
        try {
            $db->insertOrUpdate($key, $row)->commit();

        } catch (\Exception $e) {
            $db->rollback();
        }
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

        assert('!isset($db); // Cannot redeclare var $db');
        $db = $this->_getConnection();
        try {
            assert('!isset($query); // Cannot redeclare var $query');
            $query = new \Yana\Db\Queries\Delete($db);
            $query->setTable(\Yana\Security\Data\Tables\RuleEnumeration::TABLE)->setRow($offset);
            $db->remove($query);
            $db->commit(); // may throw exception

        } catch (\Exception $e) {

            $db->rollback(); // don't try to commit the faulty statement again
            assert('!isset($message); // Cannot redeclare var $message');
            $message = "Unable to commit changes to the database server while trying to remove rule '{$offset}'.";
            assert('!isset($level); // Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException($message, $level, $e);
        }
    }

    /**
     * Returns the number of entries in the table.
     *
     * @return  int
     */
    public function count()
    {
        assert('!isset($query); // Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\SelectCount($this->_getConnection());
        return $query->setTable(\Yana\Security\Data\Tables\RuleEnumeration::TABLE)->countResults();
    }

    /**
     * Returns a list of database ids.
     *
     * @return  int[]
     */
    public function getIds()
    {
        assert('!isset($query); // Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\Select($this->_getConnection());
        $query
            ->setTable(\Yana\Security\Data\Tables\RuleEnumeration::TABLE)
            ->setColumn(\Yana\Security\Data\Tables\RuleEnumeration::ID);
        return $query->getResults();
    }

    /**
     * Saves the rule data to the database.
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
     * Get security levels.
     *
     * Returns all the user's security level as an array, where the keys are the profile names and the values are the levels.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  \Yana\Security\Data\SecurityRules\IsCollection
     */
    public function findEntities($userId, $profileId = "")
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');
        assert('is_string($profileId); // Wrong type for argument $profileId. String expected');

        assert('!isset($entities); // Cannot redeclare var $entities');
        $entities = new \Yana\Security\Data\SecurityRules\Collection();

        assert('!isset($query); // Cannot redeclare var $query');
        $query = $this->_buildQuery($userId, $profileId);
        assert('!isset($row); // Cannot redeclare var $row');
        foreach ($query->getResults() as $row)
        {
            $entities[] = $this->_getEntityMapper()->toEntity($row);
        }
        unset($row);

        return $entities;
    }

    /**
     * Build and return query to select all security levels.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  \Yana\Db\Queries\Select
     */
    private function _buildQuery($userId, $profileId = "")
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');
        assert('is_string($profileId); // Wrong type for argument $profileId. String expected');

        assert('!isset($where); // Cannot redeclare var $where');
        $where = array(\Yana\Security\Data\Tables\RuleEnumeration::USER, '=', \Yana\Util\Strings::toUpperCase($userId));
        if ($profileId > "") {
            $where = array(
                $where,
                'and',
                array(\Yana\Security\Data\Tables\RuleEnumeration::PROFILE, '=', \Yana\Util\Strings::toUpperCase($profileId))
            );
        }

        assert('!isset($query); // Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\Select($this->_getConnection());
        $query
                ->setTable(\Yana\Security\Data\Tables\RuleEnumeration::TABLE)
                ->setWhere($where);

        return $query;
    }

}

?>