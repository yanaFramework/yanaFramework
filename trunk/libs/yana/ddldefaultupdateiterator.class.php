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
 * update form iterator
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 * @ignore
 */
class DDLDefaultUpdateIterator extends DDLDefaultInsertIterator
{
    /**
     * rows taken from result set
     *
     * @access  protected
     * @var     array
     * @ignore
     */
    protected $rows = null;

    /**
     * current row index numer
     *
     * @access  private
     * @var     int
     * @ignore
     */
    private $row = 0;

    /**
     * create new instance
     *
     * @access  public
     * @param   DDLAbstractForm  $form  iterate over this form
     */
    public function __construct(DDLAbstractForm $form)
    {
        $this->form = $form;

        $fields = array();
        /* @var $field DDLDefaultField */
        foreach ($form->getFields() as $field)
        {
            // skip field which are not selectable
            if (!$field->isVisible()) {
                continue;
            }
            $fields[] = $field;
        } // end foreach
        $this->fields = $fields;
    }

    /**
     * next row
     *
     * Increment iterator to next row.
     *
     * @access  public
     */
    public function nextRow()
    {
        $this->row++;
        next($this->getRows());
    }

    /**
     * rewind row iterator
     *
     * @access  public
     */
    public function rewindRows()
    {
        $this->row = 0;
        reset($$this->getRows());
    }

    /**
     * get primary key of current row
     *
     * @access  public
     * @return  string
     */
    public function primaryKey()
    {
        return current($this->getRows());
    }

