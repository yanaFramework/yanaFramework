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
class Container extends \Yana\Core\Object implements \Yana\Core\Dependencies\IsApplicationContainer
{

    /**
     * System configuration file
     *
     * @var  \Yana\Util\IsXmlArray
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
     * @var  \Yana\VDrive\IsRegistry
     */
    private $_registry = null;

    /**
     * to read and write user data and permissions
     *
     * @var  \Yana\Security\IsFacade
     */
    private $_security = null;

    /**
     * the currently selected template
     *
     * @var  \Yana\Views\Managers\IsManager
     */
    private $_view = null;

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
     * true  = safe-mode    (use default profile)
     *
     * @var  bool
     */
    private $_isSafeMode = null;

    /**
     * @var  \Yana\Http\Facade
     */
    private $_request = null;

    /**
     * @var  \Yana\Security\Sessions\IsWrapper
     */
    private $_session = null;

    /**
     * @var  \Yana\Plugins\Menus\Builder
     */
    private $_menuBuilder = null;

    /**
     * <<constructor>> Creates an instance.
     *
     * @param  \Yana\Util\IsXmlArray  $configuration  loaded from XML file in config-directory
     */
    public function __construct(\Yana\Util\IsXmlArray $configuration)
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
            $tempDir = $this->_getPathToCacheDirectory();
            if (YANA_CACHE_ACTIVE === true && is_dir($tempDir)) {
                $temporaryDirectory = new \Yana\Files\Dir($tempDir);
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
    public function getAction()
    {
        if (!isset($this->_action)) {
            $action = $this->getRequest()->getActionArgument();

            // error checking
            switch (true)
            {
                case !$this->getPlugins()->isEvent($action):
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
     * Get security facade.
     *
     * This facade is used to manage user information and check permissions.
     * 
     * @return \Yana\Security\IsFacade
     */
    public function getSecurity()
    {
        if (!isset($this->_security)) {
            $container = new \Yana\Security\Dependencies\Container($this->getPlugins());
            $container
                    ->setCache($this->getCache())
                    ->setSession($this->getSession())
                    ->setDefaultUser($this->getDefault('user'))
                    ->setProfileId($this->getProfileId());
            $this->_security = new \Yana\Security\Facade($container);
        }
        return $this->_security;
    }

    /**
     * Application is in safe-mode.
     *
     * @return  bool
     */
    public function isSafemode()
    {
        if (!isset($this->_isSafemode)) {
            $eventConfiguration = $this->getPlugins()->getEventConfiguration($this->getAction());
            if ($eventConfiguration instanceof \Yana\Plugins\Configs\IsMethodConfiguration) {
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
            $cacheId = (string) 'registry_' . $this->getProfileId();

            // get configuration mode
            \Yana\VDrive\Registry::useDefaults($this->isSafemode());
            $cache = $this->getCache();

            if (isset($cache[$cacheId])) {
                $this->_registry = $cache[$cacheId];
                assert($this->_registry instanceof \Yana\VDrive\IsRegistry);
            } else {
                $this->_registry = new \Yana\VDrive\Registry((string) $this->_configuration->configdrive, YANA_INSTALL_DIR);
                $this->_registry->setVar("ID", $this->getProfileId());
                $this->_registry->mergeVars('*', \Yana\Util\Hashtable::changeCase($this->_configuration->toArray(), \CASE_UPPER));
            }
            $request = $this->getRequest();
            $this->_registry->mergeVars('*', $request->all()->asArrayOfStrings());
            $this->_registry->setAsGlobal();

            // set user name
            $session = $this->getSession();
            if (!empty($session['user_name'])) {
                $this->_registry->setVar("SESSION_USER_ID", $session['user_name']);
            }
            unset($session);

            // set CD-ROM temp-dir
            if (YANA_CDROM === true) {
                $this->_registry->setVar('YANA_CDROM_DIR', YANA_CDROM_DIR);
            }

            $this->_registry->read();

            // create cache file
            $cache[$cacheId] = $this->_registry;

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
            $cacheId = 'pluginmanager';
            $cache = $this->getCache();

            if (isset($cache[$cacheId])) {
                $this->_plugins = $cache[$cacheId];
                assert($this->_plugins instanceof \Yana\Plugins\Manager);

            } else {
                $this->_plugins = \Yana\Plugins\Manager::getInstance();
                $container = new \Yana\Plugins\Dependencies\Container($this->getSession(), $this->_getDefaultEvent());
                $this->_plugins->attachDependencies($container);
                $this->_plugins->attachLogger($this->getLogger());
                if (!\Yana\Plugins\Manager::getConfigFilePath()->exists()) {
                    $this->_plugins->refreshPluginFile();
                }
                $cache[$cacheId] = $this->_plugins;
            }
        }
        return $this->_plugins;
    }

    /**
     * Gets settings for default event configuration and converts them to an array.
     *
     * @return  array
     */
    private function _getDefaultEvent()
    {
        $defaultEvent = $this->getDefault('EVENT');
        if ($defaultEvent instanceof \Yana\Util\IsXmlArray) {
            $defaultEvent = $defaultEvent->toArray();
        }
        if (!\is_array($defaultEvent)) {
            $defaultEvent = array();
        }
        return $defaultEvent;
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
            $this->getRegistry()->setVar("ACTION", $this->getAction());
        }
        return $this->_view;
    }

    /**
     * Get language translation-repository.
     *
     * This returns the language component. If none exists, a new instance is created.
     *
     * @return  \Yana\Translations\IsFacade
     */
    public function getLanguage()
    {
        if (!isset($this->_language)) {
            $this->_language = $this->_buildNewTranslationFacade();
        }
        return $this->_language;
    }

    /**
     * Builds and returns a new translation facade.
     *
     * @return  \Yana\Translations\IsFacade
     */
    protected function _buildNewTranslationFacade()
    {
        $registry = $this->getRegistry();
        $languageDir = (string) $registry->getVar('LANGUAGEDIR');
        /* @var $translationFacade \Yana\Translations\Facade */
        $translationFacade = \Yana\Translations\Facade::getInstance();
        $translationFacade->addDirectory($languageDir);
        $translationFacade->attachLogger($this->getLogger());

        $translationFacade->setLocale((string) $this->_configuration->default->language);
        $session = $this->getSession();
        if (isset($session['language'])) {
            try {
                $translationFacade->setLocale((string) $session['language']);
            } catch (\Yana\Core\Exceptions\InvalidArgumentException $e){
                unset($session['language']);
            }
        }
        try {
            $translationFacade->loadTranslations('default');
        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e){
            unset($session['language']);
        }
        $array = array();
        foreach (glob($languageDir . "*", GLOB_ONLYDIR) as $dir)
        {
            $array[basename($dir)] = 1;
        }
        $registry->setVar('INSTALLED_LANGUAGES', $array);
        if (isset($session['language'])) {
            $registry->setVar('SELECTED_LANGUAGE', (string) $session['language']);
        }
        return $translationFacade;
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
            assert('!isset($registry); // Cannot redeclare var $registry');
            $registry = $this->getRegistry();
            assert('!isset($cache); // Cannot redeclare var $cache');
            $cache = $this->getCache();
            assert('!isset($cacheId); // Cannot redeclare var $cacheId');
            $cacheId = 'skin_' . (string) $registry->getVar('PROFILE.SKIN');

            if (isset($cache[$cacheId])) {
                $this->_skin = $cache[$cacheId];
                assert($this->_skin instanceof \Yana\Views\Skins\IsSkin);

            } else {
                $this->_skin = new \Yana\Views\Skins\Skin((string) $registry->getVar('PROFILE.SKIN'));
                $cache[$cacheId] = $this->_skin;
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
     *   <li> $container->getProfileId():
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
    public function getProfileId()
    {
        if (!isset($this->_id)) {
            $id = $this->getRequest()->getProfileArgument();
            if ($id > "") {
                $this->_id = $id;

            } elseif (!empty($this->_configuration->default->profile)) {
                $this->_id = (string) $this->_configuration->default->profile;

            } else {
                $this->_id = 'default';
            }
        }
        return $this->_id;
    }

    /**
     * Returns the attached logger.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger()
    {
        return \Yana\Log\LogManager::getLogger();
    }

    /**
     * Get default configuration value.
     *
     * Returns the default value for a given var if any,
     * returns NULL (not false!) if there is none.
     *
     * Example 1:
     * <code>
     * \Yana\Application::getDefault('CONTAINER1.CONTAINER2.DATA');
     * </code>
     *
     * Example 2:
     * <code>
     * if (!isset($foo)) {
     *     $foo = \Yana\Application::getDefault('FOO');
     * }
     * </code>
     *
     * Note: system default values are typically defined in the
     * 'default' section of the 'config/system.config' configurations file.
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     */
    public function getDefault($key)
    {
        assert('is_scalar($key); // Invalid argument $key: scalar expected');
        $result = null;
        if (isset($this->_configuration->default)) {
            $key = mb_strtolower("$key");
            if (isset($this->_configuration->default->$key)) {
                $result = $this->_configuration->default->$key;
            } else {
                $values = $this->_configuration->default;
                foreach (explode('.', $key) as $i)
                {
                    if (!isset($values->$i)) {
                        return null;
                    }
                    $values = $values->$i;
                }
                unset($i);
                $result = $values;
            }
        }
        if ($result instanceof \Yana\Util\IsXmlArray) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * Creates and returns an application menu builder.
     *
     * @param   \Yana\Application  $application  necessary to initialize dependency container
     * @return  \Yana\Plugins\Menus\IsCacheableBuilder
     */
    public function getMenuBuilder(\Yana\Application $application)
    {
        if (!isset($this->_menuBuilder)) {
            $container = new \Yana\Plugins\Dependencies\MenuContainer($application);
            $this->_menuBuilder = new \Yana\Plugins\Menus\Builder($container);
            $this->_menuBuilder->attachLogger($this->getLogger());
            $this->_menuBuilder->setLocale($this->getLanguage()->getLocale());
        }
        return $this->_menuBuilder;
    }

    /**
     * Returns ... well, the path to the cache directory.
     *
     * The cache directory is defined in the application configuration.
     * If it is not, this will assume that there is a directory called "cache" in the application root.
     *
     * This function will always add a "/" path delimiter to make sure the returned path has one.
     * Note! This doesn't check if that directoy actually exists.
     *
     * @return  string
     */
    protected function _getPathToCacheDirectory()
    {
        $tempDir = 'cache';
        if (isset($this->_configuration->tempdir)) {
            $tempDir = (string) $this->_configuration->tempdir;
        }
        if ($tempDir !== '/' and \strlen($tempDir) > 1) {
            $tempDir .= '/';
        }
        return $tempDir;
    }

}

?>