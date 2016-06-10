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

namespace Yana\Security\Passwords\Generators;

/**
 * Password generator.
 *
 * Generates a random password.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class StandardAlgorithm extends \Yana\Security\Passwords\Generators\AbstractAlgorithm
{

    /**
     * Generate a random password.
     *
     * @param   int  $length  must be 8 or greater
     * @return  string
     */
    public function __invoke($length = 8)
    {
        assert('is_int($length); // Wrong argument type: $length. Integer expected');
        assert('$length >= 8;  Invalid argument value: $length. Must be 8 or greater');
        return substr(md5(uniqid()), 0, $length >= 8 ? (int) $length : 8);
    }

}

?>