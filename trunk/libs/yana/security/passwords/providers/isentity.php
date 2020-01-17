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
 * <<interface>> User.
 *
 * Holds user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsEntity extends \Yana\Data\Adapters\IsEntity
{

    /**
     * Get the name of this provider configuration.
     *
     * Returns an empty string if there is none.
     *
     * The "name" is a human-readable label for the user's information only, not an ID.
     * To get the ID, use "getId()".
     *
     * @return  string
     */
    public function getName(): string;

    /**
     * Get name of the chosen authentication method.
     *
     * The authentication method must match one of the given enumeration items.
     *
     * Returns an empty string if there is none.
     *
     * @return  string
     */
    public function getMethod(): string;

    /**
     * Get IP or name of target host.
     *
     * This is an optional setting (not all authentication providers need a host server).
     * Returns NULL if there is none.
     *
     * @return  string|null
     */
    public function getHost(): ?string;

    /**
     * Set the name of this provider configuration.
     *
     * The "name" is a human-readable label for the user's information only, not an ID.
     * To set the ID, use "setId()".
     *
     * @param   string  $name  any alpha-numeric string is valid (case-sensitive)
     * @return  $this
     */
    public function setName(string $name);

    /**
     * Set name of the chosen authentication method.
     *
     * The authentication method must match one of the given enumeration items.
     * However, the entity doesn't check that at this point as this is checked further downstream, closer to the database.
     *
     * @param   string  $method  must match one of the enumeration items in the database
     * @return  $this
     */
    public function setMethod(string $method);

    /**
     * Set IP or name of target host.
     *
     * This is an optional setting (not all authentication providers need a host server).
     * 
     * @param   string|null  $host  IP or valid host name
     * @return  $this
     */
    public function setHost(?string $host = null);

}

?>