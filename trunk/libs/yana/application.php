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
 * @todo        Add Dependency container from Yana\Core\Dependencies\Container
 */
final class Application extends \Yana\Core\Object implements \Yana\Report\IsReportable, \Yana\Core\IsVarContainer
{

    /**
     * Contains code to initialize and return sub-modules.
     *
     * @var  \Yana\Core\Dependencies\IsApplicationContainer
     */
    private $_dependencyContainer = null;

    /**
     * <<constructor>> Inject dependencies.
     *
     * @param  \Yana\Core\Dependencies\IsApplicationContainer  $container  injected dependencies
     */
    public function __construct(\Yana\Core\Dependencies\IsApplicationContainer $container)
    {
        $this->_dependencyContainer = $container;
    }

    /**
     * Returns the container.
     *
     * The dependency container contains code to initialize and return sub-modules.
     *
     * @return  \Yana\Core\Dependencies\IsApplicationContainer
     */
    protected function _getDependencyContainer()
    {
        return $this->_dependencyContainer;
    }

    /**
     * Returns the class name of the called class.
     *
     * @return string
     */
    protected static function _getClassName()
    {
        return __CLASS__;
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
    public function execute($action = "", array $args = null)
    {
        assert('is_string($action); // Invalid argument $action: string expected');

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
        assert('!isset($eventConfiguration); // Cannot redeclare var $eventConfiguration');
        assert('!isset($plugins); // Cannot redeclare var $plugins');
        $plugins = $this->getPlugins();
        $eventConfiguration = $plugins->getEventConfiguration($action);
        if (!($eventConfiguration instanceof \Yana\Plugins\Configs\IsMethodConfiguration)) {
            $error = new \Yana\Core\Exceptions\InvalidActionException();
            $error->setAction($action);
            return false;
        }

        assert('!isset($paths); // Cannot redeclare var $paths');
        $paths = $eventConfiguration->getPaths();
        if ($paths) {
            assert('!isset($language); // Cannot redeclare var $language');
            $language = $this->getLanguage();
            // mount language directory, if it exists
            assert('!isset($langDir); // Cannot redeclare var $langDir');
            foreach ($eventConfiguration->getPaths() as $langDir)
            {
                $langDir = $langDir . "/languages/";
                if (is_dir($langDir)) {
                    $language->addDirectory($langDir);
                }
            }
            unset($langDir, $language);
        }
        unset($paths);
        // load language files
        assert('!isset($languages); // Cannot redeclare var $languages');
        $languages = $eventConfiguration->getLanguages();
        if ($languages) {
            assert('!isset($language); // Cannot redeclare var $language');
            $language = $this->getLanguage();
            assert('!isset($languageId); // Cannot redeclare var $languageId');
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
        assert('!isset($styles); // Cannot redeclare var $styles');
        $styles = $eventConfiguration->getStyles();
        if ($styles) {
            $this->getView()->addStyles($styles);
        }
        unset($styles);
        assert('!isset($scripts); // Cannot redeclare var $scripts');
        $scripts = $eventConfiguration->getScripts();
        if ($scripts) {
            $this->getView()->addScripts($scripts);
        }
        unset($scripts);

        /**
         * 3) handle event
         *
         * Returns bool(true) on success and bool(false) otherwise.
         */
        $result = false;
        try {

            $result = $plugins->sendEvent($action, $args, $this);
            if ($result !== false) {
                /* Create timestamp to provide information for read-stability isolation level */
                $_SESSION['transaction_isolation_created'] = time();
            }

        } catch (\Yana\Core\Exceptions\IsException $e) {
            $this->_getExceptionLogger()->addException($e);

        } catch (\Exception $e) {
            $message = get_class($e) . ': ' . $e->getMessage() . ' Thrown in ' . $e->getFile() .
                ' on line ' . $e->getLine();
            $this->getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);

        }
        return $result !== false;
    }

    /**
     * Get exception logger.
     *
     * Builds and returns a class that converts exceptions to messages and passes them as var
     * "STDOUT" to a var-container for output in a template or on the command line.
     *
     * @return  \Yana\Log\ExceptionLogger
     * @ignore
     */
    protected function _getExceptionLogger()
    {
        return $this->_getDependencyContainer()->getExceptionLogger();
    }

    /**
     * Access security sub-system.
     *
     * This facade is a front that provides simplified access to the various security features
     * the framework supports out-of-the-box.
     * 
     * @return \Yana\Security\IsFacade
     */
    public function getSecurity()
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
    public function getRegistry()
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
    public function getPlugins()
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
    public function getView()
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
    public function getLanguage()
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
    public function getSkin()
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
    public function getProfileId()
    {
        return $this->_getDependencyContainer()->getProfileId();
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
        assert('is_scalar($key); // Invalid argument $key: scalar expected');
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
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     * @name    \Yana\Application::getVar()
     * @see     \Yana\Application::setVarByReference()
     * @see     \Yana\Application::setVar()
     */
    public function getVar($key)
    {
        assert('is_scalar($key); // Invalid argument $key: scalar expected');
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
     * @return  \Yana\Application
     * @name    \Yana\Application::setVarByReference()
     * @see     \Yana\Application::setVar()
     * @see     \Yana\Application::getVar()
     */
    public function setVarByReference($key, &$value)
    {
        assert('is_scalar($key); // Invalid argument $key: scalar expected');
        $this->getRegistry()->setVarByReference((string) $key, $value);
        return $this;
    }

    /**
     * Replace all vars in the global registry by reference.
     *
     * @param   array  &$values  new set of values
     * @return  \Yana\Application
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
     * @return  \Yana\Application
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
     * @return  \Yana\Files\AbstractResource
     */
    public function getResource($path)
    {
        assert('is_string($path); // Invalid argument $path: string expected');
        return $this->getRegistry()->getResource($path);
    }

    /**
     * exit the current script
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
     * @since  2.9.0 RC2
     */
    public function exitTo($event = 'null', array $args = array())
    {
        assert('is_string($event); // Invalid argument $event: string expected');
        $eventLowerCase = mb_strtolower((string) $event);
        unset($event);

        /**
         * save log-files (if any)
         *
         * By default this will output any messages to a table of the database named 'log'.
         */
        $view = $this->getView();

        assert('!isset($template); // Cannot redeclare var $template');
        $templateName = 'id:MESSAGE';

        /**
         * is an AJAX request
         */
        if ($this->_getDependencyContainer()->getRequest()->isAjaxRequest()) {
            $eventLowerCase = 'null';
            $templateName = 'id:STDOUT';
        }

        /**
         * output a message and DO NOT RELOCATE, when
         *   1) headers are already sent, OR
         *   2) the template explicitely requests a message, OR
         *   3) the special 'NULL-event' (no event) is requested.
         */
        if ($eventLowerCase === 'null' || $this->getDefault('MESSAGE') === true || headers_sent() === true) {

            $template = $view->createLayoutTemplate($templateName, '', $this->getVars());
            $template->setVar('ACTION', mb_strtolower("$eventLowerCase"));

            exit((string) $template);
        }

        /**
         * save message and relocate.
         */
        unset($_SESSION['STDOUT']);
        $messageCollection = $this->_getExceptionLogger()->getMessages();
        if ($messageCollection->count() > 0) {
            $_SESSION['STDOUT'] = $messageCollection;
        }

        $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
        $args["action"] = $eventLowerCase;
        header("Location: " . $urlFormatter(http_build_query($args), true));
        exit(0);
    }

    /**
     * Provides GUI from current data.
     */
    public function outputResults()
    {
        /* 0 initialize vars */
        $plugins = $this->getPlugins();
        $event = $plugins->getFirstEvent();
        $result = $plugins->getLastResult();
        $eventConfiguration = $plugins->getEventConfiguration($event);
        if (! $eventConfiguration instanceof \Yana\Plugins\Configs\IsMethodConfiguration) {
            return; // error - unable to continue
        }
        $template = $eventConfiguration->getTemplate();
        unset($eventConfiguration);

        switch (strtolower($template))
        {
            /**
             * 1) the reserved template 'NULL' is an alias for 'no template' and will prevent the use of HTML template files.
             *
             * This may mean the plugin has created some output itself using print(),
             * or it is a triggered cron-job that is not meant to produce any output at all,
             * or it has returned a value, that will be sent as a JSON encoded string.
             */
            case 'null':
                $this->_outputAsJson($result);
                break;
            /**
             * 2) the reserved template 'MESSAGE' is a special template that produces a text message.
             *
             * The text usually is an ID of some text.
             * The actual message is stored in the language files and the translated message will be read from there
             * depending on the user's prefered language setting.
             */
            case 'message':
                $this->_outputAsMessage();
                break;
            /**
             * 3) all other template settings go here
             */
            default:
                if ($result === false && $this->_getExceptionLogger()->getMessages()->count() === 0) {
                    $this->_outputAsMessage();
                    return;
                }
                $this->_outputAsTemplate($template);
                break;
        }

    }

    /**
     * Output results as JSON.
     *
     * If the function returned a result, it will be printed as a JSON string.
     *
     * @param  mixed  $result  whatever the last called action returned
     */
    private function _outputAsJson($result)
    {
        $json = "";
        if (!headers_sent()) {
            header('Content-Type: text/plain');
            header('Content-Encoding: UTF-8');
            header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }
        if (!is_null($result)) {
            $json = json_encode($result);
        }
        print $json;
    }

    /**
     * Output a text message and relocate to next event.
     */
    private function _outputAsMessage()
    {
        $route = $this->getPlugins()->getNextEvent();
        $target = "";
        $messageClass = "";

        $logger = $this->_getExceptionLogger();
        if ($route instanceof \Yana\Plugins\Configs\EventRoute) {
            // create default message if there is none
            if ($logger->getMessages()->count() === 0) {

                $level = \Yana\Log\TypeEnumeration::ERROR;
                $message = 'Action was not successfully';
                if ($route->getCode() === \Yana\Plugins\Configs\ReturnCodeEnumeration::SUCCESS) {
                    $level = \Yana\Log\TypeEnumeration::SUCCESS;
                    $message = 'Action carried out successfully';
                }

                $messageClass = $route->getMessage();
                if ($messageClass && class_exists($messageClass)) {
                    $logger->addException(new $messageClass($message, $level));
                } else {
                    $logger->addLog($message, $level);
                }
            }

            $target = $route->getTarget();
        }
        if (empty($target)) {
            // if no other destination is defined, route back to default homepage
            $target = $this->getDefault("homepage");
            assert('!empty($target); // Configuration error: No default homepage set.');
            assert('is_string($target); // Configuration error: Default homepage invalid.');
        }
        $this->setVar('STDOUT', $logger->getMessages());

        $this->exitTo($target);
    }

    /**
     * Select the given template as output target and print the result page.
     *
     * @param  string  $template  a valid template identifier
     */
    private function _outputAsTemplate($template)
    {
        assert('is_string($template); // Invalid argument $template: string expected');
        $view = $this->getView();

        // Find base template
        $baseTemplate = 'id:INDEX';
        $_template = mb_strtoupper(\Yana\Plugins\Annotations\Enumeration::TEMPLATE);
        $defaultEvent = $this->_getDependencyContainer()->getDefault('event');
        if ($defaultEvent instanceof \Yana\Util\IsXmlArray && !empty($defaultEvent->$_template)) {
            $baseTemplate = (string) $defaultEvent->$_template;
        }
        unset($defaultEvent);

        if (!is_file($template) && !\Yana\Util\Strings::startsWith($template, 'id:')) {
            $template = "id:{$template}";
        }
        /* register templates with view sub-system */
        $template = $view->createLayoutTemplate($baseTemplate, $template, $this->getVars());
        /* there is a special var called 'STDOUT' that is used to output messages */
        if (isset($_SESSION['STDOUT'])) {
            $template->setVar('STDOUT', $_SESSION['STDOUT']);
            unset($_SESSION['STDOUT']);
        } else {
            $template->setVar('STDOUT', $this->_getExceptionLogger()->getMessages());
        }

        /* print the page to the client */
        print $template->fetch();
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
        assert('is_scalar($key); // Invalid argument $key: scalar expected');
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
        $this->_getDependencyContainer()->getMenuBuilder($this)
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
     * @param   string|\Yana\Db\Ddl\Database  $schema  name of the database schema file (see config/db/*.xml),
     *                                                 or instance of \Yana\Db\Ddl\Database
     * @return  \Yana\Db\IsConnection
     */
    public function connect($schema)
    {
        return $this->_getDependencyContainer()->getConnectionFactory()->createConnection($schema);
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
     * <?xml version="1.0"?>
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
    public function getReport(\Yana\Report\IsReport $report = null)
    {
        if (is_null($report)) {
            $report = \Yana\Report\Xml::createReport(__CLASS__);
        }

        /**
         * 1) General system information
         */
        $report->addNotice("installed version of Yana Framework is: " . YANA_VERSION);
        $report->addNotice("installed version of PHP is: " . PHP_VERSION);
        $report->addNotice("current server time is: " . date("r", time()));
        $report->addNotice("running diagnostics on profile: " . $this->getProfileId());

        $subreport = $report->addReport("Testing installation");

        /**
         * 2) Check for availability of PEAR-DB
         */
        @include_once "MDB2.php";
        if (!class_exists("MDB2")) {
            $message = "PHP PEAR-MDB2 module not found. " .
                "Database plugins require PEAR-MDB2 and will not run unless you install it.";
            $subreport->addError($message);
        } else {
            $subreport->addText("PHP PEAR-MDB2 found (required to run database plugins)");
        }

        /**
         * 3) Check availability of configuration file and configuration directory
         */
        if (YANA_CDROM === true) {
            if (!is_writeable(YANA_CDROM_DIR)) {
                $message = "Temporary directory " . YANA_CDROM_DIR . " is not writeable. " .
                    "Set access rights for directory '" .
                    YANA_CDROM_DIR . "' to 777, including all subdirectories and files.";
                $subreport->addError($message);
            }
        } else {
            if (!is_writeable($this->getVar('CONFIGDIR'))) {
                $message = "Configuration directory is not writeable. " .
                    "Set access rights for directory '" . $this->getVar('CONFIGDIR') .
                    "' to 777, including all subdirectories and files.";
                $subreport->addError($message);
            }
            if (!is_writeable($this->getVar('TEMPDIR'))) {
                $message = "Directory for temporary files is not writeable. " .
                    "Set access rights for directory '" . $this->getVar('TEMPDIR') .
                    "' to 777, including all subdirectories and files.";
                $subreport->addError($message);
            }
        }
        unset($subreport);

        /**
         * 4) Add a list of MD5 checksums for several important files
         */
        $systemIntegrityReport = $report->addReport("System-integrity check");
        $message = "The following list contains the MD5 checksums of several important files. " .
            "Compare these with your own list to see, " .
            "if any of these files have recently been modified without your knowledge.";
        $systemIntegrityReport->addText($message);

        if (is_dir('manual')) {
            $message = "You do not need to copy the directory 'manual' to your website. " .
                "It is not required to run the program. You might want to remove it to safe space.";
            $systemIntegrityReport->addNotice($message);
        }

        foreach (glob('./*.php') as $root)
        {
            $root = basename($root);
            if (!in_array($root, array('index.php', 'library.php', 'cli.php'))) {
                $message = "Unexpected file '" . $root . "' found. " .
                    "If you did'nt place this file here, " .
                    "it might be the result of an hijacking attempt. " .
                    "You should consider removing this file.";
                $systemIntegrityReport->addWarning($message);
            } else {
                $systemIntegrityReport->addText("{$root} = " . md5_file($root));
            }
        } // end foreach

        /**
         *  5) Add subreports
         */
        foreach ($this as $name => $member)
        {
            if (is_object($member) && $member instanceof \Yana\Report\IsReportable) {
                $memberReport = $report->addReport("$name");
                $member->getReport($memberReport);
            }
        }
        unset($memberReport);
        $this->getPlugins()->getReport($report->addReport("Plugins"));
        $this->getSkin()->getReport($report->addReport("Skins"));
        $this->getRegistry()->getReport($report->addReport("Virtual files"));

        $iconIntegrityReport = $report->addReport('Searching for icon images');
        $registry = $this->getRegistry();
        /* @var $dir \Dir */
        assert('!isset($dir); // Cannot redeclare var $dir');
        $dir = $registry->getResource('system:/smile');
        /* @var $dir \Yana\Files\Dir */
        $smilies = $dir->listFiles();
        if (count($smilies)==0) {
            $message = "No Icons found. Please check if the given directory is correct: '" .
                $dir->getPath() . "'.";
            $iconIntegrityReport->addWarning($message);
        } else {
            $iconIntegrityReport->addText(count($smilies) . " Icons found in directory '" . $dir->getPath() . "'.");
            $iconIntegrityReport->addText("No problems found: Directory setting seems to be correct.");
        }
        unset($dir);

        return $report;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger()
    {
        return $this->_getDependencyContainer()->getLogger();
    }

    /**
     * Refresh application settings.
     *
     * Clears the cache, scans the plugin directory for new plugins,
     * and loads the security settings for the plugins found.
     *
     * @return  self
     * @throws  \Yana\Core\Exceptions\NotReadableException       when the plugin repository source is not readable
     * @throws  \Yana\Core\Exceptions\NotWriteableException      when the plugin repository target is not writeable
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  when new security entries could not be inserted
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  when outdated security entries could not be deleted
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
    public function buildApplicationMenu()
    {
        return $this->_getDependencyContainer()->getMenuBuilder($this)->buildMenu();
    }

}

?>