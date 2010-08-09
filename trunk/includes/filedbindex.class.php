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

    /** @var DDLTable */ private $table = "";
    /** @var SML      */ private $data = null;
    /** @var string   */ private $filename = "";
    /** @var array    */ private $indexes = array();

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

        $this->table = $table;
        $this->data = $data;
        $this->filename = "$filename";
        // initialize contents
        $this->rollback();
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
            $indexes = array();
            assert('empty($update); // No column name provided. Unable to build index');
            // get indexed columns
            foreach ($this->table->getIndexes() as $index)
            {
                foreach($index->getColumns() as $indexColumn)
                {
                    $indexes[] = $indexColumn->getName();
                }
            }
            // auto-create indexes for unique constraints
            foreach ($this->table->getUniqueConstraints() as $indexColumn)
            {
                $indexes[] = $indexColumn->getName();
            } // end foreach
            // remove duplicate entries
            foreach (array_unique($indexes) as $columnName)
            {
                // create indexes
                try {

                    $this->create($columnName);

                } catch (Exception $e) {
                    /* Indexes are for performance and not that important after all.
                     * If we can't create it - just ignore it.
                     */
                    return false;
                }
            }
            return true;
        }

        /**
         * continue without scan
         */
        if (!$this->table->isColumn($column)) {
            Log::report("SQL syntax error. ".
                "No such column '$column' in table '{$this->table->getName()}'.", E_USER_WARNING);
            return false;
        }
        $primaryKey = $this->table->getPrimaryKey();

        /* no need to index primary key, it is an index by itself */
        if (strcasecmp($primaryKey, $column) === 0) {
            return false;
        }

        $column = mb_strtoupper("$column");

        /* create / recreate index */
        if (empty($update) || !isset($this->indexes[$column])) {

            $dataset = $this->data->getVar($primaryKey);
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
            $data = $this->indexes[$column];
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
        } /* end foreach */
        ksort($data);
        unset($this->indexes[$column]);

        $this->indexes[$column] = $data;
        return true;
    }

    /**
     * get index for some column
     *
     * Same as FileDbIndex::get(), but retuns
     * result by reference;
     *
     * @access  public
     * @param   string  $column     column name
     * @param   scalar  $value      value
     * @return  array()
     * @see     FileDbIndex::get()
     * @throws  NotFoundException  when requested column does not exist
     */
    public function &getByReference($column, $value = null)
    {
        assert('is_string($column); // Wrong argument type for argument 1. String expected.');
        assert('is_null($value) || is_scalar($value); // Wrong argument type for argument 2. Scalar expected.');

        $isError = false;
        $emptyIndex = array();
        if (!$this->table->isColumn($column)) {
            throw new NotFoundException("SQL syntax error. ".
                "No such column '{$column}' in table '{$this->table->getName()}'.", E_USER_WARNING);
        }

        if (strcasecmp($this->table->getPrimaryKey(), $column) === 0) {
            return $emptyIndex;
        }

        $column = mb_strtoupper("$column");
        if (is_null($value)) {
            if (isset($this->indexes[$column]) && is_array($this->indexes[$column])) {
                return $this->indexes[$column];
            } else {
                throw new NotFoundException("SQL syntax error. ".
                    "No such index '$column' in table '{$this->table->getName()}'.", E_USER_WARNING);
            }
        } else {
            $value = mb_strtoupper("$value");
            if (isset($this->indexes[$column][$value])) {
                return $this->indexes[$column][$value];
            } else {
                throw new NotFoundException("SQL syntax error. ".
                    "No such index '$column' in table '{$this->table->getName()}'.", E_USER_WARNING);
            }
        }
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
     */
    public function get($column, $value = null)
    {
        assert('is_string($column); // Wrong argument type for argument 1. String expected.');
        assert('is_null($value) || is_scalar($value); // Wrong argument type for argument 2. Scalar expected.');
        return $this->getByReference($column, $value);
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
        if (file_put_contents($this->filename, serialize($this->indexes)) !== false) {
            return true;
        } else {
            return false;
        }
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
     * Returns bool(true) on success,
     * returns bool(false) on error.
     *
     * @access  public
     * @return  bool
     * @throws  InvalidArgumentException  when file is not valid
     */
    public function rollback()
    {
        if (file_exists($this->filename) === false) {
            if (!touch($this->filename)) {
                throw new InvalidArgumentException("Not a valid filename '{$this->filename}'.");
            }
            $this->create();
        } else {
            $indexes = unserialize(file_get_contents($this->filename));
            if (is_array($indexes)) {
                $this->indexes = $indexes;
            } else {
                $this->create();
            }
        }
    }

}

?>