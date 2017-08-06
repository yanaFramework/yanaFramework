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

namespace Yana\Security\Data\SecurityLevels;

/**
 * <<interface>> Security level rule data-adapter.
 *
 * Provides access to security data.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsDataAdapter extends \Yana\Data\Adapters\IsDataBaseAdapter
{

    /**
     * Get security level.
     *
     * Returns the user's security level as an integer value.
     * The default is 0.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  \Yana\Security\Data\SecurityLevels\IsLevel
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching level is found
     */
    public function findEntityOwnedByUser($userId, $profileId);

    /**
     * Get security levels.
     *
     * Returns all the user's security level as a collection.
     *
     * @param   string  $userId  user name
     * @return  \Yana\Security\Data\SecurityLevels\IsCollection
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching level is found
     */
    public function findEntitiesOwnedByUser($userId);

    /**
     * Get security levels the user created but does not own.
     *
     * Returns all entries this user granted to other users.
     *
     * @param   string  $userId     user name
     * @param   string  $profileId  profile id
     * @return  \Yana\Security\Data\SecurityLevels\IsCollection
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no matching rule is found
     */
    public function findEntitiesGrantedByUser($userId, $profileId = "");

}

?>