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

namespace Yana\Views;

/**
 * <<interace>> Use this to implement an entity representing a template.
 *
 * @package     yana
 * @subpackage  views
 */
interface IsTemplate
{

    /**
     * This function will fetch the current template and return it as a string.
     *
     * Predefined variables may be imported from the global registry
     * to the template.
     * Existing template vars will be replaced.
     *
     * @return  string
     */
    public function fetch();

    /**
     * Get template var.
     *
     * There are two ways to call this function:
     *
     * If you call $template->getVar($varName) it will get the
     * template var $varName and return it.
     *
     * If you call $template->getVar("*") with the wildcard '*'
     * or an empty string '' it will return an associative array
     * containing all template vars.
     *
     * @param   string  $key  variable-name
     * @return  mixed
     */
    public function getVar($key = '*');

    /**
     * Assign a variable to a key by value.
     *
     * Unlike Smarty's "assign()" this function takes an
     * additional value for $varName:
     *
     * You may use the wildcard '*' to merge an associative array with the template vars.
     * Example of usage: <code>$template->setVar('*', array  $var) </code>
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * {@internal
     *
     * The following synopsis: <code>$template->setVar('*', string $var)</code>
     * has been dropped as of version 2.9.2.
     *
     * }}
     *
     * @param   string  $varName  address
     * @param   mixed   $var      some new value
     * @return  bool
     */
    public function setVar($varName, $var);

    /**
     * Assign a variable to a key by reference.
     *
     * Unlike Smarty's "assign()" this function takes an
     * additional value for $varName:
     *
     * You may use the wildcard '*' to merge an associative array
     * with the template vars.
     *
     * Example of usage:
     * <code>$template->setVarByReference('*', array  $var) </code>
     *
     * {@internal
     *
     * The following synopsis:
     * <code>$template->setVarByReference('*', string $var)</code>
     * has been dropped as of version 2.9.2.
     *
     * }}
     *
     * @param   string  $varName  address
     * @param   mixed   &$var     some new value
     * @return  bool
     */
    public function setVarByReference($varName, &$var);

    /**
     * Set filename to fetch.
     *
     * Please note:
     * <ol>
     *   <li>  Template files may not have a reserved extension like
     *         "htaccess", "php", "config" or the like.
     *   </li>
     *   <li>  Files should be adressed from the root.
     *         This is where "index.php" is stored.
     *   </li>
     *   <li>  If you can't access a file, the file does not exist
     *         or is not readable, a template error is thrown.
     *   </li>
     *   <li>  Filenames are case-sensitive!  </li>
     * </ol>
     *
     * @param   string  $filename  name of the template file
     * @return  \Yana\Views\Template
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the filename is invalid
     */
    public function setPath($filename);

    /**
     * Returns a string with the path and name of the current template.
     *
     * @return  string
     */
    public function getPath();

}

?>