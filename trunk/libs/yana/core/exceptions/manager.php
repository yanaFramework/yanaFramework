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

namespace Yana\Core\Exceptions;

/**
 * Manager class meant to track, hold and inform about caught exceptions.
 *
 * @package     yana
 * @subpackage  core
 * @ignore
 */
class Manager extends \Yana\Core\AbstractCollection
{

    /**
     * Insert or replace item.
     *
     * @param   scalar  $offset  index of item to replace
     * @param   mixed   $value   new value of item
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid collection item
     * @return  \Exception
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof \Exception) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Exception expected.");
        }
        return parent::_offsetSet($offset, $value);
    }

    /**
     * Returns a result type.
     *
     * Scans and checks all registered exception to find wether the last
     * result was a success or an error.
     *
     * @return  string
     */
    public function getResultType()
    {
        $messageClass = ""; // None
        foreach ($this->toArray() as $message)
        {
            switch ($message->getCode())
            {
                case \E_USER_ERROR:
                case \E_ERROR:
                    $messageClass = \Yana\Core\Exceptions\ResultTypeEnumeration::ERROR;
                    break 2; // Abort
                case \E_USER_WARNING:
                case \E_WARNING:
                    $messageClass = \Yana\Core\Exceptions\ResultTypeEnumeration::WARNING;
                    break;
                case \E_USER_NONE:
                    $messageClass = \Yana\Core\Exceptions\ResultTypeEnumeration::MESSAGE;
                    break 2; // Abort
                case \E_NOTICE:
                case \E_USER_DEPRECATED:
                case \E_USER_NOTICE:
                default:
                    $messageClass = \Yana\Core\Exceptions\ResultTypeEnumeration::ALERT;
            }
        }
        return $messageClass;
    }

}

?>