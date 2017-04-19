<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana\Core\Dependencies;

/**
 * Dependency container for the application class.
 *
 * @package     yana
 * @subpackage  core
 */
class Container extends \Yana\Core\Object
{

    /**
     * System configuration file
     *
     * @var  \Yana\Util\XmlArray
     */
    private $_configuration = null;

    /**
     * profile id
     *
     * @var  string
     */
    private $_id = null;

    /**
     * action parameter
     *
     * @var  string
     */
    private $_action = null;

    /**
     * to communicate with plugins
     *
     * @var  \Yana\Plugins\Manager
     */
    private $_plugins = null;

    /**
     * to load language strings
     *
     * @var  \Yana\Translations\Facade
     */
    private $_language = null;

    /**
     * to load skins and templates
     *
     * @var  \Yana\Views\Skins\Skin
     */
    private $_skin = null;

    /**
     * to read and write data to the global registry
     *
     * @var  \Yana\VDrive\Registry
     */
    private $_registry = null;

    /**
     * to read and write user data and permissions
     *
     * @var  \Yana\Security\Data\SessionManager
     */
    private $_session = null;

    /**
     * the currently selected template
     *
     * @var  \Yana\Views\Managers\IsManager
     */
    private $_view = null;

    /**
     * Collection of logger classes.
     *
     * @var  \Yana\Log\LoggerCollection
     */
    private $_loggers = null;

    /**
     * Tracks and prepares output messages.
     *
     * @var  \Yana\Log\ExceptionLogger
     */
    private $_exceptionLogger = null;

    /**
     * file cache in temporary directory
     *
     * @var  \Yana\Data\Adapters\IsDataAdapter
     */
    private $_cache = null;

    /**
     * safe-mode settings
     *
     * false = default-mode (use profile settings)
       true  = safe-mode    (use default profile)
     *
     * @var  bool
     */
    private $_isSafeMode = null;

    /**
     *
     * @var  \Yana\Http\Facade
     */
    private $_request = null;

    /**
     * Creates an instance.
     */
    public function __construct(\Yana\Util\XmlArray $configuration)
    {
        $this->_configuration = $configuration;
    }

    /**
     * Builds and returns request object.
     *
     * By default this will be done by using the respective super-globals like $_GET, $_POST aso.
     *
     * @return  \Yana\Http\Facade
     */
    public function getRequest()
    {
        if (!isset($this->_request)) {
            $this->_request = new \Yana\Http\Facade();
        }
        return $this->_request;
    }

    /**
     * Get the application cache.
     *
     * By default this will be a file-cache in the temporary directory of the framework.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    public function getCache()
    {
        if (!isset($this->_cache)) {
            if (!empty($this->_configuration->tempdir) && is_dir((string) $this->_configuration->tempdir)) {
                $temporaryDirectory = new \Yana\Files\Dir((string) $this->_configuration->tempdir);
                $this->_cache = new \Yana\Data\Adapters\FileCacheAdapter($temporaryDirectory);
            } else {
                $this->_cache = new \Yana\Data\Adapters\ArrayAdapter();
            }
        }
        return $this->_cache;
    }

    /**
     * Get exception logger.
     *
     * Builds and returns a class that converts exceptions to messages and passes them as var
     * "STDOUT" to a var-container for output in a template or on the command line.
     *
     * @return  \Yana\Log\ExceptionLogger
     */
    public function getExceptionLogger()
    {
        if (!isset($this->_exceptionLogger)) {
            $languageManager = $this->getLanguage();
            $languageManager->loadTranslations('message');
            $this->_exceptionLogger = new \Yana\Log\ExceptionLogger($languageManager->getTranslations());
        }
        return $this->_exceptionLogger;
    }

    /**
     * Get current action.
     *
     * @internal This also checks the action parameter for validity.
     *
     * Work-around for IE-bug.
     *
     * Example:
     * <code>
     * <form><button type="submit" name="a" value="1">2</button></form>
     * </code>
     *
     * IE sends a=2 instead of a=1. This is because IE automatically
     * handles button-tags as input tags and copies the caption text
     * to the value attribute. This is WRONG according to W3C.
     *
     * Solution:
     * <code>
     * <form><input type="submit" name="a[1]" value="2"/></form>
     * if ($a[1]) $a = 1;
     * </code>
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\InvalidActionException  when the event is undefined
     */
    protected function _getAction()
    {
        if (!isset($this->_action)) {
            $action = $this->getRequest()->all()->value('action')->asSafeString();
            // work-around for IE-bug
            if (is_array($action) && count($action) === 1) {
                // action[name]=1 -> action=name
                reset($action); // rewind iterator
                $action = key($action); // get first key
            }
            // error checking
            switch (true)
            {
                case isset($action) && !is_string($action):
                case isset($action) && !$this->getPlugins()->isEvent($action):
                    $error = new \Yana\Core\Exceptions\InvalidActionException();
                    $error->setAction($action);
                // fall through
                case empty($action):
                    assert('!empty($this->_configuration->default->homepage); // Configuration missing default homepage.');
                    $action = (string) $this->_configuration->default->homepage;
                // fall through
                default:
                    $action = mb_strtolower($action);
                break;
            }
            $this->_action = $action;
        }
        return $this->_action;
    }

