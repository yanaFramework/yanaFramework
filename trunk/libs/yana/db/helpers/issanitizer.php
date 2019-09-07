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
interface IsSanitizer
{

    /**
     * Validate a row against database schema.
     *
     * The argument $row is expected to be an associative array of values, representing
     * a row that should be inserted or updated in the table. The keys of the array $row are
     * expected to be the lowercased column names.
     *
     * @param   array   $row       values of the inserted/updated row
     * @param   bool    $isInsert  type of operation (true = insert, false = update)
     * @param   array   &$files    list of modified or inserted columns of type file or image
     * @return  array
     * @throws  \Yana\Core\Exceptions\NotWriteableException         when a target column or table is not writeable
     * @throws  \Yana\Core\Exceptions\NotFoundException             when the column definition is invalid
     * @throws  \Yana\Core\Exceptions\NotImplementedException       when a column was encountered that has an unknown datatype
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException   when a given value is not valid
     * @throws  \Yana\Core\Exceptions\Forms\InvalidSyntaxException  when a value does not match a required pattern or syntax
     * @throws  \Yana\Core\Exceptions\Forms\MissingFieldException   when a not-nullable column is missing
     * @throws  \Yana\Core\Exceptions\Forms\FieldNotFoundException  when a value was provided but no corresponding column exists
     * @throws  \Yana\Core\Exceptions\Files\SizeException           when an uploaded file is too large
     */
    public function sanitizeRowByTable(\Yana\Db\Ddl\Table $table, array $row, bool $isInsert = true, array &$files = array()): array;

    /**
     * Validate a value against a database column.
     *
     * Returns the sanitized value.
     *
     * @param   \Yana\Db\Ddl\Column $column  
     * @param   mixed               $value   value of the inserted/updated row
     * @param   array               &$files  list of modified or inserted columns of type file or image
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\NotFoundException            if the column definition is invalid
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  if an invalid value is encountered, that could not be sanitized
     * @throws  \Yana\Core\Exceptions\Forms\InvalidSyntaxException if a value does not match a required pattern or syntax
     * @throws  \Yana\Core\Exceptions\NotImplementedException      when the column has an unknown datatype
     * @throws  \Yana\Core\Exceptions\Files\SizeException          when uploaded file is too large
     */
    public function sanitizeValueByColumn(\Yana\Db\Ddl\Column $column, $value, array &$files = array());

}

?>