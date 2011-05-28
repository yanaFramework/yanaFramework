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
class FormFieldAutomatedHtmlBuilder extends FormFieldHtmlBuilder
{

    /**
     * Set name attribute based on field settings.
     *
     * @access  protected
     * @param   FormFieldFacade  $field  definition to create name from
     * @return  FormFieldAutomatedHtmlBuilder
     */
    private function _setName(FormFieldFacade $field)
    {
        $key = $field->getContext()->getRows()->key();
        $name = $field->getForm()->getName() . "[" . $field->getContext()->getContextName() . "]" .
            ((!is_null($key)) ? "[" . $key . "]" : "") . "[" . $field->getName() . "]";
        return $this->setName($name);
    }

    /**
     * Set id attribute based on field settings.
     *
     * @access  private
     * @param   FormFieldFacade  $field  definition to create id from
     * @return  FormFieldAutomatedHtmlBuilder
     */
    private function _setId(FormFieldFacade $field)
    {
        $id = $field->getForm()->getName() . "-" . $field->getContext()->getContextName() . "-" . $field->getName();
        return $this->setId($id);
    }

    /**
     * Set class attribute based on field settings.
     *
     * @access  private
     * @param   FormFieldFacade  $field  definition to create id from
     * @return  FormFieldAutomatedHtmlBuilder
     */
    private function _setCssClass(FormFieldFacade $field)
    {
        $class = $field->getForm()->getName() . "-" . $field->getContext()->getContextName() . "-" . $field->getName();
        return $this->setCssClass($class);
    }

    /**
     * Create HTML for current field.
     *
     * Returns the HTML-code representing an input element for the current field.
     * If the field has an action attached to it, an clickable icon or text-link is created next to it.
     *
     * @access  public
     * @param   FormFieldFacade  $field  structure definition
     * @return  string
     *
     * @ignore
     */
    public function __invoke(FormFieldFacade $field)
    {
        $this->_setName($field);
        $this->setTitle($field->getTitle());
        $setup = $field->getForm()->getSetup();
        switch ($field->getContext()->getContextName())
        {
            case 'update':
                if ($field->isUpdatable() && $field->getForm()->getSetup()->getUpdateAction()) {
                    return $this->buildByTypeUpdatable($field, $setup) . $this->createLink($field);
                }
            // fall through
            case 'read':
                $this->_setCssClass($field);
                return $this->buildByTypeNonUpdatable($field, $setup) . $this->createLink($field);
            case 'search':
                $this->_setId($field);
                return $this->buildByTypeSearchfield($field, $setup);
            case 'insert':
                $this->_setId($field);
                return $this->buildByTypeUpdatable($field, $setup);
            default:
                return "";
        }
    }

