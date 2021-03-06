<?php
/**
 * include this before any test-case
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
 * @package  test
 * @license  http://www.gnu.org/licenses/gpl.txt
 * @ignore
 */

date_default_timezone_set('Europe/Paris');

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../library.php';

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../smarty/bootstrap.php';

if (!defined('CWD')) {
    define('CWD', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}
if (empty($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['OS']) && preg_match('/windows/i', $_SERVER['OS'])) {
    $_SERVER['DOCUMENT_ROOT'] =  realpath(CWD . "../../../../../") . '/htdocs/';
    $path = dirname($_SERVER["DOCUMENT_ROOT"]); // Will be used when importing test-case
}

\Yana\Db\Ddl\DDL::setDirectory(CWD . '/resources/');
\Yana\Db\FileDb\Helpers\FilenameMapper::setBaseDirectory(CWD . 'resources/db/');

\Yana\Plugins\Facade::setPluginDirectory(new \Yana\Files\Dir(CWD . '/../../../plugins/'));

?>