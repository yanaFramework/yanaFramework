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
 * default iterator class
 *
 * @access      public
 * @abstract
 * @package     yana
 * @subpackage  database
 * @ignore
 */
abstract class DDLAbstractFormWrapper extends Object implements Iterator, Countable
{
    /**
     * current position
     *
     * @access  private
     * @var     int
     */
    private $index = 0;

    /**
     * form
     *
     * @access  protected
     * @var     DDLAbstractForm
     * @ignore
     */
    protected $form = null;

    /**
     * list of fields
     *
     * @access  protected
     * @var     DDLDefaultField[]
     * @ignore
     */
    protected $fields = array();

    /**
     * states if the given value is valid
     *
     * @access  private
     * @var     array
     * @ignore
     */
    private $isValid = array();

    /**
     * current values taken from the form
     *
     * @access  protected
     * @var     array
     * @ignore
     */
    protected $values = null;

    /**
     * create new instance
     *
     * @access  public
     * @param   DDLAbstractForm  $form  iterate over this form
     */
    public function __construct(DDLAbstractForm $form)
    {
        $this->form = $form;
        $this->fields = $form->getFields();
    }

    /**
     * get current field
     *
     * Returns the current Field definition.
     * Throws an exception if the current index is not valid.
     *
     * @access  public
     * @return  DDLDefaultField
     * @throws  OutOfBoundsException  if the iterator is out of bounds
     */
    public function current()
    {
        if ($this->valid()) {
            assert('$this->fields[$this->index] instanceof DDLDefaultField;');
            return $this->fields[$this->index];
        } else {
            throw new OutOfBoundsException("Iterator index out of bounds");
        }
    }

