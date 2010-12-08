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
 * insert form iterator
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 * @ignore
 */
class DDLDefaultInsertIterator extends DDLAbstractFieldIterator
{
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
    private $referenceValues = null;

    /**
     * initialize instance
     *
     * @access  public
     * @ignore
     */
    public function __wakeup()
    {
        $this->referenceValues = null;
        $this->references = null;
    }

    /**
     * create new instance
     *
     * @access  public
     * @param   DDLAbstractForm  $form  iterate over this form
     */
    public function __construct(DDLAbstractForm $form)
    {
        parent::__construct($form);
        $fields = array();
        /* @var $field DDLDefaultField */
        foreach ($this->fields as $field)
        {
            // skip field which are not selectable
            if (!$field->isVisible() || !$field->isInsertable()) {
                continue;
            }
            $fields[] = $field;
        } // end foreach
        $this->fields = $fields;
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
            foreach ($this->fields as $field)
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
     */
    protected function getReferenceValues($fieldName)
    {
        if (!isset($this->referenceValues[$fieldName])) {
            $this->referenceValues[$fieldName] = array();
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
                $this->referenceValues[$fieldName] = $values;
            }
        }
        return $this->referenceValues[$fieldName];
    }

    /**
     * create HTML for current field
     *
     * Returns the HTML-code representing an input element for the current field.
     *
     * @access  public
     * @return  string
     *
     * @ignore
     */
    public function toString()
    {
        $field = $this->current();
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
                /* template for new entries */
                $template = '<input' . $attr . ' size="5" type="text" name="' . $name . '[names][]" value="%s"/>' .
                    '&nbsp;=&nbsp;<input size="10" type="text" name="' . $name . '[values][]" value="%s"/>' .
                    '<a class="buttonize" href="javascript://yanaRemoveItem(this)" ' .
                    'onclick="yanaRemoveItem(this)" title="'. $lang->getVar('remove') . '">' .
                    '<span class="icon_delete">&nbsp;</span></a>' .
                    '<a class="buttonize" href="javascript://yanaAddItem(this)" onclick="yanaAddItem(this)" ' .
                    'title="' . $lang->getVar('button_new') . '">' .
                    '<span class="icon_new">&nbsp;</span></a>';
            // fall through

            /*
             * a list is an array, where all keys are of type integer
             */
            case 'list':
                /* template for new entries */
                if (!isset($template)) {
                    $template = '<input' . $attr . ' size="21" type="text" name="' . $name .'[%i]" value="%s"/>' .
                        '<a class="buttonize" href="javascript://yanaRemoveItem(this)" ' .
                        'onclick="yanaRemoveItem(this)" title="'. $lang->getVar('remove') . '">' .
                        '<span class="icon_delete">&nbsp;</span></a>' .
                        '<a class="buttonize" href="javascript://yanaAddItem(this)" onclick="yanaAddItem(this)" ' .
                        'title="' . $lang->getVar('button_new') . '">' .
                        '<span class="icon_new">&nbsp;</span></a>';
                }

                $result = '<div class="gui_generator_array">';

                /* list of entries*/
                $result .= '<ol>';

                if (!empty($value)) {
                    $template = '<li>' . $template . '</li>';
                    ksort($value);
                    foreach ($value as $key => $text)
                    {
                        $result .= sprintf($template, $key, $text);
                    }
                } else {
                    $result .= '<li>' . sprintf($template, '', '') . '</li>';
                }

                // link to add new entry
                $result .= '</ol></div>';

                return $result;
            break;
            case 'bool':
                return '<input' . $attr . ' id="' . $id . '" name="' . $name . '" ' .
                    'type="checkbox" value="true" ' . ( ($value) ? 'checked="checked"' : '' ) . '/>';
            break;
            case 'color':
                return '<input' . $attr . ' id="' . $id . '" name="' . $name . '" ' .
                    'type="text" value="' . $value . '"/>' . SmartUtility::colorpicker(array('id' => $id));
            break;
            case 'enum':
                $items = $column->getEnumerationItems();
                if ($column->isNullable()) {
                    $null = $lang->getVar('choose_option');
                } else {
                    $null = "";
                }
                return self::generateSelect($id, $name, "gui_generator_set", $items, $value, $null, $attr);
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
                if ($column->isNullable()) {
                    $null = $lang->getVar('choose_option');
                } else {
                    $null = "";
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