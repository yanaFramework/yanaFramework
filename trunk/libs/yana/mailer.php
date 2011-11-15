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
 */

/**
 * create and send mails based on templates
 *
 * @package     yana
 * @subpackage  mail
 */
class Mailer extends \Yana\Core\Object
{

    /**
     * @var \Yana\Views\Template
     */
    private $_template = null;

    /**
     * Handler function to send mail.
     *
     * @var  mixed
     */
    private $_mailHandler = null;

    /**
     * Global handler function to send mail.
     *
     * @var  mixed
     */
    private static $_globalMailHandler = "mb_send_mail";

    /**
     * Subject line of mail to be send.
     *
     * @var  string
     */
    private $_subject = "";

    /**
     * Sender of mail to be send.
     *
     * Be warned that some e-mail providers may check
     * if the address given here exists and will
     * not deliver any e-mail in case it doesn't.
     *
     * @var  string
     */
    private $_sender = "";

    /**
     * This sets up the content of the E-Mail from a template of your choice.
     *
     * @param  \Yana\Views\Template  $template  E-Mail template
     */
    public function __construct(\Yana\Views\Template $template)
    {
        $this->_template = $template;
    }

    /**
     * Get subject line.
     *
     * @return  string
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Set subject line.
     *
     * @param   string  $subject  single-line of text
     * @return  Mailer
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * Get sender of mail.
     *
     * @return  string
     */
    public function getSender()
    {
        return $this->_sender;
    }

    /**
     * Set sender of mail.
     *
     * Be warned that some e-mail providers may check
     * if the address given here exists and will
     * not deliver any e-mail in case it doesn't.
     *
     * @param   string  $sender  e-mail address
     * @return  Mailer
     */
    public function setSender($sender)
    {
        $this->_sender = $sender;
        return $this;
    }

    /**
     * Send an e.mail.
     *
     * This function sends an e.mail with the currently set subject
     * and content to the recipient you provide.
     *
     * @uses    $mailer->send('recipient@somewhere.tld');
     *
     * @param   string  $recipient  mail address
     * @return  bool
     * @since   2.8
     */
    public function send($recipient)
    {
        assert('is_string($this->_subject); // Invalid property "subject". String expected');
        assert('is_string($this->_sender); // Invalid property "sender". String expected');
        assert('is_string($recipient); // Wrong type for argument 1. String expected');

        /* prepare header */
        if (!empty($this->_sender)) {
            $header = array();
            $header['from']        = $this->_sender;
            $header['return-path'] = $this->_sender;
        } else {
            $header = array();
        }
        $content = preg_replace("/<br ?\/?>/", "\n", (string) $this);
        return self::mail($recipient, "[MAILFORM] " . $this->_subject, $content, $header, $this->_mailHandler);
    }

    /**
     * set global mail handler function
     *
     * The mail handler may be any function that implements the same interface
     * as PHP's mail() function.
     *
     * It is called when sending an e-mail.
     *
     * This is a global setting that is used as a default for all instances.
     * It may be overwritten for a particular instead where needed.
     *
     * @param   function  $function  callable handler function
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the function is not callable
     */
    public static function setGlobalMailHandler($function)
    {
        if (is_callable($function)) {
            self::$_globalMailHandler = $function;
        } else {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("The argument '" .
                print_r($function, true) . "' is not a callable mail handler.",
                E_USER_WARNING);
        }
    }

    /**
     * get global mail handler function
     *
     * The mail handler may be any function that implements the same interface
     * as PHP's mail() function.
     *
     * It is called when sending an e-mail.
     *
     * The returned value may either be a lambda-function, a string containing
     * a function name, or an array containing a class or object reference and
     * a method name.
     *
     * This is a global setting that is used as a default for all instances.
     * It may be overwritten for a particular instead where needed.
     *
     * @return  mixed
     */
    public static function getGlobalMailHandler()
    {
        assert('is_callable(self::$_globalMailHandler); // Unexpected value. mailHandler should be callable');
        return self::$_globalMailHandler;
    }

    /**
     * set mail handler function
     *
     * The mail handler may be any function that implements the same interface
     * as PHP's mail() function.
     *
     * It is called when sending an e-mail.
     *
     * @param   function  $function  callable handler function
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the function is not callable
     */
    public function setMailHandler($function)
    {
        if (is_callable($function)) {
            $this->_mailHandler = $function;
        } else {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("The argument '" .
                print_r($function, true) . "' is not a callable mail handler.",
                E_USER_WARNING);
        }
    }

    /**
     * get mail handler function
     *
     * The mail handler may be any function that implements the same interface
     * as PHP's mail() function.
     *
     * It is called when sending an e-mail.
     *
     * The returned value may either be a lambda-function, a string containing
     * a function name, or an array containing a class or object reference and
     * a method name.
     *
     * If no local mail handler function was set, the global mail handler is
     * returned instead.
     *
     * @return  mixed
     */
    public function getMailHandler()
    {
        if (is_callable($this->_mailHandler)) {
            return $this->_mailHandler;
        } else {
            return self::getGlobalMailHandler();
        }
    }

