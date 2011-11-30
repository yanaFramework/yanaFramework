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

namespace Yana\Db;

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
 * {@link Yana::connect()}.
 *
 * @package     yana
 * @subpackage  db
 */
class FileDbConnection extends \Yana\Db\AbstractConnection
{

    /**
     * Creates a new instance of this class.
     *
     * @param  \Yana\Db\Ddl\Database  $schema  schema in database definition language
     */
    public function __construct(\Yana\Db\Ddl\Database $schema)
    {
        parent::__construct($schema);
        $this->dsn = array(
            'USE_ODBC' => false,
            'DBMS'     => '',
            'HOST'     => false,
            'PORT'     => false,
            'USERNAME' => false,
            'PASSWORD' => false,
            'DATABASE' => $this->getName()
        );
    }

    /**
     * Import SQL from a file.
     *
     * Not implemented for FileDb-Drivers.
     *
     * @param   string|array  $sqlFile filename which contain the SQL statments or an nummeric array of SQL statments.
     * @return  bool
     */
    public function importSQL($sqlFile)
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
    public function quoteId($value)
    {
        return (string) $value;
    }

    /**
     * optional API bypass
     *
     * Send a sinle sql-statement directly to FileDB, bypassing the query-builder.
     *
     * The function will return bool(false) if the database connection is not
     * available and otherwise will return whatever FileDB sends back as the
     * result of your statement.
     *
     * Note: FileDB was designed to understand the statements generated
     * by the query-builder and is not intended to be called directly.
     * This means it will understand only SQL statements that comply with the syntax
     * that is provided by the query-builder.
     *
     * @param   string|object  $sqlStmt  one SQL statement to execute
     * @param   int            $offset   the row to start from
     * @param   int            $limit    the maximum numbers of rows in the resultset
     * @return  mixed
     * @since   2.8.8
     * @ignore
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the SQL statement is not valid
     */
    public function query($sqlStmt, $offset = 0, $limit = 0)
    {
        assert('is_int($offset) && $offset >= 0; // Invalid argument $offset. Must be a positive integer.');
        assert('is_int($limit) && $limit >= 0; // Invalid argument $limit. Must be a positive integer.');

        // check input
        if ($sqlStmt instanceof \Yana\Db\Queries\AbstractQuery) {
            return $this->getConnection()->dbQuery($sqlStmt);
        } elseif (!is_string($sqlStmt)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException('Argument $sqlStmt is expected to be a string.');
        }

        $reg = "/;.*(?:select|insert|delete|update|create|alter|grant|revoke).*$/is";
        if (strpos($sqlStmt, ';') !== false && preg_match($reg, $sqlStmt)) {
            $message = "A semicolon has been found in the current input '{$sqlStmt}', " .
                "indicating multiple queries.\n\t\t As this might be the result of a hacking attempt " .
                "it is prohibited for security reasons and the queries won't be executed.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        // send query to database
        return $this->getConnection()->limitQuery($sqlStmt, $offset, $limit);
    }

    /**
     * Returns bool(true) if the object is an error result.
     *
     * @param   mixed  $result  result
     * @return  bool
     * @ignore
     */
    public function isError($result)
    {
        return ($result instanceof \Yana\Db\FileDb\Result && $result->isError());
    }

    /**
     * get database connection
     *
     * @return  \MDB2_Driver_Common
     * @ignore
     */
    protected function getConnection()
    {
        if (!isset($this->database)) {
            $this->database = new \Yana\Db\FileDb\Driver($this->getSchema());
            $this->database->autoCommit(false);
        }
        return $this->database;
    }

}

?>