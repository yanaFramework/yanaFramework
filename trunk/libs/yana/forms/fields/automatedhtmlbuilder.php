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
declare(strict_types=1);

namespace Yana\Forms\Fields;

/**
 * <<builder>> HTML Form builder.
 *
 * This class is meant to create HTML fields for forms.
 *
 * @package     yana
 * @subpackage  form
 */
class AutomatedHtmlBuilder extends \Yana\Forms\Fields\HtmlBuilder
{

    /**
     * Set name attribute based on field settings.
     *
     * @param   \Yana\Forms\Fields\IsField  $field  definition to create name from
     * @return  \Yana\Forms\Fields\AutomatedHtmlBuilder
     */
    private function _setName(\Yana\Forms\Fields\IsField $field)
    {
        $key = $field->getContext()->getRows()->key();
        $formName = $field->getForm()->getName();
        $contextName = $field->getContext()->getContextName();
        $fieldName = $field->getName();

        $name = $formName . "[" . $contextName . "]" . ((!is_null($key)) ? "[" . $key . "]" : "") . "[" . $fieldName . "]";
        return $this->setName($name);
    }

    /**
     * Set id attribute based on field settings.
     *
     * @param   \Yana\Forms\Fields\IsField  $field  definition to create id from
     * @return  \Yana\Forms\Fields\AutomatedHtmlBuilder
     */
    private function _setId(\Yana\Forms\Fields\IsField $field)
    {
        $id = $field->getForm()->getName() . "-" . $field->getContext()->getContextName() . "-" . $field->getName();
        return $this->setId($id);
    }

    /**
     * Set id attribute based on row number and field settings.
     *
     * @param   \Yana\Forms\Fields\IsField  $field  definition to create name from
     * @return  \Yana\Forms\Fields\AutomatedHtmlBuilder
     */
    private function _setIdByRow(\Yana\Forms\Fields\IsField $field)
    {
        $key = $field->getContext()->getRows()->key();
        $id = $field->getForm()->getName() . "-" . $field->getContext()->getContextName() .
            ((!is_null($key)) ? "-" . $key : "") . "-" . $field->getName();
        return $this->setId($id);
    }

    /**
     * Set class attribute based on field settings.
     *
     * @param   \Yana\Forms\Fields\IsField  $field  definition to create id from
     * @return  \Yana\Forms\Fields\AutomatedHtmlBuilder
     */
    private function _setCssClass(\Yana\Forms\Fields\IsField $field)
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
     * @param   \Yana\Forms\Fields\IsField  $field  structure definition
     * @return  string
     *
     * @ignore
     */
    public function __invoke(\Yana\Forms\Fields\IsField $field)
    {
        $this->_setName($field);
        $setup = $field->getForm()->getSetup();
        switch ($field->getContext()->getContextName())
        {
            case \Yana\Forms\Setups\ContextNameEnumeration::UPDATE:
                if ($field->isUpdatable() && $field->getForm()->getSetup()->getUpdateAction()) {
                    $this->_setIdByRow($field);
                    return $this->buildByTypeUpdatable($field, $setup) . $this->createLink($field);
                }
            // fall through
            case \Yana\Forms\Setups\ContextNameEnumeration::READ:
                $this->_setCssClass($field);
                return $this->buildByTypeNonUpdatable($field, $setup) . $this->createLink($field);
            case \Yana\Forms\Setups\ContextNameEnumeration::SEARCH:
                $this->_setId($field);
                return $this->buildByTypeSearchfield($field, $setup);
            case \Yana\Forms\Setups\ContextNameEnumeration::INSERT:
                $this->_setId($field);
                return $this->buildByTypeUpdatable($field, $setup);
            default:
                return "";
        }
    }

