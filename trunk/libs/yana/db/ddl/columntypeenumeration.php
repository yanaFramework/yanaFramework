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

namespace Yana\Db\Ddl;

/**
 * <<enumeration>> Pre-defined column types.
 *
 * @package     yana
 * @subpackage  db
 */
class ColumnTypeEnumeration extends \Yana\Core\AbstractEnumeration
{
    const INT = 'integer';
    const STRING = 'string';
    const FLOAT = 'float';
    const BOOL = 'bool';
    const RANGE = 'range';
    const ENUM = 'enum';
    const SET = 'set';
    const TIME = 'time';
    const TIMESTAMP = 'timestamp';
    const DATE = 'date';
    const REFERENCE = 'reference';
    const PASSWORD = 'password';
    const HTML = 'html';
    const IMAGE = 'image';
    const FILE = 'file';
    const COLOR = 'color';
    const LST = 'list';
    const TEXT = 'text';
    const ARR = 'array';
    const INET = 'inet';
    const MAIL = 'mail';
    const TELEPHONE = 'tel';
    const URL = 'url';


    /**
     * Get list of column types.
     *
     * Returns a list with all supported column types as a numeric array.
     *
     * @return  array
     */
    public static function getSupportedTypes()
    {
        return array(
            self::ARR,
            self::BOOL,
            self::COLOR,
            self::DATE,
            self::ENUM,
            self::FILE,
            self::FLOAT,
            self::HTML,
            self::IMAGE,
            self::LST,
            self::INET,
            self::INT,
            self::MAIL,
            self::PASSWORD,
            self::RANGE,
            self::REFERENCE,
            self::SET,
            self::STRING,
            self::TELEPHONE,
            self::TEXT,
            self::TIME,
            self::TIMESTAMP,
            self::URL);
    }

    /**
     * Is single-line.
     *
     * Returns bool(true) if the given type can be displayed using an input element,
     * which requires no more than a single line of text. Returns bool(false) otherwise.
     *
     * @param   string  $type  to compare
     * @return  bool
     */
    public static function isSingleLine($type)
    {
        assert(is_string($type), 'Invalid argument type $type: String expected');
        // filter by column type
        switch ($type)
        {
            case self::BOOL:
            case self::DATE:
            case self::ENUM:
            case self::FILE:
            case self::FLOAT: 
            case self::INET:
            case self::INT:
            case self::MAIL:
            case self::RANGE:
            case self::STRING:
            case self::TELEPHONE:
            case self::TIME:
            case self::TIMESTAMP:
            case self::URL:
            case self::REFERENCE:
                return true;

            default:
                return false;
        } // end switch
    }

    /**
     * Is multi-line.
     *
     * Returns bool(true) if the given type can be displayed using an input element,
     * which requires multiple lines. Returns bool(false) otherwise.
     *
     * @param   string  $type  to compare
     * @return  bool
     */
    public static function isMultiLine($type)
    {
        // filter by column type
        switch ($type)
        {
            case self::TEXT:
            case self::HTML:
            case self::IMAGE:
            case self::SET:
            case self::LST:
                return true;

            default:
                return false;
        } // end switch
    }

    /**
     * Check if type can be filtered using a string.
     *
     * Returns bool(true) if the type is supposed to be able to get filtered using a string and bool(false) if it is not.
     *
     * @return  bool
     */
    public static function isFilterable($type)
    {
        // filter by column type
        switch ($type)
        {
            case self::BOOL:
            case self::COLOR:
            case self::ENUM:
            case self::FLOAT:
            case self::INET:
            case self::INT:
            case self::MAIL:
            case self::RANGE:
            case self::STRING:
            case self::TELEPHONE:
            case self::TEXT:
            case self::HTML:
            case self::URL:
                return true;

            default:
                return false;
        } // end switch
    }

}

?>