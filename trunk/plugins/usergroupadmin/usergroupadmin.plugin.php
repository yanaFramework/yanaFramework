<?php
/**
 * Groups- and Roles-Management
 *
 * This plugin adds a setup interface to create, edit and remove groups and roles.
 *
 * {@translation
 *
 *   de:   Gruppen- und Rollenverwaltung
 *
 *         Dieses Plugin fügt eine Setup-Schnittstelle zum Erstellen, Bearbeiten und
 *         Entfernen für Gruppen und Rollen hinzu.
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @extends    usergroup
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\UserGroupAdmin;

/**
 * user group setup
 *
 * @package    yana
 * @subpackage plugins
 */
class UserGroupAdminPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * get form definition
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getActionForm()
    {
        $builder = $this->_getApplication()->buildForm('user_admin');
        return $builder->setId('securityactionrules')->__invoke();
    }

    /**
     * get form definition
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getGroupForm()
    {
        $builder = $this->_getApplication()->buildForm('user_admin');
        return $builder->setId('securitygroup')->__invoke();
    }

    /**
     * get form definition
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getRoleForm()
    {
        $builder = $this->_getApplication()->buildForm('user_admin');
        return $builder->setId('securityrole')->__invoke();
    }

    /**
     * user groups panel
     *
     * @type        config
     * @template    USER_GROUP_TEMPLATE
     * @user        group: admin, level: 100
     * @menu        group: setup
     * @title       {lang id="USER.OPTION.26"}
     */
    public function get_usergroups()
    {
        // Just views template - no business logic required.
    }

    /**
     * user actions panel
     *
     * @type        config
     * @template    USER_ACTION_SETTINGS_TEMPLATE
     * @user        group: admin, level: 100
     * @menu        group: setup
     * @title       {lang id="USER.OPTION.32"}
     */
    public function get_user_action_settings()
    {
        $this->_getApplication()->setVar('WHERE', array('actionrule_predefined', '=', false));
        // Just views template - no further business logic required.
    }

    /**
     * Edit user action settings
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USER_ACTION_SETTINGS
     * @onerror     goto: GET_USER_ACTION_SETTINGS
     * @return      bool
     */
    public function set_user_action_settings_edit()
    {
        $form = $this->_getActionForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        $worker->beforeUpdate(
            function (&$id, &$entry)
            {
                $id = mb_strtolower($id);
                if ($entry['level'] > 100) {
                    $entry['level'] = 100;
                }
                if ($entry['level'] < 0) {
                    $entry['level'] = 0;
                }
            }
        );
        return $worker->update();
    }

    /**
     * Delete user action settings
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USER_ACTION_SETTINGS
     * @onerror     goto: GET_USER_ACTION_SETTINGS
     * @param       array  $selected_entries  array of params passed to the function
     * @return      bool
     */
    public function set_user_action_settings_delete(array $selected_entries)
    {
        $database = $this->_connectToDatabase('user_admin');

        try {
            /* remove entry from database */
            foreach ($selected_entries as $id)
            {
                $database->remove("securityactionrules.{$id}");
            }
            $database->commit(); // may throw exception
            return true;

        } catch (\Exception $e) {
            $database->rollback();
            return false;
        }
    }

    /**
     * Create user action settings
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USER_ACTION_SETTINGS
     * @onerror     goto: GET_USER_ACTION_SETTINGS
     * @return      bool
     */
    public function set_user_action_settings_new()
    {
        $form = $this->_getActionForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        $worker->beforeCreate(
            function (&$newEntry)
            {
                if ($newEntry['level'] > 100) {
                    $newEntry['level'] = 100;
                }
                if ($newEntry['level'] < 0) {
                    $newEntry['level'] = 0;
                }
            }
        );
        return $worker->create();
    }

    /**
     * Edit user groups
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USERGROUPS
     * @onerror     goto: GET_USERGROUPS
     * @return      bool
     */
    public function set_usergroup_edit()
    {
        $form = $this->_getGroupForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        $worker->beforeUpdate(
            function (&$id)
            {
                $id = mb_strtolower($id);
            }
        );
        return $worker->update();
    }

    /**
     * Delete user groups
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USERGROUPS
     * @onerror     goto: GET_USERGROUPS
     * @param       array  $selected_entries  array of params passed to the function
     * @return      bool
     */
    public function set_usergroup_delete(array $selected_entries)
    {
        $form = $this->_getGroupForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * Create user group
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USERGROUPS
     * @onerror     goto: GET_USERGROUPS
     * @return      bool
     */
    public function set_usergroup_new()
    {
        $form = $this->_getGroupForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->create();
    }

    /**
     * user roles panel
     *
     * @type        config
     * @template    USER_ROLE_TEMPLATE
     * @user        group: admin, level: 100
     * @menu        group: setup
     * @title       {lang id="USER.OPTION.25"}
     */
    public function get_userroles()
    {
        // Just views template - no business logic required.
    }

    /**
     * Edit user roles
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USERROLES
     * @onerror     goto: GET_USERROLES
     * @return      bool
     */
    public function set_userrole_edit()
    {
        $form = $this->_getRoleForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        $worker->beforeUpdate(
            function (&$id)
            {
                $id = mb_strtolower($id);
            }
        );
        return $worker->update();
    }

    /**
     * Delete user roles
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USERROLES
     * @onerror     goto: GET_USERROLES
     * @param       array  $selected_entries  array of params passed to the function
     * @return      bool
     */
    public function set_userrole_delete(array $selected_entries)
    {
        $form = $this->_getRoleForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * Create user roles
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USERROLES
     * @onerror     goto: GET_USERROLES
     * @return      bool
     */
    public function set_userrole_new()
    {
        $form = $this->_getRoleForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->create();
    }

}

?>