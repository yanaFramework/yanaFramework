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

/**
 * <<Enumeration>> Database query result type enumeration.
 *
 * Values for possible database results.
 * Including: whole table, rows, columns and cells.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 *
 * @ignore
 */
class DbResultEnumeration extends \Yana\Core\AbstractEnumeration
{

    /**
     * Unknown or undefined result type.
     */
    const UNKNOWN = 0;
    /**
     * Result is two-dimensional array, representing a table.
     */
    const TABLE = 1;
    /**
     * Result is an array, representing a row.
     */
    const ROW = 2;
    /**
     * Result is representing a single cell.
     */
    const CELL = 3;
    /**
     * Result is an array, representing a column.
     */
    const COLUMN = 4;

}

?>