    /**
     * Get session manager instance.
     *
     * The SessionManager class is used to manage user information
     * and resolve permissions.
     * 
     * @return \Yana\Security\Data\SessionManager
     */
    public function getSession()
    {
        if (!isset($this->_session)) {
            $this->_session = \Yana\Security\Data\SessionManager::getInstance();
        }
        return $this->_session;
    }

    /**
     * Application is in safe-mode.
     *
     * @return  bool
     */
    protected function _isSafemode()
    {
        if (!isset($this->_isSafemode)) {
            $eventConfiguration = $this->getPlugins()->getEventConfiguration($this->_getAction());
            if ($eventConfiguration instanceof \Yana\Plugins\Configs\MethodConfiguration) {
                $this->_isSafeMode = ($eventConfiguration->getSafemode() === true);
            } else {
                $this->_isSafeMode =
                    !empty($this->_configuration->default->event->{\Yana\Plugins\Annotations\Enumeration::SAFEMODE});
            }
        }
        return $this->_isSafeMode;
    }

    /**
     * Get registry.
     *
     * This returns the registry. If none exists, a new instance is created.
     * These settings may be read later by using \Yana\Application::getVar().
     *
     * @return  \Yana\VDrive\IsRegistry
     * @throws  \Yana\Core\Exceptions\NotReadableException    when Registry file is not readable
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when Registry file could not be read or contains invalid syntax
     */
    public function getRegistry()
    {
        if (!isset($this->_registry)) {
            // path to cache file
            $cacheFile = (string) $this->_configuration->tempdir . 'registry_' . $this->getId() . '.tmp';

            // get configuration mode
            \Yana\VDrive\Registry::useDefaults($this->_isSafemode());

            if (YANA_CACHE_ACTIVE === true && file_exists($cacheFile)) {
                $this->_registry = unserialize(file_get_contents($cacheFile));
                assert($this->_registry instanceof \Yana\VDrive\IsRegistry);
            } else {
                $this->_registry = new \Yana\VDrive\Registry((string) $this->_configuration->configdrive, "");
                $this->_registry->setVar("ID", self::getId());
                $this->_registry->mergeVars('*', \Yana\Util\Hashtable::changeCase($this->_configuration->toArray(), \CASE_UPPER));
            }
            $request = $this->getRequest();
            $this->_registry->mergeVars('*', $request->all()->asArrayOfStrings());
            $this->_registry->setAsGlobal();

            // set user name
            if (!empty($_SESSION['user_name'])) {
                $this->_registry->setVar("SESSION_USER_ID", $_SESSION['user_name']);
            }

            // set CD-ROM temp-dir
            if (YANA_CDROM === true) {
                $this->_registry->setVar('YANA_CDROM_DIR', YANA_CDROM_DIR);
            }

            $this->_registry->read();

            // create cache file
            if (YANA_CACHE_ACTIVE === true && !file_exists($cacheFile)) {
                file_put_contents($cacheFile, serialize($this->_registry));
            }

            if (!$request->all()->isEmpty('page')) {
                $this->_registry->setVar('PAGE', $request->all()->value('page')->asInt());
            }
            if (!$request->all()->isEmpty('target')) {
                $this->_registry->setVar('TARGET', $request->all()->value('target')->asSafeString());
            }
            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $this->_registry->setVar('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
            } else {
                $this->_registry->setVar('REMOTE_ADDR', '0.0.0.0');
            }
            if (!empty($_SERVER['HTTP_REFERER'])) {
                $referer = preg_replace("/(.*\/).*(\?.*)?/", "\\1", $_SERVER['HTTP_REFERER']);
                $this->_registry->setVar("REFERER", $referer);
            }
        }
        return $this->_registry;
    }

    /**
     * Get plugin-manager.
     *
     * This returns the plugin manager. If none exists, a new instance is created.
     * The pluginManager holds repositories for interfaces and implementations of plugins.
     *
     * @return  \Yana\Plugins\Manager
     */
    public function getPlugins()
    {
        if (!isset($this->_plugins)) {
            $cacheFile = YANA_INSTALL_DIR . (string) $this->_configuration->plugincache;

            if (YANA_CACHE_ACTIVE === true && file_exists($cacheFile)) {
                $this->_plugins = unserialize(file_get_contents($cacheFile));
                assert($this->_plugins instanceof \Yana\Plugins\Manager);

            } else {
                $this->_plugins = \Yana\Plugins\Manager::getInstance();
//                if (!is_file(\Yana\Plugins\Manager::getConfigFilePath())) {
//                    $this->_plugins->refreshPluginFile();
//                }
                file_put_contents($cacheFile, serialize($this->_plugins));
            }
        }
        return $this->_plugins;
    }

