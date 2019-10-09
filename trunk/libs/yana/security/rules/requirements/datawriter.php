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
class DataWriter extends \Yana\Security\Rules\Requirements\AbstractDataObject implements \Yana\Security\Rules\Requirements\IsDataWriter
{

    /**
     * Rescan plugin list and refresh the action security settings.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurations  to scan and insert into data-source
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if new entries could not be inserted
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  if existing entries could not be deleted
     * @return  self
     */
    public function __invoke(\Yana\Plugins\Configs\MethodCollection $eventConfigurations)
    {
        assert(!isset($wrappedConfigurations), 'Cannot redeclare var $wrappedConfigurations');
        $wrappedConfigurations = new \Yana\Security\Rules\Requirements\DataWriterHelper($eventConfigurations);

        $this
            ->flushActions() // delete old entries first
            ->insertActions($wrappedConfigurations->getActionTitles()) // re-insert new actions
            ->insertGroups($wrappedConfigurations->getGroupNames()) // insert new groups
            ->insertRoles($wrappedConfigurations->getRoleNames()) // insert new roles
            ->flushRequirements() // delete old entries first
            ->insertRequirements($wrappedConfigurations->getRequirements()) // insert new security settings
            ->commitChanges();

        return $this;
    }

    /**
     * Try to write changes to the data-source.
     *
     * @return  self
     * @throws  \Exception  if there is some unexpected problem with the data-source
     */
    public function commitChanges()
    {
        $this->_getDatasource()->commit(); // may throw exception
        return $this;
    }

    /**
     * Remove all existing requirements.
     *
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  if the existing entries could not be deleted
     * @return  self
     */
    public function flushRequirements()
    {
        $where = array(\Yana\Security\Data\Tables\RequirementEnumeration::IS_PREDEFINED, '=', true);
        $database = $this->_getDatasource();
        try {
            // remove old predefined security settings
            $database->remove(\Yana\Security\Data\Tables\RequirementEnumeration::TABLE, $where, 0);
        } catch (\Yana\Core\Exceptions\NotWriteableException $e) {
            $database->rollback();
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException("Unable to delete old entries.");
        }
        return $this;
    }

    /**
     * Remove all existing actions.
     *
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  if the existing entries could not be deleted
     * @return  self
     */
    public function flushActions()
    {
        $database = $this->_getDatasource();
        try {
            // remove old actions
            $database->remove(\Yana\Security\Data\Tables\ActionEnumeration::TABLE, array(), 0);
        } catch (\Yana\Core\Exceptions\NotWriteableException $e) {
            $database->rollback();
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException("Unable to delete old entries.");
        }
        return $this;
    }

    /**
     * Insert rows into requirements table.
     *
     * @param   array  $rows  of requirement information to insert
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     * @return  self
     */
    public function insertRequirements(array $rows)
    {
        $database = $this->_getDatasource();

        assert(!isset($row), 'Cannot redeclare var $row');
        foreach ($rows as $row)
        {
            try {
                $database->insert(\Yana\Security\Data\Tables\RequirementEnumeration::TABLE, $row);
            } catch (\Exception $e) {
                $database->rollback();
                throw new \Yana\Db\Queries\Exceptions\NotCreatedException("Unable to insert new security setting.");
            }
        }
        unset($row);

        return $this;
    }

    /**
     * Insert new roles.
     *
     * Already existing entries are skipped, so that user-defined names are not overwritten.
     *
     * @see     \Yana\Security\Data\Tables\RoleEnumeration
     * @param   array  $roles  rows for database table
     * @return  \Yana\Security\Rules\Requirements\DataWriter
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     */
    public function insertRoles(array $roles)
    {
        $database = $this->_getDatasource();

        assert(!isset($roleId), 'Cannot redeclare var $roleId');
        assert(!isset($role), 'Cannot redeclare var $role');
        foreach (array_unique($roles) as $roleId)
        {
            if ($database->exists(\Yana\Security\Data\Tables\RoleEnumeration::TABLE . "." . $roleId)) {
                continue;
            }
            $role = array(
                \Yana\Security\Data\Tables\RoleEnumeration::ID => $roleId,
                \Yana\Security\Data\Tables\RoleEnumeration::NAME => $roleId
            );
            try {
                $database->insert(\Yana\Security\Data\Tables\RoleEnumeration::TABLE . "." . $roleId, $role);
            } catch (\Exception $e) {
                $database->rollback();
                throw new \Yana\Db\Queries\Exceptions\NotCreatedException("Unable to insert new role.");
            }
        }
        unset($role, $roleId);

        return $this;
    }

    /**
     * Insert new groups.
     *
     * Already existing entries are skipped, so that user-defined names are not overwritten.
     *
     * @see     \Yana\Security\Data\Tables\GroupEnumeration
     * @param   array  $groups  rows for database table
     * @return  \Yana\Security\Rules\Requirements\DataWriter
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     */
    public function insertGroups(array $groups)
    {
        $database = $this->_getDatasource();

        assert(!isset($groupId), 'Cannot redeclare var $groupId');
        assert(!isset($group), 'Cannot redeclare var $group');
        foreach (array_unique($groups) as $groupId)
        {
            if ($database->exists(\Yana\Security\Data\Tables\GroupEnumeration::TABLE . "." . $groupId)) {
                continue;
            }
            $group = array(
                \Yana\Security\Data\Tables\GroupEnumeration::ID => $groupId,
                \Yana\Security\Data\Tables\GroupEnumeration::NAME => $groupId
            );
            try {
                $database->insert(\Yana\Security\Data\Tables\GroupEnumeration::TABLE . "." . $groupId, $group);
            } catch (\Exception $e) {
                $database->rollback();
                throw new \Yana\Db\Queries\Exceptions\NotCreatedException("Unable to insert new group.");
            }
        }
        unset($group, $groupId);

        return $this;
    }

    /**
     * Insert new actions.
     *
     * Already existing entries are updated.
     *
     * @see     \Yana\Security\Data\Tables\ActionEnumeration
     * @param   array  $actions  rows for database table
     * @return  \Yana\Security\Rules\Requirements\DataWriter
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     */
    public function insertActions(array $actions)
    {
        $database = $this->_getDatasource();

        assert(!isset($name), 'Cannot redeclare var $name');
        assert(!isset($title), 'Cannot redeclare var $title');
        assert(!isset($action), 'Cannot redeclare var $action');
        foreach ($actions as $name => $title)
        {
            $action = array(
                \Yana\Security\Data\Tables\ActionEnumeration::ID => $name,
                \Yana\Security\Data\Tables\ActionEnumeration::TITLE => $title
            );
            try {
                $database->insert(\Yana\Security\Data\Tables\ActionEnumeration::TABLE, $action);
            } catch (\Exception $e) {
                $database->rollback();
                throw new \Yana\Db\Queries\Exceptions\NotCreatedException("Unable to insert new action.");
            }
        }
        unset($action, $name, $title);

        return $this;
    }

}

?>