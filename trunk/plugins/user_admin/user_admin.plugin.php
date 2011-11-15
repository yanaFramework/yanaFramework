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

/**
 * User management plugin.
 *
 * This creates forms and implements functions to manage user data.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_user_admin extends StdClass implements IsPlugin
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
    protected static function getAccessForm()
    {
        return self::getUserForm()->getForm('securityrules');
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getLevelForm()
    {
        return self::getUserForm()->getForm('securitylevel');
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getUserForm()
    {
        $builder = new \Yana\Forms\Builder('user_admin');
        return $builder->setId('user')->__invoke();
    }

    /**
     * @access  private
     * @var     array
     */
    private $visibleColumns = array('user_id', 'user_mail', 'user_active', 'user_inserted', 'user_login_last');

    /**
     * @access  private
     * @static
     * @var     string
     */
    private static $userName = "";

    /**
     * Constructor
     *
     * @access  public
     * @ignore
     */
    public function __construct()
    {
        if (isset($_SESSION['user_name'])) {
            self::$userName = $_SESSION['user_name'];
        }
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
     *
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

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

        global $YANA;

        $userName = (string) $target['user_id'];
        try {
            $user = YanaUser::getInstance($userName);
        } catch (\Yana\Core\Exceptions\NotFoundException $e) { // user not found
            return false;
        }
        $password = $user->setPassword();

        if (!$password) {
            return false;
        }
        $YANA->setVar('PASSWORT', $password);
        $YANA->setVar('NAME', $user->getName());
        if (filter_var($user->getMail(), FILTER_VALIDATE_EMAIL)) {
            assert('!isset($sender); // Cannot redeclare var $sender');
            $sender = $YANA->getVar("PROFILE.MAIL");
            if (filter_var($sender, FILTER_VALIDATE_EMAIL)) {
                $mail = new Mailer($YANA->getView()->createContentTemplate("id:USER_PASSWORD_MAIL"));
                $mail->setSender($sender);
                $mail->setVar('DATE', date('d-m-Y'));
                $mail->setSubject($YANA->getLanguage()->getVar("user.mail_subject"));
                $mail->send($user->getMail());
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
        global $YANA;
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
        $form = self::getUserForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        $visibleColumns = $this->visibleColumns;
        $worker->beforeUpdate(
            function ($id, $entry) use ($visibleColumns)
            {
                $id = mb_strtolower($id);

                foreach (array_keys($entry) as $i)
                {
                    if (!in_array($i, $visibleColumns)) {
                        unset($entry[$i]);
                    }
                }

                // before doing anything, check if entry exists 
                if (!YanaUser::isUser($id)) {
                    throw new UserNotFoundError();
                }

                /* should not deactivate administrator */
                if ($id === "administrator" && !$entry['user_active']) {
                    throw new UserDeleteAdminError();
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
            // Administrator account should not be deleted
            if (strtoupper($id) == "ADMINISTRATOR") {
                throw new UserDeleteAdminError();
            }

            // try to remove user
            YanaUser::removeUser($id);
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
        global $YANA;
        // reset Id-setting (just in case some plugin changed this)
        $YANA->setVar('ID', Yana::getId());

        $newUser = self::getUserForm()->getInsertValues();
        $userName = $newUser['user_id'];

        try {

            YanaUser::createUser($userName, $newUser['user_mail']);
            $db = SessionManager::getDatasource();
            if (!$db->update("user.$userName", $newUser) || !$db->commit()) {
                return false;
            }
            return $this->set_user_pwd(array('target' => array('user_id' => $userName)));

        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
            throw new InvalidInputWarning();
        } catch (\Yana\Core\Exceptions\AlreadyExistsException $e) {
            throw new UserAllreadyExistsWarning();
        } catch (\Exception $e) {
            throw new Error();
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
        $form = self::getAccessForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
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
        $form = self::getAccessForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
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
        $form = self::getAccessForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
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
        $form = self::getLevelForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
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
        $form = self::getLevelForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
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
        $form = self::getLevelForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->create();
    }

}

?>