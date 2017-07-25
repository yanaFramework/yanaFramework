<?php
/**
 * User proxies
 *
 * Allows a user to grant a part, or all of it's security privileges to a
 * third-party to either take over tasks to cover the user, or even act on it's
 * behalf as it's proxies.
 *
 * {@translation
 *
 *   de:   Stellvertretung für Nutzer
 *
 *         Erlaubt es einer NutzerIn, einen Teil oder alle seine
 *         Sicherheitsrechte an Dritte zu verleihen, damit diese Aufgaben an
 *         ihrer Stelle übernehmen, oder als Stellvertreter agieren können.
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @extends    user
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\UserProxy;

/**
 * user management plugin
 *
 * This creates forms and implements functions to
 * manage user data.
 *
 * @package    yana
 * @subpackage plugins
 */
class UserProxyPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Default event handler
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     *
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Create edit form
     *
     * this action expects no arguments
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    USER_PROXY
     * @menu        group: setup
     * @title       {lang id="user.32"}
     *
     * @access      public
     */
    public function get_user_proxy()
    {
        $YANA = $this->_getApplication();

        // check user expert setting
        $YANA->setVar('USER_IS_EXPERT', (bool) $this->_getSecurityFacade()->loadUser()->isExpert());

        $currentUser = \Yana\Util\Strings::toUpperCase($this->_getSession()->getCurrentUserName());

        /**
         * get all Users Names
         */
        $users = $this->_getSecurityFacade()->loadListOfUsers();
        if (isset($users[$currentUser])) {
            unset($users[$currentUser]);
        }
        $YANA->setVar("USERLIST", $users);
        unset($users);

        /**
         * get security levels
         */
        $user = $this->_getSecurityFacade()->loadUser($currentUser);
        assert('!isset($levels); // Cannot redeclare var $levels');
        $levels = array();
        assert('!isset($profileId); // Cannot redeclare var $profileId');
        assert('!isset($level); // Cannot redeclare var $level');
        foreach ($user->getAllSecurityLevels() as $profileId => $level)
        {
            /* @var $level \Yana\Security\Data\SecurityLevels\IsLevel */

            if (!$level->isUserProxyActive()) {
                continue;
            }
            $levels[$profileId] = array(
                "SECURITY_ID" => $level->getId(),
                "SECURITY_LEVEL" => $level->getSecurityLevel()
            );
        }
        unset($profileId, $level);
        $YANA->setVar("LEVELS", $levels);
        unset($levels);

        /**
         * get security rules
         */
        assert('!isset($profiles); // Cannot redeclare var $profiles');
        $profiles = array();
        assert('!isset($defaultProfile); // Cannot redeclare var $defaultProfile');
        $defaultProfile = $YANA->getDefault('profile');
        assert('!isset($levels); // Cannot redeclare var $levels');
        $rules = array();
        assert('!isset($rule); // Cannot redeclare var $rule');
        assert('!isset($profileId); // Cannot redeclare var $profileId');
        foreach ($user->getAllSecurityGroupsAndRoles() as $rule)
        {
            /* @var $rule \Yana\Security\Data\SecurityRules\IsRule */

            if (!$rule->isUserProxyActive()) {
                continue;
            }
            $profileId = $rule->getProfile() > "" ? $rule->getProfile() : $defaultProfile;
            if (!isset($rules[$profileId])) {
                $rules[$profileId] = array();
            }
            $rules[$profileId][$rule->getId()] = array(
                "GROUP_ID" => $rule->getGroup(),
                "ROLE_ID" => $rule->getRole()
            );
        }
        unset($rule, $profileId);
        $YANA->setVar("RULES", $rules);
        $YANA->setVar("PROFILES", array_keys($rules)); // set profiles
        unset($profiles, $rules);

        // collect users who are granted security privileges
        $users = array();
        // collect profiles
        $profiles = array();

        $db = \Yana\Security\Data\SessionManager::getDatasource();
        /**
         * get security levels
         */
        assert('!isset($where); // Cannot redeclare var $where');
        $where = array(
            array('USER_CREATED', '=', $currentUser),
            'and',
            array('USER_ID', '!=', $currentUser)
        );
        $rows = $db->select('securitylevel', $where, array("profile", "security_level"), 0, 0, true);
        $YANA->setVar("GRANTED_LEVELS", self::_getLevels($rows, $profiles, $users));
        unset($rows);
        // WHERE clause will be reused

        /**
         * get groups
         */
        $rows = $db->select('securityrules', $where, array("user_id"));
        $YANA->setVar("GRANTED_RULES", self::_getRules($rows, $profiles, $users));
        unset($where, $rows);

        // store list of users with grants
        $YANA->setVar("GRANTED_USERS", $users);

        // store list of profiles with grants
        $YANA->setVar("GRANTED_PROFILES", $profiles);

        return true;
    }

    /**
     * set new user proxy
     *
     * @type        config
     * @template    MESSAGE
     * @user        group: admin, level: 100
     * @onsuccess   goto: GET_USER_PROXY
     * @onerror     goto: GET_USER_PROXY
     *
     * @access      public
     * @return      bool
     * @param       string  $user    user id
     * @param       array   $rules   list of rules to apply
     * @param       array   $levels  list of profile ids
     */
    public function set_user_proxy($user, array $rules = array(), array $levels = array())
    {
        if (empty($rules) && empty($levels)) {
            $message = "Nothing to do: no entry selected to operate on.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $warning = new \Yana\Core\Exceptions\Forms\NothingSelectedException($message, $level);
            throw $warning->setField('rules/levels');
        }

        $db = \Yana\Security\Data\SessionManager::getDatasource();
        $defaultProfile = $this->_getApplication()->getDefault('profile');
        $currentUser = $this->_getSession()->getCurrentUserName();

        foreach ($rules as $i => $ruleId)
        {
            if (is_numeric($ruleId)) {
                $where = array(
                    array("USER_ID", '=', $currentUser),
                    'and',
                    array('USER_PROXY_ACTIVE', '=', true)
                );
                $rule = $db->select("securityrules.{$ruleId}", $where);

                if (empty($rule)) {
                    unset($rules[$i]);
                    continue;
                }
                unset($rule['RULE_ID']);

                $rule['USER_ID'] = $user;
                $rule['USER_CREATED'] = $currentUser;
                $rule['USER_PROXY_ACTIVE'] = false;

                // get all entries where user created is the logged user
                $get = $db->select('securityrules', array('USER_CREATED', '=', $currentUser));

                if (!empty($get)) {
                    foreach ($get as $key)
                    {
                        // check if entry already exist
                        switch (true)
                        {
                            case $key['USER_ID'] != $user:
                            case $key['GROUP_ID'] != $rule['GROUP_ID']:
                            case $key['ROLE_ID'] != $rule['ROLE_ID']:
                            case $key['PROFILE'] != $rule['PROFILE']:
                                // does not match
                                continue;
                                break;
                            default:
                                // entry is the same
                                unset($rule);
                                break;
                        }
                    }
                }
                unset($get, $key);

                if (isset($rule)) {
                    try {
                        $db->insert("securityrules", $rule);
                    } catch (\Exception $e) {
                        return false;
                    }
                }
            } else {
                unset($rules[$i]);
            }
        }

        $securityFacade = $this->_getSecurityFacade();
        foreach ($levels as $i => $profileId)
        {

            if (empty($profileId)) {
                $profileId = $defaultProfile;
            }

            $level = $securityFacade->loadUser($currentUser)->getSecurityLevel($profileId);
            $row = array();
            if (is_int($level)) {
                $row = array(
                    'USER_ID' => $user,
                    'PROFILE' => $profileId,
                    'SECURITY_LEVEL' => $level,
                    'USER_CREATED' => $currentUser,
                    'USER_PROXY_ACTIVE' => false
                );
                // get all entries where user created is the logged user
                $get = $db->select('securitylevel', array('USER_CREATED', '=', $currentUser));
                if (!empty($get) && isset($row)) {
                    foreach ($get as $key)
                    {
                        // check if entry already exist
                        switch (true)
                        {
                            case $key['USER_ID'] != $user:
                            case $key['SECURITY_LEVEL'] != $row['SECURITY_LEVEL']:
                            case $key['PROFILE'] != $row['PROFILE']:
                                // does not match
                                continue;
                                break;
                            default:
                                // entry is the same
                                unset($row);
                                break;
                        }
                    }
                }
                unset($get, $key);
                if (isset($row)) {
                    try {
                        $db->insert("securitylevel", $row);
                    } catch (\Exception $e) {
                        return false;
                    }
                }
            } else {
                unset($levels[$i]);
            }
        }
        $db->commit(); // may throw exception
        return true;
    }

    /**
     * remove new user proxy
     *
     * Arguments:
     * - array   $rules    List of ids (table: securityrules)
     * - array   $levels   List of ids (table: securitylevel)
     * - string  $user     name of a user to limit changes to
     *
     * Either
     *
     * Constraint: for all removed entries, the user who created the entry must equal
     * the current user.
     *
     * @type        config
     * @template    MESSAGE
     * @user        group: admin, level: 100
     * @onsuccess   goto: GET_USER_PROXY
     * @onerror     goto: GET_USER_PROXY
     *
     * @access      public
     * @return      bool
     * @param       string  $user    user id
     * @param       array   $rules   list of rules to apply
     * @param       array   $levels  list of profile ids
     */
    public function remove_user_proxy($user = "", array $rules = array(), array $levels = array())
    {
        $user = mb_strtoupper($user);

        if (empty($rules) && empty($levels)) {
            $message = "Nothing to do: no entry selected to operate on.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $warning = new \Yana\Core\Exceptions\Forms\NothingSelectedException($message, $level);
            throw $warning->setField('rules/levels');
        }
        $db = \Yana\Security\Data\SessionManager::getDatasource();
        $currentUser = $this->_getSession()->getCurrentUserName();

        $where = array('USER_CREATED', '=', $currentUser);
        if (!empty($user)) {
            if ($user === $currentUser) {
                throw new \Yana\Core\Exceptions\User\DeleteSelfException();
            }
            $where = array($where, 'and', array('USER_ID', '=', $user));
        }
        foreach ($rules as $key)
        {
            try {
                $db->remove('securityrules.' . $key, $where);
            } catch (\Exception $ex) {
                return false;
            }
        }
        unset($key);

        foreach ($levels as $key)
        {
            try {
                $db->remove('securitylevel.' . $key, $where);
            } catch (\Exception $ex) {
                return false;
            }
        }
        unset($key);

        try {
            $db->commit(); // may throw exception
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get security levels.
     *
     * @param   \Yana\Security\Data\SecurityLevels\Collection  $levels     collection
     * @param   array                                          &$profiles  profiles
     * @param   array                                          &$users     users
     * @return  array
     */
    private static function _getLevels(\Yana\Security\Data\SecurityLevels\Collection $levels, array &$profiles, &$users = false)
    {
        $userLevels = array();
        $defaultProfile = $this->_getApplication()->getDefault('profile');
        foreach ($levels as $profile => $item)
        {
            /* @var $item \Yana\Security\Data\SecurityLevels\IsLevel */
            if ($profile > "") {
                $profile = mb_strtoupper($profile);
            } else {
                $profile = $defaultProfile;
            }
            if ($users !== false) {
                $userName = $item['USER_ID'];
            }
            $entry = array(
                "SECURITY_ID" => $item->getId(),
                "SECURITY_LEVEL" => $item->getSecurityLevel()
            );
            if (isset($userName) && !isset($userLevels[$profile][$userName])) {
                if (!in_array($userName, $users)) {
                    $users[] = $userName;
                }
                if (!in_array($profile, $profiles)) {
                    $profiles[] = $profile;
                }
                $userLevels[$profile][$userName] = $entry;

            } elseif (!isset($userLevels[$profile])) {
                $profiles[] = $profile;
                $userLevels[$profile] = $entry;
            }
        }
        return $userLevels;
    }

    /**
     * get security rules
     *
     * @access  private
     * @static
     * @param   \Yana\Security\Data\SecurityRules\Collection  $rows  rows
     * @param   array  &$profiles  profiles
     * @param   array  &$users     users
     * @return  array
     * @ignore
     */
    private static function _getRules(\Yana\Security\Data\SecurityRules\Collection $rows, array &$profiles, &$users = false)
    {
        $userRules = array();
        $defaultProfile = $this->_getApplication()->getDefault('profile');
        foreach ($rows as $key => $item)
        {
            if (!empty($item['PROFILE'])) {
                $profile = mb_strtoupper($item['PROFILE']);
            } else {
                $profile = $defaultProfile;
            }
            if (!in_array($profile, $profiles)) {
                $profiles[] = $profile;
            }
            if ($users !== false) {
                $userName = $item['USER_ID'];
                if (!in_array($userName, $users)) {
                    $users[] = $userName;
                }
            }
            if (isset($item['GROUP_ID'])) {
                $groupId = $item['GROUP_ID'];
            } else {
                $groupId = '';
            }
            if (isset($item['ROLE_ID'])) {
                $roleId = $item['ROLE_ID'];
            } else {
                $roleId = '';
            }
            if (isset($userName)) {
                $userRules[$profile][$userName][$key]['GROUP_ID'] = $groupId;
                $userRules[$profile][$userName][$key]['ROLE_ID'] = $roleId;
            } else {
                $userRules[$profile][$key]['GROUP_ID'] = $groupId;
                $userRules[$profile][$key]['ROLE_ID'] = $roleId;
            }
        }
        return $userRules;
    }

}

?>