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

namespace Yana\Mails\Headers;

/**
 * <<strategy>> Abstract base class for all mailing strategies.
 *
 * @package     yana
 * @subpackage  mails
 */
class MailHeaderCollection extends \Yana\Core\AbstractCollection implements \Yana\Mails\Headers\IsHeader
{

    /**
     * Insert or replace header value.
     *
     * The header keys will be lower-cased.
     *
     * @param   string  $offset  header key
     * @param   mixed   $value   header value(s)
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the offset is not a valid header key
     * @return  string
     */
    public function offsetSet($offset, $value)
    {
        if (!\is_string($offset) || empty($offset)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException();
        }
        $offset = mb_strtolower((string) $offset);
        parent::_offsetSet($offset, $value);
        return $value;
    }

    /**
     * Sets the priority of the message to "highest".
     *
     * @return  \Yana\Mails\Headers\IsHeader
     */
    public function setHighPriority()
    {
        $this['x-priority'] = '1 (Highest)'; // Proprietary
        $this['importance'] = 'High'; // RFC 4021
        return $this;
    }

    /**
     * Sets the priority of the message to "normal".
     *
     * @return  \Yana\Mails\Headers\IsHeader
     */
    public function setNormalPriority()
    {
        $this['x-priority'] = '3 (Normal)'; // Proprietary
        $this['importance'] = 'Normal'; // RFC 4021
        return $this;
    }

    /**
     * Sets the priority of the message to "lowest".
     *
     * @return  \Yana\Mails\Headers\IsHeader
     */
    public function setLowPriority()
    {
        $this['x-priority'] = '5 (Lowest)'; // Proprietary
        $this['importance'] = 'Low'; // RFC 4021
        return $this;
    }

    /**
     * Returns priority as integer.
     *
     * Result may be -1: low, 0: normal, 1: high.
     * Default is 0.
     *
     * @return  int
     */
    protected function _getPriority()
    {
        $priority = 0;
        if (isset($this['importance'])) {
            switch (strtolower($this['importance']))
            {
                case 'low':
                    $priority = -1;
                    break;
                case 'high':
                    $priority = 1;
                    break;
            }
        }
        if (!$priority && isset($this['x-priority'])) {
            switch (\filter_var($this['x-priority'], \FILTER_SANITIZE_NUMBER_INT))
            {
                case 1:
                case 2:
                    $priority = 1;
                    break;
                case 3:
                    $priority = 0;
                    break;
                case 4:
                case 5:
                    $priority = -1;
                    break;
            }
        }
        return $priority;
    }

    /**
     * Returns true if the message has high priority.
     *
     * @return  bool
     */
    public function isHighPriority()
    {
        return $this->_getPriority() > 0;
    }

    /**
     * Returns true if the message has normal priority.
     *
     * This is the default.
     *
     * @return  bool
     */
    public function isNormalPriority()
    {
        return $this->_getPriority() === 0;
    }

    /**
     * Returns true if the message has low priority.
     *
     * @return  bool
     */
    public function isLowPriority()
    {
        return $this->_getPriority() < 0;
    }

    /**
     * Set the addresses to include, when the recipient replies to your mail.
     *
     * @param   array  $mails  some mail addresses
     * @return  \Yana\Mails\Headers\IsHeader
     */
    public function setReplyAddresses(array $mails)
    {
        $this['reply-to'] = $mails;
        return $this;
    }

    /**
     * Returns the addresses to reply to.
     *
     * @return  array
     */
    public function getReplyAddresses()
    {
        return (array) $this['reply-to'];
    }

    /**
     * Set the address of the mail sender.
     *
     * Be warned that some e-mail providers may check if the address given here exists
     * and will not deliver any e-mail in case it doesn't.
     *
     * @param   string  $mail  some mail address
     * @return  \Yana\Mails\Headers\IsHeader
     */
    public function setFromAddress($mail)
    {
        $this['from'] = $mail;
        return $this;
    }

    /**
     * Returns the address of the mail's sender.
     *
     * @return  string
     */
    public function getFromAddress()
    {
        return (string) $this['from'];
    }

    /**
     * Set additional recipients.
     *
     * @param   array  $mails  some mail addresses
     * @return  \Yana\Mails\Headers\IsHeader
     */
    public function setCcAddresses(array $mails)
    {
        $this['cc'] = $mails;
        return $this;
    }

    /**
     * Returns the addresses of the mail's additional recipients.
     *
     * @return  array
     */
    public function getCcAddresses()
    {
        return (array) $this['cc'];
    }

    /**
     * Set additional (hidden) recipients.
     *
     * The "blind-carbon-copy" (BCC) recipients will not show up as recipients to
     * other recipients of that mail.
     *
     * @param  array  $mails  some mail addresses
     * @return  \Yana\Mails\Headers\IsHeader
     */
    public function setBccAddresses(array $mails)
    {
        $this['bcc'] = $mails;
        return $this;
    }

    /**
     * Get additional (hidden) recipients.
     *
     * @return  array
     */
    public function getBccAddresses()
    {
        return (array) $this['bcc'];
    }

    /**
     * Set content type to HTML.
     *
     * @return  \Yana\Mails\Headers\MailHeader
     */
    public function setAsHtml()
    {
        $this['content-type'] = 'text/html; charset=UTF-8';
        return $this;
    }

    /**
     * Returns true if content type to HTML.
     *
     * @return  bool
     */
    public function isHtml()
    {
        $isHtml = isset($this['content-type']) && preg_match('/^text\/html/i', $this['content-type']);
        return $isHtml;
    }

    /**
     * Set content type to HTML.
     *
     * @return  \Yana\Mails\Headers\MailHeader
     */
    public function setAsPlainText()
    {
        $this['content-type'] = 'text/plain; charset=UTF-8';
        return $this;
    }

    /**
     * Returns true if content type to text.
     *
     * This is the default.
     *
     * @return  bool
     */
    public function isPlainText()
    {
        $isText = !isset($this['content-type']) || preg_match('/^text\/plain/i', $this['content-type']);
        return $isText;
    }

}

?>