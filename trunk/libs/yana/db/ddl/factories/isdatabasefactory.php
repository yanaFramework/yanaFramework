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
 * <<interface>> Database structure.
 *
 * This wrapper class represents the structure of a database.
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
interface IsDatabaseFactory
{

    /**
     * Create database from tableInfo.
     *
     * Try to extract some information on the structure of a database from the
     * information provided by PEAR-MDB2's Reverse module.
     *
     * @param   \Yana\Db\Ddl\Factories\IsMdb2Wrapper  $connection  MDB2 database connection
     * @return  \Yana\Db\Ddl\Database
     * @throws  \Yana\Db\ConnectionException  when unable to open connection to database
     */
    public function createDatabase(\Yana\Db\Ddl\Factories\IsMdb2Wrapper $connection);

}

?>