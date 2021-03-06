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
 * <<interface>> Defines dependencies required by behavior-builder.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsFacadeContainer extends \Yana\Security\Dependencies\IsContainer, \Yana\Security\Dependencies\IsPasswordContainer
{

    /**
     * Get cache-adapter.
     *
     * Uses an ArrayAdapter by default.
     * The cache-adapter is passed on to the security rule manager.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    public function getCache(): \Yana\Data\Adapters\IsDataAdapter;

    /**
     * Retrieve session wrapper.
     *
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function getSession(): \Yana\Security\Sessions\IsWrapper;

    /**
     * Retrieve cookie wrapper.
     *
     * @return  \Yana\Core\Sessions\IsCookieWrapper
     */
    public function getCookie(): \Yana\Core\Sessions\IsCookieWrapper;

    /**
     * Returns a ready-to-use factory to create open database connections.
     *
     * @return  \Yana\Db\IsConnectionFactory
     */
    public function getConnectionFactory(): \Yana\Db\IsConnectionFactory;

    /**
     * Returns the stored list of events for plugins.
     *
     * If none was given, tries to autoload them.
     *
     * @return  \Yana\Plugins\Configs\IsMethodCollection
     */
    public function getEventConfigurationsForPlugins(): \Yana\Plugins\Configs\IsMethodCollection;

    /**
     * Get default user settings.
     *
     * @return  array
     */
    public function getDefaultUser(): array;

    /**
     * Get default user security requirements.
     *
     * @return  array
     */
    public function getDefaultUserRequirements(): array;

    /**
     * Get event logger.
     *
     * Retrieves a default logger if none was defined.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger(): \Yana\Log\IsLogHandler;


    /**
     * Get profile id for current request.
     *
     * @return  string
     */
    public function getProfileId(): string;

    /**
     * Get action for current request.
     *
     * @return  string
     */
    public function getLastPluginAction(): string;

    /**
     * Builds and returns a rule-checker object.
     *
     * @return  \Yana\Security\Rules\IsChecker
     */
    public function getRulesChecker(): \Yana\Security\Rules\IsChecker;

    /**
     * Create and return data reader.
     *
     * @return \Yana\Security\Rules\Requirements\IsDataReader
     */
    public function getDataReader(): \Yana\Security\Rules\Requirements\IsDataReader;

    /**
     * Create and return data writer.
     *
     * @return \Yana\Security\Rules\Requirements\IsDataWriter
     */
    public function getDataWriter(): \Yana\Security\Rules\Requirements\IsDataWriter;

    /**
     * Create and return user adapter.
     *
     * @return \Yana\Security\Data\Users\IsDataAdapter
     */
    public function getUserAdapter(): \Yana\Security\Data\Users\IsDataAdapter;

}

?>