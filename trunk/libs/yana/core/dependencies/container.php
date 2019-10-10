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
declare(strict_types=1);

namespace Yana\Core\Dependencies;

/**
 * Dependency container for the application class.
 *
 * @package     yana
 * @subpackage  core
 */
class Container extends \Yana\Core\StdObject implements \Yana\Core\Dependencies\IsApplicationContainer
{

    use \Yana\Core\Dependencies\HasSecurity, \Yana\Core\Dependencies\HasPlugin, \Yana\Core\Dependencies\HasRequest;

    /**
     * System configuration file
     *
     * @var  \Yana\Util\IsXmlObject
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
     * @var  \Yana\Plugins\Facade
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
    private $_applicationCache = null;

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
     * @var  \Yana\Plugins\Menus\Builder
     */
    private $_menuBuilder = null;

    /**
     * @var  \Yana\Plugins\Data\IsAdapter
     */
    private $_pluginAdapter = null;

    /**
     * @var  \Yana\Db\IsConnectionFactory
     */
    private $_connectionFactory = null;

    /**
     * @var  \Yana\Views\Icons\IsLoader
     */
    private $_iconLoader = null;

    /**
     * @var  \Yana\Plugins\Configs\MethodCollection
     */
    private $_eventConfigurationsForPlugins = null;

    /**
     * <<constructor>> Creates an instance.
     *
     * @param  \Yana\Util\IsXmlObject  $configuration  loaded from XML file in config-directory
     */
    public function __construct(\Yana\Util\Xml\IsObject $configuration)
    {
        $this->_configuration = $configuration;
    }

    /**
     * Get the application cache.
     *
     * By default this will be a file-cache in the temporary directory of the framework.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    public function getCache(): \Yana\Data\Adapters\IsDataAdapter
    {
        if (!isset($this->_applicationCache)) {
            $tempDir = $this->_getPathToCacheDirectory();
            if (YANA_CACHE_ACTIVE === true && is_dir($tempDir)) {
                // @codeCoverageIgnoreStart
                $temporaryDirectory = new \Yana\Files\Dir($tempDir);
                $this->_applicationCache = new \Yana\Data\Adapters\FileCacheAdapter($temporaryDirectory);
                // @codeCoverageIgnoreEnd
            } else {
                $this->_applicationCache = new \Yana\Data\Adapters\ArrayAdapter();
            }
        }
        return $this->_applicationCache;
    }

    /**
     * Get exception logger.
     *
     * Builds and returns a class that converts exceptions to messages and passes them as var
     * "STDOUT" to a var-container for output in a template or on the command line.
     *
     * @return  \Yana\Log\IsLogger
     */
    public function getExceptionLogger(): \Yana\Log\IsLogger
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
    public function getAction(): string
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
                    assert(!empty($this->_configuration->default->homepage), 'Configuration missing default homepage.');
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
     * Get security facade.
     *
     * This facade is used to manage user information and check permissions.
     * 
     * @return \Yana\Security\IsFacade
     */
    public function getSecurity(): \Yana\Security\IsFacade
    {
        if (!isset($this->_security)) {
            $this->_security = new \Yana\Security\Facade($this);
        }
        return $this->_security;
    }

    /**
     * Application is in safe-mode.
     *
     * @return  bool
     */
    public function isSafemode(): bool
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
     * XML default configuration object.
     *
     * @return  \Yana\Util\Xml\IsObject
     */
    public function getDefaultConfiguration(): \Yana\Util\Xml\IsObject
    {
        return $this->_configuration->default;
    }

    /**
     * XML template configuration object.
     *
     * @return  \Yana\Util\Xml\IsObject
     */
    public function getTemplateConfiguration(): \Yana\Util\Xml\IsObject
    {
        return $this->_configuration->templates;
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
    public function getRegistry(): \Yana\VDrive\IsRegistry
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
                $configurationArray = \Yana\Util\Xml\Converter::convertObjectToAssociativeArray($this->_configuration);
                $this->_registry->mergeVars('*', \Yana\Util\Hashtable::changeCase($configurationArray, \CASE_UPPER));
            }
            $request = $this->getRequest();
            $this->_registry->mergeVars('*', $request->all()->asArrayOfStrings());
            $this->_registry->setAsGlobal();

