<?php
/**
 * User Groups and Roles
 *
 * This plugin adds support for groups and roles to the user authentication methods.
 *
 * {@translation
 *
 *   de:   Nutzergruppen und Rollen
 *
 *         Dieses Plugin fügt Unterstützung für Gruppen und Rollen den Verfahren
 *         zur Nutzerauthentifizierung hinzu.
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       security
 * @extends    user
 * @priority   highest
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * user authentification plugin
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_user_group extends StdClass implements \Yana\IsPlugin
{

    /**
     * default profile id
     *
     * @var     string
     * @access  private
     * @static
     */
    private static $defaultProfileId = null;

    /**
     * user settings
     *
     * @var     array
     * @access  private
     * @static
     */
    private static $userSettings = array();

    /**
     * Registers user-group security rule.
     *
     * @access  public
     */
    public function __construct()
    {
        \Yana\SessionManager::addSecurityRule(array(__CLASS__, 'checkGroupsAndRoles'));
        self::$defaultProfileId = \Yana\Application::getDefault('profile');
    }

    /**
     * Default event handler.
     *
     * Keep this active to ensure, the security rule (checkGroupsAndRoles) is loaded for every event.
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     */
    public function catchAll($event, array $ARGS)
    {
        return true; // Nothing to do: The security rule is called automatically.
    }

    /**
     * check security level
     *
     * @access  public
     * @static
     * @param   \Yana\Db\IsConnection   $database    database
     * @param   array                   $required    required
     * @param   string                  $profileId   profile id
     * @param   string                  $action      action
     * @param   string                  $userName    user Name
     * @return  bool
     *
     * @ignore
     */
    public static function checkGroupsAndRoles(\Yana\Db\IsConnection $database, array $required, $profileId, $action, $userName)
    {
        // skip if not required
        if (empty($required)) {
            return true;
        }
        // rule does not apply
        if (!isset($required[\Yana\Plugins\Annotations\Enumeration::ROLE]) && !isset($required[\Yana\Plugins\Annotations\Enumeration::GROUP])) {
            return null;
        }

        if (isset(self::$userSettings[$userName][$profileId])) {
            $userSettings = self::$userSettings[$userName][$profileId];

        } else {
            if (!isset(self::$userSettings[$userName])) {
                self::$userSettings[$userName] = array();
            }
            if (!isset(self::$userSettings[$userName][$profileId])) {
                self::$userSettings[$userName][$profileId] = array();
            }
            $userSettings =& self::$userSettings[$userName][$profileId];

            // get user default settings
            if (!empty($userName)) {
                $query = new \Yana\Db\Queries\Select($database);
                // get list of user settings
                $query->setTable('securityrules');
                $query->setWhere(array('USER_ID', '=', strtoupper($userName)));
                $securityRules = $database->select($query);
                unset($query);
                if (!empty($securityRules)) {
                    foreach ($securityRules as $key => $item)
                    {
                        if (isset($item['PROFILE'])) {
                            $isDefaultProfile = strcasecmp($item['PROFILE'], self::$defaultProfileId) === 0;
                            $isCurrentProfile = strcasecmp($item['PROFILE'], $profileId) === 0;
                            if (!$isDefaultProfile && !$isCurrentProfile) {
                                unset($securityRules[$key]);
                                continue;
                            }

                        }
                        if (isset($item['GROUP_ID'])) {
                            if (!isset($userSettings['groups'][$item['GROUP_ID']])) {
                                $userSettings['groups'][$item['GROUP_ID']] = array();
                            }
                            $userSettings['groups'][$item['GROUP_ID']][] = $key;
                            if (!isset($item['ROLE_ID'])) {
                                $userSettings['global_groups'][$item['GROUP_ID']] = 1;
                            }
                        }
                        if (isset($item['ROLE_ID'])) {
                            if (!isset($userSettings['roles'][$item['ROLE_ID']])) {
                                $userSettings['roles'][$item['ROLE_ID']] = array();
                            }
                            $userSettings['roles'][$item['ROLE_ID']][] = $key;
                            if (!isset($item['GROUP_ID'])) {
                                $userSettings['global_roles'][$item['ROLE_ID']] = 1;
                            }
                        }
                    }
                    unset($key, $item);
                }
            }
            unset($securityRules);
        }

        // check required role
        if (isset($required[\Yana\Plugins\Annotations\Enumeration::ROLE])) {
            $requiredRole = strtoupper($required[\Yana\Plugins\Annotations\Enumeration::ROLE]);

            // if required role does not match
            if (!isset($userSettings['roles'][$requiredRole])) {
                return false;
            } else {
                assert('!isset($roles); // Cannot redeclare var $requiredRoles');
                $roles = $userSettings['roles'][$requiredRole];
            }

            // role matches and no group is required
            if (!isset($required[\Yana\Plugins\Annotations\Enumeration::GROUP])) {
                return true;

            // if role AND group are both required ...
            } else {
                $requiredGroup = strtoupper($required[\Yana\Plugins\Annotations\Enumeration::GROUP]);
            }

            // if required group does not match
            if (!isset($userSettings['groups'][$requiredGroup])) {
                return false;
            } else {
                assert('!isset($groups); // Cannot redeclare var $requiredGroups');
                $groups = $userSettings['groups'][$requiredGroup];
            }

            // group and role are defined independently
            if (isset($userSettings['global_roles'][$requiredRole])) {
                if (isset($userSettings['global_groups'][$requiredGroup])) {
                    return true;
                }
            }

            // required groups and roles are not defined in correct combination
            if (count(array_intersect($roles, $groups)) === 0) {
                return false;
            }

            // group and role settings match
            return true;
        }
        // if required group does not match
        if (isset($required[\Yana\Plugins\Annotations\Enumeration::GROUP])) {
            if (!isset($userSettings['groups'][strtoupper($required[\Yana\Plugins\Annotations\Enumeration::GROUP])])) {
                return false;
            }
        }
        // match found, return success
        return true;
    }

}

?>