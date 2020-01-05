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
declare(strict_types=1);

namespace Yana\Core\Output;

/**
 * Helps the application class to handle output behavior.
 *
 * @package     yana
 * @subpackage  core
 */
class DefaultBehavior extends \Yana\Core\StdObject implements \Yana\Core\Output\IsBehavior
{
    use \Yana\Core\Dependencies\HasApplicationContainer;

    /**
     * Provides GUI from current data.
     *
     * Returns the name of the action to call next (if any).
     * The returned string will be empty if there is no such action.
     *
     * @return  string|NULL
     */
    public function outputResults() : ?string
    {
        /* 0 initialize vars */
        $plugins = $this->_getDependencyContainer()->getPlugins();
        $event = $plugins->getFirstEvent();
        $result = $plugins->getLastResult();
        $eventConfiguration = $plugins->getEventConfiguration($event);
        if (! $eventConfiguration instanceof \Yana\Plugins\Configs\IsMethodConfiguration) {
            return null; // error - unable to continue
        }
        // @codeCoverageIgnoreStart
        $template = $eventConfiguration->getTemplate();

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
                return $this->outputAsJson($result);
            /**
             * 2) the reserved template 'MESSAGE' is a special template that produces a text message.
             *
             * The text usually is an ID of some text.
             * The actual message is stored in the language files and the translated message will be read from there
             * depending on the user's prefered language setting.
             */
            case 'message':
                return $this->outputAsMessage();
            /**
             * 3) all other template settings go here
             */
            default:
                if ($result === false && $this->_getDependencyContainer()->getExceptionLogger()->getMessages()->count() === 0) {
                    return $this->outputAsMessage();
                }
                return $this->outputAsTemplate($template, $eventConfiguration);
        }
        // @codeCoverageIgnoreEnd

    }

    /**
     * Output results as JSON.
     *
     * If the function returned a result, it will be printed as a JSON string.
     *
     * @param  mixed  $result  whatever the last called action returned
     */
    public function outputAsJson($result)
    {
        $json = "";
        // @codeCoverageIgnoreStart
        if (!headers_sent()) {
            header('Content-Type: text/plain');
            header('Content-Encoding: UTF-8');
            header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }
        // @codeCoverageIgnoreEnd
        if (!is_null($result)) {
            $json = json_encode($result);
        }
        $this->_printText($json);
    }

    /**
     * Output a text message and relocate to next event.
     *
     * @return  string|NULL
     */
    public function outputAsMessage(): ?string
    {
        $route = $this->_getDependencyContainer()->getPlugins()->getNextEvent();
        $target = null;

        $logger = $this->_getDependencyContainer()->getExceptionLogger();
        if ($route instanceof \Yana\Plugins\Configs\EventRoute) {
            // create default message if there is none
            if ($logger->getMessages()->count() === 0) {

                $this->_logExecutionResult($route, $logger);
            }

            $target = $route->getTarget();
        }
        if (empty($target)) {
            // if no other destination is defined, route back to default homepage
            $target = $this->_getDependencyContainer()->getDefault("homepage");
            assert(!empty($target), 'Configuration error: No default homepage set.');
            assert(is_string($target), 'Configuration error: Default homepage invalid.');
        }
        $this->_getDependencyContainer()->getRegistry()->setVar('STDOUT', $logger->getMessages());

        return $target;
    }

    /**
     * Create a log entry to report the result of the execution.
     *
     * This function is called when an executed action did not throw any exception and did not yield any success message of its own.
     *
     * In this case, this function will create a default success or error message and push it to the log-stack.
     *
     * @param  \Yana\Plugins\Configs\EventRoute  $route   holds information about the action that has been executed
     * @param  \Yana\Log\ExceptionLogger         $logger  the logger to send the log to
     */
    private function _logExecutionResult(\Yana\Plugins\Configs\EventRoute $route, \Yana\Log\ExceptionLogger $logger)
    {
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

    /**
     * Select the given template as output target and print the result page.
     *
     * @param  string                                       $templateId          a valid template identifier
     * @param  \Yana\Plugins\Configs\IsMethodConfiguration  $eventConfiguration  event meta data containing information about scripts and stylesheets
     */
    public function outputAsTemplate(string $templateId, \Yana\Plugins\Configs\IsMethodConfiguration $eventConfiguration)
    {
        $view = $this->_getDependencyContainer()->getView();

        // Find base template
        $baseTemplate = 'id:INDEX';
        $_template = mb_strtoupper(\Yana\Plugins\Annotations\Enumeration::TEMPLATE);
        $defaultEvent = $this->_getDependencyContainer()->getDefault('event');
        if (is_array($defaultEvent) && !empty($defaultEvent->$_template)) {
            $baseTemplate = 'id:' . (string) $defaultEvent->$_template;
        }
        unset($defaultEvent);

        if (!is_file($templateId) && !\Yana\Util\Strings::startsWith($templateId, 'id:')) {
            $templateId = "id:{$templateId}";
        }
        /* register templates with view sub-system */
        $template = $view->createLayoutTemplate($baseTemplate, $templateId, $this->_getDependencyContainer()->getRegistry()->getVars());
        /* there is a special var called 'STDOUT' that is used to output messages */
        if (isset($_SESSION['STDOUT'])) {
            $template->setVar('STDOUT', $_SESSION['STDOUT']);
            unset($_SESSION['STDOUT']);
        } else {
            $template->setVar('STDOUT', $this->_getDependencyContainer()->getExceptionLogger()->getMessages());
        }

        $view->addStyles($eventConfiguration->getStyles());
        $view->addScripts($eventConfiguration->getScripts());

        /* print the page to the client */
        $this->_printTemplate($template);
    }

    /**
     * Output relocation request.
     *
     * This will flush error messages and warnings to the screen
     * and tell the client (i.e. a browser) to relocate, so that the given action can be executed.
     *
     * You may use the special event 'null' to prevent the framework from handling an event.
     *
     * @param  string  $action  relocate here
     * @param   array  $args    with these arguments
     */
    public function relocateTo(string $action, array $args)
    {
        assert(!isset($actionLowerCase), 'Cannot redeclare var $actionLowerCase');
        $actionLowerCase = mb_strtolower((string) $action);
        unset($action);

        /**
         * save log-files (if any)
         *
         * By default this will output any messages to a table of the database named 'log'.
         */

        assert(!isset($template), 'Cannot redeclare var $template');
        $templateName = 'id:MESSAGE';

        /**
         * is an AJAX request
         */
        if ($this->_getDependencyContainer()->getRequest()->isAjaxRequest()) {
            $actionLowerCase = 'null';
            $templateName = 'id:STDOUT';
        }

        /**
         * output a message and DO NOT RELOCATE, when
         *   1) headers are already sent, OR
         *   2) the template explicitely requests a message, OR
         *   3) the special 'NULL-event' (no event) is requested.
         */
        if ($actionLowerCase === 'null' || $this->_getDependencyContainer()->getDefault('MESSAGE') === true || headers_sent() === true) {

            $args = $this->_getDependencyContainer()->getRegistry()->getVars();
            $template = $this->_getDependencyContainer()->getView()->createLayoutTemplate($templateName, '', $args);
            $template->setVar('ACTION', mb_strtolower("$actionLowerCase"));

            $this->_printTemplate($template);

        } else {

            /**
             * save message and relocate.
             */

            // @codeCoverageIgnoreStart
            unset($_SESSION['STDOUT']);
            $messageCollection = $this->_getDependencyContainer()->getExceptionLogger()->getMessages();
            if ($messageCollection->count() > 0) {
                $_SESSION['STDOUT'] = $messageCollection;
            }

            $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
            $args["action"] = $actionLowerCase;
            header("Location: " . $urlFormatter(http_build_query($args), true));
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Print the template content to the client.
     *
     * This function should be overwritten for unit tests.
     *
     * @param  \Yana\Views\Templates\IsTemplate  $template  to be printed
     * @codeCoverageIgnore
     */
    protected function _printTemplate(\Yana\Views\Templates\IsTemplate $template)
    {
        print $template->fetch();
    }

    /**
     * Print the page to the client.
     *
     * This function should be overwritten for unit tests.
     *
     * @param  string  $text  to be printed
     * @codeCoverageIgnore
     */
    protected function _printText(string $text)
    {
        print $text;
    }

}

?>