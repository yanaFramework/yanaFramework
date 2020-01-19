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
 * <<interface>> Aids in opening and keeping connections.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsConnectionFactory
{

    /**
     * <<factory>> Returns a ready-to-use database connection.
     *
     * Note: Since Yana 4 it is possible to configure more than 1 database servers as data source.
     * To do so, open the administration panel and add the settings under "other data sources".
     *
     * If you did that and wish to use the database server you set up, add the name that you
     * gave it as the second parameter. The connection will then be opened to that server and
     * using the same schema information.
     *
     * This is particularly useful during migration, to switch environments on the fly in your code,
     * or when you wish to distribute your databases across several servers for special purposes
     * like reporting or logging.
     *
     * @param   string|\Yana\Db\Ddl\Database  $schema                  name of the database schema file (see config/db/*.xml),
     *                                                                 or instance of \Yana\Db\Ddl\Database
     * @param   \Yana\Db\Sources\IsEntity     $optionalDatasourceName  if you wish another than the default data source, add the name here
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no such database was found
     * @throws  \Yana\Db\ConnectionException             when connection to database failed
     */
    public function createConnection($schema, ?\Yana\Db\Sources\IsEntity  $optionalDatasourceName = null): \Yana\Db\IsConnection;

}

?>