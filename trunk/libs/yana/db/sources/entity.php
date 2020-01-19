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
class Entity extends \Yana\Data\Adapters\AbstractEntity implements \Yana\Db\Sources\IsEntity
{

    /**
     * @var int
     */
    private $_id = null;

    /**
     * @var string
     */
    private $_name = "";

    /**
     * @var string
     */
    private $_dbms = "";

    /**
     * @var string
     */
    private $_host = "";

    /**
     * @var ?int
     */
    private $_port = null;

    /**
     * @var string
     */
    private $_database = null;

    /**
     * @var string
     */
    private $_user = null;

    /**
     * @var string
     */
    private $_password = null;

    /**
     * Build and initialize an instance based on DSN settings.
     *
     * @param   array  $dsn  see DsnEnumeration for a list of valid keys
     * @return  \Yana\Db\Sources\IsEntity
     */
    public static function buildFromDsn(array $dsn): \Yana\Db\Sources\IsEntity
    {
        $entity = new self();
        if (isset($dsn[\Yana\Db\Sources\DsnEnumeration::DATABASE])) {
            $entity->setDatabase((string) $dsn[\Yana\Db\Sources\DsnEnumeration::DATABASE]);
        }
        if (isset($dsn[\Yana\Db\Sources\DsnEnumeration::DBMS])) {
            $entity->setDbms((string) $dsn[\Yana\Db\Sources\DsnEnumeration::DBMS]);
        }
        if (isset($dsn[\Yana\Db\Sources\DsnEnumeration::HOST])) {
            $entity->setHost((string) $dsn[\Yana\Db\Sources\DsnEnumeration::HOST]);
        }
        if (isset($dsn[\Yana\Db\Sources\DsnEnumeration::PASSWORD])) {
            $entity->setPassword((string) $dsn[\Yana\Db\Sources\DsnEnumeration::PASSWORD]);
        }
        if (!empty($dsn[\Yana\Db\Sources\DsnEnumeration::PORT])) {
            $entity->setHost((int) $dsn[\Yana\Db\Sources\DsnEnumeration::PORT]);
        }
        if (isset($dsn[\Yana\Db\Sources\DsnEnumeration::USER])) {
            $entity->setUser((string) $dsn[\Yana\Db\Sources\DsnEnumeration::USER]);
        }
        return $entity;
    }

    /**
     * Exports this as a DSN array.
     *
     * @see \Yana\Db\Sources\DsnEnumeration
     * @return array
     */
    public function toDsn(): array
    {
        return array(
            \Yana\Db\Sources\DsnEnumeration::DATABASE => $this->getDatabase(),
            \Yana\Db\Sources\DsnEnumeration::DBMS => $this->getDbms(),
            \Yana\Db\Sources\DsnEnumeration::HOST => $this->getHost(),
            \Yana\Db\Sources\DsnEnumeration::PASSWORD => $this->getPassword(),
            \Yana\Db\Sources\DsnEnumeration::PORT => $this->getPort(),
            \Yana\Db\Sources\DsnEnumeration::USER => $this->getUser(),
        );
    }

    /**
     * Get the database ID of this data source.
     *
     * This may return NULL if the database entry has not yet been saved.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set the database ID of this data source.
     *
     * @param  int  $id  database ID to use
     * @return  \Yana\Data\Adapters\IsEntity
     */
    public function setId($id)
    {
        assert(is_scalar($id), 'Wrong type for argument 1. Integer expected');
        $this->_id = (int) $id;
        return $this;
    }

    /**
     * Get the unique name of this data source.
     *
     * @return  string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * Set the unique name of this data source.
     *
     * Note: That the name actually is unique won't be checked until you
     * are trying to actually store this entity in the database.
     *
     * @param  string  $name  alpha-numeric, case-sensitive
     * @return  \Yana\Data\Adapters\IsEntity
     */
    public function setName(string $name): \Yana\Db\Sources\IsEntity
    {
        $this->_name = (string) $name;
        return $this;
    }

    /**
     * Get Database Management System setting.
     *
     * E.g. "mysql" or "postgresql".
     *
     * @return string
     */
    public function getDbms(): string
    {
        return $this->_dbms;
    }

    /**
     * Get host name or IP.
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->_host;
    }

    /**
     * Get server port.
     *
     * Returns NULL if none has been set.
     * NULL means "use DBMS-dependent default", e.g. 3306 for MySQL.
     *
     * @return ?int
     */
    public function getPort(): ?int
    {
        return $this->_port;
    }

    /**
     * Return database name.
     *
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->_database;
    }

    /**
     * Return user name.
     *
     * This is part of the user credentials.
     *
     * @return string
     */
    public function getUser(): string
    {
        return $this->_user;
    }

    /**
     * Return user's password.
     *
     * This is part of the user credentials.
     *
     * return string
     */
    public function getPassword(): string
    {
        return $this->_password;
    }

    /**
     * Set Database Management System setting.
     *
     * E.g. "mysql" or "postgresql".
     * See the respective enumerations for MDB2 and/or Doctrine DBAL.
     *
     * @param   string  $dbms  alpha-numeric DBMS name
     * @return  $this
     */
    public function setDbms(string $dbms): \Yana\Db\Sources\IsEntity
    {
        $this->_dbms = $dbms;
        return $this;
    }

    /**
     * Set host name or IP. 
     *
     * @param   string  $host  a host name or IP
     * @return  $this
     */
    public function setHost(string $host): \Yana\Db\Sources\IsEntity
    {
        $this->_host = $host;
        return $this;
    }

    /**
     * Set server port.
     *
     * Returns NULL if none has been set.
     * NULL means "use DBMS-dependent default", e.g. 3306 for MySQL.
     *
     * @param   int|null  $port  number, set NULL for default
     * @return  $this
     */
    public function setPort(?int $port): \Yana\Db\Sources\IsEntity
    {
        $this->_port = $port;
        return $this;
    }

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
    public function setDatabase(string $database): \Yana\Db\Sources\IsEntity
    {
        $this->_database = $database;
        return $this;
    }

    /**
     * Set user name.
     *
     * This is part of the user credentials.
     *
     * @param   string  $user  alpha-numeric user id
     * @return  $this
     */
    public function setUser(string $user): \Yana\Db\Sources\IsEntity
    {
        $this->_user = $user;
        return $this;
    }

    /**
     * Set user's password.
     *
     * This is part of the user credentials.
     *
     * @param   string  $password  can be whatever the database consideres a valid password
     * @return  $this
     */
    public function setPassword(string $password): \Yana\Db\Sources\IsEntity
    {
        $this->_password = $password;
        return $this;
    }

}

?>