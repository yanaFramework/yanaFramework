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
 * <<facade>> Transparent field wrapper base class.
 *
 * @access      public
 * @package     yana
 * @subpackage  form
 * @ignore
 */
class FormFieldFacade extends Object
{

    /**
     * Form in which the field is defined.
     *
     * @access  private
     * @var     FormContextSensitiveWrapper
     */
    private $_form = null;

    /**
     * Structure definition of base column.
     *
     * @access  private
     * @var     DDLColumn
     */
    private $_column = null;

    /**
     * Field to operate on
     *
     * @access  private
     * @var     DDLField
     */
    private $_field = null;

    /**
     * Caches if the field can be used as a filter.
     *
     * @access  private
     * @var     bool
     * @ignore
     */
    private $_isFilterable = null;

    /**
     * Caches the filter (having clause) on this field.
     *
     * @access  private
     * @var     array
     * @ignore
     */
    private $_filter = null;

    /**
     * Caches the filter value (part of having clause) on this field.
     *
     * @access  private
     * @var     string
     * @ignore
     */
    private $_filterValue = null;

    /**
     * Create new instance.
     *
     * @access  public
     * @param   FormContextSensitiveWrapper  $parentForm  form structure of configuration
     * @param   DDLField                     $field       wrapped field instance
     * @param   DDLColumn                    $column      base column definition
     */
    public function __construct(FormContextSensitiveWrapper $parentForm, DDLColumn $column, DDLField $field = null)
    {
        $this->_form = $parentForm;
        $this->_column = $column;
        $this->_field = $field;
    }

    /**
     * Transparent wrapping functions.
     *
     * @access  public
     * @param   string  $name       function name
     * @param   array   $arguments  function arguments
     * @return  mixed
     */
    public function __call($name, array $arguments)
    {
        if (isset($this->_field) && method_exists($this->_field, $name)) {
            return call_user_func_array(array($this->_field, $name), $arguments);
        } elseif (method_exists($this->_column, $name)) {
            return call_user_func_array(array($this->_column, $name), $arguments);
        } else {
            return call_user_func_array(array($this->_form, $name), $arguments);
        }
    }

    /**
     * Get column definition.
     *
     * @access  public
     * @return  DDLColumn
     */
    public function getColumn()
    {
        return $this->_column;
    }

    /**
     * Get field definition.
     *
     * @access  public
     * @return  DDLField
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * Get form context.
     *
     * @access  public
     * @return  FormSetupContext
     */
    public function getContext()
    {
        return $this->_form->getContext();
    }

    /**
     * Get form structure.
     *
     * @access  public
     * @return  FormFacade
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Get title.
     *
     * The title is a label text that should be displayed in the UI when viewing this object.
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        $title = "";
        if ($this->getField()) {
            $title = $this->getField()->getTitle();
        }
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
     * @access  public
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
     * @access  public
     * @return  bool
     */
    public function isFilterable()
    {
        if (!isset($this->_isFilterable)) {
            switch ($this->getColumn()->getType())
            {
                case 'bool':
                case 'color':
                case 'enum':
                case 'float':
                case 'inet':
                case 'integer':
                case 'mail':
                case 'range':
                case 'string':
                case 'tel':
                case 'text':
                case 'html':
                case 'url':
                    $this->_isFilterable = (bool) !$this->getField() || $this->refersToTable();
                break;
                default:
                    $this->_isFilterable = false;
                break;
            }
        }
        return !empty($this->_isFilterable);
    }

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
     * @access  public
     * @return  bool
     */
    public function refersToTable()
    {
        return !($this->_field instanceof DDLField && $this->_field->getColumn() instanceof DDLColumn);
    }

