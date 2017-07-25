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

namespace Plugins\UserGroup;

/**
 * user authentification plugin
 *
 * @package    yana
 * @subpackage plugins
 */
class UserGroupPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Registers user-group security rule.
     *
     * @access  public
     */
    public function __construct()
    {
        $security = $this->_getSecurityFacade();
        $defaultProfileId = $this->_getApplication()->getDefault('profile');
        $security->addSecurityRule(new \Yana\Security\Rules\SecurityGroupRule($defaultProfileId));
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

}

?>