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
     * selected database table
     *
     * @var  string
     */
    private $_tableName = "";

    /**
     * constructor
     *
     * @param   string  $index  where to store session data $_SESSION[$index]
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the table is not registered in the database
     */
    public function __construct(\Yana\Db\IsConnection $db, $table)
    {
        assert('is_string($table)', ' Wrong argument type argument 1. String expected');
        if (!$db->getSchema()->isTable($table)) {
            $message = "Table not found: '$table' in database '{$db->schema->getName()}'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message);
        }
        $this->_databaseConnection = $db;
        $this->_tableName = mb_strtolower("$table");
    }

    /**
     * Returns the name of the target table.
     *
     * @return  string
     */
    protected function _getTableName()
    {
        return $this->_tableName;
    }

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
        $query = new \Yana\Db\Queries\Select($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());
        $query->setRow($offset);
        $dataSet = $query->getResults();
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
     * @return  \Yana\Data\Adapters\IsEntity
     */
    public function offsetSet($offset, $entity)
    {
        if (!$entity instanceof \Yana\Data\Adapters\IsEntity) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException('Instance of "IsEntity" expected.');
        }
        if ($offset !== null && $offset !== $entity->getId()) {
            $entity->setId($offset);
        }

        if ($offset && $this->offsetExists($offset)) {
            $query = new \Yana\Db\Queries\Update($this->_getDatabaseConnection());
        } else {
            $query = new \Yana\Db\Queries\Insert($this->_getDatabaseConnection());
        }
        $query->setTable($this->_getTableName());
        if ($offset) {
            $query->setRow($offset);
        }
        $values = $this->_serializeEntity($entity);
        $query->setValues($values);
        $query->sendQuery();
        return $entity;
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
        $query = new \Yana\Db\Queries\Delete($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());
        $query->setRow($offset);
        $query->sendQuery();
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
    public function deleteEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        $this->offsetUnset($entity->getId());
    }

    /**
     * DELETE all entries WHERE "column" = 'value'.
     *
     * @param   string  $columnName  name of column to search in
     * @param   scalar  $value       used for where clause
     * @return  int
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when the used column is not known
     */
    public function deleteEntities($columnName, $value)
    {
        $query = new \Yana\Db\Queries\Delete($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());
        $query->setWhere(array($columnName, '=', $value));
        $query->setLimit(0);
        $query->sendQuery();
    }

    /**
     * SELECTs all entries WHERE "column" = 'value'.
     *
     * @param   string  $columnName  name of column to search in
     * @param   scalar  $value       used for where clause
     * @return  \Yana\Data\Adapters\IsEntity[]
     * @throws  \Yana\Db\Queries\Exceptions\ColumnNotFoundException  when the used column is not known
     */
    public function findEntities($columnName, $value)
    {
        $where = array($columnName, '=', $value);
        return $this->_getEntities($where);
    }

    /**
     * Analyzes the given entity and returns all items with similar properties.
     *
     * What is considered "similar" depends on the implementation.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  compose the where clause based on this object
     * @return  \Yana\Data\Adapters\IsEntity[]
     */
    public function findSimilarEntities(\Yana\Data\Adapters\IsEntity $entity)
    {
        $where = $this->_buildWhereClause($entity);
        return $this->_getEntities($where);
    }

    /**
     * SELECTs all entries WHERE "column" = 'value'.
     *
     * @return  \Yana\Data\Adapters\IsEntity[]
     */
    public function getAllEntities()
    {
        return $this->_getEntities();
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

    /**
     * Build a where clause based on the properties of the given entity.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  object to convert
     * @return array 
     */
    protected function _buildWhereClause(\Yana\Data\Adapters\IsEntity $entity)
    {
        $dataSet = $this->_serializeEntity($entity);
        $where = array();
        foreach ($dataSet as $columnName => $value)
        {
            $_clause = array($columnName, '=', $value);
            $where = (empty($where)) ? $_clause : array($_clause, 'AND', $where);
        }
        return $where;
    }
}

?>