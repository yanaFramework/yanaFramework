<?php
/**
 * YANA Commandline interface
 *
 * This simple program is used to trigger the YANA framework from
 * the command line, for example via a cron-job.
 *
 * To run this script use the following syntax:
 * php cli.php "arg1=value 1" "arg2=value 2" ...
 *
 * Note: errors are printed to a log file (cache/error.log).
 *
 * Note: as of the web-interface the argument "action" is mandatory.
 * The arguments are lower-cased before being processed.
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

/**
 * @ignore
 */
require_once 'library.php';

/* prepare input arguments */
$argv = Request::getVars();

if (!isset($argv['action'])) {
    trigger_error("Missing argument: \"action\". This argument is mandatory. Syntax: \"action=foo\"", E_USER_ERROR);
    exit(1);
}

/* Initialize the framework */
ErrorUtility::setErrorReporting(YANA_ERROR_LOG);
$YANA = Yana::getInstance();

/* Output a standard header */
print "
---------------------------------
| Yana - command line interface |
---------------------------------

Running:  ".$_SERVER['argv'][0]."
Time:     ".date('r')."
Arguments:
".print_r($argv, true)."\n";

try
{
    $YANA->callAction($argv['action'], $argv);
}
catch (Exception $log)
{
    $message = (string) $log;
    switch (mb_strtolower(get_class($log)))
    {
        case 'log':
        case 'message':
        case 'alert':
            trigger_error($message, E_USER_NOTICE);
        break;
        case 'warning':
            trigger_error($message, E_USER_WARNING);
        break;
        case 'error':
            trigger_error($message, E_USER_ERROR);
        break;
        default:
            trigger_error($message, E_USER_WARNING);
        break;
    }
}

if (PluginManager::getLastResult() === true) {
    print 'Execution finished successfully.';
    exit(0);
} else {
    print 'Execution finished with errors. See the error log for details. (default output path is: cache/error.log)';
    trigger_error('Execution finished with errors.', E_USER_WARNING);
    exit(2);
}
?>