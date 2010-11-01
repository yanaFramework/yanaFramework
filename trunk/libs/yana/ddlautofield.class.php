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
 * auto-generated database form field structure
 *
 * This is an automatically generated field, that is produced when the {@see DDLDefaultForm}
 * is initialized with the attribute hasAllInput.
 *
 * This field must not be serialized to XML.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLAutoField extends DDLDefaultField
{
    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * This tag is empty, so the element is ignored when the parent object is serialzed to XML.
     *
     * @access  protected
     * @var  string
     * @ignore
     */
    protected $xddlTag = null;

    /**
     * check whether the dbo has read-only access
     *
     * Returns bool(true) if the field OR the column are set to read-only and
     * bool(false) otherwise.
     *
     * The default is bool(false).
     *
     * @access  public
     * @return  bool
     */
    public function isReadonly()
    {
        $isReadonly = parent::isReadonly();
        return $isReadonly || $this->getColumnDefinition()->isReadonly();
    }
}

?>