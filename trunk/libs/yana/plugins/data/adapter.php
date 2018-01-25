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
 * Plugin data-adapter.
 *
 * This persistent class provides access to the stored status of installed plugins.
 *
 * @package     yana
 * @subpackage  plugins
 */
class Adapter extends \Yana\Plugins\Data\AbstractAdapter
{

    /**
     * Returns the name of the target table.
     *
     * @return  string
     */
    protected function _getTableName()
    {
        return \Yana\Plugins\Data\Tables\PluginEnumeration::TABLE;
    }

    /**
     * Serializes the entity object to a table-row.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  object to convert
     * @return  array
     */
    protected function _serializeEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        return $this->_getEntityMapper()->toDatabaseRow($entity);
    }

    /**
     * Unserializes the table-row to an entity object.
     *
     * @param   array  $dataSet  table row to convert
     * @return  \Yana\Data\Adapters\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given data is invalid
     */
    protected function _unserializeEntity(array $dataSet)
    {
        $entity = $this->_getEntityMapper()->toEntity($dataSet);
        $entity->setDataAdapter($this);
        return $entity;
    }

    /**
     * Loads and returns an entry from the database.
     *
     * @param   string  $id  name of the plugin
     * @return  \Yana\Plugins\Data\IsEntity
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no such entry exists
     */
    public function offsetGet($id)
    {
        assert('is_string($id); // Wrong type argument $id. String expected.');

        try {
            return parent::offsetGet(\Yana\Util\Strings::toUpperCase((string) $id));

        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {

            $message = "No plugin found with id: " . \htmlentities((string) $id);
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\NotFoundException($message, $level, $e);
        }
    }

    /**
     * Write an entry to the database.
     *
     * @param  string                       $id      name of plugin (can be NULL)
     * @param  \Yana\Plugins\Data\IsEntity  $entity  the plugin status
     * @return \Yana\Plugins\Data\IsEntity
     * @throws \Yana\Core\Exceptions\InvalidArgumentException  when the entity is invalid
     * @throws \Yana\Db\DatabaseException                      when there was a problem with the database
     */
    public function offsetSet($id, $entity)
    {
        assert('is_string($id) || is_null($id); // Wrong type argument $id. String expected.');

        if (!($entity instanceof \Yana\Plugins\Data\IsEntity)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException('Instance of "\Yana\Plugins\Data\IsEntity" expected.');
        }

        if (!is_null($id)) {
            $id = \Yana\Util\Strings::toUpperCase((string) $id);
        }

        return parent::offsetSet($id, $entity);
    }

    /**
     * Returns collection of all plugin status stored in database.
     *
     * @return  \Yana\Plugins\Data\Collection
     */
    public function getAll()
    {
        $collection = new \Yana\Plugins\Data\Collection();
        foreach ($this->_getDatabaseConnection()->select($this->_getTableName()) as $row)
        {
            $collection[] = $this->_getEntityMapper()->toEntity($row);
        }
        return $collection;
    }

    /**
     * Return only those plugins that are active.
     *
     * @param   array  $plugins  list of identifiers
     * @return  array
     */
    public function filterActivePlugins(array $plugins)
    {
        assert('!isset($filteredPlugins); // Cannot redeclare var $filteredPlugins');
        $filteredPlugins = array();

        assert('!isset($pluginName); // Cannot redeclare var $pluginName');
        foreach ($plugins as $pluginName)
        {
            assert('is_string($pluginName); // Invalid argument $pluginName: string expected');
            if ($this->isActive($pluginName)) {
                $filteredPlugins[] = $pluginName;
            }
        }
        unset($pluginName);

        return $filteredPlugins;
    }

    /**
     * Check if plugin is active.
     *
     * Returns bool(true) if the plugin identified by $pluginName exists
     * and is active and bool(false) otherwise.
     *
     * @param   string  $pluginName  identifier for the plugin
     * @return  bool
     */
    public function isActive($pluginName)
    {
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');

        assert('!isset($query); // Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\SelectExist($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());
        $query->setRow($pluginName);
        $query->setWhere(array(\Yana\Plugins\Data\Tables\PluginEnumeration::IS_ACTIVE, '=', true));
        return $query->doesExist();
    }

    /**
     * Saves the plugin status to the database.
     *
     * @param  \Yana\Data\Adapters\IsEntity  $entity  object to persist
     * @throws \Yana\Core\Exceptions\InvalidArgumentException  when the entity is invalid
     * @throws \Yana\Db\DatabaseException                      when there was a problem with the database
     */
    public function saveEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        $this->offsetSet($entity->getId(), $entity);
    }

}

?>