    /**
     * Create HTML for an updatable field.
     *
     * Returns the HTML-code representing an input element for the current field.
     *
     * @param   \Yana\Forms\Fields\IsField  $field  structure definition
     * @param   \Yana\Forms\IsSetup          $setup  information about how to treat the form
     * @return  string
     *
     * @ignore
     */
    protected function buildByTypeUpdatable(\Yana\Forms\Fields\IsField $field, \Yana\Forms\IsSetup $setup)
    {
        $column = $field->getColumn();

        $lang = \Yana\Translations\Facade::getInstance();

        // retrieve search arguments
        $value = $field->getValue();
        if (is_null($value)) {
            $value = $column->getAutoValue();
        }
        if (is_string($value)) {
            $value = \Yana\Util\Strings::htmlSpecialChars($value);
        }

        $this->setAttr($this->createJavascriptEvents($field) . $this->getAttr()); // get javascript events

        /**
         * Switch by column's type
         */
        switch ($field->getType())
        {
            case \Yana\Db\Ddl\ColumnTypeEnumeration::ARR:
                return $this->buildList((array) $value, false);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::LST:
                return $this->buildList((array) $value, true);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL:
                return $this->buildBoolCheckbox($value);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::COLOR:
                return $this->buildColorpicker($value);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::ENUM:
                $items = $column->getEnumerationItems();
                $null = "";
                if ($column->isNullable()) {
                    $null = $lang->getVar('choose_option');
                }
                if (!$this->getCssClass()) {
                    $this->setCssClass("gui_generator_set");
                }
                return $this->buildSelect($items, $value, $null);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::FILE:
                if (!is_string($value)) {
                    $value = "";
                }
                $result = '<div class="gui_generator_file_download">';
                $result .= $this->buildFileDownload($value, $setup->getDownloadAction());
                $hasDelete = !empty($value) && $column->isNullable();
                $result .= $this->buildFilefield($hasDelete);
                $result .= '</div>';
                return $result;
            case \Yana\Db\Ddl\ColumnTypeEnumeration::IMAGE:
                if (!is_string($value)) {
                    $value = "";
                }
                $result = '<div class="gui_generator_image">';
                $result .= $this->buildImageDownload($value, $setup->getDownloadAction());
                $hasDelete = !empty($value) && $column->isNullable();
                $result .= $this->buildFilefield($hasDelete, "image/*");
                $result .= '</div>';
                return $result;
            case \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT:
                $length = (int) $column->getLength();
                $precision = (int) $column->getPrecision();
                $this->setTitle($field->getTitle() . ': ' . (($length < 8) ? str_pad('', $length, '#') : '########') .
                     '.' . (($precision < 8) ? str_pad('', $precision, '#') : '########'));
                $this->setMaxLength($length + 1);
                return $this->buildTextfield($value);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::HTML:
                $this->setCssClass("editable");
                return $this->buildTextarea($value);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::PASSWORD:
                return $this->buildTextfield('', 'password');
            case \Yana\Db\Ddl\ColumnTypeEnumeration::RANGE:
                $rangeStep = $column->getRangeStep();
                if (empty($rangeStep)) {
                    $rangeStep = 1.0;
                }
                if (empty($value)) {
                    $value = $column->getRangeMin();
                }
                $this->setMaxLength(4);
                return $this->buildRange((float) $value, (float) $column->getRangeMin(), (float) $column->getRangeMax(), $rangeStep);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::REFERENCE:
                $null = "";
                if ($column->isNullable()) {
                    $null = $lang->getVar('choose_option');
                }
                $this->setCssClass("gui_generator_reference");
                $items = $field->getForm()->getSetup()->getReferenceValues($column->getName());
                return $this->buildSelect($items, $value, $null);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::SET:
                assert(!isset($items), 'Cannot redeclare var $items');
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
            case \Yana\Db\Ddl\ColumnTypeEnumeration::TEXT:
                return $this->buildTextarea($value);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::DATE:
                if (is_string($value)) {
                    $value = strtotime($value);
                }
                if (empty($value)) {
                    $value = time();
                }
                if (is_int($value)) {
                    $value = array(
                        'day' => (int) date('j', $value),
                        'month' =>(int) date('n', $value),
                        'year' => (int) date('Y', $value),
                        'hour' => (int) date('H', $value),
                        'minute' => (int) date('i', $value)
                    );
                }
                $this->setCssClass("gui_generator_date");
                return $this->buildSpan($this->buildDateSelector($value));
            case \Yana\Db\Ddl\ColumnTypeEnumeration::TIME:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::TIMESTAMP:
                if (is_string($value)) {
                    $value = strtotime($value);
                }
                if (empty($value)) {
                    $value = time();
                }
                if (is_int($value)) {
                    $value = array(
                        'day' => (int) date('j', $value),
                        'month' =>(int) date('n', $value),
                        'year' => (int) date('Y', $value),
                        'hour' => (int) date('H', $value),
                        'minute' => (int) date('i', $value)
                    );
                }
                $this->setCssClass("gui_generator_time");
                return $this->buildSpan(
                    $this->buildDateSelector($value) .
                    $this->buildTimeSelector($value)
                );
            case \Yana\Db\Ddl\ColumnTypeEnumeration::URL:
                return $this->buildTextfield($value);
            default:
                return $this->buildTextfield($value);
        }
    }

