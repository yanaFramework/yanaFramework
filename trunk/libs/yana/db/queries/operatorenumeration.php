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
 * <<Enumeration>> Of operators to be used in where or having clauses.
 *
 * @package     yana
 * @subpackage  db
 * @codeCoverageIgnore
 */
class OperatorEnumeration extends \Yana\Core\AbstractEnumeration
{
    /**
     * AND operator to connect to statements
     */
    const AND = 'and';
    /**
     * OR operator to connect to statements
     */
    const OR = 'or';
    const EQUAL = '=';
    const NOT_EQUAL = '!=';
    const LIKE = 'like';
    const NOT_LIKE = 'not like';
    const GREATER = '>';
    const GREATER_OR_EQUAL = '>=';
    const LESS = '<';
    const LESS_OR_EQUAL = '<=';
    const IN = 'in';
    const NOT_IN = 'not in';
    const REGEX = 'regexp';
    const NOT_REGEX = 'not regexp';
    const EXISTS = 'exists';
    const NOT_EXISTS = 'not exists';

}

?>