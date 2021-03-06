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
declare(strict_types=1);

namespace Yana\Db\FileDb;

/**
 * FileDbIndex
 *
 * This implements index files for table indexes,
 * for the FileDB database.
 *
 * @package     yana
 * @subpackage  db
 * @since       2.8.6
 */
class Index extends \Yana\Core\StdObject
{

    /**
     * @var \Yana\Db\Ddl\Table
     */
    private $_table = "";

    /**
     * @var \Yana\Files\SML
     */
    private $_data = null;

    /**
     * @var string
     */
    private $_filename = "";

    /**
     * @var array
     */
    private $_indexes = null;

    /**
     * constructor
     *
     * @param  \Yana\Db\Ddl\Table  $table     table (DDL object)
     * @param  \Yana\Files\SML                $data      data (SML object)
     * @param  string              $filename  filename
     */
    public function __construct(\Yana\Db\Ddl\Table $table, \Yana\Files\SML $data, string $filename)
    {
        $this->_table = $table;
        $this->_data = $data;
        $this->_filename = "$filename";
    }

    /**
     * Get cached indexes for 1 column.
     *
     * Returns an empty array if the column is not stored.
     *
     * @param   string  $column  name
     * @return  array
     */
    protected function getColumnValues(string $column): array
    {
        $indexes = $this->getVars();
        if (isset($indexes[$column])) {
            return $indexes[$column];

        } else {
            return array();
        }
    }

    /**
     * Get all cached indexes.
     *
     * @return  array
     */
    protected function getVars(): array
    {
        if (!isset($this->_indexes)) {
            $this->rollback();
        }
        assert(is_array($this->_indexes), 'is_array($this->_indexes)');
        return $this->_indexes;
    }

    /**
     * Set index entry.
     *
     * @param  string $column name
     * @param  scalar $values indexed values
     */
    protected function setColumnIndex(string $column, array $values)
    {
        assert(is_array($this->_indexes), 'is_array($this->_indexes)');
        ksort($values);
        $this->_indexes[$column] = $values;
    }

    /**
     * Remove index entry.
     *
     * @param  string $column name
     */
    protected function unsetColumnIndex(string $column)
    {
        assert(is_array($this->_indexes), 'is_array($this->_indexes)');
        unset($this->_indexes[$column]);
    }

    /**
     * create an index
     *
     * Returns bool(true) on success,
     * returns bool(false) on error.
     *
     * Note: This will commit any uncommitted data.
     *
     * @param   string|null  $column     column
     * @param   array        $update     update data for index
     * @return  bool
     */
    public function create(?string $column = null, array $update = array()): bool
    {
        assert(is_null($column) || is_string($column), 'Wrong type for argument 1. String expected');
        assert(is_array($update), 'Wrong type for argument 2. Array expected');
        assert(count($update)===0 || count($update)===2, 'Argument $update must have 2 items');
        assert(empty($update) || is_scalar($update[0]), '1st item of argument 2 is not scalar');
        assert(empty($update) || is_scalar($update[1]), '2nd item of argument 2 is not scalar');

        /* autoscan */
        if (is_null($column)) {
            assert(empty($update), 'No column name provided. Unable to build index');
            $indexes = $this->_findIndexes($this->_table);
            // remove duplicate entries
            foreach ($indexes as $columnName)
            {
                $this->create($columnName);
            }
            return true;
        }

        assert($this->_table->isColumn($column), 'No such column: ' . $column . ' in table: ' . $this->_table->getName());
        $primaryKey = \mb_strtoupper($this->_table->getPrimaryKey());

        /* no need to index primary key, it is an index by itself */
        if (strcasecmp($primaryKey, $column) === 0) {
            return false;
        }

        $column = mb_strtoupper("$column");
        $index = $this->getColumnValues($column);

        /* create / recreate index */
        if (empty($update) || empty($index)) {

            $dataset = $this->_data->getVar($primaryKey);
            // table is empty
            if (empty($dataset)) {
                $dataset = array();
            }
            $data = array();
            // target column provided
            if (!empty($update)) {
                assert(!isset($updateSet), 'Cannot redeclar var $updateSet');
                $updateSet = array(mb_strtoupper((string) $update[0]) => array($column => $update[1]));
                $dataset = \Yana\Util\Hashtable::merge($dataset, $updateSet);
                unset($updateSet);
            }
            assert(is_array($dataset), 'is_array($dataset)');

        /* update index */
        } else {

            $dataset = array(mb_strtoupper((string) $update[0]) => array($column => $update[1]));
            $data = $index;
            if (!is_array($data)) {
                $data = array();
            }

        }

        /* process index */
        foreach ($dataset as $value => $row)
        {
            /* NULL values are to be ignored */
            if (isset($row[$column])) {
                $key = mb_strtoupper((string) $row[$column]);
                if (isset($data[$key])) {
                    if (is_array($data[$key])) {
                        array_push($data, $value);
                    } else {
                        $data[$key] = array($data[$key], $value);
                    }
                } else {
                    $data[$key] = $value;
                }
            }
        } // end foreach
        $this->unsetColumnIndex($column);

        $this->setColumnIndex($column, $data);
        return true;
    }

