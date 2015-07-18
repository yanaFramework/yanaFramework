<?php
/**
 * Password quality - Setup
 *
 * {@translation
 *
 *    de:  Passwortqualität - Setup
 *
 *    Dieses Plugin erlaubt die Konfiguration der Anforderungen zur Passwortqualität.
 * }
 *
 * @author     Dariusz Josko
 * @type       security
 * @extends    user
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @priority   highest
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\UserPwdAdmin;

/**
 * password quality
 *
 * This implements setup functions for the password quality plugin.
 *
 * @package    yana
 * @subpackage plugins
 */
class UserPwdAdminPlugin extends \Yana\Plugins\AbstractPlugin
{
    /**
     * Default event handler
     *
     * @access  public
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @return  bool
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Password quality Setup (Default settings)
     *
     * @type        security
     * @user        group: admin, level: 100
     * @template    user_pwd_quality
     * @menu        group: setup
     * @safemode    true
     *
     * @access      public
     */
    public function get_pwd_quality_default()
    {
        $YANA = $this->_getApplication();
        $YANA->setVar("ON_SUBMIT", "set_config_default");
        $configFile = $YANA->getResource('system:/config');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());
    }

    /**
     * Password quality Setup (Profile specific settings)
     *
     * @type        security
     * @user        group: admin, level: 100
     * @template    guestbook_config_template
     * @menu        group: setup
     * @safemode    false
     *
     * @access      public
     */
    public function get_pwd_quality_profile()
    {
        $YANA = $this->_getApplication();
        $YANA->setVar("ON_SUBMIT", "set_config_profile");
        $configFile = $YANA->getResource('system:/config');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());
    }

    /**
     * Check Password time duration before login
     *
     * @subscribe
     *
     * @access  public
     * @param   string  $user  user name
     * @param   string  $pass  password
     * @return  bool
     */
    public function check_login($user, $pass = "")
    {
        /* @var $YANA \Yana\Application */
        assert('!isset($YANA); // Cannot redeclare var $YANA');
        $YANA = $this->_getApplication();
        $timeDuration = (int) $YANA->getVar("PROFILE.USER.PASSWORD.TIME");
        if ($timeDuration > 0) {
            if ($this->_isExpired($user, $timeDuration)) {
                $message = "Your password has expired.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                new \Yana\Core\Exceptions\Security\PasswordExpiredException($message, $level);
                $YANA->exitTo("get_pwd");
            }
        }
        return true;
    }

    /**
     * Cchecks if the password quality is high enough.
     *
     * @subscribe
     *
     * @access      public
     * @param       string  $old_pwd     old password
     * @param       string  $new_pwd     new password
     * @param       string  $repeat_pwd  duplicate of new password
     * @return      bool
     */
    public function set_pwd($old_pwd, $new_pwd, $repeat_pwd)
    {
        $YANA = $this->_getApplication();

        /* get the minimum quality which is defined in profile */
        $min_quality = (int) $YANA->getVar("PROFILE.USER.PASSWORD.QUALITY");
        if ($min_quality < 0) {
            $min_quality = 0;
        }
        if ($min_quality > 100) {
            $min_quality = 100;
        }
        if ($this->_getQuality($new_pwd) < $min_quality) {
            $message = "Password is not complex enough.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Security\LowPasswordQualityException($message, $level);
        }
        // check if the password does not match the last (max. 5) used passwords
        self::_isAllowedPwd($old_pwd, $new_pwd); // may throw exception
        return true;
    }

    /**
     * get password quality
     *
     * This function returns an integer of (0-100).
     *
     * @param   string  $password  user's new password
     * @return  int
     */
    private function _getQuality($password)
    {
        assert('is_string($password); // $password must be of type string');
        assert('!empty($password); // $password can not be empty');

        /*  count the length of the password */
        $level = 0;
        $maxSecurityLevel = 8;
        $length = (int)strlen($password);
        if ($length > 4) {
            $level ++;
        }

        if ($length > 7) {
            $level ++;
        }

        if ($length > 11) {
            $level ++;
        }

        /* count the numbers in the password string*/
        $num_numeric = strlen(preg_replace('/[0-9]/', '', $password));
        $numeric=($length - (int) $num_numeric);
        if ($numeric > 0) {
            $level ++;
        }

        /* count the special characters */
        $symbols = strlen(preg_replace('/\W/', '', $password));
        $num_symbols=($length -(int) $symbols);
        if ($num_symbols > 0) {
            $level ++;
        }

        /* count the upper case characters */
        $num_upper = strlen(preg_replace('/[A-Z]/', '', $password));
        $upper=($length - (int) $num_upper);
        if ($upper > 0) {
            $level ++;
        }

        /* number | not a number | number */
        $count_numbers_in_order = strlen(preg_replace('/\d\D+\d/', '', $password));
        $numbers= ($length - (int) $count_numbers_in_order);
        if ($numbers > 0) {
            $level ++;
        }
        /* charachter | non-character | character */
        $num_upper_in_order = strlen(preg_replace('/[A-Z][^A-Z]+[A-Z]/', '', $password));
        $upper=($length - (int) $num_upper_in_order);
        if ($upper != 0) {
            $level ++;
        }
        /* calculate the password strength */
        $pwd_strength = ($level / $maxSecurityLevel) * 100;
         
        /* check if password strength (quality) is out of range */
        if ($pwd_strength < 0) {
            $pwd_strength = 0;
        }
        if ($pwd_strength > 100) {
            $pwd_strength = 100;
        }
        return $pwd_strength;
    }

    /**
     * Password time duration.
     *
     * This function checks the date of expiration and returns bool(true)
     * if the password has expired, and bool(false) otherwise.
     *
     * @param   string  $userName       user name
     * @param   int     $timeDuration   number of months a password is valid
     * @return  bool
     */
    private function _isExpired($userName, $timeDuration)
    {
        assert('is_string($userName); // $userName must be of type string');
        assert('!empty($userName); // $userName can not be empty');
        assert('is_int($timeDuration); // $timeDuration must be of type int');
        $db = \Yana\Security\Users\SessionManager::getDatasource();

        /* get the current user password expiry time */
        $time = $db->select("user.$userName.user_pwd_time");
        $currentTime = time();
        $expiryTime = $currentTime;
        if (!empty($time)) {
            $expiryTime = $time + ($timeDuration * 30 * 24 * 60 * 60);
        }
        return $expiryTime < $currentTime;
    }

    /**
     * Password allowed
     *
     * This function checks if the password was allready used by the user.
     * So it can be guaranteed that the user will not reuse the same passwords.
     *
     * The maximum number of past passwords is stored in the profile settings at
     * key $PROFILE.USER.PASSWORD.COUNT.
     *
     * @param   string  $old_password  old password
     * @param   string  $new_password  new password
     * @return  bool
     * @throws  \Yana\Core\Exceptions\Security\InvalidLoginException  when name or password are invalid
     */
    private function _isAllowedPwd($old_password, $new_password)
    {
        assert('is_string($new_password); // $new_password must be of type string');
        assert('!empty($new_password); // $new_password can not be empty');
        $YANA = $this->_getApplication();
        $db = \Yana\Security\Users\SessionManager::getDatasource();
        
        /* get information how many passwords which was allready used will be needed for checking with the new one */
        $count_pwd = (int) $YANA->getVar("PROFILE.USER.PASSWORD.COUNT");

        /* get the current user from session*/
        $user = $_SESSION['user_name'];
         
        /* get the database information from the user table for the curren user */
        $currentUserInformation = $db->select('user', array('USER_ID', '=', $user));

        $currentUserInformation = array_pop($currentUserInformation);
        $new_password = \Yana\User::calculatePassword($user, $new_password);
        /* needed for equal with the actually password */
        $old_password = \Yana\User::calculatePassword($user, $old_password);
         
        assert('is_array($currentUserInformation); // the value must be of type array');
        assert('!empty($currentUserInformation); //   the value can not be empty');

        /* check if the old password is correct */
        if (isset($currentUserInformation['USER_PWD']) && $currentUserInformation['USER_PWD'] != $old_password) {
            if ($currentUserInformation['USER_PWD'] != 'UNINITIALIZED') {
                $message = "Invalid name or password.";
                $level = \Yana\Log\TypeEnumeration::ERROR;
                throw new \Yana\Core\Exceptions\Security\InvalidLoginException($message, $level);
            }
        }
        /* check if the new password is the same like the last one */
        if (isset($currentUserInformation['USER_PWD']) && $currentUserInformation['USER_PWD'] == $new_password) {
            $message = "Password is the same that you have used before. Please select another one.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Security\PasswordDoesNotMatchException($message, $level);
        }
        /* check if the new password is the same like the last (max. 5)*/
        $currentPWDList = $currentUserInformation['USER_PWD_LIST'];
        if (isset($currentPWDList)) {
            if (in_array($new_password, $currentPWDList)) {
                $message = "Password is the same that you have used before. Please select another one.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\Security\PasswordUsedBeforeException($message, $level);
            }
            array_push($currentPWDList, $currentUserInformation['USER_PWD']);
            // update the user password list or insert the list if does not exist
            if (count($currentPWDList) >= $count_pwd) {
                $currentPWDList = array_splice($currentPWDList, count($currentPWDList) - $count_pwd);
            }
        } else {
            // create the list with the password
            $currentPWDList = array($currentUserInformation['USER_PWD']);
        }
        try {
            $db->update("USER.$user.USER_PWD_LIST", $currentPWDList);
            /* set pwd create date if not exist or update */
            $db->update("USER.$user.USER_PWD_TIME", mktime());
            $db->commit(); // may throw exception
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}

?>