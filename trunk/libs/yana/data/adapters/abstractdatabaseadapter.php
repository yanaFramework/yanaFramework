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

namespace Yana\Data\Adapters;

/**
 * <<adapter>> data adapter
 *
 * Database adapter, that stores and restores the given object from a database connection.
 *
 * @package     yana
 * @subpackage  io
 */
abstract class AbstractDatabaseAdapter extends \Yana\Core\Object implements \Yana\Data\Adapters\IsDataBaseAdapter
{

    /**
     * database connection
     *
     * @var  \Yana\Db\IsConnection
     */
    private $_databaseConnection = null;

    /**
     * constructor
     *
     * @param   \Yana\Db\IsConnection $db
     */
    public function __construct(\Yana\Db\IsConnection $db)
    {
        $this->_databaseConnection = $db;
    }

    /**
     * Returns the name of the target table.
     *
     * @return  string
     */
    abstract protected function _getTableName();

    /**
     * Returns the current open connection to the database.
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getDatabaseConnection()
    {
        return $this->_databaseConnection;
    }

    /**
     * Serializes the entity object to a table-row.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  object to convert
     * @return  array
     */
    abstract protected function _serializeEntity(\Yana\Data\Adapters\IsEntity $entity);

    /**
     * Unserializes the table-row to an entity object.
     *
     * @param   array  $dataSet  table row to convert
     * @return  \Yana\Data\Adapters\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given data is invalid
     */
    abstract protected function _unserializeEntity(array $dataSet);

