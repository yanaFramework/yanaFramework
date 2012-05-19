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

namespace Yana\Data;

/**
 * Bool-values validation class.
 *
 * @package     yana
 * @subpackage  io
 */
class BooleanValidator extends AbstractValidator
{

    /**
     * Validate if a value is a boolean.
     *
     * @param   mixed  $object  value to validate
     * @return  bool
     */
    public static function validate($object)
    {
        return is_bool($object);
    }

    /**
     * Sanitize object.
     *
     * Returns NULL for invalid objects.
     *
     * @param   mixed  $boolean  value to sanitize
     * @return  bool 
     */
    public function __invoke($boolean)
    {
        return !empty($boolean);
    }

    /**
     * Sanitize object.
     *
     * Returns NULL for invalid objects.
     *
     * @param   mixed  $boolean  value to sanitize
     * @return  bool 
     */
    public static function sanitize($boolean)
    {
        $validator = new self();
        return $validator($boolean);
    }

}

?>