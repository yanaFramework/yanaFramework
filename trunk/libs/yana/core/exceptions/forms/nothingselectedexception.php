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

namespace Yana\Core\Exceptions\Forms;

/**
 * <<exception>> No entry was selected.
 *
 * If an action operates on a list of entries and the client has to select which;
 * This exception is thrown, when the client selected none of the options, submits the form anyway
 * and there is nothing to do for the action called.
 *
 * @package     yana
 * @subpackage  core
 */
class NothingSelectedException extends \Yana\Core\Exceptions\Forms\FieldException
{
    /* intentionally left blank */
}

?>