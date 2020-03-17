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
declare(strict_types=1);

namespace Yana\Core\Exceptions\Forms;

/**
 * <<exception>> CSRF-token is invalid (or missing).
 *
 * The CSRF form token ensures that the form has been called by the browser before sending it's contents.
 * C.S.R.F = cross-site request forgery. This prevents 1) primitive spam-bots from auto-submitting posts
 * without ever viewing the forms and 2) tricking users into submitting a form action (like setting a new password)
 * by making them click a forged link (note: for that attack to be successful the user needs to be currently logged in).
 *
 * This exception is thrown when the token is either missing or invalid.
 *
 * @package     yana
 * @subpackage  core
 */
class InvalidTokenException extends \Yana\Core\Exceptions\Forms\FormException
{
    /* intentionally left blank */
}

?>