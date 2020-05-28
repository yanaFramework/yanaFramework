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

namespace Yana\Db\Mdb2;

/**
 * <<enumeration>> Lists all supported RDBMS.
 *
 * @package     yana
 * @subpackage  db
 */
class DriverEnumeration extends \Yana\Core\AbstractEnumeration
{
    const FRONTBASE = 'fbsql';
    const INTERBASE = 'ibase';
    const MSSQL = 'mssql';
    const MYSQL = 'mysqli';
    const ORACLE = 'oci8';
    const POSTGRESQL = 'pgsql';
    const QUERYSIM = 'querysim';
    const SQLITE = 'sqlite';

    /**
     * Maps a driver alias as given by MDB2 or Doctrine to common DBMS driver name.
     *
     * If the alias is unknown, it is returned unchanged.
     *
     * @param   string  $dbms  alias to map
     * @return  string
     */
    public static function mapAliasToDriver(string $dbms): string
    {
        switch ($dbms)
        {
            // Mapping aliases (driver names) to real DBMS names
            case \Yana\Db\DriverEnumeration::MYSQL:
            case \Yana\Db\Doctrine\DriverEnumeration::MYSQL:
            case \Yana\Db\Doctrine\DriverEnumeration::MYSQL_2:
            case \Yana\Db\Doctrine\DriverEnumeration::MYSQL_PDO:
            case \Yana\Db\Doctrine\DriverEnumeration::MYSQL_DRIZZLE:
                return self::MYSQL;

            case \Yana\Db\DriverEnumeration::MSSQL:
            case \Yana\Db\Doctrine\DriverEnumeration::MSSQL:
            case \Yana\Db\Doctrine\DriverEnumeration::MSSQL_PDO:
                return self::MSSQL;

            case \Yana\Db\DriverEnumeration::POSTGRESQL:
            case \Yana\Db\Doctrine\DriverEnumeration::POSTGRESQL:
            case \Yana\Db\Doctrine\DriverEnumeration::POSTGRESQL_ALIAS:
            case \Yana\Db\Doctrine\DriverEnumeration::POSTGRESQL_ALIAS2:
            case \Yana\Db\Doctrine\DriverEnumeration::POSTGRESQL_PDO:
                return self::POSTGRESQL;

            case \Yana\Db\DriverEnumeration::SQLLITE:
            case \Yana\Db\Doctrine\DriverEnumeration::SQLITE:
            case \Yana\Db\Doctrine\DriverEnumeration::SQLITE_ALIAS:
            case \Yana\Db\Doctrine\DriverEnumeration::SQLITE_PDO:
                return self::SQLITE;

            case \Yana\Db\DriverEnumeration::ORACLE:
            case \Yana\Db\Doctrine\DriverEnumeration::ORACLE:
            case \Yana\Db\Doctrine\DriverEnumeration::ORACLE_ALIAS:
            case \Yana\Db\Doctrine\DriverEnumeration::ORACLE_PDO:
                return self::ORACLE;

            case \Yana\Db\DriverEnumeration::FRONTBASE:
                return self::FRONTBASE;

            case \Yana\Db\DriverEnumeration::INTERBASE:
                return self::INTERBASE;

            case \Yana\Db\DriverEnumeration::QUERYSIM:
                return self::QUERYSIM;
            // any other
            default:
                return $dbms;
        }
    }

}

?>