    /**
     * Create HTML for non-updatable field.
     *
     * Returns the HTML-code representing an input element for the current field.
     *
     * @param   \Yana\Forms\Fields\IsField  $field  structure definition
     * @param   \Yana\Forms\IsSetup          $setup  information about how to treat the form
     * @return  string
     *
     * @ignore
     */
    protected function buildByTypeNonUpdatable(\Yana\Forms\Fields\IsField $field, \Yana\Forms\IsSetup $setup)
    {
        // retrieve search arguments
        $value = $field->getValue();
        // Convert "NULL"-values to dash
        if (is_null($value) || $value === array() || $value === "") {
            return '&ndash;';
        }

        $this->setAttr($this->createJavascriptEvents($field)); // get javascript events

        /**
         * Switch by column's type
         */
        switch ($field->getType())
        {
            case \Yana\Db\Ddl\ColumnTypeEnumeration::ARR:
                $this->setCssClass("gui_generator_array");
                return $this->buildDiv(\Yana\Views\Helpers\Html\MenuHelper::factory()->__invoke($value));
            case \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL:
                $value = ($value) ? "true" : "false";
                $this->setCssClass("gui_generator_bool icon_" . $value);
                return $this->buildSpan('&nbsp;');
            case \Yana\Db\Ddl\ColumnTypeEnumeration::COLOR:
                $this->setAttr('style="background-color: ' . \Yana\Util\Strings::htmlSpecialChars((string) $value) . '"')->setCssClass("gui_generator_color");
                return $this->buildSpan(\Yana\Util\Strings::htmlSpecialChars((string) $value));
            case \Yana\Db\Ddl\ColumnTypeEnumeration::FILE:
                $this->setCssClass('gui_generator_file_download');
                if (!is_string($value)) {
                    $value = "";
                }
                return $this->buildSpan($this->buildFileDownload($value, $setup->getDownloadAction()));
            case \Yana\Db\Ddl\ColumnTypeEnumeration::TEXT:
                $textFormatter = new \Yana\Views\Helpers\Formatters\TextFormatterCollection();
                $value = $textFormatter(\Yana\Util\Strings::htmlSpecialChars((string) $value));
            // fall through
            case \Yana\Db\Ddl\ColumnTypeEnumeration::HTML:
                if (mb_strlen($value) > 25) {
                    $this->setCssClass('gui_generator_readonly_textarea');
                }
                return $this->buildDiv($value);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::IMAGE:
                if (!is_string($value)) {
                    $value = "";
                }
                $this->setCssClass('gui_generator_image');
                return $this->buildDiv($this->buildImageDownload($value, $setup->getDownloadAction()));
            case \Yana\Db\Ddl\ColumnTypeEnumeration::ENUM:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::SET:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::LST:
                $this->setCssClass('gui_generator_array');
                if (is_array($value)) {
                    $value = \Yana\Views\Helpers\Html\MenuHelper::factory()
                        ->setUseKeys(\Yana\Views\Helpers\Html\MenuLayouts\KeyEnumeration::DONT_PRINT_KEYS)
                        ->__invoke($value);
                }
                return $this->buildDiv((string) $value);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::PASSWORD:
                return '&ndash;'; // never show password
            case \Yana\Db\Ddl\ColumnTypeEnumeration::REFERENCE:
                $label = mb_strtoupper($field->getColumn()->getReferenceSettings()->getLabel());
                if ($label !== "") {
                    $row = $field->getContext()->getRow();
                    $value = isset($row[$label]) ? $row[$label] : (string) $value;
                }
                if (!is_string($value)) {
                    $value = "";
                }
                return $this->buildSpan(\Yana\Util\Strings::htmlSpecialChars((string) $value));
            case \Yana\Db\Ddl\ColumnTypeEnumeration::DATE:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::TIME:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::TIMESTAMP:
                $dateFormatter = new \Yana\Views\Helpers\Formatters\DateFormatter();
                return $this->buildSpan($dateFormatter($value));
            case \Yana\Db\Ddl\ColumnTypeEnumeration::URL:
                if (!is_string($value)) {
                    return "&ndash;";
                }
                return $this->buildExternalLink($value);
            default:
                if (!is_scalar($value)) {
                    $value = "&ndash;";

                } elseif (mb_strlen((string) $value) > 80) {
                    $value = \Yana\Util\Strings::htmlSpecialChars(mb_substr((string) $value, 0, 76)) . '&nbsp;...';

                } else {
                    $value = \Yana\Util\Strings::htmlSpecialChars((string) $value);
                }
                return $this->buildSpan($value);
        }
    }

