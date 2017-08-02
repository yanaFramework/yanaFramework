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
     * Returns the database key for the user as: table.id.
     *
     * @param   int  $id  primary key
     * @return  string
     */
    protected function _toDatabaseKey($id)
    {
        assert('is_string($id); // Wrong type argument $id. Integer expected.');

        return \Yana\Security\Data\Tables\LevelEnumeration::TABLE . '.' . (int) $id;
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
            ->setTable(\Yana\Security\Data\Tables\LevelEnumeration::TABLE)
            ->setColumn(\Yana\Security\Data\Tables\LevelEnumeration::ID);
        return $query->getResults();
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
        return $query->setTable(\Yana\Security\Data\Tables\LevelEnumeration::TABLE)->countResults();
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
        $rows = $this->_getConnection()->select($query);
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
        $rows = $this->_getConnection()->select($query);
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
        $query = new \Yana\Db\Queries\Select($this->_getConnection());
        $query
                ->setTable(\Yana\Security\Data\Tables\LevelEnumeration::TABLE)
                ->setWhere($where);

        return $query;
    }

}

?>