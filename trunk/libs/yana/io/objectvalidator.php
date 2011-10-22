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

namespace Yana\Io;

/**
 * Object validation base class.
 *
 * All validators should extend this class.
 *
 * @package     yana
 * @subpackage  io
 */
class ObjectValidator extends AbstractValidator
{

    /**
     * Validate if a value is an object.
     *
     * @param   mixed  $object  value to validate
     * @return  bool
     */
    public static function validate($object)
    {
        return is_object($object);
    }

    /**
     * Sanitize object.
     *
     * Returns NULL for invalid objects.
     *
     * @param   mixed  $object  value to sanitize
     * @return  mixed 
     */
    public function __invoke($object)
    {
        if (!is_object($object)) {
            $object = null;
        }
        return $object;
    }

    /**
     * Sanitize object.
     *
     * Returns NULL for invalid objects.
     *
     * @param   mixed  $object  value to sanitize
     * @return  mixed 
     */
    public static function sanitize($object)
    {
        $validator = new self();
        return $validator($object);
    }

}

?>