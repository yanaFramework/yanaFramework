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
 * Form wrapper base class.
 *
 * @access      public
 * @abstract
 * @package     yana
 * @subpackage  database
 * @ignore
 */
abstract class DDLFormWrapper extends Collection
{

    /**
     * form
     *
     * @access  protected
     * @var     DDLAbstractForm
     * @ignore
     */
    protected $form = null;

    /**
     * form
     *
     * @access  protected
     * @var     DDLFormSetup
     * @ignore
     */
    protected $setup = null;

    /**
     * states if the given value is valid
     *
     * @access  private
     * @var     array
     * @ignore
     */
    private $_isValid = array();

    /**
     * list of foreign key references
     *
     * @access  protected
     * @var     array
     * @ignore
     */
    protected $references = null;

    /**
     * list of foreign key values
     *
     * @access  private
     * @var     array
     * @ignore
     */
    private $_referenceValues = null;

    /**
     * create new instance
     *
     * @access  public
     * @param   DDLForm       $form   iterate over this form
     * @param   DDLFormSetup  $setup  current form configuration and values
     */
    public function __construct(DDLForm $form, DDLFormSetup $setup)
    {
        $this->form = $form;
        $this->setup = $setup;
        $this->setItems($form->getFields());
    }

    /**
     * Insert or replace item.
     *
     * Example:
     * <code>
     * $collection[$offset] = $item;
     * $collection->offsetSet($offset, $item);
     * </code>
     *
     * @access  public
     * @param   scalar    $offset  name of field (filled automatically when not present)
     * @param   DDLField  $value   new value of item
     */
    public function offsetSet($offset, $value)
    {
        assert('$value instanceof DDLField;');
        if (!is_string($offset)) {
            $offset = $value->getName();
        }
        parent::offsetSet($offset, $value);
    }

    /**
     * Relay function call to wrapped object.
     *
     * @access  public
     * @param   string  $name       method name
     * @param   array   $arguments  list of arguments to pass to function
     * @return  mixed
     */
    public static function __call($name, $arguments)
    {
        return call_user_func_array(array($this->form, $name), $arguments);
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
     * @param   string  $key  field name
     * @return  bool
     * @throws  NotFoundException  when column definition was not found (unable to validate)
     */
    public function isValid($key)
    {
        if (empty($key)) {
            $key = $this->key();
        }
        if (!isset($this->_isValid[$key])) {
            $column = $this->current()->getColumnDefinition();
            try {

                $column->sanitizeValue($this->getValue());
                $this->_isValid[$key] = true;

            } catch (Exception $e) {
                // an error occured - Field is not valid
                $this->_isValid[$key] = false;
            }
        }
        assert('is_bool($this->_isValid[$key]);');
        return $this->_isValid[$key] === true;
    }

    /**
     * mark field as invalid
     *
     * This funciton manually sets the field content as invalid.
     * Note that this overwrites the basic function checks.
     *
     * @access  public
     * @param   string  $key  field name
     */
    public function setInvalid($key = null)
    {
        if (empty($key)) {
            $key = $this->key();
        }
        $this->_isValid[$key] = false;
    }

    /**
     * get form values
     *
     * @access  public
     * @return  array
     */
    public function getValues()
    {
        return $this->setup->getValue(strtolower($this->getClass()));
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
     * get list of foreign-key reference settings
     *
     * This returns an array of the following contents:
     * <code>
     * array(
     *   'primaryKey1' => array(
     *     'table' => 'name of target table'
     *     'column' => 'name of target column'
     *     'label' => 'name of a column in target table that should be used as a label'
     * }
     * </code>
     *
     * @access  protected
     * @return  array
     * @ignore
     */
    protected function getReferences()
    {
        if (!isset($this->references)) {
            $this->references = array();
            assert('!isset($field);');
            /* @var $field DDLDefaultField */
            foreach ($this->toArray() as $field)
            {
                if ($field->getType() !== 'reference') {
                    continue;
                }
                assert('!isset($column);');
                $column = $field->getColumnDefinition();
                $reference = $column->getReferenceSettings();
                if (!isset($reference['column'])) {
                    $reference['column'] = $column->getReferenceColumn()->getName();
                }
                if (!isset($reference['label'])) {
                    $reference['label'] = $reference['column'];
                }
                if (!isset($reference['table'])) {
                    $reference['table'] = $column->getReferenceColumn()->getParent()->getName();
                }
                $this->references[$field->getName()] = $reference;
                unset($column);
            } // end foreach
            unset($field);
        }
        return $this->references;
    }

    /**
     * get reference values
     *
     * This function returns an array, where the keys are the values of the primary keys in the
     *
     * @access  protected
     * @param   string  $fieldName  name of field to look up
     * @return  array
     * @ignore
     * @todo    move to builder class
     */
    protected function getReferenceValues($fieldName)
    {
        if (!isset($this->_referenceValues[$fieldName])) {
            $this->_referenceValues[$fieldName] = array();
            $references = $this->getReferences();
            if (isset($references[$fieldName])) {
                $reference = $references[$fieldName];
                $db = $this->form->getQuery()->getDatabase();
                $select = new DbSelect($db);
                $select->setTable($reference['table']);
                $columns = array('LABEL' => $reference['label'], 'VALUE' => $reference['column']);
                $select->setColumns($columns);
                $values = array();
                foreach ($select->getResults() as $row)
                {
                    $values[$row['VALUE']] = $row['LABEL'];
                }
                $this->_referenceValues[$fieldName] = $values;
            }
        }
        return $this->_referenceValues[$fieldName];
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