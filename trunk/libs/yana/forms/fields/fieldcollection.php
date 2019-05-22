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

namespace Yana\Forms\Fields;

/**
 * <<collection>> of form fields.
 *
 * A field represents an UI input-element inside a form.
 *
 * @package     yana
 * @subpackage  form
 */
class FieldCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Insert or replace item.
     *
     * @param   string                      $offset  index of item to replace
     * @param   \Yana\Forms\Fields\IsField  $value   new value of item
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given value is not valid
     * @return  \Yana\Forms\Fields\IsField
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof \Yana\Forms\Fields\IsField) {
            $message = "Instance of IsFacade expected. Found " . gettype($value) . "(" .
                ((is_object($value)) ? get_class($value) : $value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        if (!is_string($offset)) {
            $offset = $value->getName();
        }
        return $this->_offsetSet(mb_strtolower($offset), $value);
    }

}

?>