    /**
     * get the name of the primary key column
     *
     * @access  public
     * @return  string
     */
    public function primaryColumn()
    {
        return $this->form->getTableDefinition()->getPrimaryKey();
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
            return strtolower($this->primaryKey() . "." . $field->getName());
        } else {
            return "";
        }
    }

    /**
     * returns the number of rows
     *
     * @access  public
     * @return  string
     */
    public function getRowCount()
    {
        return count($this->getRows());
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
        return true;
    }

    /**
     * get current page number
     *
     * Returns the current page number as an integer.
     * The page number is the offset of the first visible entry in the table,
     * plus the offset of the entry in the table itself.
     *
     * The page number equals the offset parameter in a SQL-Select statement.
     *
     * @access  public
     * @return  int
     */
    public function getPage()
    {
        return $this->form->getPage() + $this->row;
    }

    /**
     * get name attribute of form element
     *
     * @access  protected
     * @return  string
     */
    protected function getName()
    {
        if ($this->valid()) {
            $field = $this->current();
            return $this->form->getName() . "[" . $this->getClass() . "][" . $this->primaryKey() .
                "][" . $field->getName() . "]";
        } else {
            return "";
        }
    }

    /**
     * get id attribute of form element
     *
     * @access  public
     * @return  string
     */
    public function getId()
    {
        if ($this->valid()) {
            $field = $this->current();
            return $this->form->getName() . "-rows-" . $this->primaryKey() . "-" . $field->getName();
        } else {
            return "";
        }
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
            case 'reference':
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
     * get list of row ids
     *
     * @access  protected
     * @return  &array
     * @ignore
     */
    protected function &getRows()
    {
        if (!isset($this->rows)) {
            $values = $this->getValues();
            $this->rows = array_keys($values);
        }
        return $this->rows;
    }

    /**
     * get current row
     *
     * Returns the current row as an array, or NULL if there is none.
     *
     * @access  protected
     * @return  array
     * @ignore
     */
    protected function currentRow()
    {
        $rows = $this->getValues();
        $key = strtolower($this->primaryKey());
        if (isset($rows[$key])) {
            return $rows[$key];
        } else {
            return null;
        }
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
            $values = parent::getValues();
            /*
             * get list of rows for parameter $values
             */
            $query = $this->form->getQuery();
            // make sure the user can't go further than the last page
            $lastPage = $this->form->getLastPage();
            if ($query->getOffset() >= $lastPage) {
                // reduce by 1, so the last page will contain at least 1 entry
                if ($lastPage > 0) {
                    $lastPage--;
                }
                $query->setOffset($lastPage);
            }
            unset($lastPage);
            try {

                $parentForm = $this->form->getParent();
                // copy foreign key from parent query
                if ($parentForm instanceof DDLAbstractForm) {

                    if ($parentForm->getTable() === $this->form->getTable()) {
                        $query->setRow($parentForm->getQuery()->getRow());
                        $this->form->setEntriesPerPage(1);
                    } else {
                        list($source, $target) = $this->form->getForeignKey();
                        $target = strtoupper($target);
                        $parentQuery = $this->form->getParent()->getQuery();
                        $results = $parentQuery->getResults();
                        if (count($results) === 1) {
                            $results = current($results);
                            if (isset($results[$target])) {
                                $query->setHaving(array($source, '=', $results[$target]));
                            }
                        }
                    }
                    unset($source, $target, $parentQuery, $results);
                }

                $this->values = $query->getResults();
                $this->values = Hashtable::changeCase($this->values, CASE_LOWER);
                if (!empty($this->values) && $query->getExpectedResult() === DbResultEnumeration::ROW) {
                        $id = $this->values[strtolower($this->primaryColumn())];
                        $this->values = array($id => $this->values);
                }

            } catch (Exception $e) {
                // this function must not throw an exception, instead move entry to logs
                Log::report($e->getMessage(), $e->getCode(), $e->getTraceAsString());
                $this->values = array();
            }
            unset($query);
            if (!empty($values)) {
                $this->values = Hashtable::merge($this->values, $values);
            }
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
            $key = $this->key();
            $values = $this->getValues();
            return Hashtable::get($values, $key);
        }
        return null;
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
    protected function getReferenceValue($fieldName, $value)
    {
        $referenceValues = $this->getReferenceValues($fieldName);
        if (isset($referenceValues[$value])) {
            return $referenceValues[$value];
        } else {
            return $value;
        }
    }

    /**
     * create HTML for current field
     *
     * Returns the HTML-code representing an input element for the current field.
     * If the field has an action attached to it, an clickable icon or text-link is created next to it.
     *
     * @access  public
     * @return  string
     *
     * @ignore
     */
    public function toString()
    {
        $field = $this->current();

        // field may be edited
        if ($field->isUpdatable() && $this->form->getUpdateAction()) {
            return parent::toString() . $this->createLink();
        }
        // field may not be changed
        return $this->toStringNonUpdatable() . $this->createLink();
    }

    /**
     * create HTML for non-updatable field
     *
     * Returns the HTML-code representing an input element for the current field.
     *
     * @access  protected
     * @return  string
     *
     * @ignore
     */
    protected function toStringNonUpdatable()
    {
        $field = $this->current();
        $column = $field->getColumnDefinition();
        $length = $column->getLength();

        $name = $this->getName();
        $lang = Language::getInstance();

        // retrieve search arguments
        $value = $this->getValue();
        if (empty($value) && $value !== false) {
            return '&ndash;';
        }
        global $YANA;
        if (isset($YANA)) {
            $dataDir = $YANA->getVar('DATADIR');
        } else {
            $dataDir = '';
        }

        // get javascript events
        assert('!isset($attr); // Cannot redeclare var $attr');
        $attr = $field->getEventsAsHTML() . ' id="' . $this->getId() . '"';

        /**
         * Switch by column's type
         */
        switch ($field->getType())
        {
            case 'array':
                return '<div' . $attr . ' class="gui_generator_array">' . SmartUtility::printUL1($value) .
                    '</div>';
            break;
            case 'bool':
                if ($value) {
                    $value = "true";
                } else {
                    $value = "false";
                }
                return '<span' . $attr . ' class="gui_generator_bool gui_generator_' . $value . '">' .
                    '<img alt="' . $value . '" src=\'' . $dataDir . 'boolean_' . $value . '.gif\'/></span>';
            break;
            case 'color':
                return '<span' . $attr . ' class="gui_generator_color" style="background-color: ' . $value . '">' .
                    $value . '</span>';
            break;
            case 'date':
                return '<span' . $attr . '>' . SmartUtility::date($value) . '</span>';
            break;
            case 'file':
                global $YANA;
                if (!$YANA->getSession()->checkPermission(null, $this->form->getDownloadAction())) {
                    return "&nbsp;";
                }
                $value = DbBlob::storeFilenameInSession($value);
                return '<span class="gui_generator_file_download">' .
                    '<a class="buttonize" ' . $attr . ' title="' . $lang->getVar('title_download') . '" href=' .
                    SmartUtility::href(
                        "action=" . $this->form->getDownloadAction() . "&target={$value}"
                     ) . '><span class="icon_download">&nbsp;</span></a></span>';
            break;
            case 'text':
                $value = SmartUtility::smilies(SmartUtility::embeddedTags($value));
            // fall through
            case 'html':
                if (mb_strlen($value) > 25) {
                    return '<div' . $attr . ' class="gui_generator_readonly_textarea">' . $value . '</div>';
                } else {
                    return '<div' . $attr . '>' . $value . '</div>';
                }
            break;
            case 'image':
                global $YANA;
                if (!$YANA->getSession()->checkPermission(null, $this->form->getDownloadAction())) {
                    return "&nbsp;";
                }
                $value = DbBlob::storeFilenameInSession($value);
                return '<div class="gui_generator_image">' .
                    '<a' . $attr . ' href=' .
                    SmartUtility::href(
                        "action=" . $this->form->getDownloadAction() . "&target={$value}&fullsize=true"
                    ) . '><img border="0" alt="" src=' .
                    SmartUtility::href(
                        "action=" . $this->form->getDownloadAction() . "&target={$value}"
                    ) . '/></a>' . '</div>';
            break;
            case 'enum':
            case 'set':
            case 'list':
                return '<div' . $attr . ' class="gui_generator_array">' . SmartUtility::printUL1($value, 2) .
                    '</div>';
            break;
            case 'password':
                // never show password
                return '&ndash;';
            break;
            case 'reference':
                $references = $this->getReferences();
                if (isset($references[$field->getName()])) {
                    $reference = $references[$field->getName()];
                    $label = strtolower($reference['label']);
                    $row = $this->currentRow();
                    if (isset($row[$label])) {
                        $value = $row[$label];
                    }
                }
                return '<span' . $attr . '>' . $value . '</span>';
            break;
            case 'time':
            case 'timestamp':
                return '<span' . $attr . '>' . SmartUtility::date($value) . '</span>';
            break;
            case 'url':
                $class = 'class="gui_generator_ext_link"';
                $onclick = 'onclick="return confirm(\'' . $lang->getVar('confirm_ext_link') . '\')"';
                $title = 'title="' . $lang->getVar('ext_link') . '"';
                $target = 'target="_blank"';
                $href = 'href="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"';
                if (mb_strlen($value) > 80) {
                    $text = mb_substr($value, 0, 76) . ' ...';
                } else {
                    $text = $value;
                }
                return "<a$attr $target $class $onclick $title $href>$text</a>$link";
            break;
            default:
                if (mb_strlen($value) > 80) {
                    return '<span' . $attr . '>' . mb_substr($value, 0, 76) . '&nbsp;...</span>';
                } else {
                    return '<span' . $attr . '>' . $value . '</span>';
                }
            break;
        }
    }

    /**
     * create a reference link (where available)
     *
     * Returns the HTML-code for this field.
     *
     * @access  protected
     * @return  string
     *
     * @ignore
     */
    protected function createLink()
    {
        $value = $this->getValue();
        if (empty($value) && $value !== false) {
            return '';
        }
        $lang = Language::getInstance();
        $field = $this->current();
        $column = $field->getColumnDefinition();
        $table = $this->form->getTableDefinition();
        $id = 'id="' . $this->form->getName() . '-' . $this->primaryColumn() . '-' .
            $this->primaryKey() . '-' . $field->getName() . '"';
        $class = 'class="gui_generator_int_link"';
        $result = "";
        /* @var $event DDLEvent */
        foreach ($field->getEvents() as $event)
        {
            $code = $event->getAction();
            $label = $event->getLabel();
            $title = $event->getTitle();
            $icon = $event->getIcon();

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
                    $targetParam = "target[" . $table->getPrimaryKey() . "]=" . $this->primaryKey() .
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
        return $result;
    }

    /**
     * flush cache when object is unserialized
     *
     * @access  public
     * @ignore
     */
    public function __wakeup()
    {
        $this->rows = null;
    }
}

?>