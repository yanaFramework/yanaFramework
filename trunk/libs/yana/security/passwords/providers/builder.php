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
     * @var  \Yana\Security\Passwords\Providers\IsDataAdapter
     */
    private $_adapter = null;

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
     * @param  array                                             $providers  list of class names with the keys as alpha-numeric identifiers
     * @param  \Yana\Security\Passwords\IsAlgorithm              $algorithm  inject a NULL-algorithm for Unit-tests
     * @param  \Yana\Security\Passwords\Providers\IsDataAdapter  $adapter    inject a NULL-adapter for Unit-tests
     */
    public function __construct(array $providers, \Yana\Security\Passwords\IsAlgorithm $algorithm, \Yana\Security\Passwords\Providers\IsDataAdapter $adapter = null)
    {
        $this->_providers = $providers;
        $this->_passwordAlgorithm = $algorithm;
        $this->_adapter = $adapter;
    }

    /**
     * Checks whether a name is in the list of known providers.
     *
     * Returns bool(true) if the name refers to a provider and the provider is valid.
     * Returns bool(false) otherwise.
     *
     * @param   string  $name  case-sensitive provider name
     * @return  bool
     */
    protected function _isProvider(string $name): bool
    {
        return array_key_exists($name, $this->_providers) && is_string($this->_providers[$name]) && \class_exists($this->_providers[$name]);
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
        if ($this->_isProvider($authenticationProviderId)) {
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
     * Returns an adapter.
     *
     * If there is none, it will create a fitting adapter automatically.
     *
     * @return  \Yana\Security\Passwords\Providers\IsDataAdapter
     */
    protected function _getAdapter(): \Yana\Security\Passwords\Providers\IsDataAdapter
    {
        if (!isset($this->_adapter)) {
            assert(!isset($factory), 'Cannot redeclare var $factory.');
            $factory = new \Yana\Db\ConnectionFactory(new \Yana\Db\SchemaFactory());
            $this->_adapter = new \Yana\Security\Passwords\Providers\Adapter($factory->createConnection('user'));
            unset($factory);
        }
        return $this->_adapter;
    }

    /**
     * Build an user object based on a given user name.
     *
     * @param   string $userId  the name/id of the provider
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     * @throws  \Yana\Core\Exceptions\NotFoundException       if no such provider is found
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found
     */
    public function buildFromUserName(string $userId): \Yana\Security\Passwords\Providers\IsAuthenticationProvider
    {
        $entity = $this->_getAdapter()->getFromUserName($userId);
        $authenticationMethod = $entity->getMethod();
        return $this->buildFromAuthenticationSettings($authenticationMethod);
    }

    /**
     * Build an user object based on a given authentication name.
     *
     * @param   \Yana\Security\Passwords\Providers\IsEntity  $e  containing request method and host information
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     * @throws  \Yana\Core\Exceptions\NotFoundException  if no such provider is found
     */
    public function buildFromAuthenticationSettings(\Yana\Security\Passwords\Providers\IsEntity $e): \Yana\Security\Passwords\Providers\IsAuthenticationProvider
    {
        if (!$e->getMethod()) {
            return $this->buildDefaultAuthenticationProvider();
        }

        $className = $this->_getClassName($e->getMethod()); // may throw exception
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