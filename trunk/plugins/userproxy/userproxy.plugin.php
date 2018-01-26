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
     * Get security levels.
     *
     * @param   \Yana\Security\Data\Behaviors\IsBehavior   $user            that is currently logged in
     * @param   string                                     $defaultProfile  must be valid id
     * @return  array
     */
    private function _getAllSecurityLevels(\Yana\Security\Data\Behaviors\IsBehavior $user, $defaultProfile)
    {
        assert('!isset($grantableLevels); // Cannot redeclare var $grantableLevels');
        $grantableLevels = array();
        assert('!isset($level); // Cannot redeclare var $level');
        foreach ($user->getAllSecurityLevels() as $level)
        {
            /* @var $level \Yana\Security\Data\SecurityLevels\IsLevelEntity */
            if (!$level->isUserProxyActive()) {
                continue; // If this level can't be granted to other users, we ignore it.
            }
            $profileId = \mb_strtolower($level->getProfile() > "" ? $level->getProfile() : $defaultProfile);
            $grantableLevels[$profileId] = array(
                "SECURITY_ID" => $level->getId(),
                "SECURITY_LEVEL" => $level->getSecurityLevel()
            );
            unset($profileId);
        }
        unset($level);

        return $grantableLevels;
    }

    /**
     * Get security rules.
     *
     * @param   \Yana\Security\Data\Behaviors\IsBehavior   $user            that is currently logged in
     * @param   string                                     $defaultProfile  must be valid id
     * @return  array
     */
    private function _getAllSecurityRules(\Yana\Security\Data\Behaviors\IsBehavior $user, $defaultProfile)
    {
        assert('!isset($rules); // Cannot redeclare var $rules');
        $rules = array();
        assert('!isset($rule); // Cannot redeclare var $rule');
        assert('!isset($profileId); // Cannot redeclare var $profileId');
        foreach ($user->getAllSecurityGroupsAndRoles() as $rule)
        {
            /* @var $rule \Yana\Security\Data\SecurityRules\IsRuleEntity */

            if (!$rule->isUserProxyActive()) {
                continue;
            }
            $profileId = \mb_strtolower($rule->getProfile() > "" ? $rule->getProfile() : $defaultProfile);
            if (!isset($rules[$profileId])) {
                $rules[$profileId] = array();
            }
            $rules[$profileId][$rule->getId()] = array(
                "GROUP_ID" => $rule->getGroup(),
                "ROLE_ID" => $rule->getRole()
            );
        }
        unset($rule, $profileId);

        return $rules;
    }

    /**
     * Create edit form.
     *
     * This presents a form that shows all security levels, groups,
     * and roles this user can grant to others users.
     * As well as all security levels, groups, and roles this user has already
     * granted to others.
     *
     * This action expects no arguments.
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    USER_PROXY
     * @menu        group: setup
     * @title       {lang id="user.32"}
     *
     * @todo  As of yet, this code has been migrated but not tested. Tests required.
     */
    public function get_user_proxy()
    {
        $YANA = $this->_getApplication();

        // check user expert setting
        $YANA->setVar('USER_IS_EXPERT', (bool) $this->_getSecurityFacade()->loadUser()->isExpert());

        $currentUser = \Yana\Util\Strings::toUpperCase($this->_getSession()->getCurrentUserName());

        /**
         * get all usernames
         */
        $users = \Yana\Util\Hashtable::changeCase($this->_getSecurityFacade()->loadListOfUsers(), \CASE_UPPER);
        if (isset($users[$currentUser])) {
            unset($users[$currentUser]);
        }
        $YANA->setVar("USERLIST", $users);
        unset($users);

        /**
         * Will be using the default profile id as fallback if none was provided.
         */
        assert('!isset($defaultProfile); // Cannot redeclare var $defaultProfile');
        $defaultProfile = $YANA->getDefault('profile');

        /**
         * get security levels
         */
        $user = $this->_getSecurityFacade()->loadUser($currentUser);
        $YANA->setVar("LEVELS", $this->_getAllSecurityLevels($user, $defaultProfile));

        /**
         * get security rules
         */
        assert('!isset($rules); // Cannot redeclare var $rules');
        $rules = $this->_getAllSecurityRules($user, $defaultProfile);
        $YANA->setVar("RULES", $rules);
        $YANA->setVar("PROFILES", array_keys($rules)); // set profiles
        unset($rules);

        // collect users who are granted security privileges
        $users = array();
        // collect profiles that users are granted access to
        assert('!isset($profiles); // Cannot redeclare var $profiles');
        $profiles = array();

        /**
         * get security levels
         */
        assert('!isset($grantedLevels); // Cannot redeclare var $grantedLevels');
        $grantedLevels = array();
        assert('!isset($level); // Cannot redeclare var $level');
        foreach ($user->getAllSecurityLevelsGrantedToOthers() as $level)
        {
            /* @var $level \Yana\Security\Data\SecurityLevels\IsLevelEntity */
            $profileId = $level->getProfile() > "" ? $level->getProfile() : $defaultProfile;
            if (!isset($grantedLevels[$profileId])) {
                $grantedLevels[$profileId] = array();
            }
            $grantedLevels[$profileId][$level->getUserName()] = array(
                "SECURITY_ID" => $level->getId(),
                "SECURITY_LEVEL" => $level->getSecurityLevel()
            );
            $users[] = $level->getUserName();
            $profiles[] = $profileId;
        }
        unset($level);
        $YANA->setVar("GRANTED_LEVELS", $grantedLevels);
        unset($grantedLevels, $profileId);

        /**
         * get groups
         */
        assert('!isset($grantedRules); // Cannot redeclare var $grantedRules');
        $grantedRules = array();
        assert('!isset($rule); // Cannot redeclare var $rule');
        assert('!isset($profileId); // Cannot redeclare var $profileId');
        foreach ($user->getAllSecurityGroupsAndRolesGrantedToOthers() as $rule)
        {
            /* @var $rule \Yana\Security\Data\SecurityRules\IsRuleEntity */
            $profileId = $rule->getProfile() > "" ? $rule->getProfile() : $defaultProfile;
            if (!isset($grantedRules[$profileId])) {
                $grantedRules[$profileId] = array();
            }
            if (!isset($grantedRules[$profileId][$rule->getUserName()])) {
                $grantedRules[$profileId][$rule->getUserName()] = array();
            }
            $grantedRules[$profileId][$rule->getUserName()][$rule->getId()] = array(
                "GROUP_ID" => $rule->getGroup(),
                "ROLE_ID" => $rule->getRole()
            );
            $profiles[] = $profileId;
            $users[] = $rule->getUserName();
        }
        unset($rule, $profileId);
        $YANA->setVar("GRANTED_RULES", $grantedRules);
        unset($grantedRules, $profileId);

        // store list of users with grants
        $YANA->setVar("GRANTED_USERS", \array_unique($users));

        // store list of profiles with grants
        $YANA->setVar("GRANTED_PROFILES", \array_unique($profiles));

        return true;
    }

    /**
     * Set new user proxy.
     *
     * This action takes a list of selected security rules and levels as IDs
     * and grants them to the given user.
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
     *
     * @todo  As of yet, this code has been migrated but not tested. Tests required.
     */
    public function set_user_proxy($user, array $rules = array(), array $levels = array())
    {
        if (empty($rules) && empty($levels)) {
            $message = "Nothing to do: no entry selected to operate on.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $warning = new \Yana\Core\Exceptions\Forms\NothingSelectedException($message, $level);
            throw $warning->setField('rules/levels');
        }
        if (!$this->_getSecurityFacade()->isExistingUserName($user)) {
            $message = "No such user: " . $user;
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $warning = new \Yana\Core\Exceptions\Forms\InvalidValueException($message, $level);
            throw $warning->setField('user');
        }

        $currentUser = $this->_getSecurityFacade()->loadUser();
        if (!empty($levels)) {
            foreach ($currentUser->getAllSecurityLevels() as $level)
            {
                /* @var $level \Yana\Security\Data\SecurityLevels\IsLevelEntity */
                if (!$level->isUserProxyActive()) {
                    continue; // Cannot be granted to other users: skip
                }
                if (!\in_array($level->getId(), $levels)) {
                    continue; // Not in the list of permissions to grant: skip
                }
                try {
                    $level->grantTo($user); // may throw exception

                } catch (\Yana\Core\Exceptions\User\LevelAlreadyExistsException $e) {
                    // If we don't actually need the level since the user already has it,
                    // we may savely skip it.
                    continue;

                } catch (\Exception $e) {
                    return false;
                }
            }
            unset($level);
        }
        if (!empty($rules)) {
            foreach ($currentUser->getAllSecurityGroupsAndRoles() as $rule)
            {
                /* @var $rule \Yana\Security\Data\SecurityRules\IsRuleEntity */
                if (!$rule->isUserProxyActive()) {
                    continue; // Cannot be granted to other users: skip
                }
                if (!\in_array($rule->getId(), $rules)) {
                    continue; // Not in the list of permissions to grant: skip
                }
                try {
                    $rule->grantTo($user); // may throw exception

                } catch (\Yana\Core\Exceptions\User\RuleAlreadyExistsException $e) {
                    // If we don't actually need the rule since the user already has it,
                    // we may savely skip it.
                    continue;

                } catch (\Exception $e) {
                    return false;
                }
            }
            unset($rule);
        }
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
     * Constraint: for all removed entries, the user who created the entry must be
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
     *
     * @todo  As of yet, this code has been migrated but not tested. Tests required.
     */
    public function remove_user_proxy($user = "", array $rules = array(), array $levels = array())
    {
        if (empty($rules) && empty($levels)) {
            $message = "Nothing to do: no entry selected to operate on.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $warning = new \Yana\Core\Exceptions\Forms\NothingSelectedException($message, $level);
            throw $warning->setField('rules/levels');
        }
        if ($user > "" && !$this->_getSecurityFacade()->isExistingUserName($user)) {
            $message = "No such user: " . $user;
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $warning = new \Yana\Core\Exceptions\Forms\InvalidValueException($message, $level);
            throw $warning->setField('user');
        }
        $currentUser = $this->_getSecurityFacade()->loadUser();

        if (!empty($user)) {
            if (\strcasecmp($user, $currentUser->getId()) === 0) {
                throw new \Yana\Core\Exceptions\User\DeleteSelfException();
            }
        }
        if (!empty($rules)) {
            foreach ($currentUser->getAllSecurityGroupsAndRolesGrantedToOthers() as $rule)
            {
                /* @var $rule \Yana\Security\Data\SecurityRules\IsRuleEntity */
                if (!empty($user) && \strcasecmp($user, $rule->getUserName()) !== 0) {
                    continue;
                }
                if (!\in_array($rule->getId(), $rules)) {
                    continue;
                }
                try {
                    $currentUser->revokePreviouslyGrantedSecurityGroupOrRole($rule);
                } catch (\Exception $ex) {
                    return false;
                }
            }
            unset($rule);
        }
        if (!empty($levels)) {
            foreach ($currentUser->getAllSecurityLevelsGrantedToOthers() as $level)
            {
                /* @var $level \Yana\Security\Data\SecurityLevels\IsLevelEntity */
                if (!empty($user) && \strcasecmp($user, $level->getUserName()) !== 0) {
                    continue;
                }
                if (!\in_array($level->getId(), $levels)) {
                    continue;
                }
                try {
                    $currentUser->revokePreviouslyGrantedSecurityLevel($level);
                } catch (\Exception $ex) {
                    return false;
                }
            }
            unset($level);
        }

        return true;
    }

}

?>