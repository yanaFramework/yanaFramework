<?php
/**
 * Password Protection
 *
 * This plugin checks passwords and manages restrictions on the access of installed plugins.
 *
 * {@translation
 *
 *   de:   Passwortschutz
 *
 *         Dieses Plugin prüft Passwörter und regelt den Zugriff auf installierte Plugins.
 *
 *   , fr: Mot de Passe
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       security
 * @priority   high
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @active     always
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\User;

/**
 * user authentification plugin
 *
 * @package    yana
 * @subpackage plugins
 */
class UserPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * @access  private
     * @static
     * @var     string
     */
    private static $userName = "";
    /**
     * @access  private
     * @static
     * @var     string
     */
    private static $profileId = "";
    /**
     * @access  private
     * @static
     * @var     int
     */
    private static $securityLevel = 0;

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        global $YANA;
        if (isset($YANA)) {
            self::$userName = \Yana\User::getUserName();
            if (!empty(self::$userName)) {
                self::$securityLevel = $YANA->getSession()->getSecurityLevel(self::$userName);
                self::$profileId = \Yana\Application::getId();
                $YANA->setVar("SESSION_USER_ID", self::$userName);
                $YANA->setVar("PERMISSION", self::$securityLevel);
            }
            $YANA->setVar("SESSION_ID", session_id());
            $YANA->setVar("SESSION_NAME", session_name());
        }
        \Yana\Security\Users\SessionManager::addSecurityRule(array(__CLASS__, 'checkSecurityLevel'));
    }

    /**
     * Default event handler.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     */
    public function catchAll($event, array $ARGS)
    {
        /* @var $YANA \Yana\Application */
        $YANA = $this->_getApplication();
        // Load translation strings
        try {

            $YANA->getLanguage()->readFile("user"); // may throw exception

        } catch (\Yana\Core\Exceptions\Translations\TranslationException $e) {
            /* Throwing an exception here would prevent the whole application from running at all,
             * so we just report it and leave it for later.
             */
            $message = $e->getMessage();
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $data = $e->getData();
            \Yana\Log\LogManager::getLogger()->addLog($message, $level, $data);
            unset($e);
        }

        if ($this->_getSecurityFacade()->checkRules(null, $event)) {
            /**
             * Access granted.
             */
            $this->_addLoginMenuEntry();
            return true;
        } elseif (!$this->_createLoginManager()->isLoggedIn($this->_loadUser())) {
            /**
             * Access denied.
             *
             * Relocates to the login-page.
             */
            $_SESSION['on_login_goto'] = $event;
            $message = "A valid login is required to access this function.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            new \Yana\Core\Exceptions\Security\LoginRequiredException($message, $level);
            $YANA->exitTo("login");
        } else {
            /**
             * Access denied.
             *
             * Relocates to the start-page.
             */
            $message = "The login is valid, but the access rights are not enough to access the function.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            new \Yana\Core\Exceptions\Security\InsufficientRightsException($message, $level);
            $YANA->exitTo();
        }
    }
    /**
     * This adds a menu entry for login or logout.
     */
    private function _addLoginMenuEntry()
    {
        $YANA = $this->_getApplication();
        // Where the menu entry should go to
        $action = "login";
        if ($this->_createLoginManager()->isLoggedIn($this->_loadUser())) {
            $action = "logout";
        }
        // What the name of the entry should be
        $title = $YANA->getLanguage()->getVar($action);
        // Create entry for login or logout
        $menuEntry = new \Yana\Plugins\Menus\Entry();
        $menuEntry->setTitle($title);
        // Add entry to menu

        $builder = new \Yana\Plugins\Menus\Builder();
        $builder->buildMenu() // using default settings
            ->setMenuEntry($action, $menuEntry);
    }

    /**
     * check security level
     *
     * @param   \Yana\Db\IsConnection   $database    database
     * @param   array                   $required    required level
     * @param   string                  $profileId   profile id
     * @param   string                  $action      action
     * @param   string                  $userName    user name
     * @return  bool
     *
     * @ignore
     */
    public static function checkSecurityLevel(\Yana\Db\IsConnection $database, array $required, $profileId, $action, $userName)
    {
        if (!isset($required[\Yana\Plugins\Annotations\Enumeration::LEVEL])) {
            return null;
        }
        // skip if nothing to check
        if (empty($required[\Yana\Plugins\Annotations\Enumeration::LEVEL])) {
            return true;
        }

        $requiredLevel = (int) $required[\Yana\Plugins\Annotations\Enumeration::LEVEL];

        if (!\Yana\User::isLoggedIn()) {
            return false;
        }

        if ($userName == self::$userName && self::$profileId == $profileId) {
            return $requiredLevel <= self::$securityLevel;
        }

        $securityLevel = (int) \Yana\Security\Users\SessionManager::getInstance()->getSecurityLevel($userName, $profileId);

        return $requiredLevel <= $securityLevel;
    }

    /**
     * login screen
     *
     * @type        security
     * @template    login_template
     *
     * @return      bool
     */
    public function login()
    {
        return true;
    }

    /**
     * Send login data screen
     *
     * @type        security
     * @template    USER_LOST_PASSWORD
     *
     * @access      public
     * @return      bool
     */
    public function set_lost_pwd()
    {
        return true;
    }

    /**
     * Send login data screen
     *
     * @type        security
     * @template    message
     * @onsuccess   goto: login, text: Yana\Core\Exceptions\Messages\MailMessage
     * @onerror     goto: set_lost_pwd, text: Yana\Core\Exceptions\InvalidInputException
     *
     * @param       array   $ARGS   array of arguments passed to the function
     * @return      bool
     */
    public function get_lost_pwd(array $ARGS)
    {
        $YANA = $this->_getApplication();
        $database = \Yana\Security\Users\SessionManager::getDatasource();
        // check captcha field
        if (\Yana\Plugins\Manager::getInstance()->isActive('antispam') && $YANA->getVar("PROFILE.SPAM.CAPTCHA")) {
            if ($YANA->callAction("security_check_image", $ARGS) === false) {
                \Yana\Log\LogManager::getLogger()->addLog('SPAM: CAPTCHA not solved, entry has not been created.');
                return false;
            }
        }

        $userMail = mb_strtolower($ARGS['user']);
        // email address is not valid
        if (filter_var($userMail, FILTER_VALIDATE_EMAIL) === false) {
            $message = "User's e-mail address is invalid.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\User\MailInvalidException($message, $level);
        }

        // check if user exist in the database (select * from user where user_mail = ?)
        $user = $database->select('user', array('USER_MAIL', '=', $userMail));

        // e-mail is not found in the db
        if (count($user) !== 1) {
            $message = "No user found with mail " . $userMail;
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\User\MailNotFoundException($message, $level);
        }
        unset($userMail);

        /* Get user name and mail.
         *
         * Note that for security reasosns you should not trust the mail address the user
         * entered himself.
         */
        $user = array_pop($user);
        $userName = $user['USER_ID'];
        $recipient = $user['USER_MAIL'];
        unset($user);
        assert('is_string($userName); // $userName must be of type String');
        assert('is_string($recipient); // $recipient must be of type String');
        assert('filter_var($recipient, FILTER_VALIDATE_EMAIL); // $recipient not a valid e-mail');

        // calculate unique recovery-key
        $uniqueKey = uniqid(substr(md5($recipient), 0, 3));

        // update the user record with time() and uniqueID for verification
        assert('!isset($recovery) // Cannot redeclare var $recovery');
        $recovery = array("user_recover_id" => $uniqueKey, "user_recover_utc" => time());
        try {
            $database->update("user.{$userName}", $recovery)
                ->commit();
        } catch (\Exception $e) {
            return false; // unsuccessful user record update
        }
        unset($recovery);

        assert('isset($userName); // variable $userName is not set');

        $subject = $YANA->getLanguage()->getVar('user.6');
        $sender = $YANA->getVar('PROFILE.MAIL');
        assert('filter_var($sender, FILTER_VALIDATE_EMAIL); // $sender not a valid e-mail');

        // get the mail template
        $viewManager = $this->_getApplication()->getView();
        $template = $viewManager->createContentTemplate('id:USER_LOST_PWD');
        $website = 'http://' . $_SERVER['SERVER_ADDR'] . $_SERVER['PHP_SELF'] .
            '?action=set_reset_pwd&key=' . $uniqueKey;

        // set the mail values
        $templateMailer = new \Yana\Mails\TemplateMailer($template);
        $vars = array(
            'NAME' => $userName,
            'TIME' => date("m.d.y H:i:s"),
            'WEBSITE' => $website
        );
        $headers = array('from' => $sender);
        return (bool) $templateMailer->send($recipient, $subject, $vars, $headers);
    }

    /**
     * Send login data screen
     *
     * @type      security
     * @template  USER_RESET_PWD
     * @onerror   text: Yana\Core\Exceptions\Files\NotFoundException
     *
     * @param     string  $key  user id
     * @return    bool
     */
    public function set_reset_pwd($key)
    {
        $this->_getUserId($key);
        return true;
    }

    /**
     * Send login data screen
     *
     * @type        security
     * @template    message
     * @onsuccess   goto: login
     * @onerror     goto: login, text: Yana\Core\Exceptions\InvalidInputException
     *
     * @param       string  $key         user id
     * @param       string  $new_pwd     new password
     * @param       string  $repeat_pwd  duplicate of new password
     * @return      bool
     * @throws      \Yana\Core\Exceptions\Security\PasswordDoesNotMatchException  when the passwords don't match
     * @throws      \Yana\Core\Exceptions\Security\PasswordException              when the password was not saved
     */
    public function reset_pwd($key, $new_pwd, $repeat_pwd)
    {
        // check if user exist in the database
        $userName = $this->_getUserId($key);
        assert('is_string($userName);');

        $isSuccess = true;
        try {

            $user = $this->_loadUser($userName);
            $this->_setPwd($user, $new_pwd, $repeat_pwd); // may throw exception

        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            unset($e);
            $isSuccess = false;
        }
        return $isSuccess;
    }

    /**
     * Set new password
     *
     * @type        security
     * @template    message
     * @onsuccess   goto: index
     * @onerror     goto: index
     *
     * @param       string  $new_pwd     new password
     * @param       string  $repeat_pwd  duplicate of new password
     * @param       string  $old_pwd     old password
     * @return      bool
     * @throws      \Yana\Core\Exceptions\Security\InvalidLoginException          when name or password are invalid
     * @throws      \Yana\Core\Exceptions\Security\PasswordDoesNotMatchException  when the passwords don't match
     * @throws      \Yana\Core\Exceptions\Security\PasswordException              when the password was not saved
     */
    public function set_pwd($new_pwd, $repeat_pwd, $old_pwd = "")
    {
        $isSuccess = true;
        try {

            if (!\Yana\User::getInstance()->checkPassword($old_pwd)) {
                $message = "Invalid name or password.";
                $level = \Yana\Log\TypeEnumeration::ERROR;
                throw new \Yana\Core\Exceptions\Security\InvalidLoginException($message, $level);
                /**
                 * This case exits to the default page (which should be public),
                 * to avoid redirection problems when redirecting to a page which
                 * needs correct login information - for which we already know
                 * that it is false.
                 */
            }
            $user = $this->_loadUser();
            $this->_setPwd($user, $new_pwd, $repeat_pwd); // may throw exception

        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            unset($e);
            $isSuccess = false;
        }
        return $isSuccess;
    }

    /**
     * Set new password
     *
     * @param       \Yana\Security\Users\IsUser  $user         user instance
     * @param       string                       $newPwd       new password
     * @param       string                       $repeatPwd    new password
     * @throws      \Yana\Core\Exceptions\Security\PasswordDoesNotMatchException  when the passwords don't match
     * @throws      \Yana\Core\Exceptions\Security\PasswordException              when the password was not saved
     */
    private function _setPwd(\Yana\Security\Users\IsUser $user, $newPwd, $repeatPwd)
    {
        if ($newPwd !== $repeatPwd) {
            $message ="The two new passwords entered do not match.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Security\PasswordDoesNotMatchException($message, $level);
        }
        try {
            $this->_getSecurityFacade()->changePassword($user, $newPwd);

        } catch (\Exception $e) { // unable to set password
            $message = "Unable to set password.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\Security\PasswordException($message, $level, $e);
        }
    }

    /**
     * Check Password reset ticket.
     *
     * @param   string  $recoveryId recovery id
     * @return  array
     * @throws  \Yana\Core\Exceptions\Security\PasswordExpiredException  when the user's password has expired
     */
    private function _getUserId($recoveryId)
    {
        assert('is_string($recoveryId); // Invalid argument $recoveryId: string expected');

        $database = \Yana\Security\Users\SessionManager::getDatasource();
        $user = $database->select('user', array('user_recover_id', '=', $recoveryId));

        assert('is_array($user); // $user must be of type array');
        if (count($user) !== 1) {
            $message = "No user found with with recovery id: " . \htmlentities($recoveryId);
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
        }

        $user = array_pop($user);
        // Note: 14400 = 4h
        if (!isset($user['USER_RECOVER_UTC']) || $user['USER_RECOVER_UTC'] + 14400 < time()) {
            throw new \Yana\Core\Exceptions\Security\PasswordExpiredException();
        }

        return $user['USER_ID'];
    }

    /**
     * Logout and destroy session
     *
     * @type        security
     * @template    message
     * @onsuccess   text: Yana\Core\Exceptions\Messages\LogoutMessage
     */
    public function logout()
    {
        //$this->_getSecurityFacade()->logout();
        $this->_createLoginManager()->handleLogout($this->_loadUser());
    }

    /**
     * Check if login data is correct
     *
     * @type        security
     * @template    message
     *
     * @param       string  $user  user name
     * @param       string  $pass  password
     * @throws      \Yana\Core\Exceptions\Security\InvalidLoginException      when invalid password was entered
     * @throws      \Yana\Core\Exceptions\Security\PermissionDeniedException  when invalid password was entered 3 times
     * @onsuccess   text: Yana\Core\Exceptions\Messages\LoginMessage
     */
    public function check_login($user, $pass = "")
    {
        assert('!isset($session); // Cannot redeclare var $nextAction');
        $session = $this->_getSession();
        try {
            $this->_getSecurityFacade()->login($user, $pass);

        } catch (\Yana\Core\Exceptions\NotFoundException $e) {

            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\Security\InvalidLoginException("Invalid name or password.", $level);

        } catch (\Yana\Core\Exceptions\InvalidLoginException $e) {

            // delay output if attempt failed to make brute-force attacks more difficult to commit
            if (isset($session['on_login_goto'])) {
                unset($session['on_login_goto']);
            }

            /* create a log for each failed login attempt */
            \Yana\Log\LogManager::getLogger()->addLog("Login attempt failed for user '{$user}'. Invalid password.");

            /* The sleep-command is introduced for security reasons,
             * to make brute-force attacks on password forms harder.
             */
            sleep(2);
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\Security\InvalidLoginException("Invalid name or password.", $level);
        }

        self::$userName = $user;

        /* route next action */
        if (isset($session['on_login_goto']) && is_string($session['on_login_goto']) && $session['on_login_goto'] > "") {

            assert('!isset($nextAction); // Cannot redeclare var $nextAction');
            $nextAction = $session['on_login_goto'];
            unset($session['on_login_goto']);
            $this->_getApplication()->exitTo($nextAction);
        }

        return true;
    }

    /**
     * @return  \Yana\Security\Users\Logins\Manager
     */
    private function _createLoginManager()
    {
        return new \Yana\Security\Users\Logins\Manager();
    }

    /**
     * @param  string  $userName  identifies user
     * @return  \Yana\Security\Users\IsUser
     */
    private function _loadUser($userName = "")
    {
        $builder = new \Yana\Security\Users\UserBuilder();
        if ($userName > "") {
            $user = $builder->buildFromUserName($userName);
        } else {
            $user = $builder->buildFromSession($this->_getSession());
        }
        return $user;
    }

}

?>