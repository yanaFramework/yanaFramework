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
abstract class AbstractDriver extends \Yana\Core\StdObject implements \Yana\Db\IsDriver
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
    protected function _getAutoIncrement(): \Yana\Db\FileDb\Sequence
    {
        if (!isset($this->_autoIncrement)) {
            assert(!isset($name), 'Cannot redeclare var $name');
            $name = $this->getClass() . '\\' . $this->_getDatabaseName() . '\\' . $this->_getTableName();
            try {
                assert(!isset($sequence), 'Cannot redeclare var $sequence');
                $sequence = new \Yana\Db\FileDb\Sequence($name);

            } catch (\Yana\Db\Queries\Exceptions\NotFoundException $e) {
                \Yana\Db\FileDb\Sequence::create($name);
                $sequence = new \Yana\Db\FileDb\Sequence($name);
                unset($e);
            }
            unset($name);
            $this->_setAutoIncrement($sequence);
            unset($sequence);
        }
        return $this->_autoIncrement;
    }

    /**
     * Return database schema.
     *
     * @return  \Yana\Db\Ddl\Database
     */
    protected function _getSchema(): \Yana\Db\Ddl\Database
    {
        return $this->_schema;
    }

    /**
     * Return name of the database.
     *
     * @return  string
     */
    protected function _getDatabaseName(): string
    {
        return $this->_databaseName;
    }

    /**
     * Return schema of currently selected table.
     *
     * @return  \Yana\Db\Ddl\Table|null
     */
    protected function _getTable(): ?\Yana\Db\Ddl\Table
    {
        return $this->_table;
    }

    /**
     * Return name of currently selected table.
     *
     * @return  string
     */
    protected function _getTableName(): string
    {
        return $this->_tableName;
    }

    /**
     * Get sort column.
     *
     * @return  array
     */
    protected function _getSortColumns(): array
    {
        return $this->_sortColumns;
    }

    /**
     * Returns list of columns for which the search-order is reversed.
     *
     * @return  array
     */
    protected function _getDescendingSortColumns(): array
    {
        return $this->_descendingSortColumns;
    }

    /**
     * Returns bool(true) if all queries should be committed automatically.
     *
     * @return  bool
     * @codeCoverageIgnore
     */
    protected function _isAutoCommit(): bool
    {
        return $this->_autoCommit;
    }

    /**
     * Returns the current query as an object.
     *
     * @return  \Yana\Db\Queries\AbstractQuery|null
     */
    protected function _getQuery(): ?\Yana\Db\Queries\AbstractQuery
    {
        return $this->_query;
    }

    /**
     * Set auto-increment sequence counter.
     *
     * @param   \Yana\Db\FileDb\Sequence  $autoIncrement
     * @return  $this
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
     * @return  $this
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
     * @return  $this
     */
    protected function _setDatabaseName(string $database)
    {
        $this->_databaseName = $database;
        return $this;
    }

    /**
     * Return schema of currently selected table.
     *
     * @param   \Yana\Db\Ddl\Table  $table
     * @return  $this
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
     * @return  $this
     */
    protected function _setTableName(string $tableName)
    {
        $this->_tableName = $tableName;
        return $this;
    }

    /**
     * Set sort columns.
     *
     * A lower-cased list of columns the resultset is ordered by.
     *
     * @param   array  $sort
     * @return  $this
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
     * @return  $this
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
     * @return  $this
     */
    protected function _setAutoCommit(bool $autoCommit)
    {
        $this->_autoCommit = (bool) $autoCommit;
        return $this;
    }

    /**
     *
     * @param   \Yana\Db\Queries\AbstractQuery  $query
     * @return  $this
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
     * @return   bool
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