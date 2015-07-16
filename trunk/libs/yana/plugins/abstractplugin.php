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

namespace Yana\Plugins;

/**
 * <<factory>> Plugin.
 *
 * Base-class from which all plugin classes inherit features.
 * This class is supposed to provide protected shorthand-functions to quickly
 * and easily access frequently used features of the framework.
 *
 * Use the factory function to create an instance, don't call the constructor directly!
 *
 * @package     yana
 * @subpackage  plugins
 */
abstract class AbstractPlugin extends \Yana\Core\Object implements \Yana\IsPlugin
{

    /**
     * @var  \Yana\Plugins\DependencyContainer
     */
    private $_dependencyContainer = null;

    /**
     * Protected constructor so that it can't be called directly, thus leaving the dependency container uninitialized.
     */
    protected function __construct()
    {
        // intentionally left blank
    }

    /**
     * <<factory>> Load plugin.
     *
     * Creates an instance of the desired plugin and creates and injects a dependency injection container,
     * if the plugins base-class was also derived from an AbstractPlugin.
     *
     * @param   string  $name           Must be valid identifier. Consists of chars, numbers and underscores.
     * @param   string  $fromDirectory  where plugin files reside
     * @param   \Yana\Plugins\AbstractDependencyContainer  $container  To be injected into the plugin
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the plugin or its base-class was not found
     */
    public static function loadPlugin($name, $fromDirectory, \Yana\Plugins\AbstractDependencyContainer $container)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        assert('is_string($fromDirectory); // Invalid argument $fromDirectory: string expected');

        // load base class, if it exists
        assert('!isset($classFile); // Cannot redeclare var $classFile');
        $classFile = $fromDirectory . $name . "/" . $name . ".plugin.php";
        if (is_file($classFile)) {
            include_once "$classFile";
        }
        unset($classFile);

        // instantiate class, if it exists
        assert('!isset($className); // Cannot redeclare var $className');
        $className = \Yana\Plugins\Manager::PREFIX . $name;
        if (!class_exists($className)) {
            throw new \Yana\Core\Exceptions\NotFoundException("Plugin base-class not found: " . $className);
        }
        $plugin = new $className();

        if ($plugin instanceof self) {
            $plugin->_dependencyContainer = $container;
        }

        assert($plugin instanceof \Yana\IsPlugin);
        return $plugin;
    }

    /**
     * @return  \Yana\Application
     */
    protected function _getApplication()
    {
        return $this->_dependencyContainer->getApplication();
    }

    /**
     * <<factory>> Returns a ready-to-use database connection.
     *
     * Example:
     * <code>
     * // Connect to database using 'config/db/user.config'
     * $db = $this->_createConnection('user');
     * </code>
     *
     * @param   string|\Yana\Db\Ddl\Database  $schema  name of the database schema file (see config/db/*.xml),
     *                                                 or instance of \Yana\Db\Ddl\Database
     * @return  \Yana\Db\IsConnection
     */
    protected function _connectToDatabase($schema)
    {
        return $this->_dependencyContainer->getConnectionFactory()->createConnection($schema);
    }

}

?>