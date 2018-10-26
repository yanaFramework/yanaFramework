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

namespace Yana\Db\Doctrine;

/**
 * <<wrapper>> Wrapper / adapter for Doctrine database abstraction layer (DBAL).
 *
 * Of Doctrine we use only the DBAL as an alternative for the deprecated MDB2 database abstraction layer.
 *
 * We won't expose the Doctrine query builder because it is not schema-aware and thus of no use for our purposes.
 *
 * @package     yana
 * @subpackage  db
 *
 * @internal Note that we don't use PDO directly for the simple reason that PDO offers no reverse engineering functionality.
 */
class Driver extends \Yana\Db\Doctrine\AbstractDriver
{

    /**
     * Begin transaction.
     *
     * This deactives auto-commit, so the following statements will wait for commit or rollback.
     *
     * @return  bool
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function beginTransaction()
    {
        try {
            $this->_getDecoratedObject()->beginTransaction();

        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        return true;
    }

    /**
     * Rollback current transaction.
     *
     * @return  bool
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function rollback()
    {
        try {
            $this->_getDecoratedObject()->rollBack();

        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        return true;
    }

    /**
     * Commit current transaction.
     *
     * @return  bool
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function commit()
    {
        try {
            return $this->_getDecoratedObject()->commit();

        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        return true;
    }

    /**
     * Get list of databases.
     *
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listDatabases()
    {
        try {
            /* @var $manager \Doctrine\DBAL\Schema\AbstractSchemaManager */
            $manager = $this->_getDecoratedObject()->getSchemaManager();
            return $manager->listDatabases();

        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        return true;
    }

    /**
     * Get list of tables in current database.
     *
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listTables()
    {
        try {
            /* @var $manager \Doctrine\DBAL\Schema\AbstractSchemaManager */
            $manager = $this->_getDecoratedObject()->getSchemaManager();
            return $manager->listTableNames();

        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        return true;
    }

    /**
     * Get list of functions.
     *
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listFunctions()
    {
        throw new \Yana\Db\DatabaseException('Not supported', \Yana\Log\TypeEnumeration::INFO);
    }

    /**
     * Get list of sequences.
     *
     * @param   string  $database  name of database to query
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listSequences($database = null)
    {
        assert('is_null($database) || is_string($database); // Invalid argument $database: string expected');
        try {
            assert('!isset($connection); // Cannot redeclare var $connection');
            /* @var $connection \Doctrine\DBAL\Connection */
            $connection = $this->_getDecoratedObject();
            assert('!isset($manager); // Cannot redeclare var $manager');
            /* @var $manager \Doctrine\DBAL\Schema\AbstractSchemaManager */
            $manager = $connection->getSchemaManager();
            assert('!isset($platform); // Cannot redeclare var $platform');
            /* @var $platform \Doctrine\DBAL\Platforms\AbstractPlatform */
            $platform = $connection->getDatabasePlatform();

            return $manager->listSequences(is_string($database) ? $platform->quoteIdentifier($database) : null);

        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
    }

    /**
     * Get list of columns.
     *
     * @param   string  $table  table name
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listTableFields($table)
    {
        assert('is_string($table); // Invalid argument $table: string expected');
        try {
            assert('!isset($connection); // Cannot redeclare var $connection');
            /* @var $connection \Doctrine\DBAL\Connection */
            $connection = $this->_getDecoratedObject();
            assert('!isset($manager); // Cannot redeclare var $manager');
            /* @var $manager \Doctrine\DBAL\Schema\AbstractSchemaManager */
            $manager = $connection->getSchemaManager();
            assert('!isset($platform); // Cannot redeclare var $platform');
            /* @var $platform \Doctrine\DBAL\Platforms\AbstractPlatform */
            $platform = $connection->getDatabasePlatform();

            return $manager->listTableColumns($platform->quoteIdentifier($table));

        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
    }

    /**
     * Get list of indexes.
     *
     * @param   string  $table  table name
     * @return  array
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function listTableIndexes($table)
    {
        assert('is_string($table); // Invalid argument $table: string expected');
        assert('is_string($table); // Invalid argument $table: string expected');
        try {
            assert('!isset($connection); // Cannot redeclare var $connection');
            /* @var $connection \Doctrine\DBAL\Connection */
            $connection = $this->_getDecoratedObject();
            assert('!isset($manager); // Cannot redeclare var $manager');
            /* @var $manager \Doctrine\DBAL\Schema\AbstractSchemaManager */
            $manager = $connection->getSchemaManager();
            assert('!isset($platform); // Cannot redeclare var $platform');
            /* @var $platform \Doctrine\DBAL\Platforms\AbstractPlatform */
            $platform = $connection->getDatabasePlatform();

            return $manager->listTableIndexes($platform->quoteIdentifier($table));

        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
    }

    /**
     * Execute a single query.
     *
     * @param   \Yana\Db\Queries\AbstractQuery  $dbQuery  query object
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function sendQueryObject(\Yana\Db\Queries\AbstractQuery $dbQuery)
    {
        return $this->sendQueryString((string) $dbQuery, $dbQuery->getLimit(), $dbQuery->getOffset());
    }

    /**
     * Execute one SQL statement.
     *
     * @param   string  $sqlStmt  SQL statement
     * @param   int     $limit    the number of rows to return for this query
     * @param   int     $offset   the first row to return for this query
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Db\DatabaseException  on failure
     */
    public function sendQueryString($sqlStmt, $limit = 0, $offset = 0)
    {
        assert('is_string($sqlStmt); // Invalid argument $sqlStmt: string expected');
        assert('is_int($limit); // Invalid argument $limit: integer expected');
        assert('is_int($offset); // Invalid argument $offset: integer expected');

        try {
            assert('!isset($connection); // Cannot redeclare var $connection');
            $connection = $this->_getDecoratedObject();
            if ($limit > 0 || $offset > 0) {

                $connection->getDatabasePlatform()->modifyLimitQuery($sqlStmt, (int) $limit, (int) $offset);
            }
            /* @var $statement \Doctrine\DBAL\Statement */
            $statement = $connection->query($sqlStmt);
            return new \Yana\Db\Doctrine\Result($statement);

        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        return true;
    }

    /**
     * quote a value
     *
     * Returns the quoted values as a string
     * surrounded by double-quotes.
     *
     * @param   mixed  $value value too qoute
     * @return  string
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