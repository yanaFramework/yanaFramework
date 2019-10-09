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
 * <<entity>> Class for composing mail messages based on form data.
 *
 * @package     yana
 * @subpackage  mails
 */
class FormMessage extends \Yana\Mails\Messages\Message
{

    /**
     * Set the subject line of the e-mail.
     *
     * Adds the prefix "[MAILFORM] " to the subject.
     *
     * @param   string  $subject  one line of plain text
     * @return  \Yana\Mails\Messages\IsMessage
     */
    public function setSubject($subject)
    {
        $subject = "[MAILFORM] " . $subject;
        return parent::setSubject($subject);
    }

    /**
     * Composes the message text based on some given form data.
     *
     * Note: Mail content-type will be text/plain.
     *
     * @param  string  $formName  name of the form to be used in headline
     * @param  array   $formData  some form data
     * @return \Yana\Mails\Messages\FormMessage
     */
    public function composeTextFromFormData($formName, array $formData)
    {
        assert(is_string($formName), 'Invalid argument $formName: string expected');

        $mailText = "==========================================\n";
        $mailText .= "  " . $formName . "\n";
        $mailText .= "==========================================\n\n";

        foreach ($formData as $key => $value)
        {
            assert(is_scalar($value) || is_array($value), 'form value has unexpected type');
            $sanitizedKey = \preg_replace('/[^\w\döäüß\/\-\. ]/i', '_', $key);
            $readableValue = \print_r($value, true);
            $mailText .= "    " . $sanitizedKey . ":\t" . $readableValue . "\n";
        }
        unset($key, $value);

        $ipAddress = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'not available';

        $mailText .= "\n==========================================\n";
        $mailText .= "Date:\t" . date("c", time()) . "\n";
        $mailText .= "IP:\t" . $ipAddress . "\n";

        $this->setText($mailText);
        return $this;
    }

}

?>