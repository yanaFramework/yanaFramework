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
 * <<context>> Use this to send mails containing untrusted user input.
 *
 * @package     yana
 * @subpackage  mails
 */
class UserInputContext extends \Yana\Mails\Strategies\Contexts\AbstractContext
{

    /**
     * Send an e-mail.
     *
     * This function sends an e-mail and lets you provide the recipient, subject, text and header.
     *
     * Note that this method introduces several filters to prevent header-injection attacks,
     * that might be used to send spam mails.
     *
     * Due to this feature, this method should be prefered over of PHP's native mail()
     * function in any productive environment.
     *
     * One should note that the 4th parameter, containing the header-information,
     * is an associative array here, rather than a string.
     *
     * This means, you need to write this parameter as follows:
     * <code>
     * $header = array(
     * 'from'         => $senderMail,
     * 'cc'           => $theOtherGuy,
     * 'content-type' => 'text/plain; charset=UTF-8'
     * );
     * $userInputContext($recipient, $subject, $text, $header);
     * // instead of:
     * $header = "from: $senderMail;\n";
     * $header .= "cc: $theOtherGuy;\n";
     * $header .= "content-type: text/plain; charset=UTF-8;\n";
     * mail($recipient, $subject, $text, $header);
     * </code>
     *
     * <ul>
     *
     * <li>
     * Note that the default encoding for mails to send via this method
     * is plain text in UTF-8. You may change this via the $header var 'content-type'.
     * </li>
     *
     * <li>
     * When sending HTML-mails the following tags will automatically be removed for
     * security reasons: link, meta, script, style, img, embed, object, param.
     * In addition any '@' sign within the subject or text of the message will be
     * replaced by the phrase "[at]".
     * As this is a blacklist approach, it is not completely failproof.
     * You are encouraged to prepend additional checks as you see fit, before passing
     * your params to this method.
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
     * See the developer's cookbook for more details.
     *
     * @param  \Yana\Mails\Messages\IsMessage  $message  the mail's properties
     * @return  bool
     * @throws  \Yana\Core\Exceptions\Mails\NotSupportedException      when the strategy is not supported
     * @throws  \Yana\Core\Exceptions\Mails\InvalidMailException  when recipient is no valid e-mail
     * @throws  \Yana\Core\Exceptions\Mails\MissingSubjectException    when the subject is missing
     * @throws  \Yana\Core\Exceptions\Mails\MissingTextException       when the mail text is missing
     */
    public function __invoke(\Yana\Mails\Messages\IsMessage $message)
    {
        $recipient = $message->getRecipient();
        $subject = $message->getSubject();
        $text = $message->getText();
        $headers = $message->getHeaders()->toArray();

        if (!filter_var($recipient, \FILTER_VALIDATE_EMAIL)) {
            $message = "Cannot send an e-mail without a valid recipient.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Mails\InvalidMailException($message, $level);
        }

        $subject = $this->_sanitizeSubject($subject);
        $sanitizedHeaders = $this->_sanitizeHeaders($headers);
        $restrictedHeaders = $this->_restrictHeaders($sanitizedHeaders);
        $completeHeaders = $this->_addDefaultHeaders($restrictedHeaders);
        $sanitizedText = $this->_sanitizeText($text, $completeHeaders['content-type']);

        if (empty($subject)) {
            $message = "Cannot send an e-mail without a subject.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Mails\MissingSubjectException($message, $level);
        }

        if (empty($text)) {
            $message = "Cannot send an e-mail without text.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Mails\MissingTextException($message, $level);
        }

        $mailFunction = $this->_getMailingStrategy();
        return (bool) $mailFunction($recipient, $subject, $sanitizedText, $completeHeaders);
    }

}

?>