            // set user name
            $session = $this->getSession();
            if ($this->getSession()->getCurrentUserName()) {
                $this->_registry->setVar("SESSION_USER_ID", $this->getSession()->getCurrentUserName());
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
                $targetValue = $request->all()->value('target');
                $sanitizedTarget = $targetValue->isScalar() ? $targetValue->asSafeString() : $targetValue->asArrayOfSafeStrings();
                $this->_registry->setVar('TARGET', $sanitizedTarget);
                unset($targetValue, $sanitizedTarget);
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
     * The plugin facade holds repositories for interfaces and implementations of plugins.
     *
     * @return  \Yana\Plugins\Facade
     */
    public function getPlugins(): \Yana\Plugins\Facade
    {
        if (!isset($this->_plugins)) {
            $cacheId = 'plugins';
            $cache = $this->getCache();

            if (isset($cache[$cacheId])) {
                $this->_plugins = $cache[$cacheId];
                assert($this->_plugins instanceof \Yana\Plugins\Facade);

            } else {
                $this->_plugins = new \Yana\Plugins\Facade($this);
                $this->_plugins->attachLogger($this->getLogger());
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
    public function getDefaultEvent(): array
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
    public function getView(): \Yana\Views\Managers\IsManager
    {
        if (!isset($this->_view)) {
            $factory = new \Yana\Views\EngineFactory($this);
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
    public function getLanguage(): \Yana\Translations\IsFacade
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
    protected function _buildNewTranslationFacade(): \Yana\Translations\IsFacade
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
        } catch (\Yana\Core\Exceptions\Translations\LanguageFileNotFoundException $e){
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
    public function getSkin(): \Yana\Views\Skins\IsSkin
    {
        if (!isset($this->_skin)) {
            assert(!isset($registry), 'Cannot redeclare var $registry');
            $registry = $this->getRegistry();
            assert(!isset($cache), 'Cannot redeclare var $cache');
            $cache = $this->getCache();
            assert(!isset($cacheId), 'Cannot redeclare var $cacheId');
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
    public function getProfileId(): string
    {
        if (!isset($this->_id)) {
            $id = $this->isSafemode() ? '' : $this->getRequest()->getProfileArgument();
            if ($id > "") {
                // @codeCoverageIgnoreStart

                // trivial - no need to test this
                $this->_id = $id; // We get here if the profile id is passed via the URL, which is the case most of the time
                // @codeCoverageIgnoreEnd

            } elseif (!empty($this->_configuration->default->profile)) {
                $this->_id = (string) $this->_configuration->default->profile; // Default profile id, in case nothing else is provided

            } else {
                // @codeCoverageIgnoreStart
                $this->_id = 'default'; // This is just a fallback, and should be unreachable (unless the configuration file is bust)
                // @codeCoverageIgnoreEnd
            }
        }
        return $this->_id;
    }

    /**
     * Returns a string containing profile and session parameters.
     *
     * Looks like this: ?id={profileId}&{sessionName}={sessionId}.
     *
     * If the client accepts session cookies, the session information is not included.
     *
     * @return string
     */
    public function getApplicationUrlParameters(): string
    {
        $baseUrl = "?id=" . $this->getProfileId();
        if (empty($_COOKIE) && session_status() === \PHP_SESSION_ACTIVE) {
            // @codeCoverageIgnoreStart
            $baseUrl .= "&" . session_name() . "=" . session_id();
            // @codeCoverageIgnoreEnd
        }
        return $baseUrl;
    }

    /**
     * Returns the attached logger.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger(): \Yana\Log\IsLogHandler
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
     * @param   scalar  $key  adress of data in memory (case insensitive)
     * @return  mixed
     */
    public function getDefault($key)
    {
        assert(is_scalar($key), 'Invalid argument $key: scalar expected');
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
        if ($result instanceof \Yana\Util\Xml\IsObject) {
            $result = \Yana\Util\Xml\Converter::convertObjectToAssociativeArray($result);
        }
        return $result;
    }

    /**
     * Creates and returns an application menu builder.
     *
     * @return  \Yana\Plugins\Menus\IsCacheableBuilder
     */
    public function getMenuBuilder(): \Yana\Plugins\Menus\IsCacheableBuilder
    {
        if (!isset($this->_menuBuilder)) {
            $container = new \Yana\Plugins\Dependencies\MenuContainer($this);
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
    protected function _getPathToCacheDirectory(): string
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

    /**
     * Returns a ready-to-use factory to create open database connections.
     *
     * @return  \Yana\Db\IsConnectionFactory
     */
    public function getConnectionFactory(): \Yana\Db\IsConnectionFactory
    {
        if (!isset($this->_connectionFactory)) {
            $this->_connectionFactory = new \Yana\Db\ConnectionFactory(new \Yana\Db\SchemaFactory($this->getCache()));
        }
        return $this->_connectionFactory;
    }

    /**
     * Returns the application's default icon loader.
     *
     * @return  \Yana\Views\Icons\IsLoader
     */
    public function getIconLoader(): \Yana\Views\Icons\IsLoader
    {
        if (!isset($this->_iconLoader)) {
            $registry = $this->getRegistry();
            $file = $registry->getResource('system:/smile/config.text');
            $directory = (string) $registry->getVar('PROFILE.SMILEYDIR');
            $dataAdapater = new \Yana\Views\Icons\XmlAdapter($file, $directory);
            $this->_iconLoader = new \Yana\Views\Icons\Loader($dataAdapater);
        }
        return $this->_iconLoader;
    }

    /**
     * Returns the stored list of events for plugins.
     *
     * If none was given, tries to autoload them.
     *
     * @return  \Yana\Plugins\Configs\MethodCollection
     */
    public function getEventConfigurationsForPlugins(): \Yana\Plugins\Configs\MethodCollection
    {
        if (!isset($this->_eventConfigurationsForPlugins)) {
            $this->_eventConfigurationsForPlugins = $this->getPlugins()->getEventConfigurations();
        }
        return $this->_eventConfigurationsForPlugins;
    }

    /**
     * Get action for current request.
     *
     * This is just a shorthand for getPlugins()->getLastEvent().
     *
     * @return  string
     */
    public function getLastPluginAction(): string
    {
        return (string) $this->getPlugins()->getLastEvent();
    }

    /**
     * Get default user settings.
     *
     * @return  array
     */
    public function getDefaultUser(): array
    {
        return $this->getDefault('user');
    }

    /**
     * Lazy-loads and returns a plugin database adapter.
     *
     * @return  \Yana\Plugins\Data\IsAdapter
     */
    public function getPluginAdapter(): \Yana\Plugins\Data\IsAdapter
    {
        if (!isset($this->_pluginAdapter)) {
            $this->_pluginAdapter = new \Yana\Plugins\Data\Adapter($this->getConnectionFactory()->createConnection("plugins"));
        }
        return $this->_pluginAdapter;
    }

}

?>