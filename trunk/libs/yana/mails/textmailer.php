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
 * Create and send mails based on templates.
 *
 * @package     yana
 * @subpackage  mails
 */
class TextMailer extends \Yana\Mails\AbstractMailerFacade
{

    /**
     * @var \Yana\Mails\Messages\IsMessage
     */
    private $_message = null;

    /**
     * Create new mailer facade.
     *
     * @param  \Yana\Mails\Strategies\Contexts\IsContext  $context  a callable class used to send e-mails
     * @param  \Yana\Mails\Messages\IsMessage             $message  the mail's properties
     */
    public function __construct(?\Yana\Mails\Strategies\Contexts\IsContext $context = null,
        \Yana\Mails\Messages\IsMessage $message = null)
    {
        // @codeCoverageIgnoreStart
        if (is_null($message)) {
            $message = new \Yana\Mails\Messages\Message();
        }
        if (is_null($context)) {
            $strategy = new \Yana\Mails\Strategies\NativeStrategy();
            $context = new \Yana\Mails\Strategies\Contexts\UserInputContext($strategy);
        }
        // @codeCoverageIgnoreEnd
        $this->_setMessage($message);
        $this->_setContext($context);
    }

    /**
     * Set up the message properties.
     *
     * @param   \Yana\Mails\Messages\IsMessage  $message  the message settings
     * @return  \Yana\Mails\FormMailer
     */
    protected function _setMessage(\Yana\Mails\Messages\IsMessage $message)
    {
        $this->_message = $message;
        return $this;
    }

    /**
     * Returns the message properties.
     *
     * @return  \Yana\Mails\Messages\IsMessage
     */
    protected function _getMessage()
    {
        return $this->_message;
    }

    /**
     * Send an e-mail to a recipient, using the properties provided.
     *
     * @param   string  $recipient  mail address
     * @param   string  $subject    one line of short description
     * @param   string  $text       some mail text
     * @param   array   $headers    additional mail headers
     * @return  bool
     */
    public function send($recipient, $subject, $text, array $headers = array())
    {
        $headerCollection = new \Yana\Mails\Headers\MailHeaderCollection();
        $headerCollection->setItems($headers);

        $message = $this->_getMessage();
        $message->setRecipient($recipient)
            ->setSubject($subject)
            ->setText($text)
            ->setHeaders($headerCollection);

        $context = $this->_getContext();
        return $context($message);
    }

}

?>
