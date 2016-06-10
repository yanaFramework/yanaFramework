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
        $this
            ->flushActions() // delete old entries first
            ->insertActions($this->extractActionTitles($eventConfigurations)) // re-insert new actions
            ->insertGroups($this->extractGroupNames($eventConfigurations)) // insert new groups
            ->insertRoles($this->extractRoleNames($eventConfigurations)) // insert new roles
            ->flushRequirements() // delete old entries first
            ->insertRequirements($this->extractRequirements($eventConfigurations)) // insert new security settings
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
        $where = array(\Yana\Security\Users\Tables\RequirementEnumeration::IS_PREDEFINED, '=', true);
        $database = $this->_getDatasource();
        try {
            // remove old predefined security settings
            $database->remove(\Yana\Security\Users\Tables\RequirementEnumeration::TABLE, $where, 0);
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
            $database->remove(\Yana\Security\Users\Tables\ActionEnumeration::TABLE, array(), 0);
        } catch (\Yana\Core\Exceptions\NotWriteableException $e) {
            $database->rollback();
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException("Unable to delete old entries.");
        }
        return $this;
    }

    /**
     * Extract action ids and titles from a collection of event configurations.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurations  requirements information
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     * @return  array
     */
    public function extractActionTitles(\Yana\Plugins\Configs\MethodCollection $eventConfigurations)
    {
        $actions = array();
        /* @var $configuration \Yana\Plugins\Configs\MethodConfiguration */
        foreach ($eventConfigurations as $configuration)
        {
            $name = $configuration->getMethodName();
            $title = $configuration->getTitle();
            /**
             * @todo reactivate this when form creator is done
             * if (!isset($actions[$name]) && !empty($title)) {
             */
            if (!isset($actions[$name]) || $actions[$name] == $name) {
                if (empty($title)) {
                    $title = $name;
                }
                $actions[$name] = $title;
            }
        }
        return $actions;
    }

    /**
     * Extract roles from a collection of event configurations.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurations  requirements information
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     * @return  array
     */
    public function extractRoleNames(\Yana\Plugins\Configs\MethodCollection $eventConfigurations)
    {
        $roles = array();
        /* @var $configuration \Yana\Plugins\Configs\MethodConfiguration */
        foreach ($eventConfigurations as $configuration)
        {
            foreach ($configuration->getUserLevels() as $level)
            {
                if ($level->getRole() === "") {
                    continue;
                } else {
                    $role = mb_strtolower($level->getRole());
                    $roles[$role] = $role;
                }
            }
        }

        return $roles;
    }

    /**
     * Extract groups from a collection of event configurations.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurations  requirements information
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     * @return  array
     */
    public function extractGroupNames(\Yana\Plugins\Configs\MethodCollection $eventConfigurations)
    {
        $roles = array();
        /* @var $configuration \Yana\Plugins\Configs\MethodConfiguration */
        foreach ($eventConfigurations as $configuration)
        {
            foreach ($configuration->getUserLevels() as $level)
            {
                if ($level->getGroup() === "") {
                    continue;
                } else {
                    $role = mb_strtolower($level->getGroup());
                    $roles[$role] = $role;
                }
            }
        }

        return $roles;
    }

    /**
     * Extract rows of requirements from a collection of event configurations.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurations  requirements information
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     * @return  array
     */
    public function extractRequirements(\Yana\Plugins\Configs\MethodCollection $eventConfigurations)
    {
        $rows = array();
        /* @var $configuration \Yana\Plugins\Configs\MethodConfiguration */
        foreach ($eventConfigurations as $configuration)
        {
            assert('!isset($row); // Cannot redeclare var $row');
            assert('!isset($level); // Cannot redeclare var $level');
            foreach ($configuration->getUserLevels() as $level)
            {
                $row = $this->_extractRequirement($level, $configuration->getMethodName());
                $rows[] = $row;
            }
            unset($level, $row);
        }
        unset($configuration);

        return $rows;
    }

    /**
     * Map information of given requirement to an array.
     *
     * @param   \Yana\Plugins\Configs\UserPermissionRule  $level  contains information about requirements
     * @param   string                                    $name   
     * @return  array
     */
    private function _extractRequirement(\Yana\Plugins\Configs\UserPermissionRule $level, $name)
    {
        assert('is_string($name); // Invalid argument type: $name. String expected');
        /* @var $level \Yana\Plugins\Configs\UserPermissionRule */
        $row = array(
            \Yana\Security\Users\Tables\RequirementEnumeration::IS_PREDEFINED => true,
            \Yana\Security\Users\Tables\RequirementEnumeration::ACTION => (string) $name
        );
        if ($level->getGroup() !== "") {
            $row[\Yana\Security\Users\Tables\RequirementEnumeration::GROUP] = mb_strtolower($level->getGroup());
        }
        if ($level->getRole() !== "") {
            $row[\Yana\Security\Users\Tables\RequirementEnumeration::ROLE] = mb_strtolower($level->getRole());
        }
        if ((int) $level->getLevel() !== 0) {
            $row[\Yana\Security\Users\Tables\RequirementEnumeration::LEVEL] = (int) $level->getLevel();
        }

        return $row;
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

        assert('!isset($row); // Cannot redeclare var $row');
        foreach ($rows as $row)
        {
            try {
                $database->insert(\Yana\Security\Users\Tables\RequirementEnumeration::TABLE, $row);
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
     * @see     \Yana\Security\Users\Tables\RoleEnumeration
     * @param   array  $roles  rows for database table
     * @return  \Yana\Security\Rules\Requirements\DataWriter
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     */
    public function insertRoles(array $roles)
    {
        $database = $this->_getDatasource();

        assert('!isset($roleId); // Cannot redeclare var $roleId');
        assert('!isset($role); // Cannot redeclare var $role');
        foreach (array_unique($roles) as $roleId)
        {
            if ($database->exists(\Yana\Security\Users\Tables\RoleEnumeration::TABLE . "." . $roleId)) {
                continue;
            }
            $role = array(
                \Yana\Security\Users\Tables\RoleEnumeration::ID => $roleId,
                \Yana\Security\Users\Tables\RoleEnumeration::NAME => $roleId
            );
            try {
                $database->insert(\Yana\Security\Users\Tables\RoleEnumeration::TABLE . "." . $roleId, $role);
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
     * @see     \Yana\Security\Users\Tables\GroupEnumeration
     * @param   array  $groups  rows for database table
     * @return  \Yana\Security\Rules\Requirements\DataWriter
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     */
    public function insertGroups(array $groups)
    {
        $database = $this->_getDatasource();

        assert('!isset($groupId); // Cannot redeclare var $groupId');
        assert('!isset($group); // Cannot redeclare var $group');
        foreach (array_unique($groups) as $groupId)
        {
            if ($database->exists(\Yana\Security\Users\Tables\GroupEnumeration::TABLE . "." . $groupId)) {
                continue;
            }
            $group = array(
                \Yana\Security\Users\Tables\GroupEnumeration::ID => $groupId,
                \Yana\Security\Users\Tables\GroupEnumeration::NAME => $groupId
            );
            try {
                $database->insert(\Yana\Security\Users\Tables\GroupEnumeration::TABLE . "." . $groupId, $group);
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
     * @see     \Yana\Security\Users\Tables\ActionEnumeration
     * @param   array  $actions  rows for database table
     * @return  \Yana\Security\Rules\Requirements\DataWriter
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     */
    public function insertActions(array $actions)
    {
        $database = $this->_getDatasource();

        assert('!isset($name); // Cannot redeclare var $name');
        assert('!isset($title); // Cannot redeclare var $title');
        assert('!isset($action); // Cannot redeclare var $action');
        foreach ($actions as $name => $title)
        {
            $action = array(
                \Yana\Security\Users\Tables\ActionEnumeration::ID => $name,
                \Yana\Security\Users\Tables\ActionEnumeration::TITLE => $title
            );
            try {
                $database->insert(\Yana\Security\Users\Tables\ActionEnumeration::TABLE, $action);
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