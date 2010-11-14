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
 * <<Enumeration>> Database query type enumeration.
 *
 * Values for possible database statements.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 *
 * @ignore
 */
class DbQueryTypeEnumeration
{

    /**
     * unknown or undefined statement type
     */
    const UNKNOWN = 0;
    /**
     * select statement
     */
    const SELECT = 8;
    /**
     * update statement
     */
    const UPDATE = 16;
    /**
     * insert statement
     */
    const INSERT = 32;
    /**
     * delete statement
     */
    const DELETE = 64;
    /**
     * checks if a database object exists
     */
    const EXISTS = 128;
    /**
     * checks number of occurences
     */
    const LENGTH = 256;
    /**
     * alias of LENGTH
     */
    const COUNT = self::LENGTH;

}

?>