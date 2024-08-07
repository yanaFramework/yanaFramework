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

namespace Yana\Core\Exceptions\User;

/**
 * <<exception>> Security management issue.
 *
 * Thrown when trying to grant a permission that cannot be granted to another user.
 *
 * @package     yana
 * @subpackage  core
 */
class NotGrantableException extends \Yana\Core\Exceptions\User\UserException
{

    /**
     * Create a new instance.
     *
     * @param  string      $message   the message that should be reported
     * @param  int         $code      optional error code
     * @param  \Exception  $previous  use this when you need to rethrow a catched exception
     */
    public function __construct($message = "", $code = \Yana\Log\TypeEnumeration::ERROR, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}

?>
