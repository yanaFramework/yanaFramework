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
 * <<strategy>> The NullMailer.
 *
 * Also known as the infamous "FlatRatsMailer".
 *
 * @package     yana
 * @subpackage  mails
 */
class NullStrategy extends \Yana\Mails\Strategies\AbstractStrategy
{

    /**
     * Mail buffer for unit tests.
     *
     * @var  array
     */
    private $_mails = array();

    /**
     * Play dead.
     *
     * @param   string  $recipient  mail address
     * @param   string  $subject    short description
     * @param   string  $text       message text
     * @param   array   $headers    additional headers
     * @return  bool
     */
    public function __invoke($recipient, $subject, $text, array $headers = array())
    {
        $this->_mails[] = array($recipient, $subject, $text, $headers);
        return true;
    }

    /**
     * Retrieve last mails sent.
     *
     * @return  array
     */
    public function getMails()
    {
        return $this->_mails;
    }
}

?>