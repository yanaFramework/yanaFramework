<?php
/**
 * YANA library
 *
 * Software:  Yana PHP-Framework
 * Version:   {VERSION} - {DATE}
 * License:   GNU GPL  http://www.gnu.org/licenses/
 *
 * This program: can be redistributed and/or modified under the
 * terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 *
 * This notice MAY NOT be removed.
 *
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 *
 * @ignore
 */

/**
 * User
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
class YanaUser extends \Yana\Core\Object
{
    /**
     * Name of currently selected user
     *
     * @var  string
     * @ignore
     */
    protected static $selectedUser = null;

    /**
     * List of existing instances
     *
     * @var  array
     * @ignore
     */
    protected static $instances = array();

    /**
     * database connection
     *
     * @var  \Yana\Db\IsConnection
     */
    private static $_database = null;

    /**
     * Is the currently selected user logged-in (yes/no)
     *
     * @var  bool
     */
    private static $_isLoggedIn = null;

    /**
     * list of user names
     *
     * @var  array
     */
    private static $_userNames = null;

    /** @var string */ private $_name = null;
    /** @var string */ private $_language = null;
    /** @var string */ private $_password = null;
    /** @var string */ private $_mail = null;
    /** @var bool   */ private $_isActive = false;
    /** @var int    */ private $_failureCount = 0;
    /** @var int    */ private $_failureTime = 0;
    /** @var int    */ private $_loginCount = 0;
    /** @var int    */ private $_loginTime = 0;
    /** @var bool   */ private $_isExpert = false;
    /** @var string */ private $_passwordRecoveryId = null;
    /** @var int    */ private $_passwordRecoveryTime = null;
    /** @var int    */ private $_passwordTime = null;
    /** @var array  */ private $_passwords = array();
    /** @var int    */ private $_timeCreated = null;
    /** @var string */ private $_session = null;
    /** @var array  */ private $_groups = null;
    /** @var array  */ private $_roles = null;

    /**
     * update cache
     *
     * @var  array
     * @ignore
     */
    protected $updates = array();

    /**
     * unique application id used for security checks like CSRF-tokens.
     *
     * @var  string
     * @ignore
     */
    protected static $applicationId = null;

    /**
     * get instance of this class
     *
     * Looks up an returns the instance by the given name.
     * If there is none, it creates a new one.
     *
     * If $skinName is NULL the function will return the currently
     * selected main skin instead.
     *
     * @param   string  $userName  name of instance to get
     * @return  YanaUser
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the requested user does not exist
     */
    public static function &getInstance($userName = null)
    {
        if (empty($userName)) {
            $userName = self::getUserName();
            if (empty($userName)) {
                throw new \Yana\Core\Exceptions\NotFoundException();
            }
        } else {
            $userName = mb_strtoupper($userName);
        }

        if (!isset(self::$instances[$userName])) {
            self::$instances[$userName] = new self($userName);
        }
        return self::$instances[$userName];
    }

    /**
     * check if user exists
     *
     * Returns bool(true) if a user named $userName can be found in the current database.
     * Returns bool(false) otherwise.
     *
     * @param   string  $userName   user name
     * @return  bool
     */
    public static function isUser($userName)
    {
        assert('is_string($userName); // Wrong type for argument 1. String expected');

        $db = self::getDatasource();
        return $db->exists("user.$userName");
    }

    /**
     * get currently selected user's name
     *
     * Returns the name of the currently logged-in user as a string.
     * If there is none NULL is returned.
     *
     * @return  string
     */
    public static function getUserName()
    {
        if (!isset(self::$selectedUser)) {
            if (isset($_SESSION['user_name'])) {
                self::$selectedUser = $_SESSION['user_name'];
            }
        }
        return self::$selectedUser;
    }

    /**
     * get list of user names
     *
     * Returns a list of all registered user names.
     *
     * @return  array
     */
    public static function getUserNames()
    {
        if (!isset(self::$_userNames)) {
            $db = self::getDatasource();
            $query = new \Yana\Db\Queries\Select($db);
            $query->setTable('user');
            $query->setColumn('user_id');
            self::$_userNames = $query->getResults();
        }
        return self::$_userNames;
    }

    /**
     * Creates an user by name.
     *
     * @param   string  $userName  current user name
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the requested user does not exist
     */
    private function __construct($userName)
    {
        assert('is_string($userName); // Wrong type for argument 1. String expected');

        $db = self::getDatasource();
        $userInfo = $db->select("user.$userName");
        if (empty($userInfo)) {
            throw new \Yana\Core\Exceptions\NotFoundException("User '$userName' not found.");
        }

        $this->_name = "$userName";
        if (isset($userInfo['USER_LANGUAGE'])) {
            $this->_language = $userInfo['USER_LANGUAGE'];
        }
        $this->_password = $userInfo['USER_PWD'];
        $this->_mail = $userInfo['USER_MAIL'];
        $this->_isActive = !empty($userInfo['USER_ACTIVE']);
        if (isset($userInfo['USER_FAILURE_COUNT'])) {
            $this->_failureCount = $userInfo['USER_FAILURE_COUNT'];
        }
        if (isset($userInfo['USER_FAILURE_TIME'])) {
            $this->_failureTime = $userInfo['USER_FAILURE_TIME'];
        }
        if (isset($userInfo['USER_LOGIN_COUNT'])) {
            $this->_loginCount = $userInfo['USER_LOGIN_COUNT'];
        }
        if (isset($userInfo['USER_LOGIN_LAST'])) {
            $this->_loginTime = $userInfo['USER_LOGIN_LAST'];
        }
        $this->_isExpert = !empty($userInfo['USER_IS_EXPERT']);
        if (isset($userInfo['USER_RECOVER_ID'])) {
            $this->_passwordRecoveryId = $userInfo['USER_RECOVER_ID'];
        }
        if (isset($userInfo['USER_RECOVER_UTC'])) {
            $this->_passwordRecoveryTime = $userInfo['USER_RECOVER_UTC'];
        }
        if (isset($userInfo['USER_PWD_TIME'])) {
            $this->_passwordTime = $userInfo['USER_PWD_TIME'];
        }
        if (isset($userInfo['USER_PWD_LIST'])) {
            $this->_passwords = (array) $userInfo['USER_PWD_LIST'];
        }
        if (isset($userInfo['USER_SESSION'])) {
            $this->_session = $userInfo['USER_SESSION'];
        }
        if (isset($userInfo['USER_INSERTED'])) {
            $this->_timeCreated = $userInfo['USER_INSERTED'];
        }
    }

    /**
     * set datasource
     *
     * @param   \Yana\Db\IsConnection  $database     datasource
     * @ignore
     */
    public static function setDatasource(\Yana\Db\IsConnection $database)
    {
        self::$_database = $database;
    }

    /**
     * get datasource
     *
     * @return  \Yana\Db\IsConnection
     * @ignore
     */
    public static function getDatasource()
    {
        if (!isset(self::$_database)) {
            self::$_database = Yana::connect('user');
        }
        return self::$_database;
    }

    /**
     * Writes back changes to the database.
     *
     * @ignore
     */
    public function __destruct()
    {
        if (!empty($this->updates)) {
            self::$_database->update("user.{$this->_name}", $this->updates);
            self::$_database->commit();
        }
    }

    /**
     * get user groups
     *
     * Returns an array of group names, where the keys are the group ids and the values are
     * the human-readable group names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function getGroups()
    {
        if (!isset($this->_groups)) {
            $this->_groups = self::$_database->select("securityrules.*.group_id", array('user_id', '=', $this->_name));
        }
        return $this->_groups;
    }

    /**
     * get user roles
     *
     * Returns an array of role names, where the keys are the group ids and the values are
     * the human-readable role names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function getRoles()
    {
        if (!isset($this->_roles)) {
            $this->_roles = self::$_database->select("securityrules.*.role_id", array('user_id', '=', $this->_name));
        }
        return $this->_roles;
    }

    /**
     * check login data
     *
     * Returns bool(true) if the password is correct an bool(false) otherwise.
     *
     * @param   string  $userPwd  user password
     * @return  bool
     * @ignore
     */
    public function checkPassword($userPwd)
    {
        assert('is_string($userPwd); // Wrong type for argument 1. String expected');

        $savedPwd = $this->_getPassword();

        // no password
        if ($savedPwd === "UNINITIALIZED") {
            return true;
        }

        $currentPwd = self::calculatePassword($this->_name, $userPwd);
        if ($currentPwd === $savedPwd) {
            // reset failure count
            $this->resetFailureCount();
            return true;
        } else {
            $this->updates['USER_FAILURE_COUNT'] = ++$this->_failureCount;
            $this->updates['USER_FAILURE_TIME'] = $this->_failureTime = time();
            return false;
        }
    }

    /**
     * check if user is logged in
     *
     * Returns bool(true) if the user is currently
     * logged in and bool(false) otherwise.
     *
     * @internal  Note on security:
     * This framework introduces SHA-1 encoded session-ids only to logged-in users and instead
     * provides md5 encoded ids to others.
     * SHA-1 produces a 20 bytes long string (a 40 digits hexadecimal number).
     * MD5 encoded ids are only 16 bytes (a 32 digits hexadecimal number).
     * Thus: if a session-id is shorter than 20 bytes (40 digits) this is an obvious hint that
     * either the user has not logged-in, or the session id is not valid.
     *
     * @return  bool
     */
    public static function isLoggedIn()
    {
        if (!isset(self::$_isLoggedIn)) {
            $name = self::getUserName();
            if (empty($name)) {
                return false;
            }
            try {
                $user = self::getInstance();
            } catch (\Yana\Core\Exceptions\NotFoundException $e) { // user was recently deleted
                return false;
            }
            switch (true)
            {
                case function_exists('sha1') && strlen(session_id()) < 20:
                case !isset($_SESSION['prog_id']) || $_SESSION['prog_id'] !== self::getApplicationId():
                case !isset($_SESSION['user_name']) || $_SESSION['user_name'] !== $name:
                case !isset($_SESSION['user_session']) || $_SESSION['user_session'] !== $user->_session:
                    self::$_isLoggedIn = false;
                break;
                default:
                    self::$_isLoggedIn = true;
                break;
            }
        }
        return self::$_isLoggedIn;
    }

    /**
     * get user name
     *
     * Returns the name of the user as a string.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * get password
     *
     * @return  string
     */
    private function _getPassword()
    {
        return $this->_password;
    }

    /**
     * change password
     *
     * Set login password to $password.
     * If no password is provided, a new random password is auto-generated.
     *
     * In case of success the function returns the new password.
     * (You will need it, if you use a auto-created random password.)
     *
     * @param   string  $password user password
     * @return  string
     * @throws  DbError  when the database update failed
     */
    public function setPassword($password = NULL)
    {
        // auto-generate new random password
        if (is_null($password)) {
            $password = substr(md5(uniqid()), 0, 10);
        }

        assert('is_string($password); // Wrong type for argument 1. String expected');

        $newPwd = self::calculatePassword($this->_name, $password);
        switch (false)
        {
            case self::$_database->update("USER.{$this->_name}.USER_PWD", $newPwd):
            case self::$_database->commit():
                throw new DbError("A new password was requested for user '{$this->_name}'. " .
                    "But the database entry could not be updated.");
            break;
            default:
                $this->_passwords[] = $this->_getPassword();
                if (count($this->_passwords) > 10) {
                    array_shift($this->_passwords);
                }
                $this->updates['USER_PWD_LIST'] = $this->_passwords;
                $this->updates['USER_PWD_TIME'] = $this->_passwordTime = time();
                $this->_resetPasswordRecoveryId();
                $this->_password = $newPwd;
                return $password;
            break;
        }
    }

    /**
     * login
     *
     * This function is used to handle user logins.
     *
     * It destroys any previous session (to prevent session fixation).
     * Creates new session id and updates the user's session information in the database.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @throws  \Yana\Core\Exceptions\Security\InvalidLoginException  when access is denied
     */
    public function login()
    {
        if (!$this->isActive()) {
            throw new \Yana\Core\Exceptions\Security\InvalidLoginException();
        }
        /* never reuse old sessions, to prevent injection of data or session id */
        $this->logout();

        /* create new session with new session id */
        $sessionId = uniqid(self::getApplicationId());
        $encryptedId = "";
        if (function_exists('sha1')) {
            $encryptedId = sha1($sessionId);
        } else {
            /* if sha1 is not supported, fall back to default encryption method */
            $encryptedId = md5($sessionId);
        }
        session_id($encryptedId); // overwrites the session id
        @session_start();

        $_SESSION = array();
        $_SESSION['user_name'] = $this->getName();
        $_SESSION['prog_id'] = self::getApplicationId();
        $_SESSION['user_session'] = md5(session_id());

        // initialize language settings
        if (!empty($this->_language)) {
            assert('!isset($languageManager); // Cannot redeclare var $languageManager');
            $languageManager = Language::getInstance();
            try {

                $languageManager->setLocale($this->_language);
                $_SESSION['language'] = $this->_language;

            } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
                // ignore
            }
            unset($languageManager);
        } // end if

        // set time of last login to current timestamp
        $this->updates['USER_LOGIN_LAST'] = $this->_loginTime = time();
        // mark user as logged-in in database
        $this->updates['USER_SESSION'] = $_SESSION['user_session'];
        // increment login count
        $this->updates['USER_LOGIN_COUNT'] = ++$this->_loginCount;

        self::$_isLoggedIn = true;
        self::$selectedUser = $this->_name;
    }

    /**
     * logout
     *
     * Destroy the current session and clear all session data.
     */
    public function logout()
    {
        // backup language setting before destroying old session
        if (isset($_SESSION['language'])) {
            $this->setLanguage($_SESSION['language']);
        }
        // make session cookie expire (get's deleted)
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        // unset session data
        $_SESSION = array();
        // kill session
        @session_destroy();
        // get rid of the old sesion id - just in case
        @session_regenerate_id();
        // mark user as logged-out in database
        $this->updates["USER_SESSION"] = "";
        self::$_isLoggedIn = false;
    }

    /**
     * update language
     *
     * Sets prefered language of the user, that is used to provide translates GUI elements.
     *
     * @param   string  $language  language or locale string
     */
    public function setLanguage($language)
    {
        assert('is_string($language); // Wrong type for argument 1. String expected');

        $this->_language = "$language";
        $this->updates['USER_LANGUAGE'] = $this->_language;
    }

    /**
     * get prefered language
     *
     * @return  string
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * get failure count
     *
     * Returns the number of times the user entered an invalid password recently.
     * Note: This number is reset, when the user inserts a valid password.
     *
     * The default is 0.
     *
     * @return  int
     */
    public function getFailureCount()
    {
        return (int) $this->_failureCount;
    }

    /**
     * get failure time
     *
     * Returns the timestamp when user last entered an invalid password.
     * Note: This number is reset, when the user inserts a valid password.
     *
     * The default is 0.
     *
     * @return  int
     */
    public function getFailureTime()
    {
        return (int) $this->_failureTime;
    }

    /**
     * reset failure count
     *
     * Resets the number of times the user entered an invalid password back to 0.
     * Use this, when the maximum failure time has expired.
     */
    public function resetFailureCount()
    {
        $this->updates['USER_FAILURE_COUNT'] = $this->_failureCount = 0;
        $this->updates['USER_FAILURE_TIME'] = $this->_failureTime = 0;
    }

    /**
     * reset password recovery id
     */
    private function _resetPasswordRecoveryId()
    {
        $this->updates['USER_RECOVER_ID'] = $this->_passwordRecoveryId = "";
        $this->updates['USER_RECOVER_UTC'] = $this->_passwordRecoveryTime = 0;
    }

    /**
     * get login count
     *
     * Returns the number of times the user sucessfully logged-in.
     *
     * The default is 0.
     * @return  int
     */
    public function getLoginCount()
    {
        return (int) $this->_loginCount;
    }

    /**
     * Get the timestamp when user last sucessfully logged-in.
     *
     * Note: This number is not reset on log-out.
     * Thus you cannot use this settings to check if a user is currently logged-in.
     *
     * The default is 0.
     *
     * @return  int
     */
    public function getLoginTime()
    {
        return (int) $this->_loginTime;
    }

    /**
     * update mail
     *
     * Sets the user's mail address. This information is required to send the user a password.
     *
     * @param   string  $mail  e-mail address
     */
    public function setMail($mail)
    {
        assert('is_string($mail); // Wrong type for argument 1. String expected');

        $this->_mail = "$mail";
        $this->updates['USER_MAIL'] = $this->_mail;
    }

    /**
     * get mail address
     *
     * @return  string
     */
    public function getMail()
    {
        return $this->_mail;
    }

    /**
     * update expert setting
     *
     * Set to bool(true) if the user prefers to see expert applications settings and bool(false)
     * if a simpler GUI is prefered.
     *
     * @param   bool  $isExpert  use expert settings (yes/no)
     */
    public function setExpert($isExpert)
    {
        assert('is_bool($isExpert); // Wrong type for argument 1. Boolean expected');

        $this->_isExpert = !empty($isExpert);
        $this->updates['USER_IS_EXPERT'] = $this->_isExpert;
    }

    /**
     * user prefers expert settings
     *
     * Returns bool(true) if the user prefers to see expert applications settings and bool(false)
     * if a simpler GUI is prefered.
     *
     * @return  string
     */
    public function isExpert()
    {
        return !empty($this->_isExpert);
    }

    /**
     * update expert setting
     *
     * Set to bool(true) if the user should be able to log-in or to bool(false) if the user
     * should be deactivated (suspended) without permanently deleting the user settings.
     *
     * @param   bool  $isActive  use expert settings (yes/no)
     */
    public function setActive($isActive)
    {
        assert('is_bool($isActive); // Wrong type for argument 1. Boolean expected');

        $this->_isActive = !empty($isActive);
        $this->updates['USER_ACTIVE'] = $this->_isActive;
    }

    /**
     * user is active
     *
     * Returns bool(true) if the user is activated and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isActive()
    {
        return (bool) $this->_isActive;
    }

    /**
     * get the time when the user was created
     *
     * @return  string
     */
    public function getTimeCreated()
    {
        return $this->_timeCreated;
    }

    /**
     * get time when password was last changed
     *
     * This returns the timestamp for when the password was last updated.
     * You may use this to determine if the password hasn't changed within a long time and prompt
     * the user to enter a new one.
     *
     * The default is 0.
     *
     * @return  int
     */
    public function getPasswordChangedTime()
    {
        return (int) $this->_passwordTime;
    }

    /**
     * get list of 10 recent passwords
     *
     * This returns a list of MD5-encoded password strings that the user used recently.
     * The list does NOT include the current password.
     *
     * Use this to enforce that the user does not reuse a password multiple times.
     *
     * If there are have been no other passwords then the current, this returns an empty list.
     *
     * @return  array
     */
    public function getRecentPasswords()
    {
        return (int) $this->_passwords;
    }

    /**
     * get password recovery id
     *
     * When the user requests a new password, a recovery id is created and sent to his mail address.
     * This is to ensure that the user is a allowed to reset the password.
     *
     * @return  string
     */
    public function getPasswordRecoveryId()
    {
        return $this->_passwordRecoveryId;
    }

    /**
     * get password recovery time
     *
     * When the user requests a new password, the time is stored.
     * This is meant to check, wether the password recovery request has expired.
     *
     * The default is 0.
     *
     * @return  int
     */
    public function getPasswordRecoveryTime()
    {
        return (int) $this->_passwordRecoveryTime;
    }

    /**
     * create new password recovery id
     *
     * When the user requests a new password, a recovery id is created and the time is stored.
     * This is to ensure that the user is a allowed to reset the password and determine, when the
     * request has expired.
     *
     * Returns the new recovery id.
     *
     * @return  string
     */
    public function createPasswordRecoveryId()
    {
        $this->_passwordRecoveryId = uniqid(substr(md5($this->getMail()), 0, 3));
        $this->updates['USER_RECOVER_ID'] = $this->_passwordRecoveryId;
        $this->_passwordRecoveryTime = time();
        $this->updates['USER_RECOVER_UTC'] = $this->_passwordRecoveryTime;
        return $this->_passwordRecoveryId;
    }

    /**
     * Create a new user.
     *
     * @param   string  $userName  user name
     * @param   string  $mail      e-mail address
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    if another user with the same name already exists
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when no user name is given
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    if another user with the same name already exists
     * @throws  \DbError                                        when the database entry could not be created
     */
    public static function createUser($userName, $mail)
    {
        assert('is_string($userName); // Wrong type for argument 1. String expected');
        assert('is_string($mail); // Wrong type for argument 2. String expected');

        $userName = mb_strtoupper("$userName");

        if (empty($userName)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("No user name given.", E_USER_WARNING);
        }
        if (YanaUser::isUser($userName)) {
            throw new \Yana\Core\Exceptions\AlreadyExistsException("A user with the name '$userName' already exists.");
        }
        // insert user settings
        self::$_database->insert("user.$userName", array('USER_MAIL' => $mail));
        // initialize user profile
        self::$_database->insert("userprofile.$userName", array("userprofile_modified" => time()));
        if (!self::$_database->commit()) {
            throw new \DbError("Unable to commit changes to the database server while trying to update " .
                "settings for user '{$userName}'.");
        }
    }

    /**
     * Remove the chosen user from the database.
     *
     * @param   string  $userName  user name
     * @return  bool
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when no valid user name given
     * @throws  \Yana\Core\Exceptions\NotFoundException         when the given user does not exist
     * @throws  DbError                                         when a query on the database failed
     * @throws  Error                                           when the user may not be deleted for other reasons
     */
    public static function removeUser($userName)
    {
        assert('is_string($userName); // Wrong type for argument 1. String expected');

        if (empty($userName)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("No user name given.", E_USER_WARNING);
        }
        $userName = mb_strtoupper($userName);

        // user should not delete himself
        if ($userName === self::getUserName()) {
            throw new Yana\Core\Exceptions\User\DeleteSelfException();
        }

        // user does not exist
        if (!YanaUser::isUser($userName)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such user: '$userName'.", E_USER_WARNING);
        }

        switch (false)
        {
            // delete profile
            case self::$_database->remove("userprofile.$userName"):
            // delete user's security level
            case self::$_database->remove("securitylevel", array("user_id", "=", $userName), 0):
            // delete access permissions (temporarily) granted by this user
            case self::$_database->remove("securityrules", array("user_created", "=", $userName), 0):
            case self::$_database->remove("securitylevel.*", array("user_created", "=", $userName), 0):
            // delete user settings
            case self::$_database->remove("user.$userName"):
            // commit changes
            case self::$_database->commit():
                throw new DbError("Unable to commit changes to the database server while trying to remove".
                    "user '{$userName}'.");
            break;
            default:
                if (isset(self::$instances[$userName])) {
                    unset(self::$instances[$userName]);
                }
            break;
        }
    }

    /**
     * calculate password
     *
     * This function takes user name and password phrase as clear text and returns the
     * hash-code for this password.
     *
     * @param   string  $salt   user name
     * @param   string  $text   password (clear text)
     * @return  string
     */
    public static function calculatePassword($salt, $text)
    {
        assert('is_scalar($salt); // Wrong argument type for argument 1. String expected.');
        assert('is_scalar($text); // Wrong argument type for argument 2. String expected.');
        $salt = mb_substr(mb_strtoupper("$salt"), 0, 2);

        $string = "$salt$text";

        if (function_exists('sha1')) {
            return sha1($string);
        } else {
            return md5($string);
        }
    }

    /**
     * Application instance id
     *
     * The instance-id identifies the current instance of the installation,
     * where multiple instances of the framework are available on the same server.
     *
     * @return  string
     * @ignore
     */
    protected static function getApplicationId()
    {
        if (!isset(self::$applicationId)) {
            $remoteAddr = '127.0.0.1';
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $remoteAddr = $_SERVER['REMOTE_ADDR'];
            }
            self::$applicationId = $remoteAddr . '@' . dirname(__FILE__);
        }
        return self::$applicationId;
    }
}

?>