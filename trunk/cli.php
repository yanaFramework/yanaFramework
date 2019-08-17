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
declare(strict_types=1);

/**
 * @ignore
 */
require_once 'library.php';

/* Output a standard header */
print "
---------------------------------
| Yana - command line interface |
---------------------------------

Running:  " . $_SERVER['argv'][0] . "
Time:     " . date('r') . "
Arguments:
" . print_r($_SERVER['argv'], true) . "\n";

$application = new \Yana\ApplicationBuilder();
$application->setErrorReporting(YANA_ERROR_ON)
    ->execute();

print 'Execution finished successfully.';
exit(0);

?>