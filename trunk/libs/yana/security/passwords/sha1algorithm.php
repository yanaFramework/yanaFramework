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

namespace Yana\Security\Passwords;

/**
 * Password hashing algorithm.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Sha1Algorithm extends \Yana\Security\Users\Md5Algorithm
{

    /**
     * calculate password
     *
     * This function takes user name and password phrase as clear text and returns the
     * hash-code for this password.
     *
     * @param   string  $userName   user name
     * @param   string  $password   password (clear text)
     * @return  string
     */
    public function __invoke($userName, $password)
    {
        assert('is_scalar($userName); // Wrong argument type for argument 1. String expected.');
        assert('is_scalar($password); // Wrong argument type for argument 2. String expected.');

        $hashString = "";
        if (function_exists('sha1')) {
            $salt = mb_substr(mb_strtoupper("$userName"), 0, 2);
            $string = "{$salt}{$password}";
            $hashString = sha1($string);

        } else {
            $hashString = parent::__invoke($userName, $password);
        }

        return $hashString;
    }

}

?>