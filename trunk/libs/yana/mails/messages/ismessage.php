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
 * <<interface>> Mailer interface.
 *
 * @package     yana
 * @subpackage  mails
 */
interface IsMessage
{

    /**
     * Get the currently selected additional headers.
     *
     * @return  \Yana\Mails\Headers\IsHeader
     */
    public function getHeaders();

    /**
     * Set additional headers.
     *
     * @param   \Yana\Mails\Headers\IsHeader  $headers  key is header name, value is header value
     * @return  \Yana\Mails\Messages\IsMessage
     */
    public function setHeaders(\Yana\Mails\Headers\IsHeader $headers);

    /**
     * Get the subject line of the e-mail.
     *
     * @return  string
     */
    public function getSubject();

    /**
     * Set the subject line of the e-mail.
     *
     * @param   string  $subject  one line of plain text
     * @return  \Yana\Mails\Messages\IsMessage
     */
    public function setSubject($subject);

    /**
     * Get the recipient's e-mail.
     *
     * @return  string
     */
    public function getRecipient();

    /**
     * Set the recipient's e-mail.
     *
     * @param   string  $recipient  valid e-mail address
     * @return  \Yana\Mails\Messages\IsMessage
     */
    public function setRecipient($recipient);

    /**
     * Get the e-mail's body text.
     *
     * @return  string
     */
    public function getText();

    /**
     * Set the e-mail's body text.
     *
     * @param   string  $text  some mail text
     * @return  \Yana\Mails\Messages\IsMessage
     */
    public function setText($text);

}

?>