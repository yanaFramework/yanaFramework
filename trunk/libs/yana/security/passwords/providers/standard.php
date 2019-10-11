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

namespace Yana\Security\Passwords\Providers;

/**
 * Standard authentication provider to check passwords.
 *
 * @package     yana
 * @subpackage  security
 */
class Standard extends \Yana\Security\Passwords\Providers\AbstractProvider implements \Yana\Security\Passwords\Providers\IsProvider
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
     * @param  \Yana\Security\Data\Users\IsEntity    $user  from database
     * @param  \Yana\Security\Passwords\IsAlgorithm  $algorithm  to encode and compare passwords
     */
    public function __construct(\Yana\Security\Data\Users\IsEntity $user, \Yana\Security\Passwords\IsAlgorithm $algorithm)
    {
        parent::__construct($user);
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
     * Returns TRUE for active users and FALSE for inactive users.
     *
     * @return  bool
     */
    public function isAbleToChangePassword(): bool
    {
        return $this->_getUser()->isActive();
    }

    /**
     * Update login password.
     *
     * @param   string  $oldPassword  current user password
     * @param   string  $newPassword  new user password
     */
    public function changePassword(string $oldPassword, string $newPassword)
    {
        if ($this->isAbleToChangePassword() && $this->checkPassword($oldPassword)) {
            $this->_getUser()->setPassword($newPassword);
        }
    }

    /**
     * Compare password with password of current user.
     *
     * Returns bool(true) if the password is correct an bool(false) otherwise.
     *
     * @param   string  $password  user password
     * @return  bool
     */
    public function checkPassword(string $password): bool
    {
        return $this->_getAlgorithm()->isEqual($password, $this->_getUser()->getPassword());
    }

}

?>