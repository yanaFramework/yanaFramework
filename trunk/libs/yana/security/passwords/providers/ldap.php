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
 * @codeCoverageIgnore
 */
class Ldap extends \Yana\Security\Passwords\Providers\AbstractProvider implements \Yana\Security\Passwords\Providers\IsAuthenticationProvider
{

    /**
     * @var  string
     */
    private $_ldapServer = "";

    /**
     * Initialize dependencies.
     *
     * @param  \Yana\Security\Passwords\Generators\IsAlgorithm  $generator  to generade new random passwords
     */
    /**
     * <<construct>> Initialize user entity.
     *
     * @param  string  $ldapServer  IP or host name of LDAP server
     */
    public function __construct(string $ldapServer)
    {
        $this->_ldapServer = $ldapServer;
    }

    /**
     * Retruns address of LDAP server.
     *
     * This function returns either an IP or host name that should be used to connect to the LDAP server.
     *
     * @return string
     */
    protected function _getLdapServer(): string
    {
        return $this->_ldapServer;
    }

    /**
     * Create and return LDAP connection.
     *
     * @return  resource
     */
    protected function _createLdapConnection()
    {
        $connection = \ldap_connect($this->_getLdapServer());
        // Sets the protocol version to 3.0
        \ldap_set_option($connection, \LDAP_OPT_PROTOCOL_VERSION, 3);
        // Prevents the client from referring the same credentials to another server if asked to do so (security setting in case server was hacked)
        \ldap_set_option($connection, \LDAP_OPT_REFERRALS, 0);
        return $connection;
    }

    /**
     * Returns TRUE if the provider supports changing passwords.
     *
     * @return  bool
     */
    public function isAbleToChangePassword(): bool
    {
        return false;
    }

    /**
     * Update login password.
     *
     * @param  \Yana\Security\Data\Users\IsEntity  $user         holds password information
     * @param  string                              $newPassword  new user password
     */
    public function changePassword(\Yana\Security\Data\Users\IsEntity $user, string $newPassword)
    {
        $message = "Changing passwords is not implemented for LDAP.";
        throw new \Yana\Core\Exceptions\NotImplementedException($message, \Yana\Log\TypeEnumeration::ERROR);
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
        $userId = $user->getId() . "@" . $this->_getLdapServer();
        $connection = $this->_createLdapConnection();
        if (!\ldap_bind($connection, $userId, $password)) {
            return false;
        }
        return true;
    }

    /**
     * <<factory>> Create an instance of this class.
     *
     * @param   \Yana\Security\Passwords\Providers\IsDependencyContainer  $container  every provider may have different dependencies,
     *                                                                                so to have a common interface regardless,
     *                                                                                we inject them via a dependency container
     * @return  self
     */
    public static function factory(IsDependencyContainer $container): \Yana\Security\Passwords\Providers\IsAuthenticationProvider
    {
        return new \Yana\Security\Passwords\Providers\Ldap($container->getAuthenticationSettings()->getHost());
    }

}

?>