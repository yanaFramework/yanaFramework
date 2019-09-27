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
 * <<interface>> For queries that have values.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsQueryWithValue extends \Yana\Db\Queries\IsQuery
{

    /**
     * set value(s) for current query
     *
     * This takes an associative array, where the keys are column names.
     * When updating a single column, it may also be a scalar value.
     *
     * @param   mixed  $values  value(s) for current query
     * @return  $this
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException          when a given argument is invalid
     * @throws  \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException  when the primary key is invalid or ambigious
     * @throws  \Yana\Db\Queries\Exceptions\ConstraintException         when a constraint violation is detected
     * @throws  \Yana\Db\Queries\Exceptions\InvalidResultTypeException  when trying to insert anything but a row.
     * @throws  \Yana\Core\Exceptions\NotWriteableException             when a target column or table is not writeable
     * @throws  \Yana\Core\Exceptions\NotFoundException                 when the column definition is invalid
     * @throws  \Yana\Core\Exceptions\NotImplementedException           when a column was encountered that has an unknown datatype
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException       when a given value is not valid
     * @throws  \Yana\Core\Exceptions\Forms\InvalidSyntaxException      when a value does not match a required pattern or syntax
     * @throws  \Yana\Core\Exceptions\Forms\MissingFieldException       when a not-nullable column is missing
     * @throws  \Yana\Core\Exceptions\Forms\FieldNotFoundException      when a value was provided but no corresponding column exists
     * @throws  \Yana\Core\Exceptions\Files\SizeException               when an uploaded file is too large
     */
    public function setValues($values);

    /**
     * Get the list of values.
     *
     * If none are available, NULL (not bool(false)!) is returned.
     *
     * @return  mixed
     */
    public function getValues();

}

?>