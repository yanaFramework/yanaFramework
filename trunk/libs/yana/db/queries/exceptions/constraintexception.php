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

namespace Yana\Db\Queries\Exceptions;

/**
 * <<exception>> When a query detects that a statement does not comply with an existing constraint.
 *
 * Note: some constraints may be checked by the query-builder, yet: not all.
 * So the query does a pre-check and may find constraint violation based on static structure.
 * The database may find constraint violations based on dynamic data, that the query-builder may not detect.
 * Check for both!
 *
 * @package     yana
 * @subpackage  db
 */
class ConstraintException extends \Yana\Db\Queries\Exceptions\QueryException
{

    // intentionally left blank

}

?>