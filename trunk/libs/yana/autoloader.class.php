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
 * <<utility>> Automatic class loader
 *
 * To use this
 *
 * @static
 * @access      public
 * @name        Index
 * @package     yana
 * @subpackage  core
 */
class AutoLoader extends Utility
{

    /**
     * YANA auto-loader
     *
     * This static function implements lazy class-loading for the Yana framework.
     * It is registered using the Standard PHP-library (SPL) function spl_autoload_register().
     *
     * For more information see the manual pages.
     *
     * @static
     * @access  public
     * @param   string  $className  class name
     * @link    http://de.php.net/manual/en/language.oop5.autoload.php
     * @link    http://de.php.net/manual/de/function.spl-autoload-register.php
     */
    public static function autoload($className)
    {
        $className = strtolower($className);
        switch ($className)
        {
            case 'smarty':
                $path = '/../smarty/Smarty.class.php';
                break;
            break;
            case 'sql_parser':
                $path = '/../sql_parser/Parser.php';
                break;
            break;
            case 'mdb2':
                include_once "MDB2.php";
                return;
            break;
            default:
                $path = str_replace(array('_', '\\'), '/', $className);
                $path .= '.class.php';
            break;
        }
        $dir = dirname(__FILE__) . '/';
        if (file_exists($dir . $path)) {
            include_once $dir . $path;
        }
    }

}

?>