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
 */

namespace Yana\Plugins\Dependencies;

/**
 * <<factory>> Creates dependency ccntainers.
 *
 * Used to inject class dependencies into plugins.
 *
 * @package     yana
 * @subpackage  plugins
 */
class ContainerFactory extends \Yana\Plugins\Dependencies\AbstractContainer
{

    /**
     * Initialize basic settings.
     *
     * @param  \Yana\Application  $application  currently active application settings
     */
    public function __construct(\Yana\Application $application)
    {
        $this->_setApplication($application);
    }

    /**
     * Creates a dependency-container and returns it.
     *
     * @return  \Yana\Plugins\Dependencies\Container
     */
    public function createDependencies()
    {
        $container = new \Yana\Plugins\Dependencies\Container();
        $container
            ->_setApplication($this->_getApplication())
            ->_setConnectionFactory($this->_getConnectionFactory());
        return $container;
    }

    /**
     * Creates a menu-dependency-container and returns it.
     *
     * @return \Yana\Plugins\Dependencies\IsMenuContainer
     */
    public function createMenuDependencies()
    {
        // Yes, this looks like the laziest way to do it, because it is the laziest way to do it.
        // However: we need the application instance anyway, so we might as well use it.
        return new \Yana\Plugins\Dependencies\MenuContainer($this->_getApplication());
    }

    /**
     * Creates a connection-factory, using the applications default cache.
     *
     * @return  \Yana\Db\IsConnectionFactory
     */
    protected function _getConnectionFactory()
    {
        $connectionFactory = parent::_getConnectionFactory();
        if (!$connectionFactory instanceof \Yana\Db\IsConnectionFactory) {
            $schemaFactory = new \Yana\Db\SchemaFactory();
            $schemaFactory->setCache($this->_getApplication()->getCache());
            $connectionFactory = new \Yana\Db\ConnectionFactory($schemaFactory);
            $this->_setConnectionFactory($connectionFactory);
        }
        return $connectionFactory;
    }

}

?>