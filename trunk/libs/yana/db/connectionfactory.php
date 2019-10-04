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

namespace Yana\Db;

/**
 * Aids in opening and keeping connections.
 *
 * Keeps track of active connections it has created and reuses them, if the same connection is requested twice.
 *
 * @package     yana
 * @subpackage  db
 */
class ConnectionFactory extends \Yana\Core\StdObject implements \Yana\Db\IsConnectionFactory
{

    /**
     * caches database connections
     *
     * @var  \Yana\Db\ConnectionCollection
     */
    private $_connections = null;

    /**
     * helps resolve schema-names and creates database-schema instances.
     *
     * @var  \Yana\Db\IsSchemaFactory
     */
    private $_schemaFactory = null;

    /**
     * Initialize instance with default values
     *
     * @param  \Yana\Db\IsSchemaFactory  $factory  helps resolve schema-names and creates database-schema instances
     */
    public function __construct(\Yana\Db\IsSchemaFactory $factory)
    {
        $this->_connections = new \Yana\Db\ConnectionCollection();
        $this->_schemaFactory = $factory;
    }

    /**
     * Returns factory that produces schema objects by resolving their names.
     *
     * @return  \Yana\Db\IsSchemaFactory
     */
    protected function _getSchemaFactory()
    {
        return $this->_schemaFactory;
    }

    /**
     * Returns pool of known active connections created by this factory.
     *
     * @return  \Yana\Db\ConnectionCollection
     */
    protected function _getConnections()
    {
        return $this->_connections;
    }

    /**
     * <<factory>> Returns a ready-to-use database connection.
     *
     * Example:
     * <code>
     * // Connect to database using 'config/db/user.db.xml'
     * $db = \Yana\Db\ConnectionFactory();
     * $db->createConnection('user');
     * </code>
     *
     * @param   string|\Yana\Db\Ddl\Database  $schema        name of the database schema file (see config/db/*.xml),
     *                                                       or instance of \Yana\Db\Ddl\Database
     * @param   bool                          $ignoreFileDb  set to bool(true) if you DON'T want to use the fallback File-DB driver
     * @return  \Yana\Db\IsConnection
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no such database was found
     * @throws  \Yana\Db\ConnectionException             when connection to database failed
     */
    public function createConnection($schema, $ignoreFileDb = YANA_DATABASE_ACTIVE)
    {
        if (is_string($schema)) {
            $schema = $this->_getSchemaFactory()->createSchema($schema);
        }
        assert($schema instanceof \Yana\Db\Ddl\Database);

        return $this->_createConnection($schema, $ignoreFileDb);
    }

    /**
     * @param   \Yana\Db\Ddl\Database  $schema        passed on to connection
     * @param   bool                   $ignoreFileDb  set to bool(true) if you DON'T want to use the fallback File-DB driver
     * @return  \Yana\Db\IsConnection
     */
    protected function _createConnection(\Yana\Db\Ddl\Database $schema, $ignoreFileDb)
    {
        $connections = $this->_getConnections();
        $schemaName = $schema->getName();
        if (!isset($connections[$schemaName])) {
            $connection = null;
            if ($ignoreFileDb && \Yana\Db\Doctrine\ConnectionFactory::isDoctrineAvailable()) {
                $connection = $this->_buildDoctrineConnection($schema); // may return NULL

            }
            if (is_null($connection) && $ignoreFileDb && \Yana\Db\Mdb2\ConnectionFactory::isMdb2Available()) {
                $connection = $this->_buildMdb2Connection($schema); // may return NULL

            }
            if (is_null($connection)) {
                $connection = $this->_buildFileDbConnection($schema);
            }
            $connections[$schemaName] = $connection;
        }
        return $connections[$schemaName];
    }

    /**
     * Build Doctrine connection.
     *
     * Returns NULL on failure.
     *
     * @param   \Yana\Db\Ddl\Database  $schema  passed on to connection
     * @return  \Yana\Db\Doctrine\Connection?
     * @codeCoverageIgnore
     */
    protected function _buildDoctrineConnection(\Yana\Db\Ddl\Database $schema)
    {
        try {
            return new \Yana\Db\Doctrine\Connection($schema);

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Build Pear MDB2 connection.
     *
     * Returns NULL on failure.
     *
     * @param   \Yana\Db\Ddl\Database  $schema  passed on to connection
     * @return  \Yana\Db\Mdb2\Connection?
     * @codeCoverageIgnore
     */
    protected function _buildMdb2Connection(\Yana\Db\Ddl\Database $schema)
    {
        try {
            return new \Yana\Db\Mdb2\Connection($schema);

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Build Yana File-DB connection.
     *
     * This always works (well, at least we hope so - it's our last option anyways if all else fails).
     *
     * @param   \Yana\Db\Ddl\Database  $schema  passed on to connection
     * @return  \Yana\Db\FileDb\Connection
     */
    protected function _buildFileDbConnection(\Yana\Db\Ddl\Database $schema)
    {
        return new \Yana\Db\FileDb\Connection($schema);
    }

}

?>