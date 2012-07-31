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

namespace Yana\Core\Exceptions\Forms;

/**
 * <<exception>> Invalid syntax of form field data.
 *
 * Thrown when a field is provided with data, that does not match a given syntax.
 * E.g. a field of type "date" that does not follow the expected date-format.
 *
 * @package     yana
 * @subpackage  core
 */
class InvalidSyntaxException extends \Yana\Core\Exceptions\Forms\InvalidValueException
{

    /**
     * Set invalid value.
     *
     * @param   mixed $value the invalid value
     * @return  \Yana\Core\Exceptions\Forms\InvalidSyntaxException 
     */
    public function setValue($value)
    {
        $this->data['VALUE'] = print_r($value, true);
        return $this;
    }

    /**
     * Set list of valid characters.
     *
     * @param   string  $valid  list of valid characters
     * @return  \Yana\Core\Exceptions\Forms\InvalidSyntaxException 
     */
    public function setValid($valid)
    {
        assert('is_string($valid); // Invalid argument $valid: string expected');
        $this->data['VALID'] = $valid;
        return $this;
    }

}

?>