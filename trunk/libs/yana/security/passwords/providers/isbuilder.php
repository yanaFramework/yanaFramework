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
 * <<interface>> Produces instances of IsAuthenticationProvider.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsBuilder
{

    /**
     * Build an user object based on a given user name.
     *
     * @param   string  $userId  the name/id of the provider
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     * @throws  \Yana\Core\Exceptions\NotFoundException  if no such provider is found
     */
    public function buildFromUserName(string $userId): \Yana\Security\Passwords\Providers\IsAuthenticationProvider;

    /**
     * Build a provider based on a given authentication method.
     *
     * @param   \Yana\Security\Passwords\Providers\IsEntity  $settings  containing request method and host information
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     * @throws  \Yana\Core\Exceptions\NotFoundException  if no such provider is found
     */
    public function buildFromAuthenticationSettings(\Yana\Security\Passwords\Providers\IsEntity $settings): \Yana\Security\Passwords\Providers\IsAuthenticationProvider;

    /**
     * Build the default authentication provider.
     *
     * This always works.
     *
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     */
    public function buildDefaultAuthenticationProvider(): \Yana\Security\Passwords\Providers\IsAuthenticationProvider;

}

?>