    /**
     * magic function call
     *
     * Calls a function on the current field and returns the result.
     *
     * @access  public
     * @param   string  $name       name
     * @param   array   $arguments  arguments
     * @return  mixed
     */
    public function __call($name, array $arguments)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        return call_user_func_array(array($this->current(), $name), $arguments);
    }

    /**
     * has next field
     *
     * Returns bool(true) if the iterator has more fields.
     * Returns bool(false) if it has reached the last value.
     *
     * @access  public
     * @return  bool
     */
    public function hasNext()
    {
        return isset($this->fields[$this->index + 1]);
    }

    /**
     * next field
     *
     * Increment iterator to next field.
     * Note that this function does not check if the next iterator value is valid.
     * Use {@see DDLDefaultSearchIterator::valid()} or {@see DDLDefaultSearchIterator::hasNext()}
     * to check this.
     *
     * @access  public
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * get field key
     *
     * @access  public
     * @return  string
     */
    public function key()
    {
        if ($this->valid()) {
            $field = $this->current();
            return $field->getName();
        } else {
            return "";
        }
    }

    /**
     * check if field is valid
     *
     * @access  public
     * @return  bool
     */
    public function valid()
    {
        return isset($this->fields[$this->index]);
    }

    /**
     * rewind iterator
     *
     * @access  public
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * get field count
     *
     * This function returns the number of fields in the collection.
     * If there is no field available, it returns 0.
     *
     * @access  public
     * @return  int
     */
    public function count()
    {
        return count($this->fields);
    }

    /**
     * is single-line
     *
     * Returns bool(true) if the current field can be displayed using an input element,
     * which requires no more than a single line of text. Returns bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isSingleLine()
    {
        if (!$this->valid()) {
            return false;
        }
        // filter fields by column type
        switch ($this->current()->getType())
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
                return true;
            break;
            default:
                return false;
            break;
        } // end switch
    }

    /**
     * is multi-line
     *
     * Returns bool(true) if the current field can be displayed using an input element,
     * which requires multiple lines. Returns bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isMultiLine()
    {
        if (!$this->valid()) {
            return false;
        }
        // filter fields by column type
        switch ($this->current()->getType())
        {
            case 'text':
            case 'html':
            case 'image':
            case 'set':
            case 'list':
                return true;
            break;
            default:
                return false;
            break;
        } // end switch
    }

    /**
     * validate value
     *
     * This validates the current content of the field and returns bool(true) if it is valid and
     * bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     * @throws  NotFoundException  when column definition was not found (unable to validate)
     */
    public function isValid()
    {
        $key = $this->key();
        if (!isset($this->isValid[$key])) {
            $column = $this->current()->getColumnDefinition();
            try {

                $column->sanitizeValue($this->getValue());
                $this->isValid[$key] = true;

            } catch (Exception $e) {
                // an error occured - Field is not valid
                $this->isValid[$key] = false;
            }
        }
        assert('is_bool($this->isValid[$key]);');
        return $this->isValid[$key] === true;
    }

    /**
     * mark field as invalid
     *
     * This funciton manually sets the field content as invalid.
     * Note that this overwrites the basic function checks.
     *
     * @access  public
     */
    public function setInvalid()
    {
        $this->isValid[$this->key()] = false;
    }

    /**
     * get form values
     *
     * @access  public
     * @return  array
     */
    public function getValues()
    {
        if (!isset($this->values)) {
            $key = strtolower($this->getClass());
            $this->values = $this->form->getValue($key);
        }
        return $this->values;
    }

    /**
     * get form value
     *
     * @access  public
     * @return  mixed
     */
    public function getValue()
    {
        if ($this->valid()) {
            $name = $this->current()->getName();
            $values = $this->getValues();
            if (isset($values[$name])) {
                return $values[$name];
            }
        }
        return null;
    }

    /**
     * has next row
     *
     * Returns bool(true) if the iterator has more rows.
     * Returns bool(false) if it has no rows.
     *
     * @access  public
     * @return  bool
     */
    public function hasRows()
    {
        return false;
    }

    /**
     * returns the number of rows
     *
     * @access  public
     * @return  string
     */
    public function getRowCount()
    {
        return 1;
    }

    /**
     * get name attribute of form element
     *
     * @access  protected
     * @return  string
     */
    protected function getName()
    {
        return $this->form->getName() . "[" . $this->getClass() . "][" . $this->key() . "]";
    }

    /**
     * get id attribute of form element
     *
     * @access  public
     * @return  string
     */
    public function getId()
    {
        return $this->form->getName() . "-" . $this->getClass() . "-" . $this->key();
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
        if (!$this->valid()) {
            return "";
        }
        $field = $this->current();
        $cssClass = $field->getCssClass();
        if (empty($cssClass)) {
            return "gui_generator_col_" . $field->getName();
        } else {
            return $cssClass;
        }
    }

    /**
     * generate HTML select element
     *
     * @access  protected
     * @static
     * @param   string  $id        value of id attribute
     * @param   string  $name      value of name attribute
     * @param   string  $class     value of class attribute
     * @param   array   $items     item list
     * @param   string  $selected  selected value
     * @param   string  $null      text for NULL item (may be empty if there is none)
     * @param   string  $attr      additional attributes
     * @return  string
     */
    protected static function generateSelect($id, $name, $class, array $items, $selected, $null = "", $attr = "")
    {
        return '<select class="' . $class . '" id="' . $id . '" name="' . $name . '" ' . $attr . '>' .
            (($null) ? '<option value="">'. $null . '</option>' : '') .
            self::_getOptions($items, $selected) .
            '</select>';
    }

    /**
     * generate HTML option and optgroup elements
     *
     * @access  private
     * @static
     * @param   array   $items     item list
     * @param   string  $selected  selected value
     * @return  string
     */
    private static function _getOptions(array $items, $selected)
    {
        $result = "";
        foreach ($items as $key => $text)
        {
            if (is_array($text)) { // is optgroup
                $result .= '<optgroup label="' . $key . '">' .
                    self::_getOptions($text, $selected) . '</optgroup>';
            } else { // is option
                $result .= '<option value="' . $key . '" ' .
                    (($key == $selected) ? 'selected="selected"' : '') . '>' . $text . '</option>';
            }
        }
        return $result;
    }

    /**
     * generate HTML radio element
     *
     * @access  protected
     * @static
     * @param   string  $id        value of id attribute
     * @param   string  $name      value of name attribute
     * @param   string  $class     value of class attribute
     * @param   array   $items     item list
     * @param   string  $selected  selected value
     * @param   string  $null      text for NULL item (may be empty if there is none)
     * @param   string  $attr      additional attributes
     * @return  string
     */
    protected static function generateRadio($id, $name, $class, array $items, $selected, $null = "", $attr = "")
    {
        $result = '';
        if ($null) {
            $result = '<label class="' . $class . '"><input type="radio" ' . $attr . ' ' .
                'name="' . $name . '" value=""/>' . $null . '</label> ';
        }

        $id = ' id="' . $id . '"'; // only first element
        foreach ($items as $key => $text)
        {
            $result .= ' <label class="' . $class . '"><input' . $id . ' ' . $attr . ' type="radio" ' .
                'name="' . $name . '" value="' . $key . '" ' .
                (($key === $selected) ? 'checked="checked"' : '') . '/>' . $text . '</label>';
            $id = ""; // reset id for secound element
        }

        return $result;
    }

    /**
     * generate HTML checkbox element
     *
     * @access  protected
     * @static
     * @param   string  $id       value of id attribute
     * @param   string  $name     value of name attribute
     * @param   string  $class    value of class attribute
     * @param   array   $items    item list
     * @param   array   $checked  selected values
     * @param   string  $attr     additional attributes
     * @return  string
     */
    protected static function generateCheckboxes($id, $name, $class, array $items, array $checked, $attr = "")
    {
        $template = '<label class="' . $class . '"><input %s' . $attr .
            ' type="checkbox" ' . 'name="' . $name . '[]" value="%s"/>%s</label>' . "\n";
        $attributes = ' id="' . $id . '"'; // only first element
        return self::_getCheckBoxes($template, $attributes, $items, $checked);
    }

    /**
     * generate HTML checkbox and fieldset elements
     *
     * @access  protected
     * @static
     * @param   string  $template  checkbox template for sprintf
     * @param   string  &$attr     additional attributes (for first element only)
     * @param   array   $items     item list
     * @param   array   $checked   selected values
     * @return  string
     */
    private static function _getCheckBoxes($template, &$attr, array $items, array $checked)
    {
        $result = "";
        foreach ($items as $key => $text)
        {
            if (is_array($text)) { // is optgroup
                $result .= '<fieldset><legend>' . $key . '</legend>' .
                    self::_getCheckBoxes($template, $attr, $text, $checked) . '</fieldset>';
            } else { // is option
                if (in_array($key, $checked, true)) {
                    $attr .= ' checked="checked"';
                }
                $result .=  sprintf($template, $attr, $key, $text);
            }
            $attr = ""; // reset attribute list for second element
        }

        return $result;
    }
}

?>