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

namespace Yana\Db\Doctrine;

/**
 * <<factory>> Connect to a database server.
 *
 * This class provides methods to establish or test connections with database servers.
 *
 * @package     yana
 * @subpackage  db
 */
class ConnectionFactory extends \Yana\Core\Object implements \Yana\Db\Doctrine\IsConnectionFactory
{

    /**
     * @var mixed
     */
    private $_connection = null;

    /**
     * List of drivers:
     *
     * pdo_mysql {
     *      user
     *      password
     *      host
     *      port
     *      dbname
     *      unix_socket
     *      charset
     * }
     * mysqli {
     *      user
     *      password
     *      host
     *      port
     *      dbname
     *      unix_socket
     *      charset
     *      ssl_key
     *      ssl_cert
     *      ssl_ca
     *      ssl_capath
     *      ssl_cipher
     *      driverOptions {@link{http://www.php.net/manual/en/mysqli.real-connect.php}}
     * }
     * pdo_sqlite {
     *      user
     *      password
     *      path
     *      memory true = in memory database, false = default
     * }
     * drizzle_pdo_mysql {
     *      user
     *      password
     *      host
     *      port
     *      dbname
     *      unix_socket
     * }
     * pdo_pgsql {
     *      user
     *      password
     *      host
     *      port
     *      dbname
     *      charset
     *      default_dbname
     *      sslmode
     *      sslcert
     *      sslkey
     *      sslcrl certificate revocation list
     *      sslrootcert
     *      application_name some fancy name you can make up for the application
     * }
     * sqlsrv {
     *      user
     *      password
     *      host
     *      port
     *      dbname
     * }
     * oci8 {
     *      user
     *      password
     *      host
     *      port
     *      dbname
     *      servicename
     *      pooled
     *      charset
     *      instancename
     *      connectstring
     *      persistent
     * }
     * sqlanywhere {
     *      user
     *      password
     *      host
     *      port
     *      dbname
     *      persistent (bool) for persistent connections that remain open and can be reused
     * }
     *
     * Or:
     * 'pdo' => $anyInstanceOfPdo
     *
     * @link https://www.doctrine-project.org/projects/doctrine-dbal/en/2.8/reference/configuration.html
     * @var array
     */
    private $_dsn =  array(
        'DBMS' => YANA_DATABASE_DBMS,
        'HOST' => YANA_DATABASE_HOST,
        'PORT' => YANA_DATABASE_PORT,
        'USERNAME' => YANA_DATABASE_USER,
        'PASSWORD' => YANA_DATABASE_PASSWORD,
        'DATABASE' => YANA_DATABASE_NAME
    );

    /**
     * <<constructor>> Create a new instance.
     *
     * The $dsn parameter is an array containing the following information (all entries are optional):
     *
     * <ul>
     *     <li><pre> string  ( DBMS )     default =  mysql      name of the php-dbms api to be used
     *                                            e.g. mysql, mysqli, db2, ... </pre></li>
     *     <li><pre> string  ( HOST )     default =  localhost  adress of the host e.g. localhost,
     *                                            123.456.789.0,
     *                                            COMPUTER-NAME\DB-INSTANCE (windows+MS-SQL)</pre></li>
     *     <li><pre> integer ( PORT )     default =  0          port number </pre></li>
     *     <li><pre> string  ( USERNAME ) default =  root </pre></li>
     *     <li><pre> string  ( PASSWORD ) default =  n/a </pre></li>
     *     <li><pre> string  ( DATABASE ) default =  yana       name of the database </pre></li>
     * </ul>
     *
     * The default settings may be changed by editing file config/system.config in key DEFAULT.DATABASE.DSN.
     * The parameter $dsn has the following fall-back behaviour: $dsn -> global user settings -> yana default settings
     *
     * @param   array  $dsn  for a description of the $dsn parameter see the text above
     * @throws  \Yana\Db\Doctrine\DbalException  when Doctrine DBAL is not available
     */
    public function __construct(array $dsn = null)
    {
        if (!class_exists('\Doctrine\DBAL\DriverManager')) {
            /* error handling */
            $message = "Unable to open connection to database using Doctrine DBAL. Might not be installed.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Db\Doctrine\DbalException($message, $level);
        }

        // process settings provided by the user
        if (is_array($dsn)) {
            $dsn = \Yana\Util\Hashtable::changeCase($dsn, CASE_UPPER);
            $this->_dsn = \Yana\Util\Hashtable::merge($this->_dsn, $dsn);
        }
    }