    /**
     * send an e.mail
     *
     * This function sends an e.mail and lets
     * you provide the recipient, subject, text and header.
     *
     * Note that this method introduces several filters to
     * prevent header-injection attacks, that might be used
     * to send spam mails.
     *
     * Due to this feature, this method should be prefered
     * over of PHP's native mail() function in any productive
     * environment.
     *
     *
     * One should note that the 4th parameter, containing the
     * header-information, is an associative array here, rather
     * than a string.
     *
     * This means, you need to write this parameter as follows:
     * <code>
     * $header = array(
     * 'from'         => $senderMail,
     * 'cc'           => $theOtherGuy,
     * 'content-type' => 'text/plain; charset=UTF-8'
     * );
     * Mailer::mail($recipient, $subject, $text, $header);
     * // instead of:
     * $header = "from: $senderMail;\n";
     * $header .= "cc: $theOtherGuy;\n";
     * $header .= "content-type: text/plain; charset=UTF-8;\n";
     * mail($recipient, $subject, $text, $header);
     * </code>
     *
     * <ul>
     * <li>
     * Note that for security reasons the $header parameter does not
     * allow recipients to be defined using 'bcc'. You should use 'cc'
     * instead.
     * </li>
     *
     * <li>
     * Also note that the default encoding for mails send via this method
     * is plain text in ISO Latin-1. You may change this via the $header
     * var 'content-type'.
     * </li>
     *
     * <li>
     * When sending HTML-mails the following tags will automatically be
     * removed for security reasons: link, meta, script, style, img,
     * embed, object, param.
     * In addition any '@' sign within the subject or text of the message
     * will be replaced by the phrase "[at]".
     * As this is a blacklist approach, it is not completely failproof.
     * You are encouraged to prepend additional checks as you see fit,
     * before passing your params to this method.
     * </li>
     *
     * <li>
     * When recieving a mail send via this method, you might want to check
     * the mail's header for two flags named 'x-yana-php-header-protection'
     * and 'x-yana-php-spam-protection'.
     * The first indicates the result of the header-injection checks.
     * It's value should be '0'. If the value is '1', than some filter did
     * note some suspicious header data and dropped it, before sending the
     * mail. This might be the result of an header-injection attack.
     * The number is followed by a white-space and a description in
     * round brackets, e.g. "0 (no suspicious header found)".
     * The second indicates the result of the spam-protection checks.
     * You may preset both values if you want to run your own filters
     * before passing values to this method.
     * In addition you might want to tell your local spam-filter to sort out
     * any messages, that have these parameters set to '1'.
     * </li>
     * </ul>
     *
     * See the developer's cookbook for more detailed information and examples.
     *
     * @uses    Mailer::mail('recipient@somewhere.tld', 'My Subject', 'My Text', array('from' => 'myMail@domain.tld'));
     *
     * @param   string    $recipient    mail address
     * @param   string    $subject      short description
     * @param   string    $text         message text
     * @param   array     $header       (optional)
     * @param   function  $mailHandler  function to handle mails
     * @return  bool
     * @since   2.8
     */
    public static function mail($recipient, $subject, $text, array $header = array(), $mailHandler = null)
    {
        if (is_null($mailHandler)) {
            $mailHandler = self::$_globalMailHandler;
        }
        assert('is_string($recipient); // Wrong type for argument 1. String expected');
        assert('is_string($subject); // Wrong type for argument 2. String expected');
        assert('is_string($text); // Wrong type for argument 3. String expected');
        assert('is_callable($mailHandler); // Wrong type for argument 5. Function expected');

        $recipient = filter_var($recipient, FILTER_SANITIZE_EMAIL);
        $subject = strip_tags(\Yana\Io\StringValidator::sanitize($subject, 128, \Yana\Io\StringValidator::LINEBREAK));
        assert('is_string($text); // Unexpected result: $text. String expected.');

        /* settype to ARRAY */
        $header = (array) $header;
        $header = \Yana\Util\Hashtable::changeCase($header, CASE_LOWER);

        $default_header = array();
        $default_header['x-mailer']      = "PHP/". phpversion();
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $default_header['x-sender-ip'] = $_SERVER['REMOTE_ADDR'];
        } else {
            $default_header['x-sender-ip'] = 'not available';
        }
        $default_header['x-server-time'] = date("d.m.Y H:i:s", time());
        $default_header['content-type']  = 'text/plain; charset=UTF-8';
        $default_header['mime-version']  = '1.0';
        $default_header['x-yana-php-header-protection'] = '0 (no suspicious header found)';
        $default_header['x-yana-php-spam-protection']   = '0 (no recipients were dropped)';

        $untainted_header = array();

