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

/**
 * user management plugin
 *
 * This creates forms and implements functions to
 * manage user data.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_user_proxy extends StdClass implements IsPlugin
{

    /**
     * is user expert mode
     *
     * @access  private
     * @var     bool
     */
    private $isExpert = null;

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
        global $YANA;

        // check user expert setting
        $YANA->setVar('USER_IS_EXPERT', $this->_getIsExpert());

        $currentUser = YanaUser::getUserName();
        /**
         * @var DBStream $db
         */
        $db = SessionManager::getDatasource();

        /**
         * get all Users Names
         */
        $where = array('USER_ID', '!=', $currentUser);
        $users = $db->select('user.*.user_id', $where);
        $YANA->setVar("USERLIST", $users);
        unset($where, $users);

        $profiles = array();

        /**
         * get security levels
         */
        $where = array(
            array('USER_ID', '=', $currentUser),
            'and',
            array('USER_PROXY_ACTIVE', '=', true)
        );
        $rows = $db->select('securitylevel', $where, array("profile", "security_level"), 0, 0, true);
        $YANA->setVar("LEVELS", self::_getLevels($rows, $profiles));
        unset($rows);
        // WHERE clause will be reused

        /**
         * get groups
         */
        $rows = $db->select('securityrules', $where);
        $YANA->setVar("RULES", $this->_getRules($rows, $profiles));
        unset($where, $rows);

        /**
         * set profiles
         */
        $YANA->setVar("PROFILES", $profiles);
        unset($profiles);

        // collect users who are granted security privileges
        $users = array();
        // collect profiles
        $profiles = array();

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

        $db = SessionManager::getDatasource();
        $defaultProfile = Yana::getDefault('profile');
        $currentUser = YanaUser::getUserName();

        foreach ($rules as $i => $ruleId)
        {
            if (is_numeric($ruleId)) {
                $where = array(
                    array("USER_ID", '=', YanaUser::getUserName()),
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
                    if (!$db->insert("securityrules", $rule)) {
                        return false;
                    }
                }
            } else {
                unset($rules[$i]);
            }
        }

        $session = SessionManager::getInstance();
        foreach ($levels as $i => $profileId)
        {

            if (empty($profileId)) {
                $profileId = $defaultProfile;
            }

            $level = $session->getSecurityLevel($currentUser, $profileId);
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
                    if (!$db->insert("securitylevel", $row)) {
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
        $db = SessionManager::getDatasource();
        $currentUser = YanaUser::getUserName();

        $where = array('USER_CREATED', '=', $currentUser);
        if (!empty($user)) {
            if ($user === $currentUser) {
                throw new \Yana\Core\Exceptions\User\DeleteSelfException();
            }
            $where = array($where, 'and', array('USER_ID', '=', $user));
        }
        foreach ($rules as $key)
        {
            if (!$db->remove('securityrules.' . $key, $where)) {
                return false;
            }
        }
        unset($key);

        foreach ($levels as $key)
        {
            if (!$db->remove('securitylevel.' . $key, $where)) {
                return false;
            }
        }
        unset($key);

        $db->commit(); // may throw exception
        return true;
    }

    /**
     * get user expert mode
     *
     * @access  private
     * @return  bool
     * @ignore
     */
    private function _getIsExpert()
    {
        if (!isset($this->isExpert)) {
            $currentUser = YanaUser::getUserName();
            if (empty($currentUser)) {
                return false;
            }
            // get database connection
            $database = SessionManager::getDatasource();
            // get current user-mode
            if ($database->select("user." . $currentUser . ".user_is_expert")) {
                $this->isExpert = true;
            } else {
                $this->isExpert = false;
            }
        }
        return $this->isExpert;
    }

    /**
     * get security levels
     *
     * @access  private
     * @static
     * @param   array  $rows       rows
     * @param   array  &$profiles  profiles
     * @param   array  &$users     users
     * @return  array
     * @ignore
     */
    private static function _getLevels(array $rows, array &$profiles, &$users = false)
    {
        $userLevels = array();
        $defaultProfile = Yana::getDefault('profile');
        foreach ($rows as $item)
        {
            if (!empty($item['PROFILE'])) {
                $profile = mb_strtoupper($item['PROFILE']);
            } else {
                $profile = $defaultProfile;
            }
            if ($users !== false) {
                $userName = $item['USER_ID'];
            }
            $entry = array(
                "SECURITY_ID" => $item["SECURITY_ID"],
                "SECURITY_LEVEL" => $item["SECURITY_LEVEL"]
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
     * @param   array  $rows       rows
     * @param   array  &$profiles  profiles
     * @param   array  &$users     users
     * @return  array
     * @ignore
     */
    private static function _getRules(array $rows, array &$profiles, &$users = false)
    {
        $userRules = array();
        $defaultProfile = Yana::getDefault('profile');
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