    /**
     * create HTML for current field
     *
     * Returns the HTML-code representing an input element for the current field.
     *
     * @access  protected
     * @param   FormFieldFacade  $field  structure definition
     * @param   FormSetup        $setup  information about how to treat the form
     * @return  string
     *
     * @ignore
     */
    protected function buildByTypeUpdatable(FormFieldFacade $field, FormSetup $setup)
    {
        $column = $field->getColumn();

        $lang = Language::getInstance();

        // retrieve search arguments
        $value = $field->getValue();
        if (is_null($value)) {
            $value = $column->getAutoValue();
        }
        if (is_string($value)) {
            $value = String::htmlSpecialChars($value);
        }

        $this->setAttr($this->createJavascriptEvents($field) . $this->getAttr()); // get javascript events

        /**
         * Switch by column's type
         */
        switch ($field->getType())
        {
            case 'array':
                return $this->buildList((array) $value, false);
            case 'list':
                return $this->buildList((array) $value, true);
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
            case 'file':
                $result = '<div class="gui_generator_file_download">';
                $result .= $this->buildFileDownload($value, $setup->getDownloadAction());
                $hasDelete = !empty($value) && $column->isNullable();
                $result .= $this->buildFilefield($hasDelete);
                $result .= '</div>';
                return $result;
            case 'image':
                $result = '<div class="gui_generator_image">';
                $result .= $this->buildImageDownload($value, $setup->getDownloadAction());
                $hasDelete = !empty($value) && $column->isNullable();
                $result .= $this->buildFilefield($hasDelete, "image/*");
                $result .= '</div>';
                return $result;
            case 'float':
                $length = (int) $column->getLength();
                $precision = (int) $column->getPrecision();
                $this->setTitle($field->getTitle() . ': ' . (($length < 8) ? str_pad('', $length, '#') : '########') .
                     '.' . (($precision < 8) ? str_pad('', $precision, '#') : '########'));
                $this->setMaxLength($length + 1);
                return $this->buildTextfield($value);
            case 'html':
                $this->setCssClass("editable");
                return $this->buildTextarea($value);
            case 'password':
                return $this->buildTextfield('', 'password');
            case 'range':
                $rangeStep = $column->getRangeStep();
                if (empty($rangeStep)) {
                    $rangeStep = 1;
                }
                if (empty($value)) {
                    $value = $column->getRangeMin();
                }
                $this->setMaxLength(4);
                return $this->buildTextfield($value) .
                    '<script type="text/javascript">yanaSlider("' . $this->getId() . '", ' . $column->getRangeMin() .
                     ', ' . $column->getRangeMax() . ', ' . $rangeStep . ', ' . $value . ');</script>';
            case 'reference':
                $null = "";
                if ($column->isNullable()) {
                    $null = $lang->getVar('choose_option');
                }
                $this->setCssClass("gui_generator_reference");
                $items = $this->getReferenceValues($field->getName()); // @todo fix this function reference
                return $this->buildSelect($items, $value, $null);
            case 'set':
                assert('!isset($items); // Cannot redeclare var $items');
                $items = $column->getEnumerationItems();
                if (empty($value)) {
                    $value = array();
                }
                $this->setCssClass("gui_generator_set");
                $result = "";
                if (count($items) < 5) {
                    $result = $this->buildCheckboxes($items, $value);
                } else {
                    $result = $this->buildSelectMultiple($items, $value);
                }
                return $result;
            case 'text':
                return $this->buildTextarea($value);
            case 'date':
                if (is_string($value)) {
                    $value = strtotime($value);
                }
                if (is_int($value)) {
                    $value = getdate($value);
                }
                $this->setCssClass("gui_generator_date");
                return $this->buildSpan(
                    SmartUtility::selectDate(array(
                        'time' => $value,
                        'attr' => $this->getAttr(),
                        'id' => $this->getId(),
                        'name' => $this->getName())
                    )
                );
            case 'time':
            case 'timestamp':
                if (is_string($value)) {
                    $value = strtotime($value);
                }
                if (is_int($value)) {
                    $value = getdate($value);
                }
                $this->setCssClass("gui_generator_time");
                return $this->buildSpan(
                    SmartUtility::selectDate(array(
                        'time' => $value,
                        'attr' => $this->getAttr(),
                        'id' => $this->getId(),
                        'name' => $this->getName())
                    ) .
                    SmartUtility::selectTime(array(
                        'time' => $value,
                        'attr' => $this->getAttr(),
                        'id' => $this->getId(),
                        'name' => $this->getName())
                    )
                );
            case 'url':
                return $this->buildTextfield($value);
            default:
                return $this->buildTextfield($value);
        }
    }

