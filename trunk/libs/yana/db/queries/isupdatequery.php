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
 * <<interface>> This class represents a join condition in the form of JoinedTable.TargetKey = SourceTable.ForeignKey.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsUpdateQuery extends
    \Yana\Db\Queries\IsQueryWithValue,
    \Yana\Db\Queries\IsQueryWithWhereClause,
    \Yana\Db\Queries\IsQueryWithOrderClause,
    \Yana\Db\Queries\IsQueryWithColumn
{

    /**
     * Returns the currently address as a string.
     *
     * If none has been selected yet, an empty string is returned.
     *
     * @return  string
     */
    public function getArrayAddress(): string;

    /**
     * Will retrieve and return the row that is to be deleted.
     *
     * Returns a list of affected rows.
     *
     * @return  array
     */
    public function getOldValues(): array;
}

?>