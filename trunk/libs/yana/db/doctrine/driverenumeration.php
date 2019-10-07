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

namespace Yana\Db\Doctrine;

/**
 * <<enumeration>> Lists all supported RDBMS and their aliases.
 *
 * @package     yana
 * @subpackage  db
 */
class DriverEnumeration extends \Yana\Core\AbstractEnumeration
{
    const MYSQL = 'mysqli';
    const MYSQL_2 = 'mysql2';
    const MYSQL_PDO = 'pdo_mysql';
    const MYSQL_DRIZZLE = 'drizzle_pdo_mysql';
    const DB2 = 'db2';
    const DB2_ALIAS = 'ibm_db2';
    const MSSQL = 'sqlsrv';
    const MSSQL_PDO = 'pdo_sqlsrv';
    const POSTGRESQL = 'pgsql';
    const POSTGRESQL_ALIAS = 'postgres';
    const POSTGRESQL_ALIAS2 = 'postgresql';
    const POSTGRESQL_PDO = 'pdo_pgsql';
    const SQLITE = 'sqlite';
    const SQLITE_ALIAS = 'sqlite3';
    const SQLITE_PDO = 'pdo_sqlite';
    const ORACLE = 'oci';
    const ORACLE_ALIAS = 'oci8';
    const ORACLE_PDO = 'pdo_oci';
    const SYBASE = 'sqlanywhere';

}

?>