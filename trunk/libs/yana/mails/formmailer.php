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
 * <<facade>> Create and send mails from form data.
 *
 * @package     yana
 * @subpackage  mails
 */
class FormMailer extends \Yana\Mails\AbstractMailerFacade
{

    /**
     * @var \Yana\Mails\Messages\FormMessage
     */
    private $_message = null;

    /**
     * Create new mailer facade.
     *
     * @param  \Yana\Mails\Strategies\Contexts\IsContext  $context  a callable class used to send e-mails
     * @param  \Yana\Mails\Messages\FormMessage           $message  the mail's properties
     */
    public function __construct(?\Yana\Mails\Strategies\Contexts\IsContext $context = null,
        \Yana\Mails\Messages\FormMessage $message = null)
    {
        // @codeCoverageIgnoreStart
        if (is_null($message)) {
            $message = new \Yana\Mails\Messages\FormMessage();
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
     * @param   \Yana\Mails\Messages\FormMessage  $message  the message settings
     * @return  \Yana\Mails\FormMailer
     */
    protected function _setMessage(\Yana\Mails\Messages\FormMessage $message)
    {
        $this->_message = $message;
        return $this;
    }

    /**
     * Returns the message properties.
     *
     * @return  \Yana\Mails\Messages\FormMessage
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
     * @param   array   $formData   some form data
     * @param   array   $headers    additional mail headers
     * @return  bool
     */
    public function send($recipient, $subject, array $formData, array $headers = array())
    {
        $headerCollection = new \Yana\Mails\Headers\MailHeaderCollection();
        $headerCollection->setItems($headers);

        $message = $this->_getMessage();
        $message->composeTextFromFormData($subject, $formData)
            ->setRecipient($recipient)
            ->setSubject($subject)
            ->setHeaders($headerCollection);

        $context = $this->_getContext();
        return $context($message);
    }

}

?>
