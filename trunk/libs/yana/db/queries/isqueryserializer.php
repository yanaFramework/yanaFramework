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
 * <<interface>> Serialize query object to SQL string.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsQuerySerializer
{

    /**
     * Convert Insert query to SQL string.
     *
     * Result INSERT INTO ... (...) VALUES (...)
     *
     * @param   \Yana\Db\Queries\IsInsertQuery  $query  source query
     * @return  string
     */
    public function fromInsertQuery(\Yana\Db\Queries\IsInsertQuery $query): string;

    /**
     * Convert Update query to SQL string.
     *
     * Result UPDATE ... SET ... WHERE ...
     * 
     * @param   \Yana\Db\Queries\IsUpdateQuery  $query  source query
     * @return  string
     */
    public function fromUpdateQuery(\Yana\Db\Queries\IsUpdateQuery $query): string;

    /**
     * Convert Delete query to SQL string.
     *
     * Result DELETE FROM ... WHERE ... ORDER BY ...
     * 
     * @param   \Yana\Db\Queries\IsDeleteQuery  $query  source query
     * @return  string
     */
    public function fromDeleteQuery(\Yana\Db\Queries\IsDeleteQuery $query): string;

    /**
     * Convert Select-1 query to SQL string.
     *
     * Result SELECT 1 FROM ... JOIN ... WHERE ...
     * 
     * @param   \Yana\Db\Queries\IsExistsQuery  $query  source query
     * @return  string
     */
    public function fromExistsQuery(\Yana\Db\Queries\IsExistsQuery $query): string;

    /**
     * Convert Select-Count query to SQL string.
     *
     * Result SELECT count(...) FROM ... JOIN ... WHERE ...
     * 
     * @param   \Yana\Db\Queries\IsCountQuery  $query  source query
     * @return  string
     */
    public function fromCountQuery(\Yana\Db\Queries\IsCountQuery $query): string;

    /**
     * Convert Select-Count query to SQL string.
     *
     * Result SELECT ... FROM ... JOIN ... WHERE ... HAVING ... ORDER BY ...
     * 
     * @param   \Yana\Db\Queries\IsSelectQuery  $query  source query
     * @return  string
     */
    public function fromSelectQuery(\Yana\Db\Queries\IsSelectQuery $query): string;

    /**
     * Return list of query parameters to bind to the SQL.
     *
     * If no parameters were recorderd, the result will be empty.
     *
     * If parameters exists, the resulting SQL query must have a "?" symbol for each parameter.
     * The order in which the "?" appear in the query match the order of the parameters returned by this function.
     *
     * @return  array
     */
    public function getQueryParameters(): array;
}

?>