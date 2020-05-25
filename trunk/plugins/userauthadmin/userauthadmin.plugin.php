<?php
/**
 * User Authentication Management.
 *
 * Allows creation and configuration of additional authentication methods (like LDAP).
 *
 * {@translation
 *
 *   de:   Authentifizierungsmethoden
 *
 *         Erlaubt Anlage und Konfiguration von weiteren Authentifizierungsmethoden (wie LDAP).
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

namespace Plugins\UserAuthAdmin;

/**
 * Authentication method management plugin.
 *
 * This creates forms and implements functions to manage authentication providers.
 *
 * @package    yana
 * @subpackage plugins
 */
class UserAuthAdminPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * get form definition
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getAuthForm()
    {
        $builder = $this->_getApplication()->buildForm('user_auth', 'authenticationprovider');
        return $builder->__invoke();
    }

    /**
     * configuration panel
     *
     * @type        config
     * @template    templates/index.html.tpl
     * @menu        group: setup
     * @title       {lang id="user.authentication.title"}
     * @user        group: admin, level: 100
     */
    public function get_auth_admin()
    {
        // intentionally left blank
    }

    /**
     * Edit authentication methods.
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: get_auth_admin
     * @onerror     goto: get_auth_admin
     *
     * @return      bool
     */
    public function set_auth_edit()
    {
        $form = $this->_getAuthForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->update();
    }

    /**
     * Delete authentication methods.
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: get_auth_admin
     * @onerror     goto: get_auth_admin
     *
     * @return      bool
     * @param       array  $selected_entries  array of params passed to the function
     */
    public function set_auth_delete(array $selected_entries)
    {
        $form = $this->_getAuthForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * Add authentication methods.
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: get_auth_admin
     * @onerror     goto: get_auth_admin
     *
     * @return      bool
     */
    public function set_auth_new()
    {
        $form = $this->_getAuthForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->create();
    }

}

?>