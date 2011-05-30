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
     * HTML attribute "title".
     *
     * @access  private
     * @var     string
     */
    private $_title = "";

    /**
     * HTML attribute "class".
     *
     * @access  private
     * @var     string
     */
    private $_class = "";

    /**
     * HTML attribute "maxlength".
     *
     * @access  private
     * @var     int
     */
    private $_maxLength = 0;

    /**
     * Other HTML attributes.
     *
     * @access  private
     * @var     string
     */
    private $_attr = "";

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
     * @return  FormFieldHtmlBuilder 
     */
    public function setId($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');
        $this->_id = String::htmlSpecialChars($id, ENT_QUOTES);
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
     * @return  FormFieldHtmlBuilder 
     */
    public function setName($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $this->_name = String::htmlSpecialChars($name, ENT_QUOTES);
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
     * @return  FormFieldHtmlBuilder 
     */
    public function setCssClass($class)
    {
        assert('is_string($class); // Invalid argument $class: string expected');
        $this->_class = String::htmlSpecialChars($class, ENT_QUOTES);
        return $this;
    }

    /**
     * Get HTML attribute "title".
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set HTML attribute "id".
     *
     * @access  public
     * @param   string  $title  any text without HTML code
     * @return  FormFieldHtmlBuilder 
     */
    public function setTitle($title)
    {
        assert('is_string($title); // Invalid argument $id: string expected');
        $this->_title = String::htmlSpecialChars($title, ENT_QUOTES);
        return $this;
    }

    /**
     * Get HTML attribute "maxlength".
     *
     * If the var has no maximum length at all, the function will return a number < 1.
     *
     * @access  public
     * @return  int
     */
    public function getMaxLength()
    {
        return $this->_maxLength;
    }

    /**
     * Set HTML attribute "maxlength".
     *
     * To reset the value, set it to 0.
     *
     * @access  public
     * @param   int  $maxLength  must be a positive number
     * @return  FormFieldHtmlBuilder 
     */
    public function setMaxLength($maxLength)
    {
        assert('is_int($maxLength); // Invalid argument $maxLength: int expected');
        assert('$maxLength >= 0; // Invalid argument $maxLength: must be >= 0');
        $this->_maxLength = (int) $maxLength;
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
     * @return  FormFieldHtmlBuilder 
     */
    public function setAttr($attr)
    {
        assert('is_string($attr); // Invalid argument $attr: string expected');
        $this->_attr = String::htmlSpecialChars($attr, ENT_NOQUOTES);
        return $this;
    }

    /**
     * Generate HTML select element.
     *
     * If the item list is a multi-dimensional array, "optgroup" elements will be created to
     * group the items. Note that you should not use nested optgroups.
     *
     * @access  public
     * @param   array   $values         item list
     * @param   string  $selectedValue  selected value
     * @param   string  $null           text for NULL item (may be empty if there is none)
     * @return  string
     */
    public function buildSelect(array $values, $selectedValue, $null = "")
    {
        return $this->_getSelect($values, (array) $selectedValue, false, $null);
    }

    /**
     * Generate HTML select element.
     *
     * If the item list is a multi-dimensional array, "optgroup" elements will be created to
     * group the items. Note that you should not use nested optgroups.
     *
     * @access  public
     * @param   array   $values         item list
     * @param   array   $selectedValue  selected values
     * @return  string
     */
    public function buildSelectMultiple(array $values, array $selectedValue)
    {
        return $this->_getSelect($values, $selectedValue, true);
    }

    /**
     * Generate HTML select element.
     *
     * @access  public
     * @param   array   $values          item list
     * @param   array   $selectedValues  one or more selected values
     * @param   bool    $multiple        allow to select multiple values
     * @param   string  $null            text for NULL item (may be empty if there is none)
     * @return  string
     */
    private function _getSelect(array $values, array $selectedValues, $multiple, $null = "")
    {
        return '<select class="' . $this->getCssClass() . '" id="' . $this->getId() . '" name="' . $this->getName() .
            (($multiple) ? '[]" multiple="multiple"' : '" ') .
            $this->getAttr() . '>' . (($null) ? '<option value="">'. $null . '</option>' : '') .
            self::_getOptions($values, (array) $selectedValues) .
            '</select>';
    }

    /**
     * Create HTML option and optgroup elements.
     *
     * @access  private
     * @static
     * @param   array  $values          item list
     * @param   array  $selectedValues  selected values
     * @return  string
     */
    private static function _getOptions(array $values, array $selectedValues)
    {
        $result = "";
        foreach ($values as $key => $text)
        {
            if (is_array($text)) { // is optgroup
                $result .= '<optgroup label="' . $key . '">' .
                    self::_getOptions($text, $selectedValues) . '</optgroup>';
            } else { // is option
                $result .= '<option value="' . $key . '" ' .
                    ((in_array($key, $selectedValues)) ? 'selected="selected"' : '') . '>' . $text . '</option>';
            }
        }
        return $result;
    }

    /**
     * Create HTML radio element.
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
     * Create HTML checkbox element.
     *
     * @access  public
     * @param   array   $values    item list
     * @param   array   $checked  selected values
     * @return  string
     */
    public function buildCheckboxes(array $values, array $checked)
    {
        $class = ($this->getCssClass()) ? $this->getCssClass() : "gui_generator_check";
        $template = '<label class="' . $class . '" title="' . $this->getTitle() . '"><input %s' . $this->getAttr() .
            ' type="checkbox" ' . 'name="' . $this->getName() . '[]"  ' .
            ' class="' . $class . '" value="%s"/>%s</label>' . "\n";
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
            'name="' . $this->getName() . '" value="1" ' . (($isChecked) ? 'checked="checked" ' : ' ') .
            'title="' . $this->getTitle() . '"/>';
    }

    /**
     * Create HTML checkbox and fieldset elements.
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
     * @param   string  $value      some text, must not contain line-breaks.
     * @param   string  $text       valid HTML type attribute.
     * @return  string
     */
    public function buildTextfield($value, $type = 'text')
    {
        assert('is_string($type); // Invalid argument $text: string expected');
        assert('preg_match("/^[a-z]+$/", $type); // Invalid argument $text: must only contain characters a-z');
        $maxLength = (int) $this->getMaxLength();
        return '<input' . $this->getAttr() .' id="' . $this->getId() . '" name="' . $this->getName() . '" ' .
            'class="' . $this->getCssClass() . '" type="' . $type . '" value="' . $value .
            '" ' . ( ($maxLength > 0 ) ? 'maxlength="' . $maxLength . '"' : '' ) .
            ( ($maxLength < 22 ) ? ' size="' . $maxLength . '"' : '' ) . ' title="' . $this->getTitle() . '"' . '/>';
    }

    /**
     * Create HTML input field of type file.
     *
     * This also adds a checkbox to delete existing files on demand.
     *
     * @access  public
     * @param   bool  $hasDelete  true = add "delete" button for existing file, false = no "delete" button
     * @return  string 
     */
    public function buildFilefield($hasDelete, $mimeType = '')
    {
        assert('is_bool($hasDelete); // Invalid argument $hasDelete: bool expected');
        assert('is_string($mimeType); // Invalid argument $mimeType: string expected');
        $attr = $this->getAttr();
        if ($mimeType) {
            $attr .= ' accept="' . string::htmlSpecialChars($mimeType) . '"';
        }
        if ($this->getMaxLength()) {
            $attr .= ' maxlength="' . (int) $this->getMaxLength() . '"';
        }
        $result = '<input' . $attr . ' size="1" type="file" id="' . $this->getId() . '" name="' .
            $this->getName() . '"/>';

        if ($hasDelete) {
            $lang = Language::getInstance();
            $result .= '<label class="gui_generator_file_delete">' .
                '<input title="' . $lang->getVar('button_delete_one') . '" type="checkbox" ' .
                'id="' . $this->getId() . '_delete" name="' . $this->getName() . '" value="1"/>' .
                $lang->getVar('button_delete_one') . '</label>';
            unset($lang);
        }

        return $result;
    }

    /**
     * Create HTML textarea field.
     *
     * @access  public
     * @param   string  $value  some text, must not contain line-breaks.
     * @return  string
     */
    public function buildTextarea($value)
    {
        $check = ' cols="20"';
        if ($this->getMaxLength() > 2000) {
            $check .= ' cols="30"';
        }
        return '<textarea' . $this->getAttr() . ' id="' . $this->getId() . '" name="' . $this->getName() .
            '" class="' . $this->getCssClass() . '" ' . '" title="' . $this->getTitle() . '" ' . $check . ' rows="3">' .
            $value . '</textarea>';
    }

    /**
     * Create download link for file.
     *
     * @access  public
     * @param   string  $filename        target file
     * @param   string  $downloadAction  name of function called to download the file
     * @return  string 
     */
    public function buildFileDownload($filename, $downloadAction)
    {
        if (empty($filename) || empty($downloadAction)) {
            return '<span class="icon_blank">&nbsp;</span>';
        } else {
            assert('is_string($filename); // Invalid argument $filename: string expected');
            assert('is_string($downloadAction); // Invalid argument $downloadAction: string expected');
            $lang = Language::getInstance();
            $fileId = DbBlob::storeFilenameInSession($filename);
            return '<a class="buttonize" title="' . $lang->getVar('title_download') . '" href=' .
                SmartUtility::href("action={$downloadAction}&target={$fileId}") .
                '><span class="icon_download">&nbsp;</span></a>';
        }
    }

    /**
     * Create download link and preview for image file.
     *
     * @access  public
     * @param   string  $filename        target file
     * @param   string  $downloadAction  name of function called to download the file
     * @return  string 
     */
    public function buildImageDownload($filename, $downloadAction)
    {
        if (empty($filename) || empty($downloadAction)) {
            return '<span class="icon_blank">&nbsp;</span>';
        } else {
            $fileId = DbBlob::storeFilenameInSession($filename);
            return '<a href=' .
                SmartUtility::href("action={$downloadAction}&target={$fileId}&fullsize=true") .
                '><img border="0" alt="" src=' .
                SmartUtility::href("action={$downloadAction}&target={$fileId}") . '/></a>';
        }
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
     * Create span-tag.
     *
     * @access  public
     * @param   string  $content  HTML content
     * @return  string
     */
    public function buildSpan($content)
    {
        return '<span' . $this->getAttr() . ' id="' . $this->getId() . '" title="' . $this->getTitle() . '" class="' .
            $this->getCssClass() . '">' . $content . '</span>';
    }

    /**
     * Create div-tag.
     *
     * @access  public
     * @param   string  $content  HTML content
     * @return  string
     */
    public function buildDiv($content)
    {
        return '<div' . $this->getAttr() . ' id="' . $this->getId() . '" title="' . $this->getTitle() . '" class="' .
            $this->getCssClass() . '">' . $content . '</div>';
    }

    /**
     * Create a-tag with href to external website.
     *
     * @access  public
     * @param   string  $url  target URL
     * @return  string 
     */
    public function buildExternalLink($url)
    {
        $lang = Language::getInstance();

        $class = ($this->getCssClass()) ? $this->getCssClass() : 'gui_generator_ext_link';
        $title = ($this->getTitle()) ? $this->getTitle() : $lang->getVar('ext_link');

        $onclick = 'return confirm(\'' . $lang->getVar('confirm_ext_link') . '\')';
        $href = htmlspecialchars($url, ENT_COMPAT, 'UTF-8');
        $text = $url;
        if (mb_strlen($text) > 80) {
            $text = mb_substr($text, 0, 76) . ' ...';
        }
        return '<a' . $this->getAttr() . ' id="' . $this->getId() . '" class="' . $class . '" onclick="' . $onclick .
            '" title="' . $this->getTitle() . '" href="' . $href . '">' . $text . '</a>';
    }

}

?>