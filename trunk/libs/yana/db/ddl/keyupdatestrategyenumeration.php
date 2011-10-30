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

namespace Yana\Db\Ddl;

/**
 * <<Enumeration>> Foreign key update strategy Enumeration
 *
 * Values for possible update strategies.
 * Either no-action, restrict, cascade, set-null, set-default.
 *
 * @access      public
 * @package     yana
 * @subpackage  db
 *
 * @ignore
 */
class KeyUpdateStrategyEnumeration extends \Yana\Core\AbstractEnumeration
{

    /**#@+
     * Values for on update/delete
     */
    const NOACTION = 0;
    const RESTRICT = 1;
    const CASCADE = 2;
    const SETNULL = 3;
    const SETDEFAULT = 4;
    /**#@-*/

}

?>