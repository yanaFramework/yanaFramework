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

namespace Yana\Plugins\Data;

/**
 * Entity mapper.
 *
 * Maps entities to database rows and vice versa.
 *
 * @package     yana
 * @subpackage  plugins
 */
class Mapper extends \Yana\Core\StdObject implements \Yana\Data\Adapters\IsEntityMapper
{

    /**
     * Creates an entity based on a database row.
     *
     * @param   array  $databaseRow  must be associative array containing row-names as keys and corresponding values of correct data-type
     * @return  \Yana\Plugins\Data\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given user has no name
     */
    public function toEntity(array $databaseRow)
    {
        assert('!isset($row); // Cannot redeclare var $row');
        $row = \array_change_key_case($databaseRow);
        if (!isset($row[\Yana\Plugins\Data\Tables\PluginEnumeration::ID])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Can't create entity without an ID.");
        }

        $entity = new \Yana\Plugins\Data\Entity();
        $entity->setId((string) $row[\Yana\Plugins\Data\Tables\PluginEnumeration::ID]);

        if (isset($row[\Yana\Plugins\Data\Tables\PluginEnumeration::IS_ACTIVE])) {
            $entity->setActive((bool) $row[\Yana\Plugins\Data\Tables\PluginEnumeration::IS_ACTIVE]);
        }
        return $entity;
    }

    /**
     * Creates a database row based on an entity.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  entity object
     * @return  array
     */
    public function toDatabaseRow(\Yana\Data\Adapters\IsEntity $entity)
    {
        assert('!isset($row); // Cannot redeclare var $row');
        $row = array();

        if ($entity instanceof \Yana\Plugins\Data\IsEntity) {

            $row = array(
                \Yana\Plugins\Data\Tables\PluginEnumeration::ID => $entity->getId(),
                \Yana\Plugins\Data\Tables\PluginEnumeration::IS_ACTIVE => $entity->isActive()
            );
        }
        return $row;
    }

}

?>