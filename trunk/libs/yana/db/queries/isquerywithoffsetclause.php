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
 * <<interface>> Queries that have a where clause.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsQueryWithOffsetClause extends \Yana\Db\Queries\IsQueryWithLimitClause
{

    /**
     * Set an offset for this query.
     *
     * Note: This setting will not be part of the sql statement
     * produced by __toString(). Use the API's $limit and
     * $offset parameter instead when sending the query.
     *
     * This restriction does not apply if you use sendQuery().
     *
     * @param   int  $offset  offset for this query
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when offset is not positive
     * @return  $this
     */
    public function setOffset(int $offset);

}

?>