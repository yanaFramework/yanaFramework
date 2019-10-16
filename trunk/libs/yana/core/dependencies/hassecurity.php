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

namespace Yana\Core\Dependencies;

/**
 * <<trait>> Security sub-system dependencies.
 *
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
trait HasSecurity
{

    use \Yana\Data\Adapters\HasCache, \Yana\Core\Dependencies\HasSession;

    /**
     * Database connection.
     *
     * @var  \Yana\Db\IsConnection
     */
    private $_dataConnection = null;

    /**
     * Level data adapter.
     *
     * @var  \Yana\Security\Data\SecurityLevels\Adapter
     */
    private $_levelsAdapter = null;

    /**
     * Rules data adapter.
     *
     * @var  \Yana\Security\Data\SecurityRules\Adapter
     */
    private $_rulesAdapter = null;

    /**
     * @var  \Yana\Security\Rules\Requirements\IsDataReader
     */
    private $_requirementsDataReader = null;

    /**
     * @var  \Yana\Security\Rules\IsChecker
     */
    private $_rulesChecker = null;

    /**
     * Handles the login- and logout-functionality.
     *
     * @var  \Yana\Security\Logins\IsBehavior
     */
    private $_loginBehavior = null;

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
     * Handles the changing of passwords.
     *
     * @var  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    private $_passwordBehavior = null;

    /**
     * Used to change and check passwords.
     *
     * @var  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     */
    private $_authenticationProvider = null;

    /**
     * Used to change and check passwords.
     *
     * @var  \Yana\Security\Passwords\Behaviors\IsBuilder
     */
    private $_passwordBehaviorBuilder = null;

    /**
     * Get default user settings.
     *
     * @return  array
     */
    abstract public function getDefaultUser(): array;

    /**
     * Get default user settings.
     *
     * @return  array
     */
    abstract public function getDefaultUserRequirements(): array;

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
     * Builds and returns a rule-checker object.
     *
     * @return  \Yana\Security\Rules\IsChecker
     */
    public function getRulesChecker(): \Yana\Security\Rules\IsChecker
    {
        if (!isset($this->_rulesChecker)) {
            $rulesChecker = new \Yana\Security\Rules\CacheableChecker($this->getRequirementsDataReader());
            $rulesChecker->setCache($this->_getCache());
            $this->_rulesChecker = $rulesChecker;
        }
        return $this->_rulesChecker;
    }

    /**
     * Builds and returns a default data reader.
     *
     * The purpose of the data reader is to retrieve requirements data from the database and build corresponding entities.
     *
     * @return \Yana\Security\Rules\Requirements\IsDataReader
     */
    public function getRequirementsDataReader(): \Yana\Security\Rules\Requirements\IsDataReader
    {
        if (!isset($this->_requirementsDataReader)) {
            $this->_requirementsDataReader = new \Yana\Security\Rules\Requirements\DefaultableDataReader(
                $this->_getDataConnection(), $this->getDefaultUserRequirements());
        }
        return $this->_requirementsDataReader;
    }

    /**
     * Inject login behavior instance.
     *
     * @param   \Yana\Security\Logins\IsBehavior  $loginBehavior  dependency
     * @return  $this
     */
    public function setLoginBehavior(\Yana\Security\Logins\IsBehavior $loginBehavior)
    {
        $this->_loginBehavior = $loginBehavior;
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
     * Inject password checking behavior.
     *
     * @param   \Yana\Security\Passwords\Behaviors\IsBehavior  $passwordBehavior  dependency
     * @return  $this
     */
    public function setPasswordBehavior(\Yana\Security\Passwords\Behaviors\IsBehavior $passwordBehavior)
    {
        $this->_passwordBehavior = $passwordBehavior;
        return $this;
    }

    /**
     * Inject password behavior builder.
     *
     * @param   \Yana\Security\Passwords\Behaviors\IsBuilder  $builder  dependency
     * @return  $this
     */
    public function setPasswordBehaviorBuilder(\Yana\Security\Passwords\Behaviors\IsBuilder $builder)
    {
        $this->_passwordBehaviorBuilder = $builder;
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
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     */
    public function getAuthenticationProvider(): \Yana\Security\Passwords\Providers\IsAuthenticationProvider
    {
        if (!isset($this->_authenticationProvider)) {
            $this->_authenticationProvider =
                new \Yana\Security\Passwords\Providers\Standard($this->getPasswordAlgorithm());
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
     * Retrieve password behavior dependency.
     *
     * @return  \Yana\Security\Logins\IsBehavior
     */
    public function getLoginBehavior(): \Yana\Security\Logins\IsBehavior
    {
        if (!isset($this->_loginBehavior)) {
            $this->_loginBehavior = new \Yana\Security\Logins\StandardBehavior($this->getSession());
        }
        return $this->_loginBehavior;
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
     * Retrieve password behavior builder dependency.
     *
     * @return \Yana\Security\Passwords\Behaviors\IsBuilder
     */
    public function getPasswordBehaviorBuilder(): \Yana\Security\Passwords\Behaviors\IsBuilder
    {
        if (!isset($this->_passwordBehaviorBuilder)) {
            $this->_passwordBehaviorBuilder = new \Yana\Security\Passwords\Behaviors\Builder();
            $this->_passwordBehaviorBuilder
                    ->setPasswordAlgorithm($this->getPasswordAlgorithm())
                    ->setPasswordGenerator($this->getPasswordGenerator())
                    ->setPasswordAlgorithmBuilder($this->getPasswordAlgorithmBuilder())
                    ->setAuthenticationProvider($this->getAuthenticationProvider());
        }
        return $this->_passwordBehaviorBuilder;
    }

    /**
     * Retrieve password behavior dependency.
     *
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function getPasswordBehavior(): \Yana\Security\Passwords\Behaviors\IsBehavior
    {
        if (!isset($this->_passwordBehavior)) {
            $builder = $this->getPasswordBehaviorBuilder();
            $this->_passwordBehavior = $builder->__invoke();
        }
        return $this->_passwordBehavior;
    }

    /**
     * Retrieve levels data adapter.
     *
     * @return  \Yana\Security\Data\SecurityLevels\Adapter
     */
    public function getLevelsAdapter(): \Yana\Security\Data\SecurityLevels\Adapter
    {
        if (!isset($this->_levelsAdapter)) {
            $this->_levelsAdapter = new \Yana\Security\Data\SecurityLevels\Adapter($this->_getDataConnection());
        }
        return $this->_levelsAdapter;
    }

    /**
     * Retrieve rules data adapter.
     *
     * @return  \Yana\Security\Data\SecurityRules\Adapter
     */
    public function getRulesAdapter(): \Yana\Security\Data\SecurityRules\Adapter
    {
        if (!isset($this->_rulesAdapter)) {
            $this->_rulesAdapter = new \Yana\Security\Data\SecurityRules\Adapter($this->_getDataConnection());
        }
        return $this->_rulesAdapter;
    }

    /**
     * Create and return data reader.
     *
     * @return \Yana\Security\Rules\Requirements\IsDataReader
     */
    public function getDataReader(): \Yana\Security\Rules\Requirements\IsDataReader
    {
        return new \Yana\Security\Rules\Requirements\DefaultableDataReader($this->_getDataConnection(), $this->getDefaultUserRequirements());
    }

    /**
     * Create and return data writer.
     *
     * @return \Yana\Security\Rules\Requirements\IsDataWriter
     */
    public function getDataWriter(): \Yana\Security\Rules\Requirements\IsDataWriter
    {
        return new \Yana\Security\Rules\Requirements\DataWriter($this->_getDataConnection());
    }

    /**
     * Create and return user adapter.
     *
     * @return \Yana\Security\Data\Users\Adapter
     */
    public function getUserAdapter(): \Yana\Security\Data\Users\IsDataAdapter
    {
        return new \Yana\Security\Data\Users\Adapter($this->_getDataConnection(), $this->_getUserMapper());
    }

    /**
     * Create and return user database mapper.
     *
     * @return  \Yana\Data\Adapters\IsEntityMapper
     */
    protected function _getUserMapper(): \Yana\Data\Adapters\IsEntityMapper
    {
        return new \Yana\Security\Data\Users\Mapper();
    }
}

?>