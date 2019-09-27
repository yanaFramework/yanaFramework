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
 * @ignore
 */
declare(strict_types=1);

namespace Yana\Db\Helpers;

/**
 * <<interface>> This class is meant to be used to sanitize values before sending them to the database.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsValueConverter
{

    /**
     * Prepare a database entry for output.
     *
     * @param   mixed                $value   value of the row
     * @param   \Yana\Db\Ddl\Column  $column  base definition
     * @param   string               $key     array address (applies to columns of type array only)
     * @return  mixed
     */
    public function convertToInternalValue($value, \Yana\Db\Ddl\Column $column, string $key = "");

    /**
     * Validate a row against database schema.
     *
     * @param   \Yana\Db\Ddl\Table  $table  database object to use as base
     * @param   array               $row    values of the inserted/updated row
     * @return  array
     * @throws  \Yana\Core\Exceptions\Forms\FieldNotFoundException  when a value was provided but no corresponding column exists
     */
    public function convertRowToString(\Yana\Db\Ddl\Table $table, array $row): array;

    /**
     * Serialize value to string.
     *
     * @param   mixed   $value  value of the row
     * @param   string  $type   element of ColumnTypeEnumeration
     * @return  string
     */
    public function convertValueToString($value, string $type): string;

}

?>