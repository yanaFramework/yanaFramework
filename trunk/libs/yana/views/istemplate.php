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
interface IsTemplate extends \Yana\Core\IsVarContainer
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

}

?>