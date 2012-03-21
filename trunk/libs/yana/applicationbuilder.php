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

namespace Yana;

/**
 * Frontcontroller.
 *
 * This implements the main program.
 *
 * The framework also accepts calls from the PHP command line interface.
 * This is intended to be used for cronjobs and maintenance.
 *
 * Example of usage (Windows):
 * <pre>php index.php action=test target=detailed >stdout.log</pre>
 *
 * When running PHP from the command line under UNIX, you need to
 * include the following line on top of the "index.php" file:
 * <pre>#!/usr/bin/php</pre>
 * This is to tell where the PHP binaries can be found.
 *
 * The path might be another on your server.
 * If you don't know the path, enter the following on your command line:
 * <pre>which php</pre>
 * This will output the path where the php binaries are installed.
 *
 * When running the program under Windows this is not necessary.
 * Instead you should change the system path to point to the
 * PHP installation directory, if you have'nt already done that.
 *
 * See the PHP manual for more details.
 *
 * @name        Index
 * @package     yana
 */
class ApplicationBuilder extends \Yana\Core\Object
{

    /**
     * @var \Yana\Log\Errors\Handler
     */
    private $_errorHandler = null;

    /**
     * Set error reporting level.
     *
     * You may use the following constants:
     * <ol>
     * <li>  YANA_ERROR_ON = catch all errors and print to screen  </li>
     * <li>  YANA_ERROR_OFF = do not report any errors or messages  </li>
     * <li>  YANA_ERROR_LOG = write errors and messages to a log file  </li>
     * </ol>
     *
     * When logging is active, will set error reporting to E_ALL (default) and catch all errors.
     * When logging is inactive, will set error reporting to 0 (default) and restore all error handlers.
     *
     * @param  string  $logging  examples: YANA_ERROR_OFF, YANA_ERROR_LOG,
     *                           YANA_ERROR_ON or E_ALL, E_ALL & ~E_NOTICE
     * @return ApplicationBuilder 
     */
    public function setErrorReporting($logging)
    {
        if (!defined('YANA_ERROR_REPORTING')) {
            /**
             * what to do with errors and system messages
             *
             * This constant reflects the way how errors and other messages are treated.
             * It can be one of the following: 'on', 'off' or 'log'.
             *
             * Meaning:
             * <ol>
             * <li>  on = catch all errors and print to screen  </li>
             * <li>  off = do not report any errors or messages  </li>
             * <li>  log = write errors and messages to a log file  </li>
             * </ol>
             */
            define('YANA_ERROR_REPORTING', $logging);
        }
        $logger = null;
        $formatter = null;
        switch ($logging)
        {
            case YANA_ERROR_ON:
                error_reporting(E_ALL);
                if ($this->_isCommandLineCall()) {
                    $formatter = new \Yana\Log\Formatter\TextFormatter();
                } else {
                    $formatter = new \Yana\Log\Formatter\HtmlFormatter();
                }
                $logger = new \Yana\Log\ScreenLogger();
                $logger->setLogLevel(E_ALL);
                $isActive = true;
                break;
            case YANA_ERROR_LOG:
                error_reporting(E_ALL);
                $formatter = new \Yana\Log\Formatter\TextFormatter();
                $logger = new \Yana\Log\FileLogger(new TextFile('cache/error.log'));
                $logger->setLogLevel(E_ALL & ~E_STRICT);
                $isActive = true;
                break;
            /**
             * Prevent PHP from showing error messages to avoid information leak to hackers.
             * Do no evaluate assertions for better performance.
             */
            default:
                error_reporting(0);
                $formatter = new \Yana\Log\Formatter\NullFormatter();
                $logger = new \Yana\Log\NullLogger();
                $isActive = false;
                break;
        }
        $this->_errorHandler = new \Yana\Log\Errors\Handler($formatter, $logger);
        $this->_errorHandler->setActivate($isActive);
        return $this;
    }

