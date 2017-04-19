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
 * User manager.
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Mapper extends \Yana\Core\Object implements \Yana\Data\Adapters\IsEntityMapper
{

    /**
     * Creates an entity based on a database row.
     *
     * @param   array  $databaseRow  row containing user info
     * @return  \Yana\Security\Data\SecurityLevels\Level
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given user has no name
     */
    public function toEntity(array $databaseRow)
    {
        assert('!isset($level); // Cannot redeclare var $level');
        $level = 0;
        if (isset($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::LEVEL])) {
            $level = (int) $databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::LEVEL];
        }
        assert('!isset($isProxy); // Cannot redeclare var $isProxy');
        $isProxy = true;
        if (isset($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::IS_PROXY])) {
            $isProxy = (bool) $databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::IS_PROXY];
        }

        return new \Yana\Security\Data\SecurityLevels\Level($level, $isProxy);
    }

    /**
     * Creates a database row based on an entity.
     *
     * @param   \Yana\Security\Data\SecurityLevels\Level  $entity  entity containing user info
     * @return  array
     */
    public function toDatabaseRow(\Yana\Data\Adapters\IsEntity $entity)
    {
        assert('!isset($row); // Cannot redeclare var $row');
        $row = array();

        if ($entity instanceof \Yana\Security\Data\SecurityLevels\Level) {

            $row[\Yana\Security\Data\Tables\LevelEnumeration::IS_PROXY] = $entity->isUserProxyActive();
            $row[\Yana\Security\Data\Tables\LevelEnumeration::LEVEL] = $entity->getSecurityLevel();
        }
        return $row;
    }

}

?>