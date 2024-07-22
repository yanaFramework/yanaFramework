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

namespace Yana;

/**
 * <<Facade>> Application.
 *
 * This is a primary controller and application loader for the Yana Framework.
 * It implements the "facade" pattern and thus delegates calls to underlying classes and methods.
 *
 * Example:
 * <code>
 * // handle request
 * $application->callAction($_REQUEST['action']);
 * // output results
 * $application->outputResults();
 * </code>
 *
 * @package     yana
 * @subpackage  core
 */
final class Application extends \Yana\Core\StdObject implements \Yana\Report\IsReportable, \Yana\Core\IsVarContainer
{
    use \Yana\Core\Dependencies\HasApplicationContainer;

    /**
     * @var  \Yana\Core\Output\IsBehavior
     */
    private $_outputBehavior = null;

    /**
     * Creates and returns output behavior object.
     *
     * @return  \Yana\Core\Output\IsBehavior
     * @codeCoverageIgnore
     */
    protected function _getOutputBehavior(): \Yana\Core\Output\IsBehavior
    {
        if (!isset($this->_outputBehavior)) {
            $this->_outputBehavior = new \Yana\Core\Output\DefaultBehavior($this->_getDependencyContainer());
        }
        return $this->_outputBehavior;
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
        return $this->_getDependencyContainer()->getCache();
    }

