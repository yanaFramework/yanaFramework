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

namespace Yana\Db\Sources;

/**
 * Entity mapper.
 *
 * Maps entities to database rows and vice versa.
 *
 * @package     yana
 * @subpackage  db
 */
class Mapper extends \Yana\Core\StdObject implements \Yana\Data\Adapters\IsEntityMapper
{

    /**
     * Creates an authentication provider entity based on a database row.
     *
     * @param   array  $databaseRow  row containing provider info
     * @return  \Yana\Db\Sources\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given data is invalid
     */
    public function toEntity(array $databaseRow)
    {
        $databaseRowLower = \Yana\Util\Hashtable::changeCase($databaseRow, \CASE_LOWER);
        $entity = new \Yana\Db\Sources\Entity();
        if (!isset($databaseRowLower[\Yana\Db\Sources\TableEnumeration::ID])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Not a valid database entry");
        }
        $entity->setId((string) $databaseRowLower[\Yana\Db\Sources\TableEnumeration::ID]);
        if (isset($databaseRowLower[\Yana\Db\Sources\TableEnumeration::NAME])) {
            $entity->setName((string) $databaseRowLower[\Yana\Db\Sources\TableEnumeration::NAME]);
        }
        if (isset($databaseRowLower[\Yana\Db\Sources\TableEnumeration::DATABASE])) {
            $entity->setDatabase((string) $databaseRowLower[\Yana\Db\Sources\TableEnumeration::DATABASE]);
        }
        if (isset($databaseRowLower[\Yana\Db\Sources\TableEnumeration::DBMS])) {
            $entity->setDbms((string) $databaseRowLower[\Yana\Db\Sources\TableEnumeration::DBMS]);
        }
        if (isset($databaseRowLower[\Yana\Db\Sources\TableEnumeration::HOST])) {
            $entity->setHost((string) $databaseRowLower[\Yana\Db\Sources\TableEnumeration::HOST]);
        }
        if (isset($databaseRowLower[\Yana\Db\Sources\TableEnumeration::PASSWORD])) {
            $entity->setPassword((string) $databaseRowLower[\Yana\Db\Sources\TableEnumeration::PASSWORD]);
        }
        if (!empty($databaseRowLower[\Yana\Db\Sources\TableEnumeration::PORT])) {
            $entity->setPort((int) $databaseRowLower[\Yana\Db\Sources\TableEnumeration::PORT]);
        }
        if (isset($databaseRowLower[\Yana\Db\Sources\TableEnumeration::USER])) {
            $entity->setUser((string) $databaseRowLower[\Yana\Db\Sources\TableEnumeration::USER]);
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

        if ($entity instanceof \Yana\Db\Sources\IsEntity) {

            if ($entity->getId() !== null) {
                $row[\Yana\Db\Sources\TableEnumeration::ID] = $entity->getId();
            }
            if ($entity->getName() !== null) {
                $row[\Yana\Db\Sources\TableEnumeration::NAME] = $entity->getName();
            }
            if ($entity->getDbms() !== null) {
                $row[\Yana\Db\Sources\TableEnumeration::DBMS] = $entity->getDbms();
            }
            if ($entity->getPassword() !== null) {
                $row[\Yana\Db\Sources\TableEnumeration::PASSWORD] = $entity->getPassword();
            }
            if ($entity->getPort() !== null) {
                $row[\Yana\Db\Sources\TableEnumeration::PORT] = (int) $entity->getPort();
            }
            if ($entity->getHost() !== null) {
                $row[\Yana\Db\Sources\TableEnumeration::HOST] = $entity->getHost();
            }
            if ($entity->getUser() !== null) {
                $row[\Yana\Db\Sources\TableEnumeration::USER] = $entity->getUser();
            }
            if ($entity->getDatabase() !== null) {
                $row[\Yana\Db\Sources\TableEnumeration::DATABASE] = $entity->getDatabase();
            }
        }
        return $row;
    }

}

?>