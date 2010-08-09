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
 * create and send mails from form data
 *
 * @access      public
 * @package     yana
 * @subpackage  mail
 */
class FormMailer extends Object
{

    /**#@+
     * @access public
     */

    /** @var string */  public $subject  = "";
    /** @var string */  public $headline = "";
    /** @var string */  public $footline = "";
    /** @var array  */  public $content  = array();

    /**#@-*/

    /**
     * constructor
     *
     * Creates a new instance of this class.
     */
    public function __construct()
    {
        /* intentionally left blank */
    }

    /**
     * send an e-mail
     *
     * Sends the current contents to $recipient,
     * where $recipient should be a valid mail address.
     *
     * Returns bool(true) on success or bool(false) on error.
     *
     * @access  public
     * @param   string    $recipient    mail address
     * @param   function  $mailHandler  function to handle mails
     * @return  bool
     */
    public function send($recipient, $mailHandler = null)
    {
        assert('is_string($recipient); // Wrong type for argument 1. String expected');
        assert('is_null($mailHandler) || is_callable($mailHandler); // Wrong type for argument 2. Function expected');
        assert('!empty($recipient); // Recipient cannot be empty');

        /* settype to STRING */
        $recipient = (string) $recipient;

        if (count($this->content) > 0 && $recipient !== '') {

            /* untaint subject */
            assert('is_string($this->subject);');
            $this->subject = untaintInput($this->subject, "string", 128, YANA_ESCAPE_LINEBREAK);
            $this->subject = strip_tags($this->subject);
            $this->subject = preg_replace("/[^\w \(\)äÄüÜöÖß]/", "", $this->subject);
            /* untaint send mail */
            $test = Mailer::mail($recipient, "[MAILFORM] " . $this->subject, $this->_makeMail(), array(), $mailHandler);
            assert('is_bool($test); // Unexpected result: $test. Boolean expected.');
            return $test;

        } else {
            return false;
        }

    }

    /**
     * make mail
     *
     * @access  private
     * @return  string
     * @ignore
     */
    private function _makeMail()
    {
        assert('is_string($this->headline);');
        $mail  = $this->headline;

        $mail .= "==========================================\n";
        assert('is_string($this->subject);');
        if (!empty($this->subject)) {
            $mail .= "  " . $this->subject . "\n";
            $mail .= "==========================================\n\n";
        }

        assert('is_array($this->content) || is_string($this->content);');
        /* settype to ARRAY */
        $this->content = (array) $this->content;
        assert('!isset($a); /* cannot redeclare variable $a */');
        assert('!isset($b); /* cannot redeclare variable $b */');
        foreach ($this->content as $a => $b)
        {
            /* 1) check if key is valid */
            if (!preg_match('/^[\w\döäüß\/_\- ]+$/si', $a)) {
                $message = "Invalid form data. Key '".$a."' is invalid. ".
                    "Only alpha-numeric characters may be used as form keys.";
                trigger_error($message, E_USER_NOTICE);
            /* 2) check if value has valid type */
            } elseif (is_scalar($b)) {
                $mail .= "    ".$a.":\t".$b."\n";
            } elseif (is_array($b)) {
                $mail .= "    ".$a.":\t".print_r($b, true)."\n";
            } else {
                $message = "Invalid form data in key '".$a."'. Expected a value of a scalar type or an array, found '".
                    gettype($b)."' instead.";
                trigger_error($message, E_USER_NOTICE);
            }
        } /* end foreach */
        unset($a, $b);

        $mail .= "\n==========================================\n";
        $mail .= "Date:\t".date("d.m.Y H:i:s", time())."\n";
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $mail .= "IP:\t".$_SERVER['REMOTE_ADDR']."\n";
        } else {
            $mail .= "IP:\tno IP available\n";
        }

        assert('is_string($this->footline);');
        $mail .= $this->footline;

        return $mail;
    }

    /**
     * create an automatically formated e.mail from an array of form data
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @static
     * @param   string    $recipient    mail address
     * @param   string    $subject      subject of the mail
     * @param   array     &$formdata    form-data submitted by the user
     * @param   function  $mailHandler  function to handle mails
     * @return  bool
     * @since   2.8
     */
    public static function mail($recipient, $subject, array &$formdata, $mailHandler = null)
    {
        assert('is_string($recipient); // Wrong type for argument 1. String expected');
        assert('is_string($subject); // Wrong type for argument 2. String expected');
        assert('is_null($mailHandler) || is_callable($mailHandler); // Wrong type for argument 3. Function expected');
        assert('!empty($recipient); // Recipient cannot be empty');

        unset($formdata['action']);

        $formMail = new formMailer();
        $formMail->subject = $subject;
        $formMail->content =& $formdata;

        $recipient = untaintInput($recipient, 'mail');

        if (!empty($recipient)) {
            if ($formMail->send($recipient, $mailHandler)) {
                return true;
            } else {
                return false;
            }
        } else {
            trigger_error("Invalid recipient, the provided value is not a valid mail adress.", E_USER_NOTICE);
            return false;
        }
    }

}

?>