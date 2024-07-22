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

namespace Yana\Db\Ddl\Factories;

/**
 * <<factory>> Produces database objects.
 *
 * "Database" is the root level element of a XDDL document.
 * It may contain several child elements.
 * Those may be seperated to 5 basic groups: Tables, Views, Forms, Functions and
 * Change-logs.
 *
 * The database element defines basic properties of the database itself, as well
 * as information for the client and applications that may connect with the
 * database.
 *
 * @package     yana
 * @subpackage  db
 * @codeCoverageIgnore
 */
class DatabaseFactory extends \Yana\Core\StdObject implements \Yana\Db\Ddl\Factories\IsDatabaseFactory
{

    /**
     * Autoselect reverse engineering module and build suitable worker.
     *
     * @param   array  $dsn  leave empty to use default
     * @return  \Yana\Db\Ddl\Factories\IsWorker
     * @throws  \Yana\Db\Ddl\Factories\NotAvailableException  When no applicable DB layer is available to connect to the database.
     */
    public function buildWorker(?array $dsn = null)
    {
        if (\Yana\Db\Doctrine\ConnectionFactory::isDoctrineAvailable()) {
            $db = new \Yana\Db\Doctrine\ConnectionFactory($dsn);
            return $this->buildDoctrineWorker($db->getConnection());

        } elseif (\Yana\Db\Mdb2\ConnectionFactory::isMdb2Available()) {
            $errorReporting = error_reporting(E_ERROR | E_WARNING); // suppress MDB2 Notices
            $db = new \Yana\Db\Mdb2\ConnectionFactory($dsn);
            error_reporting($errorReporting);
            return $this->buildMdb2Worker($db->getConnection());
        }
        throw new \Yana\Db\Ddl\Factories\NotAvailableException("No applicable DB layer is available to connect to the database.");
    }

    /**
     * Build a database refactory worker based on Doctrine DBAL schema.
     *
     * @param   \Doctrine\DBAL\Connection  $connection  Doctrine DBAL database connection
     * @return  \Yana\Db\Ddl\Factories\IsWorker
     */
    public function buildDoctrineWorker(\Doctrine\DBAL\Connection $connection)
    {
        $mapper = new \Yana\Db\Ddl\Factories\DoctrineMapper();
        $wrapper = new \Yana\Db\Ddl\Factories\DoctrineWrapper($connection);
        return new \Yana\Db\Ddl\Factories\DoctrineWorker($mapper, $wrapper);
    }

    /**
     * Build a database refactory worker based on MDB2.
     *
     * @param   \MDB2_Driver_Common  $connection  MDB2 database connection
     * @return  \Yana\Db\Ddl\Factories\IsWorker
     */
    public function buildMdb2Worker(\MDB2_Driver_Common $connection)
    {
        $mapper = new \Yana\Db\Ddl\Factories\Mdb2Mapper();
        $wrapper = new \Yana\Db\Ddl\Factories\Mdb2Wrapper($connection);
        return new \Yana\Db\Ddl\Factories\Mdb2Worker($mapper, $wrapper);
    }

}

?>
