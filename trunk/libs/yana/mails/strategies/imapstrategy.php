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

namespace Yana\Mails\Strategies;

/**
 * <<strategy>> PHP's native mailing strategy.
 *
 * @package     yana
 * @subpackage  mails
 */
class ImapStrategy extends \Yana\Mails\Strategies\AbstractStrategy
{

    /**
     * Send an e-mail using the IMAP method.
     *
     * @param   string    $recipient  mail address
     * @param   string    $subject    short description
     * @param   string    $text       message text
     * @param   array     $header     (optional)
     * @return  bool
     * @throws  \Yana\Core\Exceptions\Mails\NotSupportedException  when IMAP mail is not installed
     */
    public function __invoke($recipient, $subject, $text, array $header = array())
    {
        assert('is_string($recipient)', ' Wrong type for argument 1. String expected');
        assert('is_string($subject)', ' Wrong type for argument 2. String expected');
        assert('is_string($text)', ' Wrong type for argument 3. String expected');

        if (!\function_exists('imap_mail')) {
            $message = "IMAP mail module is not installed. See PHP manual for details.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\Mails\NotSupportedException($message, $level);
        }
        $headerString = $this->_convertHeadersToString($header);

        return (bool) !empty($recipient) && imap_mail($recipient, $subject, $text, $headerString);
    }

}

?>