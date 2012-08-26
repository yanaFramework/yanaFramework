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
 * <<strategy>> Abstract base class for all mailing strategies.
 *
 * @package     yana
 * @subpackage  mails
 */
abstract class AbstractStrategy extends \Yana\Core\Object implements \Yana\Mails\Strategies\IsStrategy
{

    /**
     * Converts the array to a string.
     * 
     * @param   array  $headers  key-value pairs of mail headers
     * @return  string
     */
    protected function _convertHeadersToString(array $headers)
    {
        $headerString = "";
        $replaceCharacters = array("\n", "\r", "\f", ":");
        foreach ($headers as $key => $string)
        {
            $value = \str_replace($replaceCharacters, "", $string);
            $headerString .= $key . ": " . $value . "\r\n";
        }
        return $headerString;
    }

}

?>