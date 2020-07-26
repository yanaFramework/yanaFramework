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

namespace Yana\Db\Queries;

/**
 * Database query builder
 *
 * This class is a query builder that can be used to build SQL statements to update existing
 * rows or cells in a database-table.
 *
 * Note: this class does NOT untaint input data for you.
 *
 * @package     yana
 * @subpackage  db
 */
class Sql extends \Yana\Db\Queries\AbstractSql
{

    /**
     * Sends the query to the database server and returns a result-object.
     *
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException if the SQL statement is not valid
     */
    public function sendQuery()
    {
        return $this->getDatabase()->sendQueryString($this->_getSqlStatement());
    }

}

?>