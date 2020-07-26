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

namespace Yana\Db\Export;

/**
 * <<facade>>  database Creator
 *
 * This decorator class is intended to create SQL DDL (data definition language)
 * from YANA Framework - database structure files.
 *
 * For this task it provides functions which create specific
 * DDL for various DBS.
 *
 * @package     yana
 * @subpackage  db
 */
class SqlFacade extends \Yana\Core\StdObject
{

    /**
     * @var  \Yana\Db\Export\SqlFactory
     */
    private $_sqlFactory = null;

    /**
     * @var  \Yana\Db\IsConnection
     */
    private $_connection = null;

    /**
     * <<constructor>> Initialize a new instance.
     *
     * @param   \Yana\Db\IsConnection  $connection  target database
     */
    public function __construct(\Yana\Db\IsConnection $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Loads and returns SQL factory.
     *
     * The SQL factory will take any database schema and generate the required SQL statements.
     *
     * @return  \Yana\Db\Export\SqlFactory
     */
    protected function _getSqlFactory(): \Yana\Db\Export\SqlFactory
    {
        if (!isset($this->_sqlFactory)) {
            $this->_sqlFactory = new \Yana\Db\Export\SqlFactory($this->getConnection()->getSchema());
        }
        return $this->_sqlFactory;
    }

    /**
     * Get database connection.
     *
     * The target database to deploy the generated SQL to.
     *
     * @return \Yana\Db\IsConnection
     */
    public function getConnection(): \Yana\Db\IsConnection
    {
        return $this->_connection;
    }

    /**
     * Create DDL and return statements as array of SQL query strings.
     *
     * @return  array
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when the chosen DBMS is not supported
     * @codeCoverageIgnore SqlFactory has its own unit test 
     */
    protected function _getDdlStatementsFromSchema(): array
    {
        switch (\Yana\Db\DriverEnumeration::mapAliasToDriver($this->getConnection()->getDBMS()))
        {
            case \Yana\Db\DriverEnumeration::MYSQL;
                return $this->_getSqlFactory()->createMySQL();

            case \Yana\Db\DriverEnumeration::DB2;
                return $this->_getSqlFactory()->createDB2();

            case \Yana\Db\DriverEnumeration::MSSQL;
                return $this->_getSqlFactory()->createMSSQL();

            case \Yana\Db\DriverEnumeration::ORACLE;
                return $this->_getSqlFactory()->createOracleDB();

            case \Yana\Db\DriverEnumeration::POSTGRESQL;
                return $this->_getSqlFactory()->createPostgreSQL();

            // Other DBMS are currently not supported
            default:
                $message = "Chosen DBMS is invalid.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Core\Exceptions\NotImplementedException($message, $level);
        }
    }

    /**
     * Encapsulate and return init statements as array of SQL query strings.
     *
     * @return  array
     */
    protected function _getInitStatementsFromSchema(): array
    {
        assert(!isset($connection), 'Cannot redeclare var $connection');
        $connection = $this->getConnection();

        assert(!isset($statements), 'Cannot redeclare var $statements');
        $statements = array();

        assert(!isset($statement), 'Cannot redeclare var $statement');
        foreach ($connection->getSchema()->getInit($connection->getDBMS()) as $statement)
        {
            $statements[] = $statement;
        }
        unset($statement);

        return $statements;
    }

    /**
     * Create and return all DDL and init statements as SQL query strings.
     *
     * @return  array
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when the chosen DBMS is not supported
     */
    protected function _getAllStatements(): array
    {
        return $this->_getDdlStatementsFromSchema() + $this->_getInitStatementsFromSchema();
    }

    /**
     * Create and execture all SQL statements required to deploy the database in question.
     *
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if an SQL statement is not valid
     * @throws  \Yana\Core\Exceptions\NotWriteableException     when the database or table is locked
     * @throws  \Yana\Db\CommitFailedException                  when the commit failed
     * @throws  \Yana\Core\Exceptions\NotImplementedException   when the chosen DBMS is not supported
     */
    public function __invoke()
    {
        assert(!isset($connection), 'Cannot redeclare var $connection');
        $connection = $this->getConnection();

        assert(!isset($sqlQuery), 'Cannot redeclare var $sqlQuery');
        foreach ($this->_getAllStatements() as $sqlQuery)
        {
            $connection->sendQueryString($sqlQuery); // May throw InvalidArgumentException
        }
        unset($sqlQuery);

        $connection->commit(); // May throw CommitFailedException
    }

}

?>