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

namespace Yana\Mails\Strategies\Contexts;

/**
 * <<context>> Use this to send mails from trusted sources only.
 *
 * @package     yana
 * @subpackage  mails
 */
class TrustedInputContext extends \Yana\Mails\Strategies\Contexts\AbstractContext
{

    /**
     * Send an e-mail without additional checks.
     *
     * Warning! NEVER use this context for handling untrusted user input.
     *
     * @param  \Yana\Mails\Messages\IsMessage  $message  the mail's properties
     * @return  bool
     * @throws  \Yana\Core\Exceptions\Mails\NotSupportedException      when the strategy is not supported
     * @throws  \Yana\Core\Exceptions\Mails\InvalidRecipientException  when recipient is no valid e-mail
     * @throws  \Yana\Core\Exceptions\Mails\MissingSubjectException    when the subject is missing
     * @throws  \Yana\Core\Exceptions\Mails\MissingTextException       when the mail text is missing
     */
    public function __invoke(\Yana\Mails\Messages\IsMessage $message)
    {
        $recipient = $message->getRecipient();
        $subject = $message->getSubject();
        $text = $message->getText();
        $headers = $message->getHeaders();

        $mailFunction = $this->_getMailingStrategy();
        return (bool) $mailFunction($recipient, $subject, $text, $headers);
    }

}

?>