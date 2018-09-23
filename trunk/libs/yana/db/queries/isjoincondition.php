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

namespace Yana\Db\Queries;

/**
 * <<interface>> This class represents a join condition in the form of JoinedTable.TargetKey = SourceTable.ForeignKey.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsJoinCondition
{

    /**
     * Returns the name of the joined table.
     *
     * @return  string
     */
    public function getJoinedTableName();

    /**
     * Returns the name of the column in the joined table.
     *
     * @return  string
     */
    public function getTargetKey();

    /**
     * Returns the name of the source table.
     *
     * @return  string
     */
    public function getSourceTableName();

    /**
     * Returns the name of the column in the source table.
     *
     * @return  string
     */
    public function getForeignKey();

    /**
     * Returns bool(true) if this is an INNER join.
     *
     * @return  bool
     */
    public function isInnerJoin();

    /**
     * Returns bool(true) if this is an LEFT join.
     *
     * @return  bool
     */
    public function isLeftJoin();

    /**
     * Returns bool(true) if this is a NATURAL join.
     *
     * @return  bool
     */
    public function isNaturalJoin();

}

?>