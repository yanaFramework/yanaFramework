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

namespace Yana\Security\Dependencies;

/**
 * Dependency container.
 *
 * With default settings, creates the required instances automatically.
 * Otherwise allowing them to be overwritten.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Container extends \Yana\Core\Object implements \Yana\Security\Dependencies\IsFacadeContainer, \Yana\Data\Adapters\IsCacheable
{

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
     * @var  array
     */
    private $_defaultUser = array();

    /**
     * @var  \Yana\Security\Rules\Requirements\IsDataReader
     */
    private $_requirementsDataReader = null;

    /**
     * @var  \Yana\Security\Rules\IsChecker
     */
    private $_rulesChecker = null;

    /**
     * @var  \Yana\Security\Sessions\IsWrapper
     */
    private $_session = null;

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
     * @var  \Yana\Data\Adapters\IsDataAdapter
     */
    private $_cache = null;

    /**
     * @var  \Yana\Plugins\Configs\MethodCollection
     */
    private $_eventConfigurationsForPlugins = null;

    /**
     * @var  string
     */
    private $_profileId = "";

    /**
     * @var  \Yana\Plugins\Facade
     */
    private $_plugins;

    /**
     * <<constructor>> Initializes dependencies.
     *
     * @param  \Yana\Plugins\Facade  $facade  dependent resource
     */
    public function __construct(\Yana\Plugins\Facade $facade = null)
    {
        $this->_plugins = $facade;
    }

    /**
     * Return plugin manager instance.
     *
     * @return  \Yana\Plugins\Facade
     */
    protected function _getPlugins()
    {
        if (!isset($this->_plugins)) {
            $this->_plugins = \Yana\Plugins\Facade::getInstance();
        }
        return $this->_plugins;
    }

    /**
     * Replace the cache adapter.
     *
     * This class uses an ArrayAdapter by default.
     * Overwrite only for unit-tests, or if you are absolutely sure you need to
     * and know what you are doing.
     * Replacing this by the wrong adapter might introduce a security risk,
     * unless you are in a very specific usage scenario.
     *
     * Note that this may also replace the cache contents.
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $cache  new cache adapter
     * @return  \Yana\Data\Adapters\IsCacheable
     */
    public function setCache(\Yana\Data\Adapters\IsDataAdapter $cache)
    {
        $this->_cache = $cache;
        return $this;
    }

    /**
     * Get cache-adapter.
     *
     * Uses an ArrayAdapter by default.
     * The cache-adapter is passed on to the security rule manager.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    public function getCache()
    {
        if (!isset($this->_cache)) {
            $this->_cache = new \Yana\Data\Adapters\ArrayAdapter();
        }
        return $this->_cache;
    }

    /**
     * Get database connection.
     *
     * @return  \Yana\Db\IsConnection
     */
    public function getDataConnection()
    {
        if (!isset($this->_dataConnection)) {
            $connectionFactory = new \Yana\Db\ConnectionFactory(new \Yana\Db\SchemaFactory($this->getCache()));
            $this->_dataConnection = $connectionFactory->createConnection('user');
        }
        return $this->_dataConnection;
    }

    /**
     * Set connection to user database.
     *
     * @param   \Yana\Db\IsConnection  $dataConnection  connection to user database
     * @return  self
     */
    public function setDataConnection(\Yana\Db\IsConnection $dataConnection)
    {
        $this->_dataConnection = $dataConnection;
        return $this;
    }

    /**
     * Get default user settings.
     *
     * @return  array
     */
    public function getDefaultUser()
    {
        return $this->_defaultUser;
    }

    /**
     * Set default user settings.
     *
     * @param   array  $defaultUser  settings
     * @return  self
     */
    public function setDefaultUser(array $defaultUser)
    {
        $this->_defaultUser = $defaultUser;
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
            $rulesChecker->setCache($this->getCache());
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
                $this->getDataConnection(), $this->getDefaultUser());
        }
        return $this->_requirementsDataReader;
    }

    /**
     * Retrieve session wrapper.
     *
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function getSession()
    {
        if (!isset($this->_session)) {
            $this->_session = new \Yana\Security\Sessions\Wrapper();
        }
        return $this->_session;
    }

    /**
     * Inject session wrapper.
     *
     * @param   \Yana\Security\Sessions\IsWrapper  $session  dependency
     * @return  self
     */
    public function setSession(\Yana\Security\Sessions\IsWrapper $session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     * Inject login behavior instance.
     *
     * @param   \Yana\Security\Logins\IsBehavior  $loginBehavior  dependency
     * @return  self
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
     * @return  self
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
     * @return  self
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
     * @return  self
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
     * @return  self
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
            $this->_levelsAdapter = new \Yana\Security\Data\SecurityLevels\Adapter($this->getDataConnection());
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
            $this->_rulesAdapter = new \Yana\Security\Data\SecurityRules\Adapter($this->getDataConnection());
        }
        return $this->_rulesAdapter;
    }

    /**
     * Set list of events for plugins.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurationsForPlugins  provided by Plugins\Facade
     * @return  self
     */
    public function setEventConfigurationsForPlugins(\Yana\Plugins\Configs\MethodCollection $eventConfigurationsForPlugins)
    {
        $this->_eventConfigurationsForPlugins = $eventConfigurationsForPlugins;
        return $this;
    }

    /**
     * Returns the stored list of events for plugins.
     *
     * If none was given, tries to autoload them.
     *
     * @return  \Yana\Plugins\Configs\MethodCollection
     */
    public function getEventConfigurationsForPlugins()
    {
        if (!isset($this->_eventConfigurationsForPlugins)) {
            $this->_eventConfigurationsForPlugins = $this->_getPlugins()->getEventConfigurations();
        }
        return $this->_eventConfigurationsForPlugins;
    }

    /**
     * Get event logger.
     *
     * Retrieves a default logger if none was defined.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger()
    {
        return \Yana\Log\LogManager::getLogger();
    }

    /**
     * Get profile id for current request.
     *
     * @return  string
     */
    public function getProfileId()
    {
        return $this->_profileId;
    }

    /**
     * Get action for current request.
     *
     * @return  string
     */
    public function getLastPluginAction()
    {
        return (string) $this->_getPlugins()->getLastEvent();
    }

    /**
     * Set profile id for current request.
     *
     * @param   string  $profileId  from request to application
     * @return  self
     */
    public function setProfileId($profileId)
    {
        assert('is_string($profileId); // $profileId expected to be String');
        $this->_profileId = (string) $profileId;
        return $this;
    }

}

?>