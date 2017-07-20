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
abstract class AbstractDependencyWrapper extends \Yana\Core\Object implements \Yana\IsPlugin
{

    /**
     * @var  \Yana\Plugins\DependencyContainer
     */
    private $_dependencyContainer = null;

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
        $classFile = \Yana\Plugins\PluginNameMapper::toClassFilenameWithDirectory($name, $fromDirectory);
        if (is_file($classFile)) {
            include_once "$classFile";
        }
        unset($classFile);

        // instantiate class, if it exists
        assert('!isset($className); // Cannot redeclare var $className');
        $className = \Yana\Plugins\PluginNameMapper::toClassNameWithNamespace($name);
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
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    protected function _getSession()
    {
        return $this->_dependencyContainer->getSession();
    }

    /**
     * @return  \Yana\Security\IsFacade
     */
    protected function _getSecurityFacade()
    {
        return $this->_dependencyContainer->getSecurityFacade();
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
     * @param   string  $schema  name of the database schema file (see config/db/*.xml),
     *                           or instance of \Yana\Db\Ddl\Database
     * @return  \Yana\Db\IsConnection
     */
    protected function _connectToDatabase($schema)
    {
        assert('is_string($schema); // Invalid argument $schema: string expected');

        return $this->_dependencyContainer->getConnectionFactory()->createConnection((string) $schema);
    }

    /**
     * Download a file.
     *
     * This function will automatically determine the requested resource. It will
     * check whether it is of type "image" or "file" and handle the request
     * accordingly. This means it will be sending appropriate headers,
     * retrieving and outputting the contents of the resource and terminating
     * the program.
     */
    protected function _downloadFile()
    {
        $source = \Yana\Db\Blob::getFileId();
        if ($source === false) {
            exit("Error: invalid resource.");
        }
        $directory = preg_quote(\Yana\Db\Blob::getDirectory(), '/');
        // downloading a file
        if (preg_match('/^' . $directory . 'file\.\w+\.gz$/', $source)) {

            $dbBlob = new \Yana\Db\Blob($source);
            $dbBlob->read();
            header("Cache-Control: maxage=1"); // Workaround for a Bug in IE8 with HTTPS-downloads
            header("Pragma: public");
            header('Content-Disposition: attachment; filename=' . $dbBlob->getPath());
            header('Content-Length: ' . $dbBlob->getFilesize());
            header('Content-type: ' . $dbBlob->getMimeType());
            print $dbBlob->getContent();

        // downloading an image
        } elseif (preg_match('/^' . $directory . '(image|thumb)\.\w+\.png$/', $source)) {

            $image = new Image($source);
            $image->outputToScreen();

        } else {
            print "Error: invalid resource.";
        }
        exit;
    }

}

?>