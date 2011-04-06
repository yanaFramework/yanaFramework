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
 *
 * @ignore
 */

/**
 * <<builder>> HTML Form builder.
 *
 * This class is meant to create HTML fields for forms.
 *
 * @static
 * @access      public
 * @package     yana
 * @subpackage  form
 */
class FormFieldHtmlBuilder extends Object
{

    /**
     * HTML attribute "id".
     *
     * @access  private
     * @var     string
     */
    private $_id = "";

    /**
     * HTML attribute "name".
     *
     * @access  private
     * @var     string
     */
    private $_name = "";

    /**
     * HTML attribute "class".
     *
     * @access  private
     * @var     string
     */
    private $_class = "";

    /**
     * Other HTML attributes.
     *
     * @access  private
     * @var     string
     */
    private $_attr = "";

    /**
     * Initialize new instance.
     *
     * @access  public
     */
    public function _construct()
    {
        $this->createNewField();
    }

    /**
     * Reset instance and create new field.
     * 
     * @access  public
     * @return  FormHtmlBuilder 
     */
    public function createNewField()
    {
        $this->_attr = "";
        $this->_class = "";
        $this->_id = "";
        $this->_name = "";
        return $this;
    }

    /**
     * Get HTML attribute "id".
     *
     * @access  public
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set HTML attribute "id".
     *
     * @access  public
     * @param   string  $id  must be valid unique identifier
     * @return  FormHtmlBuilder 
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Get HTML attribute "name".
     *
     * @access  public
     * @return  string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set HTML attribute "name".
     *
     * @access  public
     * @param   string  $name  must be valid unique identifier
     * @return  FormHtmlBuilder 
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Get HTML attribute "class".
     *
     * @access  public
     * @return  string
     */
    public function getCssClass()
    {
        return $this->_class;
    }

    /**
     * Set HTML attribute "class".
     *
     * @access  public
     * @param   string  $class  must be valid CSS class name
     * @return  FormHtmlBuilder 
     */
    public function setCssClass($class)
    {
        $this->_class = $class;
        return $this;
    }

    /**
     * Get other HTML attributes.
     *
     * @access  public
     * @return  string
     */
    public function getAttr()
    {
        return $this->_attr;
    }

    /**
     * Set other HTML attributes as HTML code.
     *
     * @access  public
     * @param   string  $attr  list of HTML attributes.
     * @return  FormHtmlBuilder 
     */
    public function setAttr($attr)
    {
        $this->_attr = $attr;
        return $this;
    }

    /**
     * Generate HTML select element.
     *
     * @access  public
     * @param   array   $values    item list
     * @param   string  $selected  selected value
     * @param   string  $null      text for NULL item (may be empty if there is none)
     * @return  string
     */
    public function buildSelect(array $values, $selected, $null = "")
    {
        return '<select class="' . $this->getCssClass() . '" id="' . $this->getId() . '" name="' . $this->getName() .
             '" ' . $this->getAttr() . '>' . (($null) ? '<option value="">'. $null . '</option>' : '') .
            self::_getOptions($values, $selected) .
            '</select>';
    }

