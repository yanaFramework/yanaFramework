<?php
/**
 * User Management
 *
 * Allows creation of new users, editing the properties of existing users and setting passwords.
 * When active, the menu entry "user management" is available.
 *
 * {@translation
 *
 *   de:   Nutzerverwaltung
 *
 *         Erlaubt es neue Nutzer anzulegen, die Einstellungen existierender Nutzer zu editieren und
 *         Passwörter zu vergeben.
 *         Wenn es aktiviert ist, wird dem Hauptmenü der Eintrag "Nutzerverwaltung" hinzugefügt.
 *
 *   , fr: Administration des Usagers
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

namespace Plugins\UserAdmin;

/**
 * User management plugin.
 *
 * This creates forms and implements functions to manage user data.
 *
 * @package    yana
 * @subpackage plugins
 */
class UserAdminPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * get form definition
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getAccessForm()
    {
        return $this->_getUserForm()->getForm('securityrules');
    }

    /**
     * get form definition
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getLevelForm()
    {
        return $this->_getUserForm()->getForm('securitylevel');
    }

    /**
     * get form definition
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getUserForm()
    {
        $builder = $this->_getApplication()->buildForm('user_admin');
        return $builder->setId('user')->__invoke();
    }

    /**
     * @var  array
     */
    private $visibleColumns = array('user_id', 'user_mail', 'user_active', 'user_inserted', 'user_login_last');

    /**
     * event handler
     *
     * @type        config
     * @template    MESSAGE
     * @user        group: admin, level: 100
     * @onsuccess   goto: GET_USER_LIST
     * @onerror     goto: GET_USER_LIST
     * @language    user
     *
     * @access      public
     * @return      bool
     * @param       array  $target  array of params passed to the function
     */
    public function set_user_pwd(array $target)
    {
        if (!isset($target['user_id'])) {
            return false;
        }

        try {
            $user = $this->_getSecurityFacade()->loadUser((string) $target['user_id']);
            return $this->_generateRandomPassword($user);
        } catch (\Yana\Core\Exceptions\User\NotFoundException $e) { // user not found
            return false;
        }
    }

    /**
     * Generate a random password, sumbit changes, and send an e-mail.
     *
     * @param   \Yana\Security\Data\Behaviors\IsBehavior  $user  entity
     * @return  bool
     */
    private function _generateRandomPassword(\Yana\Security\Data\Behaviors\IsBehavior $user)
    {
        assert('!isset($password); // $password already declared');
        $password = $user->generateRandomPassword();

        if (!$password) {
            return false;
        }

        assert('!isset($YANA); // $YANA already declared');
        $YANA = $this->_getApplication();
        $YANA->setVar('PASSWORT', $password);
        $YANA->setVar('NAME', $user->getId());
        if (filter_var($user->getMail(), FILTER_VALIDATE_EMAIL)) {
            assert('!isset($sender); // Cannot redeclare var $sender');
            $sender = (string) $YANA->getVar("PROFILE.MAIL");
            if (filter_var($sender, FILTER_VALIDATE_EMAIL)) {
                $template = $YANA->getView()->createContentTemplate("id:USER_PASSWORD_MAIL");
                $templateMailer = new \Yana\Mails\TemplateMailer($template);
                $recipient = $user->getMail();
                $subject = $YANA->getLanguage()->getVar("user.mail_subject");
                $vars = array('DATE' => date('d-m-Y'));
                $headers = array('from' => $sender);
                $templateMailer->send($recipient, $subject, $vars, $headers);
            }
            unset($sender);
        }
        return true;
    }

    /**
     * user configuration panel
     *
     * @type        config
     * @template    USER_CONFIGURATION_TEMPLATE
     * @menu        group: setup
     * @title       {lang id="user.2"}
     * @user        group: admin, level: 100
     *
     * @access      public
     * @return      bool
     */
    public function get_user_list()
    {
        $YANA = $this->_getApplication();
        $YANA->setVar("DESCRIPTION", $YANA->getLanguage()->getVar("DESCR_USER_CONFIGURATION"));
        $YANA->setVar("VISIBLE_COLUMNS", $this->visibleColumns);
        return true;
    }

    /**
     * Delete user
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USER_LIST
     * @onerror     goto: GET_USER_LIST
     *
     * @access      public
     * @return      bool
     */
    public function set_user_edit()
    {
        $form = $this->_getUserForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        $visibleColumns = $this->visibleColumns;
        $securityFacade = $this->_getSecurityFacade();
        $worker->beforeUpdate(
            function ($id, $entry) use ($visibleColumns, $securityFacade)
            {
                $id = mb_strtolower($id);

                foreach (array_keys($entry) as $i)
                {
                    if (!in_array($i, $visibleColumns)) {
                        unset($entry[$i]);
                    }
                }

                // before doing anything, check if entry exists
                if (!$securityFacade->isExistingUserName($id)) {
                    $message = "No user found with id: " . \htmlentities($id);
                    $level = \Yana\Log\TypeEnumeration::ERROR;
                    throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
                }

                if ($id === "administrator" && !$entry['user_active']) {
                    $message = "The administrator's account must not be deactivated";
                    $level = \Yana\Log\TypeEnumeration::ERROR;
                    throw new \Yana\Core\Exceptions\User\DeleteAdminException($message, $level);
                }
            }
        );
        return $worker->update();
    }

    /**
     * Delete user
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USER_LIST
     * @onerror     goto: GET_USER_LIST
     *
     * @access      public
     * @return      bool
     * @param       array  $selected_entries  array of params passed to the function
     */
    public function set_user_delete(array $selected_entries)
    {
        /* remove entry from database */
        foreach ($selected_entries as $id)
        {
            // try to remove user
            $this->_getSecurityFacade()->removeUser($id); // may throw exception
        }
        return true;
    }

    /**
     * Insert a new user
     *
     * handles input from action "get_user_new"
     * this action expects no arguments
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: GET_USER_LIST
     * @onerror     goto: GET_USER_LIST
     *
     * @access      public
     * @return      bool
     */
    public function set_user_new()
    {
        $YANA = $this->_getApplication();
        // reset Id-setting (just in case some plugin changed this)
        $YANA->setVar('ID', $YANA->getProfileId());

        assert('!isset($user); // $user already declared');
        $user = $this->_getSecurityFacade()->createUserByFormData($this->_getUserForm()->getInsertValues());
        try {
            $user->saveChanges();
            return $this->_generateRandomPassword($user);

        } catch (\Exception $e) {

            return false;
        }
    }

    /**
     * Edit access rights
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: get_user_list
     * @onerror     goto: get_user_list
     *
     * @access      public
     * @return      bool
     */
    public function set_access_edit()
    {
        $form = $this->_getAccessForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->update();
    }

    /**
     * Revoke access rights.
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: get_user_list
     * @onerror     goto: get_user_list
     *
     * @access      public
     * @return      bool
     * @param       array  $selected_entries  array of params passed to the function
     */
    public function set_access_delete(array $selected_entries)
    {
        $form = $this->_getAccessForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * Grant access rights.
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: get_user_list
     * @onerror     goto: get_user_list
     *
     * @access      public
     * @return      bool
     */
    public function set_access_new()
    {
        $form = $this->_getAccessForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->create();
    }

    /**
     * Edit access rights
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: get_user_list
     * @onerror     goto: get_user_list
     *
     * @access      public
     * @return      bool
     */
    public function set_securitylevel_edit()
    {
        $form = $this->_getLevelForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->update();
    }

    /**
     * Revoke access rights.
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: get_user_list
     * @onerror     goto: get_user_list
     *
     * @access      public
     * @return      bool
     * @param       array  $selected_entries  array of params passed to the function
     */
    public function set_securitylevel_delete(array $selected_entries)
    {
        $form = $this->_getLevelForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * Grant access rights.
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: get_user_list
     * @onerror     goto: get_user_list
     *
     * @access      public
     * @return      bool
     */
    public function set_securitylevel_new()
    {
        $form = $this->_getLevelForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
        return $worker->create();
    }

}

?>