        if (is_array($header)) {
            assert('!isset($key); /* cannot redeclare variable $key */');
            assert('!isset($value); /* cannot redeclare variable $value */');
            foreach ($header as $key => $value)
            {
                if (preg_match('/^[a-z\d-]+$/', $key) && !preg_match('/[\r\n]/', $value)) {
                    $value = \Yana\Io\StringValidator::sanitize($value, 128, \Yana\Io\StringValidator::LINEBREAK);
                    if (!empty($value)) {
                        switch ($key) {
                            case 'cc':
                                if (is_array($value)) {
                                    assert('!isset($a_key); /* cannot redeclare variable $a_key */');
                                    assert('!isset($a_value); /* cannot redeclare variable $a_value */');
                                    foreach ($value as $a_key => $a_value)
                                    {
                                        if (filter_var($a_value, FILTER_VALIDATE_EMAIL)) {
                                            if (empty($untainted_header['cc'])) {
                                                $untainted_header['cc']  = "$a_value";
                                            } else {
                                                $untainted_header['cc'] .= "; $a_value";
                                            }
                                        }
                                    } /* end foreach */
                                    unset($a_key, $a_value);
                                } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                    $untainted_header['cc'] = "$value";
                                }
                            break;
                            case 'bcc':
                                /* bcc is not allowed! */
                                $spamProtection = '1 (bcc is not allowed in form mail - recipients were dropped)';
                                $untainted_header['x-yana-php-spam-protection'] = $spamProtection;
                            break;
                            case 'return-path':
                                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                    $untainted_header['return-path'] = "$value";
                                }
                            break;
                            case 'from':
                                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                    $untainted_header['from'] = "$value";
                                }
                            break;
                            case 'content-type':
                                if (preg_match('/^(\w+\/\w+);( ?| +)charset="?[\w\d-]+"?$/i', $value)) {
                                    $untainted_header['content-type'] = "$value";
                                }
                            break;
                            case 'mime-version':
                                if (preg_match('/^\d\.\d$/', $value)) {
                                    $untainted_header['mime-version'] = "$value";
                                }
                            break;
                            case 'content-transfer-encoding':
                                if (preg_match('/^\d{,2}bit$/i', $value)) {
                                    $untainted_header['content-transfer-encoding'] = "$value";
                                }
                            break;
                            default:
                                if (preg_match('/^x-[a-z\d-]+$/', $key)) {
                                    $headerProtection = 'x-yana-php-header-protection';
                                    if ($key != $headerProtection && $key != 'x-yana-php-spam-protection') {
                                        $untainted_header[$key] = "$value";
                                    } else {
                                        return false;
                                    }
                                } else {
                                    $headerProtection = '1 (Suspicious header attribute dropped due to '.
                                        'security reasons. Mail might contain errors)';
                                    $untainted_header['x-yana-php-header-protection'] = $headerProtection;
                                }
                            break;
                        }
                    }
                } else {
                    $headerProtection = '1 (Suspicious header attribute dropped due to security reasons. '.
                        'Mail might contain errors)';
                    $untainted_header['x-yana-php-header-protection'] = $headerProtection;
                }
            } /* end foreach */
            unset($key,$value);
        } /* end if */

        assert('!isset($key); /* cannot redeclare variable $key */');
        assert('!isset($value); /* cannot redeclare variable $value */');
        foreach ($default_header as $key => $value)
        {
            if (empty($untainted_header[$key]) && !empty($value)) {
                $untainted_header[$key] = $value;
            }
        }
        unset($key,$value);

        $text = preg_replace('/@/', '[at]', "$text");

        if (preg_match('/^text\/plain/i', $untainted_header['content-type'])) {
            $text = wordwrap($text, 70);
        } elseif (preg_match('/^text\/html/i', $untainted_header['content-type'])) {
            while (preg_match('/<\/?(\?|\!|link|meta|script|style|img|embed|object|param|).*>/Usi', $text))
            {
                $text = preg_replace('/<\/?(\?|\!|link|meta|script|style|img|embed|object|param|).*>/Usi', '', $text);
            }
        }

        $result_header = "";
        assert('!isset($key); /* cannot redeclare variable $key */');
        assert('!isset($value); /* cannot redeclare variable $value */');
        foreach ($untainted_header as $key => $value)
        {
            $result_header .= "$key: $value\n";
        }
        unset($key, $value);

        /*
         * error - argument $recipient may not be empty
         */
        if (empty($recipient)) {
            trigger_error("Cannot send an e-mail without a valid recipient.", E_USER_NOTICE);
            return false;

        /*
         * error - argument $subject may not be empty
         */
        } elseif (empty($subject)) {
            trigger_error("Cannot send an e-mail without a subject.", E_USER_NOTICE);
            return false;

        /*
         * error - argument $text may not be empty
         */
        } elseif (empty($text)) {
            trigger_error("Cannot send an e-mail without text.", E_USER_NOTICE);
            return false;

        /*
         * success - send mail
         */
        } else {
            return (bool) call_user_func($mailHandler, $recipient, $subject, $text, $result_header);
        }
    }

    /**
     * Fetches the template and returns it as a string.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->_template->__toString();
    }

}

?>