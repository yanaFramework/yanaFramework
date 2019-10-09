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

namespace Yana\Plugins\Data;

/**
 * <<collection>> Collection of entities.
 *
 * @package     yana
 * @subpackage  plugins
 */
class Collection extends \Yana\Core\AbstractCollection
{

    /**
     * Replaces the given offset and returns the value.
     *
     * @param   string                       $offset  plugin name
     * @param   \Yana\Plugins\Data\IsEntity  $value   plugin instance
     * @return  \Yana\Plugins\Data\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid collection item
     */
    public function offsetSet($offset, $value)
    {
        assert(is_null($offset) || is_string($offset), '$offset expected to be String');
        if (!$value instanceof \Yana\Plugins\Data\IsEntity) {
            $message = "Instance of \Yana\Plugins\Data\IsEntity expected.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        if (is_null($offset)) {
            $offset = $value->getId();
        }
        return $this->_offsetSet($offset, $value);
    }
    
}

?>