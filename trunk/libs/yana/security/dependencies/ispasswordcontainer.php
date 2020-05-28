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

namespace Yana\Security\Dependencies;

/**
 * <<interface>> Defines dependencies required by password behavior-builder.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsPasswordContainer
{

    /**
     * Retrieve password algorithm dependency.
     *
     * @return  \Yana\Security\Passwords\IsAlgorithm
     */
    public function getPasswordAlgorithm(): \Yana\Security\Passwords\IsAlgorithm;

    /**
     * Retrieve password generator dependency.
     *
     * @return  \Yana\Security\Passwords\Generators\IsAlgorithm
     */
    public function getPasswordGenerator(): \Yana\Security\Passwords\Generators\IsAlgorithm;

    /**
     * Returns an authentication provider.
     *
     * The authentication provider is used to check and/or change passwords.
     * If none has been set, this function will initialize and return a standard
     * authentication provider by default.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     */
    public function getAuthenticationProvider(\Yana\Security\Data\Users\IsEntity $user): \Yana\Security\Passwords\Providers\IsAuthenticationProvider;

}

?>