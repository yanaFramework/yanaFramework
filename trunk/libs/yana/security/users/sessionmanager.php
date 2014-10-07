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

namespace Yana\Security\Users;

/**
 * Session manager.
 *
 * This is a manager class to handle user data and
 * permission levels.
 *
 * @name        SessionManager
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class SessionManager extends \Yana\Core\AbstractSingleton
{

    /**
     * database connection
     *
     * @var  \Yana\Db\IsConnection
     */
    private static $_database = null;

    /**
     * default profile id
     *
     * @var  string
     */
    private static $_defaultProfileId = "DEFAULT";

    /**
     * result cache
     *
     * @ignore
     * @var  array
     */
    protected $cache = array();

    /**
     * @ignore
     * @var  array
     */
    protected static $rules = array();

    /**
     * get instance of this class
     *
     * Creates an instance if there is none.
     * Then it returns a reference to this (single) instance.
     *
     * @return  \Yana\Security\Users\SessionManager
     */
    protected static function _createNewInstance()
    {
        assert('!isset($defaultProfileId); // Cannot redeclare variable $defaultProfileId');
        $defaultProfileId = \Yana\Application::getDefault('profile');
        if (is_string($defaultProfileId)) {
            self::$_defaultProfileId = mb_strtoupper($defaultProfileId);
        }
        return new static();
    }

    /**
     * Returns the class name of the called class.
     *
     * @return string
     */
    protected static function _getClassName()
    {
        return __CLASS__;
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
    public static function getGroups()
    {
        $db = self::getDatasource();
        return $db->select('securitygroup.*.group_name');
    }

    /**
     * get user roles
     *
     * Returns an array of role names, where the keys are the group ids and the values are
     * the human-readable role names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public static function getRoles()
    {
        $db = self::getDatasource();
        return $db->select('securityrole.*.role_name');
    }

    /**
     * Set datasource.
     *
     * @param  \Yana\Db\IsConnection  $database  data-source
     * @ignore
     */
    public static function setDatasource(\Yana\Db\IsConnection $database)
    {
        self::$_database = $database;
    }

    /**
     * Get datasource.
     *
     * @return  \Yana\Db\IsConnection
     * @ignore
     */
    public static function getDatasource()
    {
        if (!isset(self::$_database)) {
            self::$_database = \Yana\Application::connect('user');
        }
        return self::$_database;
    }

    /**
     * Add security rule.
     *
     * This method adds a reference to an user-definded function to a list of custom security checks.
     *
     * The SessionManager class does not implement any security checks itself (except for password
     * checks). Instead the programmer may define any set of functions he sees fit for a particular
     * purpose.
     *
     * To execute these checks call the function {@see SessionManager::checkPermission()}.
     * The functions are executed in the order in which they were added.
     *
     * The parameter $rule must be a valid callback. It is either:
     * <ol>
     * <li> a string containing a function name </li>
     * <li> an array with 2 elements, where the first is a class name and the second is a name of
     *      a static function </li>
     * <li> an array with 2 elements, where the first is an object and the second is the name of a
     *      non-static function </li>
     * </ol>
     *
     * The called functions must return bool(true) if the user ist granted permission to proceed
     * with the requested action and bool(false) otherwise.
     * They may not throw any exception or error.
     *
     * All called functions are provided the following arguments (in the given order):
     * <ol>
     *   <li> DBStream  $database    open connection to user database </li>
     *   <li> array     $required    associative array of required priviliges, according to the
     *                               definition of the requested action.
     *                               It may contain one or all of the following items:
     *     <ul>
     *       <li> PluginAnnotationEnumeration::GROUP  required user group </li>
     *       <li> PluginAnnotationEnumeration::ROLE   required user role </li>
     *       <li> PluginAnnotationEnumeration::LEVEL  required security level </li>
     *     </ul>
     *   </li>
     *   <li> string    $profileId   current application profile id, see: {@see \Yana\Application::getId()} </li>
     *   <li> string    $actionName  name of requested action </li>
     *   <li> string    $userName    user name (may be empty if not logged in) </li>
     * </ol>
     *
     * Example implementation for a custom security plugin:
     * <code>
     * class plugin_my_check extends StdClass implements IsPlugin
     * {
     *   public function __construct()
     *   {
     *     SessionManager::addSecurityRule(array(__CLASS__, '_check'));
     *   }
     *   public static function _check(DBStream $database, array $required,
     *       $profileId, $action, $userName)
     *   {
     *     $manager = SessionManager::getInstance();
     *     $level = $manager->getSecurityLevel($userName, $profileId);
     *     return $required[PluginAnnotationEnumeration::LEVEL] <= $level;
     *   }
     * }
     * </code>
     * The code above returns true, if the user's security level is higher or equal the required
     * level. The check is added when the plugin is created.
     *
     * @param   string  $rule  must be a valid callback
     * @see     SessionManager::checkPermission()
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException when the function is not callable
     */
    public static function addSecurityRule($rule)
    {
        if (!is_callable($rule)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("The argument is not a valid callback function.");
        }
        self::$rules[] = $rule;
    }

    /**
     * Rescan plugin list and refresh the action security settings.
     *
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  if the existing entries could not be deleted
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     *
     * @ignore
     */
    public static function refreshPluginSecuritySettings()
    {
        // remove old predefined security settings
        $where = array('actionrule_predefined', '=', true);
        $database = self::getDatasource();
        if (!$database->remove('securityactionrules', $where, 0)) {
            $database->rollback();
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException("Unable to delete old entries.");
        }
        // remove old actions
        if (!$database->remove('securityaction', array(), 0)) {
            $database->rollback();
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException("Unable to delete old entries.");
        }
        $rows = array();
        $groups = array();
        $roles = array();
        $actions = array();
        $pluginManager = \Yana\Plugins\Manager::getInstance();
        /* @var $configuration \Yana\Plugins\Configs\MethodConfiguration */
        foreach ($pluginManager->getEventConfigurations() as $configuration)
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
            if (!$database->insert('securityaction', $row)) {
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
            if (!$database->insert("securitygroup.$groupId", $group)) {
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
            if (!$database->insert("securityrole.$roleId", $role)) {
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
            if (!$database->insert('securityactionrules', $row)) {
                $database->rollback();
                throw new \Yana\Db\Queries\Exceptions\NotCreatedException("Unable to insert new security setting.");
            }
        }
        unset($row);
        $database->commit(); // may throw exception
    }

    /**
     * check permission
     *
     * Check if user has permission to apply changes to the profile identified
     * by the argument $profileId.
     *
     * Returns bool(true) if the user's permission level is high enough to
     * execute the changes and bool(false) otherwise.
     *
     * @param   string  $profileId  profile id
     * @param   string  $action     action
     * @param   string  $userName   user name
     * @return  bool
     * @since   2.9
     * @ignore
     */
    public function checkPermission($profileId = null, $action = null, $userName = null)
    {
        assert('is_null($profileId) || is_string($profileId); // Wrong type for argument 1. String expected');
        assert('is_null($action) || is_string($action); // Wrong type for argument 2. String expected');
        assert('is_null($userName) || is_string($userName); // Wrong type for argument 3. String expected');

        /* Argument 1 */
        if (empty($profileId)) {
            $profileId = \Yana\Application::getId();
        }
        $profileId = mb_strtoupper("$profileId");
        assert('is_string($profileId);');

        /* Argument 2 */
        if (empty($action)) {
            $action = \Yana\Plugins\Manager::getLastEvent();
            // security restriction on undefined event
            if (empty($action)) {
                return false;
            }
        }
        $action = mb_strtolower("$action");
        assert('is_string($action);');

        /* Argument 3 */
        /**
         * {@internal
         * The user id is resolved by the "user" plugin and stored
         * in a session var called "user_name", so other plugins can look it up.
         * }}
         */
        if (empty($userName)) {
            $userName = \Yana\User::getUserName();

            // if no value is provided, switch to default user
            if (empty($userName)) {
                $userName = '';
            }

        }
        $userName = mb_strtoupper("$userName");
        assert('is_string($userName);');

        /**
         * {@internal
         * check if value has already been processed and cached
         * and if so, return the cached value instead, for a
         * better performance.
         * }}
         */
        if (isset($this->cache["$profileId\\$userName\\$action"])) {
            assert('is_bool($this->cache["$profileId\\\\$userName\\\\$action"]); /* '.
                'unexpected result in cached value */');
            return $this->cache["$profileId\\$userName\\$action"];
        }
        $database = self::getDatasource();
        // if security settings are missing, auto-refresh them and issue a warning
        if ($database->isEmpty("securityactionrules")) {
            self::refreshPluginSecuritySettings();
            $message = "No security settings found. Trying to auto-refresh table 'securityactionrules'.";
            \Yana\Log\LogManager::getLogger()->addLog($message);
            return false;
        }
        // find out what the required permission level is to perform the current action
        assert('!isset($requiredLevels); // Cannot redeclare var $requiredLevels');
        $requiredLevels = $database->select("securityactionrules", array('action_id', '=', $action));
        // if not defined, load defaults
        if (empty($requiredLevels)) {
            $requiredLevels = \Yana\Application::getDefault('event.user');
            if (!empty($requiredLevels)) {
                $requiredLevels = array($requiredLevels);
            }
        }
        // if nothing else is defined, then the current event is public ...
        if (empty($requiredLevels)) {
            $this->cache["$profileId\\$userName\\$action"] = true;
            return true;
        }

        // ... else check user permissions
        assert('!isset($result); // Cannot redeclare var $result');
        $result = false;
        assert('!isset($required); // cannot redeclare $required');
        foreach ($requiredLevels as $required)
        {
            if (self::checkRule($required, $profileId, $action, $userName)) {
                $result = true;
                break;
            }
        }
        unset($required);

        /* cache the result and return it */
        $this->cache["$profileId\\$userName\\$action"] = $result;
        assert('is_bool($result); // return type should be boolean');
        return $result;
    }

    /**
     * Check requirements against given rules.
     *
     * @param   array   $required   list of required privileges
     * @param   string  $profileId  profile id
     * @param   string  $action     action name
     * @param   string  $userName   user name
     * @return  bool
     * @ignore
     */
    public static function checkRule(array $required, $profileId, $action, $userName)
    {
        assert('is_string($profileId); // Wrong argument type argument 2. String expected');
        assert('is_string($action); // Wrong argument type argument 3. String expected');
        assert('is_string($userName); // Wrong argument type argument 4. String expected');

        if (empty($required)) {
            return true;
        }
        $result = false;
        $required = array_change_key_case($required, CASE_LOWER);
        $database = self::getDatasource();
        // loop through rules
        assert('!isset($function); // cannot redeclare $function');
        foreach (self::$rules as $function)
        {
            $allowed = call_user_func($function, $database, $required, $profileId, $action, $userName);
            if ($allowed === false) {
                $result = false;
                break;
            } elseif ($allowed === true) {
                $result = true;
            } else {
                // rule does not apply
            }
        }
        return $result;
    }

    /**
     * Set security level.
     *
     * Sets the user's security level to an integer value.
     * The value must be greater or equal 0 and less or equal 100.
     *
     * @param   int     $level      new security level [0,100]
     * @param   string  $userName   user to update
     * @param   string  $profileId  profile to update
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  on database error
     * @throws  \Yana\Db\CommitFailedException                   on database error
     * @throws  \Yana\Core\Exceptions\NotFoundException          when user not found
     */
    public function setSecurityLevel($level, $userName = '', $profileId = '')
    {
        assert('is_int($level); // Wrong type for argument 1. Integer expected');
        assert('$level >= 0; // Argument 1 must not be lesser 0');
        assert('$level <= 100; // Argument 1 must not be greater 100');
        assert('is_string($userName); // Wrong type for argument 2. String expected');
        assert('is_string($profileId); // Wrong type for argument 3. String expected');

        if (empty($profileId)) {
            $profileId = mb_strtoupper(\Yana\Application::getId());
        } else {
            $profileId = mb_strtoupper($profileId);
        }


        /**
         * {@internal
         * The user id is resolved by the "user" plugin and stored
         * in a session var called "user_name", so other plugins can look it up.
         * }}
         */
        assert('!isset($currentUser); // Cannot redeclare variable $currentUser');
        if (!empty($_SESSION['user_name'])) {
            $currentUser = $_SESSION['user_name'];

        /* default user
         *
         * if no value is provided, switch to default instead
         */
        } else {
            $userName = mb_strtoupper($userName);
            $currentUser = $userName;
        }
        if (empty($userName)) {
            $userName = mb_strtoupper($currentUser);
        }

        if (empty($userName) || !\Yana\User::isUser($userName)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such user '$userName'.", E_USER_WARNING);
        }

        $database = self::getDatasource();
        $remove = $database->remove("securitylevel", array(
                array("user_id", '=', $userName),
                'and',
                array(
                    array("profile", '=', $profileId),
                    'and',
                    array("user_created", '=', $currentUser)
                )
            ), 1);
        if ($remove) {
            $database->commit(); // may throw exception
        }
        $result = $database->insert("securitylevel", array(
                "user_id" => $userName,
                "profile" => $profileId,
                "security_level" => $level,
                "user_created" => $currentUser,
                "user_proxy_active" => true
            ));
        if (!$result) {
            $message = "Unable to commit changed security level for user '$userName'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\NotCreatedException($message, $level);
        }
        $database->commit(); // may throw exception
    }

    /**
     * Get security level.
     *
     * Returns the user's security level as an integer value.
     * The default is 0.
     *
     * @param   string  $userName   user name
     * @param   string  $profileId  profile id
     * @return  int
     */
    public function getSecurityLevel($userName = '', $profileId = '')
    {
        assert('is_string($userName); // Wrong type for argument 1. String expected');
        assert('is_string($profileId); // Wrong type for argument 2. String expected');
        /* Argument 1 */
        if (empty($profileId)) {
            $profileId = \Yana\Application::getId();
        }
        $profileId = mb_strtoupper($profileId);

        /* Argument 2 */
        /**
         * {@internal
         * The user id is resolved by the "user" plugin and stored
         * in a session var called "user_name", so other plugins can look it up.
         * }}
         */
        if (empty($userName)) {
            $userName = (string) \Yana\User::getUserName();
        }

        $level = 0;

        if (!empty($userName)) {
            $database = self::getDatasource();
            // 1) get security level for current profile
            $query = new \Yana\Db\Queries\Select($database);
            $query->setKey('securitylevel.*.security_level');
            $query->setWhere(array(
                array('user_id', '=', $userName),
                'and',
                array('profile', '=', $profileId)
            ));
            $query->setOrderBy(array('security_level'), array(true));
            $query->setLimit(1);
            $level = $database->select($query);

            // 2) fall-back to security level for default profile
            if ((empty($level) || !is_array($level)) && self::$_defaultProfileId != $profileId) {
                $query->setWhere(array(
                    array('user_id', '=', $userName),
                    'and',
                    array('profile', '=', self::$_defaultProfileId)
                ));
                $level = $database->select($query);
            }

            // 3) fall-back to default security level
            if (empty($level) || !is_array($level)) {
                return (int) \Yana\Application::getDefault('user.level');
            }

            $level = array_pop($level);
            assert('is_numeric($level);');
            return (int) $level;

        } else {
            return (int) \Yana\Application::getDefault('user.level');

        }
    }

}

?>