    /**
     * Create HTML for a searchable field.
     *
     * Returns the HTML-code representing an input element for the current field.
     *
     * @param   \Yana\Forms\Fields\IsField  $field  structure definition
     * @param   \Yana\Forms\IsSetup         $setup  information about how to treat the form
     * @return  string
     *
     * @ignore
     */
    protected function buildByTypeSearchfield(\Yana\Forms\Fields\IsField $field, \Yana\Forms\IsSetup $setup)
    {
        $column = $field->getColumn();

        $lang = \Yana\Translations\Facade::getInstance();

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
            case \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL:
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
            case \Yana\Db\Ddl\ColumnTypeEnumeration::ENUM:
                $value = array($value);
            // fall through
            case \Yana\Db\Ddl\ColumnTypeEnumeration::SET:
                if (empty($value)) {
                    $value = array();
                }
                assert(!isset($items), 'Cannot redeclare var $items');
                $items = $column->getEnumerationItems();
                $this->setCssClass("gui_generator_set");
                $result = "";
                if (count($items) < 5) {
                    $result = $this->buildCheckboxes($items, $value);
                } else {
                    $result = $this->buildSelectMultiple($items, $value);
                }
                return $result;
            case \Yana\Db\Ddl\ColumnTypeEnumeration::TIME:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::TIMESTAMP:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::DATE:
                $startTime = $field->getMinValue();
                if (empty($startTime)) {
                    $startTime = array();
                }
                $endTime = $field->getMaxValue();
                if (empty($endTime)) {
                    $endTime = array();
                }
                $id = $this->getId();
                $name = $this->getName();
                $this->setName($name . '[active]');
                $result = $this->buildBoolCheckbox(is_array($value) && isset($value['active']) && $value['active'] === "true");
                $this->setId($id . '_start')->setName($name . '[start]');
                $result .= $this->buildDateSelector($startTime) . '&nbsp;&ndash;&nbsp;';
                $this->setId($id . '_end')->setName($name . '[end]');
                $result .= $this->buildDateSelector($endTime);
                $this->setId($id);
                $this->setCssClass("gui_generator_date");
                return $this->buildSpan($result);
            case \Yana\Db\Ddl\ColumnTypeEnumeration::INT:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::RANGE:
                $name = $this->getName();
                $id = $this->getId();
                $this->setId($id . '_start')->setName($name . '[start]');
                $result = $this->buildTextfield($value) . '&nbsp;&le;&nbsp;';
                $this->setId($id . '_end')->setName($name . '[end]');
                $result .= $this->buildTextfield($value);
                $this->setId($id)->setName($name);
                return $result;
            default:
                if (!is_scalar($value)) {
                    $value = "";
                }
                return $this->buildTextfield(\htmlentities($value));
        }
    }

    /**
     * Create a reference link (where available).
     *
     * Returns the HTML-code for this field.
     *
     * @param   \Yana\Forms\Fields\IsField  $field  structure definition
     * @return  string
     * @ignore
     */
    protected function createLink(\Yana\Forms\Fields\IsField $field)
    {
        $result = "";
        if ($field->getField() instanceof \Yana\Db\Ddl\Field && count($field->getField()->getEvents()) > 0) {
            $value = $field->getValue();
            if (empty($value) && $value !== false) {
                return '';
            }
            $lang = \Yana\Translations\Facade::getInstance();
            $form = $field->getForm();
            $table = $form->getTable();
            $id = 'id="' . $form->getName() . '-' . $table->getPrimaryKey() . '-' .
                $form->getPrimaryKey() . '-' . $field->getName() . '"';
            $class = 'class="gui_generator_int_link"';
            $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
            foreach ($field->getField()->getEvents() as $event)
            {
                assert($event instanceof \Yana\Db\Ddl\Event);
                /* @var $event \Yana\Db\Ddl\Event */
                $label = $event->getLabel();
                $title = $event->getTitle();
                $icon = $event->getIcon();
                $href = "";

                switch (strtolower((string) $event->getLanguage()))
                {
                    case 'javascript':
                        assert(!isset($actionId), '!isset($actionId)');
                        $actionId = \Yana\Util\Strings::htmlSpecialChars((string) $event->getAction());
                        $href = 'href="javascript://" ' . $event->getName() . '="' . $actionId . '"';
                        unset($actionId);
                    break;
                    default:
                        $actionParam = "action=" . $event->getName();
                        $targetParam = "target[" . $table->getPrimaryKey() . "]=" . $form->getPrimaryKey() .
                            "&target[" . $field->getName() . "]=" . $value;
                        $href = 'href="' . $urlFormatter($actionParam . "&" . $targetParam) . '"';
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
     * @param   \Yana\Forms\Fields\IsField  $field  input field
     * @return  string
     * @ignore
     */
    protected function createJavascriptEvents(\Yana\Forms\Fields\IsField $field)
    {
        $eventsAsHtml = "";
        if ($field->getField()) {
            /* @var $event \Yana\Db\Ddl\Event */
            foreach ($field->getField()->getEvents() as $event)
            {
                assert($event instanceof \Yana\Db\Ddl\Event);
                /* @var $event \Yana\Db\Ddl\Event */
                if (strtolower((string) $event->getLanguage()) !== 'javascript') {
                    continue; // non-javascript - ignore!
                }
                if ($event->getLabel() || $event->getIcon() ) {
                    continue; // these are links - ignore!
                }
                $name = $event->getName();
                $code = \Yana\Util\Strings::htmlSpecialChars((string) $event->getAction());
                $eventsAsHtml .= "$name=\"$code\"";
            } // end foreach
        }
        return $eventsAsHtml;
    }

}

?>