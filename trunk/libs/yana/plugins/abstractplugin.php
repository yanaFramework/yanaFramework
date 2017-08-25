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
     * @var  \Yana\Plugins\Dependencies\IsPluginContainer
     */
    private static $_fallbackDependencyContainer = null;

    /**
     * @var  \Yana\Plugins\Dependencies\IsPluginContainer
     */
    private $_dependencyContainer = null;

    /**
     * @var  \Yana\Util\Microsummary
     */
    private $_microsummary;

    /**
     * <<construct>> Empty constructor.
     *
     * This is only here so that derived classes get a warning when they overwrite this and introduce new mandatory parameters.
     */
    public function __construct()
    {
        //dummy
    }

    /**
     * Hack to ensure there will always be a depdency container, even before the constructor is called for the first time.
     *
     * @return  \Yana\Plugins\Dependencies\IsPluginContainer
     */
    private function _getDependencyContainer()
    {
        if (!isset($this->_dependencyContainer)) {
            $this->_dependencyContainer = self::$_fallbackDependencyContainer;
        }
        return $this->_dependencyContainer;
    }

    /**
     * <<factory>> Load plugin.
     *
     * Creates an instance of the desired plugin and creates and injects a dependency injection container,
     * if the plugins base-class was also derived from an AbstractPlugin.
     *
     * @param   string                                        $name           must be valid identifier. Consists of chars, numbers and underscores.
     * @param   \Yana\Files\IsDir                             $fromDirectory  where plugin files reside
     * @param   \Yana\Plugins\Dependencies\IsPluginContainer  $container      to be injected into the plugin
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the plugin or its base-class was not found
     */
    public static function loadPlugin($name, \Yana\Files\IsDir $fromDirectory, \Yana\Plugins\Dependencies\IsPluginContainer $container)
    {
        assert('is_string($name); // Invalid argument $name: string expected');

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
        // Pre-initialize the depdency-container before calling the constructor.
        // Just in case somebody used the constructor for something "interesting".
        self::$_fallbackDependencyContainer = $container;
        // With the dependencies already injected, we now call the custom constructor.
        $plugin = new $className();

        // Since the Plugin-Manager only insists on the interface
        if ($plugin instanceof self) {
            // This initializes the dependency container in case _getDependencyContainer() was not called by the constructor
            $plugin->_dependencyContainer = $container;
        }
        // We don't need the fallback anymore, so we get rid of it. Just in case somebody is counting references.
        self::$_fallbackDependencyContainer = null;

        assert($plugin instanceof \Yana\IsPlugin);
        return $plugin;
    }

    /**
     * @return  \Yana\Application
     */
    protected function _getApplication()
    {
        return $this->_getDependencyContainer()->getApplication();
    }

    /**
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    protected function _getSession()
    {
        return $this->_getDependencyContainer()->getSession();
    }

    /**
     * @return  \Yana\Security\IsFacade
     */
    protected function _getSecurityFacade()
    {
        return $this->_getApplication()->getSecurity();
    }

    /**
     * @return  \Yana\Plugins\Manager
     */
    protected function _getPluginsFacade()
    {
        return $this->_getApplication()->getPlugins();
    }

    /**
     * Create and return microsummary.
     *
     * @return \Yana\Util\Microsummary
     */
    protected function _getMicrosummary()
    {
        if (!isset($this->_microsummary)) {
            $connection = $this->_connectToDatabase('microsummary');
            $this->_microsummary = new \Yana\Util\Microsummary($connection);
        }
        return $this->_microsummary;
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

        return $this->_getApplication()->connect($schema);
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