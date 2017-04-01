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
 * This class uses the basic DES algorithm which is always available but is not very safe and thus unfit for important systems.
 * Don't use it except for testing or in a safe environment where you really don't care about secure passwords.
 *
 * If you find yourself in a situation where you <i>have</i> to use this algorithm in a productive environment because you
 * got no other options, please reconsider your environment rather than using the DES algorithm.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class BasicAlgorithm extends \Yana\Security\Passwords\AbstractAlgorithm
{

    /**
     * Calculate password.
     *
     * This function takes user name and password phrase as clear text and returns the
     * hash-code for this password.
     *
     * @param   string  $password  password (clear text)
     * @return  string
     */
    public function __invoke($password)
    {
        assert('is_scalar($password); // Wrong argument type for argument 2. String expected.');

        return \password_hash($password, \PASSWORD_DEFAULT);
    }

    /**
     * Compare hash with password.
     *
     * Returns bool(true) if the password matches the given hash and bool(false) otherwise.
     *
     * @param   string  $password  password (clear text)
     * @param   string  $hash      hashed password
     * @return  bool
     */
    public function isEqual($password, $hash)
    {
        assert('is_string($password); // Wrong argument type for argument $password. String expected.');
        assert('is_string($hash); // Wrong argument type for argument $hash. String expected.');
        return \password_verify($password, $hash);
    }

}

?>