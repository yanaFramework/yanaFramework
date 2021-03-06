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

namespace Yana\Core\Exceptions;

/**
 * <<collection>> Stores exceptions for later use.
 *
 * @package    yana
 * @subpackage core
 */
class ExceptionCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Store new value in collection.
     *
     * @param   scalar      $offset  where to place the value (may also be empty)
     * @param   \Exception  $value   new value to store
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not valid
     * @return  \Yana\Core\Exceptions\ExceptionCollection
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof \Exception) {
            $message = "Exception expected. Found " . gettype($value) . "(" .
                ((is_object($value)) ? get_class($value) : $value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        return $this->_offsetSet($offset, $value);
    }

}