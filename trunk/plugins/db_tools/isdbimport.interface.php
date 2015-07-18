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
 * import class interface
 *
 * Minimal interface to implemen if you wish to write an import adapter for
 * structure files.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
interface IsDbImport
{
    /**
     * Return table info for current data
     *
     * @access  public
     * @param   string  $table  table name
     * @return  array
     */
    public function getTableInfo($table = null);

    /**
     * import schema to Yana structure files
     *
     * The argument $schema may either be a file name or XML file content.
     *
     * This function will import the database structure from the given file and
     * transform it into a compatible structure file, that can be used to create
     * and modify databases via the framework's database API.
     *
     * The function returns an instance of class DbStructure, or bool(false)
     * on error.
     *
     * @access  public
     * @static
     * @param   string   $schema    file name or XML file content
     * @return  DbStructure
     */
    public static function getStructureFromString($schema);

    /**
     * Return database structure for current data
     *
     * @access  public
     * @return  DbStructure
     */
    public function &getStructure();

}

?>