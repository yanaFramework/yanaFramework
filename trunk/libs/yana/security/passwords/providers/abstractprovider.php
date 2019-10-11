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

namespace Yana\Security\Passwords\Providers;

/**
 * Standard authentication provider to check passwords.
 *
 * @package     yana
 * @subpackage  security
 */
abstract class AbstractProvider extends \Yana\Core\StdObject implements \Yana\Security\Passwords\Providers\IsProvider
{

    /**
     * @var \Yana\Security\Data\Users\IsEntity
     */
    private $_user = null;

    /**
     * <<construct>> Initialize user entity.
     *
     * @param  \Yana\Security\Data\Users\IsEntity  $user  from database
     */
    public function __construct(\Yana\Security\Data\Users\IsEntity $user)
    {
        $this->_user = $user;
    }

    /**
     * @return \Yana\Security\Data\Users\IsEntity
     */
    protected function _getUser(): \Yana\Security\Data\Users\IsEntity
    {
        return $this->_user;
    }

}

?>