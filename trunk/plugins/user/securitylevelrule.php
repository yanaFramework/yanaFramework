<?php
/**
 * @author     Thomas Meyer
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\User;

/**
 * Security rule
 *
 * @package    yana
 * @subpackage plugins
 */
class SecurityLevelRule extends \Yana\Security\Rules\AbstractRule
{

    /**
     * Check security level.
     *
     * @param   \Yana\Security\Rules\Requirements\IsRequirement  $required   list of required permissions
     * @param   string                                           $profileId  current application-profile id
     * @param   string                                           $action     name of the action the user tries to execute
     * @param   \Yana\Security\Users\IsUser                      $user       user information to check
     * @return  bool
     */
    public function __invoke(\Yana\Security\Rules\Requirements\IsRequirement $required, $profileId, $action, \Yana\Security\Users\IsUser $user)
    {
        if ($required->getLevel() < 0) {
            return null;
        }
        if ($required->getLevel() === 0) {
            return true;
        }

        if (!\Yana\User::isLoggedIn()) {
            return false;
        }

        $securityLevel = (int) \Yana\Security\Users\SessionManager::getInstance()->getSecurityLevel($userName, $profileId);

        return $required->getLevel() <= $securityLevel;
    }

}

?>