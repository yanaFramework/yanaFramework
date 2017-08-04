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
     * Get security level.
     *
     * Returns the user's security level as an integer value.
     * The default is 0.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  \Yana\Security\Data\SecurityLevels\IsLevel
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching level is found
     */
    public function findEntity($userId, $profileId)
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');
        assert('is_string($profileId); // Wrong type for argument $profileId. String expected');

        assert('!isset($query); // Cannot redeclare var $query');
        $query = $this->_buildQuery($userId, $profileId);
        assert('!isset($rows); // Cannot redeclare var $rows');
        $rows = $this->_getDatabaseConnection()->select($query);
        if (!is_array($rows) || count($rows) !== 1) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }
        assert('!isset($entity); // Cannot redeclare var $entity');
        $entity = $this->_getEntityMapper()->toEntity(current($rows));

        return $entity;
    }

    /**
     * Get security levels.
     *
     * Returns all the user's security level as an array, where the keys are the profile names and the values are the levels.
     *
     * @param   string  $userId  user name
     * @return  \Yana\Security\Data\SecurityLevels\Collection
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching level is found
     */
    public function findEntities($userId)
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');

        assert('!isset($profileColumn); // Cannot redeclare var $profileColumn');
        $profileColumn = \Yana\Util\Strings::toUpperCase(\Yana\Security\Data\Tables\LevelEnumeration::PROFILE);

        assert('!isset($entities); // Cannot redeclare var $entities');
        $entities = new \Yana\Security\Data\SecurityLevels\Collection();

        assert('!isset($query); // Cannot redeclare var $query');
        $query = $this->_buildQuery($userId);
        assert('!isset($rows); // Cannot redeclare var $rows');
        $rows = $this->_getDatabaseConnection()->select($query);
        if (!is_array($rows) || count($rows) === 0) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }
        assert('!isset($row); // Cannot redeclare var $row');
        foreach ($rows as $row)
        {
            $entities[(string) $row[$profileColumn]] = $this->_getEntityMapper()->toEntity($row);
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
        $where = array(\Yana\Security\Data\Tables\LevelEnumeration::USER, '=', \Yana\Util\Strings::toUpperCase($userId));
        if ($profileId > "") {

            $where = array(
                $where,
                'and',
                array(\Yana\Security\Data\Tables\LevelEnumeration::PROFILE, '=', \Yana\Util\Strings::toUpperCase($profileId))
            );
        }

        assert('!isset($query); // Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\Select($this->_getDatabaseConnection());
        $query
                ->setTable(\Yana\Security\Data\Tables\LevelEnumeration::TABLE)
                ->setWhere($where);

        return $query;
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
            $message = "No such level: '$offset'.";
            assert('!isset($level); // Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
        }

        try {
            parent::offsetUnset($offset);

        } catch (\Exception $e) {

            assert('!isset($message); // Cannot redeclare var $message');
            $message = "Unable to commit changes to the database server while trying to remove level '{$offset}'.";
            assert('!isset($level); // Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException($message, $level, $e);
        }
    }

}

?>