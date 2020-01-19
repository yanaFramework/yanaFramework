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
 *
 * @ignore
 */
declare(strict_types=1);

namespace Yana\Db\Sources;

/**
 * <<entity>> Database connection setup.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsEntity extends \Yana\Data\Adapters\IsEntity
{

    /**
     * Get the unique name of this data source.
     *
     * @return  string
     */
    public function getName(): string;

    /**
     * Set the unique name of this data source.
     *
     * Note: That the name actually is unique won't be checked until you
     * are trying to actually store this entity in the database.
     *
     * @param  string  $name  alpha-numeric, case-sensitive
     * @return  \Yana\Data\Adapters\IsEntity
     */
    public function setName(string $name): self;

    /**
     * Get Database Management System setting.
     *
     * E.g. "mysql" or "postgresql".
     *
     * @return string
     */
    public function getDbms(): string;

    /**
     * Get host name or IP.
     *
     * @return string
     */
    public function getHost(): string;

    /**
     * Get server port.
     *
     * Returns NULL if none has been set.
     * NULL means "use DBMS-dependent default", e.g. 3306 for MySQL.
     *
     * @return ?int
     */
    public function getPort(): ?int;

    /**
     * Return database name.
     *
     * @return string
     */
    public function getDatabase(): string;

    /**
     * Return user name.
     *
     * This is part of the user credentials.
     *
     * @return string
     */
    public function getUser(): string;

    /**
     * Return user's password.
     *
     * This is part of the user credentials.
     *
     * return string
     */
    public function getPassword(): string;

    /**
     * Set Database Management System setting.
     *
     * E.g. "mysql" or "postgresql".
     * See the respective enumerations for MDB2 and/or Doctrine DBAL.
     *
     * @param   string  $dbms  alpha-numeric DBMS name
     * @return  $this
     */
    public function setDbms(string $dbms): self;

    /**
     * Set host name or IP. 
     *
     * @param   string  $host  a host name or IP
     * @return  $this
     */
    public function setHost(string $host): self;

    /**
     * Set server port.
     *
     * Returns NULL if none has been set.
     * NULL means "use DBMS-dependent default", e.g. 3306 for MySQL.
     *
     * @param   int|null  $port  number, set NULL for default
     * @return  $this
     */
    public function setPort(?int $port): self;

    /**
     * Set database name.
     * 
     * Careful! For some DBMS this setting may be case-sensitive!
     * What's worse, this may vary across version numbers and operation systems.
     * So to be save, ALWAYS treat the database name as case sensitive.
     *
     * @param   string  $database  alpha-numeric database name.
     * @return  $this
     */
    public function setDatabase(string $database): self;

    /**
     * Set user name.
     *
     * This is part of the user credentials.
     *
     * @param   string  $user  alpha-numeric user id
     * @return  $this
     */
    public function setUser(string $user): self;

    /**
     * Set user's password.
     *
     * This is part of the user credentials.
     *
     * @param   string  $password  can be whatever the database consideres a valid password
     * @return  $this
     */
    public function setPassword(string $password): self;

    /**
     * Build and initialize an instance based on DSN settings.
     *
     * @param   array  $dsn  see DsnEnumeration for a list of valid keys
     * @return  \Yana\Db\Sources\IsEntity
     */
    public static function buildFromDsn(array $dsn): \Yana\Db\Sources\IsEntity;

    /**
     * Exports this as a DSN array.
     *
     * @see \Yana\Db\Sources\DsnEnumeration
     * @return array
     */
    public function toDsn(): array;

}

?>