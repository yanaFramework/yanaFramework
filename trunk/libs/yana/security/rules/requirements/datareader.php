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

namespace Yana\Security\Rules\Requirements;

/**
 * Helps with loading requirements from a data-source.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class DataReader extends \Yana\Security\Rules\Requirements\AbstractDataObject implements \Yana\Security\Rules\Requirements\IsDataReader
{

    /**
     * Maps a database row to a requirement entity.
     *
     * @param   array  $row  a row from database
     * @return  \Yana\Security\Rules\Requirements\IsRequirement
     */
    protected function _mapRowFromDatabasetoEntity(array $row)
    {
        $groupName = strtoupper(\Yana\Security\Data\Tables\RequirementEnumeration::GROUP);
        $roleName = strtoupper(\Yana\Security\Data\Tables\RequirementEnumeration::ROLE);
        $levelName = strtoupper(\Yana\Security\Data\Tables\RequirementEnumeration::LEVEL);
        return new \Yana\Security\Rules\Requirements\Requirement(
            isset($row[$groupName]) ? $row[$groupName] : '',
            isset($row[$roleName]) ? $row[$roleName] : '',
            isset($row[$levelName]) ? (int) $row[$levelName] : 0
        );
    }

    /**
     * Find and return (active) requirements for the given action.
     *
     * An exception is thrown if the datasource is empty.
     * If the datasource is not empty, but no requirements are found for (this) action nonetheless, an empty collection will be returned.
     *
     * @param   string  $action  loaded requirements must be associated with this rule
     * @return  \Yana\Security\Rules\Requirements\Collection
     * @throws  \Yana\Security\Rules\Requirements\NotFoundException  when no rules are found in the datasource
     */
    public function loadRequirementsByAssociatedAction($action)
    {
        assert(is_string($action), 'Invalid argument type: $action. String expected');

        assert(!isset($database), 'Cannot redeclare var $database');
        $database = $this->_getDatasource();

        if ($database->isEmpty(\Yana\Security\Data\Tables\RequirementEnumeration::TABLE)) {
            throw new \Yana\Security\Rules\Requirements\NotFoundException("No security settings found. Trying to auto-refresh table 'securityactionrules'.");
        }

        assert(!isset($requirements), 'Cannot redeclare var $requirements');
        $requirements = new \Yana\Security\Rules\Requirements\Collection();

        assert(!isset($whereClause), 'Cannot redeclare var $whereClause');
        $whereClause = array(
            array(\Yana\Security\Data\Tables\RequirementEnumeration::ACTION, '=', (string) $action),
            'and',
            array(\Yana\Security\Data\Tables\RequirementEnumeration::IS_ACTIVE, '=', true)
        );

        // find the required permission levels to perform the requested action
        assert(!isset($row), 'Cannot redeclare var $row');
        foreach ($database->select(\Yana\Security\Data\Tables\RequirementEnumeration::TABLE, $whereClause) as $row)
        {
            $requirements[] = $this->_mapRowFromDatabasetoEntity($row);
        }
        unset($row, $whereClause);

        return $requirements;
    }

    /**
     * Find and return (active) requirement with the given id.
     *
     * @param   int  $id  of row in table securityactionrules
     * @return  \Yana\Security\Rules\Requirements\IsRequirement
     * @throws  \Yana\Security\Rules\Requirements\NotFoundException  when no such rule is found in the datasource
     */
    public function loadRequirementById($id)
    {
        assert(is_int($id), 'Invalid argument type: $id. Integer expected');

        assert(!isset($row), 'Cannot redeclare var $row');
        $row = $this->_getDatasource()->select(\Yana\Security\Data\Tables\RequirementEnumeration::TABLE . '.' . (string) $id,
            array(\Yana\Security\Data\Tables\RequirementEnumeration::IS_ACTIVE, '=', true));

        if (empty($row)) {
            throw new \Yana\Security\Rules\Requirements\NotFoundException("No such rule found.");
        }

        return $this->_mapRowFromDatabasetoEntity($row);
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
    public function loadListOfGroups()
    {
        return $this->_getDatasource()
            ->select(\Yana\Security\Data\Tables\GroupEnumeration::TABLE . '.*.' . \Yana\Security\Data\Tables\GroupEnumeration::NAME);
    }

    /**
     * Get user roles.
     *
     * Returns an array of role names, where the keys are the group ids and the values are
     * the human-readable role names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function loadListOfRoles()
    {
        return $this->_getDatasource()
            ->select(\Yana\Security\Data\Tables\RoleEnumeration::TABLE . '.*.' . \Yana\Security\Data\Tables\RoleEnumeration::NAME);
    }
}

?>