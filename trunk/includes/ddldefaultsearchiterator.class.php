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
 * search form iterator
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 * @ignore
 */
class DDLDefaultSearchIterator extends DDLDefaultReportIterator
{
    /**
     * get minimal form value
     *
     * Returns a value (if any) associated with the 
     *
     * @access  public
     * @return  mixed
     */
    public function getMinValue()
    {
        $value = $this->getValue();
        if (is_array($value) && isset($value['start'])) {
            return $value['start'];
        } else {
            return null;
        }
    }

    /**
     * get maximal form value
     *
     * @access  public
     * @return  mixed
     */
    public function getMaxValue()
    {
        $value = $this->getValue();
        if (is_array($value) && isset($value['end'])) {
            return $value['end'];
        } else {
            return null;
        }
    }

    /**
     * get current value as where clause
     *
     * This function returns an array of (leftOperand, operator, rightOperand),
     * which may be used to set a where clause on a database query object.
     *
     * If the value is empty, the function return NULL instead.
     *
     * @access  public
     * @return  array
     */
    public function getValueAsWhereClause()
    {
        $value = $this->getValue();
        if (is_null($value) || $value === '') {
            return null;
        }
        $field = $this->current();
        if (!$field->refersToTable()) {
            return null;
        }
        $column = $field->getColumnDefinition();
        $leftOperand = array($this->form->getTable(), $column->getName());
        /**
         * Switch by column's type
         */
        switch ($field->getType())
        {
            case 'bool':
                switch ($value)
                {
                    case 'true':
                        $rightOperand = true;
                    break;
                    case 'false':
                        $rightOperand = false;
                    break;
                    default:
                        return null;
                    break;
                }
                $operator = '=';
            break;
            case 'enum':
            case 'set':
                if (!is_array($value)) {
                    return null;
                }
                $operator = 'IN';
                $validItems = $column->getEnumerationItemNames();
                // prevent use of invalid items (possible injection)
                $rightOperand = array_intersect($value, $validItems);
                if (!empty($rightOperand)) {
                    return null;
                }
                assert('is_array($rightOperand);');
            break;
            case 'time':
            case 'timestamp':
            case 'date':
                if (!isset($value['active']) || $value['active'] !== 'true') {
                    return null;
                }
                $operator = 'AND';
                $min = $this->getMinValue();
                $max = $this->getMaxValue();
                $minTime = mktime(0, 0, 0, $min['month'], $min['day'], $min['year']);
                $maxTime = mktime(23, 59, 59, $max['month'], $max['day'], $max['year']);
                $rightOperand = array($leftOperand, '<=', $maxTime);
                $leftOperand = array($leftOperand, '>=', $minTime);
            break;
            case 'integer':
            case 'float':
            case 'range':
                $min = $this->getMinValue();
                $max = $this->getMaxValue();
                if ($min != '') {
                    $rightOperand = $min;
                    if ($min === $max) {
                        $operator = '=';
                    } else {
                        $operator = '>=';
                    }
                } elseif ($max != '') {
                    $rightOperand = $max;
                    $operator = '<=';
                } else {
                    return null;
                }
            break;
            default:
                $operator = 'LIKE';
                $value = strtr($value, '*?', '%_'); // translate wildcards
                $value = String::htmlSpecialChars($value);
                $rightOperand = $value;
            break;
        }
        return array($leftOperand, $operator, $rightOperand);
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

        $key = $this->key();
        $name = $this->getName();
        $id = $this->key();
        $lang = Language::getInstance();

        // retrieve search arguments
        $value = $this->getValue();
        unset($key);
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
                return self::generateRadio($id, $name, "gui_generator_bool", $items, $value, "");
            break;
            case 'enum':
                $value = array($value);
            // fall through
            case 'set':
                if (empty($value)) {
                    $value = array();
                }
                assert('!isset($items); // Cannot redeclare var $items');
                $items = $column->getEnumerationItems();
                return self::generateCheckboxes($id, $name, "gui_generator_set", $items, $value);
            break;
            case 'time':
            case 'timestamp':
            case 'date':
                $startTime = $this->getMinValue();
                if (empty($startTime)) {
                    $startTime = array();
                }
                $endTime = $this->getMaxValue();
                if (empty($endTime)) {
                    $endTime = array();
                }
                return '<span id="' . $id . '" class="gui_generator_date">' .
                    '<label>' .
                    '<input' . (($value['active'] !== "true") ? ' checked="checked"' : '' ) .
                    ' class="gui_generator_radio" type="radio" value="false"' .
                    ' name="' . $name . '[active]"/>' .
                    $lang->getVar('any') .
                    '</label>' .
                    '<input' . (($value['active'] === "true") ? ' checked="checked"' : '' ) .
                    ' class="gui_generator_radio" type="radio" value="true"' .
                    ' name="' . $name . '[active]"/>' .
                    SmartUtility::selectDate(array(
                        'time' => $startTime,
                        'id' => "{$id}_start",
                        'name' => "{$name}[start]")
                    ) .
                    '&nbsp;&ndash;&nbsp;' .
                    SmartUtility::selectDate(array(
                        'time' => $endTime,
                        'id' => "{$id}_end",
                        'name' => "{$name}[end]")
                    ) .
                    '</span>';
            break;
            case 'integer':
            case 'float':
            case 'range':
                $isNumeric = true;
            // fall through
            default:
                $length = $column->getLength();
                $title = $field->getTitle();
                $title = String::htmlSpecialChars($title);
                $size = "";
                if ($length > 0) {
                    $size .= ' maxlength="' . $length . '"';
                    if ($length < 22) {
                        $size .= ' size="' . $length . '"';
                    }
                }
                $input = '<input name="' . $name . '%s" id="' . $id . '%s" type="text" value="%s"' .
                    $size . ' title="' . $field->getTitle() . '"/>';
                if (!empty($isNumeric)) {
                    return sprintf($input, '[start]', '', $this->getMinValue()) .
                        '&nbsp;&le;&nbsp;' . sprintf($input, '[end]', '-end', $this->getMaxValue());
                } else {
                    return sprintf($input, '', '', $value);
                }
            break;
        }
    }
}

?>