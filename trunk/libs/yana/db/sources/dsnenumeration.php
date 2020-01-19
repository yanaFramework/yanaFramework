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

namespace Yana\Db\Sources;

/**
 * <<enumeration>> Keys of DSN records.
 *
 * <ul>
 *   <li>USERNAME</li>
 *   <li>PASSWORD</li>
 *   <li>DATABASE: name of database to connect to</li>
 * </ul>
 *
 * @package     yana
 * @subpackage  db
 */
class DsnEnumeration extends \Yana\Core\AbstractEnumeration
{
    /**
     * true, if ODBC is used to connect to the database
     */
    const ODBC = 'USE_ODBC';
    /**
     * name of used database system
     */
    const DBMS = 'DBMS';
    /**
     * IP or host name, e.g. localhost<
     */
    const HOST = 'HOST';
    /**
     * port number of database server (may be empty)
     */
    const PORT = 'PORT';
    /**
     * name of database to connect to
     */
    const DATABASE = 'DATABASE';
    /**
     * user credentials
     */
    const USER = 'USERNAME';
    /**
     * user credentials
     */
    const PASSWORD = 'PASSWORD';
    const CHARSET = 'CHARSET';

}

?>