    /**
     * execute an action
     *
     * Resolves event and calls plugin(s), with the given arguments.
     *
     * Example:
     * <code>
     * // handle current action
     * $YANA->callAction();
     * // same as above
     * $YANA->callAction($_REQUEST['action'], $_REQUEST);
     * // handle user defined event 'test'
     * $myArgs = array('foo' => 'bar');
     * $success = $YANA->callAction('test', $myArgs);
     * if ($success) {
     *     print "Success!\n";
     * } else {
     *     print "Encountered an error.\n";
     * }
     * </code>
     *
     * @param   string  $action  script action parameter
     * @param   array   $args    array of passed arguments
     * @return  bool
     * @throws  \Yana\Core\Exceptions\InvalidActionException  when the event is undefined
     */
    public function execute(string $action = "", ?array $args = null): bool
    {
        /**
         * 1) check for default arguments
         */
        if (empty($action)) {
            $action = $this->_getDependencyContainer()->getAction();
        }
        if (is_null($args)) {
            $args = $this->_getDependencyContainer()->getRequest()->all()->asArrayOfStrings();
        }

        /**
         * 2) load language strings
         */
        assert(!isset($eventConfiguration), 'Cannot redeclare var $eventConfiguration');
        assert(!isset($plugins), 'Cannot redeclare var $plugins');
        $plugins = $this->getPlugins();
        $eventConfiguration = $plugins->getEventConfiguration($action);
        if (!($eventConfiguration instanceof \Yana\Plugins\Configs\IsMethodConfiguration)) {
            $error = new \Yana\Core\Exceptions\InvalidActionException();
            $error->setAction($action);
            return false;
        }

        assert(!isset($paths), 'Cannot redeclare var $paths');
        $paths = $eventConfiguration->getPaths();
        if ($paths) {
            assert(!isset($language), 'Cannot redeclare var $language');
            $language = $this->getLanguage();
            // mount language directory, if it exists
            assert(!isset($langDir), 'Cannot redeclare var $langDir');
            foreach ($eventConfiguration->getPaths() as $langDir)
            {
                $langDir = $langDir . "/languages/";
                if (is_dir($langDir)) {
                    $language->addDirectory(new \Yana\Files\Dir($langDir));
                }
            }
            unset($langDir, $language);
        }
        unset($paths);
        // load language files
        assert(!isset($languages), 'Cannot redeclare var $languages');
        $languages = $eventConfiguration->getLanguages();
        if ($languages) {
            assert(!isset($language), 'Cannot redeclare var $language');
            $language = $this->getLanguage();
            assert(!isset($languageId), 'Cannot redeclare var $languageId');
            foreach ($languages as $languageId)
            {
                try {

                    $language->readFile($languageId); // may throw exception

                } catch (\Yana\Core\Exceptions\Translations\TranslationException $e) {
                    // log the issue so that it can be reviewed later
                    $message = $e->getMessage();
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    $data = $e->getData();
                    $this->getLogger()->addLog($message, $level, $data);
                    unset($e);
                    // We may safely continue without the file for now
                }
            }
            unset($language, $languageId);
        }
        unset($languages);

        /**
         * 3) handle event
         *
         * Returns bool(true) on success and bool(false) otherwise.
         */
        try {

            $result = $plugins->sendEvent($action, $args, $this);
            if ($result !== false) {
                /* Create timestamp to provide information for read-stability isolation level */
                $_SESSION['transaction_isolation_created'] = time();
            }

            // @codeCoverageIgnoreStart
        } catch (\Yana\Core\Exceptions\IsException $e) {
            $result = false;
            $this->_getDependencyContainer()->getExceptionLogger()->addException($e);

        } catch (\Exception $e) {
            $result = false;
            $message = get_class($e) . ': ' . $e->getMessage() . ' Thrown in ' . $e->getFile() .
                ' on line ' . $e->getLine();
            $this->getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);

        }
        // @codeCoverageIgnoreEnd
        return $result !== false;
    }

    /**
     * Access security sub-system.
     *
     * This facade is a front that provides simplified access to the various security features
     * the framework supports out-of-the-box.
     * 
     * @return \Yana\Security\IsFacade
     */
    public function getSecurity(): \Yana\Security\IsFacade
    {
        return $this->_getDependencyContainer()->getSecurity();
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
        return $this->_getDependencyContainer()->getRegistry();
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
        return $this->_getDependencyContainer()->getPlugins($this);
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
        return $this->_getDependencyContainer()->getView();
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
        return $this->_getDependencyContainer()->getLanguage();
    }

    /**
     * Get skin facade.
     *
     * This returns the skin component. If none exists, a new instance is created.
     *
     * @return  \Yana\Views\Skins\IsSkin
     */
    public function getSkin(): \Yana\Views\Skins\IsSkin
    {
        return $this->_getDependencyContainer()->getSkin();
    }

    /**
     * Get current profile id.
     *
     * Returns the id of the profile the data of the current profile is to be associated with.
     *
     * This is a shortcut for $YANA->getVar('ID').
     *
     * However it is important to note a slight difference.
     * <ul>
     *   <li> $YANA->getVar('ID'):
     *     The value you get via  is
     *     available to all plugins and all plugins may read
     *     AND write this setting as the developer sees fit.
     *     This may mean, that this setting has been subject
     *     to changes by some plugin, e.g. to switch between
     *     profiles.
     *   </li>
     *   <li> \Yana\Application::getId():
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
        return $this->_getDependencyContainer()->getProfileId();
    }

    /**
     * Builds and returns request object.
     *
     * By default this will be done by using the respective super-globals like $_GET, $_POST aso.
     *
     * @return  \Yana\Http\IsFacade
     */
    public function getRequest(): \Yana\Http\IsFacade
    {
        return $this->_getDependencyContainer()->getRequest();
    }

    /**
     * Check if a var exists.
     *
     * Returns bool(true) if the key is known and bool(false) otherwise.
     *
     * @param   string  $key  some key (case insensitive)
     * @return  bool
     */
    public function isVar($key)
    {
        assert(is_scalar($key), 'Invalid argument $key: scalar expected');
        $registry = $this->getRegistry();
        return $registry->isVar("$key");
    }

    /**
     * Returns var from registry (memory shared by all plugins).
     *
     * Example:
     * <code>
     * $YANA->setVar('foo.bar', 'Hello World');
     * // outputs 'Hello World'
     * print $YANA->getVar('foo.bar');
     * </code>
     *
     * Returns NULL if there is no such key.
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     * @name    \Yana\Application::getVar()
     * @see     \Yana\Application::setVarByReference()
     * @see     \Yana\Application::setVar()
     */
    public function getVar($key)
    {
        assert(is_scalar($key), 'Invalid argument $key: scalar expected');
        $registry = $this->getRegistry();
        return $registry->getVar("$key");
    }

    /**
     * Returns all vars from the registry (memory shared by all plugins).
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  array
     */
    public function getVars()
    {
        return $this->getRegistry()->getVars();
    }

    /**
     * Sets var on registry by Reference.
     *
     * The "registry" is memory shared by all plugins.
     *
     * Example:
     * <code>
     * $bar = 'Hello';
     * $YANA->setVarByReference('foo.bar', $bar);
     * $bar .= ' World';
     * // outputs 'Hello World'
     * print $YANA->getVar('foo.bar');
     * </code>
     *
     * @param   string  $key     adress of data in memory (case insensitive)
     * @param   mixed   &$value  new value (may be scalar value or array)
     * @return  $this
     * @name    \Yana\Application::setVarByReference()
     * @see     \Yana\Application::setVar()
     * @see     \Yana\Application::getVar()
     */
    public function setVarByReference($key, &$value)
    {
        assert(is_scalar($key), 'Invalid argument $key: scalar expected');
        $this->getRegistry()->setVarByReference((string) $key, $value);
        return $this;
    }

    /**
     * Replace all vars in the global registry by reference.
     *
     * @param   array  &$values  new set of values
     * @return  $this
     */
    public function setVarsByReference(array &$values)
    {
        $this->getRegistry()->setVarsByReference($values);
        return $this;
    }

    /**
     * sets var on registry
     *
     * The "registry" is memory shared by all plugins.
     *
     * Example:
     * <code>
     * $YANA->setVar('foo.bar', 'Hello World');
     * // outputs 'Hello World'
     * print $YANA->getVar('foo.bar');
     * </code>
     *
     * @param   string  $key    adress of data in memory (case insensitive)
     * @param   mixed   $value  new value (may be scalar value or array)
     * @return  $this
     * @name    \Yana\Application::setVar()
     * @see     \Yana\Application::setVarByReference()
     * @see     \Yana\Application::getVar()
     */
    public function setVar($key, $value)
    {
        $this->getRegistry()->setVar($key, $value);
        return $this;
    }

    /**
     * Replace all vars in the global registry.
     *
     * @param   array  $value  set of new values
     * @return  \Yana\Application
     */
    public function setVars(array $value)
    {
        $this->getRegistry()->setVars($value);
        return $this;
    }

    /**
     * get a resource
     *
     * This function takes a virtual file or directory path and returns the resource.
     * The requested resource must be defined within the virtual drive.
     * See the manual for more details on the proper use of virtual drives.
     *
     * If the mountpoint for the requested resource does not exist, or doesn't return any results,
     * the function returns bool(false) instead and issues a warning.
     *
     * @param   string  $path  virtual file path
     * @return  \Yana\Files\IsResource
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the resource was not found
     */
    public function getResource(string $path): \Yana\Files\IsResource
    {
        return $this->getRegistry()->getResource($path);
    }

    /**
     * Exit the current script.
     *
     * This will flush error messages and warnings to the screen,
     * write all reported errors (if any) to the framework's logs
     * and then exit the current script.
     * After that it will call itself again to handle the event
     * provided by the argument $event.
     *
     * You may use the special event 'null' to prevent the
     * framework from handling an event. In this case it will just
     * exit.
     *
     * If the argument $event is not provided, the default event
     * will be used instead.
     *
     * Examples:
     * <code>
     * // print an error and go to start page
     * new \Yana\Core\Exceptions\InvalidValueException('Error', \Yana\Log\TypeEnumeration::ERROR);
     * $YANA->exitTo();
     *
     * // same as:
     * $YANA->exitTo('');
     *
     * // Use special event 'null' if you just want to
     * // view the error message and exit the script
     * // without handling another event.
     * // ( You may translate this to: "exit to 'nowhere'" )
     * new \Yana\Core\Exceptions\InvalidValueException('Error', \Yana\Log\TypeEnumeration::ERROR);
     * $YANA->exitTo('null');
     *
     * // output message and route to 'login' page
     * new \Yana\Core\Exceptions\InvalidValueException('Error', \Yana\Log\TypeEnumeration::ERROR);
     * $YANA->exitTo('login');
     * </code>
     *
     * Please note: any code followed after a call to this function
     * will never be executed.
     *
     * @param  string  $event  upcoming event to route to
     * @param  array   $args   list of arguments to pass to the function
     * @codeCoverageIgnore
     */
    public function exitTo(string $event = 'null', array $args = array())
    {
        $this->_getOutputBehavior()->relocateTo($event, $args);
        exit(0);
    }

    /**
     * Provides GUI from current data.
     *
     * @codeCoverageIgnore
     */
    public function outputResults()
    {
        $targetAction = $this->_getOutputBehavior()->outputResults();
        if (!\is_null($targetAction)) {
            $this->exitTo($targetAction);
        }
    }

    /**
     * get default configuration value
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
        assert(is_scalar($key), 'Invalid argument $key: scalar expected');
        return $this->_getDependencyContainer()->getDefault($key);
    }

    /**
     * Deletes all temporary files in the 'cache/' directory.
     *
     * This includes templates and preinitialized instances of system objects.
     * Use this function where system settings or profile systems are changed,
     * to make sure changes are applied without delay.
     */
    public function clearCache()
    {
        // clear Menu Cache
        $this->_getDependencyContainer()->getMenuBuilder()
                ->clearMenuCache(); // uses session cache adapter by default

        // Clear Template cache
        $this->getView()->clearCache();

        // Clear Application cache
        $cache = $this->getCache();
        foreach ($cache->getIds() as $id)
        {
            $cache->offsetUnset($id);
        }
    }

    /**
     * <<factory>> connect()
     *
     * Returns a ready-to-use database connection.
     *
     * Example:
     * <code>
     * // Connect to database using 'config/db/user.config'
     * $db = \Yana\Application::connect('user');
     * </code>
     *
     * Note: Since Yana 4 it is possible to configure more than 1 database servers as data source.
     * To do so, open the administration panel and add the settings under "other data sources".
     *
     * If you did that and wish to use the database server you set up, add the name that you
     * gave it as the second parameter. The connection will then be opened to that server and
     * using the same schema information.
     *
     * This is particularly useful during migration, to switch environments on the fly in your code,
     * or when you wish to distribute your databases across several servers for special purposes
     * like reporting or logging.
     *
     * @param   string|\Yana\Db\Ddl\Database  $schema                  name of the database schema file (see config/db/*.xml),
     *                                                                 or instance of \Yana\Db\Ddl\Database
     * @param   string                        $optionalDataSourceName  if you wish another than the default data source, add the name here
     * @return  \Yana\Db\IsConnection
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no such database was found
     * @throws  \Yana\Db\ConnectionException             when connection to database failed
     * @throws  \Yana\Core\Exceptions\NotFoundException  when a data source name was given, but no unique data source with that name was found
     */
    public function connect($schema, ?string $optionalDataSourceName = null): \Yana\Db\IsConnection
    {
        $entity = null;
        if ($optionalDataSourceName > "") {
            $entity = $this->_getDependencyContainer()->getDataSourcesAdapter()->getFromDataSourceName($optionalDataSourceName);
        }
        return $this->_getDependencyContainer()->getConnectionFactory()->createConnection($schema, $entity);
    }

    /**
     * run diagnostics and get a system report
     *
     * This function runs full diagnosicts on all mounted sub-systems.
     *
     * Returns the a report object.
     *
     * Example:
     * <code>
     * <report>
     *   <text>Base directory: foo/</text>
     *   <report>
     *     <title>bar.file</title>
     *     <text>Type: file</text>
     *     <text>Path: bar.txt</text>
     *     <error>Is not readable ...</error>
     *   </report>
     *   <report>
     *     <title>foo</title>
     *     <text>Type: dir</text>
     *     <text>Path: bar/foo/</text>
     *   </report>
     * </report>
     * </code>
     *
     * @param   \Yana\Report\IsReport  $report  base report
     * @return  \Yana\Report\IsReport
     * @name    \Yana\Application:getReport()
     * @ignore
     */
    public function getReport(\Yana\Report\IsReport ?$report = null)
    {
        if (is_null($report)) {
            $report = \Yana\Report\Xml::createReport(__CLASS__);
        }

        $reportBuilder = new \Yana\Report\ApplicationReportBuilder($report);
        $reportBuilder->buildApplicationReport($this);

        return $report;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger(): \Yana\Log\IsLogHandler
    {
        return $this->_getDependencyContainer()->getLogger();
    }

    /**
     * Refresh application settings.
     *
     * Clears the cache, scans the plugin directory for new plugins,
     * and loads the security settings for the plugins found.
     *
     * @throws  \Yana\Core\Exceptions\NotReadableException       when the plugin repository source is not readable
     * @throws  \Yana\Core\Exceptions\NotWriteableException      when the plugin repository target is not writeable
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  when new security entries could not be inserted
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  when outdated security entries could not be deleted
     *
     * @codeCoverageIgnore
     */
    public function refreshSettings()
    {
        $this->clearCache();
        $this->getPlugins()->rebuildPluginRepository();
        $this->getSecurity()->refreshPluginSecurityRules();
    }

    /**
     * Build main application menu.
     *
     * Note: there is by definition only one main application menu per language.
     * Thus calling this builder twice will give you the same instance.
     *
     * @return  \Yana\Plugins\Menus\IsMenu
     */
    public function buildApplicationMenu(): \Yana\Plugins\Menus\IsMenu
    {
        return $this->_getDependencyContainer()->getMenuBuilder()->buildMenu();
    }

    /**
     * Builds and returns a form object.
     *
     * Forms are defined in XML database definition language files (*.db.xml) stored in the config/db/ directory.
     * You can identify them by the opening "form" tag. This form tag always has a "name" attribute.
     *
     * Call this function to build a form object that corresponds to the form of the same name.
     *
     * The form object returned will allow you to access the structure of the form as well as values entered into it,
     * and any rows retrieved from the database to be displayed therein.
     * It also enables you to store the provided changes to the database, or change settings like the number of rows shown per page.
     * In short: everything you can do with a form, you can do here.
     *
     * @param   string  $fileName  name of schema file in which form is defined
     * @param   string  $formName  name of form object, defaults to $fileName
     * @return  \Yana\Forms\IsBuilder
     */
    public function buildForm(string $fileName, string $formName = ""): \Yana\Forms\IsBuilder
    {
        if ($formName === "") {
            $formName = $fileName;
        }
        $builder = new \Yana\Forms\Builder($fileName, $this->_getDependencyContainer());
        return $builder->setId($formName);
    }

}

?>
