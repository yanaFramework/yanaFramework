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
 *
 * @ignore
 */

namespace Yana\Views\Icons;

/**
 * <<collection>> Of icon files.
 *
 * @package     yana
 * @subpackage  views
 */
class Collection extends \Yana\Core\AbstractCollection
{

    /**
     * Add a new rule to the collection.
     *
     * @param   scalar                    $offset  file id
     * @param   \Yana\Views\Icons\IsFile  $value   entity that should be added
     * @return  \Yana\Views\Icons\IsFile
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not a valid entity
     */
    public function offsetSet($offset, $value)
    {
        assert(is_null($offset) || is_scalar($offset), '$offset expected to be Scalar');
        if (!$value instanceof \Yana\Views\Icons\IsFile) {
            $message = "Instance of \Yana\Views\Icons\IsFile expected. " .
                "Found " . gettype($value) . "(" . get_class($value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        if (\is_null($offset)) {
            $offset = $value->getId();
        }
        return $this->_offsetSet($offset, $value);
    }

}

?>