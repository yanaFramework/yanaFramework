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

namespace Yana\Forms\Fields;

/**
 * <<interface>> Transparent field wrapper base class.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
interface IsField
{

    /**
     * Get column definition.
     *
     * @return  \Yana\Db\Ddl\Column
     */
    public function getColumn(): \Yana\Db\Ddl\Column;

    /**
     * Get field definition.
     *
     * @return  \Yana\Db\Ddl\Field
     */
    public function getField(): \Yana\Db\Ddl\Field;

    /**
     * Get form context.
     *
     * @return  \Yana\Forms\Setups\IsContext
     */
    public function getContext(): \Yana\Forms\Setups\IsContext;

    /**
     * Get form structure.
     *
     * @return  \Yana\Forms\Fields\FieldCollectionWrapper
     */
    public function getForm(): \Yana\Forms\Fields\FieldCollectionWrapper;

    /**
     * Get title.
     *
     * The title is a label text that should be displayed in the UI when viewing this object.
     *
     * @return  string
     */
    public function getTitle(): string;

    /**
     * Returns filter term or NULL if there is none.
     *
     * The filter is set in the GUI by clicking on the filter symbol in the table header.
     *
     * @param   string  $filter  any single line string
     * @return  $this
     */
    public function setFilter(string $filter);

    /**
     * Returns filter term.
     *
     * If there is no filter, an empty string is returned.
     *
     * The filter is set in the GUI by clicking on the filter symbol in the table header.
     *
     * @return  string
     */
    public function getFilter(): string;

    /**
     * Check if a filter is set.
     *
     * Returns bool(true) if a filter has been set on the column and bool(false) otherwise.
     *
     * @return  bool
     */
    public function hasFilter(): bool;

    /**
     * Check if column has a scalar type.
     *
     * Returns bool(true) if the column exists and has a scalar type, which's values can be
     * displayed without line-breaks. Returns bool(false) otherwise.
     *
     * Note that this returns bool(false) for type "text" and bool(true) for type "enum".
     * While an enumeration may be a complex type, it stores scalar values. A text-column is not
     * scalar in the sense that it may contain tags and line-breaks, making it complex content.
     *
     * @return  bool
     */
    public function isFilterable(): bool;

    /**
     * Check if the field has a column element.
     *
     * If the field has a column as child element, it does not refer to a column in a real table.
     *
     * On the other hand, if there is no field definition and instead it is automatically derived
     * from the base table, then it does refer to (this) table.
     *
     * Therefore it must not be included in any queries on the database.
     *
     * @return  bool
     */
    public function refersToTable(): bool;

    /**
     * Is single-line.
     *
     * Returns bool(true) if the current field can be displayed using an input element,
     * which requires no more than a single line of text. Returns bool(false) otherwise.
     *
     * @return  bool
     */
    public function isSingleLine(): bool;

    /**
     * Is multi-line.
     *
     * Returns bool(true) if the current field can be displayed using an input element,
     * which requires multiple lines. Returns bool(false) otherwise.
     *
     * @return  bool
     */
    public function isMultiLine(): bool;

    /**
     * Get the column filter value.
     *
     * If the column values are to be filtered, this returns the currntly set search term
     * as a string. The string may contain wildcards.
     *
     * If there is no filter on this column, the function returns NULL instead.
     *
     * @return  string
     */
    public function getFilterValue(): string;

    /**
     * Get CSS class attribute.
     *
     * Returns the prefered CSS-class for this field as a string.
     * If there is none this function falls back to a generic name: gui_generator_col_[name],
     * where [name] is the name attribute of the column.
     *
     * @return  string
     */
    public function getCssClass(): string;

    /**
     * Get form value.
     *
     * @return  mixed
     */
    public function getValue();

    /**
     * Get minimal form value.
     *
     * Applies to search forms only.
     * If the field has a range of minimal and maximal value, this returns the minimal value of the field.
     * Otherwise it returns NULL.
     *
     * @return  scalar|NULL
     */
    public function getMinValue();

    /**
     * Get maximal form value.
     *
     * Applies to search forms only.
     * If the field has a range of minimal and maximal value, this returns the maximal value of the field.
     * Otherwise it returns NULL.
     *
     * @return  scalar|NULL
     */
    public function getMaxValue();

    /**
     * get current value as where clause
     *
     * This function returns an array of (leftOperand, operator, rightOperand),
     * which may be used to set a where clause on a database query object.
     *
     * If the value is empty, the function return NULL instead.
     *
     * @return  array|NULL
     */
    public function getValueAsWhereClause(): ?array;

}

?>