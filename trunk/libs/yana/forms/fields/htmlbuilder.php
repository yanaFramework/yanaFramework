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

namespace Yana\Forms\Fields;

/**
 * <<builder>> HTML Form builder.
 *
 * This class is meant to create HTML fields for forms.
 *
 * @package     yana
 * @subpackage  form
 */
class HtmlBuilder extends \Yana\Views\Helpers\Html\AbstractHelper
{

    /**
     * Creates a new instance and returns it.
     *
     * @return  \Yana\Db\Binaries\FileNameCache
     */
    protected function _getFilenameCache()
    {
        return new \Yana\Db\Binaries\FileNameCache();
    }

    /**
     * Creates a new instance and returns it.
     *
     * @return  \Yana\Views\Helpers\Formatters\UrlFormatter
     */
    protected function _getUrlFormatter()
    {
        return new \Yana\Views\Helpers\Formatters\UrlFormatter();
    }

    /**
     * Creates a new instance and returns it.
     *
     * @return  \Yana\Translations\Facade
     */
    protected function _getTranslationFacade()
    {
        return \Yana\Translations\Facade::getInstance();
    }

    /**
     * Generate HTML select element.
     *
     * If the item list is a multi-dimensional array, "optgroup" elements will be created to
     * group the items. Note that you should not use nested optgroups.
     *
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
     * @param   array   $values         item list
     * @param   array   $selectedValue  selected values
     * @return  string
     */
    public function buildSelectMultiple(array $values, array $selectedValue)
    {
        return $this->_getSelect($values, $selectedValue, true);
    }

    /**
     * Select date.
     *
     * Returns:
     * <code>
     *   <select name="foo_day"><option>01</option>...<option>31</option></select>
     *   <select name="foo_month"><option>01</option>...<option>12</option></select>
     *   <select name="foo_year"><option>1910</option>...<option>2035</option></select>
     * </code>
     *
     * @param   array  $value  must have indexes: "day", "month" and "year", defaults to current timestamp
     * @return  string
     */
    public function buildDateSelector(array $value = array())
    {
        assert('!isset($day); // Cannot redeclare var $day');
        assert('!isset($month); // Cannot redeclare var $month');
        assert('!isset($year); // Cannot redeclare var $year');
        // get timestamp
        switch (true)
        {
            case empty($value) || !is_array($value):
            case !isset($value['day']):
            case !isset($value['month']):
            case !isset($value['year']):
                // use current timestamp if no value provided
                $day = (int) date('j');
                $month = (int) date('n');
                $year = (int) date('Y');
            break;
            default:
                $day = (int) $value['day'];
                $month = (int) $value['month'];
                $year = (int) $value['year'];
            break;
        }
        assert('!isset($name); // Cannot redeclare var $name');
        $name = $this->getName();
        assert('!isset($id); // Cannot redeclare var $id');
        $id = $this->getId();
        assert('!isset($days); // Cannot redeclare var $days');
        $days = $this->_arrayFill(31);
        assert('!isset($months); // Cannot redeclare var $months');
        $months = $this->_arrayFill(12);
        assert('!isset($years); // Cannot redeclare var $years');
        $years = $this->_arrayFill($year + 20, $year - 100);

        // returns "<select day><select month><select year><icon>"
        assert('!isset($string); // Cannot redeclare var $string');
        $string = $this->setId($id . "_day")->setName($name . "[day]")->_getSelect($days, array($day))
            . $this->setId($id . "_month")->setName($name . "[month]")->_getSelect($months, array($month))
            . $this->setId($id . "_year")->setName($name . "[year]")->_getSelect($years, array($year))
            . '<script type="text/javascript">yanaAddCalendar("' . $id . '", "' . $id . '_year", '
            . $day . ', ' . ($month - 1) . ', ' . $year . ');</script>'.
            '<script type="text/javascript" src=\'' . \Yana\Views\Skins\Skin::getSkinDirectory('default')
            . 'scripts/calendar/' . $this->_getTranslationFacade()->getVar('calendar.js') . "'></script>";
        // Reset changed name and id.
        $this->setId($id)->setName($name);
        return $string;
    }

