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
     * @var  \Yana\Security\Passwords\Providers\IsAdapter
     */
    private $_adapter = null;

    /**
     * @var  \Yana\Security\Passwords\IsAlgorithm
     */
    private $_passwordAlgorithm = null;

    /**
     * @var  array
     */
    private static $_authenticationProviders = array();

    /**
     * <<constructor>> Set up and initialize adapters.
     *
     * @param  \Yana\Security\Passwords\IsAlgorithm              $algorithm  inject a NULL-algorithm for Unit-tests
     * @param  \Yana\Security\Passwords\Providers\IsAdapter  $adapter    inject a NULL-adapter for Unit-tests
     */
    public function __construct(\Yana\Security\Passwords\IsAlgorithm $algorithm, \Yana\Security\Passwords\Providers\IsAdapter $adapter = null)
    {
        $this->_passwordAlgorithm = $algorithm;
        $this->_adapter = $adapter;
    }

    /**
     * Add a new authentication provider.
     *
     * This function won't overwrite existing entries.
     *
     * @param   string  $id         alpha-numeric id, case-sensitive
     * @param   string  $className  must implement \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     */
    public static function addAuthenticationProvider(string $id, string $className)
    {
        if (!isset(self::$_authenticationProviders[$id])) {
            self::$_authenticationProviders[$id] = $className;
        }
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
        $providers = self::$_authenticationProviders;
        return array_key_exists($name, $providers) && is_string($providers[$name]) && \class_exists($providers[$name]);
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
            return self::$_authenticationProviders[$authenticationProviderId];
        }
        throw new \Yana\Core\Exceptions\NotFoundException("Not a valid authentication provider: " . $authenticationProviderId, \Yana\Log\TypeEnumeration::WARNING);
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
     * @return  \Yana\Security\Passwords\Providers\IsAdapter
     */
    protected function _getAdapter(): \Yana\Security\Passwords\Providers\IsAdapter
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
        return $this->buildFromAuthenticationSettings($entity);
    }

    /**
     * Build an user object based on a given authentication name.
     *
     * @param   \Yana\Security\Passwords\Providers\IsEntity  $entity  containing request method and host information
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     * @throws  \Yana\Core\Exceptions\NotFoundException  if no such provider is found
     */
    public function buildFromAuthenticationSettings(\Yana\Security\Passwords\Providers\IsEntity $entity): \Yana\Security\Passwords\Providers\IsAuthenticationProvider
    {
        if (!$entity->getMethod()) {
            return $this->buildDefaultAuthenticationProvider();
        }

        $className = $this->_getClassName($entity->getMethod()); // may throw exception
        $dependencyContainer = new \Yana\Security\Passwords\Providers\DependencyContainer($entity, $this->_getPasswordAlgorithm());
        $provider = $className::factory($dependencyContainer);
        assert($provider instanceof \Yana\Security\Passwords\Providers\IsAuthenticationProvider);
        return $provider;
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