    /**
     * Get the DSN record.
     *
     * Returns an associative array containing information on the current connection:
     *
     * <ul>
     *   <li>driver: name of used database system</li>
     *   <li>host: host name, e.g. localhost</li>
     *   <li>port: port number of database server (may be empty)</li>
     *   <li>user</li>
     *   <li>password</li>
     *   <li>dbname: name of database to connect to</li>
     * </ul>
     *
     * @return  array
     */
    protected function _getDsnForDoctrine()
    {
        assert('is_array($this->_dsn);');
        $dsn = array();

        if (isset($this->_dsn['DBMS'])) {
            $dsn['driver'] = (string) $this->_dsn['DBMS'];
        }
        if (isset($this->_dsn['HOST'])) {
            $dsn['host'] = (string) $this->_dsn['HOST'];
        }
        if (isset($this->_dsn['PORT']) && is_numeric($this->_dsn['PORT']) && $this->_dsn['PORT'] > 0) {
            $dsn['port'] = (int) $this->_dsn['PORT'];
        }
        if (!empty($this->_dsn['USERNAME'])) {
            $dsn['user'] = $this->_dsn['USERNAME'];
        }
        if (isset($this->_dsn['PASSWORD'])) {
            $dsn['password'] = $this->_dsn['PASSWORD'];
        }
        if (isset($this->_dsn['DATABASE'])) {
            $dsn['dbname'] = $this->_dsn['DATABASE'];
        }
        assert('is_array($dsn);');
        return $dsn;
    }

    /**
     * Returns an open database connection.
     *
     * @return  \Doctrine\DBAL\Connection
     * @throws  \Yana\Db\ConnectionException   when the DSN-settings are invalid
     */
    public function getConnection()
    {
        if (!isset($this->_connection)) {
            try {
                $connection = \Doctrine\DBAL\DriverManager::getConnection($this->_getDsnForDoctrine(), new \Doctrine\DBAL\Configuration());

            } catch (\Doctrine\DBAL\DBALException $e) {
                $_message = "DATABASE NOT AVAILABLE: Unable to establish connection with database server.\n\t\t".
                            "Open administration panel and choose \"database administration\" to edit settings";

                /* Don't output passwords */
                $dsn['password'] = (empty($dsn['password'])) ? 'NO' : 'YES';
                $data = $e->getMessage() . "\nUsing DSN:\n" . print_r($dsn, true);

                $exception = new \Yana\Db\ConnectionException($_message . ': ' . $data, \Yana\Log\TypeEnumeration::ERROR, $e);
                $exception->setData($data);
                throw $exception;
            }
            $this->_connection = $connection;
        }
        return $this->_connection;
    }

    /**
     * Get the DSN record.
     *
     * Returns an associative array containing information on the current connection:
     *
     * <ul>
     *   <li>DBMS: name of used database system</li>
     *   <li>HOST: host name, e.g. localhost</li>
     *   <li>PORT: port number of database server (may be empty)</li>
     *   <li>USERNAME</li>
     *   <li>PASSWORD</li>
     *   <li>DATABASE: name of database to connect to</li>
     * </ul>
     *
     * @return  array
     */
    public function getDsn()
    {
        assert('is_array($this->_dsn);');
        return (array) $this->_dsn;
    }

    /**
     * Test if a connection is available.
     *
     * Returns bool(true) if a connection to a db-server could be established via the provided parameters,
     * and bool(false) otherwise. See the constructor method for details on the $dsn parameter
     *
     * @param   array  $dsn   dns (info data for connection)
     * @return  bool
     */
    public static function isAvailable(array $dsn)
    {
        try {
            $factory = new self($dsn);
            $connection = $factory->getConnection(); // throws exception
            $connection->connect(); // throws exception
            $connection->close();
            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

}

?>