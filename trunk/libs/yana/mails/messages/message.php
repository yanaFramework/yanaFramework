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

namespace Yana\Mails\Messages;

/**
 * <<entity>> Base class implementing plain, simple mail messages.
 *
 * @package     yana
 * @subpackage  mails
 */
class Message extends \Yana\Core\Object implements \Yana\Mails\Messages\IsMessage
{

    /**
     * @var  string
     */
    private $_subject = "";

    /**
     * @var  string
     */
    private $_recipient = "";

    /**
     * @var  string
     */
    private $_text = "";

    /**
     * @var  array
     */
    private $_headers = array();

    /**
     * Get the currently selected additional headers.
     *
     * @return  array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Set additional headers.
     *
     * @param   array  $headers  key is header name, value is header value
     * @return  \Yana\Mails\Messages\IsMessage
     */
    public function setHeaders(array $headers)
    {
        $this->_headers = $headers;
        return $this;
    }

    /**
     * Get the subject line of the e-mail.
     *
     * @return  string
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Set the subject line of the e-mail.
     *
     * @param   string  $subject  one line of plain text
     * @return  \Yana\Mails\Messages\IsMessage
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * Get the recipient's e-mail.
     *
     * @return  string
     */
    public function getRecipient()
    {
        return $this->_recipient;
    }

    /**
     * Set the recipient's e-mail.
     *
     * @param   string  $recipient  valid e-mail address
     * @return  \Yana\Mails\Messages\IsMessage
     */
    public function setRecipient($recipient)
    {
        $this->_recipient = $recipient;
        return $this;
    }

    
    /**
     * Get the e-mail's body text.
     *
     * @return  string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Set the e-mail's body text.
     *
     * @param   string  $text  some mail text
     * @return  \Yana\Mails\Messages\IsMessage
     */
    public function setText($text)
    {
        $this->_text = $text;
        return $this;
    }

}

?>