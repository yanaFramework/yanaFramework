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
 * <<entity>> Authentication provider setup.
 *
 * Holds information like name, authentication method, and the optional IP of a remote host (for LDAP et al).
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Entity extends \Yana\Data\Adapters\AbstractEntity implements \Yana\Security\Passwords\Providers\IsEntity
{

    /**
     * @var int
     */
    private $_id = null;

    /**
     * @var string
     */
    private $_name = "";

    /**
     * @var string
     */
    private $_method = "";

    /**
     * @var string
     */
    private $_host = null;

    /**
     * Get the database ID of this provider setup.
     *
     * This may return NULL if the database entry has not yet been saved.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set the database ID of this provider setup.
     *
     * @param  int  $providerId  database ID to use
     * @return  \Yana\Data\Adapters\IsEntity
     */
    public function setId($providerId)
    {
        assert(is_scalar($providerId), 'Wrong type for argument 1. Integer expected');
        $this->_id = (int) $providerId;
        return $this;
    }

    /**
     * Get the name of this provider configuration.
     *
     * Returns an empty string if there is none.
     *
     * @return  string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * Get name of the chosen authentication method.
     *
     * The authentication method must match one of the given enumeration items.
     *
     * Returns an empty string if there is none.
     *
     * @return  string
     */
    public function getMethod(): string
    {
        return $this->_method;
    }

    /**
     * Get IP or name of target host.
     *
     * This is an optional setting (not all authentication providers need a host server).
     * Returns NULL if there is none.
     *
     * @return  string|null
     */
    public function getHost(): ?string
    {
        return $this->_host;
    }

    /**
     * Set the name of this provider configuration.
     *
     * @param   string  $name  any alpha-numeric string is valid (case-sensitive)
     * @return  $this
     */
    public function setName(string $name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Set name of the chosen authentication method.
     *
     * The authentication method must match one of the given enumeration items.
     * However, the entity doesn't check that at this point as this is checked further downstream, closer to the database.
     *
     * @param   string  $method  must match one of the enumeration items in the database
     * @return  $this
     */
    public function setMethod(string $method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * Set IP or name of target host.
     *
     * This is an optional setting (not all authentication providers need a host server).
     * 
     * @param   string|null  $host  IP or valid host name
     * @return  $this
     */
    public function setHost(?string $host = null)
    {
        $this->_host = $host;
        return $this;
    }

}

?>