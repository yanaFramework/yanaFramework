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

namespace Yana\Db\FileDb;

/**
 * <<abstract>> FileDb-Driver.
 *
 * Mapper for sql statements to SML-file commands.
 * It implements only a required subset of the interface
 * of PEAR MDB2 as needed by the DbStream class.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractDriver extends \Yana\Core\Object implements \Yana\Db\IsDriver
{

    /**
     * @var \Yana\Db\FileDb\Sequence
     */
    private $_autoIncrement = null;

    /**
     * @var \Yana\Db\Ddl\Database
     */
    private $_schema = null;

    /**
     * @var string
     */
    private $_databaseName = "";

    /**
     * @var \Yana\Db\Ddl\Table
     */
    private $_table = null;

    /**
     * @var string
     */
    private $_tableName = "";

    /**
     * @var array
     */
    private $_sortColumns = array();

    /**
     * @var array
     */
    private $_descendingSortColumns = array();

    /**
     * @var bool
     */
    private $_autoCommit = true;

    /**
     * @var \Yana\Db\Queries\AbstractQuery
     */
    private $_query = null;

    /**
     * Return auto-increment sequence counter.
     *
     * @return  \Yana\Db\FileDb\Sequence
     */
    protected function _getAutoIncrement()
    {
        return $this->_autoIncrement;
    }

    /**
     * Return database schema.
     *
     * @return  \Yana\Db\Ddl\Database
     */
    protected function _getSchema()
    {
        return $this->_schema;
    }

    /**
     * Return name of the database.
     *
     * @return  string
     */
    protected function _getDatabaseName()
    {
        return $this->_databaseName;
    }

    /**
     * Return schema of currently selected table.
     *
     * @return  \Yana\Db\Ddl\Table
     */
    protected function _getTable()
    {
        return $this->_table;
    }

    /**
     * Return name of currently selected table.
     *
     * @return  string
     */
    protected function _getTableName()
    {
        return $this->_tableName;
    }

    /**
     * Get sort column.
     *
     * @return  array
     */
    protected function _getSortColumns()
    {
        return $this->_sortColumns;
    }

    /**
     * Returns list of columns for which the search-order is reversed.
     *
     * @return  array
     */
    protected function _getDescendingSortColumns()
    {
        return $this->_descendingSortColumns;
    }

    /**
     * Returns bool(true) if all queries should be committed automatically.
     *
     * @return  bool
     */
    protected function _isAutoCommit()
    {
        return $this->_autoCommit;
    }

    /**
     * Returns the current query as an object.
     *
     * @return  \Yana\Db\Queries\AbstractQuery
     */
    protected function _getQuery()
    {
        return $this->_query;
    }

    /**
     * Set auto-increment sequence counter.
     *
     * @param   \Yana\Db\FileDb\Sequence  $autoIncrement
     * @return  self
     */
    protected function _setAutoIncrement(\Yana\Db\FileDb\Sequence $autoIncrement)
    {
        $this->_autoIncrement = $autoIncrement;
        return $this;
    }

    /**
     * Return database schema.
     *
     * @param   \Yana\Db\Ddl\Database  $schema
     * @return  self
     */
    protected function _setSchema(\Yana\Db\Ddl\Database $schema)
    {
        $this->_schema = $schema;
        return $this;
    }

    /**
     * Set name of the database.
     *
     * @param   string  $database
     * @return  self
     */
    protected function _setDatabaseName($database)
    {
        assert('is_string($database); // Invalid argument $database: string expected');

        $this->_databaseName = (string) $database;
        return $this;
    }

    /**
     * Return schema of currently selected table.
     *
     * @param   \Yana\Db\Ddl\Table  $table
     * @return  self
     */
    protected function _setTable(\Yana\Db\Ddl\Table $table)
    {
        $this->_table = $table;
        return $this;
    }

    /**
     * Set name of currently selected table.
     *
     * @param   string  $tableName
     * @return  self
     */
    protected function _setTableName($tableName)
    {
        assert('is_string($tableName); // Invalid argument variable: string expected');

        $this->_tableName = (string) $tableName;
        return $this;
    }

    /**
     * Set sort columns.
     *
     * A lower-cased list of columns the resultset is ordered by.
     *
     * @param   array  $sort
     * @return  self
     */
    protected function _setSortColumns(array $sort)
    {
        $this->_sortColumns = $sort;
        return $this;
    }

    /**
     * Set to columns for which the search-order is reversed.
     *
     * @param   array  $desc
     * @return  self
     */
    protected function _setDescendingSortColumns(array $desc)
    {
        $this->_descendingSortColumns = $desc;
        return $this;
    }

    /**
     * Set to bool(true) if all queries should be committed automatically.
     *
     * @param   bool  $autoCommit
     * @return  self
     */
    protected function _setAutoCommit($autoCommit)
    {
        assert('is_bool($autoCommit); // Invalid argument $autoCommit: bool expected');

        $this->_autoCommit = (bool) $autoCommit;
        return $this;
    }

    /**
     *
     * @param   \Yana\Db\Queries\AbstractQuery  $query
     * @return  self
     */
    protected function _setQuery(\Yana\Db\Queries\AbstractQuery $query)
    {
        $this->_query = $query;
        return $this;
    }

    /**
     * Compare with another object.
     *
     * Returns bool(true) if this object and $anotherObject
     * are equal and bool(false) otherwise.
     *
     * Two instances are considered equal if and only if
     * they are both objects of the same class and they both
     * refer to the same structure file.
     *
     * @param    \Yana\Core\IsObject  $anotherObject object to compare
     * @return   string
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        if ($anotherObject instanceof $this) {
            if (!isset($this->_schema) || !isset($anotherObject->_schema)) {
                return isset($this->_schema) === isset($anotherObject->_schema);
            }
            return (bool) $this->_schema->equals($anotherObject->_schema);
        }
        return false;
    }

}

?>