    /**
     * create HTML for non-updatable field
     *
     * Returns the HTML-code representing an input element for the current field.
     *
     * @access  protected
     * @param   FormFieldFacade  $field  structure definition
     * @param   FormSetup        $setup  information about how to treat the form
     * @return  string
     *
     * @ignore
     */
    protected function buildByTypeNonUpdatable(FormFieldFacade $field, FormSetup $setup)
    {
        $column = $field->getColumn();

        // retrieve search arguments
        $value = $field->getValue();
        if (empty($value) && $value !== false) {
            return '&ndash;';
        }

        $this->setAttr($this->createJavascriptEvents($field)); // get javascript events

        /**
         * Switch by column's type
         */
        switch ($field->getType())
        {
            case 'array':
                $this->setCssClass("gui_generator_array");
                return $this->buildDiv(SmartUtility::printUL1($value));
            case 'bool':
                $value = ($value) ? "true" : "false";
                $this->setCssClass("gui_generator_bool icon_" . $value);
                return $this->buildSpan('&nbsp;');
            case 'color':
                $this->setAttr(' style="background-color: ' . $value . '"')->setCssClass("gui_generator_color");
                return $this->buildSpan($value);
            case 'date':
                return $this->buildSpan(SmartUtility::date($value));
            case 'file':
                $this->setCssClass('gui_generator_file_download');
                return $this->buildSpan($this->buildFileDownload($value, $setup->getDownloadAction()));
            case 'text':
                $value = SmartUtility::smilies(SmartUtility::embeddedTags($value));
            // fall through
            case 'html':
                if (mb_strlen($value) > 25) {
                    $this->setCssClass('gui_generator_readonly_textarea');
                }
                return $this->buildDiv($value);
            case 'image':
                $this->setCssClass('gui_generator_image');
                return $this->buildDiv($this->buildImageDownload($value, $setup->getDownloadAction()));
            case 'enum':
            case 'set':
            case 'list':
                $this->setCssClass('gui_generator_array');
                return $this->buildDiv(SmartUtility::printUL1($value, 2));
            case 'password':
                return '&ndash;'; // never show password
            case 'reference':
                $references = $field->getColumn()->getReferenceSettings();
                $row = $field->getContext()->getRow();
                $label = mb_strtoupper($references->getLabel());
                if (!empty($label) && isset($row[$label])) {
                    $value = $row[$label];
                }
                return $this->buildSpan($value);
            case 'time':
            case 'timestamp':
                return $this->buildSpan(SmartUtility::date($value));
            case 'url':
                return $this->buildExternalLink($value);
            default:
                if (mb_strlen($value) > 80) {
                    $value = mb_substr($value, 0, 76) . '&nbsp;...';
                }
                return $this->buildSpan($value);
        }
    }

    /**
     * create HTML for current field
     *
     * Returns the HTML-code representing an input element for the current field.
     *
     * @access  protected
     * @param   FormFieldFacade  $field  structure definition
     * @param   FormSetup        $setup  information about how to treat the form
     * @return  string
     *
     * @ignore
     */
    protected function buildByTypeSearchfield(FormFieldFacade $field, FormSetup $setup)
    {
        $column = $field->getColumn();

        $lang = Language::getInstance();

        // retrieve search arguments
        $value = $field->getValue();
        if (is_null($value)) {
            $value = $column->getAutoValue();
        }

        /**
         * Switch by column's type
         */
        switch ($field->getType())
        {
            case 'bool':
                $items = array(
                    "*" => $lang->getVar('any'),
                    "true" => $lang->getVar('yes'),
                    "false" => $lang->getVar('no')
                );
                if (empty($value)) {
                    $value = "*";
                }
                $this->setCssClass("gui_generator_bool");
                return $this->buildRadio($items, $value);
            case 'enum':
                $value = array($value);
            // fall through
            case 'set':
                if (empty($value)) {
                    $value = array();
                }
                assert('!isset($items); // Cannot redeclare var $items');
                $items = $column->getEnumerationItems();
                $this->setCssClass("gui_generator_set");
                $result = "";
                if (count($items) < 5) {
                    $result = $this->buildCheckboxes($items, $value);
                } else {
                    $result = $this->buildSelectMultiple($items, $value);
                }
                return $result;
            case 'time':
            case 'timestamp':
            case 'date':
                $startTime = $field->getMinValue();
                if (empty($startTime)) {
                    $startTime = array();
                }
                $endTime = $field->getMaxValue();
                if (empty($endTime)) {
                    $endTime = array();
                }
                $name = $this->getName();
                $this->setName($name . '[active]');
                $result = $this->buildBoolCheckbox($value['active'] === "true");
                $this->setName($name);
                $result .=
                    SmartUtility::selectDate(array(
                        'time' => $startTime,
                        'id' => $this->getId() . "_start",
                        'name' => $name . "[start]")
                    ) .
                    '&nbsp;&ndash;&nbsp;' .
                    SmartUtility::selectDate(array(
                        'time' => $endTime,
                        'id' => $this->getId() . "_end",
                        'name' => $name . "[end]")
                    );
                $this->setCssClass("gui_generator_date");
                return $this->buildSpan($result);
            case 'integer':
            case 'float':
            case 'range':
                $isNumeric = true;
                $name = $this->getName();
                $id = $this->getId();
                $this->setId($id . '_start')->setName($name . '[start]');
                $result = $this->buildTextfield($value) . '&nbsp;&le;&nbsp;';
                $this->setId($id . '_end')->setName($name . '[end]');
                $result .= $this->buildTextfield($value);
                $this->setId($id)->setName($name);
                return $result;
            default:
                return $this->buildTextfield($value);
        }
    }

