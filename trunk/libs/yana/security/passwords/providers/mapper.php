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

namespace Yana\Security\Passwords\Providers;

/**
 * Entity mapper.
 *
 * Maps entities to database rows and vice versa.
 *
 * @package     yana
 * @subpackage  security
 */
class Mapper extends \Yana\Core\StdObject implements \Yana\Data\Adapters\IsEntityMapper
{

    /**
     * Creates an authentication provider entity based on a database row.
     *
     * @param   array  $databaseRow  row containing provider info
     * @return  \Yana\Security\Passwords\Providers\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given data is invalid
     */
    public function toEntity(array $databaseRow)
    {
        $databaseRowLower = \Yana\Util\Hashtable::changeCase($databaseRow, \CASE_LOWER);
        $entity = new \Yana\Security\Passwords\Providers\Entity();
        if (!isset($databaseRowLower[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::ID])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Not a valid database entry");
        }
        $entity->setId((string) $databaseRowLower[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::ID]);
        if (isset($databaseRowLower[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::METHOD])) {
            $entity->setMethod((string) $databaseRowLower[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::METHOD]);
        }
        if (isset($databaseRowLower[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::NAME])) {
            $entity->setName((string) $databaseRowLower[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::NAME]);
        }
        if (isset($databaseRowLower[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::HOST])) {
            $entity->setHost((string) $databaseRowLower[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::HOST]);
        }
        return $entity;
    }

    /**
     * Creates a database row based on an entity.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  entity containing authentication provider setup
     * @return  array
     */
    public function toDatabaseRow(\Yana\Data\Adapters\IsEntity $entity)
    {
        assert(!isset($row), 'Cannot redeclare var $row');
        $row = array();

        if ($entity instanceof \Yana\Security\Passwords\Providers\IsEntity) {

            if ($entity->getId() !== null) {
                $row[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::ID] = $entity->getId();
            }
            if ($entity->getName() !== null) {
                $row[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::NAME] = $entity->getName();
            }
            if ($entity->getHost() !== null) {
                $row[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::HOST] = $entity->getHost();
            }
            if ($entity->getMethod() !== null) {
                $row[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::METHOD] = $entity->getMethod();
            }
        }
        return $row;
    }

}

?>