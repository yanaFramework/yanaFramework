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
            $mapper = new \Yana\Security\Data\SecurityRules\Mapper();
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
     * @return  \Yana\Security\Data\SecurityRules\Rule
     */
    public function findEntity($userId, $profileId)
    {
        assert('is_string($userId); // Wrong type for argument $userId. String expected');
        assert('is_string($profileId); // Wrong type for argument $profileId. String expected');

        $query = $this->_buildQuery($userId, $profileId);
        $row = $this->_getConnection()->select($query);
        $entity = $this->_getEntityMapper()->toEntity($row);
        return $entity;
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

        assert('!isset($profileColumn); // Cannot redeclare var $profileColumn');
        $profileColumn = \Yana\Util\String::toUpperCase(\Yana\Security\Data\Tables\RuleEnumeration::PROFILE);

        assert('!isset($entities); // Cannot redeclare var $entities');
        $entities = array();

        assert('!isset($query); // Cannot redeclare var $query');
        $query = $this->_buildQuery($userId);
        assert('!isset($row); // Cannot redeclare var $row');
        foreach ($this->_getConnection()->select($query) as $row)
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
        $where = array(\Yana\Security\Data\Tables\RuleEnumeration::USER, '=', \Yana\Util\String::toUpperCase($userId));
        if ($profileId > "") {

            $where = array(
                $where,
                'and',
                array(\Yana\Security\Data\Tables\RuleEnumeration::PROFILE, '=', \Yana\Util\String::toUpperCase($profileId))
            );
        }

        assert('!isset($query); // Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\Select($this->_getConnection());
        $query
                ->setTable(\Yana\Security\Data\Tables\RuleEnumeration::TABLE)
                ->setWhere($where);

        return $query;
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

        $from = \Yana\Security\Data\Tables\RuleEnumeration::TABLE . ".*." . \Yana\Security\Data\Tables\RuleEnumeration::GROUP;
        $where = array(\Yana\Security\Data\Tables\RuleEnumeration::USER, '=', \Yana\Util\String::toUpperCase($userId));
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

        $from = \Yana\Security\Data\Tables\RuleEnumeration::TABLE . ".*." . \Yana\Security\Data\Tables\RuleEnumeration::ROLE;
        $where = array(\Yana\Security\Data\Tables\RuleEnumeration::USER, '=', \Yana\Util\String::toUpperCase($userId));
        // The database API adds the profile-id to the where clause automatically. So there is not need for us to check for that here
        return $this->_getConnection()->select($from, $where);
    }

}

?>