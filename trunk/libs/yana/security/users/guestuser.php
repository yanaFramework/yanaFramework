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

namespace Yana\Security\Users;

/**
 * <<entity>> A guest-user.
 *
 * An empty user that has not logged in.
 * You may use this class to register new users.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class GuestUser extends \Yana\Security\Users\Entity
{

    /**
     * Creates an user by name.
     *
     * @param  string  $userName  current user name
     */
    public function __construct($userName = "")
    {
        assert('is_string($userName); // Wrong type for argument 1. String expected');

        parent::__construct('');
    }

    /**
     * Check if user is logged in.
     *
     * For guest-users this always returns false.
     *
     * @return  bool
     */
    public function isLoggedIn()
    {
        return false;
    }

}

?>