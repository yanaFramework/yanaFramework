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

namespace Yana\Security\Rules;

/**
 * Collection of security rules.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class RuleCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Add a new rule to the collection.
     *
     * @param   scalar                       $offset  rule id
     * @param   \Yana\Security\Rules\IsRule  $value   rule that shoud be added
     * @return  \Yana\Security\Rules\IsRule
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not a mapper
     */
    public function offsetSet($offset, $value)
    {
        assert(is_null($offset) || is_scalar($offset), '$offset expected to be Scalar');
        if (!$value instanceof \Yana\Security\Rules\IsRule) {
            $message = "Instance of \Yana\Security\Rules\IsRule expected. " .
                "Found " . gettype($value) . "(" . get_class($value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        return $this->_offsetSet($offset, $value);
    }

}

?>