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
 * <<interface>> Authentication provider to check passwords.
 *
 * @package     yana
 * @subpackage  security
 */
interface IsAuthenticationProvider
{

    /**
     * Returns TRUE if the provider supports changing passwords.
     *
     * @return  bool
     */
    public function isAbleToChangePassword(): bool;

    /**
     * Update login password.
     *
     * @param  \Yana\Security\Data\Users\IsEntity  $user         holds password information
     * @param  string                              $newPassword  new user password
     */
    public function changePassword(\Yana\Security\Data\Users\IsEntity $user, string $newPassword);

    /**
     * Compare password with password of current user.
     *
     * Returns bool(true) if the password is correct an bool(false) otherwise.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user      holds password information
     * @param   string                              $password  user password
     * @return  bool
     */
    public function checkPassword(\Yana\Security\Data\Users\IsEntity $user, string $password): bool;

    /**
     * <<factory>> Create an instance of this class.
     *
     * @param   \Yana\Security\Passwords\Providers\IsDependencyContainer  $container  every provider may have different dependencies,
     *                                                                                so to have a common interface regardless,
     *                                                                                we inject them via a dependency container
     * @return  self
     */
    public static function factory(\Yana\Security\Passwords\Providers\IsDependencyContainer $container): self;
}

?>