    /**
     * Is single-line.
     *
     * Returns bool(true) if the current field can be displayed using an input element,
     * which requires no more than a single line of text. Returns bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isSingleLine()
    {
        // filter fields by column type
        switch ($this->getColumn()->getType())
        {
            case 'bool':
            case 'date':
            case 'enum':
            case 'file':
            case 'float':
            case 'inet':
            case 'integer':
            case 'mail':
            case 'string':
            case 'tel':
            case 'time':
            case 'timestamp':
            case 'url':
            case 'reference':
                return true;
            default:
                return false;
        } // end switch
    }

    /**
     * Is multi-line.
     *
     * Returns bool(true) if the current field can be displayed using an input element,
     * which requires multiple lines. Returns bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isMultiLine()
    {
        // filter fields by column type
        switch ($this->getColumn()->getType())
        {
            case 'text':
            case 'html':
            case 'image':
            case 'set':
            case 'list':
                return true;
            default:
                return false;
        } // end switch
    }

    /**
     * Get Query definition from form.
     *
     * Looks up and returns the DbSelectQuery object from the underlying form and returns it.
     *
     * @access  private
     * @throws  NotFoundException  when form or query was not found
     * @return  DbSelect
     * @deprecated
     */
    private function _getQuery()
    {
        if (!($this->parent instanceof DDLAbstractForm)) {
            throw new NotFoundException("No form found.");
        }
        return $this->parent->getQuery();
    }

    /**
     * set a filter
     *
     * Adds the following having-clause to the query: "HAVING {column} like '{value}'".
     * If another entry already exists for the column, it is replaced.
     *
     * Note: you may use wildcards (* or %, ? or _) in your query.
     * (Characters ? and * are automatically translated to _ and %)
     *
     * @access  public
     * @param   scalar  $value        column value to filter by
     * @param   bool    $isMandatory  switch between operators (true='AND', false='OR')
     * @throws  NotFoundException  when form or query was not found
     * @deprecated
     */
    private function _setFilter($value, $isMandatory = true)
    {
        assert('is_scalar($value); // Wrong type for argument 1. String expected');
        assert('is_bool($isMandatory); // Wrong type for argument 2. Boolean expected');
        $this->dropFilter();
        $query = $this->_getQuery();
        $this->_filterValue = $value;
        $value = strtr($value, '*?', '%_'); // translate wildcards
        $value = String::htmlSpecialChars($value);
        $this->_filter = array($this->name, 'like', $value);
        $query->addHaving($this->_filter, $isMandatory);
    }

    /**
     * Get the currently set filter on a certain column.
     *
     * Returns an array containing the having clause on this column, or
     * NULL if there is none.
     *
     * A having clause is an array of [leftOperand, operator, rightOperand].
     * Example:
     * <code>
     * array(
     *     'column',
     *     'like',
     *     'value'
     * )
     * </code>
     *
     * Note that the clause is unparsed. It may not be the identical to the
     * clause that's finally stored in the query.
     *
     * @access  public
     * @return  array
     * @deprecated
     */
    private function _getFilter()
    {
        return $this->_filter;
    }

    /**
     * Get the column filter value.
     *
     * If the column values are to be filtered, this returns the currntly set search term
     * as a string. The string may contain wildcards.
     *
     * If there is no filter on this column, the function returns NULL instead.
     *
     * @access  public
     * @return  string
     */
    public function getFilterValue()
    {
        return $this->_form->getSetup()->getFilter($this->getName());
    }

    /**
     * remove filter
     *
     * Unsets the having clause for this column (if there is any).
     *
     * @access  public
     * @deprecated
     */
    private function __dropFilter()
    {
        if (!is_null($this->_filter)) {
            $query = $this->_getQuery();
            $having = $query->getHaving();
            $query->setHaving(self::_dropFilter($having, $this->name));
            $this->_filter = null;
            $this->_filterValue = null;
        }
    }

    /**
     * drop a filter
     *
     * Removes all having clauses that contain the column and returns
     * the cleansed array as result.
     *
     * @access  public
     * @param   array   $having  having clause (haystack)
     * @param   string  $name    name of field (needle)
     * @return  array
     * @deprecated
     */
    private static function _dropFilter(array $having, $name)
    {
        if (empty($having)) {
            return array();
        }
        $leftOperand = $having[0];
        $operator = $having[1];
        $rightOperand = $having[2];
        switch ($operator)
        {
            case 'and':
            case 'or':
                $leftOperand = self::_dropFilter($leftOperand, $name);
                $rightOperand = self::_dropFilter($rightOperand, $name);
                if (empty($leftOperand) && empty($rightOperand)) {
                    return array();
                } elseif (empty($leftOperand)) {
                    return $rightOperand;
                } elseif (empty($rightOperand)) {
                    return $leftOperand;
                } else {
                    return array($leftOperand, $operator, $rightOperand);
                }
            break;
            case 'like':
                if (is_array($leftOperand) && $leftOperand[1] == $name) {
                    return array();
                } else {
                    return $having;
                }
            break;
            default:
                return $having;
            break;
        }
    }

