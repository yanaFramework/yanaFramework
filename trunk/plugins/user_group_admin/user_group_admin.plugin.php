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
 * @extends    user_group
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * user group setup
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_user_group_admin extends StdClass implements IsPlugin
{
    /**
     * Connection to data source (API)
     *
     * @access  private
     * @static
     * @var     DbStream
     */
    private static $database = null;

    /**
     * get database connection
     *
     * @access  protected
     * @static
     * @return  DbStream
     */
    protected static function getDatabase()
    {
        if (!isset(self::$database)) {
            self::$database = Yana::connect("user_admin");
        }
        return self::$database;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getActionForm()
    {
        $builder = new FormBuilder('user_admin');
        return $builder->setId('securityactionrules')->__invoke();
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getGroupForm()
    {
        $builder = new FormBuilder('user_admin');
        return $builder->setId('securitygroup')->__invoke();
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getRoleForm()
    {
        $builder = new FormBuilder('user_admin');
        return $builder->setId('securityrole')->__invoke();
    }

    /**
     * Default event handler.
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * user groups panel
     *
     * @type        config
     * @template    USER_GROUP_TEMPLATE
     * @user        group: admin, level: 100
     * @menu        group: setup
     * @title       {lang id="USER.OPTION.26"}
     *
     * @access      public
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
     *
     * @access      public
     */
    public function get_user_action_settings()
    {
        Yana::getInstance()->setVar('WHERE', array('actionrule_predefined', '=', false));
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
     *
     * @access      public
     * @return      bool
     */
    public function set_user_action_settings_edit()
    {
        $form = self::getActionForm();
        $worker = new FormWorker(self::getDatabase(), $form);
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
     *
     * @access      public
     * @param       array  $selected_entries  array of params passed to the function
     * @return      bool
     */
    public function set_user_action_settings_delete(array $selected_entries)
    {
        $database = self::getDatabase();

        /* remove entry from database */
        foreach ($selected_entries as $id)
        {
            if (!$database->remove("securityactionrules.${id}")) {
                /* entry does not exist */
                throw new InvalidInputWarning();
            }
        } /* end for */
        /* commit changes */
        return $database->write();
    }

    /**
     * Create user action settings
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USER_ACTION_SETTINGS
     * @onerror     goto: GET_USER_ACTION_SETTINGS
     *
     * @access      public
     * @return      bool
     */
    public function set_user_action_settings_new()
    {
        $form = self::getActionForm();
        $worker = new FormWorker(self::getDatabase(), $form);
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
     *
     * @access      public
     * @return      bool
     */
    public function set_usergroup_edit()
    {
        $form = self::getGroupForm();
        $worker = new FormWorker(self::getDatabase(), $form);
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
     *
     * @access      public
     * @param       array  $selected_entries  array of params passed to the function
     * @return      bool
     */
    public function set_usergroup_delete(array $selected_entries)
    {
        $form = self::getGroupForm();
        $worker = new FormWorker(self::getDatabase(), $form);
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
     *
     * @access      public
     * @return      bool
     */
    public function set_usergroup_new()
    {
        $form = self::getGroupForm();
        $worker = new FormWorker(self::getDatabase(), $form);
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
     *
     * @access      public
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
     *
     * @access      public
     * @return      bool
     */
    public function set_userrole_edit()
    {
        $form = self::getRoleForm();
        $worker = new FormWorker(self::getDatabase(), $form);
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
     *
     * @access      public
     * @param       array  $selected_entries  array of params passed to the function
     * @return      bool
     */
    public function set_userrole_delete(array $selected_entries)
    {
        $form = self::getRoleForm();
        $worker = new FormWorker(self::getDatabase(), $form);
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
     *
     * @access      public
     * @return      bool
     */
    public function set_userrole_new()
    {
        $form = self::getRoleForm();
        $worker = new FormWorker(self::getDatabase(), $form);
        return $worker->create();
    }

}

?>