    /**
     * Find all indexed columns in a given table.
     *
     * @param   \Yana\Db\Ddl\Table $table database table to search for indexes in
     * @return  array
     */
    private function _findIndexes(\Yana\Db\Ddl\Table $table): array
    {
        $indexes = array();
        // get indexed columns
        foreach ($table->getIndexes() as $index)
        {
            foreach($index->getColumns() as $indexColumn)
            {
                $indexes[] = $indexColumn->getName();
            }
        }
        unset($index);
        // auto-create indexes for unique constraints
        foreach ($table->getUniqueConstraints() as $indexColumn)
        {
            $indexes[] = $indexColumn->getName();
        }
        return array_unique($indexes);
    }

    /**
     * Get index for some column.
     *
     * If $value is null, returns an associative array.
     *
     * The resulting array uses the following syntax:
     * array (
     *     column_value => primary_key_value
     * )
     *
     * If $value is NOT null, returns the index of above array identified by $value.
     *
     * {@internal
     *
     * Exampe of usage:
     * E.g. given the following SQL-statement:
     *  <code>  SELECT * from myTable where indexedRow = "test"  </code>
     *
     * Handle this request as follows:
     * <code>
     * $primaryKeys = $this->getVar('indexedRow','test');
     * </code>
     * }}
     *
     * @param   string       $column column name
     * @param   scalar|null  $value  value
     * @return  array|scalar
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the requested column or value does not exist
     */
    public function getVar(string $column, $value = null)
    {
        assert(is_null($value) || is_scalar($value), 'Invalid argument $value: Scalar expected.');

        $index = $this->_getVar($column, $value);
        if (is_array($index) && count($index) === 0) {
            throw new \Yana\Core\Exceptions\NotFoundException(
                "SQL syntax error. No such index '$column' in table '" . $this->_table->getName() . "'.",
                \Yana\Log\TypeEnumeration::WARNING
            );
        }
        return $index;
    }

    /**
     * Get index for some column.
     *
     * @param   string       $column column name
     * @param   scalar|null  $value  value
     * @return  array|scalar
     */
    private function _getVar(string $column, $value = null)
    {
        assert(is_null($value) || is_scalar($value), 'Invalid argument $value: Scalar expected.');

        $column = mb_strtoupper("$column");
        assert(!isset($index), 'Cannot redeclare var $index');
        $index = $this->getColumnValues($column);

        if (is_null($value)) {
            return $index;
        } else {
            $value = is_scalar($value) ? mb_strtoupper((string) $value) : json_encode($value);
            return isset($index[$value]) ? $index[$value] : array();
        }
    }

    /**
     * Returns bool(true) if the column and value exist in the index.
     *
     * @param   string       $column column name
     * @param   scalar|null  $value  value
     * @return  bool
     */
    public function hasVar(string $column, $value = null): bool
    {
        assert(is_null($value) || is_scalar($value), 'Invalid argument $value: Scalar expected.');
        $index = $this->_getVar($column, $value);
        return count($index) > 0;
    }

    /**
     * write index changes to disk
     *
     * Returns bool(true) on success,
     * returns bool(false) on error.
     *
     * @return  bool
     */
    public function commit(): bool
    {
        return file_put_contents($this->_filename, serialize($this->getVars())) !== false;
    }

    /**
     * Autosave file on destruct.
     */
    public function  __destruct()
    {
        $this->commit();
    }

    /**
     * Reset index contents.
     *
     * Creates the index file, if it does not exist.
     * The contents are restored - if there are none, it recreates them.
     *
     * {@internal
     * If no index exists yet, the file needs to be created
     * and initialized before using it.
     * Otherwise there you would risk, working with invalid
     * data and possibly cause inconsistent data to be written
     * to the database.
     * }}
     */
    public function rollback()
    {
        $indexes = array();
        if (file_exists($this->_filename)) {
            $_indexes = unserialize(file_get_contents($this->_filename));
            if (\is_array($_indexes)) {
                $indexes = $_indexes; // should always be the case (but just to be on the safe side)
            }
            unset($_indexes);
        }
        $this->_indexes = $indexes;
    }

}

?>