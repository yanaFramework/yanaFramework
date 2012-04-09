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
 * <<adapter>> Wrapper for PEAR MDB2 database driver.
 *
 * @package     yana
 * @subpackage  db
 */
class Driver extends \Yana\Core\AbstractDecorator implements \Yana\Db\IsDriver
{

    /**
     * constructor
     *
     * @param  \Yana\Db\Ddl\Database  $schema  database schema
     */
    public function __construct(\MDB2_Driver_Common $driver)
    {
        $this->_setDecoratedObject($driver);
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
     */
    public function beginTransaction()
    {
        return $this->_getDecoratedObject()->beginTransaction() === \MDB2_OK;
    }

    /**
     * rollback current transaction
     *
     * @return  bool
     */
    public function rollback()
    {
        return $this->_getDecoratedObject()->beginTransaction() === \MDB2_OK;
    }

    /**
     * commit current transaction
     *
     * @return  bool
     */
    public function commit()
    {
        return $this->_getDecoratedObject()->commit() === \MDB2_OK;
    }

    /**
     * get list of databases
     *
     * @return  array
     */
    public function listDatabases()
    {
        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $connection->listDatabases();
    }

    /**
     * get list of tables in current database
     *
     * @param   string  $database  database nasme
     * @return  array
     */
    public function listTables($database = null)
    {
        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $connection->listTables($database = null);
    }

    /**
     * get list of functions
     *
     * @return  array
     */
    public function listFunctions()
    {
        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $connection->listFunctions();
    }

    /**
     * get list of functions
     *
     * @param   string  $database  dummy for compatibility
     * @return  array
     */
    public function listSequences($database = null)
    {
        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $connection->listSequences($database = null);
    }

    /**
     * get list of columns
     *
     * @param   string  $table  table name
     * @return  array
     */
    public function listTableFields($table)
    {
        assert('is_string($table); // Invalid argument $table: string expected');

        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $connection->listTableFields($table);
    }

    /**
     * get list of indexes
     *
     * @param   string  $table  table name
     * @return  array
     */
    public function listTableIndexes($table)
    {
        assert('is_string($table); // Invalid argument $table: string expected');

        $connection = $this->_getDecoratedObject();
        /* @var $connection \MDB2_Driver_Manager_Common */
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');
        return $connection->listTableIndexes($table);
    }

    /**
     * Execute a single query.
     *
     * @param   \Yana\Db\Queries\AbstractQuery  $dbQuery  query object
     * @return  \Yana\Db\FileDb\Result
     */
    public function sendQueryObject(\Yana\Db\Queries\AbstractQuery $dbQuery)
    {
        $this->setLimit($dbQuery->getLimit(), $dbQuery->getOffset());
        return $this->sendQueryString((string) $sqlStmt);
    }

    /**
     * Execute a single query.
     *
     * Alias of limitQuery() with $offset and $limit params stripped.
     *
     * @param   string  $sqlStmt    sql statement
     * @return  \Yana\Db\FileDb\Result
     */
    public function sendQueryString($sqlStmt)
    {
        assert('is_string($sqlStmt); // Invalid argument $sqlStmt: string expected');

        return $this->_getDecoratedObject()->query($sqlStmt);
    }

    /**
     * Set the limit and offset for next query
     *
     * This sets the limit and offset values for the next query.
     * After the query is executed, these values will be reset to 0.
     *
     * @param   int $limit  set the limit for query
     * @param   int $offset set the offset for query
     * @return  bool
     */
    public function setLimit($limit, $offset = null)
    {
        assert('is_string($limit); // Wrong type for argument 1. Integer expected');
        assert('is_null($offset) || is_int($offset); // Wrong type for argument 2. Integer expected');

        if ($offset > 0 || $limit > 0) {
            return $this->_getDecoratedObject()->setLimit($limit, $offset) === \MDB2_OK;
        }
        return false;
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
     * refer to the same structure file.
     *
     * @param    \Yana\Core\IsObject  $anotherObject object to compare
     * @return   string
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        if ($anotherObject instanceof $this) {
            if (!isset($this->_schema) || !isset($anotherObject->_schema)) {
                return isset($this->_schema) === isset($anotherObject->_schema);
            }
            return (bool) $this->_schema->equals($anotherObject->_schema);
        }
        return false;
    }

}

?>