    /**
     * Select time.
     *
     * Returns:
     * <code>
     *   <select name="foo_hour"><option>00</option>...<option>23</option></select>:
     *   <select name="foo_minute"><option>00</option>...<option>59</option></select>
     * </code>
     *
     * @param   array  $value  must have indexes: "hour" and "minute", defaults to current timestamp
     * @return  string
     */
    public function buildTimeSelector(array $value = array())
    {
        // get timestamp
        switch (true)
        {
            case empty($value):
            case !isset($value['hour']):
            case !isset($value['minute']):
                // use current timestamp if no value provided
                $hour = (int) date('H');
                $minute = (int) date('i');
            break;
            default:
                $hour = (int) $value['hour'];
                $minute = (int) $value['minute'];
            break;
        }

        $name = $this->getName();
        $id = $this->getId();
        $hours = $this->_arrayFill(23, 0);
        $minutes = $this->_arrayFill(59, 0);

        // returns "<select hour>:<select minute>"
        $string = $this->setId($id . "_hour")->setName($name . "[hour]")->_getSelect($hours, array($hour)) . ':'
            . $this->setId($id . "_minute")->setName($name . "[minute]")->_getSelect($minutes, array($minute));
        // Reset changed name and id.
        $this->setId($id)->setName($name);

        return $string;
    }

    /**
     * Used by date- and time-selectors.
     *
     * Creates an array of numbers 01,02,...,31 to create option-fields.
     *
     * @param  int  $maxInt  maximum index number to return: [1,x]
     * @param  int  $minInt  minimum index number to return: [x,2050]
     * @return array
     */
    private function _arrayFill($maxInt, $minInt = 1)
    {
        $array = array();
        for ($i = $minInt; $i <= $maxInt; $i++)
        {
            $array[$i] = ($i < 10 ? "0" : "") . (string) $i;
        }
        return $array;
    }

    /**
     * Generate HTML select element.
     *
     * @param   array   $values          item list
     * @param   array   $selectedValues  one or more selected values
     * @param   bool    $isMultiple      allow to select multiple values
     * @param   string  $null            text for NULL item (may be empty if there is none)
     * @return  string
     */
    private function _getSelect(array $values, array $selectedValues, $isMultiple = false, $null = "")
    {
        if (empty($values)) {
            return '';
        }
        return '<select class="' . $this->getCssClass() . '" id="' . $this->getId() . '" name="' . $this->getName() .
            (($isMultiple) ? '[]" multiple="multiple"' : '"') .
            ($this->getAttr() > '' ? ' ' : '') . $this->getAttr() . '>' . (($null) ? '<option value="">'. $null . '</option>' : '') .
            $this->_getOptions($values, (array) $selectedValues) .
            '</select>';
    }

    /**
     * Create HTML option and optgroup elements.
     *
     * @param   array  $values          item list
     * @param   array  $selectedValues  selected values
     * @return  string
     */
    private function _getOptions(array $values, array $selectedValues)
    {
        $result = "";
        foreach ($values as $key => $text)
        {
            if (is_array($text)) { // is optgroup
                $result .= '<optgroup label="' . $key . '">' .
                    $this->_getOptions($text, $selectedValues) . '</optgroup>';
            } else { // is option
                $result .= '<option value="' . $key . '"' .
                    ((in_array($key, $selectedValues)) ? ' selected="selected"' : '') . '>' . $text . '</option>';
            }
        }
        return $result;
    }

    /**
     * Create HTML radio element.
     *
     * @param   array   $values    item list
     * @param   string  $selected  selected value
     * @param   string  $null      text for NULL item (may be empty if there is none)
     * @return  string
     */
    public function buildRadio(array $values, $selected, $null = "")
    {
        $attr = ($this->getAttr() > '' ? ' ' : '') . $this->getAttr();
        $class = $this->getCssClass();
        $result = '';
        if ($null) {
            $result = '<label class="' . $class . '"><input type="radio"' . $attr . ' ' .
                'name="' . $this->getName() . '" value=""/>' . $null . '</label>';
        }

        $id = ' id="' . $this->getId() . '"'; // only first element
        foreach ($values as $key => $text)
        {
            $result .= ($result > '' ? ' ' : '') . '<label class="' . $class . '"><input' . $id . $attr . ' type="radio" ' .
                'name="' . $this->getName() . '" value="' . $key . '"' .
                (($key === $selected) ? ' checked="checked"' : '') . '/>' . $text . '</label>';
            $id = ""; // reset id for secound element
        }

        return $result;
    }

