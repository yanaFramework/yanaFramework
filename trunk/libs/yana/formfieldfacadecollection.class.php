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

/**
 * <<collection>> of form fields.
 *
 * A field represents an UI input-element inside a form.
 *
 * @access      public
 * @package     yana
 * @subpackage  form
 */
class FormFieldFacadeCollection extends Collection
{

    /**
     * Insert or replace item.
     *
     * @access  public
     * @param   string           $offset  index of item to replace
     * @param   FormFieldFacade  $value   new value of item
     * @throws  InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if ($value instanceof FormFieldFacade) {
            if (!is_string($offset)) {
                $offset = $value->getName();
            }
            parent::offsetSet(mb_strtolower($offset), $value);
        } else {
            $message = "Instance of DDLField expected. Found " . gettype($value) . "(" . get_class($value) . ") instead.";
            throw new InvalidArgumentException($message);
        }
    }

}

?>