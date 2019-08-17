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
     * Get default user settings.
     *
     * @return  array
     */
    abstract public function getDefaultUser();

    /**
     * Returns a ready-to-use factory to create open database connections.
     *
     * @return  \Yana\Db\IsConnectionFactory
     */
    abstract public function getConnectionFactory();

    /**
     * Get database connection.
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getDataConnection()
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
    public function getRulesChecker()
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
    public function getRequirementsDataReader()
    {
        if (!isset($this->_requirementsDataReader)) {
            $this->_requirementsDataReader = new \Yana\Security\Rules\Requirements\DefaultableDataReader(
                $this->_getDataConnection(), $this->getDefaultUser());
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
     * Retrieve algorithm builder.
     *
     * The builder's purpose is to select and create instances of hashing algorithms used to create and compare password hashes.
     *
     * @return  \Yana\Security\Passwords\Builders\IsBuilder
     */
    public function getPasswordAlgorithmBuilder()
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
    public function getLoginBehavior()
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
    public function getPasswordAlgorithm()
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
    public function getPasswordGenerator()
    {
        if (!isset($this->_passwordGenerator)) {
            $this->_passwordGenerator = new \Yana\Security\Passwords\Generators\StandardAlgorithm();
        }
        return $this->_passwordGenerator;
    }

    /**
     * Retrieve password behavior dependency.
     *
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function getPasswordBehavior()
    {
        if (!isset($this->_passwordBehavior)) {
            $this->_passwordBehavior = new \Yana\Security\Passwords\Behaviors\StandardBehavior(
                $this->getPasswordAlgorithm(), $this->getPasswordGenerator()
            );
        }
        return $this->_passwordBehavior;
    }

    /**
     * Retrieve levels data adapter.
     *
     * @return  \Yana\Security\Data\SecurityLevels\Adapter
     */
    public function getLevelsAdapter()
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
    public function getRulesAdapter()
    {
        if (!isset($this->_rulesAdapter)) {
            $this->_rulesAdapter = new \Yana\Security\Data\SecurityRules\Adapter($this->_getDataConnection());
        }
        return $this->_rulesAdapter;
    }

    /**
     * Create and return data reader.
     *
     * @return \Yana\Security\Rules\Requirements\DataReader
     */
    public function getDataReader()
    {
        return new \Yana\Security\Rules\Requirements\DefaultableDataReader($this->_getDataConnection(), $this->getDefaultUser());
    }

    /**
     * Create and return data writer.
     *
     * @return \Yana\Security\Rules\Requirements\DataWriter
     */
    public function getDataWriter()
    {
        return new \Yana\Security\Rules\Requirements\DataWriter($this->_getDataConnection());
    }

    /**
     * Create and return user adapter.
     *
     * @return \Yana\Security\Data\Users\Adapter
     */
    public function getUserAdapter()
    {
        return new \Yana\Security\Data\Users\Adapter($this->_getDataConnection(), $this->_getUserMapper());
    }

    /**
     * Create and return user database mapper.
     *
     * @return  \Yana\Data\Adapters\IsEntityMapper
     */
    protected function _getUserMapper()
    {
        return new \Yana\Security\Data\Users\Mapper();
    }
}

?>