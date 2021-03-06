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

namespace Yana\Db\FileDb;

/**
 * <<decorator>> Simulates a sql-database on a flat-file.
 *
 * Example:
 * <code>
 * $schema = XDDL::getDatabase('log');
 * $db = new FileDb($schema);
 * print_r($db->select('log.*'));
 * </code>
 *
 * You might also want to see the factory function
 * {@link \Yana\Application::connect()}.
 *
 * @package     yana
 * @subpackage  db
 */
class Connection extends \Yana\Db\AbstractConnection
{

    /**
     * @var  \Yana\Db\FileDb\Driver
     */
    private $_database = null;

    /**
     * Creates a new instance of this class.
     *
     * @param  \Yana\Db\Ddl\Database  $schema  schema in database definition language
     */
    public function __construct(\Yana\Db\Ddl\Database $schema)
    {
        parent::__construct($schema);
    }

    /**
     * Returns the name of the chosen DBMS as a lower-cased string.
     *
     * @return  string
     */
    public function getDBMS(): string
    {
        return \Yana\Db\DriverEnumeration::GENERIC;
    }

    /**
     * Import SQL from a file.
     *
     * Not implemented for FileDb-Drivers.
     *
     * @param   string|array  $sqlFile filename which contain the SQL statments or an nummeric array of SQL statments.
     * @return  bool
     */
    public function importSQL($sqlFile): bool
    {
        return true;
    }

    /**
     * Smart id quoting.
     *
     * No quoting needed for FileDb drivers.
     *
     * @param   mixed  $value  name of database object
     * @return  string
     * @ignore
     */
    public function quoteId($value): string
    {
        return (string) $value;
    }

    /**
     * Send SQL-statement directly to the FileDB driver.
     *
     * Note: FileDB was designed to understand the statements generated
     * by the query-builder and is not intended to be called directly.
     *
     * This will nevertheless try to parse the statement (if a fitting SQL parser implementation is available)
     * and return the result.
     *
     * @param   string  $sqlStmt  one SQL statement to execute
     * @param   int     $offset   the row to start from
     * @param   int     $limit    the maximum numbers of rows in the resultset
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the SQL statement is not valid
     */
    public function sendQueryString(string $sqlStmt, int $offset = 0, int $limit = 0): \Yana\Db\IsResult
    {
        assert($offset >= 0, 'Invalid argument $offset. Must be a positive integer.');
        assert($limit >= 0, 'Invalid argument $limit. Must be a positive integer.');

        // send query to database
        $connection = $this->_getDriver();
        return $connection->sendQueryString($sqlStmt, $limit, $offset); // may throw exception
    }

    /**
     * Send a prepared database statement to the FileDB driver.
     *
     * @param   \Yana\Db\Queries\AbstractQuery  $query  one SQL statement to execute
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the SQL statement is not valid
     */
    public function sendQueryObject(\Yana\Db\Queries\AbstractQuery $query)
    {
        $connection = $this->_getDriver();
        return $connection->sendQueryObject($query); // may throw exception
    }

    /**
     * Get database connection.
     *
     * @return  \Yana\Db\FileDb\Driver
     * @ignore
     */
    protected function _getDriver(): \Yana\Db\IsDriver
    {
        if (!isset($this->_database)) {
            $this->_database = new \Yana\Db\FileDb\Driver(new \Yana\Db\Queries\Parser($this));
        }
        return $this->_database;
    }


    /**
     * Set database connection.
     *
     * @param   \Yana\Db\IsDriver  $driver
     * @return  $this
     * @ignore
     */
    protected function _setDriver(\Yana\Db\IsDriver $driver)
    {
        $this->_database = $driver;
        return $this;
    }

}

?>