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

namespace Yana\Db\Mdb2;

/**
 * <<wrapper>> Wrapper / adapter for PEAR MDB2 database driver.
 *
 * @package     yana
 * @subpackage  db
 */
class Driver extends \Yana\Core\AbstractDecorator implements \Yana\Db\IsDriver
{

    /**
     * @var \Yana\Db\Mdb2\IsExceptionFactory
     */
    private $_exceptionFactory = null;

    /**
     * constructor
     *
     * @param  \Yana\Db\Ddl\Database             $schema   database schema
     * @param  \Yana\Db\Mdb2\IsExceptionFactory  $factory  use this to inject a mock factory for unit tests
     */
    public function __construct(\MDB2_Driver_Common $driver, \Yana\Db\Mdb2\IsExceptionFactory $factory = null)
    {
        $this->_exceptionFactory = $factory;
        $this->_setDecoratedObject($driver);
    }

    /**
     * Checks if the return value is an error code and throws an exception if so.
     *
     * Returns the value unchanged if it is valid.
     *
     * @return  mixed
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    protected function _checkReturnValue($errorCode)
    {
        if ($errorCode instanceof \MDB2_Error) {
            // @codeCoverageIgnoreStart
            if ($this->_exceptionFactory === null) {
                $this->_exceptionFactory = new \Yana\Db\Mdb2\ExceptionFactory();
            }
            // @codeCoverageIgnoreEnd
            throw $this->_exceptionFactory->toException($errorCode);
        }
        return $errorCode;
    }

    /**
     * Returns the instance that all calls will be relayed to.
     *
     * @return \MDB2_Driver_Common
     */
    protected function _getDecoratedObject()
    {
        return parent::_getDecoratedObject();
    }

    /**
     * begin transaction
     *
     * This deactives auto-commit, so the following statements will wait for commit or rollback.
     *
     * @return  bool
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function beginTransaction()
    {
        return $this->_checkReturnValue($this->_getDecoratedObject()->beginTransaction()) === \MDB2_OK;
    }

    /**
     * rollback current transaction
     *
     * @return  bool
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function rollback()
    {
        return $this->_checkReturnValue($this->_getDecoratedObject()->beginTransaction()) === \MDB2_OK;
    }

    /**
     * commit current transaction
     *
     * @return  bool
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function commit()
    {
        return $this->_checkReturnValue($this->_getDecoratedObject()->commit()) === \MDB2_OK;
    }

    /**
     * get list of databases
     *
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listDatabases()
    {
        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $this->_checkReturnValue($connection->listDatabases());
    }

    /**
     * get list of tables in current database
     *
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listTables()
    {
        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $this->_checkReturnValue($connection->listTables());
    }

    /**
     * get list of functions
     *
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listFunctions()
    {
        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $this->_checkReturnValue($connection->listFunctions());
    }

    /**
     * get list of functions
     *
     * @param   string  $database  dummy for compatibility
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listSequences($database = null)
    {
        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $this->_checkReturnValue($connection->listSequences($database));
    }

    /**
     * get list of columns
     *
     * @param   string  $table  table name
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listTableFields($table)
    {
        assert('is_string($table); // Invalid argument $table: string expected');

        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $this->_checkReturnValue($connection->listTableFields($table));
    }

    /**
     * get list of indexes
     *
     * @param   string  $table  table name
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listTableIndexes($table)
    {
        assert('is_string($table); // Invalid argument $table: string expected');

        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $this->_checkReturnValue($connection->listTableIndexes($table));
    }

    /**
     * Execute a single query.
     *
     * @param   \Yana\Db\Queries\AbstractQuery  $dbQuery  query object
     * @return  \Yana\Db\Mdb2\Result
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function sendQueryObject(\Yana\Db\Queries\AbstractQuery $dbQuery)
    {
        $resultObject = $this->_checkReturnValue($this->sendQueryString((string) $dbQuery, $dbQuery->getLimit(), $dbQuery->getOffset()));
        return new \Yana\Db\Mdb2\Result($resultObject);
    }

    /**
     * Execute a single query.
     *
     * Alias of limitQuery() with $offset and $limit params stripped.
     *
     * @param   string  $sqlStmt  SQL statement
     * @param   int     $limit    the maximum number of rows in the resultset
     * @param   int     $offset   the row to start from
     * @return  \Yana\Db\Mdb2\Result
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function sendQueryString($sqlStmt, $limit = 0, $offset = 0)
    {
        assert('is_string($sqlStmt); // Invalid argument $sqlStmt: string expected');

        $this->_getDecoratedObject()->setLimit($limit, $offset > 0 ? $offset : null);
        $resultObject = $this->_checkReturnValue($this->_getDecoratedObject()->query($sqlStmt));
        return new \Yana\Db\Mdb2\Result($resultObject);
    }

    /**
     * quote a value
     *
     * Returns the quoted values as a string
     * surrounded by double-quotes.
     *
     * @param   mixed  $value value too qoute
     * @return  string
     * @ignore
     */
    public function quote($value)
    {
        return $this->_getDecoratedObject()->quote($value);
    }

    /**
     * quote an identifier
     *
     * Returns the quotes Id as a string
     * surrounded by double-quotes.
     *
     * @param   string  $value  value
     * @return  string
     * @ignore
     */
    public function quoteIdentifier($value)
    {
        assert('is_string($value); // Invalid argument $value: string expected');

        return $this->_getDecoratedObject()->quoteIdentifier($value);
    }

    /**
     * Compare with another object.
     *
     * Returns bool(true) if this object and $anotherObject
     * are equal and bool(false) otherwise.
     *
     * Two instances are considered equal if and only if
     * they are both objects of the same class and they both
     * refer to the same connection.
     *
     * @param    \Yana\Core\IsObject  $anotherObject object to compare
     * @return   string
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        if ($anotherObject instanceof $this) {
            return $this->_getDecoratedObject() == $anotherObject->_getDecoratedObject();
        }
        return false;
    }

}

?>