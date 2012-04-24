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

namespace Yana\Io\Adapters;

/**
 * <<adapter>> data adapter
 *
 * Database adapter, that stores and restores the given object from a database connection.
 *
 * @package     yana
 * @subpackage  core
 */
class DatabaseAdapter extends \Yana\Core\Object implements \Yana\Io\Adapters\IsDataAdapter
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
     * @access  public
     * @param   string  $index  where to store session data $_SESSION[$index]
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the table is not registered in the database
     */
    public function __construct(\Yana\Db\IsConnection $db, $table)
    {
        assert('is_string($table); // Wrong argument type argument 1. String expected');
        if (!$db->getSchema()->isTable($table)) {
            $message = "Table not found: '$table' in database '{$db->schema->getName()}'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message);
        }
        $this->_databaseConnection = $db;
        $this->_tableName = strtolower("$table");
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
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        $query = new \Yana\Db\Queries\Select($this->_getDatabaseConnection());
        $query->setTable($this->_getTableName());
        $query->setRow($offset);
        return $query->getResults();
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
     * @param   scalar  $offset  index of item to replace
     * @param   mixed   $value   new value of item
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid collection item
     * @return  mixed
     */
    public function offsetSet($offset, $value)
    {
        if ($offset && $this->offsetExists($offset)) {
            $query = new \Yana\Db\Queries\Update($this->_getDatabaseConnection());
        } else {
            $query = new \Yana\Db\Queries\Insert($this->_getDatabaseConnection());
        }
        $query->setTable($this->_getTableName());
        $query->setRow($offset);
        $query->setValues($value);
        return $query->sendQuery();
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
     * @param  \Yana\Io\Adapters\IsEntity  $entity  what you want to add
     */
    public function saveEntity(\Yana\Io\Adapters\IsEntity $entity)
    {
        $offset = $entity->getId();
        $this->offsetSet($offset, $entity);
    }

}

?>