    /**
     * Get view.
     *
     * This returns the view component. If none exists, a new instance is created.
     * This is an auxiliary class that provides access to output-specific functions.
     *
     * @return  \Yana\Views\Managers\IsManager
     */
    public function getView()
    {
        if (!isset($this->_view)) {
            $factory = new \Yana\Views\EngineFactory($this->_configuration->templates);
            $this->_view = $factory->createInstance();
            $this->getRegistry()->setVar("ACTION", $this->_getAction());
        }
        return $this->_view;
    }

    /**
     * Get language translation-repository.
     *
     * This returns the language component. If none exists, a new instance is created.
     *
     * @return  \Yana\Translations\Facade
     */
    public function getLanguage()
    {
        if (!isset($this->_language)) {
            $languageDir = $this->getRegistry()->getVar('LANGUAGEDIR');
            $defaultProvider = new \Yana\Translations\TextData\XliffDataProvider(new \Yana\Files\Dir($languageDir));
            $this->_language = \Yana\Translations\Facade::getInstance();
            $this->_language->addTextDataProvider($defaultProvider);
            unset($defaultProvider);

            $this->_language->setLocale((string) $this->_configuration->default->language);
            if (isset($_SESSION['language'])) {
                try {
                    $this->_language->setLocale($_SESSION['language']);
                } catch (\Yana\Core\Exceptions\InvalidArgumentException $e){
                    unset($_SESSION['language']);
                }
            }
            try {
                $this->_language->loadTranslations('default');
            } catch (\Yana\Core\Exceptions\InvalidArgumentException $e){
                unset($_SESSION['language']);
            }
            $array = array();
            foreach (glob("$languageDir*", GLOB_ONLYDIR) as $dir)
            {
                $array[basename($dir)] = 1;
            }
            $this->setVar('INSTALLED_LANGUAGES', $array);
            if (isset($_SESSION['language'])) {
                $this->setVar('SELECTED_LANGUAGE', $_SESSION['language']);
            }
        }
        return $this->_language;
    }

    /**
     * get skin
     *
     * This returns the skin component. If none exists, a new instance is created.
     *
     * @return  \Yana\Views\Skins\IsSkin
     */
    public function getSkin()
    {
        if (!isset($this->_skin)) {
            $registry = $this->getRegistry();
            $registry->mount('system:/skincache.text');
            $cacheFile = $registry->getResource('system:/skincache.text');

            if (YANA_CACHE_ACTIVE === true && $cacheFile->exists()) {
                assert('!isset($skin); // Cannot redeclare var $skin');
                $this->_skin = unserialize(file_get_contents($cacheFile->getPath()));
                assert($this->_skin instanceof \Yana\Views\Skins\IsSkin);

            } else {
                $this->_skin = new \Yana\Views\Skins\Skin($this->getVar('PROFILE.SKIN'));

                if (YANA_CACHE_ACTIVE === true) {
                    $cacheFile->create();
                    $cacheFile->setContent(serialize($this->_skin));
                    $cacheFile->write();
                }
            }
        }
        return $this->_skin;
    }

    /**
     * Get current profile id.
     *
     * Returns the id of the profile the data of the current profile is to be associated with.
     *
     * This is a shortcut for $YANA->getVar('ID').
     * However it is important to note a slight difference.
     * <ul>
     *   <li> $YANA->getVar('ID'):
     *     This value is available to all plugins and all 
     *     of them may read AND write this setting as the
     *     developer sees fit.
     *     This may mean, that this setting has been subject
     *     to changes by some plugin, e.g. to switch between
     *     profiles.
     *   </li>
     *   <li> $container->getId():
     *     Always returns the original value, regardless of
     *     changes by plugins.
     *   </li>
     * </ul>
     *
     * You may want to decide for the behaviour you prefer
     * and choose either one or the other.
     *
     * @return  string
     */
    public function getId()
    {
        if (!isset($this->_id)) {
            if (!$this->getRequest()->all()->value('id')->isEmpty()) {
                $this->_id = \Yana\Util\String::toLowerCase($this->getRequest()->all()->value('id')->asSafeString());

            } elseif (!empty($this->_configuration->default->profile)) {
                $this->_id = (string) $this->_configuration->default->profile;

            } else {
                $this->_id = 'default';
            }
        }
        return $this->_id;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger()
    {
        if (!isset($this->_loggers)) {
            $this->_loggers = new \Yana\Log\LoggerCollection();
        }
        return $this->_loggers;
    }

}

?>