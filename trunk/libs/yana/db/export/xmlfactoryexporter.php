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

namespace Yana\Db\Export;

/**
 * <<utility>> Helper class to convert arrays to XML.
 *
 * This class creates database exports and backups from Yana FileDb.
 * The contents will be exported as XML file.
 *
 * This class is consumed by {@see \Yana\Db\Export\XmlFactory}.
 *
 * @package     yana
 * @subpackage  db
 * @ignore
 */
class XmlFactoryExporter extends \Yana\Core\AbstractUtility
{

    /**
     * Create XML.
     *
     * This static function will convert the content of a database to a xml string.
     *
     * Keys preceeded by a "@"-symbol will be converted to an id-attribute.
     *
     * The input should look something like this:
     * <code>
     * $input = array(
     *     'ft' => array(
     *         1 => array(
     *             'FTVALUE' => 1,
     *             'FTID' => 1,
     *             '@t' =>
     *             array(
     *                 'FOO' =>
     *                 array(
     *                     'TVALUE' => 1,
     *                     'TB' => true,
     *                     'FTID' => 1,
     *                     'TID' => 'FOO',
     *                 ),
     *             ),
     *         ),
     *     ),
     *     't' => array(
     *         'FOO' => array(
     *             'TVALUE' => 1,
     *             'TB' => true,
     *             'FTID' => 1,
     *             'TID' => 'FOO',
     *             '@i' => array(
     *                 'FOO' => array(
     *                     'TA' => array(0 => 'Test'),
     *                     'IID' => 'FOO',
     *                     'TVALUE' => 1,
     *                     'TB' => true,
     *                     'FTID' => 1,
     *                     'TID' => 'FOO',
     *                 ),
     *             ),
     *         ),
     *     ),
     *     'i' => array(
     *         'FOO' => array(
     *             'TA' => array(0 => 'Test'),
     *             'IID' => 'FOO',
     *             'TVALUE' => 1,
     *             'TB' => true,
     *             'FTID' => 1,
     *             'TID' => 'FOO',
     *         ),
     *     ),
     *     'u' => array(
     *         1 => array(
     *             'UID' => 1,
     *             'VALUE' => 'Abc'
     *         ),
     *         2 => array(
     *             'UID' => 2,
     *             'VALUE' => 'deF'
     *         ),
     *     )
     * );
     * </code>
     * 
     * Which will produce the following output:
     * <code>
     * <database>
     *     <table id="ft">
     *         <row id="1">
     *             <ftvalue>1</ftvalue>
     *             <ftid>1</ftid>
     *             <ft.t>
     *                 <row id="FOO">
     *                     <tvalue>1</tvalue>
     *                     <tb>true</tb>
     *                     <ftid>1</ftid>
     *                     <tid>FOO</tid>
     *                 </row>
     *             </ft.t>
     *         </row>
     *     </table>
     *     <table id="t">
     *         <row id="FOO">
     *             <tvalue>1</tvalue>
     *             <tb>true</tb>
     *             <ftid>1</ftid>
     *             <tid>FOO</tid>
     *             <t.i>
     *                 <row id="FOO">
     *                     <ta>
     *                         <string id="0">Test</string>
     *                     </ta>
     *                     <iid>FOO</iid>
     *                     <tvalue>1</tvalue>
     *                     <tb>true</tb>
     *                     <ftid>1</ftid>
     *                     <tid>FOO</tid>
     *                 </row>
     *             </t.i>
     *         </row>
     *     </table>
     *     <table id="i">
     *         <row id="FOO">
     *             <ta>
     *                 <string id="0">Test</string>
     *             </ta>
     *             <iid>FOO</iid>
     *             <tvalue>1</tvalue>
     *             <tb>true</tb>
     *             <ftid>1</ftid>
     *             <tid>FOO</tid>
     *         </row>
     *     </table>
     *     <table id="u">
     *         <row id="1">
     *             <value>Abc</value>
     *         </row>
     *         <row id="2">
     *             <value>deF</value>
     *         </row>
     *     </table>
     * </database>
     * </code>
     *
     * @param   array  $data  containing database contents
     * @return  string
     */
    public static function convertArrayToXml(array $data)
    {
        $xml = "<?xml version=\"1.0\"?>\n<database>\n"; // Create xml header
        foreach ($data as $tableName => $table)
        {
            if (is_array($table)) {
                $xml .= self::_handleTable($tableName, $table, 1);
            }
        }
        $xml .= "</database>";
        return $xml;
    }

    /**
     * Handle conversion of table data.
     *
     * @param   string  $tableName  name of input table
     * @param   array   $table      rows of input table
     * @param   int     $indent     number of tabs to indent
     */
    private static function _handleTable($tableName, array $table, $indent = 1)
    {
        assert('is_string($tableName); // Invalid argument type: $tableName. String expected.');
        assert('is_int($indent); // Invalid argument type: $indent. Integer expected.');

        /*
         * Create xml body.
         *
         * This applies to all following iterations only.
         */
        $tab = str_repeat("\t", $indent);

        $xml = "$tab<table id=\"" . \Yana\Util\Strings::htmlSpecialChars($tableName) . "\">\n";
        foreach ($table as $pKey => $row)
        {
            if (is_array($row)) {
                $xml .= static::_handleRow((string) $pKey, $row, $indent + 1);
            }
        }
        $xml .= "$tab</table>\n";

        return $xml;
    }

    /**
     * Handle conversion of row data.
     *
     * @param   string  $rowId   value of id attribute
     * @param   array   $row     tag content
     * @param   int     $indent  number of tabs to indent the line
     * @return  string
     */
    private static function _handleRow($rowId, array $row, $indent)
    {
        assert('is_string($rowId); // Invalid argument type: $rowId. String expected.');
        assert('is_int($indent); // Invalid argument type: $indent. Integer expected.');

        $tab = str_repeat("\t", $indent);

        $xml = "$tab<row id=\"" . \Yana\Util\Strings::htmlSpecialChars($rowId) . "\">\n";
        foreach ($row as $column => $value)
        {
            $column = mb_strtolower($column);
            $xml .= static::_handleColumn($column, $value, $indent + 1);
        }
        $xml .= "$tab</row>\n";
        return $xml;
    }

    /**
     * Handle conversion of column data.
     *
     * @param   string  $columnName  tag name
     * @param   mixed   $value       tag content
     * @param   int     $indent      number of tabs to indent the line
     * @return  string
     */
    private static function _handleColumn($columnName, $value, $indent)
    {
        assert('is_string($columnName); // Invalid argument type: $columnName. String expected.');
        assert('is_int($indent); // Invalid argument type: $indent. Integer expected.');

        $tab = str_repeat("\t", $indent);

        $tagName = mb_strtolower($columnName);
        if (is_bool($value)) {
            $xml = "$tab<$tagName>" . ( ($value) ? "true" : "false" ) . "</$tagName>\n";
        } elseif (is_array($value)) {
            if ($tagName[0] === '@') {
                $tagName = mb_substr($tagName, 1);
                $xml = self::_handleTable($tagName, $value, $indent);
            } else {
                $xml = "$tab<$tagName>\n";
                foreach ($value as $key => $item)
                {
                    $xml .= \Yana\Util\Hashtable::toXML($item, $key, CASE_MIXED, $indent + 1);
                }
                $xml .= "$tab</$tagName>\n";
            }
        } else {
            $xml = "$tab<$tagName>" . \Yana\Util\Strings::htmlSpecialChars((string) $value, ENT_NOQUOTES) . "</$tagName>\n";
        }
        return $xml;
    }

}

?>