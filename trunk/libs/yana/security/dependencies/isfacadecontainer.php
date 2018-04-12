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
interface IsFacadeContainer extends \Yana\Security\Dependencies\IsContainer
{

    /**
     * Get cache-adapter.
     *
     * Uses an ArrayAdapter by default.
     * The cache-adapter is passed on to the security rule manager.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    public function getCache();

    /**
     * Retrieve session wrapper.
     *
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function getSession();

    /**
     * Returns a ready-to-use factory to create open database connections.
     *
     * @return  \Yana\Db\IsConnectionFactory
     */
    public function getConnectionFactory();

    /**
     * Returns the stored list of events for plugins.
     *
     * If none was given, tries to autoload them.
     *
     * @return  \Yana\Plugins\Configs\MethodCollection
     */
    public function getEventConfigurationsForPlugins();

    /**
     * Get default user settings.
     *
     * @return  array
     */
    public function getDefaultUser();

    /**
     * Get event logger.
     *
     * Retrieves a default logger if none was defined.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger();


    /**
     * Get profile id for current request.
     *
     * @return  string
     */
    public function getProfileId();

    /**
     * Get action for current request.
     *
     * @return  string
     */
    public function getLastPluginAction();

    /**
     * Builds and returns a rule-checker object.
     *
     * @return  \Yana\Security\Rules\IsChecker
     */
    public function getRulesChecker();

    /**
     * Create and return data reader.
     *
     * @return \Yana\Security\Rules\Requirements\DataReader
     */
    public function getDataReader();

    /**
     * Create and return data writer.
     *
     * @return \Yana\Security\Rules\Requirements\DataWriter
     */
    public function getDataWriter();

    /**
     * Create and return user adapter.
     *
     * @return \Yana\Security\Data\Users\Adapter
     */
    public function getUserAdapter();

}

?>