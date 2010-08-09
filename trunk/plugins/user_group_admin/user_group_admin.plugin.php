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
     * Form definition
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static $actionForm = null;

    /**
     * Form definition
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static $groupForm = null;

    /**
     * Form definition
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static $roleForm = null;

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
     * @return  DDLDefaultForm
     */
    protected static function getActionForm()
    {
        if (!isset(self::$actionForm)) {
            $database = self::getDatabase();
            self::$actionForm = $database->schema->getForm("securityactionrules");
        }
        return self::$actionForm;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  DDLDefaultForm
     */
    protected static function getGroupForm()
    {
        if (!isset(self::$groupForm)) {
            $database = self::getDatabase();
            self::$groupForm = $database->schema->getForm("securitygroup");
        }
        return self::$groupForm;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  DDLDefaultForm
     */
    protected static function getRoleForm()
    {
        if (!isset(self::$roleForm)) {
            $database = self::getDatabase();
            self::$roleForm = $database->schema->getForm("securityrole");
        }
        return self::$roleForm;
    }

    /**
     * Default event handler
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @ignore
     */
    public function _default($event, array $ARGS)
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
     * @return      bool
     */
    public function get_usergroups()
    {
        return true;
    }

    /**
     * user groups panel
     *
     * @type        config
     * @template    USER_ACTION_SETTINGS_TEMPLATE
     * @user        group: admin, level: 100
     * @menu        group: setup
     * @title       {lang id="USER.OPTION.32"}
     *
     * @access      public
     * @return      bool
     */
    public function get_user_action_settings()
    {
        global $YANA;
        $YANA->setVar('WHERE', array('actionrule_predefined', '=', false));
        return true;
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
    public function set_user_action_settings_edit ()
    {
        $updatedEntries = self::getActionForm()->getUpdateValues();
       
        /* no data has been provided */
        if (empty($updatedEntries)) {
            throw new InvalidInputWarning();
        }

        $database = $this->getDatabase();
        foreach ($updatedEntries as $id => $entry)
        {
            $id = mb_strtolower($id);
            if ($entry['level'] > 100) {
                $entry['level'] = 100;
            }
            if ($entry['level'] < 0) {
                $entry['level'] = 0;
            }
            /* before doing anything, check if entry exists */
            if (!$database->exists("securityactionrules.${id}")) {

                /* error - no such entry */
                throw new InvalidInputWarning();

            /* update the row */
            } else if (!$database->update("securityactionrules.${id}", $entry)) {
                /* error - unable to perform update - possibly readonly */
                return false;
            }
        } /* end for */
        /* commit changes */
        return $database->write();
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
     * @return      bool
     * @param       array  $selected_entries  array of params passed to the function
     */
    public function set_user_action_settings_delete (array $selected_entries)
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
    public function set_user_action_settings_new()
    {
        $newEntry = self::getActionForm()->getInsertValues();

        /* no data has been provided */
        if (empty($newEntry)) {
            throw new InvalidInputWarning();
        }

        if ($newEntry['level'] > 100) {
            $newEntry['level'] = 100;
        }
        if ($newEntry['level'] < 0) {
            $newEntry['level'] = 0;
        }
        $database = self::getDatabase();
        /* insert new entry into table */
        if (!$database->insert("securityactionrules.*", $newEntry)) {
            throw new InvalidInputWarning();
        } else {
            return $database->write();
        }
    }


    /**
     * Edit user groups
     *
     * returns bool(true) on success and bool(false) on error
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
        $updatedEntries = self::getGroupForm()->getUpdateValues();

        /* no data has been provided */
        if (empty($updatedEntries)) {
            throw new InvalidInputWarning();
        }

        $database = self::getDatabase();
        foreach ($updatedEntries as $id => $entry)
        {
            $id = mb_strtolower($id);

            /* before doing anything, check if entry exists */
            if (!$database->exists("securitygroup.${id}")) {

                /* error - no such entry */
                throw new InvalidInputWarning();
            }
            /* update the row */
            if (!$database->update("securitygroup.${id}", $entry)) {
                /* error - unable to perform update - possibly readonly */
                return false;
            }
        } /* end for */
        /* commit changes */
        return $database->write();
    }

    /**
     * Delete user groups
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USERGROUPS
     * @onerror     goto: GET_USERGROUPS
     *
     * @access      public
     * @return      bool
     * @param       array  $selected_entries  array of params passed to the function
     */
    public function set_usergroup_delete (array $selected_entries)
    {
        $database = self::getDatabase();

        /* remove entry from database */
        foreach ($selected_entries as $id)
        {
            if (!$database->remove("securitygroup.${id}")) {
                /* entry does not exist */
                throw new InvalidInputWarning();
            }
        } /* end for */
        /* commit changes */
        return $database->write();
    }

    /**
     * Create user group
     *
     * returns bool(true) on success and bool(false) on error
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
        $newEntry = self::getGroupForm()->getInsertValues();

        /* no data has been provided */
        if (empty($newEntry)) {
            throw new InvalidInputWarning();
        }

        $database = self::getDatabase();
        /* insert new entry into table */
        if (!$database->insert("securitygroup.*", $newEntry)) {
            throw new InvalidInputWarning();
        }
        return $database->write();
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
     * @return      bool
     */
    public function get_userroles()
    {
        return true;
    }

    /**
     * Edit user roles
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
    public function set_userrole_edit()
    {
        $updatedEntries = self::getRoleForm()->getUpdateValues();

        /* no data has been provided */
        if (empty($updatedEntries)) {
            throw new InvalidInputWarning();
        }

        $database = self::getDatabase();
        foreach ($updatedEntries as $id => $entry)
        {
            $id = mb_strtolower($id);

            /* before doing anything, check if entry exists */
            if (!$database->exists("securityrole.${id}")) {

                /* error - no such entry */
                throw new InvalidInputWarning();
            }
            /* update the row */
            if (!$database->update("securityrole.${id}", $entry)) {
                /* error - unable to perform update - possibly readonly */
                return false;
            }
        } /* end for */
        /* commit changes */
        return $database->write();
    }

    /**
     * Delete user roles
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
     * @param       array  $selected_entries  array of params passed to the function
     */
    public function set_userrole_delete(array $selected_entries)
    {
        $database = self::getDatabase();

        /* remove entry from database */
        foreach ($selected_entries as $id)
        {
            if (!$database->remove("securityrole.${id}")) {
                /* entry does not exist */
                throw new InvalidInputWarning();
            }
        } /* end for */
        /* commit changes */
        return $database->write();
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
        $newEntry = self::getRoleForm()->getInsertValues();

        /* no data has been provided */
        if (empty($newEntry)) {
            throw new InvalidInputWarning();
        }

        $database = self::getDatabase();
        /* insert new entry into table */
        if (!$database->insert("securityrole.*", $newEntry)) {
            throw new InvalidInputWarning();
        }
        return $database->write();
    }
}

?>