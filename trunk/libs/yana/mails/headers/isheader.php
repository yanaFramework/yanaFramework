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
declare(strict_types=1);

namespace Yana\Mails\Headers;

/**
 * <<entity>> The method to send an e-mail.
 *
 * Implements the strategy pattern.
 *
 * @package     yana
 * @subpackage  mails
 */
interface IsHeader extends \Yana\Core\IsCollection
{

    /**
     * Sets the priority of the message to "highest".
     *
     * @return  $this
     */
    public function setHighPriority();

    /**
     * Sets the priority of the message to "normal".
     *
     * @return  $this
     */
    public function setNormalPriority();

    /**
     * Sets the priority of the message to "lowest".
     *
     * @return  $this
     */
    public function setLowPriority();

    /**
     * Returns true if the message has high priority.
     *
     * @return  bool
     */
    public function isHighPriority(): bool;

    /**
     * Returns true if the message has normal priority.
     *
     * This is the default.
     *
     * @return  bool
     */
    public function isNormalPriority(): bool;

    /**
     * Returns true if the message has low priority.
     *
     * @return  bool
     */
    public function isLowPriority(): bool;

    /**
     * Set the addresses to include, when the recipient replies to your mail.
     *
     * @param   array  $mails  some mail addresses
     * @return  $this
     */
    public function setReplyAddresses(array $mails);

    /**
     * Returns the addresses to reply to.
     *
     * @return  array
     */
    public function getReplyAddresses(): array;

    /**
     * Set the address of the mail sender.
     *
     * Be warned that some e-mail providers may check if the address given here exists
     * and will not deliver any e-mail in case it doesn't.
     *
     * @param   string  $mails  some mail address
     * @return  $this
     */
    public function setFromAddress(string $mails);

    /**
     * Returns the address of the mail's sender.
     *
     * @return  string
     */
    public function getFromAddress(): string;

    /**
     * Set additional recipients.
     *
     * @param   array  $mails  some mail addresses
     * @return  $this
     */
    public function setCcAddresses(array $mails);

    /**
     * Returns the addresses of the mail's additional recipients.
     *
     * @return  array
     */
    public function getCcAddresses(): array;

    /**
     * Set additional (hidden) recipients.
     *
     * The "blind-carbon-copy" (BCC) recipients will not show up as recipients to
     * other recipients of that mail.
     *
     * @param   array  $mails  some mail addresses
     * @return  $this
     */
    public function setBccAddresses(array $mails);

    /**
     * Get additional (hidden) recipients.
     *
     * @return  array
     */
    public function getBccAddresses(): array;

    /**
     * Set content type to HTML.
     *
     * @return  $this
     */
    public function setAsHtml();

    /**
     * Returns true if content type to HTML.
     *
     * @return  bool
     */
    public function isHtml(): bool;

    /**
     * Set content type to HTML.
     *
     * @return  $this
     */
    public function setAsPlainText();

    /**
     * Returns true if content type to text.
     *
     * This is the default.
     *
     * @return  bool
     */
    public function isPlainText(): bool;

}

?>