    /**
     * Create a reference link (where available).
     *
     * Returns the HTML-code for this field.
     *
     * @access  protected
     * @param   FormFieldFacade  $field  structure definition
     * @return  string
     * @ignore
     */
    protected function createLink(FormFieldFacade $field)
    {
        $result = "";
        if ($field->getField() instanceof DDLField) {
            $value = $field->getValue();
            if (empty($value) && $value !== false) {
                return '';
            }
            $lang = Language::getInstance();
            $column = $field->getColumn();
            $form = $field->getForm();
            $table = $form->getTable();
            $id = 'id="' . $form->getName() . '-' . $table->getPrimaryKey() . '-' .
                $form->getPrimaryKey() . '-' . $field->getName() . '"';
            $class = 'class="gui_generator_int_link"';
            /* @var $event DDLEvent */
            foreach ($field->getField()->getEvents() as $event)
            {
                $code = $event->getAction();
                $label = $event->getLabel();
                $title = $event->getTitle();
                $icon = $event->getIcon();
                $href = "";

                switch (strtolower($event->getLanguage()))
                {
                    case 'javascript':
                        assert('!isset($actionId);');
                        $actionId = String::htmlSpecialChars($event->getAction());
                        $href = 'href="javascript://" ' . $event->getName() . '="' . $actionId . '"';
                        unset($actionId);
                    break;
                    default:
                        $actionParam = "action=" . $event->getName();
                        $targetParam = "target[" . $table->getPrimaryKey() . "]=" . $form->getPrimaryKey() .
                            "&target[" . $field->getName() . "]=" . $value;
                        $href = 'href="' . SmartUtility::url("$actionParam&$targetParam") . '"';
                        if (empty($title)) {
                            $title = $lang->getVar('DB_ENTITY_LINK');
                        }
                    break;
                }
                if (!empty($title)) {
                    $title = "title=\"$title\"";
                }
                if (!empty($icon)) {
                    $icon  = '<img src="' . $icon . '" alt="' . $lang->getVar('BUTTON_OPEN') . '"/>';
                }
                if (!empty($label)) {
                    $result .= "<a $id $class $title $href>$label$icon</a>";
                }
            } // end foreach
        }
        return $result;
    }

    /**
     * Create a javascript events (where available).
     *
     * Returns the HTML-code for the generated attributes.
     *
     * Example:
     * <pre> onclick="alert('Hello World')" onchange="validate(this)"</pre>
     *
     * Note: the results are cached.
     *
     * @access  protected
     * @param   FormFieldFacade  $field  input field
     * @return  string
     * @ignore
     */
    protected function createJavascriptEvents(FormFieldFacade $field)
    {
        $eventsAsHtml = "";
        if ($field->getField()) {
            /* @var $event DDLEvent */
            foreach ($field->getField()->getEvents() as $event)
            {
                if (strtolower($event->getLanguage()) !== 'javascript') {
                    continue; // non-javascript - ignore!
                }
                if ($event->getLabel() || $event->getIcon() ) {
                    continue; // these are links - ignore!
                }
                $name = $event->getName();
                $code = String::htmlSpecialChars($event->getAction());
                $eventsAsHtml .= " $name=\"$code\"";
            } // end foreach
        }
        return $eventsAsHtml;
    }

}

?>