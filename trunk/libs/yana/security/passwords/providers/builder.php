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
 * <<builder>> Produces instances of IsAuthenticationProvider.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Builder extends \Yana\Core\StdObject implements \Yana\Security\Passwords\Providers\IsBuilder
{

    /**
     * @var  \Yana\Security\Data\Users\IsDataAdapter
     */
    private $_userAdapter = null;

    /**
     * @var  \Yana\Security\Passwords\IsAlgorithm
     */
    private $_passwordAlgorithm = null;

    /**
     * @var  array
     */
    private $_providers = array();

    /**
     * <<constructor>> Set up and initialize adapters.
     *
     * @param  array                                    $providers    list of class names with the keys as alpha-numeric identifiers
     * @param  \Yana\Security\Passwords\IsAlgorithm     $algorithm    inject a NULL-algorithm for Unit-tests
     * @param  \Yana\Security\Data\Users\IsDataAdapter  $userAdapter  inject a NULL-adapter for Unit-tests
     */
    public function __construct(array $providers, \Yana\Security\Passwords\IsAlgorithm $algorithm, \Yana\Security\Data\Users\IsDataAdapter $userAdapter = null)
    {
        $this->_providers = $providers;
        $this->_passwordAlgorithm = $algorithm;
        $this->_userAdapter = $userAdapter;
    }

    protected function _isProvider(string $name): bool
    {
        return array_key_exists($name, $this->_providers);
    }

    /**
     * Get class name of authentication provider identified by id.
     *
     * The mapping is generally done in the system configuration file "system.config.xml" in the "config" directory.
     *
     * @param   string  $authenticationProviderId  alpha-numeric name identifying the provider (case-sensitive)
     * @return  string
     * @throws  \Yana\Core\Exceptions\NotFoundException  if no provider with such ID is found
     */
    protected function _getClassName(string $authenticationProviderId)
    {
        $isProvider = isset($this->_providers[$authenticationProviderId]) && is_string($this->_providers[$authenticationProviderId])
            && \class_exists($this->_providers[$authenticationProviderId]);
        if ($isProvider) {
            return $this->_providers[$authenticationProviderId];
        }
        throw new \Yana\Core\Exceptions\NotFoundException("No such authentication provider: " . $authenticationProviderId, \Yana\Log\TypeEnumeration::WARNING);
    }

    /**
     * Returns a password hashing algorithm.
     *
     * @return  \Yana\Security\Passwords\IsAlgorithm
     */
    protected function _getPasswordAlgorithm(): \Yana\Security\Passwords\IsAlgorithm
    {
        return $this->_passwordAlgorithm;
    }

    /**
     * Returns a user adapter.
     *
     * If there is none, it will create a fitting adapter automatically.
     *
     * @return  \Yana\Security\Data\Users\IsDataAdapter
     */
    protected function _getUserAdapter(): \Yana\Security\Data\Users\IsDataAdapter
    {
        if (!isset($this->_userAdapter)) {
            assert(!isset($factory), 'Cannot redeclare var $factory.');
            $factory = new \Yana\Db\ConnectionFactory(new \Yana\Db\SchemaFactory());
            $this->_userAdapter = new \Yana\Security\Data\Users\Adapter($factory->createConnection('user'));
            unset($factory);
        }
        return $this->_userAdapter;
    }

    /**
     * Build an user object based on a given user name.
     *
     * @param   string $userId  the name/id of the provider
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     * @throws  \Yana\Core\Exceptions\NotFoundException  if no such provider is found
     */
    public function buildFromUserName(string $userId): \Yana\Security\Passwords\Providers\IsAuthenticationProvider
    {
        
    }

    /**
     * Build an user object based on a given authentication name.
     *
     * @param   string  $authenticationId  the name/id of the provider
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     * @throws  \Yana\Core\Exceptions\NotFoundException  if no such provider is found
     */
    public function buildFromAuthenticationName(string $authenticationId): \Yana\Security\Passwords\Providers\IsAuthenticationProvider
    {
        $className = $this->_getClassName($authenticationId);
    }

    /**
     * Build the default authentication provider.
     *
     * This always works.
     *
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     */
    public function buildDefaultAuthenticationProvider(): \Yana\Security\Passwords\Providers\IsAuthenticationProvider
    {
        return new \Yana\Security\Passwords\Providers\Standard($this->_getPasswordAlgorithm());
    }

}

?>