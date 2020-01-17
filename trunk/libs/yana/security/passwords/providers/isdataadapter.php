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
declare(strict_types=1);

namespace Yana\Security\Passwords\Providers;

/**
 * <<interface>> Authentication provider data-adapter.
 *
 * This class loads authentication provider settings from the databasse and maps them to an entity object.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsDataAdapter extends \Yana\Data\Adapters\IsDataBaseAdapter
{
    /**
     * Build a provider entity based on a given user name.
     * 
     * @param   string $userId  the name/id of the user
     * @return \Yana\Security\Passwords\Providers\IsEntity
     */
    public function getFromUserName(string $userId): \Yana\Security\Passwords\Providers\IsEntity;
}

?>