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
 * Aids in opening and keeping connections.
 *
 * Keeps track of active connections it has created and reuses them, if the same connection is requested twice.
 *
 * @package     yana
 * @subpackage  db
 */
class ConnectionFactory extends \Yana\Core\Object implements \Yana\Db\IsConnectionFactory
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
     * $db = \Yana\Db\ConnectionFactory::createConnection('user');
     * </code>
     *
     * @param   string|\Yana\Db\Ddl\Database  $schema  name of the database schema file (see config/db/*.xml),
     *                                                 or instance of \Yana\Db\Ddl\Database
     * @return  \Yana\Db\IsConnection
     */
    public function createConnection($schema)
    {
        $schemaName = "";
        if (is_string($schema)) {
            $schema = $this->_getSchemaFactory()->createSchema($schema);
        }
        assert($schema instanceof \Yana\Db\Ddl\Database);

        $connection = null;
        $connections = $this->_getConnections();
        if (YANA_DATABASE_ACTIVE) {
            $connection = new \Yana\Db\Mdb2\Connection($schema);
        } else {
            $connection = new \Yana\Db\FileDb\Connection($schema);
        }
        if (!empty($schemaName)) {
            $connections[$schemaName] = $connection;
        }
        return $connection;
    }

}

?>