    /**
     * Get CSS class attribute.
     *
     * Returns the prefered CSS-class for this field as a string.
     * If there is none this function falls back to a generic name: gui_generator_col_[name],
     * where [name] is the name attribute of the column.
     *
     * @access  public
     * @return  string
     */
    public function getCssClass()
    {
        $cssClass = "";
        if (isset($this->_field)) {
            $cssClass = $this->getField()->getCssClass();
        }
        if (empty($cssClass)) {
            return "gui_generator_col_" . $this->getColumn()->getName();
        } else {
            return $cssClass;
        }
    }

    /**
     * Get form value.
     *
     * @access  public
     * @return  mixed
     */
    public function getValue()
    {
        $name = strtoupper($this->getName()); // returns either field or column name
        $collection = $this->_form->getContext()->getRows();
        $value = null;
        if ($collection->valid()) {
            $values = $collection->current();
            if (is_array($values) && isset($values[$name])) {
                $value = $values[$name];
            }
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
     * @access  public
     * @return  scalar
     */
    public function getMinValue()
    {
        $value = $this->getValue();
        if (is_array($value) && isset($value['start'])) {
            return $value['start'];
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
     * @access  public
     * @return  scalar
     */
    public function getMaxValue()
    {
        $value = $this->getValue();
        if (is_array($value) && isset($value['end'])) {
            return $value['end'];
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
     * @access  public
     * @return  array
     */
    public function getValueAsWhereClause()
    {
        $value = $this->getValue();
        if (is_null($value) || $value === '') {
            return null;
        }
        $column = $this->current()->getColumn();
        if (!$column instanceof DDLColumn) {
            return null;
        }
        $leftOperand = array($this->_form->getBaseForm()->getTable(), $column->getName());
        /**
         * Switch by column's type
         */
        switch ($column->getType())
        {
            case 'bool':
                switch ($value)
                {
                    case 'true':
                        $rightOperand = true;
                    break;
                    case 'false':
                        $rightOperand = false;
                    break;
                    default:
                        return null;
                    break;
                }
                $operator = '=';
            break;
            case 'enum':
            case 'set':
                if (!is_array($value)) {
                    return null;
                }
                $operator = 'IN';
                $validItems = $column->getEnumerationItemNames();
                // prevent use of invalid items (possible injection)
                $rightOperand = array_intersect($value, $validItems);
                if (!empty($rightOperand)) {
                    return null;
                }
                assert('is_array($rightOperand);');
            break;
            case 'time':
            case 'timestamp':
            case 'date':
                if (!isset($value['active']) || $value['active'] !== 'true') {
                    return null;
                }
                $operator = 'AND';
                $min = $this->getMinValue();
                $max = $this->getMaxValue();
                $minTime = mktime(0, 0, 0, $min['month'], $min['day'], $min['year']);
                $maxTime = mktime(23, 59, 59, $max['month'], $max['day'], $max['year']);
                $rightOperand = array($leftOperand, '<=', $maxTime);
                $leftOperand = array($leftOperand, '>=', $minTime);
            break;
            case 'integer':
            case 'float':
            case 'range':
                $min = $this->getMinValue();
                $max = $this->getMaxValue();
                if ($min != '') {
                    $rightOperand = $min;
                    if ($min === $max) {
                        $operator = '=';
                    } else {
                        $operator = '>=';
                    }
                } elseif ($max != '') {
                    $rightOperand = $max;
                    $operator = '<=';
                } else {
                    return null;
                }
            break;
            default:
                $operator = 'LIKE';
                $value = strtr($value, '*?', '%_'); // translate wildcards
                $value = String::htmlSpecialChars($value);
                $rightOperand = $value;
            break;
        }
        return array($leftOperand, $operator, $rightOperand);
    }

    /**
     *
     * @access  public
     * @return  string
     */
    public function toString()
    {
        $builder = new FormFieldAutomatedHtmlBuilder();
        return $builder->buildByType($this);
    }

}

?>