    /**
     * generate HTML option and optgroup elements
     *
     * @access  private
     * @static
     * @param   array   $values    item list
     * @param   string  $selected  selected value
     * @return  string
     */
    private static function _getOptions(array $values, $selected)
    {
        $result = "";
        foreach ($values as $key => $text)
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
     * @access  public
     * @param   array   $values    item list
     * @param   string  $selected  selected value
     * @param   string  $null      text for NULL item (may be empty if there is none)
     * @return  string
     */
    public function buildRadio(array $values, $selected, $null = "")
    {
        $this->_attr = $this->getAttr();
        $result = '';
        if ($null) {
            $result = '<label class="' . $this->_class . '"><input type="radio" ' . $this->_attr . ' ' .
                'name="' . $this->getName() . '" value=""/>' . $null . '</label> ';
        }

        $id = ' id="' . $this->getId() . '"'; // only first element
        foreach ($values as $key => $text)
        {
            $result .= ' <label class="' . $this->_class . '"><input' . $id . ' ' . $this->_attr . ' type="radio" ' .
                'name="' . $this->getName() . '" value="' . $key . '" ' .
                (($key === $selected) ? 'checked="checked"' : '') . '/>' . $text . '</label>';
            $id = ""; // reset id for secound element
        }

        return $result;
    }

    /**
     * generate HTML checkbox element
     *
     * @access  public
     * @param   array   $values    item list
     * @param   array   $checked  selected values
     * @return  string
     */
    public function buildCheckboxes(array $values, array $checked)
    {
        $template = '<label class="' . $this->getCssClass() . '"><input %s' . $this->getAttr() .
            ' type="checkbox" ' . 'name="' . $this->getName() . '[]" value="%s"/>%s</label>' . "\n";
        $attributes = ' id="' . $this->getId() . '"'; // only first element
        return self::_getCheckBoxes($template, $attributes, $values, $checked);
    }

    /**
     * Returns HTML-code for a checkbox.
     *
     * Creates two input HTML-tags: a hidden-field containing the value "0" = FALSE and a checkbox,
     * containting the real value.
     * The value
     *
     * Adding a hidden field with value = "0" prior to a checkbox will ensure the form always returns a value.
     * "1" = checked, "0" = not checked.
     *
     * @access  public
     * @param   bool    $isChecked  true = checkbox is checked, false = checkbox is not checked
     * @return  string
     */
    public function buildBoolCheckbox($isChecked)
    {
        $class = ($this->getCssClass()) ? $this->getCssClass() : "gui_generator_check";
        return '<input type="hidden" name="' . $this->getName() . '" value="0"/>' . // add a default value
            '<input' . $this->getAttr() . ' id="' . $this->getId() . '" class="' . $class . '" type="checkbox" ' .
            'name="' . $this->getName() . '" value="1" ' . (($isChecked) ? 'checked="checked"' : '') . '/>';
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

    /**
     * Create list of HTML input fields for arrays.
     *
     * @access  public
     * @param   mixed   $values     list of items  
     * @param   bool    $isNumeric  true = numeric list, false = associative array
     * @return  string 
     */
    public function buildList(array $values = array(), $isNumeric = false)
    {
        $lang = Language::getInstance();
        $template = '';

        if (!$isNumeric) {
            $template = '<input' . $this->getAttr() . ' size="5" type="text" name="' . $this->getName() . '[names][]" value="%s"/>' .
                '&nbsp;=&nbsp;<input size="10" type="text" name="' . $this->getName() . '[values][]" value="%s"/>' .
                '<a class="buttonize" href="javascript://yanaRemoveItem(this)" ' .
                'onclick="yanaRemoveItem(this)" title="'. $lang->getVar('remove') . '">' .
                '<span class="icon_delete">&nbsp;</span></a>' .
                '<a class="buttonize" href="javascript://yanaAddItem(this)" onclick="yanaAddItem(this)" ' .
                'title="' . $lang->getVar('button_new') . '">' .
                '<span class="icon_new">&nbsp;</span></a>';
        } else {
            $template = '<input' . $this->getAttr() . ' size="21" type="text" name="' . $this->getName() .'[%i]" value="%s"/>' .
                '<a class="buttonize" href="javascript://yanaRemoveItem(this)" ' .
                'onclick="yanaRemoveItem(this)" title="'. $lang->getVar('remove') . '">' .
                '<span class="icon_delete">&nbsp;</span></a>' .
                '<a class="buttonize" href="javascript://yanaAddItem(this)" onclick="yanaAddItem(this)" ' .
                'title="' . $lang->getVar('button_new') . '">' .
                '<span class="icon_new">&nbsp;</span></a>';
        }

        $result = '<div class="' . (($this->getCssClass()) ? $this->getCssClass() : "gui_generator_array")  . '">';

        /* list of entries*/
        $result .= '<ol>';

        if (!empty($values)) {
            $template = '<li>' . $template . '</li>';
            ksort($values);
            foreach ($values as $key => $text)
            {
                $result .= sprintf($template, $key, $text);
            }
        } else {
            $result .= '<li>' . sprintf($template, '', '') . '</li>';
        }

        // link to add new entry
        $result .= '</ol></div>';

        return $result;
    }

    /**
     * Create HTML input field of type text.
     *
     * @access  public
     * @param   string  $value  some text, must not contain line-breaks.
     * @return  string
     */
    public function buildTextfield($value)
    {
        return '<input' . $this->getAttr() . ' id="' . $this->getId() . '" name="' . $this->getName() . '" ' .
            'class="' . $this->getCssClass() . '" type="text" value="' . $value . '"/>';
    }

    /**
     * Create HTML input field of type color.
     *
     * @access  public
     * @param   string  $value  some text, must not contain line-breaks.
     * @return  string
     */
    public function buildColorpicker($value)
    {
        return $this->buildTextfield($value) . SmartUtility::colorpicker(array('id' => $this->getId()));
    }

    /**
     * create HTML for current field
     *
     * Returns the HTML-code representing an input element for the current field.
     *
     * @access  public
     * @static
     * @return  string
     *
     * @ignore
     */
    public function buildByType(DDLField $field)
    {
        $column = $field->getColumnDefinition();
        $length = $column->getLength();

        $name = $this->getName();
        $id = $this->getId();
        $lang = Language::getInstance();

        // retrieve search arguments
        $value = $this->getValue();
        if (is_null($value)) {
            $value = $column->getAutoValue();
        }
        if (is_string($value)) {
            $value = String::htmlSpecialChars($value);
        }

        // get javascript events
        assert('!isset($attr); // Cannot redeclare var $attr');
        $attr = $field->getEventsAsHTML();

        /**
         * Switch by column's type
         */
        switch ($field->getType())
        {
            case 'array':
                return $this->buildList($value, false);
            case 'list':
                return $this->buildList($value, true);
            case 'bool':
                return $this->buildBoolCheckbox($value);
            case 'color':
                return $this->buildColorpicker($value);
            case 'enum':
                $items = $column->getEnumerationItems();
                $null = "";
                if ($column->isNullable()) {
                    $null = $lang->getVar('choose_option');
                }
                if (!$this->getCssClass()) {
                    $this->setCssClass("gui_generator_set");
                }
                return $this->buildSelect($items, $value, $null);
            break;
            case 'file':
                global $YANA;
                $result = '<div class="gui_generator_file_download">';
                $download = $this->form->getDownloadAction();
                if (!empty($value) && $YANA->getSession()->checkPermission(null, $download)) {
                    $value = DbBlob::storeFilenameInSession($value);
                    $result .= '<a class="buttonize" title="' . $lang->getVar('title_download') . '" href=' .
                        SmartUtility::href("action={$download}&target={$value}") .
                        '><span class="icon_download">&nbsp;</span></a>';

                } else {
                    $result .= '<span class="icon_blank">&nbsp;</span>';
                }
            // fall through

            /*
             * an image is a file which has a preview
             */
            case 'image':
                global $YANA;
                if (!isset($result)) {
                    $result = '<div class="gui_generator_image">';
                    $download = $this->form->getDownloadAction();
                    if (!empty($value) && $YANA->getSession()->checkPermission(null, $download)) {
                        $value = DbBlob::storeFilenameInSession($value);
                        $result .= '<a href=' .
                            SmartUtility::href("action={$download}&target={$value}&fullsize=true") .
                            '><img border="0" alt="" src=' .
                            SmartUtility::href("action={$download}&target={$value}") . '/></a>';

                    } else {
                        $result .= '&nbsp;';
                    }
                }
                if ($length > 0) {
                    $result .= '<input type="hidden" name="MAX_FILE_SIZE" value="' . $length . '"/>';
                }
                $result .= '<input' . $attr .' size="1" type="file" id="' . $id . '" name="' . $name . '"/>';
                if (!empty($value) && $column->isNullable()) {
                    $result .= '<label class="gui_generator_file_delete">' .
                        '<input title="' . $lang->getVar('button_delete_one') . '" type="checkbox" ' .
                        'id="' . $id . '_delete" name="' . $name . '" value="1"/>' .
                        $lang->getVar('button_delete_one') . '</label>';
                }
                $result .= '</div>';
                return $result;
            break;
            case 'float':
                $precision = (int) $column->getPrecision();
                $title = $field->getTitle() . ': ' . (($length < 8) ? str_pad('', $length, '#') : '########') .
                     '.' . (($precision < 8) ? str_pad('', $precision, '#') : '########');
                $length++;
                return '<input' . $attr .' id="' . $id . '" name="' . $name . '" type="text" value="' . $value .
                    '" ' . ( ($length > 0 ) ? 'maxlength="' . $length . '"' : '' ) .
                    ( ($length < 22 ) ? ' size="' . $length . '"' : '' ) . ' title="' . $title . '"' . '/>';
            break;
            case 'html':
                return '<textarea' . $attr .' class="editable" id="' . $id . '" name="' . $name . '" ' .
                    ' rows="3" cols="20">' . $value . '</textarea>' .
                    '<script type="text/javascript" src="skins/default/scripts/tiny_mce/tiny_mce.js"></script>';
            break;
            case 'password':
                return '<input' . $attr .' id="' . $id . '" name="' . $name . '" type="password" value=""/>';
            break;
            case 'range':
                $rangeStep = $column->getRangeStep();
                if (empty($rangeStep)) {
                    $rangeStep = 1;
                }
                if (empty($value)) {
                    $value = $column->getRangeMin();
                }
                return '<input' . $attr . ' size="4" id="' . $id . '" name="' . $name . '" type="text" value="' .
                    $value . '"/>' .
                    '<script type="text/javascript">yanaSlider("' . $id . '", ' . $column->getRangeMin() .
                     ', ' . $column->getRangeMax() . ', ' . $rangeStep . ', ' . $value . ');</script>';
            break;
            case 'reference':
                $null = "";
                if ($column->isNullable()) {
                    $null = $lang->getVar('choose_option');
                }
                $items = $this->getReferenceValues($field->getName());
                return self::generateSelect($id, $name, "gui_generator_reference", $items, $value, $null, $attr);
            break;
            case 'set':
                assert('!isset($items); // Cannot redeclare var $items');
                $items = $column->getEnumerationItems();
                if (empty($value)) {
                    $value = array();
                }
                return self::generateCheckboxes($id, $name, "gui_generator_set", $items, $value, $attr);
            break;
            case 'text':
                $check = "";
                if ($length > 0) {
                    $check = 'onkeypress="if (yanaMaxLength) yanaMaxLength(this, ' . $length . ', event)"';
                }
                if ($length > 2000) {
                    $check .= ' cols="30"';
                } else {
                    $check .= ' cols="20"';
                }
                return '<textarea' . $attr . ' id="' . $id . '" name="' . $name . '" ' . $check .
                    ' rows="3">' . $value . '</textarea>';
            break;
            case 'date':
                if (is_string($value)) {
                    $value = strtotime($value);
                }
                if (is_int($value)) {
                    $value = getdate($value);
                }
                return '<span id="' . $id . '" class="gui_generator_date">' .
                    SmartUtility::selectDate(array(
                        'time' => $value,
                        'attr' => $attr,
                        'id' => $id,
                        'name' => $name)
                    ) . '</span>';
            break;
            case 'time':
            case 'timestamp':
                if (is_string($value)) {
                    $value = strtotime($value);
                }
                if (is_int($value)) {
                    $value = getdate($value);
                }
                return '<span id="' . $id . '" class="gui_generator_time">' .
                    SmartUtility::selectDate(array(
                        'time' => $value,
                        'attr' => $attr,
                        'id' => $id,
                        'name' => $name)
                    ) .
                    SmartUtility::selectTime(array(
                        'time' => $value,
                        'attr' => $attr,
                        'id' => $id,
                        'name' => $name)
                    ) . '</span>';
            break;
            case 'url':
                return '<input' . $attr . ' id="' . $id . '" name="' . $name . '" type="text" value="' . $value .
                    '" ' .( ($length > 0 ) ? 'maxlength="' . $length . '"' : '' ) .
                    ( ($length > 0 && $length < 22 ) ? 'size="' . $length . '"' : '' ) . '/>';
            break;
            default:
                return '<input' . $attr . ' id="' . $id . '" name="' . $name . '" type="text" value="' . $value .
                    '" ' .( ($length > 0 ) ? 'maxlength="' . $length . '"' : '' ) .
                    ( ($length > 0 && $length < 22 ) ? ' size="' . $length . '"' : '' ) . '/>';
            break;
        }
    }

}

?>