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
class Mapper extends \Yana\Core\Object implements \Yana\Security\Data\SecurityLevels\IsMapper
{

    /**
     * Creates an entity based on a database row.
     *
     * @param   array  $databaseRow  row containing user info
     * @return  \Yana\Security\Data\SecurityLevels\IsLevelEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given user has no name
     */
    public function toEntity(array $databaseRow)
    {
        $databaseRowLower = \Yana\Util\Hashtable::changeCase($databaseRow, \CASE_LOWER);
        assert('!isset($level); // Cannot redeclare var $level');
        $level = 0;
        if (isset($databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::LEVEL])) {
            $level = (int) $databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::LEVEL];
        }
        assert('!isset($isProxy); // Cannot redeclare var $isProxy');
        $isProxy = false; // when the database value is NULL, it must be mapped to false
        if (isset($databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::HAS_GRANT_OPTION])) {
            $isProxy = (bool) $databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::HAS_GRANT_OPTION];
        }

        assert('!isset($entity); // Cannot redeclare var $entity');
        $entity = new \Yana\Security\Data\SecurityLevels\Level($level, $isProxy);

        if (isset($databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::ID])) {
            $entity->setId((int) $databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::ID]);
        }
        if (isset($databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER])) {
            $entity->setGrantedByUser((string) $databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER]);
        }
        if (isset($databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::PROFILE])) {
            $entity->setProfile((string) $databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::PROFILE]);
        }
        if (isset($databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::USER])) {
            $entity->setUserName((string) $databaseRowLower[\Yana\Security\Data\Tables\LevelEnumeration::USER]);
        }
        return $entity;
    }

    /**
     * Creates a database row based on an entity.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  entity containing the information you wish to map
     * @return  array
     */
    public function toDatabaseRow(\Yana\Data\Adapters\IsEntity $entity)
    {
        assert('!isset($row); // Cannot redeclare var $row');
        $row = array();
        if ($entity->getId() >= 0) {
            // We will not add the ID when none has been set (to allow AUTO-INCREMENT to do its job)
            $row[\Yana\Security\Data\Tables\LevelEnumeration::ID] = $entity->getId();
        }
        if ($entity instanceof \Yana\Security\Data\SecurityLevels\IsLevelEntity) {
            $row[\Yana\Security\Data\Tables\LevelEnumeration::LEVEL] = $entity->getSecurityLevel();
            $row[\Yana\Security\Data\Tables\LevelEnumeration::HAS_GRANT_OPTION] = $entity->isUserProxyActive();
            if ($entity->getProfile() > "") {
                $row[\Yana\Security\Data\Tables\LevelEnumeration::PROFILE] = $entity->getProfile();
            }
            if ($entity->getGrantedByUser() > "") {
                $row[\Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER] = $entity->getGrantedByUser();
            }
            if ($entity->getUserName() > "") {
                $row[\Yana\Security\Data\Tables\LevelEnumeration::USER] = $entity->getUserName();
            }
        }
        return $row;
    }

}

?>