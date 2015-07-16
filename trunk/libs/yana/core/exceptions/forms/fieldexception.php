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
 * <<exception>> Invalid form field data.
 *
 * Base class, used when any input for a given form field is invalid.
 *
 * @package     yana
 * @subpackage  core
 */
class FieldException extends \Yana\Core\Exceptions\Forms\FormException
{

    /**
     * Set field name.
     *
     * @param   string  $fieldName  Field that contained the invalid value
     * @return  \Yana\Core\Exceptions\Forms\FieldException 
     */
    public function setField($fieldName)
    {
        assert('is_string($fieldName); // Invalid argument $fieldName: string expected');
        $this->data['FIELD'] = $fieldName;
        return $this;
    }

}

?>