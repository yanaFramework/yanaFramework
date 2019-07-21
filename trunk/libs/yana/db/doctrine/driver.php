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

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        // @codeCoverageIgnoreEnd
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
            if ($this->_getDecoratedObject()->isTransactionActive()) {
                $this->_getDecoratedObject()->rollBack();
            }

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        // @codeCoverageIgnoreEnd
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
            if ($this->_getDecoratedObject()->isTransactionActive()) {
                $this->_getDecoratedObject()->commit();
            }

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        // @codeCoverageIgnoreEnd
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

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        // @codeCoverageIgnoreEnd
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

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        // @codeCoverageIgnoreEnd
        return true;
    }

    /**
     * Get list of functions.
     *
     * Doctrine doesn't support auto-discovery of functions. When called, this will always throw an exception.
     *
     * @return  array
     * @throws  \Yana\Db\DatabaseException  always
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
     * @codeCoverageIgnore
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

            assert('!isset($sequencesNames); // Cannot redeclare var $sequencesNames');
            $sequencesNames = array();
            foreach ($manager->listSequences() as $sequence)
            {
                /* @var $sequence \Doctrine\DBAL\Schema\Sequence */
                $sequencesNames[] = $sequence->getName();
            }
            unset($sequence);

            return $sequencesNames;

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        // @codeCoverageIgnoreEnd
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

            assert('!isset($columnNames); // Cannot redeclare var $columnNames');
            $columnNames = array();
            assert('!isset($column); // Cannot redeclare var $column');
            foreach ($manager->listTableColumns($table) as $column)
            {
                /* @var $column \Doctrine\DBAL\Schema\Column */
                $columnNames[] = $column->getName();
            }
            unset($column);

            return $columnNames;

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        // @codeCoverageIgnoreEnd
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

            assert('!isset($indexNames); // Cannot redeclare var $indexNames');
            $indexNames = array();
            assert('!isset($index); // Cannot redeclare var $index');
            foreach ($manager->listTableIndexes($table) as $index)
            {
                /* @var $index \Doctrine\DBAL\Schema\Index */
                $indexNames[] = $index->getName();
            }
            unset($index);

            return $indexNames;

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        // @codeCoverageIgnoreEnd
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
     * @throws  \Yana\Db\DatabaseException                       on failure
     * @throws  \Yana\Db\Queries\Exceptions\ConstraintException  when a constraint was violated
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

                $sqlStmt = $connection->getDatabasePlatform()->modifyLimitQuery($sqlStmt, (int) $limit, (int) $offset);
            }
            /* @var $statement \Doctrine\DBAL\Statement */
            $statement = $connection->query($sqlStmt);
            return new \Yana\Db\Doctrine\Result($statement);

            // @codeCoverageIgnoreStart
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new \Yana\Db\Queries\Exceptions\ConstraintException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);

        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
            throw new \Yana\Db\Queries\Exceptions\ConstraintException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);

        } catch (\Exception $e) {
            throw new \Yana\Db\DatabaseException($e->getMessage(), \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        // @codeCoverageIgnoreEnd
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
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
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
        $isEqual = false;
        if ($anotherObject instanceof $this) {
            $thisDsn = $this->_getDecoratedObject()->getParams();
            $otherDsn = $anotherObject->_getDecoratedObject()->getParams();
            $isEqual = $thisDsn === $otherDsn;
        }
        return $isEqual;
    }

}

?>