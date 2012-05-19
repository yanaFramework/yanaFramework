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

namespace Yana\Mails;

/**
 * create and send mails from form data
 *
 * @package     yana
 * @subpackage  mails
 */
class FormMailer extends \Yana\Core\Object
{

    /**
     * Subject line of mail to be send.
     *
     * @var  string
     */
    private $_subject = "";

    /**
     * Line of text before content.
     *
     * @var  string
     */
    private $_headline = "";

    /**
     * Line of text after content.
     *
     * @var  string
     */
    private $_footline = "";

    /**
     * The form content.
     *
     * @var  string
     */
    private $_content  = array();

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
     * @return  \Yana\Mails\FormMailer
     */
    public function setSubject($subject)
    {
        assert('is_string($subject); // Invalid argument $subject: string expected');
        $this->_subject = $subject;
        return $this;
    }

    /**
     * Get line of text before content.
     *
     * @return  string
     */
    public function getHeadline()
    {
        return $this->_headline;
    }

    /**
     * Set line of text before content.
     *
     * @param   string  $headline  single-line of text
     * @return  \Yana\Mails\FormMailer
     */
    public function setHeadline($headline)
    {
        assert('is_string($headline); // Invalid argument $headline: string expected');
        $this->_headline = $headline;
        return $this;
    }

    /**
     * Get line of text after content.
     *
     * @return  string
     */
    public function getFootline()
    {
        return $this->_footline;
    }

    /**
     * Set line of text after content.
     *
     * @param   string  $footline  single-line of text
     * @return  \Yana\Mails\FormMailer
     */
    public function setFootline($footline)
    {
        assert('is_string($footline); // Invalid argument $footline: string expected');
        $this->_footline = $footline;
        return $this;
    }

    /**
     * Get form content.
     *
     * @return  array
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Set form content.
     *
     * @param   string  $content  list of key-value pairs, where keys are field-names
     * @return  \Yana\Mails\FormMailer
     */
    public function setContent(array $content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Send an e-mail with the current contents to a recipient.
     *
     * Returns bool(true) on success or bool(false) on error.
     *
     * @param   string    $recipient    mail address
     * @param   function  $mailHandler  function to handle mails
     * @return  bool
     */
    public function send($recipient, $mailHandler = null)
    {
        assert('is_string($recipient); // Invalid argument $recipient: string expected');
        assert('is_null($mailHandler) || is_callable($mailHandler); // Invalid argument $mailHandler: Function expected');
        assert('!empty($recipient); // Recipient cannot be empty');

        $test = false;
        if (count($this->_content) > 0 && !empty($recipient)) {

            /* untaint subject */
            assert('is_string($this->_subject);');
            $this->_subject = \Yana\Data\StringValidator::sanitize($this->_subject, 128, \Yana\Data\StringValidator::LINEBREAK);
            $this->_subject = strip_tags($this->_subject);
            $this->_subject = preg_replace("/[^\w \(\)äÄüÜöÖß]/", "", $this->_subject);
            /* untaint send mail */
            $subject = "[MAILFORM] " . $this->_subject;
            $test = \Yana\Mails\Mailer::mail((string) $recipient, $subject, $this->_makeMail(), array(), $mailHandler);
        }
        assert('is_bool($test); // Unexpected result: $test. Boolean expected.');
        return $test;
    }

    /**
     * Make mail.
     *
     * @return  string
     */
    private function _makeMail()
    {
        assert('is_string($this->_headline);');
        $mail  = $this->_headline;

        $mail .= "==========================================\n";
        assert('is_string($this->_subject);');
        if (!empty($this->_subject)) {
            $mail .= "  " . $this->_subject . "\n";
            $mail .= "==========================================\n\n";
        }

        assert('is_array($this->_content);');
        /* settype to ARRAY */
        $this->_content = (array) $this->_content;
        assert('!isset($a); /* cannot redeclare variable $a */');
        assert('!isset($b); /* cannot redeclare variable $b */');
        foreach ($this->_content as $a => $b)
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

        assert('is_string($this->_footline);');
        $mail .= $this->_footline;

        return $mail;
    }

}

?>