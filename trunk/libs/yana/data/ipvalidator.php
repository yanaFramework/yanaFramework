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
 * IP-address validation.
 *
 * @package     yana
 * @subpackage  io
 */
class IpValidator extends AbstractValidator
{

    /**
     * Evaluate if a value is a valid IP-address.
     *
     * @param   mixed  $ip  value to validate
     * @return  bool
     */
    public static function validate($ip)
    {
        return (bool) filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * Sanitize object.
     *
     * Returns NULL for invalid values.
     *
     * @param   mixed  $ip  value to sanitize
     * @return  mixed 
     */
    public function __invoke($ip)
    {
        if (!self::validate($ip)) {
            $ip = null;
        }
        return $ip;
    }

    /**
     * Sanitize object.
     *
     * Returns NULL for invalid objects.
     *
     * @param   mixed  $ip  value to sanitize
     * @return  bool 
     */
    public static function sanitize($ip)
    {
        $validator = new self();
        return $validator($ip);
    }

}

?>