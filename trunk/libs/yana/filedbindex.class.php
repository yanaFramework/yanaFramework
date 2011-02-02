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

/**
 * FileDbIndex
 *
 * This implements index files for table indexes,
 * for the FileDB database.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 * @since       2.8.6
 * @ignore
 */
class FileDbIndex extends Object
{

    /**#@+
     * @ignore
     * @access  private
     */

    /** @var DDLTable */ private $_table = "";
    /** @var SML      */ private $_data = null;
    /** @var string   */ private $_filename = "";
    /** @var array    */ private $_indexes = null;

    /**#@-*/

    /**
     * constructor
     *
     * @param  DDLTable  $table         table (DDL object)
     * @param  SML       $data          data (SML object)
     * @param  string    $filename      filename
     */
    public function __construct(DDLTable $table, SML $data, $filename)
    {
        assert('is_string($filename); // Wrong type for argument 3. String expected');

        $this->_table = $table;
        $this->_data = $data;
        $this->_filename = "$filename";
    }

    /**
     * Get cached indexes for 1 column.
     *
     * Returns NULL if the column is not stored.
     *
     * @access  protected
     * @param   string $column name
     * @return  array
     */
    protected function getColumnValues($column)
    {
        assert('is_string($column); // Wrong argument type argument 1. String expected');
        $indexes = $this->getValues();
        if (isset($indexes[$column])) {
            return $indexes[$column];
        } else {
            $index = null;
            return $index;
        }
    }

    /**
     * Get all cached indexes.
     *
     * @access  protected
     * @return  array
     */
    protected function getValues()
    {
        if (!isset($this->_indexes)) {
            $this->rollback();
        }
        assert('is_array($this->_indexes);');
        return $this->_indexes;
    }

    /**
     * Set index entry.
     *
     * @access  protected
     * @param   string $column name
     * @param   scalar $values indexed values
     */
    protected function setColumnIndex($column, array $values)
    {
        ksort($values);
        $this->_indexes[$column] = $values;
    }

    /**
     * Remove index entry.
     *
     * @access  protected
     * @param   string $column name
     */
    protected function unsetColumnIndex($column)
    {
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
     * @access  public
     * @param   string  $column     column
     * @param   array   $update     update data for index
     * @return  bool
     */
    public function create($column = null, array $update = array())
    {
        assert('is_null($column) || is_string($column); // Wrong type for argument 1. String expected');
        assert('is_array($update); // Wrong type for argument 2. Array expected');
        assert('count($update)===0 || count($update)===2; // Argument $update must have 2 items');
        assert('empty($update) || is_scalar($update[0]); // 1st item of argument 2 is not scalar');
        assert('empty($update) || is_scalar($update[1]); // 2nd item of argument 2 is not scalar');

        /* autoscan */
        if (is_null($column)) {
            assert('empty($update); // No column name provided. Unable to build index');
            $indexes = $this->_findIndexes($this->_table);
            // remove duplicate entries
            foreach ($indexes as $columnName)
            {
                $this->create($columnName);
            }
            return true;
        }

        assert('$this->_table->isColumn($column); // No such column: ' . $column);
        $primaryKey = $this->_table->getPrimaryKey();

        /* no need to index primary key, it is an index by itself */
        if (strcasecmp($primaryKey, $column) === 0) {
            return false;
        }

        $column = mb_strtoupper("$column");
        $index = $this->getColumnValues($column);

        /* create / recreate index */
        if (empty($update) || is_null($index)) {

            $dataset = $this->_data->getVar($primaryKey);
            // table is empty
            if (empty($dataset)) {
                $dataset = array();
            }
            $data = array();
            // target column provided
            if (!empty($update)) {
                assert('!isset($updateSet); // Cannot redeclar var $updateSet');
                $updateSet = array(mb_strtoupper($update[0]) => array($column => $update[1]));
                $dataset = Hashtable::merge($dataset, $updateSet);
                unset($updateSet);
            }
            assert('is_array($dataset);');

        /* update index */
        } else {

            $dataset = array(mb_strtoupper($update[0]) => array($column => $update[1]));
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
                $key = mb_strtoupper($row[$column]);
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
     * @access  private
     * @param   DDLTable $table database table to search for indexes in
     * @return  array
     */
    private function _findIndexes(DDLTable $table)
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
     * get index for some column
     *
     * Returns an associative array on success,
     * returns bool(false) on error.
     *
     * The resulting array uses the following syntax:
     * array (
     *     column_value => primary_key_value
     * )
     *
     * {@internal
     *
     * Exampe of usage:
     * E.g. given the following SQL-statement:
     *  <code>  SELECT * from myTable where indexedRow = "test"  </code>
     *
     * Handle this request as follows:
     * <code>
     * $primaryKeys = $this->get('indexedRow','test');
     * </code>
     * }}
     *
     * @access  public
     * @param   string  $column column name
     * @param   scalar  $value  value
     * @return  array
     * @throws  NotFoundException  when the requested column or value does not exist
     */
    public function get($column, $value = null)
    {
        assert('is_string($column); // Wrong argument type for argument 1. String expected.');
        assert('is_null($value) || is_scalar($value); // Wrong argument type for argument 2. Scalar expected.');

        $column = mb_strtoupper("$column");
        assert('!isset($index); // Cannot redeclare var $index');
        $index = $this->getColumnValues($column);
        if (!is_array($index)) {
            throw new NotFoundException("SQL syntax error. ".
                "No such index '$column' in table '" . $this->_table->getName() . "'.", E_USER_WARNING);
        }
        if (is_null($value)) {
            return $index;
        } else {
            $value = mb_strtoupper("$value");
            if (isset($index[$value])) {
                return $index[$value];
            } else {
                throw new NotFoundException("SQL syntax error. ".
                    "No such index '$column' in table '" . $this->_table->getName() . "'.", E_USER_WARNING);
            }
        }
    }

    /**
     * write index changes to disk
     *
     * Returns bool(true) on success,
     * returns bool(false) on error.
     *
     * @access  public
     * @return  bool
     */
    public function commit()
    {
        return file_put_contents($this->_filename, serialize($this->getValues())) !== false;
    }

    /**
     * reset index contents
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
     *
     * @access  public
     * @throws  InvalidArgumentException  when file is not valid
     */
    public function rollback()
    {
        if (file_exists($this->_filename) === false) {
            if (!touch($this->_filename)) {
                throw new InvalidArgumentException("Not a valid filename '{$this->_filename}'.");
            }
            $this->create();
        } else {
            $indexes = unserialize(file_get_contents($this->_filename));
            if (is_array($indexes)) {
                $this->_indexes = $indexes;
            } else {
                $this->create();
            }
        }
    }

}

?>