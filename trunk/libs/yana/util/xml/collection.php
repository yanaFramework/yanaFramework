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

namespace Yana\Util\Xml;

/**
 * <<collection>> List of XML-objects.
 *
 * @package    yana
 * @subpackage core
 * @ignore
 */
class Collection extends \Yana\Core\AbstractCollection
{

    /**
     * Replaces the given offset.
     *
     * Returns the value.
     *
     * @param   scalar                           $offset  order number (usually blank)
     * @param   scalar|\Yana\Util\Xml\IsObject   $value   node
     * @return  \Yana\Util\Xml\IsObject
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid collection item
     */
    public function offsetSet($offset, $value)
    {
        if (\is_scalar($value)) {
            $value = new \Yana\Util\Xml\Object((string) $value);
        }
        assert('is_null($offset) || is_scalar($offset); // $offset expected to be Scalar');
        if (!$value instanceof \Yana\Util\Xml\IsObject) {
            $message = "Instance of \Yana\Util\Xml\IsObject expected. " .
                "Found " . gettype($value) . "(" . get_class($value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        return $this->_offsetSet($offset, $value);
    }

}

?>