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
 * report form iterator
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 * @ignore
 */
class DDLDefaultReportIterator extends DDLAbstractFieldIterator
{
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
            if (!$field->isVisible() || !$field->isSelectable()) {
                continue;
            }
            // filter fields by column type
            switch ($field->getType())
            {
                case 'bool':
                case 'date':
                case 'enum':
                case 'float':
                case 'html':
                case 'inet':
                case 'integer':
                case 'list':
                case 'mail':
                case 'range':
                case 'set':
                case 'string':
                case 'text':
                case 'time':
                case 'timestamp':
                case 'url':
                    $fields[] = $field;
                break;
                default:
                    continue;
                break;
            } // end switch
        } // end foreach
        $this->fields = $fields;
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
        $name = $this->getName();
        $id = $this->getId();
        $value = $this->getValue(); // retrieve search arguments

        /* Note:
         * Adding a hidden field with value = "0" prior to a checkbox will ensure the form always returns a value.
         * "1" = checked, "0" = not checked.
         */
        return '<input type="hidden" name="' . $name . '" value="0"/>' . // add a default value
            '<input id="' . $id . '" class="gui_generator_check" type="checkbox" ' .
            'name="' . $name . '" value="1" ' . ((!empty($value)) ? 'checked="checked"' : '') . '/>';
    }
}

?>