    /**
     * Returns TRUE if application was called from the command line.
     *
     * @return bool 
     */
    private function _isCommandLineCall()
    {
        return defined('STDIN') && !isset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * This builds and runs a Yana application.
     *
     * @return ApplicationBuilder 
     */
    public function execute()
    {
        /* differentiate between web interface and command line calls */
        if ($this->_isCommandLineCall()) {
            $this->_runOnCommandLine();
        } else {
            $this->_runOnline();
        }
        return $this;
    }

    /**
     * Handles calls from command line interface.
     *
     * The CLI is for maintenance tasks - e.g. database backups aso.
     * triggered by cronjobs.
     */
    private function _runOnCommandLine()
    {
        global $YANA;
        $YANA = \Yana::getInstance();
        // Handle the request
        $YANA->callAction();

        /* Since this is expected to be used for cronjobs,
         * no (human readable) output is explicitely created here.
         * Instead the called code should create comprehensive log files,
         * which the administrator can review later.
         */
    }

    /**
     * Handles calls from a web-client.
     */
    private function _runOnline()
    {
        global $YANA;

        /* session preparation
         *
         * {@internal
         *
         * NOTE on session handling in PHP:
         *
         * As a default PHP marks a session as "expired" after 24 minutes, BUT it is deleted
         * no sooner than on the next run of the garbage collector.
         *
         * The garbage collector is randomly started on an incoming request.
         * The propability in percent that the gc runs on a request is calculated by
         * (session.gc_probability / session.gc_divisor * 100). Where gc_probability defaults
         * to 1 and gc_divisor defaults to 100, which actually results in 1% and means the gc
         * should run once each 100 requests.
         *
         * If you got low traffic you should consider increasing the value of
         * "session.gc_probability" from default 1 to a higher value e.g. 5.
         * This means: the garbage collector will run on 5% of all incomming requests.
         *
         * Example: ini_set("session.gc_probability", "5");
         *
         * Don't use too high values either, as this might slow down your server.
         * E.g. running the gc on every request is certainly a bad idea.
         *
         * Abbr: "gc" = garbage collector
         * }}
         */
        session_name(YANA_SESSION_NAME);
        // limit session cookie to 1 hour and the local script directory
        session_set_cookie_params(3600, dirname($_SERVER['PHP_SELF']) . '/');
        // set session lifetime to 1 hour
        ini_set("session.gc_maxlifetime", "3600");
        @session_start();

        // Check language settings
        if (isset($_GET['language'])) {
            $_SESSION['language'] = $_GET['language'];
            unset($_GET['language']);
        } elseif (!isset($_SESSION['language']) && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // this tries to autodetect the clients prefered language
            $_SESSION['language'] = mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }
        /* {@internal
         *
         * Things to check BEFORE enabling output compression:
         * 1) headers already sent (e.g. by an PHP message, or another script?)
         * 2) did the browser provide a list of accepted encoding?
         * 3) does the browser accept the desired encoding?
         * 4) you can't use 'ob_gzhandler' if 'zlib.output_compression' is already enabled in php.ini
         *
         * Things to know AFTER enabling output compression:
         * 1) flushes automatically on program termination
         * 2) buffer can be erased using ob_clean() and ob_end_clean()
         * 3) flush buffer to output device using ob_flush() and ob_end_flush()
         * 4) use ob_get_length() === false to check if buffer is disabled
         * 5) use ob_get_length() === 0 to check if buffer is empty
         *
         * }}
         */
        $outputCompressionActive = false;
        if (headers_sent() === false && !ini_get('zlib.output_compression')) {
            $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'];
            if (isset($acceptEncoding) && strpos($acceptEncoding, "gzip") !== false) {
                // "output compression level" is an integer between -1 (off/default) - 9 (max)
                ini_set('zlib.output_compression_level', 6);
                ob_start("ob_gzhandler");
                $outputCompressionActive = true;
            }
        }
        $YANA = \Yana::getInstance(); // Get a yana-instance
        $YANA->callAction();         // Handle the request
        $YANA->outputResults();      // Create the output
        // flush the output buffer (GZ-compression)
        if ($outputCompressionActive && ob_get_length() !== false) {
            ob_end_flush();
        }
    }

}

?>