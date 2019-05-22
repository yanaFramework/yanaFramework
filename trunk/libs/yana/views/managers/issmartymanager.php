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
 *
 * @ignore
 */

namespace Yana\Views\Managers;

/**
 * <<interface>> Manager class that explicitly wraps the Smarty template engine.
 *
 * @package     yana
 * @subpackage  views
 */
interface IsSmartyManager extends \Yana\Views\Managers\IsManager
{

    /**
     * Bypass manager class.
     *
     * This function is used to unbox the smarty instance inside the object.
     * It may be used to bypass the template class in cases where direct
     * access to the smarty template engine is necessary.
     *
     * @return  \Smarty
     */
    public function getSmarty();

}

?>