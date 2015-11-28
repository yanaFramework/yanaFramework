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

namespace Yana\Security\Rules;

/**
 * Loads rule requirements from database.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Loader extends \Yana\Core\Object implements \Yana\Log\IsLogable
{

    /**
     * database connection
     *
     * @var  \Yana\Db\IsConnection
     */
    private $_database = null;

    /**
     * @var  \Yana\Log\LoggerCollection
     */
    private $_loggers = null;

    /**
     * Set datasource.
     *
     * @param  \Yana\Db\IsConnection  $database  data-source
     */
    public function __construct(\Yana\Db\IsConnection $database)
    {
        $this->_database = $database;
    }

    /**
     * Get datasource.
     *
     * @return  \Yana\Db\IsConnection
     * @ignore
     */
    public function getDatasource()
    {
        return $this->_database;
    }

    /**
     * Adds a logger to the class.
     *
     * @param  \Yana\Log\IsLogger  $logger  instance that will handle the logging
     */
    public function attachLogger(\Yana\Log\IsLogger $logger)
    {
        $collection = $this->getLogger();
        $collection[] = $logger;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger()
    {
        if (!isset($this->_loggers)) {
            $this->_loggers = new \Yana\Log\LoggerCollection();
            $this->_loggers = \Yana\Log\LogManager::getLogger();
        }
        return $this->_loggers;
    }

    /**
     * Check permission.
     *
     * Check if user has permission to apply changes to the profile identified
     * by the argument $profileId.
     *
     * Returns bool(true) if the user's permission level is high enough to
     * execute the changes and bool(false) otherwise.
     *
     * @param   string  $action  name of the action the user tries to execute
     * @return  \Yana\Security\Rules\Requirements\Collection
     */
    public function loadRequirementsFromDatabase($action = null)
    {
        assert('is_null($action) || is_string($action); // Invalid argument $action: String expected');

        $database = $this->getDatasource();

        if ($database->isEmpty("securityactionrules")) {
            throw new \Yana\Core\Exceptions\NotFoundException("No requirements found.");
        }

        assert('!isset($actionrules); // Cannot redeclare var $actionrules');
        $actionrules = $database->select("securityactionrules", array('action_id', '=', $action));

        // if not defined, load defaults
        if (empty($actionrules)) {
            $actionrules = \Yana\Application::getDefault('event.user');
            if (!empty($actionrules)) {
                $actionrules = array($actionrules);
            }
        }

        assert('!isset($requirements); // Cannot redeclare $requirements');
        $requirements = $this->loadRequirementsFromArray($actionrules);

        return $requirements;
    }

    /**
     * Build requirement collection from input array.
     *
     * The list of $actionrules must be an array containing the following information (in the given order):
     * <ul>
     *   <li> \Yana\Plugins\Annotations\Enumeration::GROUP  required user group </li>
     *   <li> \Yana\Plugins\Annotations\Enumeration::ROLE   required user role </li>
     *   <li> \Yana\Plugins\Annotations\Enumeration::LEVEL  required security level </li>
     * </ul>
     *
     * @param   array  $actionrules  2-dimensional array containing a list of groups, roles and levels required
     * @return  \Yana\Security\Rules\Requirements\Collection
     */
    public function loadRequirementsFromArray(array $actionrules)
    {
        assert('!isset($requirements); // Cannot redeclare $requirements');
        $requirements = new \Yana\Security\Rules\Requirements\Collection();

        assert('!isset($actionrule); // Cannot redeclare $actionrule');
        foreach ($actionrules as $actionrule) {

            $requirements[] = new \Yana\Security\Rules\Requirements\Requirement(
                (string) $actionrule[\Yana\Plugins\Annotations\Enumeration::GROUP],
                (string) $actionrule[\Yana\Plugins\Annotations\Enumeration::ROLE],
                (int) $actionrule[\Yana\Plugins\Annotations\Enumeration::LEVEL]
            );
        }
        unset($actionrule);

        return $requirements;
    }

    /**
     * Rescan plugin list and refresh the action security settings.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurations  methods for which to load the security settings
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  if the existing entries could not be deleted
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     */
    public function refreshPluginSecurityRules(\Yana\Plugins\Configs\MethodCollection $eventConfigurations)
    {
        assert('!isset($database); // Cannot redeclare $database');
        $database = $this->getDatasource();
        try {
            // remove old predefined security settings
            $database->remove('securityactionrules', array('actionrule_predefined', '=', true), 0);
            // remove old actions
            $database->remove('securityaction', array(), 0);
        } catch (\Yana\Core\Exceptions\NotWriteableException $e) {

            $database->rollback();
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException("Unable to delete old entries.");
        }
        $rows = array();
        $groups = array();
        $roles = array();
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
            if (!isset($actions[$name]) || $actions[$name]['action_title'] == $name) {
                if (empty($title)) {
                    $title = $name;
                }
                $actions[$name] = array(
                    'action_id' => $name,
                    'action_title' => $title
                );
            }
            assert('!isset($row); // Cannot redeclare var $row');
            assert('!isset($level); // Cannot redeclare var $level');
            foreach ($configuration->getUserLevels() as $level)
            {
                /* @var $level PluginUserLevel */
                $row = array(
                    'actionrule_predefined' => true,
                    'action_id' => $name,
                    'group' => mb_strtolower($level->getGroup()),
                    'role' => mb_strtolower($level->getRole()),
                    'level' => (int) $level->getLevel()
                );
                if (empty($row['group'])) {
                    unset($row['group']);
                } else {
                    $groups[] = $row['group'];
                }
                if (empty($row['role'])) {
                    unset($row['role']);
                } else {
                    $roles[] = $row['role'];
                }
                if (empty($row['level'])) {
                    unset($row['level']);
                }
                $rows[] = $row;
            }
            unset($level, $name, $title, $row);
        }
        unset($configuration);
        // insert new actions
        assert('!isset($row); // Cannot redeclare var $row');
        foreach ($actions as $row)
        {
            try {
                $database->insert('securityaction', $row);
            } catch (\Exception $e) {
                $database->rollback();
                throw new \Yana\Db\Queries\Exceptions\NotCreatedException("Unable to insert new action.");
            }
        }
        unset($actions, $row);
        // insert new groups
        assert('!isset($groupId); // Cannot redeclare var $groupId');
        assert('!isset($group); // Cannot redeclare var $group');
        foreach (array_unique($groups) as $groupId)
        {
            if ($database->exists("securitygroup.$groupId")) {
                continue;
            }
            $group = array('group_id' => $groupId, 'group_name' => $groupId);
            try {
                $database->insert("securitygroup.$groupId", $group);
            } catch (\Exception $e) {
                $database->rollback();
                throw new \Yana\Db\Queries\Exceptions\NotCreatedException("Unable to insert new group.");
            }
            unset($group);
        }
        unset($groupId, $groups);
        // insert new roles
        assert('!isset($roleId); // Cannot redeclare var $roleId');
        assert('!isset($role); // Cannot redeclare var $role');
        foreach (array_unique($roles) as $roleId)
        {
            if ($database->exists("securityrole.$roleId")) {
                continue;
            }
            $role = array('role_id' => $roleId, 'role_name' => $roleId);
            try {
                $database->insert("securityrole.$roleId", $role);
            } catch (\Exception $e) {
                $database->rollback();
                throw new \Yana\Db\Queries\Exceptions\NotCreatedException("Unable to insert new role.");
            }
            unset($role);
        }
        unset($roleId, $roles);
        // insert new security settings
        assert('!isset($row); // Cannot redeclare var $row');
        foreach ($rows as $row)
        {
            try {
                $database->insert('securityactionrules', $row);
            } catch (\Exception $e) {
                $database->rollback();
                throw new \Yana\Db\Queries\Exceptions\NotCreatedException("Unable to insert new security setting.");
            }
        }
        unset($row);
        $database->commit(); // may throw exception
    }

}

?>