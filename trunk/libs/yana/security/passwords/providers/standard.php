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
declare(strict_types=1);

namespace Yana\Security\Passwords\Providers;

/**
 * Standard authentication provider to check passwords.
 *
 * @package     yana
 * @subpackage  security
 */
class Standard extends \Yana\Security\Passwords\Providers\AbstractProvider implements \Yana\Security\Passwords\Providers\IsAuthenticationProvider
{

    /**
     * @var  \Yana\Security\Passwords\IsAlgorithm
     */
    private $_algorithm = null;

    /**
     * Initialize dependencies.
     *
     * @param  \Yana\Security\Passwords\Generators\IsAlgorithm  $generator  to generade new random passwords
     */
    /**
     * <<construct>> Initialize user entity.
     *
     * @param  \Yana\Security\Passwords\IsAlgorithm  $algorithm  to encode and compare passwords
     */
    public function __construct(\Yana\Security\Passwords\IsAlgorithm $algorithm)
    {
        $this->_algorithm = $algorithm;
    }

    /**
     * Returns password calculation algorithm.
     *
     * @return  \Yana\Security\Passwords\IsAlgorithm
     */
    protected function _getAlgorithm()
    {
        return $this->_algorithm;
    }

    /**
     * Returns TRUE if the provider supports changing passwords.
     *
     * @return  bool
     */
    public function isAbleToChangePassword(): bool
    {
        return true;
    }

    /**
     * Update login password.
     *
     * @param  \Yana\Security\Data\Users\IsEntity  $user         holds password information
     * @param  string                              $newPassword  new user password
     */
    public function changePassword(\Yana\Security\Data\Users\IsEntity $user, string $newPassword)
    {
        if ($this->isAbleToChangePassword($user)) {
            // calculate the hash-value for the new password
            $passwordHash = $this->_getAlgorithm()->__invoke($newPassword);

            // add the hash to the list of recently used passwords
            $recentPasswords = $user->getRecentPasswords();
            $recentPasswords[] = $passwordHash;
            // cut the list back to 10 passwords if necessary
            if (count($recentPasswords) > 10) {
                $recentPasswords = \array_slice($recentPasswords, 1, 10);
            }

            // update the user entity
            $user
                // replace password hash
                ->setPassword($passwordHash)
                ->setPasswordChangedTime(time())
                // update list of recently used passwords
                ->setRecentPasswords($recentPasswords)
                ->saveEntity();
        }
    }

    /**
     * Compare password with password of current user.
     *
     * Returns bool(true) if the password is correct an bool(false) otherwise.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user      holds password information
     * @param   string                              $password  user password
     * @return  bool
     */
    public function checkPassword(\Yana\Security\Data\Users\IsEntity $user, string $password): bool
    {
        return $this->_getAlgorithm()->isEqual($password, (string) $user->getPassword());
    }

}

?>