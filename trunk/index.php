<?php
/**
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
 * @author     Thomas Meyer <tom@all-community.de>
 * @link       http://www.yanaframework.net
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @package    yana
 * @copyright  2011 Thomas Meyer
 */

//error_reporting(0);
require_once 'library.php';

$application = new \Yana\ApplicationBuilder();
/* Uncomment this to print all error messages to screen */
$errorReporting = YANA_ERROR_ON;

/* Uncomment this to send all error messages to a log file or database */
//$errorReporting = YANA_ERROR_LOG;

/* Hide error messages from users */
//$errorReporting = YANA_ERROR_OFF;

$application->setErrorReporting($errorReporting)
    ->execute();
?>