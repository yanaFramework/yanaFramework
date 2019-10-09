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
class Sha512Algorithm extends \Yana\Security\Passwords\AbstractCryptAlgorithm
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
        assert(is_string($password), 'Wrong argument type for argument $password. String expected.');

        if (CRYPT_SHA512 === 1) {
            $hashString = crypt($password, '$6$rounds=5000$' . $this->_createSalt() . '$');
        } else {
            // @codeCoverageIgnoreStart
            $hashString = $this->_getFallback()->__invoke($password);
            // @codeCoverageIgnoreEnd
        }

        return $hashString;
    }

}

?>