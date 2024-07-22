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
 * Dependency container.
 *
 * This class is basically just here for unit tests.
 *
 * With default settings, creates the required instances automatically.
 * Otherwise allowing them to be overwritten.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Container extends \Yana\Core\StdObject implements \Yana\Security\Dependencies\IsFacadeContainer, \Yana\Data\Adapters\IsCacheable
{

    use \Yana\Core\Dependencies\HasSecurity;

    /**
     * @var  array
     */
    private $_defaultUser = array();

    /**
     * @var  array
     */
    private $_defaultUserRequirements = array();

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
    public function __construct(?\Yana\Plugins\Facade $facade = null)
    {
        $this->_plugins = $facade;
    }

    /**
     * Returns a ready-to-use factory to create open database connections.
     *
     * @return  \Yana\Db\IsConnectionFactory
     */
    public function getConnectionFactory(): \Yana\Db\IsConnectionFactory 
    {
        return new \Yana\Db\ConnectionFactory(new \Yana\Db\SchemaFactory($this->getCache()));
    }

    /**
     * Lazy-loads and returns a plugin database adapter.
     *
     * @return  \Yana\Db\IsConnection
     */
    public function getConnectionToUserData(): \Yana\Db\IsConnection
    {
        return $this->getConnectionFactory()->createConnection('user');
    }

    /**
     * Return plugin manager instance.
     *
     * @return  \Yana\Plugins\Facade
     */
    public function getPlugins(): \Yana\Plugins\Facade
    {
        if (!isset($this->_plugins)) {
            $this->_plugins = new \Yana\Plugins\Facade(new \Yana\Plugins\Dependencies\Container(new \Yana\Security\Sessions\Wrapper(), array()));
        }
        return $this->_plugins;
    }

    /**
     * Set plugin manager instance.
     *
     * @param   \Yana\Plugins\Facade  $facade  plugin facade
     * @return  $this
     */
    public function setPlugins(\Yana\Plugins\Facade $facade)
    {
        $this->_plugins = $facade;
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
    public function getCache(): \Yana\Data\Adapters\IsDataAdapter
    {
        return $this->_getCache();
    }

    /**
     * Get database connection.
     *
     * @return  \Yana\Db\IsConnection
     */
    public function getDataConnection(): \Yana\Db\IsConnection
    {
        return $this->_getDataConnection();
    }

    /**
     * Set connection to user database.
     *
     * @param   \Yana\Db\IsConnection  $dataConnection  connection to user database
     * @return  $this
     */
    public function setDataConnection(\Yana\Db\IsConnection $dataConnection)
    {
        return $this->_setDataConnection($dataConnection);
    }

    /**
     * Get default user settings.
     *
     * @return  array
     */
    public function getDefaultUser(): array
    {
        return $this->_defaultUser;
    }

    /**
     * Set default user settings.
     *
     * @param   array  $defaultUser  settings
     * @return  $this
     */
    public function setDefaultUser(array $defaultUser)
    {
        $this->_defaultUser = $defaultUser;
        return $this;
    }

    /**
     * Get default user security requirements.
     *
     * @return  array
     */
    public function getDefaultUserRequirements(): array
    {
        return $this->_defaultUserRequirements;
    }

    /**
     * Set default user security requirements.
     *
     * @param   array  $defaultUserRequirements  settings
     * @return  $this
     */
    public function setDefaultUserRequirements(array $defaultUserRequirements)
    {
        $this->_defaultUserRequirements = $defaultUserRequirements;
        return $this;
    }

    /**
     * Set list of events for plugins.
     *
     * @param   \Yana\Plugins\Configs\IsMethodCollection  $eventConfigurationsForPlugins  provided by Plugins\Facade
     * @return  $this
     */
    public function setEventConfigurationsForPlugins(\Yana\Plugins\Configs\IsMethodCollection $eventConfigurationsForPlugins)
    {
        $this->_eventConfigurationsForPlugins = $eventConfigurationsForPlugins;
        return $this;
    }

    /**
     * Returns the stored list of events for plugins.
     *
     * If none was given, tries to autoload them.
     *
     * @return  \Yana\Plugins\Configs\IsMethodCollection
     */
    public function getEventConfigurationsForPlugins(): \Yana\Plugins\Configs\IsMethodCollection
    {
        if (!isset($this->_eventConfigurationsForPlugins)) {
            $this->_eventConfigurationsForPlugins = $this->getPlugins()->getEventConfigurations();
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
    public function getLogger(): \Yana\Log\IsLogHandler
    {
        return \Yana\Log\LogManager::getLogger();
    }

    /**
     * Get profile id for current request.
     *
     * @return  string
     */
    public function getProfileId(): string
    {
        return $this->_profileId;
    }

    /**
     * Get action for current request.
     *
     * @return  string
     */
    public function getLastPluginAction(): string
    {
        return (string) $this->getPlugins()->getLastEvent();
    }

    /**
     * Set profile id for current request.
     *
     * @param   string  $profileId  from request to application
     * @return  $this
     */
    public function setProfileId(string $profileId)
    {
        $this->_profileId = $profileId;
        return $this;
    }

}

?>
