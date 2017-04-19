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
class Deprecated_Mapper extends \Yana\Core\Object implements \Yana\Data\Adapters\IsEntityMapper
{

    /**
     * Creates an entity based on a database row.
     *
     * @param   array  $databaseRow  row containing user info
     * @return  \Yana\Security\Data\SecurityLevels\Entity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given user has no name
     */
    public function toEntity(array $databaseRow)
    {
        $entity = new \Yana\Security\Data\SecurityLevels\Entity();

        if (isset($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::ID])) {
            $entity->setId((int) $databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::ID]);
        }
        if (isset($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER])) {
            $entity->setUserCreated((string) $databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER]);
        }
        if (isset($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::IS_PROXY])) {
            $entity->setUserProxyActive((bool) $databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::IS_PROXY]);
        }
        if (isset($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::LEVEL])) {
            $entity->setSecurityLevel((int) $databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::LEVEL]);
        }
        if (isset($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::PROFILE])) {
            $entity->setProfile((string) $databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::PROFILE]);
        }
        if (isset($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::USER])) {
            $entity->setUserId((string) $databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::USER]);
        }
        return $entity;
    }

    /**
     * Creates a database row based on an entity.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  entity containing user info
     * @return  array
     */
    public function toDatabaseRow(\Yana\Data\Adapters\IsEntity $entity)
    {
        assert('!isset($row); // Cannot redeclare var $row');
        $row = array();

        if ($entity instanceof \Yana\Security\Data\SecurityLevels\Entity) {

            if ($entity->getId() !== null) {
                $row[\Yana\Security\Data\Tables\LevelEnumeration::ID] = $entity->getId();
            }
            if ($entity->getUserCreated() !== null) {
                $row[\Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER] = $entity->getUserCreated();
            }
            if ($entity->getUserProxyActive() !== null) {
                $row[\Yana\Security\Data\Tables\LevelEnumeration::IS_PROXY] = $entity->getUserProxyActive();
            }
            if ($entity->getSecurityLevel() !== null) {
                $row[\Yana\Security\Data\Tables\LevelEnumeration::LEVEL] = $entity->getSecurityLevel();
            }
            if ($entity->getProfile() !== null) {
                $row[\Yana\Security\Data\Tables\LevelEnumeration::PROFILE] = $entity->getProfile();
            }
            if ($entity->getUserId() !== null) {
                $row[\Yana\Security\Data\Tables\LevelEnumeration::USER] = $entity->getUserId();
            }
        }
        return $row;
    }

}

?>