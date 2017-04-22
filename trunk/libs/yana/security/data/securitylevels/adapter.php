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
class Adapter extends \Yana\Security\Data\AbstractAdapter
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
     * Get security level.
     *
     * Returns the user's security level as an integer value.
     * The default is 0.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  \Yana\Security\Data\SecurityLevels\Level
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching level is found
     */
    public function findEntity($userId, $profileId)
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');
        assert('is_string($profileId); // Wrong type for argument $profileId. String expected');

        $query = $this->_buildQuery($userId, $profileId);
        assert('!isset($rows); // Cannot redeclare var $rows');
        $rows = $this->_getConnection()->select($query);
        if (!is_array($rows) || count($rows) === 1) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }
        assert('!isset($entity); // Cannot redeclare var $entity');
        $entity = new \Yana\Security\Data\SecurityLevels\Level(0, true);

        return $entity;
    }

    /**
     * Get security levels.
     *
     * Returns all the user's security level as an array, where the keys are the profile names and the values are the levels.
     *
     * @param   string  $userId  user name
     * @return  \Yana\Security\Data\SecurityLevels\Level[]
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching level is found
     */
    public function findEntities($userId)
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');

        assert('!isset($profileColumn); // Cannot redeclare var $profileColumn');
        $profileColumn = \Yana\Util\Strings::toUpperCase(\Yana\Security\Data\Tables\LevelEnumeration::PROFILE);

        assert('!isset($entities); // Cannot redeclare var $entities');
        $entities = array();

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