    /**
     * Create HTML checkbox element.
     *
     * @param   array   $values   item list
     * @param   array   $checked  selected values
     * @return  string
     */
    public function buildCheckboxes(array $values, array $checked)
    {
        $class = ($this->getCssClass()) ? $this->getCssClass() : "gui_generator_check";
        $attr = ($this->getAttr() > '' ? ' ' : '') . $this->getAttr();
        $template = '<label class="' . $class . '" title="' . $this->getTitle() . '"><input%s' . $attr .
            ' type="checkbox" name="' . $this->getName() . '[]"' .
            ' class="' . $class . '" value="%s"/>%s</label>' . "\n";
        $attributes = ' id="' . $this->getId() . '"'; // only first element
        return $this->_getCheckBoxes($template, $attributes, $values, $checked);
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
     * @param   bool    $isChecked  true = checkbox is checked, false = checkbox is not checked
     * @return  string
     */
    public function buildBoolCheckbox($isChecked)
    {
        $class = ($this->getCssClass()) ? $this->getCssClass() : "gui_generator_check";
        return '<input type="hidden" name="' . $this->getName() . '" value="0"/>' . // add a default value
            '<input' . ($this->getAttr() > '' ? ' ' : '') . $this->getAttr() . ' id="' . $this->getId() . '" class="' . $class . '" type="checkbox" ' .
            'name="' . $this->getName() . '" value="1" ' . ($isChecked ? 'checked="checked" ' : '') .
            'title="' . $this->getTitle() . '"/>';
    }

    /**
     * Create HTML checkbox and fieldset elements.
     *
     * @param   string  $template  checkbox template for sprintf
     * @param   string  &$attr     additional attributes (for first element only)
     * @param   array   $items     item list
     * @param   array   $checked   selected values
     * @return  string
     */
    private function _getCheckBoxes($template, &$attr, array $items, array $checked)
    {
        $result = "";
        foreach ($items as $key => $text)
        {
            if (is_array($text)) { // is optgroup
                $result .= '<fieldset><legend>' . $key . '</legend>' .
                    $this->_getCheckBoxes($template, $attr, $text, $checked) . '</fieldset>';
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
     * @param   array  $values     list of items  
     * @param   bool   $isNumeric  true = numeric list, false = associative array
     * @return  string 
     */
    public function buildList(array $values = array(), $isNumeric = false)
    {
        assert('!isset($lang); // Cannot redeclare var $lang');
        $lang = $this->_getTranslationFacade();
        assert('!isset($template); // Cannot redeclare var $template');
        $template = '';

        if (!$isNumeric) {
            $template = '<input' . ($this->getAttr() > '' ? ' ' : '') . $this->getAttr() . ' size="5" type="text" ' .
                'name="' . $this->getName() . '[names][]" value="%s"/>' .
                '&nbsp;=&nbsp;<input size="10" type="text" name="' . $this->getName() . '[values][]" value="%s"/>' .
                '<a class="buttonize" href="javascript://yanaRemoveItem(this)" ' .
                'onclick="yanaRemoveItem(this)" title="'. $lang->getVar('remove') . '">' .
                '<span class="icon_delete">&nbsp;</span></a>' .
                '<a class="buttonize" href="javascript://yanaAddItem(this)" onclick="yanaAddItem(this)" ' .
                'title="' . $lang->getVar('button_new') . '">' .
                '<span class="icon_new">&nbsp;</span></a>';
        } else {
            $template = '<input' . ($this->getAttr() > '' ? ' ' : '') . $this->getAttr() . ' size="21" type="text" ' .
                'name="' . $this->getName() .'[%i]" value="%s"/>' .
                '<a class="buttonize" href="javascript://yanaRemoveItem(this)" ' .
                'onclick="yanaRemoveItem(this)" title="'. $lang->getVar('remove') . '">' .
                '<span class="icon_delete">&nbsp;</span></a>' .
                '<a class="buttonize" href="javascript://yanaAddItem(this)" onclick="yanaAddItem(this)" ' .
                'title="' . $lang->getVar('button_new') . '">' .
                '<span class="icon_new">&nbsp;</span></a>';
        }

        assert('!isset($id); // Cannot redeclare var $id');
        $id = $this->getId() > '' ? ' id="' . $this->getId() . '"' : ''; 
        assert('!isset($result); // Cannot redeclare var $result');
        $result = '<div' . $id . ' class="' . (($this->getCssClass()) ? $this->getCssClass() : "gui_generator_array")  . '">';
        unset($id);

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
     * @param   string  $value  some text, must not contain line-breaks.
     * @param   string  $type   valid HTML type attribute.
     * @return  string
     */
    public function buildTextfield($value, $type = 'text')
    {
        assert('is_string($type); // Invalid argument $text: string expected');
        assert('preg_match("/^[a-z]+$/", $type); // Invalid argument $text: must only contain characters a-z');
        $maxLength = (int) $this->getMaxLength();
        $attr = ($this->getAttr() > '' ? ' ' : '') . $this->getAttr();
        return '<input' . $attr .' id="' . $this->getId() . '" name="' . $this->getName() . '" ' .
            'class="' . $this->getCssClass() . '" type="' . $type . '" value="' . $value .
            '"' . ( ($maxLength > 0 ) ? ' maxlength="' . $maxLength . '"' : '' ) .
            ( ($maxLength < 22 && $maxLength > 0 ) ? ' size="' . $maxLength . '"' : '' ) . ' title="' . $this->getTitle() . '"' . '/>';
    }

    /**
     * Create HTML input field of type file.
     *
     * This also adds a checkbox to delete existing files on demand.
     *
     * @param   bool  $hasDelete  true = add "delete" button for existing file, false = no "delete" button
     * @return  string 
     */
    public function buildFilefield($hasDelete, $mimeType = '')
    {
        assert('is_bool($hasDelete); // Invalid argument $hasDelete: bool expected');
        assert('is_string($mimeType); // Invalid argument $mimeType: string expected');
        $attr = ($this->getAttr() > '' ? ' ' : '') . $this->getAttr();
        if ($mimeType) {
            $attr .= ' accept="' . \Yana\Util\Strings::htmlSpecialChars((string) $mimeType) . '"';
        }
        if ($this->getMaxLength() > 0) {
            $attr .= ' maxlength="' . (int) $this->getMaxLength() . '"';
        }
        $result = '<input' . $attr . ' size="1" type="file" id="' . $this->getId() . '" name="' .
            $this->getName() . '"/>';

        if ($hasDelete) {
            $lang = $this->_getTranslationFacade();
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
     * @param   string  $value  some text, must not contain line-breaks.
     * @return  string
     */
    public function buildTextarea($value)
    {
        $check = 'cols="20"';
        if ($this->getMaxLength() > 2000) {
            $check = 'cols="30"';
        }
        if ($value) {
            $value = $value[0] . str_replace('[br]', "\n", substr($value, 1));
        }
        return $this->_buildTag('textarea', $value, $check . ' rows="3" name="' . $this->getName() . '"');
    }

    /**
     * Create download link for file.
     *
     * @param   string  $filename        target file
     * @param   string  $downloadAction  name of function called to download the file
     * @return  string 
     */
    public function buildFileDownload($filename, $downloadAction)
    {
        if (empty($filename) || !is_string($filename) || empty($downloadAction) || !is_string($downloadAction)) {
            return '<span class="icon_blank">&nbsp;</span>';
        }

        assert('is_string($filename); // Invalid argument $filename: string expected');
        assert('is_string($downloadAction); // Invalid argument $downloadAction: string expected');
        $lang = $this->_getTranslationFacade();
        $fileId = $this->_getFilenameCache()->storeFilename($filename);
        $formatter = $this->_getUrlFormatter();
        return '<a class="buttonize" title="' . $lang->getVar('title_download') . '" href="' .
            $formatter("action={$downloadAction}&target={$fileId}", false, false) .
            '"><span class="icon_download">&nbsp;</span></a>';
    }

    /**
     * Create download link and preview for image file.
     *
     * @param   string  $filename        target file
     * @param   string  $downloadAction  name of function called to download the file
     * @return  string 
     */
    public function buildImageDownload($filename, $downloadAction)
    {
        if (empty($filename) || empty($downloadAction)) {
            return '<span class="icon_blank">&nbsp;</span>';
        }

        assert('is_string($filename); // Invalid argument $filename: string expected');
        assert('is_string($downloadAction); // Invalid argument $downloadAction: string expected');
        $fileId = $this->_getFilenameCache()->storeFilename($filename);
        $formatter = $this->_getUrlFormatter();
        return '<a href="' . $formatter("action={$downloadAction}&target={$fileId}&fullsize=true", false, false) . '">' .
            '<img border="0" alt="" src="' . $formatter("action={$downloadAction}&target={$fileId}", false, false) . '"/>' .
            '</a>';
    }

    /**
     * Create HTML input field of type color.
     *
     * @param   string  $value  some text, must not contain line-breaks.
     * @return  string
     */
    public function buildColorpicker($value)
    {
        return $this->buildTextfield($value, 'color');
    }

    /**
     * Create span-tag.
     *
     * @param   string  $content  HTML content
     * @return  string
     */
    public function buildSpan($content)
    {
        return $this->_buildTag('span', $content);
    }

    /**
     * Create div-tag.
     *
     * @param   string  $content  HTML content
     * @return  string
     */
    public function buildDiv($content)
    {
        return $this->_buildTag('div', $content);
    }

    /**
     * Create a-tag with href to external website.
     *
     * @param   string  $url  target URL
     * @return  string 
     */
    public function buildExternalLink($url)
    {
        $lang = $this->_getTranslationFacade();

        $class = ($this->getCssClass()) ? $this->getCssClass() : 'gui_generator_ext_link';
        $title = ($this->getTitle()) ? $this->getTitle() : $lang->getVar('ext_link');

        $onclick = 'return confirm(\'' . $lang->getVar('confirm_ext_link') . '\')';
        $href = \Yana\Util\Strings::htmlSpecialChars((string) $url);
        $text = $url;
        if (mb_strlen($text) > 80) {
            $text = mb_substr($text, 0, 76) . ' ...';
        }
        return '<a' . ($this->getAttr() > '' ? ' ' : '') . $this->getAttr() . ' id="' . $this->getId() . '" title="' . $title . '" class="' . $class .
            '" onclick="' . $onclick . '" href="' . $href . '">' . $text . '</a>';
    }

    /**
     * Create input tag of type range.
     * 
     * @param   float   $value      numeric value
     * @param   float   $rangeMin   lowest accepted value
     * @param   float   $rangeMax   highest accepted value
     * @param   float   $rangeStep  smallest possible step
     * @return  string
     */
    public function buildRange($value, $rangeMin, $rangeMax, $rangeStep)
    {
        assert('is_float($value); // Invalid argument $value: float expected');
        assert('is_float($rangeMin); // Invalid argument $rangeMin: float expected');
        assert('is_float($rangeMax); // Invalid argument $rangeMax: float expected');
        assert('is_float($rangeStep); // Invalid argument $rangeStep: float expected');

        $attr = ($this->getAttr() > '' ? ' ' : '') . $this->getAttr();
        return '<input' . $attr .' id="' . $this->getId() . '" name="' . $this->getName() . '" ' .
            'class="' . $this->getCssClass() . '" type="range" value="' . (float) $value .
            '" min="' . (float) $rangeMin . '" max="' . (float) $rangeMax . '" step="' . (float) $rangeStep .
            ' title="' . $this->getTitle() . '"' .
            ' onchange="document.getElementById(\'' . $this->getId() . 'output\').innerHTML=this.value"/>' .
            '<output for="' . $this->getId() . '" id="' . $this->getId() . 'output">' . (float) $value . '</output>';
    }

    /**
     * Build simple div/span/textarea tags.
     *
     * @param   string  $tagName  the name of the tag
     * @param   string  $content  the content between the opening and closing tag
     * @param   string  $more     additional attributes (if any)
     * @return  string
     */
    protected function _buildTag($tagName, $content, $more = "")
    {
        return '<' . $tagName . ($this->getAttr() > '' ? ' ' : '') . $this->getAttr() . ' id="' . $this->getId() . '" title="' . $this->getTitle() .
            '" class="' . $this->getCssClass() . '"' . ($more > '' ? ' ' : '') . $more . '>' . $content . '</' . $tagName . '>';
    }

}

?>