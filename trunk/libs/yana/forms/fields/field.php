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

namespace Yana\Forms\Fields;

/**
 * <<facade>> Transparent field wrapper base class.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
class Field extends \Yana\Forms\Fields\AbstractField
{

    /**
     * Caches the filter (having clause) on this field.
     *
     * @var  array
     */
    private $_filter = null;

    /**
     * Get form context.
     *
     * @return  \Yana\Forms\Setups\IsContext
     */
    public function getContext()
    {
        return $this->getForm()->getContext();
    }

    /**
     * Get title.
     *
     * The title is a label text that should be displayed in the UI when viewing this object.
     *
     * @return  string
     */
    public function getTitle()
    {
        $title = $this->getField()->getTitle();
        if (empty($title)) {
            $title = $this->getColumn()->getTitle();
        }
        if (empty($title)) {
            $title = $this->getField()->getName();
        }
        return $title;
    }

    /**
     * Check if a filter is set.
     *
     * Returns bool(true) if a filter has been set on the column and bool(false) otherwise.
     *
     * @return  bool
     */
    public function hasFilter()
    {
        return !is_null($this->_filter);
    }

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
    public function isFilterable()
    {
        return \Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable((string) $this->getColumn()->getType()) && (bool) $this->refersToTable();
    }

    /**
     * Check if the field has a column element.
     *
     * A field element is the child of a form element.
     * If the field element does not refer to a column element in a table, it must carry its own column element as a child,
     * since otherwise the field would have no type.
     *
     * If the field does not refer to a column in a real table, it must not be made available in search forms.
     *
     * @return  bool
     */
    public function refersToTable()
    {
        return is_null($this->getField()->getColumn());
    }

    /**
     * Is single-line.
     *
     * Returns bool(true) if the current field can be displayed using an input element,
     * which requires no more than a single line of text. Returns bool(false) otherwise.
     *
     * @return  bool
     */
    public function isSingleLine()
    {
        return \Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine((string) $this->getColumn()->getType());
    }

    /**
     * Is multi-line.
     *
     * Returns bool(true) if the current field can be displayed using an input element,
     * which requires multiple lines. Returns bool(false) otherwise.
     *
     * @return  bool
     */
    public function isMultiLine()
    {
        return \Yana\Db\Ddl\ColumnTypeEnumeration::isMultiLine((string) $this->getColumn()->getType());
    }

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
    public function getFilterValue()
    {
        return $this->getForm()->getSetup()->getFilter($this->getName());
    }

    /**
     * Get CSS class attribute.
     *
     * Returns the prefered CSS-class for this field as a string.
     * If there is none this function falls back to a generic name: gui_generator_col_[name],
     * where [name] is the name attribute of the column.
     *
     * @return  string
     */
    public function getCssClass()
    {
        $cssClass = $this->getField()->getCssClass();
        if (empty($cssClass)) {
            return "gui_generator_col_" . $this->getColumn()->getName();
        } else {
            return $cssClass;
        }
    }

    /**
     * Get form value.
     *
     * @return  mixed
     */
    public function getValue()
    {
        $name = \Yana\Util\Strings::toUpperCase($this->getName()); // returns either field or column name
        $context = $this->getContext();
        $collection = $context->getRows();
        $value = null;
        if ($collection->valid()) {
            $values = $collection->current();
            if (is_array($values) && isset($values[$name])) {
                $value = $values[$name];
            }
        } else {
            $value = $context->getValue($name);
        }
        return $value;
    }

    /**
     * Get minimal form value.
     *
     * Applies to search forms only.
     * If the field has a range of minimal and maximal value, this returns the minimal value of the field.
     * Otherwise it returns NULL.
     *
     * @return  scalar
     */
    public function getMinValue()
    {
        $value = $this->getValue();
        if (is_array($value) && isset($value['START'])) {
            return $value['START'];
        } else {
            return null;
        }
    }

    /**
     * Get maximal form value.
     *
     * Applies to search forms only.
     * If the field has a range of minimal and maximal value, this returns the maximal value of the field.
     * Otherwise it returns NULL.
     *
     * @return  scalar
     */
    public function getMaxValue()
    {
        $value = $this->getValue();
        if (is_array($value) && isset($value['END'])) {
            return $value['END'];
        } else {
            return null;
        }
    }

    /**
     * get current value as where clause
     *
     * This function returns an array of (leftOperand, operator, rightOperand),
     * which may be used to set a where clause on a database query object.
     *
     * If the value is empty, the function return NULL instead.
     *
     * @return  array
     */
    public function getValueAsWhereClause()
    {
        $whereClause = null;
        $tableName = $this->getForm()->getBaseForm()->getTable();

        if (is_string($tableName) && $tableName > '') {

            $helper = new \Yana\Forms\Fields\WhereClauseCreator($this->getColumn(), $tableName);
            $helper
                    ->setValue($this->getValue())
                    ->setMaxValue($this->getMaxValue())
                    ->setMinValue($this->getMinValue());
            $whereClause = $helper();
        }
        return $whereClause;
    }

}

?>