    /**
     * Return the number of items in the table.
     *
     * If the collection is empty, it returns 0.
     *
     * @return  int
     */
    public function count()
    {
        assert('!isset($query); // Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\SelectCount($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());
        return $query->countResults();
    }

    /**
     * Check if item exists.
     *
     * Example:
     * <code>
     * $bool = isset($collection[$offset]);
     * $bool = $collection->offsetExists($offset);
     * </code>
     *
     * @param   scalar  $offset  index of item to test
     * @return  bool
     */
    public function offsetExists($offset)
    {
        assert('!isset($query); // Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\SelectExist($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());
        $query->setRow($offset);
        return $query->doesExist();
    }

    /**
     * Return item at offset.
     *
     * Example:
     * <code>
     * $item = $collection[$offset];
     * $item = $collection->offsetGet($offset);
     * </code>
     *
     * @param   scalar  $offset  index of item to retrieve
     * @return  \Yana\Data\Adapters\IsEntity
     */
    public function offsetGet($offset)
    {
        assert('!isset($query); // Cannot redeclare var $query');
        $query = new \Yana\Db\Queries\Select($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());
        $query->setRow($offset);
        assert('!isset($dataSet); // Cannot redeclare var $dataSet');
        $dataSet = $query->getResults();
        assert('!isset($entity); // Cannot redeclare var $entity');
        $entity = $this->_unserializeEntity($dataSet);
        return $entity;
    }

    /**
     * Insert or replace item.
     *
     * Implement this function in your sub-class as follows:
     * <code>
     * if ($yourTypeCheckHere) {
     *     $this->_offsetSet($offset, $item);
     * } else {
     *     throw new \Yana\Core\Exceptions\InvalidArgumentException();
     * }
     * </code>
     * 
     *
     * Example:
     * <code>
     * $collection[$offset] = $item;
     * parent::_offsetSet($offset, $item);
     * </code>
     *
     * The method returns the used entity to allow chained assignments (like this: $a = $b[1] = $c).
     *
     * @param   scalar                      $offset  index of item to replace
     * @param   \Yana\Data\Adapters\IsEntity  $entity  save this entity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid collection item
     * @throws  \Yana\Db\DatabaseException                      if the commit statement failed
     * @return  \Yana\Data\Adapters\IsEntity
     */
    public function offsetSet($offset, $entity)
    {
        if (!$entity instanceof \Yana\Data\Adapters\IsEntity) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException('Instance of "IsEntity" expected.');
        }

        if (is_null($offset)) {
            $offset = $entity->getId();
        }

        try {
            if ($offset && $this->offsetExists($offset)) {
                $this->_onUpdate($entity, $offset);
            } else {
                $this->_onInsert($entity, $offset);
            }
            $this->_getDatabaseConnection()->commit(); // may throw exception

        } catch (\Exception $e) {
            $this->_getDatabaseConnection()->rollback(); // if it failed, don't try it again
            throw $e; // rethrow exception, so that it can be caught upstream
        }
        return $entity;
    }

    /**
     * Selects whether the optional ID or the entity ID will be taken.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity      holds the ID
     * @param   scalar                        $optionalId  can be null
     * @return  scalar
     */
    private function _selectId(\Yana\Data\Adapters\IsEntity $entity, $optionalId)
    {
        assert('!isset($id); // Cannot redeclare var $id');
        $id = $optionalId;
        if (is_null($id) || !is_scalar($id)) {
            $id = $entity->getId();
        }
        return $id;
    }

    /**
     * Triggered when offsetSet() is called and the offset doesn't exists.
     *
     * Returns the Id used.
     * Note! This doesn't commit the query!
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity      object to be stored
     * @param   scalar                        $optionalId  primary key
     * @return  scalar
     */
    protected function _onInsert(\Yana\Data\Adapters\IsEntity $entity, $optionalId = null)
    {
        assert('!isset($id); // Cannot redeclare var $id');
        $id = $this->_selectId($entity, $optionalId);

        $query = new \Yana\Db\Queries\Insert($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());

        if ((int) $id > 0) {
            $query->setRow($id); // only set Id if it is given, so that auto-increment can still be used
        }

        $query->setTable($this->_getTableName());
        $values = $this->_serializeEntity($entity);
        $query->setValues($values);
        $query->sendQuery();
        return $id;
    }

    /**
     * Triggered when offsetSet() is called and the offset already exists.
     *
     * Returns the Id used.
     * Note! This doesn't commit the query!
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity      object to be stored
     * @param   scalar                        $optionalId  primary key
     * @return  scalar
     */
    protected function _onUpdate(\Yana\Data\Adapters\IsEntity $entity, $optionalId = null)
    {
        assert('!isset($id); // Cannot redeclare var $id');
        $id = $this->_selectId($entity, $optionalId);

        $query = new \Yana\Db\Queries\Update($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());
        $query->setRow($id);
        $query->setTable($this->_getTableName());
        $values = $this->_serializeEntity($entity);
        $query->setValues($values);
        $query->sendQuery();
        return $id;
    }

    /**
     * Remove item from collection.
     *
     * Does nothing if the item does not exist.
     *
     * Example:
     * <code>
     * unset($collection[$offset]);
     * $collection->offsetUnset($offset);
     * </code>
     *
     * @param  scalar  $offset  index of item to remove
     */
    public function offsetUnset($offset)
    {
        try {
            $query = new \Yana\Db\Queries\Delete($this->_getDatabaseConnection());
            $query->setTable($this->_getTableName());
            $query->setRow($offset);
            $query->sendQuery();
            $this->_getDatabaseConnection()->commit(); // may throw exception

        } catch (\Exception $e) {
            $this->_getDatabaseConnection()->rollback(); // if it failed, don't try it again
            throw $e; // rethrow exception, so that it can be caught upstream
        }
    }

    /**
     * return array of ids in use
     *
     * @return  array
     */
    public function getIds()
    {
        $primaryKeyName = $this->_getDatabaseConnection()->getSchema()->getTable($this->_getTableName())->getPrimaryKey();
        $query = new \Yana\Db\Queries\Select($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());
        $query->setColumn($primaryKeyName);
        return $query->getResults();
    }

    /**
     * Adds the item if it is missing.
     *
     * Same as:
     * <code>
     * $array[] = $subject;
     * </code>
     *
     * @param  \Yana\Data\Adapters\IsEntity  $entity  what you want to add
     */
    public function saveEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        $offset = $entity->getId();
        $this->offsetSet($offset, $entity);
    }

    /**
     * Removes the given entity from the database.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  compose the where clause based on this object
     */
    public function delete(\Yana\Data\Adapters\IsEntity $entity)
    {
        $this->offsetUnset($entity->getId());
    }

    /**
     * SELECTs all entries WHERE "column" = 'value'.
     *
     * @param   string  $columnName  name of column to search in
     * @param   scalar  $value       used for where clause
     * @return  \Yana\Data\Adapters\IsEntity[]
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when the used column is not known
     */
    protected function _findEntitiesByColumn($columnName, $value)
    {
        $where = array($columnName, '=', $value);
        return $this->_getEntities($where);
    }

    /**
     * Finds and returns all entities based on the given where clause.
     * 
     * @param   array  $where  where clause to use in SELECT statement
     * @return  \Yana\Data\Adapters\IsEntity[]
     */
    protected function _getEntities(array $where = array())
    {
        $query = new \Yana\Db\Queries\Select($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());
        $query->setWhere($where);
        $items = array();
        foreach ($query->getResults() as $item)
        {
            $items[] = $this->_unserializeEntity($item);
        }
        return $items;
    }

}

?>