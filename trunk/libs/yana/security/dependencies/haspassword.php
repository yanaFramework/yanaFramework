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
 * <<trait>> Security sub-system dependencies.
 *
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
trait HasPassword
{

    /**
     * Database connection.
     *
     * @var  \Yana\Db\IsConnection
     */
    private $_dataConnection = null;

    /**
     * @var  \Yana\Security\Passwords\Builders\Builder
     */
    private $_passwordAlgorithmBuilder = null;

    /**
     * @var  \Yana\Security\Passwords\IsAlgorithm
     */
    private $_passwordAlgorithm = null;

    /**
     * @var  \Yana\Security\Passwords\Generators\IsAlgorithm
     */
    private $_passwordGenerator = null;

    /**
     * Used to change and check passwords.
     *
     * @var  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     */
    private $_authenticationProvider = null;

    /**
     * Returns a ready-to-use factory to create open database connections.
     *
     * @return  \Yana\Db\IsConnectionFactory
     */
    abstract public function getConnectionFactory(): \Yana\Db\IsConnectionFactory;

    /**
     * Get database connection.
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getDataConnection(): \Yana\Db\IsConnection
    {
        if (!isset($this->_dataConnection)) {
            $this->_dataConnection = $this->getConnectionFactory()->createConnection('user');
        }
        return $this->_dataConnection;
    }

    /**
     * Set connection to user database.
     *
     * @param   \Yana\Db\IsConnection  $dataConnection  connection to user database
     * @return  $this
     */
    protected function _setDataConnection(\Yana\Db\IsConnection $dataConnection)
    {
        $this->_dataConnection = $dataConnection;
        return $this;
    }

    /**
     * Inject password algorithm builder instance.
     *
     * @param   \Yana\Security\Passwords\Builders\Builder  $passwordAlgorithmBuilder  dependency
     * @return  $this
     */
    public function setPasswordAlgorithmBuilder(\Yana\Security\Passwords\Builders\Builder $passwordAlgorithmBuilder)
    {
        $this->_passwordAlgorithmBuilder = $passwordAlgorithmBuilder;
        return $this;
    }

    /**
     * Inject specific password algorithm.
     *
     * @param   \Yana\Security\Passwords\IsAlgorithm  $passwordAlgorithm  dependency
     * @return  $this
     */
    public function setPasswordAlgorithm(\Yana\Security\Passwords\IsAlgorithm $passwordAlgorithm)
    {
        $this->_passwordAlgorithm = $passwordAlgorithm;
        return $this;
    }

    /**
     * Inject password generator.
     *
     * @param   \Yana\Security\Passwords\Generators\IsAlgorithm  $passwordGenerator  dependency
     * @return  $this
     */
    public function setPasswordGenerator(\Yana\Security\Passwords\Generators\IsAlgorithm $passwordGenerator)
    {
        $this->_passwordGenerator = $passwordGenerator;
        return $this;
    }

    /**
     * Set auhtentication provider dependency.
     *
     * @param   \Yana\Security\Passwords\Providers\IsAuthenticationProvider  $provider  dependency
     * @return  $this
     */
    public function setAuthenticationProvider(\Yana\Security\Passwords\Providers\IsAuthenticationProvider $provider)
    {
        $this->_authenticationProvider = $provider;
        return $this;
    }

    /**
     * Retrieve authentication provider dependency.
     *
     * The authentication provider is used to check and/or change passwords.
     * If none has been set, this function will initialize and return a standard
     * authentication provider by default.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     */
    public function getAuthenticationProvider(\Yana\Security\Data\Users\IsEntity $user): \Yana\Security\Passwords\Providers\IsAuthenticationProvider
    {
        if (!isset($this->_authenticationProvider)) {
            $builder = $this->getAuthenticationProviderBuilder();
            $this->_authenticationProvider = $builder->buildFromUserName($user->getId());
            unset($builder);
        }
        return $this->_authenticationProvider;
    }

    /**
     * Retrieve algorithm builder.
     *
     * The builder's purpose is to select and create instances of hashing algorithms used to create and compare password hashes.
     *
     * @return  \Yana\Security\Passwords\Builders\IsBuilder
     */
    public function getPasswordAlgorithmBuilder(): \Yana\Security\Passwords\Builders\IsBuilder
    {
        if (!isset($this->_passwordAlgorithmBuilder)) {
            $this->_passwordAlgorithmBuilder = new \Yana\Security\Passwords\Builders\Builder();
        }
        return $this->_passwordAlgorithmBuilder;
    }

    /**
     * Retrieve password algorithm dependency.
     *
     * @return  \Yana\Security\Passwords\IsAlgorithm
     */
    public function getPasswordAlgorithm(): \Yana\Security\Passwords\IsAlgorithm
    {
        if (!isset($this->_passwordAlgorithm)) {
            $this->_passwordAlgorithm =
                $this->getPasswordAlgorithmBuilder()
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::BASIC)
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::BLOWFISH)
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::SHA256)
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::SHA512)
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::BCRYPT)
                    ->__invoke();
        }
        return $this->_passwordAlgorithm;
    }

    /**
     * Retrieve password generator dependency.
     *
     * @return  \Yana\Security\Passwords\Generators\IsAlgorithm
     */
    public function getPasswordGenerator(): \Yana\Security\Passwords\Generators\IsAlgorithm
    {
        if (!isset($this->_passwordGenerator)) {
            $this->_passwordGenerator = new \Yana\Security\Passwords\Generators\StandardAlgorithm();
        }
        return $this->_passwordGenerator;
    }

    /**
     * Create and return authentication provider builder.
     *
     * @return \Yana\Security\Passwords\Providers\IsBuilder
     */
    public function getAuthenticationProviderBuilder(): \Yana\Security\Passwords\Providers\IsBuilder
    {
        return new \Yana\Security\Passwords\Providers\Builder($this->getPasswordAlgorithm(), $this->_getAuthenticationProviderAdapter());
    }

    /**
     * Create and return authentication provider adapter.
     *
     * @return \Yana\Security\Passwords\Providers\IsAdapter
     */
    protected function _getAuthenticationProviderAdapter(): \Yana\Security\Passwords\Providers\IsAdapter
    {
        return new \Yana\Security\Passwords\Providers\Adapter($this->_getDataConnection(), $this->_getAuthenticationProviderMapper());
    }

    /**
     * Create and return authentication provider database mapper.
     *
     * @return  \Yana\Data\Adapters\IsEntityMapper
     */
    protected function _getAuthenticationProviderMapper(): \Yana\Data\Adapters\IsEntityMapper
    {
        return new \Yana\Security\Passwords\Providers\Mapper();
    }

}

?>