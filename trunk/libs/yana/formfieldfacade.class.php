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
     * Field to operate on
     *
     * @access  protected
     * @var     DDLField
     * @ignore
     */
    protected $field = null;

    /**
     * Field to operate on
     *
     * @access  protected
     * @var     DDLFieldSetup
     * @ignore
     */
    protected $setup = null;

    /**
     * create new instance
     *
     * @access  public
     * @param   DDLField  $field  wrapped field instance
     */
    public function __construct(DDLField $field)
    {
        $this->field = $field;
        $this->setup = $setup;
    }

    /**
     * Transparent wrapping functions.
     *
     * @access  public
     * @param   string  $name  function name
     * @param   array   $args  function arguments
     * @return  mixed
     */
    public function __call($name, array $args)
    {
        return call_user_method_array($name, $this->object, $args);
    }

    /**
     * defition of underlying Column object
     *
     * @access  private
     * @var     DDLColumn
     */
    private $columnDefinition = null;

    /**
     * caches if the field can be used as a filter
     *
     * @access  private
     * @var     bool
     * @ignore
     */
    private $isFilterable = null;

    /**
     * caches the generated HTML for event elements
     *
     * @access  private
     * @var     bool
     * @ignore
     */
    private $eventsAsHTML = null;

    /**
     * caches the filter (having clause) on this field
     *
     * @access  private
     * @var     array
     * @ignore
     */
    private $filter = null;

    /**
     * caches the filter value (part of having clause) on this field
     *
     * @access  private
     * @var     string
     * @ignore
     */
    private $filterValue = null;

    /**
     * get column definition
     *
     * Each field definition must be linked to a column in the same database.
     * This function looks it up and returns this definition.
     *
     * @access  public
     * @return  DDLColumn
     * @throws  NotFoundException  when the database, form, table, or column was not found
     */
    public function getColumnDefinition()
    {
        if (isset($this->column)) {
            return $this->column; // has no external column definition
        } elseif (!isset($this->columnDefinition)) {
            /* @var $parent DDLAbstractForm */
            $parent = $this->getParent();
            if (!($parent instanceof DDLAbstractForm)) {
                $message = "Error in form-field '{$this->getName()}'. No parent form defined.";
                throw new NotFoundException($message);
            }
            $table = $parent->getTableDefinition();
            $column = $table->getColumn($this->getName());
            if (!($column instanceof DDLColumn)) {
                $message = "Error in form '{$parent->getName()}'. The form is using a column named " .
                    "'{$this->getName()}' which does not exist in the base table '{$table->getName()}'.";
                throw new NotFoundException($message);
            }
            $this->columnDefinition = $column;
        }
        return $this->columnDefinition;
    }

    /**
     * get title
     *
     * The title is a label text that should be displayed in the UI when viewing this object.
     *
     * It is optional. If it is not set, the function returns NULL instead.
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        if (empty($this->title)) {
            try {
                $this->title = $this->getColumnDefinition()->getTitle();
            } catch (\Exception $e) {
                $this->title = $this->getName(); // fall back to name if table does not exist
            }
        }
        return $this->title;
    }

    /**
     * get data type
     *
     * This returns the type of the underlying column.
     * It is a shortcut for: DDLDefaultField::getColumn()->getType().
     *
     * @access  public
     * @return  string
     * @throws  NotFoundException  when column definition was not found 
     */
    public function getType()
    {
        $column = $this->getColumnDefinition();
        return $column->getType();
    }

    /**
     * check whether column allows NULL values
     *
     * Returns bool(true) if the column allows undefined values (NULL).
     * Returns bool(false) otherwise.
     *
     * The default is bool(true).
     *
     * @access  public
     * @return  bool
     * @throws  NotFoundException  when column definition was not found
     */
    public function isNullable()
    {
        return $this->getColumnDefinition()->isNullable();
    }

    /**
     * check if a filter is set
     *
     * Returns bool(true) if a filter has been set on the column and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function hasFilter()
    {
        return !is_null($this->filter);
    }

    /**
     * check if column has a scalar type
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
        if (!isset($this->isFilterable)) {
            switch ($this->getType())
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
                    $this->isFilterable = (bool) $this->refersToTable();
                break;
                default:
                    $this->isFilterable = false;
                break;
            }
        }
        return !empty($this->isFilterable);
    }

    /**
     * get Query definition from form
     *
     * Looks up and returns the DbSelectQuery object from the underlying form and returns it.
     *
     * @access  private
     * @throws  NotFoundException  when form or query was not found
     * @return  DbSelect
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
     */
    public function setFilter($value, $isMandatory = true)
    {
        assert('is_scalar($value); // Wrong type for argument 1. String expected');
        assert('is_bool($isMandatory); // Wrong type for argument 2. Boolean expected');
        $this->dropFilter();
        $query = $this->_getQuery();
        $this->filterValue = $value;
        $value = strtr($value, '*?', '%_'); // translate wildcards
        $value = String::htmlSpecialChars($value);
        $this->filter = array($this->name, 'like', $value);
        $query->addHaving($this->filter, $isMandatory);
    }

    /**
     * get the currently set filter on a certain column
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
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * get the column filter value
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
        return $this->filterValue;
    }

    /**
     * remove filter
     *
     * Unsets the having clause for this column (if there is any).
     *
     * @access  public
     */
    public function dropFilter()
    {
        if (!is_null($this->filter)) {
            $query = $this->_getQuery();
            $having = $query->getHaving();
            $query->setHaving(self::_dropFilter($having, $this->name));
            $this->filter = null;
            $this->filterValue = null;
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
     * recursively check if field is selectable
     *
     * Returns bool(true) if field is selectable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isSelectable()
    {
        if (!isset($this->isSelectable)) {
            $this->isSelectable = $this->object->isSelectable() && $this->getParent()->isSelectable();
        }
        return $this->isSelectable;
    }

    /**
     * recursively check if field is insertable
     *
     * Returns bool(true) if field is insertable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isInsertable()
    {
        if (!isset($this->isInsertable)) {
            $this->isInsertable = parent::isInsertable() && $this->getParent()->isInsertable();
        }
        return $this->isInsertable;
    }

    /**
     * recursively check if field is updatable
     *
     * Returns bool(true) if field is updatable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isUpdatable()
    {
        if (!isset($this->isUpdatable)) {
            $this->isUpdatable = parent::isUpdatable() && $this->getParent()->isUpdatable() &&
                !$this->isReadonly() && $this->getColumnDefinition()->isUpdatable();
        }
        return $this->isUpdatable;
    }

    /**
     * recursively check if field is deletable
     *
     * Returns bool(true) if field is deletable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isDeletable()
    {
        if (!isset($this->isDeletable)) {
            $this->isDeletable = parent::isDeletable() && $this->getParent()->isDeletable();
        }
        return $this->isDeletable;
    }

    /**
     * recursively check if field is grantable
     *
     * Returns bool(true) if field is grantable to the current user and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isGrantable()
    {
        if (!isset($this->isGrantable)) {
            $this->isGrantable = parent::isGrantable() && $this->getParent()->isGrantable();
        }
        return $this->isGrantable;
    }

    /**
     * create a javascript events (where available)
     *
     * Returns the HTML-code for the generated attributes.
     *
     * Example:
     * <pre> onclick="alert('Hello World')" onchange="validate(this)"</pre>
     *
     * Note: the results are cached.
     *
     * @access  public
     * @return  string
     *
     * @ignore
     */
    public function getEventsAsHTML()
    {
        if (!isset($this->eventsAsHTML)) {
            $this->eventsAsHTML = "";
            /* @var $event DDLEvent */
            foreach ($this->getEvents() as $event)
            {
                if (strtolower($event->getLanguage()) !== 'javascript') {
                    continue; // non-javascript - ignore!
                }
                if ($event->getLabel() || $event->getIcon() ) {
                    continue; // these are links - ignore!
                }
                $name = $event->getName();
                $code = String::htmlSpecialChars($event->getAction());
                $this->eventsAsHTML .= " $name=\"$code\"";
            } // end foreach
        }
        return $this->eventsAsHTML;
    }

    /**
     * get CSS class attribute
     *
     * Returns the prefered CSS-class for this field as a string or NULL if there is none.
     * This function falls back to
     *
     * @access  public
     * @return  string
     */
    public function getCssClass()
    {
        $cssClass = $this->setup->getCssClass();
        if (empty($cssClass)) {
            return "gui_generator_col_" . $this->field->getName();
        } else {
            return $cssClass;
        }
    }

}

?>