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
class NativeStrategy extends \Yana\Mails\Strategies\AbstractStrategy
{

    /**
     * Send an e-mail using PHP's mail() function.
     *
     * @param   string    $recipient  mail address
     * @param   string    $subject    short description
     * @param   string    $text       message text
     * @param   array     $header     (optional)
     * @return  bool
     */
    public function __invoke($recipient, $subject, $text, array $header = array())
    {
        assert('is_string($recipient); // Wrong type for argument 1. String expected');
        assert('is_string($subject); // Wrong type for argument 2. String expected');
        assert('is_string($text); // Wrong type for argument 3. String expected');

        $headerString = $this->_convertHeadersToString($header);

        return (bool) mail($recipient, $subject, $text, $headerString);
    }

}

?>