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

/**
 * user authentification plugin
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_user extends StdClass implements IsPlugin
{
    /**
     * count boundary
     *
     * Maximum number of times a user may enter
     * a wrong password before its account
     * is suspended for $maxFailureTime seconds.
     *
     * @access  private
     * @var     int
     */
    private $maxFailureCount = 3;
    /**
     * time boundary
     *
     * Maximum time in seconds a user's login
     * is blocked after entering a wrong password
     * $maxFailureCount times.
     *
     * @access  private
     * @var     int
     */
    private $maxFailureTime = 300; /* 300 sec. = 5 minutes */
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
     * constructor
     *
     * @access  public
     * @ignore
     */
    public function __construct()
    {
        global $YANA;
        if (isset($YANA)) {
            self::$userName = YanaUser::getUserName();
            if (!empty(self::$userName)) {
                self::$securityLevel = $YANA->getSession()->getSecurityLevel(self::$userName);
                self::$profileId = Yana::getId();
                $YANA->setVar("SESSION_USER_ID", self::$userName);
                $YANA->setVar("PERMISSION", self::$securityLevel);
            }
            $YANA->setVar("SESSION_ID", session_id());
            $YANA->setVar("SESSION_NAME", session_name());
        }
        SessionManager::addSecurityRule(array(__CLASS__, 'checkSecurityLevel'));
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
     */
    public function catchAll($event, array $ARGS)
    {
        settype($event, "string");

        /* @var $YANA Yana */
        global $YANA;
        $YANA->getLanguage()->readFile("user");

        if ($YANA->getSession()->checkPermission(null, $event)) {
            /* access granted */
            $menu = \Yana\Plugins\Menu::getInstance();
            if (YanaUser::isLoggedIn()) {
                $action = "logout";
            } else {
                $action = "login";
            }
            $menuEntry = new \Yana\Plugins\MenuEntry();
            $menuEntry->setTitle($YANA->getLanguage()->getVar($action));
            \Yana\Plugins\Menu::getInstance()->setMenuEntry($action, $menuEntry);
            return true;
        } else {
            if (!YanaUser::isLoggedIn()) {
                new LoginRequiredWarning();
                $_SESSION['on_login_goto'] = $event;
                $YANA->exitTo("login");
            } else {
                new InsufficientRightsWarning();
                $YANA->exitTo();
            }
        }
    }

    /**
     * check security level
     *
     * @access  public
     * @static
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

        if (!YanaUser::isLoggedIn()) {
            return false;
        }

        if ($userName == self::$userName && self::$profileId == $profileId) {
            return $requiredLevel <= self::$securityLevel;
        }

        $securityLevel = (int) SessionManager::getInstance()->getSecurityLevel($userName, $profileId);

        return $requiredLevel <= $securityLevel;
    }

    /**
     * login screen
     *
     * @type        security
     * @template    login_template
     *
     * @access      public
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
     * @onsuccess   goto: login, text: MailMessage
     * @onerror     goto: set_lost_pwd, text: InvalidInputWarning
     *
     *
     * @access      public
     * @param       array   $ARGS   array of arguments passed to the function
     * @return      bool
     */
    public function get_lost_pwd(array $ARGS)
    {
        global $YANA;
        $sessionManager = SessionManager::getInstance();
        $database = SessionManager::getDatasource();
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
            throw new InvalidMailWarning();
        }

        // check if user exist in the database
        $user = $database->select('user', array('USER_MAIL', '=', $userMail));
        // clean variables
        unset($userMail);
        // e-mail is not present on the db
        if (count($user) !== 1) {
            throw new MailNotFoundError();
        }

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
        $setRecoveryId = $database->update("user.{$userName}", $recovery);
        unset($recovery);
        $database->commit();

        // check for successful user record update
        if ($setRecoveryId == false) {
            return false;
        }

        assert('isset($userName); // variable $userName is not set');

        $subject = $YANA->getLanguage()->getVar('user.6');
        $sender = $YANA->getVar('PROFILE.MAIL');
        assert('filter_var($sender, FILTER_VALIDATE_EMAIL); // $sender not a valid e-mail');

        // get the mail template
        $viewManager = Yana::getInstance()->getView();
        $template = $viewManager->createContentTemplate('id:USER_LOST_PWD');
        $website = 'http://' . $_SERVER['SERVER_ADDR'] . $_SERVER['PHP_SELF'] .
            '?action=set_reset_pwd&key=' . $uniqueKey;

        // set the mail values
        $mailer = new \Yana\Mails\Mailer($template);
        $mailer->setSubject($subject);
        $mailer->setSender($sender);
        $mailer->setVar('NAME', $userName);
        $mailer->setVar('TIME', date("m.d.y H:i:s"));
        $mailer->setVar('WEBSITE', $website);
        return (bool) $mailer->send($recipient);
    }

    /**
     * Send login data screen
     *
     * @type        security
     * @template    USER_RESET_PWD
     * @onerror     text: FileNotFoundError
     *
     * @access      public
     * @param       string  $key  user id
     * @return      bool
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
     * @onerror     goto: login, text: InvalidInputWarning
     *
     * @access      public
     * @param       string  $key         user id
     * @param       string  $new_pwd     new password
     * @param       string  $repeat_pwd  duplicate of new password
     * @return      bool
     */
    public function reset_pwd($key, $new_pwd, $repeat_pwd)
    {
        global $YANA;
        $db = SessionManager::getDatasource();

        // check if user exist in the database
        $userName = $this->_getUserId($key);
        assert('is_string($userName);');

        try {

            $user = YanaUser::getInstance($userName);
            return $this->_setPwd($user, $new_pwd, $repeat_pwd);

        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            return false;
        }
    }

    /**
     * Set new password
     *
     * @type        security
     * @template    message
     * @onsuccess   goto: index
     * @onerror     goto: index
     *
     * @access      public
     * @param       string  $new_pwd     new password
     * @param       string  $repeat_pwd  duplicate of new password
     * @param       string  $old_pwd     old password
     * @return      bool
     * @throws      \Yana\Core\Exceptions\Security\InvalidLoginException  when name or password are invalid
     */
    public function set_pwd($new_pwd, $repeat_pwd, $old_pwd = "")
    {
        try {

            $user = YanaUser::getInstance();
            if (!$user->checkPassword($old_pwd)) {
                $message = "Invalid name or password.";
                $level = \E_USER_ERROR;
                throw new \Yana\Core\Exceptions\Security\InvalidLoginException($message, $level);
                /**
                 * This case exits to the default page (which should be public),
                 * to avoid redirection problems when redirecting to a page which
                 * needs correct login information - for which we already know
                 * that it is false.
                 */
            }
            if (!$this->_setPwd($user, $new_pwd, $repeat_pwd)) {
                throw new Error();
            }
            return true;

        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            return false;
        }
    }

    /**
     * Set new password
     *
     * @access      private
     * @param       YanaUser  $user         user instance
     * @param       string    $newPwd       new password
     * @param       string    $repeatPwd    new password
     * @return      bool
     */
    private function _setPwd(YanaUser $user, $newPwd, $repeatPwd)
    {
        if ($newPwd !== $repeatPwd) {
            return false;
        }
        try {
            $user->setPassword($newPwd);
            return true;
        } catch (DbError $e) { // unable to set password
            return false;
        }
    }

    /**
     * Check Password reset ticket.
     *
     * @access  private
     * @param   string  $recoveryId recovery id
     * @return  array
     * @throws  \Yana\Core\Exceptions\Security\PasswordExpiredException  when the user's password has expired
     */
    private function _getUserId($recoveryId)
    {
        if (!is_string($recoveryId)) {
            throw new InvalidInputWarning("", E_USER_NOTICE);
        }

        $database = SessionManager::getDatasource();
        $user = $database->select('user', array('user_recover_id', '=', $recoveryId));

        assert('is_array($user); // $user must be of type array');
        if (count($user) !== 1) {
            throw new UserNotFoundError();
        }

        $user = array_pop($user);
        // Note: 14400 = 4h
        if (!isset($user['USER_RECOVER_UTC']) || $user['USER_RECOVER_UTC'] + 14400 < time()) {
            throw new \Yana\Core\Exceptions\Security\PasswordExpiredException();
        }

        return $user['USER_ID'];
    }

    /**
     * Logout
     *
     * @type        security
     * @template    message
     * @onsuccess   text: LogoutMessage
     *
     * @access      public
     */
    public function logout()
    {
        // Logout and destroy session
        YanaUser::getInstance()->logout();
        // Restart session with new session id
        session_start();
        session_regenerate_id(true);
    }

    /**
     * Check if login data is correct
     *
     * @type        security
     * @template    message
     *
     * @access      public
     * @param       string  $user  user name
     * @param       string  $pass  password
     * @throws      \Yana\Core\Exceptions\Security\InvalidLoginException      when invalid password was entered
     * @throws      \Yana\Core\Exceptions\Security\PermissionDeniedException  when invalid password was entered 3 times
     */
    public function check_login($user, $pass = "")
    {
        // get user instance
        try {
            $userData = YanaUser::getInstance($user);
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            /* delay output if attempt failed to make brute-force attacks more difficult to commit */
            sleep(2);
            $message = "Invalid name or password.";
            $level = \E_USER_ERROR;
            throw new \Yana\Core\Exceptions\Security\InvalidLoginException($message, $level);
        }

        /* 1. reset failure count if failure time has expired */
        if ($userData->getFailureTime() < time() - $this->maxFailureTime) {
            $userData->resetFailureCount();
        }
        /* 2. exit if the user has 3 times tried to login with a wrong password in last 5 minutes */
        if ($userData->getFailureCount() >= $this->maxFailureCount) {
            throw new \Yana\Core\Exceptions\Security\PermissionDeniedException();
        }
        /* 3. error - login has failed */
        if (!$userData->checkPassword($pass)) {
            // delay output if attempt failed to make brute-force attacks more difficult to commit
            if (isset($_SESSION['on_login_goto'])) {
                unset($_SESSION['on_login_goto']);
            }

            /* create a log for each failed login attempt */
            \Yana\Log\LogManager::getLogger()->addLog("Login attempt failed for user '{$user}'. Invalid password.");

            /* The sleep-command is introduced for security reasons,
             * to make brute-force attacks on password forms harder.
             */
            sleep(2);
            $message = "Invalid name or password.";
            $level = \E_USER_ERROR;
            throw new \Yana\Core\Exceptions\Security\InvalidLoginException($message, $level);
        }
        $nextAction = "";
        if (isset($_SESSION['on_login_goto'])) {
            $nextAction = $_SESSION['on_login_goto'];
            unset($_SESSION['on_login_goto']);
        }
        $userData->login(); // creates new session
        self::$userName = $user;
        new LoginMessage(); // report success

        /* route next action */
        if ($nextAction) {
            Yana::getInstance()->exitTo($nextAction);
        }
        return true;
    }
}

?>