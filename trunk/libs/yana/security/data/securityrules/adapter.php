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

    public function offsetSet($offset, $value)
    {
        
    }

    public function offsetUnset($offset)
    {
        
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

    public function saveEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        
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