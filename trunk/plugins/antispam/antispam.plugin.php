<?php
/**
 * Anti-Spam
 *
 * Various smart techniques to avoid spam.
 *
 * {@translation
 *
 *   de: Anti-Spam
 *
 *       Verschiedene clevere Techniken zum Schutz vor Spam.
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       default
 * @priority   high
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * automated spam protection
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_antispam extends StdClass implements IsPlugin
{

    /**
     * Default event handler.
     *
     * returns bool(true) on success and bool(false) on error.
     *
     * {@internal
     *
     * The corresponding form fields are created in SmartUtility::htmlPostProcessor();
     *
     * }}
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     */
    public function catchAll($event, array $ARGS)
    {
        assert('is_string($event); // Wrong type for argument 1. String expected');

        $yana = Yana::getInstance();
        $eventType = mb_strtolower($yana->getPlugins()->getEventType("$event"));
        unset($event);

        /**
         * 0) prepare settings
         */
        $settings = $yana->getVar('PROFILE.SPAM');
        $permission = $yana->getVar('PERMISSION');

        /**
         * Register ouput filter if option has been activated
         */
        if (!empty($settings['FORM_ID'])) {
            /**
             * Note a difference between PHP 4 and 5 here.
             * PHP 5 returns and assigns the object by reference.
             * PHP 4 returns the object by reference but then
             * assigns it by value.
             *
             * It is important to use '=&' to get the same
             * behaviour for both versions.
             */
            $smarty = $yana->getView()->getSmarty();
            $smarty->registerFilter(Smarty::FILTER_OUTPUT, array(__CLASS__, '_outputFilter'));
        }

        /**
         * 1) Permission setting
         *
         * {@internal
         *
         * Let's say: a robot would'nt have been able to log in,
         * because it would have had to defeat the login form
         * which is also protected by this implementation.
         * In addition if it DID conquer the login form already,
         * then it is quite obvious it would conquer them all.
         *
         * Thus, the "permission" option allows to disable this
         * check, if the user is already logged in.
         *
         * }}
         */
        if (empty($settings['PERMISSION']) && is_int($permission) && $permission > 0) {
            return true;

        }
        if ($eventType !== 'write') {
            return true;
        }

        /**
         * 2) create header information for review by administrator
         *
         * Note: remote address is logged automatically - no need to add this here
         */
        $header_data = array();
        if (isset($_SERVER['HTTP_REFERER'])) {
            $header_data['REFERER'] = $_SERVER['HTTP_REFERER'];
        } else {
            $header_data['REFERER'] = '-';
        }
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $header_data['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
        } else {
            $header_data['REQUEST_METHOD'] = '-';
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $header_data['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $header_data['USER_AGENT'] = '-';
        }
        if (isset($_SERVER['REMOTE_PORT'])) {
            $header_data['PORT'] = $_SERVER['REMOTE_PORT'];
        } else {
            $header_data['PORT'] = '-';
        }

        /**
         * 3) Form-id setting
         *
         * check the time the user required to fill out the form
         */
        if (!empty($settings['FORM_ID'])) {
            $time =& $_SESSION['transaction_isolation_created'];
            /**
             * 3.1) if the time is not set or = 0,
             * then we assume the form has never been displayed and we just encountered a direct request
             */
            if (empty($time)) {

                if (!empty($settings['LOG'])) {
                    assert('!isset($log); // Cannot redeclare var $log');
                    $log = 'SPAM: blocked entry because no timestamp present ' .
                        '(possibly the form was never displayed).';
                    \Yana\Log\LogManager::getLogger()->addLog($log, E_USER_NOTICE, $header_data);
                    unset($log);
                }
                throw new SpamError();
            }
            /**
             * 3.2) assume a user would need at least 5 seconds and at most 30 minutes to fill the form
             */
            if (time() - $time < 5) {

                if (!empty($settings['LOG'])) {
                    assert('!isset($log); // Cannot redeclare var $log');
                    $log = 'SPAM: blocked entry because a previous entry ' .
                        'has been issued within the last 5 seconds.';
                    \Yana\Log\LogManager::getLogger()->addLog($log, E_USER_NOTICE, $header_data);
                    unset($log);
                }
                throw new FormSuspendedWarning();
            }
            if (time() - $time > 1800) {

                if (!empty($settings['LOG'])) {
                    assert('!isset($log); // Cannot redeclare var $log');
                    $log = 'SPAM: blocked entry because maximum time of ' .
                        'life (30 minutes) for the form has been exceeded.';
                    \Yana\Log\LogManager::getLogger()->addLog($log, E_USER_NOTICE, $header_data);
                    unset($log);
                }
                throw new FormTimeoutWarning();
            }
            /**
             * 3.3) if an invisible field has been filled,
             * then we assume that the request was not send via a web browser
             */
            if (!empty($ARGS['yana_url'])) {

                if (!empty($settings['LOG'])) {
                    assert('!isset($log); // Cannot redeclare var $log');
                    $log = 'SPAM: blocked entry because a field that is ' .
                        'not visible to human visitors has been filled.';
                    \Yana\Log\LogManager::getLogger()->addLog($log, E_USER_NOTICE, $header_data);
                    unset($log);
                }
                throw new SpamError();
            }
            /**
             * 4.1) check if input has a valid form id
             */
            if ($yana->getVar('DISABLE_FORM_ID') !== true) {
                switch (true)
                {
                    case empty($ARGS['yana_form_id']):
                    case $_SESSION['yana_form_id'] === 'expired':
                    case strcasecmp($ARGS['yana_form_id'], $_SESSION['yana_form_id']) !== 0:
                        if (!empty($settings['LOG'])) {
                            $message = 'SPAM: blocked entry because no valid form Id has been found.';
                            \Yana\Log\LogManager::getLogger()->addLog($message);
                        }
                        if ($_SESSION['yana_form_id'] === 'expired') {
                            throw new SpamWarning();
                        } else {
                            throw new InvalidInputWarning();
                        }
                        $_SESSION['yana_form_id'] = 'expired';
                        return false;
                    break;
                    default:
                        $_SESSION['yana_form_id'] = 'expired';
                    break;
                }
            } else {
                $_SESSION['yana_form_id'] = 'expired';
            }
        } /* end if */

        /**
         * 4) header setting
         *
         * The following was added in version 2.8.6
         * This checks the header sent by the user for suspicious combinations of data.
         */
        switch (true)
        {
            // any of these criteria identifies correct calls
            case empty($settings['HEADER']):          // filter turned off
            case defined('STDIN'):                    // command line call
            case !isset($_SERVER['REQUEST_METHOD']):  // request method must be set (always)
            case !empty($_COOKIE):                    // bots don't use cookies
            case empty($_SERVER['HTTP_REFERER']):     // typically bots send a falsified referer
            case !empty($_SERVER['HTTP_USER_AGENT']): // reject missing user agent information
                // valid
            break;
            default:
                if (!empty($settings['LOG'])) {
                    $log = "SPAM: blocked entry because of suspicious header. " .
                        "'Referer' is set while 'user agent' is missing.";
                    \Yana\Log\LogManager::getLogger()->addLog($log, $header_data);
                    unset($log);
                }
                throw new SpamError();
            break;
        }

        /**
         * 5) bad word and regular expressions filter
         */
        if (!empty($settings['WORD_FILTER']) && !empty($settings['WORDS'])) {
            if (!empty($settings['REG_EXP'])) {
                $words = join('|', $settings['WORDS']);
                $words = html_entity_decode($words);
                $words = str_replace('||', '|', $words);
                if (@preg_match("/${words}/Usi", print_r($ARGS, true), $m)) {

                    if (!empty($settings['LOG'])) {
                        $log = "SPAM: blocked entry because a blacklisted phrase '" . $m[0]  .
                            "' has been found.";
                        \Yana\Log\LogManager::getLogger()->addLog($log, $header_data);
                        unset($log);
                    }
                    throw new SpamError();

                }
                unset($words);

            } else {
                $string = print_r($ARGS, true);
                foreach ($settings['WORDS'] as $words)
                {
                    $words = html_entity_decode($words);
                    if (!empty($words) && mb_stripos($string, $words) !== false) {

                        if (!empty($settings['LOG'])) {
                            $log = "SPAM: blocked entry because a blacklisted phrase " .
                                "'${words}' has been found.";
                            \Yana\Log\LogManager::getLogger()->addLog($log, $header_data);
                            unset($log);
                        }
                        throw new SpamError();

                    }
                }
                unset($words);
            }
        }

        /**
         * 6) clean up the garbage
         */
        if (isset($ARGS['yana_form_id'])) {
            unset($ARGS['yana_form_id']);
        }
        return true;
    }

    /**
     * <<smarty outputfilter>> outputfilter
     *
     * @access  public
     * @static
     * @param   string  $source  source
     * @return  string
     *
     * @ignore
     */
    public static function _outputFilter($source)
    {
        global $YANA;
        /* Create form id */
        if ($YANA->getVar('DISABLE_FORM_ID') !== true) {
            $yana_form_id = uniqid();
            if (strpos($source, "</form>") !== false) {
                /* insert form id */
                $source   = str_replace("</form>", "<span class=\"yana_button\"><input type=\"text\"".
                        "name=\"yana_form_id\" value=\"$yana_form_id\" /></span>\n</form>", $source);
                $_SESSION['yana_form_id'] = $yana_form_id;
                $YANA->setVar('DISABLE_FORM_ID', true);
            }
        }
        return $source;
    }

    /**
     * Create CAPTCHA-image.
     *
     * parameters taken:
     *
     * <ul>
     * <li> int security_image_index    index of image to display </li>
     * </ul>
     *
     * @type        primary
     * @template    null
     *
     * @access      public
     * @param       int  $security_image_index  id of index to check
     */
    public function security_get_image($security_image_index)
    {
        global $YANA;
        $imagesrc = dirname(__FILE__) . "captchas/security_image" . rand(0, 9) . ".png";
        $file = $YANA->getPlugins()->default_library->getResource('antispam:/security.datfile');
        $contents = array();

        if (!$file->exists()) {
            $file->create();
        }
        $file->read();
        if (!$file->isEmpty()) {
            $contents = $file->getLine(0);
        }

        if (!isset($contents['TIME']) || $contents['TIME'] < time() || $contents['MAX_TIME'] < time()) {
            $contents = array();
            $contents['MAX_TIME'] = time() + 10000;
            for ($i=1;$i<10;$i++)
            {
                $contents["_$i"] = "";
                for ($j=0;$j<5;$j++)
                {
                    $letter = "";
                    // while letter is empty or black-listed
                    while (empty($letter) || in_array(mb_strtolower($letter), array('1', '0', 'o', 'l', 'i')))
                    {
                        switch (rand(0, 2))
                        {
                            case 0:
                                $letter = chr(rand(65, 90));
                            break;
                            case 1:
                                $letter = chr(rand(48, 57));
                            break;
                            case 2:
                                $letter = chr(rand(97, 122));
                            break;
                        }
                    }
                    $contents["_$i"] .= $letter;
                }
            }
        }
        $contents['TIME'] = time() + 1200;
        $file->setLine(0, $contents);
        $file->write();

        if ($security_image_index < 1 || $security_image_index > 9) {
            $text =& $contents['_1'];
        } else {

            $text =& $contents['_'.$security_image_index];
        }

        $image = new \Yana\Media\Image($imagesrc, 'png');
        for ($i = 0; $i < 5; $i++)
        {
            $image->drawString(
                $text[$i],             // Text
                4+($i*9)+rand(0, 1),   // x
                1+rand(-1, 1),         // y
                $image->getColor(      // color (palette index number)
                    40+rand(-30, 60),  // r
                    40+rand(-30, 60),  // g
                    40+rand(-30, 60)   // b
                ),
                5                      // font size
            );
        }
        $image->outputToScreen();
        exit(0);
    }

    /**
     * Test if a string matches the corresponding CAPTCHA.
     *
     * parameters taken:
     *
     * <ul>
     * <li> int security_image_index    index of image to display </li>
     * <li> string security_image       text to compare with CAPTCHA </li>
     * </ul>
     *
     * @type        primary
     * @template    null
     *
     * @access      public
     * @param       int     $security_image_index  id of index to check
     * @param       string  $security_image        user-entered text
     * @return      bool
     */
    public function security_check_image($security_image_index, $security_image)
    {
        global $YANA;

        $permission = $YANA->getVar("PERMISSION");
        if (is_int($permission) && $permission > 0) {
            return true;
        }

        $file = $YANA->getPlugins()->default_library->getResource('antispam:/security.datfile');
        $file->read();
        if (!$file->isEmpty()) {
            $contents = $file->getLine(0);
        } else {
            $contents = array();
        }

        if ($contents['MAX_TIME'] < time()) {
            return false;
        } else {
            if ($security_image_index < 1 || $security_image_index > 9) {
                $text =& $contents['_1'];
            } else {
                $text =& $contents['_'.$security_image_index];
            }
            return (bool) (!empty($text) && ($security_image == $text));
        }
    }

}

?>